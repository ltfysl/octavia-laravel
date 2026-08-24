<?php

namespace App\Services;

use App\Enums\RunStatus;
use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\Run;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ActivityFeedService
{
    public function forUser(User $user, int $limit = 20): Collection
    {
        $take = min(max($limit, 1), 50);

        $runs = Run::with(['benchmark', 'prompt'])
            ->visibleTo($user)
            ->latest('created_at')
            ->take($take)
            ->get();

        $prompts = Prompt::visibleTo($user)
            ->latest('created_at')
            ->take($take)
            ->get(['id', 'name', 'category', 'created_at', 'description']);

        $benchmarks = Benchmark::visibleTo($user)
            ->latest('created_at')
            ->take($take)
            ->get(['id', 'name', 'category', 'created_at', 'description']);

        $notifications = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->latest('created_at')
            ->take($take)
            ->get(['id', 'data', 'created_at']);

        $items = collect();

        foreach ($runs as $run) {
            $items->push($this->mapRun($run));
        }

        foreach ($prompts as $prompt) {
            $items->push($this->mapPrompt($prompt));
        }

        foreach ($benchmarks as $benchmark) {
            $items->push($this->mapBenchmark($benchmark));
        }

        foreach ($notifications as $notification) {
            $items->push($this->mapNotification($notification));
        }

        return $items
            ->sortByDesc('timestamp')
            ->values()
            ->take($take);
    }

    private function mapRun(Run $run): array
    {
        $benchmarkName = $run->benchmark?->name ?? '—';
        $candidateCount = $run->steps()->count();
        $scorePct = $run->best_score !== null ? round($run->best_score * 100) : 0;

        $status = match ($run->status) {
            RunStatus::Completed => ['evolution_completed', 'success', "Evolution completed: {$benchmarkName}", "Best score {$scorePct}% across {$candidateCount} candidates"],
            RunStatus::Failed, RunStatus::Cancelled => ['evolution_failed', 'error', "Evolution failed: {$benchmarkName}", "Run stopped after {$candidateCount} candidates"],
            default => ['evolution_started', 'info', "Evolution started: {$benchmarkName}", "Mode {$run->mode->value} · Target score {$run->target_score}"],
        };

        return [
            'id' => 'run-'.$run->id,
            'type' => $status[0],
            'status' => $status[1],
            'title' => $status[2],
            'description' => $status[3],
            'timestamp' => $run->isFinished() && $run->finished_at ? $run->finished_at->toIso8601String() : $run->created_at->toIso8601String(),
            'relatedId' => $run->id,
            'category' => $run->benchmark?->category,
        ];
    }

    private function mapPrompt(Prompt $prompt): array
    {
        return [
            'id' => 'prompt-'.$prompt->id,
            'type' => 'prompt_saved',
            'status' => 'info',
            'title' => "Prompt saved: {$prompt->name}",
            'description' => $prompt->description ? str($prompt->description)->limit(120) : 'Added to prompt library',
            'timestamp' => $prompt->created_at->toIso8601String(),
            'relatedId' => $prompt->id,
            'category' => $prompt->category,
        ];
    }

    private function mapBenchmark(Benchmark $benchmark): array
    {
        return [
            'id' => 'benchmark-'.$benchmark->id,
            'type' => 'benchmark_created',
            'status' => 'info',
            'title' => "Benchmark created: {$benchmark->name}",
            'description' => $benchmark->description ? str($benchmark->description)->limit(120) : 'New benchmark added',
            'timestamp' => $benchmark->created_at->toIso8601String(),
            'relatedId' => $benchmark->id,
            'category' => $benchmark->category,
        ];
    }

    private function mapNotification(object $notification): array
    {
        $data = json_decode($notification->data, true);
        $title = $data['title'] ?? $data['subject'] ?? 'Notification';
        $message = $data['message'] ?? $data['body'] ?? '';
        $type = strtolower($data['type'] ?? '');

        $status = 'info';
        if (str_contains($type, 'error') || str_contains($type, 'fail')) {
            $status = 'error';
        } elseif (str_contains($type, 'warn')) {
            $status = 'warning';
        } elseif (str_contains($type, 'success') || str_contains($type, 'complete')) {
            $status = 'success';
        }

        return [
            'id' => 'notification-'.$notification->id,
            'type' => 'notification',
            'status' => $status,
            'title' => $title,
            'description' => $message,
            'timestamp' => (new \DateTime($notification->created_at))->format('c'),
            'relatedId' => null,
            'category' => null,
        ];
    }
}

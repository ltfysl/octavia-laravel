<?php

namespace App\Http\Controllers;

use App\Models\Benchmark;
use App\Models\Prompt;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $q = trim((string) $request->query('q', ''));
        /** @var User $user */
        $user = $request->user();

        $prompts = collect();
        $benchmarks = collect();

        if ($q !== '') {
            $like = "%{$q}%";

            $prompts = Prompt::visibleTo($user)
                ->with('currentVersion:id,prompt_id,version')
                ->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('description', 'like', $like))
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn ($p) => [
                    'type' => 'prompt',
                    'id' => $p->id,
                    'title' => $p->name,
                    'subtitle' => $p->description,
                    'url' => "/prompts/{$p->id}",
                ]);

            $benchmarks = Benchmark::visibleTo($user)
                ->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('description', 'like', $like))
                ->latest()
                ->limit(10)
                ->get()
                ->map(fn ($b) => [
                    'type' => 'benchmark',
                    'id' => $b->id,
                    'title' => $b->name,
                    'subtitle' => $b->description,
                    'url' => "/benchmarks/{$b->id}",
                ]);
        }

        return Inertia::render('SearchResults', [
            'query' => $q,
            'results' => ['prompts' => $prompts, 'benchmarks' => $benchmarks],
        ]);
    }
}

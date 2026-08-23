<?php

namespace App\Events;

use App\Models\Run;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired whenever a run's live progress changes (status transition,
 * best-score update, new step). The versions tab / run detail page
 * subscribes to this instead of blind polling.
 */
class RunProgress implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Run $run)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('runs.'.$this->run->id)];
    }

    public function broadcastAs(): string
    {
        return 'progress';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->run->status->value,
            'best_score' => $this->run->best_score,
            'steps' => $this->run->steps()->count(),
        ];
    }
}

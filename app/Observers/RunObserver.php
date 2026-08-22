<?php

namespace App\Observers;

use App\Models\Run;
use App\Services\CreditService;

/**
 * Settles reserved credits whenever a run reaches a terminal state.
 * Runs reserve max_steps credits at creation; unused steps are
 * refunded here — centralised so every terminal transition
 * (completed, failed, cancelled) settles identically.
 */
class RunObserver
{
    public function __construct(private readonly CreditService $credits) {}

    public function updated(Run $run): void
    {
        if (! $run->wasChanged('status') || ! $run->isFinished()) {
            return;
        }

        $executed = $run->steps()->count();
        $reserved = (int) $run->max_steps;
        $refund = max(0, $reserved - $executed);

        if ($refund > 0) {
            $this->credits->grant($run->user, $refund, CreditService::REASON_RUN_REFUND, [
                'run_id' => $run->id,
                'reserved' => $reserved,
                'executed' => $executed,
            ]);
        }
    }

    public function forceUpdated(Run $run): void
    {
        $this->updated($run);
    }
}

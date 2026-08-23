<?php

namespace App\Observers;

use App\Events\RunProgress;
use App\Models\Run;

/**
 * Broadcasts live progress whenever a run's status or best score changes.
 */
class RunProgressObserver
{
    public function updated(Run $run): void
    {
        if ($run->wasChanged(['status', 'best_score'])) {
            $this->dispatchSafely($run);
        }
    }

    public function created(Run $run): void
    {
        $this->dispatchSafely($run);
    }

    private function dispatchSafely(Run $run): void
    {
        try {
            RunProgress::dispatch($run);
        } catch (Throwable) {
            // Progress is best-effort: a missing Reverb server must never
            // break run execution (e.g. sync queues in tests/E2E).
        }
    }
}

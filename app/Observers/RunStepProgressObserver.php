<?php

namespace App\Observers;

use App\Events\RunProgress;
use App\Models\RunStep;
use Throwable;

/**
 * Broadcasts live progress whenever a new run step is persisted.
 */
class RunStepProgressObserver
{
    public function created(RunStep $step): void
    {
        try {
            RunProgress::dispatch($step->run);
        } catch (Throwable) {
            // best-effort, see RunProgressObserver
        }
    }
}

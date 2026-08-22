<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceReport;
use App\Notifications\MarketplaceReportResolvedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Marketplace moderation: admins can unlist (soft-hide) or relist any
 * listing without touching the underlying prompt/benchmark, and work
 * through the abuse-report queue.
 */
class MarketplaceController extends Controller
{
    public function index(Request $request): Response
    {
        $items = MarketplaceItem::query()
            ->with(['publisher:id,name', 'prompt:id,name,visibility', 'benchmark:id,name'])
            ->orderByRaw('published_at IS NULL DESC, published_at DESC')
            ->limit(50)
            ->get()
            ->map(fn (MarketplaceItem $item) => [
                'id' => $item->id,
                'item_type' => $item->item_type->value,
                'title' => $item->title,
                'publisher' => $item->publisher?->name,
                'version' => $item->version,
                'downloads' => $item->downloads,
                'listed' => $item->published_at !== null,
                'published_at' => $item->published_at?->toIso8601String(),
            ]);

        return Inertia::render('admin/Marketplace', ['items' => $items]);
    }

    public function reports(Request $request): Response
    {
        $reports = MarketplaceReport::query()
            ->where('status', 'open')
            ->with(['item:id,item_type,title,published_at', 'item.publisher:id,name', 'reporter:id,name,email'])
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (MarketplaceReport $report) => [
                'id' => $report->id,
                'reason' => $report->reason,
                'message' => $report->message,
                'reporter' => $report->reporter?->name,
                'item_id' => $report->item?->id,
                'item_title' => $report->item?->title,
                'item_listed' => $report->item?->published_at !== null,
                'publisher' => $report->item?->publisher?->name,
                'created_at' => $report->created_at?->toIso8601String(),
            ]);

        return Inertia::render('admin/Reports', ['reports' => $reports]);
    }

    public function setListed(Request $request, MarketplaceItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'listed' => ['required', 'boolean'],
        ]);

        if ($validated['listed']) {
            $item->forceFill(['published_at' => now()])->save();
        } else {
            $item->update(['published_at' => null]);
        }

        return back()->with('success', __('Saved.'));
    }

    /**
     * Resolve a report. Optionally unlists the reported item in the same
     * action — the common moderation outcome. Closes the loop by notifying
     * the reporter (database channel → bell).
     */
    public function resolve(Request $request, MarketplaceReport $report, string $action): RedirectResponse
    {
        $report->update([
            'status' => 'resolved',
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        if ($action === 'unlist' && $report->item) {
            $report->item->update(['published_at' => null]);
        }

        $report->reporter?->notify(new MarketplaceReportResolvedNotification($report->refresh()));

        return back()->with('success', __('Report resolved.'));
    }
}

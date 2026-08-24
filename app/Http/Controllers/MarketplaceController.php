<?php

namespace App\Http\Controllers;

use App\Actions\CopyMarketplaceItem;
use App\Enums\MarketplaceItemType;
use App\Models\Benchmark;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceReport;
use App\Models\Prompt;
use App\Notifications\ListingUpdatedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarketplaceController extends Controller
{
    public function index(Request $request): Response
    {
        $type = $request->query('type');
        $search = trim((string) $request->query('q', ''));

        $installs = $request->user()
            ? $request->user()->marketplaceInstalls()->pluck('version', 'marketplace_item_id')
            : collect();

        $user = $request->user();

        $items = MarketplaceItem::query()
            ->listed()
            ->with('publisher:id,name')
            ->when($user, function ($q) use ($user) {
                $q->with([
                    'starredBy' => fn ($q) => $q->where('users.id', $user->id),
                    'forkedBy' => fn ($q) => $q->where('users.id', $user->id),
                ]);
            })
            ->when(in_array($type, ['prompt', 'benchmark'], true), fn ($q) => $q->where('item_type', $type))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(fn ($w) => $w
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%"));
            })
            ->orderByDesc('featured')
            ->paginate(12)
            ->through(function (MarketplaceItem $item) use ($user) {

                return [
                    'id' => $item->id,
                    'item_type' => $item->item_type->value,
                    'title' => $item->title,
                    'summary' => $item->summary,
                    'version' => $item->version,
                    'downloads' => $item->downloads,
                    'stars_count' => $item->stars_count,
                    'forks_count' => $item->forks_count,
                    'has_starred' => $user ? $item->starredBy->isNotEmpty() : false,
                    'has_forked' => $user ? $item->forkedBy->isNotEmpty() : false,
                    'featured' => $item->featured,
                    'publisher' => $item->publisher?->name,
                    'published_at' => $item->published_at?->toIso8601String(),
                    'installed_version' => $installs[$item->id] ?? null,
                ];
            });

        return Inertia::render('marketplace/Index', [
            'items' => $items,
            'filters' => ['type' => $type, 'q' => $search],
        ]);
    }

    public function install(Request $request, MarketplaceItem $item, CopyMarketplaceItem $copy): RedirectResponse
    {
        abort_if($item->published_at === null, 404);

        $user = $request->user();

        $copy($item, $user);

        // Track the installed version for update-available detection.
        $user->marketplaceInstalls()->updateOrCreate(
            ['marketplace_item_id' => $item->id],
            ['version' => $item->version],
        );

        $item->increment('downloads');

        $route = $item->item_type === MarketplaceItemType::Prompt ? 'prompts.index' : 'benchmarks.index';

        return redirect()->route($route)->with('success', __('Installed to your library.'));
    }

    /**
     * Report a listing. One report per user per item; repeat clicks
     * update the existing report instead of spamming the queue.
     */
    public function report(Request $request, MarketplaceItem $item): RedirectResponse
    {
        abort_unless($item->published_at !== null, 404);

        $validated = $request->validate([
            'reason' => ['required', 'in:inappropriate,spam,copyright,broken,other'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        // Update an open report from this reporter; a previously resolved
        // report stays as history and a fresh one opens.
        $existing = MarketplaceReport::where('marketplace_item_id', $item->id)
            ->where('reporter_id', $request->user()->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            $existing->update([
                'reason' => $validated['reason'],
                'message' => $validated['message'] ?? null,
            ]);
        } else {
            MarketplaceReport::create([
                'marketplace_item_id' => $item->id,
                'reporter_id' => $request->user()->id,
                'reason' => $validated['reason'],
                'message' => $validated['message'] ?? null,
            ]);
        }

        return back()->with('success', __('Report submitted. Our team will review it.'));
    }

    public function publish(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_type' => ['required', 'in:prompt,benchmark'],
            'item_id' => ['required', 'integer'],
            'summary' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        if ($validated['item_type'] === 'prompt') {
            /** @var Prompt $prompt */
            $prompt = $user->prompts()->with('currentVersion')->findOrFail($validated['item_id']);

            $item = MarketplaceItem::updateOrCreate(
                ['item_type' => MarketplaceItemType::Prompt, 'prompt_id' => $prompt->id],
                [
                    'publisher_id' => $user->id,
                    'title' => $prompt->name,
                    'summary' => $validated['summary'] ?? $prompt->description,
                    'version' => $prompt->currentVersion?->version ?? 1,
                    // Snapshot freezes the published content: later edits to
                    // the source prompt don't retroactively change listings.
                    'snapshot' => [
                        'content' => $prompt->currentVersion?->content,
                    ],
                    'published_at' => now(),
                ],
            );
        } else {
            /** @var Benchmark $benchmark */
            $benchmark = $user->benchmarks()->with('cases.criteria')->findOrFail($validated['item_id']);

            $item = MarketplaceItem::updateOrCreate(
                ['item_type' => MarketplaceItemType::Benchmark, 'benchmark_id' => $benchmark->id],
                [
                    'publisher_id' => $user->id,
                    'title' => $benchmark->name,
                    'summary' => $validated['summary'] ?? $benchmark->description,
                    'version' => $benchmark->version,
                    // Snapshot freezes the published test suite.
                    'snapshot' => [
                        'category' => $benchmark->category->value,
                        'cases' => $benchmark->cases->map(fn ($case) => [
                            'title' => $case->title,
                            'input' => $case->input,
                            'weight' => (float) $case->weight,
                            'criteria' => $case->criteria->map(fn ($c) => [
                                'type' => $c->type->value,
                                'label' => $c->label,
                                'config' => $c->config,
                            ])->all(),
                        ])->all(),
                    ],
                    'published_at' => now(),
                ],
            );
        }

        // Notify every user whose tracked install version lags behind the
        // newly published version (database channel → bell).
        $item->installs()
            ->where('version', '<', $item->version)
            ->where('user_id', '!=', $request->user()->id)
            ->with('user')
            ->chunkById(100, function ($installs) use ($item) {
                foreach ($installs as $install) {
                    $install->user?->notify(
                        new ListingUpdatedNotification($item, $item->version),
                    );
                }
            });

        return back()->with('success', __('Published to the marketplace.'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BenchmarkCollection;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CollectionController extends Controller
{
    public function index(Request $request): Response
    {
        $collections = $request->user()
            ->collections()
            ->withCount('benchmarks')
            ->get()
            ->map(fn (BenchmarkCollection $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'description' => $c->description,
                'benchmarks_count' => $c->benchmarks_count,
            ]);

        return Inertia::render('collections/Index', [
            'collections' => $collections,
            'benchmarks' => $request->user()->benchmarks()->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        /** @var User $user */
        $user = $request->user();

        $collection = $user->collections()->create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $collection->benchmarks()->sync($validated['benchmark_ids'] ?? []);

        return back()->with('success', __('Collection created.'));
    }

    public function update(Request $request, BenchmarkCollection $collection): RedirectResponse
    {
        $this->authorize('update', $collection);

        $validated = $this->validated($request);
        $collection->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);
        $collection->benchmarks()->sync($validated['benchmark_ids'] ?? []);

        return back()->with('success', __('Saved.'));
    }

    public function duplicate(Request $request, BenchmarkCollection $collection): RedirectResponse
    {
        $this->authorize('view', $collection);

        $copy = $request->user()->collections()->create([
            'name' => $collection->name.' (copy)',
            'description' => $collection->description,
        ]);

        $copy->benchmarks()->sync($collection->benchmarks()->pluck('benchmarks.id'));

        return back()->with('success', __('Collection duplicated.'));
    }

    public function destroy(Request $request, BenchmarkCollection $collection): RedirectResponse
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return back()->with('success', __('Collection deleted.'));
    }

    private function validated(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'benchmark_ids' => ['required', 'array', 'min:1'],
            'benchmark_ids.*' => ['integer', Rule::exists('benchmarks', 'id')->where('user_id', $user->id)],
        ]);
    }
}

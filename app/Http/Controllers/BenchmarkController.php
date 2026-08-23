<?php

namespace App\Http\Controllers;

use App\Enums\BenchmarkCategory;
use App\Enums\CriterionType;
use App\Models\AuditLog;
use App\Models\Benchmark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BenchmarkController extends Controller
{
    public function index(Request $request): Response
    {
        $benchmarks = Benchmark::withCount('cases')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (Benchmark $b) => [
                'id' => $b->id,
                'name' => $b->name,
                'description' => $b->description,
                'category' => $b->category->value,
                'visibility' => $b->visibility->value,
                'cases_count' => $b->cases_count,
                'version' => $b->version,
                'updated_at' => $b->updated_at->toIso8601String(),
            ]);

        return Inertia::render('benchmarks/Index', ['benchmarks' => $benchmarks]);
    }

    public function create(): Response
    {
        return Inertia::render('benchmarks/Wizard', [
            'categories' => array_map(
                fn (BenchmarkCategory $c) => ['value' => $c->value, 'label' => $c->label()],
                BenchmarkCategory::cases(),
            ),
            'criterionTypes' => array_map(
                fn (CriterionType $t) => ['value' => $t->value],
                CriterionType::cases(),
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBenchmark($request);

        $benchmark = DB::transaction(function () use ($request, $validated) {
            $benchmark = $request->user()->benchmarks()->create($this->basics($validated));

            foreach ($validated['cases'] as $i => $caseData) {
                $case = $benchmark->cases()->create([
                    'title' => $caseData['title'],
                    'input' => $caseData['input'],
                    'weight' => $caseData['weight'] ?? 1,
                    'position' => $i,
                ]);

                foreach ($caseData['criteria'] as $j => $criterion) {
                    $case->criteria()->create($this->criterion($criterion, $j));
                }
            }

            return $benchmark;
        });

        AuditLog::record('benchmark.created', 'benchmarks', 'Benchmark created', 'benchmark', (string) $benchmark->id, $benchmark->name);

        return redirect()->route('benchmarks.show', $benchmark)->with('success', __('Benchmark created.'));
    }

    public function show(Request $request, Benchmark $benchmark): Response
    {
        $this->authorize('view', $benchmark);

        $benchmark->load(['cases.criteria']);

        return Inertia::render('benchmarks/Show', [
            'benchmark' => [
                'id' => $benchmark->id,
                'name' => $benchmark->name,
                'description' => $benchmark->description,
                'category' => $benchmark->category->value,
                'visibility' => $benchmark->visibility->value,
                'version' => $benchmark->version,
                'cases' => $benchmark->cases->map(fn ($case) => [
                    'id' => $case->id,
                    'title' => $case->title,
                    'input' => $case->input,
                    'weight' => (float) $case->weight,
                    'criteria' => $case->criteria->map(fn ($c) => [
                        'id' => $c->id,
                        'type' => $c->type->value,
                        'label' => $c->label,
                        'config' => $c->config,
                    ]),
                ]),
            ],
            'prompts' => $request->user()->prompts()->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Benchmark $benchmark): RedirectResponse
    {
        $this->authorize('update', $benchmark);

        $validated = $this->validateBenchmark($request);

        DB::transaction(function () use ($benchmark, $validated) {
            $benchmark->update($this->basics($validated));

            // Full replace of cases: benchmarks are versioned test suites,
            // edits bump the version and rewrite the case set.
            $benchmark->cases()->delete();

            foreach ($validated['cases'] as $i => $caseData) {
                $case = $benchmark->cases()->create([
                    'title' => $caseData['title'],
                    'input' => $caseData['input'],
                    'weight' => $caseData['weight'] ?? 1,
                    'position' => $i,
                ]);

                foreach ($caseData['criteria'] as $j => $criterion) {
                    $case->criteria()->create($this->criterion($criterion, $j));
                }
            }

            $benchmark->bumpVersion();
        });

        AuditLog::record('benchmark.updated', 'benchmarks', 'Benchmark updated', 'benchmark', (string) $benchmark->id, $benchmark->name);

        return back()->with('success', __('Benchmark saved.'));
    }

    public function destroy(Request $request, Benchmark $benchmark): RedirectResponse
    {
        $this->authorize('delete', $benchmark);

        $name = $benchmark->name;
        $id = (string) $benchmark->id;

        $benchmark->delete();

        AuditLog::record('benchmark.deleted', 'benchmarks', 'Benchmark deleted', 'benchmark', $id, $name, 'warning');

        return redirect()->route('benchmarks.index')->with('success', __('Benchmark deleted.'));
    }

    private function validateBenchmark(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'in:'.implode(',', BenchmarkCategory::values())],
            'visibility' => ['required', 'in:private,public'],
            'cases' => ['required', 'array', 'min:1', 'max:50'],
            'cases.*.title' => ['required', 'string', 'max:255'],
            'cases.*.input' => ['required', 'string', 'max:20000'],
            'cases.*.weight' => ['nullable', 'numeric', 'min:0.1', 'max:10'],
            'cases.*.criteria' => ['required', 'array', 'min:1', 'max:10'],
            'cases.*.criteria.*.type' => ['required', 'in:contains,not_contains,regex,llm_judge'],
            'cases.*.criteria.*.label' => ['required', 'string', 'max:255'],
            'cases.*.criteria.*.config' => ['required', 'array'],
            'cases.*.criteria.*.config.values' => ['required_if:cases.*.criteria.*.type,contains,not_contains', 'array', 'max:10'],
            'cases.*.criteria.*.config.pattern' => ['required_if:cases.*.criteria.*.type,regex', 'string', 'max:500'],
            'cases.*.criteria.*.config.description' => ['required_if:cases.*.criteria.*.type,llm_judge', 'string', 'max:2000'],
        ]);
    }

    private function basics(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'visibility' => $validated['visibility'],
        ];
    }

    private function criterion(array $criterion, int $position): array
    {
        $type = CriterionType::from($criterion['type']);

        $config = match ($type) {
            CriterionType::Contains, CriterionType::NotContains => ['values' => array_values($criterion['config']['values'] ?? [])],
            CriterionType::Regex => ['pattern' => $criterion['config']['pattern'] ?? ''],
            CriterionType::LlmJudge => ['description' => $criterion['config']['description'] ?? $criterion['label']],
        };

        return [
            'type' => $type->value,
            'label' => $criterion['label'],
            'config' => $config,
            'position' => $position,
        ];
    }
}

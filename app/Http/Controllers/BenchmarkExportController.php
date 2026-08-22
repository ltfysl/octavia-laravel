<?php

namespace App\Http\Controllers;

use App\Models\Benchmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BenchmarkExportController extends Controller
{
    public function __invoke(Request $request, Benchmark $benchmark): JsonResponse|Response|StreamedResponse
    {
        $this->authorize('view', $benchmark);

        $benchmark->load(['cases.criteria']);

        $payload = [
            'name' => $benchmark->name,
            'description' => $benchmark->description,
            'category' => $benchmark->category->value,
            'version' => $benchmark->version,
            'exported_at' => now()->toIso8601String(),
            'cases' => $benchmark->cases->map(fn ($case) => [
                'title' => $case->title,
                'input' => $case->input,
                'weight' => (float) $case->weight,
                'criteria' => $case->criteria->map(fn ($c) => [
                    'type' => $c->type->value,
                    'label' => $c->label,
                    'config' => $c->config,
                ]),
            ]),
        ];

        $filename = 'octavia-benchmark-'.Str::slug($benchmark->name).'.json';

        return response()->json($payload)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}

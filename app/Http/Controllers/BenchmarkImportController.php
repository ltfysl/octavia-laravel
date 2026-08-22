<?php

namespace App\Http\Controllers;

use App\Actions\ImportBenchmark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BenchmarkImportController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:json', 'max:2048'],
        ]);

        $content = json_decode($request->file('file')->getContent(), true);

        if (! is_array($content)) {
            return back()->with('error', __('Invalid JSON file.'));
        }

        try {
            $benchmark = ImportBenchmark::fromArray($request->user(), $content);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('benchmarks.show', $benchmark)
            ->with('success', __('Benchmark imported.'));
    }
}

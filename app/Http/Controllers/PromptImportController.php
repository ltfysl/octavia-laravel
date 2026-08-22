<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use App\Models\PromptVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromptImportController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:json', 'max:2048'],
        ]);

        $data = json_decode($request->file('file')->getContent(), true);

        if (! is_array($data) || ! isset($data['name']) || ! isset($data['versions'])) {
            return back()->with('error', __('Invalid prompt export file.'));
        }

        $prompt = DB::transaction(function () use ($request, $data) {
            $prompt = $request->user()->prompts()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'visibility' => 'private',
            ]);

            foreach ($data['versions'] as $v) {
                if (! isset($v['version'], $v['content'])) {
                    continue;
                }

                $version = $prompt->versions()->create([
                    'version' => $v['version'],
                    'content' => $v['content'],
                    'changelog' => $v['changelog'] ?? null,
                    'created_at' => now(),
                ]);
                $prompt->update(['current_version_id' => $version->id]);
            }

            return $prompt;
        });

        return redirect()->route('prompts.show', $prompt)
            ->with('success', __('Prompt imported.'));
    }
}

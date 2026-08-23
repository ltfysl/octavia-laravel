<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConfigPresetRequest;
use App\Models\ConfigPreset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConfigPresetController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('settings/Presets', [
            'presets' => $request->user()->configPresets()
                ->orderBy('is_default', 'desc')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreConfigPresetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['is_default'])) {
            $request->user()->configPresets()->where('is_default', true)->update(['is_default' => false]);
        }

        $request->user()->configPresets()->create($data);

        return redirect()->route('settings.presets')->with('success', __('messages.presetCreated'));
    }

    public function update(StoreConfigPresetRequest $request, ConfigPreset $preset): RedirectResponse
    {
        abort_unless($preset->user_id === $request->user()->id, 404);

        $data = $request->validated();

        if (! empty($data['is_default']) && ! $preset->is_default) {
            $request->user()->configPresets()->where('is_default', true)->update(['is_default' => false]);
        }

        $preset->update($data);

        return redirect()->route('settings.presets')->with('success', __('messages.presetUpdated'));
    }

    public function destroy(Request $request, ConfigPreset $preset): RedirectResponse
    {
        abort_unless($preset->user_id === $request->user()->id, 404);

        $preset->delete();

        return redirect()->route('settings.presets')->with('success', __('messages.presetDeleted'));
    }
}

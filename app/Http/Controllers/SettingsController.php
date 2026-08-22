<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function profile(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'sessions' => $this->sessions($request),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'locale' => ['required', 'in:en,de'],
            'notify_run_completed_mail' => ['sometimes', 'boolean'],
        ]);

        $request->user()->update($validated);

        return back()->with('success', __('Saved.'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', __('Saved.'));
    }

    public function logoutOthers(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        Auth::logoutOtherDevices($request->input('password'));

        return back()->with('success', __('Saved.'));
    }

    /**
     * @return list<array{id: string, agent: string, ip: string, last_active: string, current: bool}>
     */
    private function sessions(Request $request): array
    {
        return DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('last_activity')
            ->limit(10)
            ->get()
            ->map(fn ($session) => [
                'id' => $session->id,
                'agent' => trim(preg_replace('/[^\w\s.\/-]/', '', (string) $session->user_agent)),
                'ip' => (string) $session->ip_address,
                'last_active' => date('c', $session->last_activity),
                'current' => $session->id === $request->session()->getId(),
            ])
            ->all();
    }
}

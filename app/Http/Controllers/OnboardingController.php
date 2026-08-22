<?php

namespace App\Http\Controllers;

use App\Actions\CreateStarterContent;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    public function welcome(Request $request): Response
    {
        return Inertia::render('onboarding/Welcome');
    }

    public function complete(Request $request)
    {
        $request->validate([
            'locale' => ['nullable', 'in:en,de'],
            'sample' => ['nullable', 'boolean'],
        ]);

        if ($request->filled('locale')) {
            $request->user()->update(['locale' => $request->input('locale')]);
        }

        if ($request->boolean('sample')) {
            CreateStarterContent::for($request->user());
        }

        $request->user()->forceFill(['onboarded_at' => now()])->save();

        $request->user()->notify(new WelcomeNotification($request->user()));

        return redirect()->route('dashboard');
    }
}

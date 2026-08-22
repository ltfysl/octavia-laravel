<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->withCount(['prompts', 'benchmarks', 'runs'])
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")))
            ->latest()
            ->paginate(20)
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->is_admin,
                'prompts_count' => $user->prompts_count,
                'benchmarks_count' => $user->benchmarks_count,
                'runs_count' => $user->runs_count,
                'created_at' => $user->created_at?->toIso8601String(),
            ])
            ->withQueryString();

        return Inertia::render('admin/Users', [
            'users' => $users,
            'filters' => ['q' => $search],
        ]);
    }

    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('You cannot change your own admin status.'));
        }

        $user->forceFill(['is_admin' => ! $user->is_admin])->save();

        return back()->with('success', __('Saved.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('You cannot delete your own account here.'));
        }

        $user->delete();

        return back()->with('success', __('User deleted.'));
    }
}

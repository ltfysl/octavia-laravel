<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        $teams = Team::query()
            ->where('owner_id', $user->id)
            ->orWhereHas('members', fn ($q) => $q->where('user_id', $user->id))
            ->withCount('users')
            ->get()
            ->map(fn (Team $team) => [
                'id' => $team->id,
                'name' => $team->name,
                'role' => $team->roleOf($user),
                'member_count' => $team->users_count,
                'is_owner' => $team->owner_id === $user->id,
            ]);

        return Inertia::render('teams/Index', ['teams' => $teams]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $team = $request->user()->ownedTeams()->create($validated);

        return back()->with('success', __('Team created.'));
    }

    public function show(Request $request, Team $team): Response
    {
        abort_unless($team->hasMember($request->user()), 403);

        $members = collect([$team->owner])->merge(
            $team->members()->with('user:id,name,email')->get()->pluck('user'),
        )->unique('id')->values()->map(fn ($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'role' => $team->roleOf($u),
        ]);

        return Inertia::render('teams/Show', [
            'team' => ['id' => $team->id, 'name' => $team->name, 'owner_id' => $team->owner_id],
            'members' => $members,
        ]);
    }

    public function invite(Request $request, Team $team): RedirectResponse
    {
        if ($team->owner_id !== $request->user()->id && $team->roleOf($request->user()) !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', Rule::in(['admin', 'member'])],
        ]);

        $invitee = User::where('email', $validated['email'])->first();

        if ($team->hasMember($invitee)) {
            return back()->with('error', __('User is already a member.'));
        }

        $team->members()->create([
            'user_id' => $invitee->id,
            'role' => $validated['role'],
        ]);

        return back()->with('success', __('Member added.'));
    }

    public function removeMember(Request $request, Team $team, User $member): RedirectResponse
    {
        $isOwner = $team->owner_id === $request->user()->id;

        // Only the owner can remove members; members can leave themselves.
        if (! $isOwner && $member->id !== $request->user()->id) {
            abort(403);
        }

        abort_if($member->id === $team->owner_id, 403);

        $team->members()->where('user_id', $member->id)->delete();

        return back()->with('success', __('Member removed.'));
    }

    public function destroy(Request $request, Team $team): RedirectResponse
    {
        abort_unless($team->owner_id === $request->user()->id, 403);

        $team->delete();

        return redirect()->route('teams.index')->with('success', __('Team deleted.'));
    }
}

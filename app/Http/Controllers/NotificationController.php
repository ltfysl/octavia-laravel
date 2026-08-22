<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $notifications = $request->user()
            ->notifications()
            ->paginate(20)
            ->through(fn ($n) => [
                'id' => $n->id,
                'read' => $n->read_at !== null,
                'run_id' => data_get($n, 'data.run_id'),
                'run_name' => data_get($n, 'data.run_name'),
                'status' => data_get($n, 'data.status'),
                'score' => data_get($n, 'data.best_score'),
                'at' => $n->created_at?->toIso8601String(),
            ]);

        return Inertia::render('notifications/Index', [
            // `feed`, not `notifications`: the shared prop of that name is the
            // app-shell bell payload ({unread, items}); a page prop would shadow it.
            'feed' => [
                'data' => $notifications->items(),
                'links' => $notifications->linkCollection()->toArray(),
            ],
            'unreadCount' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }

    public function markUnread(Request $request, string $id): RedirectResponse
    {
        $this->owned($request, $id)?->markAsUnread();

        return back();
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        // Scoped to the owner: a foreign id simply matches nothing.
        $this->owned($request, $id)?->delete();

        return back();
    }

    private function owned(Request $request, string $id)
    {
        return $request->user()->notifications()->where('id', $id)->first();
    }
}

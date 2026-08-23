/**
 * Laravel Echo client — Reverb transport for live run progress.
 * Falls back gracefully: callers keep their polling as a safety net,
 * so a missing/absent Reverb server degrades instead of breaking.
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

declare global {
    type AnyEcho = Echo<'reverb'>;
    interface Window {
        Pusher: typeof Pusher;
        Echo?: AnyEcho;
    }
}

window.Pusher = Pusher;

let echo: AnyEcho | null = null;

export function useEcho(): AnyEcho | null {
    const key = import.meta.env.VITE_REVERB_APP_KEY;
    if (!key) return null;

    if (!echo) {
        echo = new Echo<'reverb'>({
            broadcaster: 'reverb',
            key,
            wsHost: import.meta.env.VITE_REVERB_HOST ?? '127.0.0.1',
            wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8085),
            wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8085),
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
            enabledTransports: ['ws', 'wss'],
        });
    }

    return echo;
}

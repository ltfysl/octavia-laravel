@php
    // Translate with the recipient's locale, passed from the notification.
    $t = fn (string $key) => __($key, [], $locale);
@endphp

@component('mail::message')
{{-- Octavia-branded run completion mail. Locale is set by the notification. --}}

<x-mail::panel>
    <p style="margin:0 0 4px; font-size:12px; letter-spacing:.08em; text-transform:uppercase; color:#6b6b85;">
        {{ $t('notifications.run_completed.eyebrow') }}
    </p>

    <h2 style="margin:0 0 12px; font-size:18px; color:#101018;">
        {{ $run->name }}
    </h2>

    <p style="margin:0 0 16px; font-size:14px; color:#3d3d52;">
        {{ $t('notifications.run_completed.intro') }}
    </p>

    <table style="width:100%; border-collapse:collapse; margin-bottom:16px;" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding:8px 0; font-size:13px; color:#6b6b85; width:40%;">
                {{ $t('notifications.run_completed.score') }}
            </td>
            <td style="padding:8px 0; font-size:15px; font-weight:600; color:#178a63;">
                {{ $score }}
            </td>
        </tr>
        <tr>
            <td style="padding:8px 0; font-size:13px; color:#6b6b85;">
                {{ $t('notifications.run_completed.target') }}
            </td>
            <td style="padding:8px 0; font-size:14px; color:#1a1a26;">
                {{ round($run->target_score * 100) }}%
            </td>
        </tr>
        @if ($run->benchmark)
            <tr>
                <td style="padding:8px 0; font-size:13px; color:#6b6b85;">
                    {{ $t('notifications.run_completed.benchmark') }}
                </td>
                <td style="padding:8px 0; font-size:14px; color:#1a1a26;">
                    {{ $run->benchmark->name }}
                </td>
            </tr>
        @endif
    </table>
</x-mail::panel>

<x-mail::button :url="url('/runs/'.$run->id)">
{{ $t('notifications.run_completed.cta') }}
</x-mail::button>

{{ $t('notifications.run_completed.footer') }}
@endcomponent

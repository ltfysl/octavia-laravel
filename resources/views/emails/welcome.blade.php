@component('mail::message')
# {{ __('notifications.welcome.greeting', ['name' => $name], $locale) }}

{{ __('notifications.welcome.line1', [], $locale) }}

{{ __('notifications.welcome.line2', [], $locale) }}

@component('mail::button', ['url' => url('/dashboard'), 'color' => 'primary'])
{{ __('notifications.welcome.cta', [], $locale) }}
@endcomponent

{{ __('notifications.welcome.footer', [], $locale) }}
@endcomponent

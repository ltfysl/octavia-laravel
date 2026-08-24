import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import apiKeys9055d4 from './api-keys'
import providerKeys9d79ec from './provider-keys'
import webhooksCea6cd from './webhooks'
import profile937a89 from './profile'
import presets728a1d from './presets'
import password from './password'
/**
* @see \App\Http\Controllers\ApiKeyController::apiKeys
* @see app/Http/Controllers/ApiKeyController.php:15
* @route '/settings/api-keys'
*/
export const apiKeys = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: apiKeys.url(options),
    method: 'get',
})

apiKeys.definition = {
    methods: ["get","head"],
    url: '/settings/api-keys',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ApiKeyController::apiKeys
* @see app/Http/Controllers/ApiKeyController.php:15
* @route '/settings/api-keys'
*/
apiKeys.url = (options?: RouteQueryOptions) => {
    return apiKeys.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ApiKeyController::apiKeys
* @see app/Http/Controllers/ApiKeyController.php:15
* @route '/settings/api-keys'
*/
apiKeys.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: apiKeys.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ApiKeyController::apiKeys
* @see app/Http/Controllers/ApiKeyController.php:15
* @route '/settings/api-keys'
*/
apiKeys.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: apiKeys.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ProviderKeyController::providerKeys
* @see app/Http/Controllers/ProviderKeyController.php:14
* @route '/settings/provider-keys'
*/
export const providerKeys = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: providerKeys.url(options),
    method: 'get',
})

providerKeys.definition = {
    methods: ["get","head"],
    url: '/settings/provider-keys',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProviderKeyController::providerKeys
* @see app/Http/Controllers/ProviderKeyController.php:14
* @route '/settings/provider-keys'
*/
providerKeys.url = (options?: RouteQueryOptions) => {
    return providerKeys.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProviderKeyController::providerKeys
* @see app/Http/Controllers/ProviderKeyController.php:14
* @route '/settings/provider-keys'
*/
providerKeys.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: providerKeys.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProviderKeyController::providerKeys
* @see app/Http/Controllers/ProviderKeyController.php:14
* @route '/settings/provider-keys'
*/
providerKeys.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: providerKeys.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WebhookController::webhooks
* @see app/Http/Controllers/WebhookController.php:15
* @route '/settings/webhooks'
*/
export const webhooks = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webhooks.url(options),
    method: 'get',
})

webhooks.definition = {
    methods: ["get","head"],
    url: '/settings/webhooks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WebhookController::webhooks
* @see app/Http/Controllers/WebhookController.php:15
* @route '/settings/webhooks'
*/
webhooks.url = (options?: RouteQueryOptions) => {
    return webhooks.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WebhookController::webhooks
* @see app/Http/Controllers/WebhookController.php:15
* @route '/settings/webhooks'
*/
webhooks.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webhooks.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WebhookController::webhooks
* @see app/Http/Controllers/WebhookController.php:15
* @route '/settings/webhooks'
*/
webhooks.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: webhooks.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SettingsController::profile
* @see app/Http/Controllers/SettingsController.php:16
* @route '/settings/profile'
*/
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/settings/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SettingsController::profile
* @see app/Http/Controllers/SettingsController.php:16
* @route '/settings/profile'
*/
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SettingsController::profile
* @see app/Http/Controllers/SettingsController.php:16
* @route '/settings/profile'
*/
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SettingsController::profile
* @see app/Http/Controllers/SettingsController.php:16
* @route '/settings/profile'
*/
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SettingsController::billing
* @see app/Http/Controllers/SettingsController.php:23
* @route '/settings/billing'
*/
export const billing = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: billing.url(options),
    method: 'get',
})

billing.definition = {
    methods: ["get","head"],
    url: '/settings/billing',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SettingsController::billing
* @see app/Http/Controllers/SettingsController.php:23
* @route '/settings/billing'
*/
billing.url = (options?: RouteQueryOptions) => {
    return billing.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SettingsController::billing
* @see app/Http/Controllers/SettingsController.php:23
* @route '/settings/billing'
*/
billing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: billing.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SettingsController::billing
* @see app/Http/Controllers/SettingsController.php:23
* @route '/settings/billing'
*/
billing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: billing.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ConfigPresetController::presets
* @see app/Http/Controllers/ConfigPresetController.php:14
* @route '/settings/presets'
*/
export const presets = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: presets.url(options),
    method: 'get',
})

presets.definition = {
    methods: ["get","head"],
    url: '/settings/presets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConfigPresetController::presets
* @see app/Http/Controllers/ConfigPresetController.php:14
* @route '/settings/presets'
*/
presets.url = (options?: RouteQueryOptions) => {
    return presets.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConfigPresetController::presets
* @see app/Http/Controllers/ConfigPresetController.php:14
* @route '/settings/presets'
*/
presets.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: presets.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConfigPresetController::presets
* @see app/Http/Controllers/ConfigPresetController.php:14
* @route '/settings/presets'
*/
presets.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: presets.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TeamController::workspace
* @see app/Http/Controllers/TeamController.php:27
* @route '/settings/workspace'
*/
export const workspace = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: workspace.url(options),
    method: 'get',
})

workspace.definition = {
    methods: ["get","head"],
    url: '/settings/workspace',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TeamController::workspace
* @see app/Http/Controllers/TeamController.php:27
* @route '/settings/workspace'
*/
workspace.url = (options?: RouteQueryOptions) => {
    return workspace.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TeamController::workspace
* @see app/Http/Controllers/TeamController.php:27
* @route '/settings/workspace'
*/
workspace.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: workspace.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TeamController::workspace
* @see app/Http/Controllers/TeamController.php:27
* @route '/settings/workspace'
*/
workspace.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: workspace.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SettingsController::logoutOthers
* @see app/Http/Controllers/SettingsController.php:64
* @route '/settings/logout-others'
*/
export const logoutOthers = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logoutOthers.url(options),
    method: 'post',
})

logoutOthers.definition = {
    methods: ["post"],
    url: '/settings/logout-others',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SettingsController::logoutOthers
* @see app/Http/Controllers/SettingsController.php:64
* @route '/settings/logout-others'
*/
logoutOthers.url = (options?: RouteQueryOptions) => {
    return logoutOthers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SettingsController::logoutOthers
* @see app/Http/Controllers/SettingsController.php:64
* @route '/settings/logout-others'
*/
logoutOthers.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logoutOthers.url(options),
    method: 'post',
})

const settings = {
    apiKeys: Object.assign(apiKeys, apiKeys9055d4),
    providerKeys: Object.assign(providerKeys, providerKeys9d79ec),
    webhooks: Object.assign(webhooks, webhooksCea6cd),
    profile: Object.assign(profile, profile937a89),
    billing: Object.assign(billing, billing),
    presets: Object.assign(presets, presets728a1d),
    workspace: Object.assign(workspace, workspace),
    password: Object.assign(password, password),
    logoutOthers: Object.assign(logoutOthers, logoutOthers),
}

export default settings
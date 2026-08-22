import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import profile937a89 from './profile'
import password from './password'
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
    profile: Object.assign(profile, profile937a89),
    billing: Object.assign(billing, billing),
    password: Object.assign(password, password),
    logoutOthers: Object.assign(logoutOthers, logoutOthers),
}

export default settings
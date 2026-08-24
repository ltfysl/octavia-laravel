import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\SettingsController::updateProfile
* @see app/Http/Controllers/SettingsController.php:39
* @route '/settings/profile'
*/
export const updateProfile = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateProfile.url(options),
    method: 'patch',
})

updateProfile.definition = {
    methods: ["patch"],
    url: '/settings/profile',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\SettingsController::updateProfile
* @see app/Http/Controllers/SettingsController.php:39
* @route '/settings/profile'
*/
updateProfile.url = (options?: RouteQueryOptions) => {
    return updateProfile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SettingsController::updateProfile
* @see app/Http/Controllers/SettingsController.php:39
* @route '/settings/profile'
*/
updateProfile.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateProfile.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\SettingsController::updatePassword
* @see app/Http/Controllers/SettingsController.php:53
* @route '/settings/password'
*/
export const updatePassword = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatePassword.url(options),
    method: 'patch',
})

updatePassword.definition = {
    methods: ["patch"],
    url: '/settings/password',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\SettingsController::updatePassword
* @see app/Http/Controllers/SettingsController.php:53
* @route '/settings/password'
*/
updatePassword.url = (options?: RouteQueryOptions) => {
    return updatePassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SettingsController::updatePassword
* @see app/Http/Controllers/SettingsController.php:53
* @route '/settings/password'
*/
updatePassword.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatePassword.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\SettingsController::logoutOthers
* @see app/Http/Controllers/SettingsController.php:65
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
* @see app/Http/Controllers/SettingsController.php:65
* @route '/settings/logout-others'
*/
logoutOthers.url = (options?: RouteQueryOptions) => {
    return logoutOthers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SettingsController::logoutOthers
* @see app/Http/Controllers/SettingsController.php:65
* @route '/settings/logout-others'
*/
logoutOthers.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logoutOthers.url(options),
    method: 'post',
})

const SettingsController = { profile, billing, updateProfile, updatePassword, logoutOthers }

export default SettingsController
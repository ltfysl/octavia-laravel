import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import auth from './auth'
import prompts from './prompts'
import runs from './runs'
/**
* @see \App\Http\Controllers\ActivityController::activity
* @see app/Http/Controllers/ActivityController.php:25
* @route '/api/v1/activity'
*/
export const activity = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activity.url(options),
    method: 'get',
})

activity.definition = {
    methods: ["get","head"],
    url: '/api/v1/activity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ActivityController::activity
* @see app/Http/Controllers/ActivityController.php:25
* @route '/api/v1/activity'
*/
activity.url = (options?: RouteQueryOptions) => {
    return activity.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ActivityController::activity
* @see app/Http/Controllers/ActivityController.php:25
* @route '/api/v1/activity'
*/
activity.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activity.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ActivityController::activity
* @see app/Http/Controllers/ActivityController.php:25
* @route '/api/v1/activity'
*/
activity.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activity.url(options),
    method: 'head',
})

/**
* @see routes/api.php:31
* @route '/api/v1/me'
*/
export const me = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

me.definition = {
    methods: ["get","head"],
    url: '/api/v1/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/api.php:31
* @route '/api/v1/me'
*/
me.url = (options?: RouteQueryOptions) => {
    return me.definition.url + queryParams(options)
}

/**
* @see routes/api.php:31
* @route '/api/v1/me'
*/
me.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

/**
* @see routes/api.php:31
* @route '/api/v1/me'
*/
me.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: me.url(options),
    method: 'head',
})

const api = {
    auth: Object.assign(auth, auth),
    activity: Object.assign(activity, activity),
    me: Object.assign(me, me),
    prompts: Object.assign(prompts, prompts),
    runs: Object.assign(runs, runs),
}

export default api
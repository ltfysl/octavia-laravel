import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ActivityController::api
* @see app/Http/Controllers/ActivityController.php:25
* @route '/api/v1/activity'
*/
export const api = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: api.url(options),
    method: 'get',
})

api.definition = {
    methods: ["get","head"],
    url: '/api/v1/activity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ActivityController::api
* @see app/Http/Controllers/ActivityController.php:25
* @route '/api/v1/activity'
*/
api.url = (options?: RouteQueryOptions) => {
    return api.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ActivityController::api
* @see app/Http/Controllers/ActivityController.php:25
* @route '/api/v1/activity'
*/
api.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: api.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ActivityController::api
* @see app/Http/Controllers/ActivityController.php:25
* @route '/api/v1/activity'
*/
api.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: api.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ActivityController::index
* @see app/Http/Controllers/ActivityController.php:16
* @route '/activity'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/activity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ActivityController::index
* @see app/Http/Controllers/ActivityController.php:16
* @route '/activity'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ActivityController::index
* @see app/Http/Controllers/ActivityController.php:16
* @route '/activity'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ActivityController::index
* @see app/Http/Controllers/ActivityController.php:16
* @route '/activity'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const ActivityController = { api, index }

export default ActivityController
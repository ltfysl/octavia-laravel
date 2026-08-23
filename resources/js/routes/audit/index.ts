import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\AuditLogController::index
* @see app/Http/Controllers/AuditLogController.php:12
* @route '/audit'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/audit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AuditLogController::index
* @see app/Http/Controllers/AuditLogController.php:12
* @route '/audit'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AuditLogController::index
* @see app/Http/Controllers/AuditLogController.php:12
* @route '/audit'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AuditLogController::index
* @see app/Http/Controllers/AuditLogController.php:12
* @route '/audit'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AuditLogController::store
* @see app/Http/Controllers/AuditLogController.php:39
* @route '/audit'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/audit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AuditLogController::store
* @see app/Http/Controllers/AuditLogController.php:39
* @route '/audit'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AuditLogController::store
* @see app/Http/Controllers/AuditLogController.php:39
* @route '/audit'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const audit = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
}

export default audit
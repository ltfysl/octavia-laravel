import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AuthTokenController::store
* @see app/Http/Controllers/Api/AuthTokenController.php:16
* @route '/api/v1/auth/token'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/auth/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AuthTokenController::store
* @see app/Http/Controllers/Api/AuthTokenController.php:16
* @route '/api/v1/auth/token'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuthTokenController::store
* @see app/Http/Controllers/Api/AuthTokenController.php:16
* @route '/api/v1/auth/token'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AuthTokenController::destroy
* @see app/Http/Controllers/Api/AuthTokenController.php:48
* @route '/api/v1/auth/token'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/v1/auth/token',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\AuthTokenController::destroy
* @see app/Http/Controllers/Api/AuthTokenController.php:48
* @route '/api/v1/auth/token'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuthTokenController::destroy
* @see app/Http/Controllers/Api/AuthTokenController.php:48
* @route '/api/v1/auth/token'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

const AuthTokenController = { store, destroy }

export default AuthTokenController
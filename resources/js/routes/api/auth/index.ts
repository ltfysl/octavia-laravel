import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AuthTokenController::token
 * @see app/Http/Controllers/Api/AuthTokenController.php:16
 * @route '/api/v1/auth/token'
 */
export const token = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token.url(options),
    method: 'post',
})

token.definition = {
    methods: ["post"],
    url: '/api/v1/auth/token',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AuthTokenController::token
 * @see app/Http/Controllers/Api/AuthTokenController.php:16
 * @route '/api/v1/auth/token'
 */
token.url = (options?: RouteQueryOptions) => {
    return token.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuthTokenController::token
 * @see app/Http/Controllers/Api/AuthTokenController.php:16
 * @route '/api/v1/auth/token'
 */
token.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: token.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AuthTokenController::logout
 * @see app/Http/Controllers/Api/AuthTokenController.php:54
 * @route '/api/v1/auth/token'
 */
export const logout = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: logout.url(options),
    method: 'delete',
})

logout.definition = {
    methods: ["delete"],
    url: '/api/v1/auth/token',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\AuthTokenController::logout
 * @see app/Http/Controllers/Api/AuthTokenController.php:54
 * @route '/api/v1/auth/token'
 */
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AuthTokenController::logout
 * @see app/Http/Controllers/Api/AuthTokenController.php:54
 * @route '/api/v1/auth/token'
 */
logout.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: logout.url(options),
    method: 'delete',
})
const auth = {
    token: Object.assign(token, token),
logout: Object.assign(logout, logout),
}

export default auth
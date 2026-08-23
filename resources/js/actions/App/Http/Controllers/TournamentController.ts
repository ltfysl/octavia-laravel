import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TournamentController::index
* @see app/Http/Controllers/TournamentController.php:22
* @route '/tournaments'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/tournaments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TournamentController::index
* @see app/Http/Controllers/TournamentController.php:22
* @route '/tournaments'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TournamentController::index
* @see app/Http/Controllers/TournamentController.php:22
* @route '/tournaments'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TournamentController::index
* @see app/Http/Controllers/TournamentController.php:22
* @route '/tournaments'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TournamentController::store
* @see app/Http/Controllers/TournamentController.php:60
* @route '/tournaments'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/tournaments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TournamentController::store
* @see app/Http/Controllers/TournamentController.php:60
* @route '/tournaments'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TournamentController::store
* @see app/Http/Controllers/TournamentController.php:60
* @route '/tournaments'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const TournamentController = { index, store }

export default TournamentController
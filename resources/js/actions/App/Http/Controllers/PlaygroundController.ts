import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PlaygroundController::index
* @see app/Http/Controllers/PlaygroundController.php:14
* @route '/playground'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/playground',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PlaygroundController::index
* @see app/Http/Controllers/PlaygroundController.php:14
* @route '/playground'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PlaygroundController::index
* @see app/Http/Controllers/PlaygroundController.php:14
* @route '/playground'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PlaygroundController::index
* @see app/Http/Controllers/PlaygroundController.php:14
* @route '/playground'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PlaygroundController::chat
* @see app/Http/Controllers/PlaygroundController.php:19
* @route '/playground/chat'
*/
export const chat = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chat.url(options),
    method: 'post',
})

chat.definition = {
    methods: ["post"],
    url: '/playground/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PlaygroundController::chat
* @see app/Http/Controllers/PlaygroundController.php:19
* @route '/playground/chat'
*/
chat.url = (options?: RouteQueryOptions) => {
    return chat.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PlaygroundController::chat
* @see app/Http/Controllers/PlaygroundController.php:19
* @route '/playground/chat'
*/
chat.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chat.url(options),
    method: 'post',
})

const PlaygroundController = { index, chat }

export default PlaygroundController
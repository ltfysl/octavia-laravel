import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
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

const playground = {
    chat: Object.assign(chat, chat),
}

export default playground
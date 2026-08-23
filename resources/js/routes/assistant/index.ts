import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\AssistantController::__invoke
* @see app/Http/Controllers/AssistantController.php:16
* @route '/assistant/chat'
*/
export const chat = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chat.url(options),
    method: 'post',
})

chat.definition = {
    methods: ["post"],
    url: '/assistant/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AssistantController::__invoke
* @see app/Http/Controllers/AssistantController.php:16
* @route '/assistant/chat'
*/
chat.url = (options?: RouteQueryOptions) => {
    return chat.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssistantController::__invoke
* @see app/Http/Controllers/AssistantController.php:16
* @route '/assistant/chat'
*/
chat.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: chat.url(options),
    method: 'post',
})

const assistant = {
    chat: Object.assign(chat, chat),
}

export default assistant
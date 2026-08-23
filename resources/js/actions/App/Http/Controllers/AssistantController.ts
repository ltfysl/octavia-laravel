import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AssistantController::__invoke
* @see app/Http/Controllers/AssistantController.php:16
* @route '/assistant/chat'
*/
const AssistantController = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: AssistantController.url(options),
    method: 'post',
})

AssistantController.definition = {
    methods: ["post"],
    url: '/assistant/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AssistantController::__invoke
* @see app/Http/Controllers/AssistantController.php:16
* @route '/assistant/chat'
*/
AssistantController.url = (options?: RouteQueryOptions) => {
    return AssistantController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AssistantController::__invoke
* @see app/Http/Controllers/AssistantController.php:16
* @route '/assistant/chat'
*/
AssistantController.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: AssistantController.url(options),
    method: 'post',
})

export default AssistantController
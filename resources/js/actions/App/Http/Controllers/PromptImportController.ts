import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptImportController::__invoke
* @see app/Http/Controllers/PromptImportController.php:11
* @route '/prompts/import'
*/
export const __invoke = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: __invoke.url(options),
    method: 'post',
})

__invoke.definition = {
    methods: ["post"],
    url: '/prompts/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptImportController::__invoke
* @see app/Http/Controllers/PromptImportController.php:11
* @route '/prompts/import'
*/
__invoke.url = (options?: RouteQueryOptions) => {
    return __invoke.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptImportController::__invoke
* @see app/Http/Controllers/PromptImportController.php:11
* @route '/prompts/import'
*/
__invoke.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: __invoke.url(options),
    method: 'post',
})

const PromptImportController = { __invoke }

export default PromptImportController
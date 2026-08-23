import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptInsightController::__invoke
* @see app/Http/Controllers/PromptInsightController.php:15
* @route '/prompts/{prompt}/insight'
*/
const PromptInsightController = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PromptInsightController.url(args, options),
    method: 'post',
})

PromptInsightController.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/insight',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptInsightController::__invoke
* @see app/Http/Controllers/PromptInsightController.php:15
* @route '/prompts/{prompt}/insight'
*/
PromptInsightController.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { prompt: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { prompt: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            prompt: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        prompt: typeof args.prompt === 'object'
        ? args.prompt.id
        : args.prompt,
    }

    return PromptInsightController.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptInsightController::__invoke
* @see app/Http/Controllers/PromptInsightController.php:15
* @route '/prompts/{prompt}/insight'
*/
PromptInsightController.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PromptInsightController.url(args, options),
    method: 'post',
})

export default PromptInsightController
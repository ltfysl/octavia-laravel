import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptDiffExplainController::__invoke
* @see app/Http/Controllers/PromptDiffExplainController.php:14
* @route '/prompts/{prompt}/diff-explain'
*/
const PromptDiffExplainController = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PromptDiffExplainController.url(args, options),
    method: 'post',
})

PromptDiffExplainController.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/diff-explain',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptDiffExplainController::__invoke
* @see app/Http/Controllers/PromptDiffExplainController.php:14
* @route '/prompts/{prompt}/diff-explain'
*/
PromptDiffExplainController.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return PromptDiffExplainController.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptDiffExplainController::__invoke
* @see app/Http/Controllers/PromptDiffExplainController.php:14
* @route '/prompts/{prompt}/diff-explain'
*/
PromptDiffExplainController.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PromptDiffExplainController.url(args, options),
    method: 'post',
})

export default PromptDiffExplainController
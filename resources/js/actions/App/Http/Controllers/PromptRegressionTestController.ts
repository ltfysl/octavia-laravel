import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptRegressionTestController::__invoke
* @see app/Http/Controllers/PromptRegressionTestController.php:13
* @route '/prompts/{prompt}/regression-test'
*/
const PromptRegressionTestController = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PromptRegressionTestController.url(args, options),
    method: 'post',
})

PromptRegressionTestController.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/regression-test',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptRegressionTestController::__invoke
* @see app/Http/Controllers/PromptRegressionTestController.php:13
* @route '/prompts/{prompt}/regression-test'
*/
PromptRegressionTestController.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return PromptRegressionTestController.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptRegressionTestController::__invoke
* @see app/Http/Controllers/PromptRegressionTestController.php:13
* @route '/prompts/{prompt}/regression-test'
*/
PromptRegressionTestController.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PromptRegressionTestController.url(args, options),
    method: 'post',
})

export default PromptRegressionTestController
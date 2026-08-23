import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptAbTestController::__invoke
* @see app/Http/Controllers/PromptAbTestController.php:15
* @route '/prompts/{prompt}/ab-test'
*/
export const insight = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: insight.url(args, options),
    method: 'post',
})

insight.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/ab-test',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptAbTestController::__invoke
* @see app/Http/Controllers/PromptAbTestController.php:15
* @route '/prompts/{prompt}/ab-test'
*/
insight.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return insight.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptAbTestController::__invoke
* @see app/Http/Controllers/PromptAbTestController.php:15
* @route '/prompts/{prompt}/ab-test'
*/
insight.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: insight.url(args, options),
    method: 'post',
})

const abTestprompts = {
    insight: Object.assign(insight, insight),
}

export default abTestprompts
import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptAbTestController::__invoke
* @see app/Http/Controllers/PromptAbTestController.php:15
* @route '/prompts/{prompt}/ab-test'
*/
const PromptAbTestController = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PromptAbTestController.url(args, options),
    method: 'post',
})

PromptAbTestController.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/ab-test',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptAbTestController::__invoke
* @see app/Http/Controllers/PromptAbTestController.php:15
* @route '/prompts/{prompt}/ab-test'
*/
PromptAbTestController.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return PromptAbTestController.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptAbTestController::__invoke
* @see app/Http/Controllers/PromptAbTestController.php:15
* @route '/prompts/{prompt}/ab-test'
*/
PromptAbTestController.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: PromptAbTestController.url(args, options),
    method: 'post',
})

export default PromptAbTestController
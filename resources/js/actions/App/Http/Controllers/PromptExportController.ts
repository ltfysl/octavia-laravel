import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptExportController::__invoke
 * @see app/Http/Controllers/PromptExportController.php:12
 * @route '/prompts/{prompt}/export'
 */
const PromptExportController = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PromptExportController.url(args, options),
    method: 'get',
})

PromptExportController.definition = {
    methods: ["get","head"],
    url: '/prompts/{prompt}/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptExportController::__invoke
 * @see app/Http/Controllers/PromptExportController.php:12
 * @route '/prompts/{prompt}/export'
 */
PromptExportController.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return PromptExportController.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptExportController::__invoke
 * @see app/Http/Controllers/PromptExportController.php:12
 * @route '/prompts/{prompt}/export'
 */
PromptExportController.get = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PromptExportController.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PromptExportController::__invoke
 * @see app/Http/Controllers/PromptExportController.php:12
 * @route '/prompts/{prompt}/export'
 */
PromptExportController.head = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: PromptExportController.url(args, options),
    method: 'head',
})
export default PromptExportController
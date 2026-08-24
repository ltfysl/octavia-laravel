import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptTemplateController::index
* @see app/Http/Controllers/PromptTemplateController.php:11
* @route '/prompts/templates/list'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/prompts/templates/list',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptTemplateController::index
* @see app/Http/Controllers/PromptTemplateController.php:11
* @route '/prompts/templates/list'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptTemplateController::index
* @see app/Http/Controllers/PromptTemplateController.php:11
* @route '/prompts/templates/list'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PromptTemplateController::index
* @see app/Http/Controllers/PromptTemplateController.php:11
* @route '/prompts/templates/list'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PromptTemplateController::show
* @see app/Http/Controllers/PromptTemplateController.php:22
* @route '/prompts/templates/{template}'
*/
export const show = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/prompts/templates/{template}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptTemplateController::show
* @see app/Http/Controllers/PromptTemplateController.php:22
* @route '/prompts/templates/{template}'
*/
show.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { template: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            template: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        template: typeof args.template === 'object'
        ? args.template.id
        : args.template,
    }

    return show.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptTemplateController::show
* @see app/Http/Controllers/PromptTemplateController.php:22
* @route '/prompts/templates/{template}'
*/
show.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PromptTemplateController::show
* @see app/Http/Controllers/PromptTemplateController.php:22
* @route '/prompts/templates/{template}'
*/
show.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const PromptTemplateController = { index, show }

export default PromptTemplateController
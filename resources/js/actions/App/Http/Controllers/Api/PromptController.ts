import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PromptController::index
* @see app/Http/Controllers/Api/PromptController.php:19
* @route '/api/v1/prompts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/v1/prompts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PromptController::index
* @see app/Http/Controllers/Api/PromptController.php:19
* @route '/api/v1/prompts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptController::index
* @see app/Http/Controllers/Api/PromptController.php:19
* @route '/api/v1/prompts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptController::index
* @see app/Http/Controllers/Api/PromptController.php:19
* @route '/api/v1/prompts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PromptController::store
* @see app/Http/Controllers/Api/PromptController.php:36
* @route '/api/v1/prompts'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/v1/prompts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PromptController::store
* @see app/Http/Controllers/Api/PromptController.php:36
* @route '/api/v1/prompts'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptController::store
* @see app/Http/Controllers/Api/PromptController.php:36
* @route '/api/v1/prompts'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptController::show
* @see app/Http/Controllers/Api/PromptController.php:29
* @route '/api/v1/prompts/{prompt}'
*/
export const show = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/v1/prompts/{prompt}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PromptController::show
* @see app/Http/Controllers/Api/PromptController.php:29
* @route '/api/v1/prompts/{prompt}'
*/
show.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptController::show
* @see app/Http/Controllers/Api/PromptController.php:29
* @route '/api/v1/prompts/{prompt}'
*/
show.get = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptController::show
* @see app/Http/Controllers/Api/PromptController.php:29
* @route '/api/v1/prompts/{prompt}'
*/
show.head = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PromptController::diff
* @see app/Http/Controllers/Api/PromptController.php:91
* @route '/api/v1/prompts/{prompt}/diff'
*/
export const diff = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: diff.url(args, options),
    method: 'get',
})

diff.definition = {
    methods: ["get","head"],
    url: '/api/v1/prompts/{prompt}/diff',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PromptController::diff
* @see app/Http/Controllers/Api/PromptController.php:91
* @route '/api/v1/prompts/{prompt}/diff'
*/
diff.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return diff.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptController::diff
* @see app/Http/Controllers/Api/PromptController.php:91
* @route '/api/v1/prompts/{prompt}/diff'
*/
diff.get = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: diff.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PromptController::diff
* @see app/Http/Controllers/Api/PromptController.php:91
* @route '/api/v1/prompts/{prompt}/diff'
*/
diff.head = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: diff.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PromptController::duplicate
* @see app/Http/Controllers/Api/PromptController.php:126
* @route '/api/v1/prompts/{prompt}/duplicate'
*/
export const duplicate = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: duplicate.url(args, options),
    method: 'post',
})

duplicate.definition = {
    methods: ["post"],
    url: '/api/v1/prompts/{prompt}/duplicate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PromptController::duplicate
* @see app/Http/Controllers/Api/PromptController.php:126
* @route '/api/v1/prompts/{prompt}/duplicate'
*/
duplicate.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return duplicate.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptController::duplicate
* @see app/Http/Controllers/Api/PromptController.php:126
* @route '/api/v1/prompts/{prompt}/duplicate'
*/
duplicate.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: duplicate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PromptController::evaluate
* @see app/Http/Controllers/Api/PromptController.php:63
* @route '/api/v1/prompts/{prompt}/evaluate'
*/
export const evaluate = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: evaluate.url(args, options),
    method: 'post',
})

evaluate.definition = {
    methods: ["post"],
    url: '/api/v1/prompts/{prompt}/evaluate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PromptController::evaluate
* @see app/Http/Controllers/Api/PromptController.php:63
* @route '/api/v1/prompts/{prompt}/evaluate'
*/
evaluate.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return evaluate.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PromptController::evaluate
* @see app/Http/Controllers/Api/PromptController.php:63
* @route '/api/v1/prompts/{prompt}/evaluate'
*/
evaluate.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: evaluate.url(args, options),
    method: 'post',
})

const PromptController = { index, store, show, diff, duplicate, evaluate }

export default PromptController
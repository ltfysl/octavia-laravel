import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PromptController::templates
* @see app/Http/Controllers/PromptController.php:63
* @route '/prompts/templates'
*/
export const templates = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: templates.url(options),
    method: 'get',
})

templates.definition = {
    methods: ["get","head"],
    url: '/prompts/templates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptController::templates
* @see app/Http/Controllers/PromptController.php:63
* @route '/prompts/templates'
*/
templates.url = (options?: RouteQueryOptions) => {
    return templates.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::templates
* @see app/Http/Controllers/PromptController.php:63
* @route '/prompts/templates'
*/
templates.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: templates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PromptController::templates
* @see app/Http/Controllers/PromptController.php:63
* @route '/prompts/templates'
*/
templates.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: templates.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PromptController::index
* @see app/Http/Controllers/PromptController.php:25
* @route '/prompts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/prompts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptController::index
* @see app/Http/Controllers/PromptController.php:25
* @route '/prompts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::index
* @see app/Http/Controllers/PromptController.php:25
* @route '/prompts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PromptController::index
* @see app/Http/Controllers/PromptController.php:25
* @route '/prompts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PromptController::create
* @see app/Http/Controllers/PromptController.php:45
* @route '/prompts/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/prompts/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptController::create
* @see app/Http/Controllers/PromptController.php:45
* @route '/prompts/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::create
* @see app/Http/Controllers/PromptController.php:45
* @route '/prompts/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PromptController::create
* @see app/Http/Controllers/PromptController.php:45
* @route '/prompts/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PromptController::store
* @see app/Http/Controllers/PromptController.php:68
* @route '/prompts'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/prompts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptController::store
* @see app/Http/Controllers/PromptController.php:68
* @route '/prompts'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::store
* @see app/Http/Controllers/PromptController.php:68
* @route '/prompts'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PromptController::show
* @see app/Http/Controllers/PromptController.php:86
* @route '/prompts/{prompt}'
*/
export const show = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/prompts/{prompt}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptController::show
* @see app/Http/Controllers/PromptController.php:86
* @route '/prompts/{prompt}'
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
* @see \App\Http\Controllers\PromptController::show
* @see app/Http/Controllers/PromptController.php:86
* @route '/prompts/{prompt}'
*/
show.get = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PromptController::show
* @see app/Http/Controllers/PromptController.php:86
* @route '/prompts/{prompt}'
*/
show.head = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PromptController::update
* @see app/Http/Controllers/PromptController.php:185
* @route '/prompts/{prompt}'
*/
export const update = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/prompts/{prompt}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\PromptController::update
* @see app/Http/Controllers/PromptController.php:185
* @route '/prompts/{prompt}'
*/
update.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::update
* @see app/Http/Controllers/PromptController.php:185
* @route '/prompts/{prompt}'
*/
update.put = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\PromptController::update
* @see app/Http/Controllers/PromptController.php:185
* @route '/prompts/{prompt}'
*/
update.patch = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\PromptController::destroy
* @see app/Http/Controllers/PromptController.php:224
* @route '/prompts/{prompt}'
*/
export const destroy = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/prompts/{prompt}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\PromptController::destroy
* @see app/Http/Controllers/PromptController.php:224
* @route '/prompts/{prompt}'
*/
destroy.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::destroy
* @see app/Http/Controllers/PromptController.php:224
* @route '/prompts/{prompt}'
*/
destroy.delete = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\PromptController::playground
* @see app/Http/Controllers/PromptController.php:115
* @route '/prompts/{prompt}/playground'
*/
export const playground = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: playground.url(args, options),
    method: 'post',
})

playground.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/playground',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptController::playground
* @see app/Http/Controllers/PromptController.php:115
* @route '/prompts/{prompt}/playground'
*/
playground.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return playground.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::playground
* @see app/Http/Controllers/PromptController.php:115
* @route '/prompts/{prompt}/playground'
*/
playground.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: playground.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PromptController::playgroundChat
* @see app/Http/Controllers/PromptController.php:134
* @route '/prompts/{prompt}/playground/chat'
*/
export const playgroundChat = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: playgroundChat.url(args, options),
    method: 'post',
})

playgroundChat.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/playground/chat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptController::playgroundChat
* @see app/Http/Controllers/PromptController.php:134
* @route '/prompts/{prompt}/playground/chat'
*/
playgroundChat.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return playgroundChat.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::playgroundChat
* @see app/Http/Controllers/PromptController.php:134
* @route '/prompts/{prompt}/playground/chat'
*/
playgroundChat.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: playgroundChat.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PromptController::diff
* @see app/Http/Controllers/PromptController.php:155
* @route '/prompts/{prompt}/diff'
*/
export const diff = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: diff.url(args, options),
    method: 'get',
})

diff.definition = {
    methods: ["get","head"],
    url: '/prompts/{prompt}/diff',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptController::diff
* @see app/Http/Controllers/PromptController.php:155
* @route '/prompts/{prompt}/diff'
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
* @see \App\Http\Controllers\PromptController::diff
* @see app/Http/Controllers/PromptController.php:155
* @route '/prompts/{prompt}/diff'
*/
diff.get = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: diff.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PromptController::diff
* @see app/Http/Controllers/PromptController.php:155
* @route '/prompts/{prompt}/diff'
*/
diff.head = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: diff.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PromptController::analytics
* @see app/Http/Controllers/PromptController.php:270
* @route '/prompts/{prompt}/analytics'
*/
export const analytics = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: analytics.url(args, options),
    method: 'get',
})

analytics.definition = {
    methods: ["get","head"],
    url: '/prompts/{prompt}/analytics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PromptController::analytics
* @see app/Http/Controllers/PromptController.php:270
* @route '/prompts/{prompt}/analytics'
*/
analytics.url = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return analytics.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::analytics
* @see app/Http/Controllers/PromptController.php:270
* @route '/prompts/{prompt}/analytics'
*/
analytics.get = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: analytics.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PromptController::analytics
* @see app/Http/Controllers/PromptController.php:270
* @route '/prompts/{prompt}/analytics'
*/
analytics.head = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: analytics.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PromptController::duplicate
* @see app/Http/Controllers/PromptController.php:238
* @route '/prompts/{prompt}/duplicate'
*/
export const duplicate = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: duplicate.url(args, options),
    method: 'post',
})

duplicate.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/duplicate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptController::duplicate
* @see app/Http/Controllers/PromptController.php:238
* @route '/prompts/{prompt}/duplicate'
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
* @see \App\Http\Controllers\PromptController::duplicate
* @see app/Http/Controllers/PromptController.php:238
* @route '/prompts/{prompt}/duplicate'
*/
duplicate.post = (args: { prompt: number | { id: number } } | [prompt: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: duplicate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PromptController::restoreVersion
* @see app/Http/Controllers/PromptController.php:207
* @route '/prompts/{prompt}/versions/{version}/restore'
*/
export const restoreVersion = (args: { prompt: number | { id: number }, version: number | { id: number } } | [prompt: number | { id: number }, version: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreVersion.url(args, options),
    method: 'post',
})

restoreVersion.definition = {
    methods: ["post"],
    url: '/prompts/{prompt}/versions/{version}/restore',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PromptController::restoreVersion
* @see app/Http/Controllers/PromptController.php:207
* @route '/prompts/{prompt}/versions/{version}/restore'
*/
restoreVersion.url = (args: { prompt: number | { id: number }, version: number | { id: number } } | [prompt: number | { id: number }, version: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            prompt: args[0],
            version: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        prompt: typeof args.prompt === 'object'
        ? args.prompt.id
        : args.prompt,
        version: typeof args.version === 'object'
        ? args.version.id
        : args.version,
    }

    return restoreVersion.definition.url
            .replace('{prompt}', parsedArgs.prompt.toString())
            .replace('{version}', parsedArgs.version.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PromptController::restoreVersion
* @see app/Http/Controllers/PromptController.php:207
* @route '/prompts/{prompt}/versions/{version}/restore'
*/
restoreVersion.post = (args: { prompt: number | { id: number }, version: number | { id: number } } | [prompt: number | { id: number }, version: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restoreVersion.url(args, options),
    method: 'post',
})

const PromptController = { templates, index, create, store, show, update, destroy, playground, playgroundChat, diff, analytics, duplicate, restoreVersion }

export default PromptController
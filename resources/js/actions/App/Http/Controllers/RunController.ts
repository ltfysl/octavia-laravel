import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\RunController::index
* @see app/Http/Controllers/RunController.php:20
* @route '/runs'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/runs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RunController::index
* @see app/Http/Controllers/RunController.php:20
* @route '/runs'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RunController::index
* @see app/Http/Controllers/RunController.php:20
* @route '/runs'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RunController::index
* @see app/Http/Controllers/RunController.php:20
* @route '/runs'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RunController::create
* @see app/Http/Controllers/RunController.php:42
* @route '/runs/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/runs/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RunController::create
* @see app/Http/Controllers/RunController.php:42
* @route '/runs/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RunController::create
* @see app/Http/Controllers/RunController.php:42
* @route '/runs/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RunController::create
* @see app/Http/Controllers/RunController.php:42
* @route '/runs/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RunController::show
* @see app/Http/Controllers/RunController.php:139
* @route '/runs/{run}'
*/
export const show = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/runs/{run}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RunController::show
* @see app/Http/Controllers/RunController.php:139
* @route '/runs/{run}'
*/
show.url = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { run: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { run: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            run: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        run: typeof args.run === 'object'
        ? args.run.id
        : args.run,
    }

    return show.definition.url
            .replace('{run}', parsedArgs.run.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RunController::show
* @see app/Http/Controllers/RunController.php:139
* @route '/runs/{run}'
*/
show.get = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RunController::show
* @see app/Http/Controllers/RunController.php:139
* @route '/runs/{run}'
*/
show.head = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RunController::store
* @see app/Http/Controllers/RunController.php:68
* @route '/runs'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/runs',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RunController::store
* @see app/Http/Controllers/RunController.php:68
* @route '/runs'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RunController::store
* @see app/Http/Controllers/RunController.php:68
* @route '/runs'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RunController::status
* @see app/Http/Controllers/RunController.php:191
* @route '/runs/{run}/status'
*/
export const status = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/runs/{run}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RunController::status
* @see app/Http/Controllers/RunController.php:191
* @route '/runs/{run}/status'
*/
status.url = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { run: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { run: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            run: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        run: typeof args.run === 'object'
        ? args.run.id
        : args.run,
    }

    return status.definition.url
            .replace('{run}', parsedArgs.run.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RunController::status
* @see app/Http/Controllers/RunController.php:191
* @route '/runs/{run}/status'
*/
status.get = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RunController::status
* @see app/Http/Controllers/RunController.php:191
* @route '/runs/{run}/status'
*/
status.head = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RunController::cancel
* @see app/Http/Controllers/RunController.php:202
* @route '/runs/{run}/cancel'
*/
export const cancel = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

cancel.definition = {
    methods: ["post"],
    url: '/runs/{run}/cancel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RunController::cancel
* @see app/Http/Controllers/RunController.php:202
* @route '/runs/{run}/cancel'
*/
cancel.url = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { run: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { run: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            run: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        run: typeof args.run === 'object'
        ? args.run.id
        : args.run,
    }

    return cancel.definition.url
            .replace('{run}', parsedArgs.run.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RunController::cancel
* @see app/Http/Controllers/RunController.php:202
* @route '/runs/{run}/cancel'
*/
cancel.post = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

const RunController = { index, create, show, store, status, cancel }

export default RunController
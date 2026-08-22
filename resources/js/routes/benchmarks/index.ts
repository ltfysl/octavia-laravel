import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\BenchmarkController::create
* @see app/Http/Controllers/BenchmarkController.php:36
* @route '/benchmarks/wizard'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/benchmarks/wizard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BenchmarkController::create
* @see app/Http/Controllers/BenchmarkController.php:36
* @route '/benchmarks/wizard'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkController::create
* @see app/Http/Controllers/BenchmarkController.php:36
* @route '/benchmarks/wizard'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BenchmarkController::create
* @see app/Http/Controllers/BenchmarkController.php:36
* @route '/benchmarks/wizard'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BenchmarkExportController::__invoke
* @see app/Http/Controllers/BenchmarkExportController.php:14
* @route '/benchmarks/{benchmark}/export'
*/
export const exportMethod = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/benchmarks/{benchmark}/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BenchmarkExportController::__invoke
* @see app/Http/Controllers/BenchmarkExportController.php:14
* @route '/benchmarks/{benchmark}/export'
*/
exportMethod.url = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { benchmark: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { benchmark: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            benchmark: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        benchmark: typeof args.benchmark === 'object'
        ? args.benchmark.id
        : args.benchmark,
    }

    return exportMethod.definition.url
            .replace('{benchmark}', parsedArgs.benchmark.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkExportController::__invoke
* @see app/Http/Controllers/BenchmarkExportController.php:14
* @route '/benchmarks/{benchmark}/export'
*/
exportMethod.get = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BenchmarkExportController::__invoke
* @see app/Http/Controllers/BenchmarkExportController.php:14
* @route '/benchmarks/{benchmark}/export'
*/
exportMethod.head = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BenchmarkController::index
* @see app/Http/Controllers/BenchmarkController.php:16
* @route '/benchmarks'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/benchmarks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BenchmarkController::index
* @see app/Http/Controllers/BenchmarkController.php:16
* @route '/benchmarks'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkController::index
* @see app/Http/Controllers/BenchmarkController.php:16
* @route '/benchmarks'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BenchmarkController::index
* @see app/Http/Controllers/BenchmarkController.php:16
* @route '/benchmarks'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BenchmarkController::store
* @see app/Http/Controllers/BenchmarkController.php:50
* @route '/benchmarks'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/benchmarks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BenchmarkController::store
* @see app/Http/Controllers/BenchmarkController.php:50
* @route '/benchmarks'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkController::store
* @see app/Http/Controllers/BenchmarkController.php:50
* @route '/benchmarks'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BenchmarkController::show
* @see app/Http/Controllers/BenchmarkController.php:76
* @route '/benchmarks/{benchmark}'
*/
export const show = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/benchmarks/{benchmark}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BenchmarkController::show
* @see app/Http/Controllers/BenchmarkController.php:76
* @route '/benchmarks/{benchmark}'
*/
show.url = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { benchmark: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { benchmark: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            benchmark: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        benchmark: typeof args.benchmark === 'object'
        ? args.benchmark.id
        : args.benchmark,
    }

    return show.definition.url
            .replace('{benchmark}', parsedArgs.benchmark.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkController::show
* @see app/Http/Controllers/BenchmarkController.php:76
* @route '/benchmarks/{benchmark}'
*/
show.get = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BenchmarkController::show
* @see app/Http/Controllers/BenchmarkController.php:76
* @route '/benchmarks/{benchmark}'
*/
show.head = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BenchmarkController::edit
* @see app/Http/Controllers/BenchmarkController.php:0
* @route '/benchmarks/{benchmark}/edit'
*/
export const edit = (args: { benchmark: string | number } | [benchmark: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/benchmarks/{benchmark}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BenchmarkController::edit
* @see app/Http/Controllers/BenchmarkController.php:0
* @route '/benchmarks/{benchmark}/edit'
*/
edit.url = (args: { benchmark: string | number } | [benchmark: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { benchmark: args }
    }

    if (Array.isArray(args)) {
        args = {
            benchmark: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        benchmark: args.benchmark,
    }

    return edit.definition.url
            .replace('{benchmark}', parsedArgs.benchmark.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkController::edit
* @see app/Http/Controllers/BenchmarkController.php:0
* @route '/benchmarks/{benchmark}/edit'
*/
edit.get = (args: { benchmark: string | number } | [benchmark: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BenchmarkController::edit
* @see app/Http/Controllers/BenchmarkController.php:0
* @route '/benchmarks/{benchmark}/edit'
*/
edit.head = (args: { benchmark: string | number } | [benchmark: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BenchmarkController::update
* @see app/Http/Controllers/BenchmarkController.php:107
* @route '/benchmarks/{benchmark}'
*/
export const update = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/benchmarks/{benchmark}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\BenchmarkController::update
* @see app/Http/Controllers/BenchmarkController.php:107
* @route '/benchmarks/{benchmark}'
*/
update.url = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { benchmark: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { benchmark: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            benchmark: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        benchmark: typeof args.benchmark === 'object'
        ? args.benchmark.id
        : args.benchmark,
    }

    return update.definition.url
            .replace('{benchmark}', parsedArgs.benchmark.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkController::update
* @see app/Http/Controllers/BenchmarkController.php:107
* @route '/benchmarks/{benchmark}'
*/
update.put = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BenchmarkController::update
* @see app/Http/Controllers/BenchmarkController.php:107
* @route '/benchmarks/{benchmark}'
*/
update.patch = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\BenchmarkController::destroy
* @see app/Http/Controllers/BenchmarkController.php:139
* @route '/benchmarks/{benchmark}'
*/
export const destroy = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/benchmarks/{benchmark}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\BenchmarkController::destroy
* @see app/Http/Controllers/BenchmarkController.php:139
* @route '/benchmarks/{benchmark}'
*/
destroy.url = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { benchmark: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { benchmark: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            benchmark: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        benchmark: typeof args.benchmark === 'object'
        ? args.benchmark.id
        : args.benchmark,
    }

    return destroy.definition.url
            .replace('{benchmark}', parsedArgs.benchmark.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkController::destroy
* @see app/Http/Controllers/BenchmarkController.php:139
* @route '/benchmarks/{benchmark}'
*/
destroy.delete = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\BenchmarkImportController::importMethod
* @see app/Http/Controllers/BenchmarkImportController.php:12
* @route '/benchmarks/import'
*/
export const importMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

importMethod.definition = {
    methods: ["post"],
    url: '/benchmarks/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BenchmarkImportController::importMethod
* @see app/Http/Controllers/BenchmarkImportController.php:12
* @route '/benchmarks/import'
*/
importMethod.url = (options?: RouteQueryOptions) => {
    return importMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkImportController::importMethod
* @see app/Http/Controllers/BenchmarkImportController.php:12
* @route '/benchmarks/import'
*/
importMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

const benchmarks = {
    create: Object.assign(create, create),
    export: Object.assign(exportMethod, exportMethod),
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    import: Object.assign(importMethod, importMethod),
}

export default benchmarks
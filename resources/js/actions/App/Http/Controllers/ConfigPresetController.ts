import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ConfigPresetController::index
* @see app/Http/Controllers/ConfigPresetController.php:14
* @route '/settings/presets'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/presets',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConfigPresetController::index
* @see app/Http/Controllers/ConfigPresetController.php:14
* @route '/settings/presets'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConfigPresetController::index
* @see app/Http/Controllers/ConfigPresetController.php:14
* @route '/settings/presets'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConfigPresetController::index
* @see app/Http/Controllers/ConfigPresetController.php:14
* @route '/settings/presets'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ConfigPresetController::store
* @see app/Http/Controllers/ConfigPresetController.php:24
* @route '/settings/presets'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/presets',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ConfigPresetController::store
* @see app/Http/Controllers/ConfigPresetController.php:24
* @route '/settings/presets'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConfigPresetController::store
* @see app/Http/Controllers/ConfigPresetController.php:24
* @route '/settings/presets'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ConfigPresetController::update
* @see app/Http/Controllers/ConfigPresetController.php:37
* @route '/settings/presets/{preset}'
*/
export const update = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/settings/presets/{preset}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\ConfigPresetController::update
* @see app/Http/Controllers/ConfigPresetController.php:37
* @route '/settings/presets/{preset}'
*/
update.url = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { preset: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { preset: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            preset: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        preset: typeof args.preset === 'object'
        ? args.preset.id
        : args.preset,
    }

    return update.definition.url
            .replace('{preset}', parsedArgs.preset.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConfigPresetController::update
* @see app/Http/Controllers/ConfigPresetController.php:37
* @route '/settings/presets/{preset}'
*/
update.patch = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\ConfigPresetController::destroy
* @see app/Http/Controllers/ConfigPresetController.php:52
* @route '/settings/presets/{preset}'
*/
export const destroy = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/presets/{preset}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ConfigPresetController::destroy
* @see app/Http/Controllers/ConfigPresetController.php:52
* @route '/settings/presets/{preset}'
*/
destroy.url = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { preset: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { preset: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            preset: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        preset: typeof args.preset === 'object'
        ? args.preset.id
        : args.preset,
    }

    return destroy.definition.url
            .replace('{preset}', parsedArgs.preset.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConfigPresetController::destroy
* @see app/Http/Controllers/ConfigPresetController.php:52
* @route '/settings/presets/{preset}'
*/
destroy.delete = (args: { preset: number | { id: number } } | [preset: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const ConfigPresetController = { index, store, update, destroy }

export default ConfigPresetController
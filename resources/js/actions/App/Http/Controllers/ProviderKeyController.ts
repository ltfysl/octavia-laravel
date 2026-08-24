import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ProviderKeyController::index
* @see app/Http/Controllers/ProviderKeyController.php:14
* @route '/settings/provider-keys'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/provider-keys',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProviderKeyController::index
* @see app/Http/Controllers/ProviderKeyController.php:14
* @route '/settings/provider-keys'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProviderKeyController::index
* @see app/Http/Controllers/ProviderKeyController.php:14
* @route '/settings/provider-keys'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProviderKeyController::index
* @see app/Http/Controllers/ProviderKeyController.php:14
* @route '/settings/provider-keys'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ProviderKeyController::store
* @see app/Http/Controllers/ProviderKeyController.php:33
* @route '/settings/provider-keys'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/provider-keys',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ProviderKeyController::store
* @see app/Http/Controllers/ProviderKeyController.php:33
* @route '/settings/provider-keys'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProviderKeyController::store
* @see app/Http/Controllers/ProviderKeyController.php:33
* @route '/settings/provider-keys'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ProviderKeyController::update
* @see app/Http/Controllers/ProviderKeyController.php:48
* @route '/settings/provider-keys/{providerKey}'
*/
export const update = (args: { providerKey: number | { id: number } } | [providerKey: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/settings/provider-keys/{providerKey}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\ProviderKeyController::update
* @see app/Http/Controllers/ProviderKeyController.php:48
* @route '/settings/provider-keys/{providerKey}'
*/
update.url = (args: { providerKey: number | { id: number } } | [providerKey: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { providerKey: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { providerKey: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            providerKey: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        providerKey: typeof args.providerKey === 'object'
        ? args.providerKey.id
        : args.providerKey,
    }

    return update.definition.url
            .replace('{providerKey}', parsedArgs.providerKey.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProviderKeyController::update
* @see app/Http/Controllers/ProviderKeyController.php:48
* @route '/settings/provider-keys/{providerKey}'
*/
update.patch = (args: { providerKey: number | { id: number } } | [providerKey: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\ProviderKeyController::destroy
* @see app/Http/Controllers/ProviderKeyController.php:61
* @route '/settings/provider-keys/{providerKey}'
*/
export const destroy = (args: { providerKey: number | { id: number } } | [providerKey: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/provider-keys/{providerKey}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ProviderKeyController::destroy
* @see app/Http/Controllers/ProviderKeyController.php:61
* @route '/settings/provider-keys/{providerKey}'
*/
destroy.url = (args: { providerKey: number | { id: number } } | [providerKey: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { providerKey: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { providerKey: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            providerKey: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        providerKey: typeof args.providerKey === 'object'
        ? args.providerKey.id
        : args.providerKey,
    }

    return destroy.definition.url
            .replace('{providerKey}', parsedArgs.providerKey.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProviderKeyController::destroy
* @see app/Http/Controllers/ProviderKeyController.php:61
* @route '/settings/provider-keys/{providerKey}'
*/
destroy.delete = (args: { providerKey: number | { id: number } } | [providerKey: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const ProviderKeyController = { index, store, update, destroy }

export default ProviderKeyController
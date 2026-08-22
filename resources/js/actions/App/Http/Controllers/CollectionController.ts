import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CollectionController::index
 * @see app/Http/Controllers/CollectionController.php:15
 * @route '/collections'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/collections',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CollectionController::index
 * @see app/Http/Controllers/CollectionController.php:15
 * @route '/collections'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CollectionController::index
 * @see app/Http/Controllers/CollectionController.php:15
 * @route '/collections'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\CollectionController::index
 * @see app/Http/Controllers/CollectionController.php:15
 * @route '/collections'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CollectionController::store
 * @see app/Http/Controllers/CollectionController.php:34
 * @route '/collections'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/collections',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CollectionController::store
 * @see app/Http/Controllers/CollectionController.php:34
 * @route '/collections'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CollectionController::store
 * @see app/Http/Controllers/CollectionController.php:34
 * @route '/collections'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CollectionController::update
 * @see app/Http/Controllers/CollectionController.php:51
 * @route '/collections/{collection}'
 */
export const update = (args: { collection: number | { id: number } } | [collection: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/collections/{collection}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\CollectionController::update
 * @see app/Http/Controllers/CollectionController.php:51
 * @route '/collections/{collection}'
 */
update.url = (args: { collection: number | { id: number } } | [collection: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { collection: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { collection: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    collection: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        collection: typeof args.collection === 'object'
                ? args.collection.id
                : args.collection,
                }

    return update.definition.url
            .replace('{collection}', parsedArgs.collection.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CollectionController::update
 * @see app/Http/Controllers/CollectionController.php:51
 * @route '/collections/{collection}'
 */
update.put = (args: { collection: number | { id: number } } | [collection: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\CollectionController::update
 * @see app/Http/Controllers/CollectionController.php:51
 * @route '/collections/{collection}'
 */
update.patch = (args: { collection: number | { id: number } } | [collection: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\CollectionController::destroy
 * @see app/Http/Controllers/CollectionController.php:65
 * @route '/collections/{collection}'
 */
export const destroy = (args: { collection: number | { id: number } } | [collection: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/collections/{collection}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CollectionController::destroy
 * @see app/Http/Controllers/CollectionController.php:65
 * @route '/collections/{collection}'
 */
destroy.url = (args: { collection: number | { id: number } } | [collection: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { collection: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { collection: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    collection: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        collection: typeof args.collection === 'object'
                ? args.collection.id
                : args.collection,
                }

    return destroy.definition.url
            .replace('{collection}', parsedArgs.collection.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CollectionController::destroy
 * @see app/Http/Controllers/CollectionController.php:65
 * @route '/collections/{collection}'
 */
destroy.delete = (args: { collection: number | { id: number } } | [collection: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const CollectionController = { index, store, update, destroy }

export default CollectionController
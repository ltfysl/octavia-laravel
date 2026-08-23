import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\MarketplaceController::index
* @see app/Http/Controllers/Admin/MarketplaceController.php:21
* @route '/admin/marketplace'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/marketplace',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::index
* @see app/Http/Controllers/Admin/MarketplaceController.php:21
* @route '/admin/marketplace'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::index
* @see app/Http/Controllers/Admin/MarketplaceController.php:21
* @route '/admin/marketplace'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::index
* @see app/Http/Controllers/Admin/MarketplaceController.php:21
* @route '/admin/marketplace'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::listed
* @see app/Http/Controllers/Admin/MarketplaceController.php:65
* @route '/admin/marketplace/{item}/listed'
*/
export const listed = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: listed.url(args, options),
    method: 'post',
})

listed.definition = {
    methods: ["post"],
    url: '/admin/marketplace/{item}/listed',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::listed
* @see app/Http/Controllers/Admin/MarketplaceController.php:65
* @route '/admin/marketplace/{item}/listed'
*/
listed.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { item: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { item: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            item: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        item: typeof args.item === 'object'
        ? args.item.id
        : args.item,
    }

    return listed.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::listed
* @see app/Http/Controllers/Admin/MarketplaceController.php:65
* @route '/admin/marketplace/{item}/listed'
*/
listed.post = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: listed.url(args, options),
    method: 'post',
})

const marketplace = {
    index: Object.assign(index, index),
    listed: Object.assign(listed, listed),
}

export default marketplace
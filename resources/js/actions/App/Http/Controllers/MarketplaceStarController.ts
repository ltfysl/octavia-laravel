import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MarketplaceStarController::__invoke
* @see app/Http/Controllers/MarketplaceStarController.php:12
* @route '/marketplace/{item}/star'
*/
const MarketplaceStarController = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: MarketplaceStarController.url(args, options),
    method: 'post',
})

MarketplaceStarController.definition = {
    methods: ["post"],
    url: '/marketplace/{item}/star',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MarketplaceStarController::__invoke
* @see app/Http/Controllers/MarketplaceStarController.php:12
* @route '/marketplace/{item}/star'
*/
MarketplaceStarController.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return MarketplaceStarController.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MarketplaceStarController::__invoke
* @see app/Http/Controllers/MarketplaceStarController.php:12
* @route '/marketplace/{item}/star'
*/
MarketplaceStarController.post = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: MarketplaceStarController.url(args, options),
    method: 'post',
})

export default MarketplaceStarController
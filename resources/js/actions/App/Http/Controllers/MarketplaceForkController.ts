import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MarketplaceForkController::__invoke
* @see app/Http/Controllers/MarketplaceForkController.php:13
* @route '/marketplace/{item}/fork'
*/
const MarketplaceForkController = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: MarketplaceForkController.url(args, options),
    method: 'post',
})

MarketplaceForkController.definition = {
    methods: ["post"],
    url: '/marketplace/{item}/fork',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MarketplaceForkController::__invoke
* @see app/Http/Controllers/MarketplaceForkController.php:13
* @route '/marketplace/{item}/fork'
*/
MarketplaceForkController.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return MarketplaceForkController.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MarketplaceForkController::__invoke
* @see app/Http/Controllers/MarketplaceForkController.php:13
* @route '/marketplace/{item}/fork'
*/
MarketplaceForkController.post = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: MarketplaceForkController.url(args, options),
    method: 'post',
})

export default MarketplaceForkController
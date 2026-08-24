import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MarketplaceController::index
* @see app/Http/Controllers/MarketplaceController.php:19
* @route '/marketplace'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/marketplace',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\MarketplaceController::index
* @see app/Http/Controllers/MarketplaceController.php:19
* @route '/marketplace'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MarketplaceController::index
* @see app/Http/Controllers/MarketplaceController.php:19
* @route '/marketplace'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\MarketplaceController::index
* @see app/Http/Controllers/MarketplaceController.php:19
* @route '/marketplace'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\MarketplaceController::install
* @see app/Http/Controllers/MarketplaceController.php:73
* @route '/marketplace/{item}/install'
*/
export const install = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: install.url(args, options),
    method: 'post',
})

install.definition = {
    methods: ["post"],
    url: '/marketplace/{item}/install',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MarketplaceController::install
* @see app/Http/Controllers/MarketplaceController.php:73
* @route '/marketplace/{item}/install'
*/
install.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return install.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MarketplaceController::install
* @see app/Http/Controllers/MarketplaceController.php:73
* @route '/marketplace/{item}/install'
*/
install.post = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: install.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MarketplaceController::publish
* @see app/Http/Controllers/MarketplaceController.php:131
* @route '/marketplace/publish'
*/
export const publish = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publish.url(options),
    method: 'post',
})

publish.definition = {
    methods: ["post"],
    url: '/marketplace/publish',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MarketplaceController::publish
* @see app/Http/Controllers/MarketplaceController.php:131
* @route '/marketplace/publish'
*/
publish.url = (options?: RouteQueryOptions) => {
    return publish.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MarketplaceController::publish
* @see app/Http/Controllers/MarketplaceController.php:131
* @route '/marketplace/publish'
*/
publish.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publish.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MarketplaceController::report
* @see app/Http/Controllers/MarketplaceController.php:98
* @route '/marketplace/{item}/report'
*/
export const report = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: report.url(args, options),
    method: 'post',
})

report.definition = {
    methods: ["post"],
    url: '/marketplace/{item}/report',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MarketplaceController::report
* @see app/Http/Controllers/MarketplaceController.php:98
* @route '/marketplace/{item}/report'
*/
report.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return report.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\MarketplaceController::report
* @see app/Http/Controllers/MarketplaceController.php:98
* @route '/marketplace/{item}/report'
*/
report.post = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: report.url(args, options),
    method: 'post',
})

const MarketplaceController = { index, install, publish, report }

export default MarketplaceController
import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Admin\MarketplaceController::setListed
* @see app/Http/Controllers/Admin/MarketplaceController.php:65
* @route '/admin/marketplace/{item}/listed'
*/
export const setListed = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: setListed.url(args, options),
    method: 'post',
})

setListed.definition = {
    methods: ["post"],
    url: '/admin/marketplace/{item}/listed',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::setListed
* @see app/Http/Controllers/Admin/MarketplaceController.php:65
* @route '/admin/marketplace/{item}/listed'
*/
setListed.url = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return setListed.definition.url
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::setListed
* @see app/Http/Controllers/Admin/MarketplaceController.php:65
* @route '/admin/marketplace/{item}/listed'
*/
setListed.post = (args: { item: number | { id: number } } | [item: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: setListed.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::reports
* @see app/Http/Controllers/Admin/MarketplaceController.php:42
* @route '/admin/reports'
*/
export const reports = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reports.url(options),
    method: 'get',
})

reports.definition = {
    methods: ["get","head"],
    url: '/admin/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::reports
* @see app/Http/Controllers/Admin/MarketplaceController.php:42
* @route '/admin/reports'
*/
reports.url = (options?: RouteQueryOptions) => {
    return reports.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::reports
* @see app/Http/Controllers/Admin/MarketplaceController.php:42
* @route '/admin/reports'
*/
reports.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reports.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::reports
* @see app/Http/Controllers/Admin/MarketplaceController.php:42
* @route '/admin/reports'
*/
reports.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reports.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::resolve
* @see app/Http/Controllers/Admin/MarketplaceController.php:85
* @route '/admin/reports/{report}/resolve/{action}'
*/
export const resolve = (args: { report: number | { id: number }, action: string | number } | [report: number | { id: number }, action: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolve.url(args, options),
    method: 'post',
})

resolve.definition = {
    methods: ["post"],
    url: '/admin/reports/{report}/resolve/{action}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::resolve
* @see app/Http/Controllers/Admin/MarketplaceController.php:85
* @route '/admin/reports/{report}/resolve/{action}'
*/
resolve.url = (args: { report: number | { id: number }, action: string | number } | [report: number | { id: number }, action: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            report: args[0],
            action: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        report: typeof args.report === 'object'
        ? args.report.id
        : args.report,
        action: args.action,
    }

    return resolve.definition.url
            .replace('{report}', parsedArgs.report.toString())
            .replace('{action}', parsedArgs.action.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::resolve
* @see app/Http/Controllers/Admin/MarketplaceController.php:85
* @route '/admin/reports/{report}/resolve/{action}'
*/
resolve.post = (args: { report: number | { id: number }, action: string | number } | [report: number | { id: number }, action: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolve.url(args, options),
    method: 'post',
})

const MarketplaceController = { index, setListed, reports, resolve }

export default MarketplaceController
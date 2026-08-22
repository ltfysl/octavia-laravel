import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\MarketplaceController::index
 * @see app/Http/Controllers/Admin/MarketplaceController.php:42
 * @route '/admin/reports'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::index
 * @see app/Http/Controllers/Admin/MarketplaceController.php:42
 * @route '/admin/reports'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\MarketplaceController::index
 * @see app/Http/Controllers/Admin/MarketplaceController.php:42
 * @route '/admin/reports'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Admin\MarketplaceController::index
 * @see app/Http/Controllers/Admin/MarketplaceController.php:42
 * @route '/admin/reports'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
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
const reports = {
    index: Object.assign(index, index),
resolve: Object.assign(resolve, resolve),
}

export default reports
import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ReportController::__invoke
* @see app/Http/Controllers/ReportController.php:15
* @route '/reports'
*/
const ReportController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ReportController.url(options),
    method: 'get',
})

ReportController.definition = {
    methods: ["get","head"],
    url: '/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::__invoke
* @see app/Http/Controllers/ReportController.php:15
* @route '/reports'
*/
ReportController.url = (options?: RouteQueryOptions) => {
    return ReportController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::__invoke
* @see app/Http/Controllers/ReportController.php:15
* @route '/reports'
*/
ReportController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ReportController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ReportController::__invoke
* @see app/Http/Controllers/ReportController.php:15
* @route '/reports'
*/
ReportController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ReportController.url(options),
    method: 'head',
})

export default ReportController
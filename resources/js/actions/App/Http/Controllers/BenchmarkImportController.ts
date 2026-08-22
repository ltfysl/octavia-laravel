import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BenchmarkImportController::__invoke
 * @see app/Http/Controllers/BenchmarkImportController.php:12
 * @route '/benchmarks/import'
 */
export const __invoke = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: __invoke.url(options),
    method: 'post',
})

__invoke.definition = {
    methods: ["post"],
    url: '/benchmarks/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BenchmarkImportController::__invoke
 * @see app/Http/Controllers/BenchmarkImportController.php:12
 * @route '/benchmarks/import'
 */
__invoke.url = (options?: RouteQueryOptions) => {
    return __invoke.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkImportController::__invoke
 * @see app/Http/Controllers/BenchmarkImportController.php:12
 * @route '/benchmarks/import'
 */
__invoke.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: __invoke.url(options),
    method: 'post',
})
const BenchmarkImportController = { __invoke }

export default BenchmarkImportController
import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BenchmarkExportController::__invoke
 * @see app/Http/Controllers/BenchmarkExportController.php:14
 * @route '/benchmarks/{benchmark}/export'
 */
const BenchmarkExportController = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: BenchmarkExportController.url(args, options),
    method: 'get',
})

BenchmarkExportController.definition = {
    methods: ["get","head"],
    url: '/benchmarks/{benchmark}/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BenchmarkExportController::__invoke
 * @see app/Http/Controllers/BenchmarkExportController.php:14
 * @route '/benchmarks/{benchmark}/export'
 */
BenchmarkExportController.url = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { benchmark: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { benchmark: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    benchmark: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        benchmark: typeof args.benchmark === 'object'
                ? args.benchmark.id
                : args.benchmark,
                }

    return BenchmarkExportController.definition.url
            .replace('{benchmark}', parsedArgs.benchmark.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkExportController::__invoke
 * @see app/Http/Controllers/BenchmarkExportController.php:14
 * @route '/benchmarks/{benchmark}/export'
 */
BenchmarkExportController.get = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: BenchmarkExportController.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\BenchmarkExportController::__invoke
 * @see app/Http/Controllers/BenchmarkExportController.php:14
 * @route '/benchmarks/{benchmark}/export'
 */
BenchmarkExportController.head = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: BenchmarkExportController.url(args, options),
    method: 'head',
})
export default BenchmarkExportController
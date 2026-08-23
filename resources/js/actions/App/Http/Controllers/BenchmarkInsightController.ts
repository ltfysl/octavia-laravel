import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\BenchmarkInsightController::__invoke
* @see app/Http/Controllers/BenchmarkInsightController.php:11
* @route '/benchmarks/{benchmark}/insight'
*/
const BenchmarkInsightController = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: BenchmarkInsightController.url(args, options),
    method: 'post',
})

BenchmarkInsightController.definition = {
    methods: ["post"],
    url: '/benchmarks/{benchmark}/insight',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BenchmarkInsightController::__invoke
* @see app/Http/Controllers/BenchmarkInsightController.php:11
* @route '/benchmarks/{benchmark}/insight'
*/
BenchmarkInsightController.url = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return BenchmarkInsightController.definition.url
            .replace('{benchmark}', parsedArgs.benchmark.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BenchmarkInsightController::__invoke
* @see app/Http/Controllers/BenchmarkInsightController.php:11
* @route '/benchmarks/{benchmark}/insight'
*/
BenchmarkInsightController.post = (args: { benchmark: number | { id: number } } | [benchmark: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: BenchmarkInsightController.url(args, options),
    method: 'post',
})

export default BenchmarkInsightController
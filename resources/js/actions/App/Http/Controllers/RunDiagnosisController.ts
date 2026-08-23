import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\RunDiagnosisController::__invoke
* @see app/Http/Controllers/RunDiagnosisController.php:14
* @route '/runs/{run}/diagnosis'
*/
const RunDiagnosisController = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RunDiagnosisController.url(args, options),
    method: 'post',
})

RunDiagnosisController.definition = {
    methods: ["post"],
    url: '/runs/{run}/diagnosis',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RunDiagnosisController::__invoke
* @see app/Http/Controllers/RunDiagnosisController.php:14
* @route '/runs/{run}/diagnosis'
*/
RunDiagnosisController.url = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { run: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { run: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            run: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        run: typeof args.run === 'object'
        ? args.run.id
        : args.run,
    }

    return RunDiagnosisController.definition.url
            .replace('{run}', parsedArgs.run.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RunDiagnosisController::__invoke
* @see app/Http/Controllers/RunDiagnosisController.php:14
* @route '/runs/{run}/diagnosis'
*/
RunDiagnosisController.post = (args: { run: number | { id: number } } | [run: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: RunDiagnosisController.url(args, options),
    method: 'post',
})

export default RunDiagnosisController
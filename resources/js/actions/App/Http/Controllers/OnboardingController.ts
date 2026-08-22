import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\OnboardingController::welcome
* @see app/Http/Controllers/OnboardingController.php:13
* @route '/welcome'
*/
export const welcome = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: welcome.url(options),
    method: 'get',
})

welcome.definition = {
    methods: ["get","head"],
    url: '/welcome',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OnboardingController::welcome
* @see app/Http/Controllers/OnboardingController.php:13
* @route '/welcome'
*/
welcome.url = (options?: RouteQueryOptions) => {
    return welcome.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OnboardingController::welcome
* @see app/Http/Controllers/OnboardingController.php:13
* @route '/welcome'
*/
welcome.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: welcome.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OnboardingController::welcome
* @see app/Http/Controllers/OnboardingController.php:13
* @route '/welcome'
*/
welcome.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: welcome.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OnboardingController::complete
* @see app/Http/Controllers/OnboardingController.php:18
* @route '/welcome/complete'
*/
export const complete = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/welcome/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OnboardingController::complete
* @see app/Http/Controllers/OnboardingController.php:18
* @route '/welcome/complete'
*/
complete.url = (options?: RouteQueryOptions) => {
    return complete.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OnboardingController::complete
* @see app/Http/Controllers/OnboardingController.php:18
* @route '/welcome/complete'
*/
complete.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(options),
    method: 'post',
})

const OnboardingController = { welcome, complete }

export default OnboardingController
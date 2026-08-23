import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\EmailVerificationNotificationController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationNotificationController.php:11
* @route '/email/verification-notification'
*/
export const __invoke = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: __invoke.url(options),
    method: 'post',
})

__invoke.definition = {
    methods: ["post"],
    url: '/email/verification-notification',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\EmailVerificationNotificationController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationNotificationController.php:11
* @route '/email/verification-notification'
*/
__invoke.url = (options?: RouteQueryOptions) => {
    return __invoke.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\EmailVerificationNotificationController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationNotificationController.php:11
* @route '/email/verification-notification'
*/
__invoke.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: __invoke.url(options),
    method: 'post',
})

const EmailVerificationNotificationController = { __invoke }

export default EmailVerificationNotificationController
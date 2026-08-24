import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\WebhookController::index
* @see app/Http/Controllers/WebhookController.php:15
* @route '/settings/webhooks'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/settings/webhooks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WebhookController::index
* @see app/Http/Controllers/WebhookController.php:15
* @route '/settings/webhooks'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WebhookController::index
* @see app/Http/Controllers/WebhookController.php:15
* @route '/settings/webhooks'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WebhookController::index
* @see app/Http/Controllers/WebhookController.php:15
* @route '/settings/webhooks'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WebhookController::store
* @see app/Http/Controllers/WebhookController.php:36
* @route '/settings/webhooks'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/settings/webhooks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WebhookController::store
* @see app/Http/Controllers/WebhookController.php:36
* @route '/settings/webhooks'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WebhookController::store
* @see app/Http/Controllers/WebhookController.php:36
* @route '/settings/webhooks'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WebhookController::update
* @see app/Http/Controllers/WebhookController.php:55
* @route '/settings/webhooks/{webhook}'
*/
export const update = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/settings/webhooks/{webhook}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\WebhookController::update
* @see app/Http/Controllers/WebhookController.php:55
* @route '/settings/webhooks/{webhook}'
*/
update.url = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhook: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { webhook: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            webhook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhook: typeof args.webhook === 'object'
        ? args.webhook.id
        : args.webhook,
    }

    return update.definition.url
            .replace('{webhook}', parsedArgs.webhook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WebhookController::update
* @see app/Http/Controllers/WebhookController.php:55
* @route '/settings/webhooks/{webhook}'
*/
update.patch = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\WebhookController::destroy
* @see app/Http/Controllers/WebhookController.php:72
* @route '/settings/webhooks/{webhook}'
*/
export const destroy = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/webhooks/{webhook}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\WebhookController::destroy
* @see app/Http/Controllers/WebhookController.php:72
* @route '/settings/webhooks/{webhook}'
*/
destroy.url = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhook: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { webhook: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            webhook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhook: typeof args.webhook === 'object'
        ? args.webhook.id
        : args.webhook,
    }

    return destroy.definition.url
            .replace('{webhook}', parsedArgs.webhook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WebhookController::destroy
* @see app/Http/Controllers/WebhookController.php:72
* @route '/settings/webhooks/{webhook}'
*/
destroy.delete = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\WebhookController::deliveries
* @see app/Http/Controllers/WebhookController.php:81
* @route '/settings/webhooks/{webhook}/deliveries'
*/
export const deliveries = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: deliveries.url(args, options),
    method: 'get',
})

deliveries.definition = {
    methods: ["get","head"],
    url: '/settings/webhooks/{webhook}/deliveries',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WebhookController::deliveries
* @see app/Http/Controllers/WebhookController.php:81
* @route '/settings/webhooks/{webhook}/deliveries'
*/
deliveries.url = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhook: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { webhook: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            webhook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhook: typeof args.webhook === 'object'
        ? args.webhook.id
        : args.webhook,
    }

    return deliveries.definition.url
            .replace('{webhook}', parsedArgs.webhook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WebhookController::deliveries
* @see app/Http/Controllers/WebhookController.php:81
* @route '/settings/webhooks/{webhook}/deliveries'
*/
deliveries.get = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: deliveries.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WebhookController::deliveries
* @see app/Http/Controllers/WebhookController.php:81
* @route '/settings/webhooks/{webhook}/deliveries'
*/
deliveries.head = (args: { webhook: number | { id: number } } | [webhook: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: deliveries.url(args, options),
    method: 'head',
})

const WebhookController = { index, store, update, destroy, deliveries }

export default WebhookController
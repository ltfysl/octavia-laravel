import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\NotificationController::index
* @see app/Http/Controllers/NotificationController.php:12
* @route '/notifications'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\NotificationController::index
* @see app/Http/Controllers/NotificationController.php:12
* @route '/notifications'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationController::index
* @see app/Http/Controllers/NotificationController.php:12
* @route '/notifications'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\NotificationController::index
* @see app/Http/Controllers/NotificationController.php:12
* @route '/notifications'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\NotificationController::unread
* @see app/Http/Controllers/NotificationController.php:45
* @route '/notifications/{id}/unread'
*/
export const unread = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: unread.url(args, options),
    method: 'post',
})

unread.definition = {
    methods: ["post"],
    url: '/notifications/{id}/unread',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NotificationController::unread
* @see app/Http/Controllers/NotificationController.php:45
* @route '/notifications/{id}/unread'
*/
unread.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return unread.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationController::unread
* @see app/Http/Controllers/NotificationController.php:45
* @route '/notifications/{id}/unread'
*/
unread.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: unread.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NotificationController::markRead
* @see app/Http/Controllers/NotificationController.php:38
* @route '/notifications/mark-read'
*/
export const markRead = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markRead.url(options),
    method: 'post',
})

markRead.definition = {
    methods: ["post"],
    url: '/notifications/mark-read',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NotificationController::markRead
* @see app/Http/Controllers/NotificationController.php:38
* @route '/notifications/mark-read'
*/
markRead.url = (options?: RouteQueryOptions) => {
    return markRead.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationController::markRead
* @see app/Http/Controllers/NotificationController.php:38
* @route '/notifications/mark-read'
*/
markRead.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markRead.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NotificationController::destroy
* @see app/Http/Controllers/NotificationController.php:52
* @route '/notifications/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/notifications/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\NotificationController::destroy
* @see app/Http/Controllers/NotificationController.php:52
* @route '/notifications/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NotificationController::destroy
* @see app/Http/Controllers/NotificationController.php:52
* @route '/notifications/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const notifications = {
    index: Object.assign(index, index),
    unread: Object.assign(unread, unread),
    markRead: Object.assign(markRead, markRead),
    destroy: Object.assign(destroy, destroy),
}

export default notifications
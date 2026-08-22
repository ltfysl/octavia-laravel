import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import auth from './auth'
import prompts from './prompts'
import runs from './runs'
/**
* @see routes/api.php:25
* @route '/api/v1/me'
*/
export const me = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

me.definition = {
    methods: ["get","head"],
    url: '/api/v1/me',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/api.php:25
* @route '/api/v1/me'
*/
me.url = (options?: RouteQueryOptions) => {
    return me.definition.url + queryParams(options)
}

/**
* @see routes/api.php:25
* @route '/api/v1/me'
*/
me.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: me.url(options),
    method: 'get',
})

/**
* @see routes/api.php:25
* @route '/api/v1/me'
*/
me.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: me.url(options),
    method: 'head',
})

const api = {
    auth: Object.assign(auth, auth),
    me: Object.assign(me, me),
    prompts: Object.assign(prompts, prompts),
    runs: Object.assign(runs, runs),
}

export default api
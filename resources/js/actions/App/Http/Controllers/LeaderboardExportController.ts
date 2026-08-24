import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LeaderboardExportController::__invoke
* @see app/Http/Controllers/LeaderboardExportController.php:14
* @route '/export/leaderboard'
*/
const LeaderboardExportController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LeaderboardExportController.url(options),
    method: 'get',
})

LeaderboardExportController.definition = {
    methods: ["get","head"],
    url: '/export/leaderboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LeaderboardExportController::__invoke
* @see app/Http/Controllers/LeaderboardExportController.php:14
* @route '/export/leaderboard'
*/
LeaderboardExportController.url = (options?: RouteQueryOptions) => {
    return LeaderboardExportController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LeaderboardExportController::__invoke
* @see app/Http/Controllers/LeaderboardExportController.php:14
* @route '/export/leaderboard'
*/
LeaderboardExportController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LeaderboardExportController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LeaderboardExportController::__invoke
* @see app/Http/Controllers/LeaderboardExportController.php:14
* @route '/export/leaderboard'
*/
LeaderboardExportController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: LeaderboardExportController.url(options),
    method: 'head',
})

export default LeaderboardExportController
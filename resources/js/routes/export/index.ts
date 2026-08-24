import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\LeaderboardExportController::__invoke
* @see app/Http/Controllers/LeaderboardExportController.php:12
* @route '/export/leaderboard'
*/
export const leaderboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: leaderboard.url(options),
    method: 'get',
})

leaderboard.definition = {
    methods: ["get","head"],
    url: '/export/leaderboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LeaderboardExportController::__invoke
* @see app/Http/Controllers/LeaderboardExportController.php:12
* @route '/export/leaderboard'
*/
leaderboard.url = (options?: RouteQueryOptions) => {
    return leaderboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LeaderboardExportController::__invoke
* @see app/Http/Controllers/LeaderboardExportController.php:12
* @route '/export/leaderboard'
*/
leaderboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: leaderboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LeaderboardExportController::__invoke
* @see app/Http/Controllers/LeaderboardExportController.php:12
* @route '/export/leaderboard'
*/
leaderboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: leaderboard.url(options),
    method: 'head',
})

const exportMethod = {
    leaderboard: Object.assign(leaderboard, leaderboard),
}

export default exportMethod
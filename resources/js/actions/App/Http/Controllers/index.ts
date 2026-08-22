import Api from './Api'
import SitemapController from './SitemapController'
import LandingController from './LandingController'
import Auth from './Auth'
import DashboardController from './DashboardController'
import OnboardingController from './OnboardingController'
import PromptController from './PromptController'
import CollectionController from './CollectionController'
import BenchmarkController from './BenchmarkController'
import BenchmarkExportController from './BenchmarkExportController'
import BenchmarkImportController from './BenchmarkImportController'
import RunController from './RunController'
import MarketplaceController from './MarketplaceController'
import NotificationController from './NotificationController'
import SettingsController from './SettingsController'
import SearchController from './SearchController'
import TeamController from './TeamController'
import Admin from './Admin'
const Controllers = {
    Api: Object.assign(Api, Api),
SitemapController: Object.assign(SitemapController, SitemapController),
LandingController: Object.assign(LandingController, LandingController),
Auth: Object.assign(Auth, Auth),
DashboardController: Object.assign(DashboardController, DashboardController),
OnboardingController: Object.assign(OnboardingController, OnboardingController),
PromptController: Object.assign(PromptController, PromptController),
CollectionController: Object.assign(CollectionController, CollectionController),
BenchmarkController: Object.assign(BenchmarkController, BenchmarkController),
BenchmarkExportController: Object.assign(BenchmarkExportController, BenchmarkExportController),
BenchmarkImportController: Object.assign(BenchmarkImportController, BenchmarkImportController),
RunController: Object.assign(RunController, RunController),
MarketplaceController: Object.assign(MarketplaceController, MarketplaceController),
NotificationController: Object.assign(NotificationController, NotificationController),
SettingsController: Object.assign(SettingsController, SettingsController),
SearchController: Object.assign(SearchController, SearchController),
TeamController: Object.assign(TeamController, TeamController),
Admin: Object.assign(Admin, Admin),
}

export default Controllers
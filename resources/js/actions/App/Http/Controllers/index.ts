import Api from './Api'
import SitemapController from './SitemapController'
import LandingController from './LandingController'
import Auth from './Auth'
import DashboardController from './DashboardController'
import OnboardingController from './OnboardingController'
import PromptController from './PromptController'
import PromptTemplateController from './PromptTemplateController'
import PromptInsightController from './PromptInsightController'
import PromptAbTestController from './PromptAbTestController'
import PromptRegressionTestController from './PromptRegressionTestController'
import PromptExportController from './PromptExportController'
import PromptImportController from './PromptImportController'
import CollectionController from './CollectionController'
import BenchmarkController from './BenchmarkController'
import BenchmarkExportController from './BenchmarkExportController'
import BenchmarkInsightController from './BenchmarkInsightController'
import BenchmarkImportController from './BenchmarkImportController'
import RunController from './RunController'
import AssistantController from './AssistantController'
import RunDiagnosisController from './RunDiagnosisController'
import ReportController from './ReportController'
import AuditLogController from './AuditLogController'
import TournamentController from './TournamentController'
import MarketplaceController from './MarketplaceController'
import MarketplaceStarController from './MarketplaceStarController'
import MarketplaceForkController from './MarketplaceForkController'
import NotificationController from './NotificationController'
import WebhookController from './WebhookController'
import SettingsController from './SettingsController'
import ConfigPresetController from './ConfigPresetController'
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
    PromptTemplateController: Object.assign(PromptTemplateController, PromptTemplateController),
    PromptInsightController: Object.assign(PromptInsightController, PromptInsightController),
    PromptAbTestController: Object.assign(PromptAbTestController, PromptAbTestController),
    PromptRegressionTestController: Object.assign(PromptRegressionTestController, PromptRegressionTestController),
    PromptExportController: Object.assign(PromptExportController, PromptExportController),
    PromptImportController: Object.assign(PromptImportController, PromptImportController),
    CollectionController: Object.assign(CollectionController, CollectionController),
    BenchmarkController: Object.assign(BenchmarkController, BenchmarkController),
    BenchmarkExportController: Object.assign(BenchmarkExportController, BenchmarkExportController),
    BenchmarkInsightController: Object.assign(BenchmarkInsightController, BenchmarkInsightController),
    BenchmarkImportController: Object.assign(BenchmarkImportController, BenchmarkImportController),
    RunController: Object.assign(RunController, RunController),
    AssistantController: Object.assign(AssistantController, AssistantController),
    RunDiagnosisController: Object.assign(RunDiagnosisController, RunDiagnosisController),
    ReportController: Object.assign(ReportController, ReportController),
    AuditLogController: Object.assign(AuditLogController, AuditLogController),
    TournamentController: Object.assign(TournamentController, TournamentController),
    MarketplaceController: Object.assign(MarketplaceController, MarketplaceController),
    MarketplaceStarController: Object.assign(MarketplaceStarController, MarketplaceStarController),
    MarketplaceForkController: Object.assign(MarketplaceForkController, MarketplaceForkController),
    NotificationController: Object.assign(NotificationController, NotificationController),
    WebhookController: Object.assign(WebhookController, WebhookController),
    SettingsController: Object.assign(SettingsController, SettingsController),
    ConfigPresetController: Object.assign(ConfigPresetController, ConfigPresetController),
    SearchController: Object.assign(SearchController, SearchController),
    TeamController: Object.assign(TeamController, TeamController),
    Admin: Object.assign(Admin, Admin),
}

export default Controllers
import DashboardController from './DashboardController'
import UserController from './UserController'
import MarketplaceController from './MarketplaceController'
const Admin = {
    DashboardController: Object.assign(DashboardController, DashboardController),
UserController: Object.assign(UserController, UserController),
MarketplaceController: Object.assign(MarketplaceController, MarketplaceController),
}

export default Admin
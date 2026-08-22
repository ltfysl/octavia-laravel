import AuthTokenController from './AuthTokenController'
import PromptController from './PromptController'
import RunController from './RunController'
const Api = {
    AuthTokenController: Object.assign(AuthTokenController, AuthTokenController),
PromptController: Object.assign(PromptController, PromptController),
RunController: Object.assign(RunController, RunController),
}

export default Api
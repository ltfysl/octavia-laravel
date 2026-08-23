import RegisteredUserController from './RegisteredUserController'
import PasswordResetLinkController from './PasswordResetLinkController'
import NewPasswordController from './NewPasswordController'
import EmailVerificationNotificationController from './EmailVerificationNotificationController'
import VerifyEmailController from './VerifyEmailController'

const Auth = {
    RegisteredUserController: Object.assign(RegisteredUserController, RegisteredUserController),
    PasswordResetLinkController: Object.assign(PasswordResetLinkController, PasswordResetLinkController),
    NewPasswordController: Object.assign(NewPasswordController, NewPasswordController),
    EmailVerificationNotificationController: Object.assign(EmailVerificationNotificationController, EmailVerificationNotificationController),
    VerifyEmailController: Object.assign(VerifyEmailController, VerifyEmailController),
}

export default Auth
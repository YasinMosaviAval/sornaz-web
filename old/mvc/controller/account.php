<?php

require_once 'account/pages.php';
require_once 'account/queries.php';
require_once 'account/api.php';



// require_once 'account/register.php';
// require_once 'account/login.php';
// require_once 'account/verify.php';
// require_once 'account/password.php';
// require_once 'account/oauth.php';
// require_once 'account/profile.php';

/**
 * mvc/controller/account.php
 *
 * Web Routes:
 *   GET  /account/register             → registerForm()
 *   POST /account/register             → register()
 *   GET  /account/login                → loginForm()
 *   POST /account/login                → login()
 *   GET  /account/logout               → logout()
 *   GET  /account/verify-email         → verifyEmail()     ?token=xxx
 *   GET  /account/verify-phone         → verifyPhoneForm()
 *   POST /account/verify-phone         → verifyPhone()
 *   POST /account/resend-otp           → resendOtp()
 *   GET  /account/forgot-password      → forgotForm()
 *   POST /account/forgot-password      → forgotPassword()
 *   GET  /account/reset-password       → resetForm()       ?token=xxx
 *   POST /account/reset-password       → resetPassword()
 *   GET  /account/profile              → profileView()
 *   POST /account/profile              → profileUpdate()
 *   GET  /account/google               → googleRedirect()
 *   GET  /account/google/callback      → googleCallback()
 *
 * API Routes:
 *   POST /api/account/register         → registerPost()
 *   POST /api/account/login            → loginPost()
 *   POST /api/account/logout           → logoutPost()
 *   POST /api/account/verify-otp       → verifyOtpPost()
 *   POST /api/account/resend-otp       → resendOtpPost()
 *   POST /api/account/forgot-password  → forgotPasswordPost()
 *   POST /api/account/reset-password   → resetPasswordPost()
 *   GET  /api/account/me               → meGet()
 */
class AccountController extends BaseController {

    public function __construct() {
        // grantAcademyManaging();
        // grantAcademyManager();
    }

    

    use AccountPagesTrait;
    use AccountQueriesTrait;
    use AccountApiTrait;
    
    
    // use AccountLoginTrait;
    // use AccountRegisterTrait;
    // use AccountVerifyTrait;
    // use AccountPasswordTrait;
    // use AccountOAuthTrait;
    // use AccountProfileTrait;

}

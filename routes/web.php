<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MarketingTemplateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ServiceTicketController;
use App\Http\Controllers\GstInvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SiteSurveyController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('login', [LoginController::class, 'showForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    // Password Reset Routes
    Route::get('password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware(['set.active.company', 'check.subscription', 'check.plan'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Billing & Subscription Management
        Route::get('billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
        Route::post('billing/upgrade', [\App\Http\Controllers\BillingController::class, 'upgrade'])->name('billing.upgrade');
        Route::get('billing/expired', [\App\Http\Controllers\BillingController::class, 'expired'])->name('billing.expired');

        // Customer Module
        Route::resource('customers', CustomerController::class);
        
        // Lead Module
        Route::patch('leads/{lead}/stage', [LeadController::class, 'updateStage'])->name('leads.update-stage');
        Route::resource('leads', LeadController::class);
        
        // Product Module
        Route::resource('products', ProductController::class);
        
        // Quote Module
        Route::post('quotes/{quote}/send', [QuoteController::class, 'send'])->name('quotes.send');
        Route::resource('quotes', QuoteController::class);
        
        // Installation Module
        Route::post('installations/{installation}/milestones/{milestone}', [InstallationController::class, 'updateMilestone'])->name('installations.milestone.update');
        Route::resource('installations', InstallationController::class);
        
        // Site Survey Module
        Route::resource('surveys', SiteSurveyController::class);
        
        // Service Ticket Module
        Route::resource('tickets', ServiceTicketController::class);
        
        // Invoice Module
        Route::patch('invoices/{invoice}/status', [GstInvoiceController::class, 'updateStatus'])->name('invoices.status');
        Route::resource('invoices', GstInvoiceController::class)->only(['index', 'show']);
        
        // Payment Module
        Route::resource('payments', PaymentController::class);
        
        // Activities
        Route::post('activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::delete('activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

        // Marketing Module
        Route::group(['prefix' => 'marketing'], function () {
            Route::resource('campaigns', CampaignController::class);
            Route::post('campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
            Route::resource('templates', MarketingTemplateController::class);
        });

        // Settings & Team
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
        
        // Social Media Publishing
        Route::get('social', [\App\Http\Controllers\SocialPostController::class, 'index'])->name('social.index');
        Route::get('social/create', [\App\Http\Controllers\SocialPostController::class, 'create'])->name('social.create');
        Route::post('social', [\App\Http\Controllers\SocialPostController::class, 'store'])->name('social.store');
        Route::get('social/{post}/edit', [\App\Http\Controllers\SocialPostController::class, 'edit'])->name('social.edit');
        Route::put('social/{post}', [\App\Http\Controllers\SocialPostController::class, 'update'])->name('social.update');
        Route::delete('social/{post}', [\App\Http\Controllers\SocialPostController::class, 'destroy'])->name('social.destroy');
        Route::get('settings/social', function() {
            $account = \App\Models\SocialAccount::where('company_id', app('current_company_id'))->first();
            return view('social.settings', compact('account'));
        })->name('social.settings');
        Route::get('auth/facebook', [\App\Http\Controllers\SocialAuthController::class, 'redirect'])->name('social.auth.facebook');
        Route::get('auth/facebook/callback', [\App\Http\Controllers\SocialAuthController::class, 'callback']);
        
        Route::get('team', [TeamController::class, 'index'])->name('team.index');
        Route::post('team/invite', [TeamController::class, 'invite'])->name('team.invite');
        Route::get('team/permissions', [TeamController::class, 'permissions'])->name('team.permissions');
        Route::post('team/permissions/role', [TeamController::class, 'updateRolePermissions'])->name('team.permissions.role');
        Route::post('team/permissions/user', [TeamController::class, 'updateUserPermissions'])->name('team.permissions.user');
        Route::put('team/{user}', [TeamController::class, 'update'])->name('team.update');
        Route::delete('team/{user}', [TeamController::class, 'destroy'])->name('team.destroy');

        // Super Admin
        Route::group(['prefix' => 'admin'], function () {
            Route::get('companies', [AdminController::class, 'companies'])->name('admin.companies');
            Route::post('companies/store', [AdminController::class, 'storeCompany'])->name('admin.companies.store');
            Route::post('companies/{company}/update-plan', [AdminController::class, 'updatePlan'])->name('admin.companies.update-plan');
            Route::post('companies/{company}/impersonate', [AdminController::class, 'impersonate'])->name('admin.impersonate');
            Route::post('stop-impersonating', [AdminController::class, 'stopImpersonating'])->name('admin.stop-impersonating');
            
            // Dynamic Subscription Plans CRUD
            Route::resource('plans', \App\Http\Controllers\AdminPlanController::class, ['as' => 'admin']);

            // Plan Upgrade Requests
            Route::get('upgrade-requests', [AdminController::class, 'upgradeRequests'])->name('admin.upgrade-requests');
            Route::post('upgrade-requests/{upgradeRequest}/approve', [AdminController::class, 'approveRequest'])->name('admin.upgrade-requests.approve');
            Route::post('upgrade-requests/{upgradeRequest}/reject', [AdminController::class, 'rejectRequest'])->name('admin.upgrade-requests.reject');
        });
    });
});

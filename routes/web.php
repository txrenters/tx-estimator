<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\CurrentCompanyController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\EstimateProposalController;
use App\Http\Controllers\EstimateShareController;
use App\Http\Controllers\EstimateStatusController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\PricingDefaultsController;
use App\Http\Controllers\PublicProposalController;
use App\Http\Controllers\ServiceCatalogController;
use App\Http\Controllers\ServiceTemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    return match (true) {
        $user === null => redirect()->route('login'),
        $user->isGlobal() => redirect()->route('overview'),
        default => redirect()->route('estimates.index'),
    };
})->name('home');

// Customer share links: /p/{company short name}/{12-character code}. No login, rate limited;
// the code alone identifies the estimate, the company part is for the customer's benefit.
Route::middleware('throttle:60,1')->group(function () {
    Route::get('p/{company}/{token}', PublicProposalController::class)
        ->where(['company' => '[a-z0-9-]{1,60}', 'token' => '[A-Za-z0-9]{12,40}'])
        ->name('proposal.public');
    Route::get('p/{token}', [PublicProposalController::class, 'legacy'])
        ->where('token', '[A-Za-z0-9]{12,40}')
        ->name('proposal.legacy');
});
Route::post('p/{company}/{token}/authorize', [PublicProposalController::class, 'sign'])
    ->where(['company' => '[a-z0-9-]{1,60}', 'token' => '[A-Za-z0-9]{12,40}'])
    ->middleware('throttle:10,1')
    ->name('proposal.authorize');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('overview', OverviewController::class)->name('overview');
    Route::post('current-company', [CurrentCompanyController::class, 'update'])->name('current-company.update');

    Route::resource('estimates', EstimateController::class)->except('show');
    Route::post('estimates/{estimate}/duplicate', [EstimateController::class, 'duplicate'])->name('estimates.duplicate');
    Route::post('estimates/{estimate}/status', EstimateStatusController::class)->name('estimates.status');
    Route::get('estimates/{estimate}/proposal', EstimateProposalController::class)->name('estimates.proposal');
    Route::post('estimates/{estimate}/share', [EstimateShareController::class, 'store'])->name('estimates.share');
    Route::delete('estimates/{estimate}/share', [EstimateShareController::class, 'destroy'])->name('estimates.unshare');

    Route::resource('templates', ServiceTemplateController::class)->except('show');

    Route::get('compare', CompareController::class)->name('compare.index');
    Route::inertia('calculator', 'Calculator')->name('calculator');

    Route::get('pricing-defaults', [PricingDefaultsController::class, 'edit'])->name('pricing-defaults.edit');
    Route::put('pricing-defaults', [PricingDefaultsController::class, 'update'])->name('pricing-defaults.update');

    Route::get('company/settings', [CompanySettingsController::class, 'edit'])->name('company-settings.edit');
    Route::post('company/settings', [CompanySettingsController::class, 'update'])->name('company-settings.update');

    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');

    Route::get('services', [ServiceCatalogController::class, 'index'])->name('services.index');
    Route::post('services', [ServiceCatalogController::class, 'storeService'])->name('services.store');
    Route::put('services/{service}', [ServiceCatalogController::class, 'updateService'])->name('services.update');
    Route::delete('services/{service}', [ServiceCatalogController::class, 'destroyService'])->name('services.destroy');
    Route::post('service-groups', [ServiceCatalogController::class, 'storeGroup'])->name('service-groups.store');
    Route::put('service-groups/{group}', [ServiceCatalogController::class, 'updateGroup'])->name('service-groups.update');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::patch('users/{user}/active', [UserController::class, 'toggleActive'])->name('users.active');
    Route::post('users/{user}/invite', [UserController::class, 'invite'])->name('users.invite');
});

require __DIR__.'/settings.php';

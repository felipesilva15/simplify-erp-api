<?php

use App\Modules\Catalog\Http\Controllers\ProductCategoryController;
use App\Modules\Geography\Http\Controllers\AddressController;
use App\Modules\Geography\Http\Controllers\CityController;
use App\Modules\Geography\Http\Controllers\CountryController;
use App\Modules\Geography\Http\Controllers\StateController;
use App\Modules\HR\Http\Controllers\ProfessionController;
use App\Modules\Security\Http\Controllers\AuthController;
use App\Modules\Security\Http\Controllers\ModuleController;
use App\Modules\Security\Http\Controllers\PermissionController;
use App\Modules\Security\Http\Controllers\ResourceController;
use App\Modules\Security\Http\Controllers\RoleController;
use App\Modules\Security\Http\Controllers\UserController;
use App\Modules\ThirdParty\Http\Controllers\PartnerController;
use App\Modules\ThirdParty\Http\Controllers\PartnerTypeController;
use App\Modules\Catalog\Http\Controllers\ProductBrandController;
use App\Modules\Catalog\Http\Controllers\MeasurementUnitController;
use Illuminate\Support\Facades\Route;

Route::post('security/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('security/auth/token', [AuthController::class, 'token'])->name('auth.token');

Route::group(['middleware' => 'auth'], function () {
    Route::prefix('security')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::get('users/lookup', [UserController::class, 'lookup'])->name('users.lookup');
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');
        Route::crudResource('users', UserController::class);

        Route::get('roles/lookup', [RoleController::class, 'lookup'])->name('roles.lookup');
        Route::get('roles/export', [RoleController::class, 'export'])->name('roles.export');
        Route::crudResource('roles', RoleController::class);
        Route::patch('roles/{role}/permissions', [RoleController::class, 'definePermissions'])->name('roles.definePermissions');

        Route::crudResource('permissions', PermissionController::class);

        Route::crudResource('modules', ModuleController::class);
        Route::crudResource('resources', ResourceController::class);
    });

    Route::prefix('geography')->group(function () {
        Route::get('countries/lookup', [CountryController::class, 'lookup'])->name('countries.lookup');
        Route::resource('countries', CountryController::class)->only('index', 'show');

        Route::get('states/lookup', [StateController::class, 'lookup'])->name('states.lookup');
        Route::resource('states', StateController::class)->only('index', 'show');

        Route::get('cities/lookup', [CityController::class, 'lookup'])->name('cities.lookup');
        Route::resource('cities', CityController::class)->only('index', 'show');

        Route::crudResource('addresses', AddressController::class);
    });

    Route::prefix('third-party')->group(function () {
        Route::get('partner-types/lookup', [PartnerTypeController::class, 'lookup'])->name('partnerTypes.lookup');
        Route::get('partner-types/export', [PartnerTypeController::class, 'export'])->name('partnerTypes.export');
        Route::crudResource('partner-types', PartnerTypeController::class);

        Route::get('partners/lookup', [PartnerController::class, 'lookup'])->name('partners.lookup');
        Route::get('partners/export', [PartnerController::class, 'export'])->name('partners.export');
        Route::crudResource('partners', PartnerController::class);
    });

    Route::prefix('hr')->group(function () {
        Route::get('professions/lookup', [ProfessionController::class, 'lookup'])->name('professions.lookup');
        Route::get('professions/export', [ProfessionController::class, 'export'])->name('professions.export');
        Route::get('professions/{id}/activity-logs', [ProfessionController::class, 'activityLogs'])->name('professions.activityLogs');
        Route::resource('professions', ProfessionController::class)->only('index', 'show');
    });

    Route::prefix('catalog')->group(function () {
        Route::get('product-categories/lookup', [ProductCategoryController::class, 'lookup'])->name('productCategories.lookup');
        Route::get('product-categories/export', [ProductCategoryController::class, 'export'])->name('productCategories.export');
        Route::crudResource('product-categories', ProductCategoryController::class);
        
        Route::get('product-brands/lookup', [ProductBrandController::class, 'lookup'])->name('productBrands.lookup');
        Route::get('product-brands/export', [ProductBrandController::class, 'export'])->name('productBrands.export');
        Route::crudResource('product-brands', ProductBrandController::class);

        Route::get('measurement-units/lookup', [MeasurementUnitController::class, 'lookup'])->name('measurementUnits.lookup');
        Route::get('measurement-units/export', [MeasurementUnitController::class, 'export'])->name('measurementUnits.export');
        Route::crudResource('measurement-units', MeasurementUnitController::class);
    });
});

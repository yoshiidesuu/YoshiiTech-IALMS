<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public theme CSS route (accessible without authentication)
Route::get('admin/theme/css', [App\Http\Controllers\Admin\ThemeController::class, 'generateCSS'])
     ->name('admin.theme.generateCSS');

// Service Worker route (accessible without authentication)
Route::get('/sw.js', function () {
    return response()->view('sw')
        ->header('Content-Type', 'application/javascript')
        ->header('Service-Worker-Allowed', '/');
})->name('sw');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Admin routes with role-based access
    Route::prefix('admin')->name('admin.')->middleware('role:super_admin,admin')->group(function () {
        
        // Admin Dashboard
        Route::get('/', function () {
            return view('dashboard');
        })->name('dashboard');
        
        // User Management Routes
        Route::resource('users', App\Http\Controllers\UserController::class);
        Route::patch('users/{user}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');
        
        // Role Management Routes
        Route::resource('roles', App\Http\Controllers\RoleController::class);
        Route::patch('roles/{role}/toggle-status', [App\Http\Controllers\RoleController::class, 'toggleStatus'])
             ->name('roles.toggle-status');
        
        // Permission Management Routes
        Route::resource('permissions', App\Http\Controllers\PermissionController::class);
        Route::patch('permissions/{permission}/toggle-status', [App\Http\Controllers\PermissionController::class, 'toggleStatus'])
             ->name('permissions.toggle-status');
        
        // Configuration Management Routes
        Route::resource('configurations', App\Http\Controllers\Admin\ConfigurationController::class);
        Route::post('configurations/clear-cache', [App\Http\Controllers\Admin\ConfigurationController::class, 'clearCache'])
             ->name('configurations.clear-cache');
        Route::get('configurations/export', [App\Http\Controllers\Admin\ConfigurationController::class, 'export'])
             ->name('configurations.export');
        
        // Theme Management Routes
        Route::get('theme', [App\Http\Controllers\Admin\ThemeController::class, 'index'])
             ->name('theme.index');
        Route::put('theme', [App\Http\Controllers\Admin\ThemeController::class, 'update'])
             ->name('theme.update');
        Route::post('theme/reset', [App\Http\Controllers\Admin\ThemeController::class, 'reset'])
             ->name('theme.reset');
        // Note: CSS generation route moved outside admin group for public access
        
        // Branding Management Routes
        Route::get('branding', [App\Http\Controllers\Admin\BrandingController::class, 'index'])
             ->name('branding.index');
        Route::put('branding', [App\Http\Controllers\Admin\BrandingController::class, 'update'])
             ->name('branding.update');
        Route::post('branding/reset', [App\Http\Controllers\Admin\BrandingController::class, 'reset'])
             ->name('branding.reset');
        Route::delete('branding/file', [App\Http\Controllers\Admin\BrandingController::class, 'deleteFile'])
             ->name('branding.deleteFile');
        Route::get('branding/settings', [App\Http\Controllers\Admin\BrandingController::class, 'getSettings'])
             ->name('branding.getSettings');
        
        // SMTP Configuration Routes
         Route::get('smtp', [App\Http\Controllers\Admin\SmtpController::class, 'index'])
              ->name('smtp.index');
         Route::post('smtp/update', [App\Http\Controllers\Admin\SmtpController::class, 'update'])
              ->name('smtp.update');
         Route::post('smtp/test', [App\Http\Controllers\Admin\SmtpController::class, 'testConnection'])
              ->name('smtp.test');
         Route::get('smtp/settings', [App\Http\Controllers\Admin\SmtpController::class, 'getSettings'])
              ->name('smtp.getSettings');
         Route::get('smtp/templates', [App\Http\Controllers\Admin\SmtpController::class, 'getEmailTemplates'])
              ->name('smtp.getTemplates');
        
        // Two-Factor Authentication
        Route::get('two-factor', [App\Http\Controllers\Admin\TwoFactorController::class, 'index'])
             ->name('two-factor.index');
        Route::post('two-factor/enable', [App\Http\Controllers\Admin\TwoFactorController::class, 'enable'])
             ->name('two-factor.enable');
        Route::post('two-factor/confirm', [App\Http\Controllers\Admin\TwoFactorController::class, 'confirm'])
             ->name('two-factor.confirm');
        Route::post('two-factor/disable', [App\Http\Controllers\Admin\TwoFactorController::class, 'disable'])
             ->name('two-factor.disable');
        Route::post('two-factor/backup-codes', [App\Http\Controllers\Admin\TwoFactorController::class, 'generateBackupCodes'])
             ->name('two-factor.backup-codes');
        Route::get('two-factor/status', [App\Http\Controllers\Admin\TwoFactorController::class, 'getStatus'])
             ->name('two-factor.status');
        
        // Email Template Routes
        Route::get('email-templates', [App\Http\Controllers\Admin\EmailTemplateController::class, 'index'])
             ->name('email-templates.index');
        Route::get('email-templates/get', [App\Http\Controllers\Admin\EmailTemplateController::class, 'getTemplates'])
             ->name('email-templates.get');
        Route::post('email-templates', [App\Http\Controllers\Admin\EmailTemplateController::class, 'store'])
             ->name('email-templates.store');
        Route::put('email-templates/{id}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'update'])
             ->name('email-templates.update');
        Route::delete('email-templates/{id}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'delete'])
             ->name('email-templates.delete');
        Route::post('email-templates/preview', [App\Http\Controllers\Admin\EmailTemplateController::class, 'preview'])
             ->name('email-templates.preview');
        Route::post('email-templates/test', [App\Http\Controllers\Admin\EmailTemplateController::class, 'sendTest'])
              ->name('email-templates.test');
        
        // Maintenance Mode Management
        Route::get('maintenance', [App\Http\Controllers\Admin\MaintenanceModeController::class, 'index'])
              ->name('maintenance.index');
        Route::post('maintenance/enable', [App\Http\Controllers\Admin\MaintenanceModeController::class, 'enable'])
              ->name('maintenance.enable');
        Route::post('maintenance/disable', [App\Http\Controllers\Admin\MaintenanceModeController::class, 'disable'])
              ->name('maintenance.disable');
        Route::get('maintenance/status', [App\Http\Controllers\Admin\MaintenanceModeController::class, 'status'])
              ->name('maintenance.status');
        Route::post('maintenance/config', [App\Http\Controllers\Admin\MaintenanceModeController::class, 'updateConfig'])
              ->name('maintenance.updateConfig');
        
        // File Security Management
        Route::get('file-security', [App\Http\Controllers\Admin\FileSecurityController::class, 'index'])
              ->name('file-security.index');
        Route::post('file-security/config', [App\Http\Controllers\Admin\FileSecurityController::class, 'updateConfig'])
              ->name('file-security.updateConfig');
        Route::get('file-security/status', [App\Http\Controllers\Admin\FileSecurityController::class, 'getStatus'])
              ->name('file-security.status');
        Route::post('file-security/test', [App\Http\Controllers\Admin\FileSecurityController::class, 'testValidation'])
              ->name('file-security.testValidation');
        Route::get('file-security/quarantined', [App\Http\Controllers\Admin\FileSecurityController::class, 'getQuarantinedFiles'])
              ->name('file-security.quarantined');
        Route::post('file-security/delete-quarantined', [App\Http\Controllers\Admin\FileSecurityController::class, 'deleteQuarantinedFile'])
              ->name('file-security.deleteQuarantined');
        
        // Academic Management Routes (Phase 3)
        
        // Academic Year Management Routes
        Route::resource('academic-years', App\Http\Controllers\Admin\AcademicYearController::class);
        Route::patch('academic-years/{academicYear}/toggle-status', [App\Http\Controllers\Admin\AcademicYearController::class, 'toggleStatus'])
             ->name('academic-years.toggle-status');
        Route::patch('academic-years/{academicYear}/set-current', [App\Http\Controllers\Admin\AcademicYearController::class, 'setCurrent'])
             ->name('academic-years.set-current');
        Route::patch('academic-years/{academicYear}/archive', [App\Http\Controllers\Admin\AcademicYearController::class, 'archive'])
             ->name('academic-years.archive');
        Route::patch('academic-years/{academicYear}/restore', [App\Http\Controllers\Admin\AcademicYearController::class, 'restore'])
             ->name('academic-years.restore');
        
        // Semester Management Routes
        Route::resource('semesters', App\Http\Controllers\Admin\SemesterController::class);
        Route::patch('semesters/{semester}/toggle-status', [App\Http\Controllers\Admin\SemesterController::class, 'toggleStatus'])
             ->name('semesters.toggle-status');
        Route::patch('semesters/{semester}/set-current', [App\Http\Controllers\Admin\SemesterController::class, 'setCurrent'])
             ->name('semesters.set-current');
        Route::patch('semesters/{semester}/toggle-enrollment', [App\Http\Controllers\Admin\SemesterController::class, 'toggleEnrollment'])
             ->name('semesters.toggle-enrollment');
        
        // Subject Management Routes
        Route::resource('subjects', App\Http\Controllers\Admin\SubjectController::class);
        Route::patch('subjects/{subject}/toggle-status', [App\Http\Controllers\Admin\SubjectController::class, 'toggleStatus'])
             ->name('subjects.toggle-status');
        Route::get('subjects/search-prerequisites', [App\Http\Controllers\Admin\SubjectController::class, 'searchPrerequisites'])
             ->name('subjects.search-prerequisites');
        Route::post('subjects/{subject}/prerequisites', [App\Http\Controllers\Admin\SubjectController::class, 'updatePrerequisites'])
             ->name('subjects.update-prerequisites');
        
        // Policy Management Routes
        Route::resource('policies', App\Http\Controllers\Admin\PolicyController::class);
        Route::patch('policies/{policy}/toggle-status', [App\Http\Controllers\Admin\PolicyController::class, 'toggleStatus'])
             ->name('policies.toggle-status');
        Route::post('policies/{policy}/publish', [App\Http\Controllers\Admin\PolicyController::class, 'publish'])
             ->name('policies.publish');
        Route::post('policies/{policy}/unpublish', [App\Http\Controllers\Admin\PolicyController::class, 'unpublish'])
             ->name('policies.unpublish');
        Route::get('policies/{policy}/versions', [App\Http\Controllers\Admin\PolicyController::class, 'getVersions'])
             ->name('policies.versions');
        Route::post('policies/{policy}/create-version', [App\Http\Controllers\Admin\PolicyController::class, 'createVersion'])
             ->name('policies.create-version');
        
        // Grade Encoding Period Management Routes
        Route::resource('grade-periods', App\Http\Controllers\Admin\GradeEncodingPeriodController::class);
        Route::patch('grade-periods/{gradePeriod}/toggle-status', [App\Http\Controllers\Admin\GradeEncodingPeriodController::class, 'toggleStatus'])
             ->name('grade-periods.toggle-status');
        Route::patch('grade-periods/{gradePeriod}/set-current', [App\Http\Controllers\Admin\GradeEncodingPeriodController::class, 'setCurrent'])
             ->name('grade-periods.set-current');
        Route::post('grade-periods/{gradePeriod}/extend-deadline', [App\Http\Controllers\Admin\GradeEncodingPeriodController::class, 'extendDeadline'])
             ->name('grade-periods.extend-deadline');
      });
});

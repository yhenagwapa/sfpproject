<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\MilkFeedingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ChildCenterController;
use App\Http\Controllers\NutritionalStatusController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ImplementationController;
use App\Http\Controllers\ChildDevelopmentCenterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route::get('/register', [ProfileController::class, 'index'])->name('register');
// Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

Route::middleware('guest')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    Route::resources([
        'roles' => RoleController::class,
        'users' => UserController::class,
        'child' => ChildController::class,
    ]);

    Route::get('/child/create', [ChildController::class, 'create'])->name('child.create');
    Route::get('/child/{id}/additional-info', [ChildCenterController::class, 'additionalInfo'])->name('child.additional-info');

    Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');


    Route::get('/attendance/index/{child}', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/{child_id}/store-cycle-attendance', [AttendanceController::class, 'storeCycleAttendance'])->name('attendance.storeCycleAttendance');
    Route::post('/attendance/{child_id}/store-milk-attendance', [AttendanceController::class, 'storeMilkAttendance'])->name('attendance.storeMilkAttendance');

    Route::get('nutritionalstatus/{id}', [NutritionalStatusController::class, 'index'])->name('nutritionalstatus.index');
    Route::post('nutritionalstatus/store-entry', [NutritionalStatusController::class, 'storeUponEntryDetails'])->name('nutritionalstatus.storeUponEntryDetails');
    Route::post('nutritionalstatus/store-exit', [NutritionalStatusController::class, 'storeExitDetails'])->name('nutritionalstatus.storeExitDetails');

    Route::get('nutritionalstatus/{id}/edit', [NutritionalStatusController::class, 'edit'])->name('nutritionalstatus.edit');
    Route::put('nutritionalstatus/{id}/edit-upon-entry', [NutritionalStatusController::class, 'updateUponEntryDetails'])->name('nutritionalstatus.updateUponEntryDetails');
    Route::put('nutritionalstatus/{id}/edit-after-120', [NutritionalStatusController::class, 'updateAfter120Details'])->name('nutritionalstatus.updateAfter120Details');

    Route::get('/centers', [ChildDevelopmentCenterController::class, 'index'])->name(name: 'centers.index');
    Route::get('/centers/create', [ChildDevelopmentCenterController::class, 'create'])->name(name: 'centers.create');
    Route::post('/centers/store', [ChildDevelopmentCenterController::class, 'store'])->name(name: 'centers.store');

    Route::get('/centers/{id}/edit', [ChildDevelopmentCenterController::class, 'edit'])->name(name: 'centers.edit');
    Route::put('/centers/{center}/update', [ChildDevelopmentCenterController::class, 'update'])->name(name: 'centers.update');

    Route::get('/cycle', [ImplementationController::class, 'index'])->name(name: 'cycle.index');
    Route::get('/cycle/create', [ImplementationController::class, 'create'])->name(name: 'cycle.create');
    Route::post('/cycle/store', [ImplementationController::class, 'store'])->name(name: 'cycle.store');
    Route::get('/cycle/{id}/edit', [ImplementationController::class, 'edit'])->name(name: 'cycle.edit');
    Route::put('/cycle/{cycle}/update', [ImplementationController::class, 'update'])->name(name: 'cycle.update');
    Route::put('/cycle/{cycle}/update-status', [ImplementationController::class, 'updateStatus'])->name(name: 'cycle.update-status');

    Route::post('/reports/{cycle}', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/{cycle}/print/masterlist', [PDFController::class, 'printMasterlist'])->name('reports.print.masterlist');
    Route::get('/reports/{cycle}/print/malnourished', [PDFController::class, 'printMalnourish'])->name('reports.print.malnourished');
    Route::get('/reports/{cycle}/print/disabilities', [PDFController::class, 'printDisabilities'])->name('reports.print.disabilities');
    Route::get('/reports/{cycle}/print/undernourished-upon-entry', [PDFController::class, 'printUndernourishedUponEntry'])->name('reports.print.undernourished-upon-entry');
    Route::get('/reports/{cycle}/print/undernourished-after-120', [PDFController::class, 'printUndernourishedAfter120'])->name('reports.print.undernourished-after-120');
    Route::get('/reports/{cycle}/print/weight-for-age-upon-entry', [PDFController::class, 'printWeightForAgeUponEntry'])->name('reports.print.weight-for-age-upon-entry');
    Route::get('/reports/{cycle}/print/weight-for-age-after-120', [PDFController::class, 'printWeightForAgeAfter120'])->name('reports.print.weight-for-age-after-120');
    Route::get('/reports/{cycle}/print/weight-for-height-upon-entry', [PDFController::class, 'printWeightForHeightUponEntry'])->name('reports.print.weight-for-height-upon-entry');
    Route::get('/reports/{cycle}/print/weight-for-height-after-120', [PDFController::class, 'printWeightForHeightAfter120'])->name('reports.print.weight-for-height-after-120');
    Route::get('/reports/{cycle}/print/height-for-age-upon-entry', [PDFController::class, 'printHeightForAgeUponEntry'])->name('reports.print.height-for-age-upon-entry');
    Route::get('/reports/{cycle}/print/height-for-age-after-120', [PDFController::class, 'printHeightForAgeAfter120'])->name('reports.print.height-for-age-after-120');
    Route::get('/reports/{cycle}/print/age-bracket-upon-entry', [PDFController::class, 'printAgeBracketUponEntry'])->name('reports.print.age-bracket-upon-entry');
    Route::get('/reports/{cycle}/print/age-bracket-after-120', [PDFController::class, 'printAgeBracketAfter120'])->name('reports.print.age-bracket-after-120');
    Route::get('/reports/{cycle}/print/monitoring', [PDFController::class, 'printMonitoring'])->name('reports.print.monitoring');
    Route::get('/reports/{cycle}/print/unfunded', [PDFController::class, 'printUnfunded'])->name('reports.print.unfunded');

    Route::post('/milkfeedings/report/{milkfeeding}', [MilkFeedingController::class, 'reportIndex'])->name('milkfeedings.report');
    Route::get('/milkfeedings/report/{milkfeeding}/print/masterlist', [MilkFeedingController::class, 'printMasterlist'])->name('milkfeedings.print.masterlist');
    Route::get('/milkfeedings/report/{milkfeeding}/print/malnourish', [MilkFeedingController::class, 'printMalnourish'])->name('milkfeedings.print.malnourished');
    Route::get('/milkfeedings/report/{milkfeeding}/print/disabilities', [MilkFeedingController::class, 'printDisabilities'])->name('milkfeedings.print.disabilities');
    Route::get('/milkfeedings/report/{milkfeeding}/print/monitoring', [MilkFeedingController::class, 'printMonitoring'])->name('milkfeedings.print.monitoring');
    Route::get('/milkfeedings/report/{milkfeeding}/print/undernourished-upon-entry', [MilkFeedingController::class, 'printUndernourishedUponEntry'])->name('milkfeedings.print.undernourished-upon-entry');
    Route::get('/milkfeedings/report/{milkfeeding}/print/undernourished-after-120', [MilkFeedingController::class, 'printUndernourishedAfter120'])->name('milkfeedings.print.undernourished-after-120');
    Route::get('/milkfeedings/report/{milkfeeding}/print/age-bracket-upon-entry', [MilkFeedingController::class, 'printEntryAgeBracket'])->name('milkfeedings.print.age-bracket-upon-entry');
    Route::get('/milkfeedings/report/{milkfeeding}/print/age-bracket-after-120', [MilkFeedingController::class, 'printAfter120AgeBracket'])->name('milkfeedings.print.age-bracket-after-120');
    Route::get('/milkfeedings/report/{milkfeeding}/print/weight-for-age-upon-entry', [MilkFeedingController::class, 'printWeightForAgeUponEntry'])->name('milkfeedings.print.weight-for-age-upon-entry');
    Route::get('/milkfeedings/report/{milkfeeding}/print/weight-for-age-after-120', [MilkFeedingController::class, 'printWeightForAgeAfter120'])->name('milkfeedings.print.weight-for-age-after-120');
    Route::get('/milkfeedings/report/{milkfeeding}/print/weight-for-height-upon-entry', [MilkFeedingController::class, 'printWeightForHeightUponEntry'])->name('milkfeedings.print.weight-for-height-upon-entry');
    Route::get('/milkfeedings/report/{milkfeeding}/print/weight-for-height-after-120', [MilkFeedingController::class, 'printWeightForHeightAfter120'])->name('milkfeedings.print.weight-for-height-after-120');
    Route::get('/milkfeedings/report/{milkfeeding}/print/height-for-age-upon-entry', [MilkFeedingController::class, 'printHeightForAgeUponEntry'])->name('milkfeedings.print.height-for-age-upon-entry');
    Route::get('/milkfeedings/report/{milkfeeding}/print/height-for-age-after-120', [MilkFeedingController::class, 'printHeightForAgeAfter120'])->name('milkfeedings.print.height-for-age-after-120');
    Route::get('/milkfeedings/report/{milkfeeding}/print/unfunded', [MilkFeedingController::class, 'printUnfunded'])->name('reports.print.unfunded');

    Route::get('/activitylogs', [ActivityLogController::class, 'index'])->name('activitylogs.index');
});


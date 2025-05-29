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


Route::get('/child', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('child');

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

Route::middleware(['auth', 'temp.edit'])->group(function () {

    Route::get('/child', [ChildController::class, 'index'])->name('child.index');
    Route::get('/child/create', [ChildController::class, 'create'])->name('child.create');
    Route::post('/child/store', [ChildController::class, 'store'])->name('child.store');
    Route::post('/child/view', [ChildController::class, 'view'])->name('child.view');
    Route::post('/child/show', [ChildController::class, 'show'])->name('child.show');
    Route::get('/child/edit', [ChildController::class, 'edit'])->name('child.edit');
    Route::patch('/child/update', [ChildController::class, 'update'])->name('child.update');

    Route::resources([
        'roles' => RoleController::class
        // 'users' => UserController::class,
    ]);

    Route::post('/clear-child-session', function (\Illuminate\Http\Request $request) {
        session()->forget(['step', 'step1Data', 'step2Data']);
        return redirect(request('redirect_url', '/'));
    })->name('clear.child.session');

    Route::get('/users/index', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/show', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/update', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
    Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');


    Route::get('/attendance/index/{child}', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/{child_id}/store-cycle-attendance', [AttendanceController::class, 'storeCycleAttendance'])->name('attendance.storeCycleAttendance');
    Route::post('/attendance/{child_id}/store-milk-attendance', [AttendanceController::class, 'storeMilkAttendance'])->name('attendance.storeMilkAttendance');

    Route::post('/nutritionalstatus/create', [NutritionalStatusController::class, 'create'])->name('nutritionalstatus.create');
    Route::get('/nutritionalstatus', [NutritionalStatusController::class, 'index'])->name('nutritionalstatus.index');
    Route::post('/nutritionalstatus/store-entry', [NutritionalStatusController::class, 'storeUponEntryDetails'])->name('nutritionalstatus.storeUponEntryDetails');
    Route::post('/nutritionalstatus/store-exit', [NutritionalStatusController::class, 'storeExitDetails'])->name('nutritionalstatus.storeExitDetails');
    Route::post('/nutritionalstatus/show', [NutritionalStatusController::class, 'show'])->name('nutritionalstatus.show');
    Route::get('/nutritionalstatus/edit', [NutritionalStatusController::class, 'edit'])->name('nutritionalstatus.edit');
    Route::patch('nutritionalstatus/updateUponEntryDetails', [NutritionalStatusController::class, 'updateUponEntryDetails'])->name('nutritionalstatus.updateUponEntryDetails');
    Route::patch('nutritionalstatus/updateAfter120Details', [NutritionalStatusController::class, 'updateAfter120Details'])->name('nutritionalstatus.updateAfter120Details');

    Route::get('/centers', [ChildDevelopmentCenterController::class, 'index'])->name(name: 'centers.index');
    Route::get('/centers/create', [ChildDevelopmentCenterController::class, 'create'])->name(name: 'centers.create');
    Route::post('/centers/store', [ChildDevelopmentCenterController::class, 'store'])->name(name: 'centers.store');
    Route::post('/centers/show', [ChildDevelopmentCenterController::class, 'show'])->name(name: 'centers.show');
    Route::get('/centers/edit', [ChildDevelopmentCenterController::class, 'edit'])->name(name: 'centers.edit');
    Route::post('/centers/update', [ChildDevelopmentCenterController::class, 'update'])->name(name: 'centers.update');

    Route::get('/cycle', [ImplementationController::class, 'index'])->name(name: 'cycle.index');
    Route::get('/cycle/create', [ImplementationController::class, 'create'])->name(name: 'cycle.create');
    Route::post('/cycle/checkActiveStatus', [ImplementationController::class, 'checkActiveStatus'])->name(name: 'cycle.checkActiveStatus');
    Route::post('/cycle/store', [ImplementationController::class, 'store'])->name(name: 'cycle.store');
    Route::post('/cycle/show', [ImplementationController::class, 'show'])->name(name: 'cycle.show');
    Route::get('/cycle/edit', [ImplementationController::class, 'edit'])->name(name: 'cycle.edit');
    Route::patch('/cycle/update', [ImplementationController::class, 'update'])->name(name: 'cycle.update');
    Route::patch('/cycle/update-cycle-status', [ImplementationController::class, 'updateCycleStatus'])->name(name: 'cycle.update-cycle-status');
    Route::patch('/cycle/update-milkfeeding-status', [ImplementationController::class, 'updateMilkFeedingStatus'])->name(name: 'cycle.update-milkfeeding-status');


    //--------
    // Route::get('/cycle/reports', [ReportsController::class, 'index'])->name(name: 'cycle.reports.index');
    // Route::get('/cycle/reports/print/masterlist', [PDFController::class, 'printMasterlist'])->name('cycle.reports.print.masterlist');
    // Route::get('/cycle/reports/print/age-bracket-upon-entry', [PDFController::class, 'printAgeBracketUponEntry2'])->name('cycle.reports.print.age-bracket-upon-entry');
    // Route::get('/cycle/reports/print/age-bracket-after-120', [PDFController::class, 'printAgeBracketAfter1202'])->name('cycle.reports.print.age-bracket-after-120');
    // Route::get('/cycle/reports/print/monitoring', [PDFController::class, 'printMonitoring2'])->name('cycle.reports.print.monitoring');
    // Route::get('/cycle/reports/print/unfunded', [PDFController::class, 'printUnfunded2'])->name('cycle.reports.print.unfunded');
    // Route::get('/cycle/reports/print/malnourished', [PDFController::class, 'printMalnourish2'])->name('cycle.reports.print.malnourished');
    // Route::get('/cycle/reports/print/disabilities', [PDFController::class, 'printDisabilities2'])->name('cycle.reports.print.disabilities');
    // Route::get('/cycle/reports/print/undernourished-upon-entry', [PDFController::class, 'printUndernourishedUponEntry2'])->name('cycle.reports.print.undernourished-upon-entry');
    // Route::get('/cycle/reports/print/undernourished-after-120', [PDFController::class, 'printUndernourishedAfter1202'])->name('cycle.reports.print.undernourished-after-120');
    // Route::get('/cycle/reports/print/nutritional-status-upon-entry', [PDFController::class, 'printNutritionalStatusUponEntry'])->name('cycle.reports.print.nutritional-status-upon-entry');
    // Route::get('/cycle/reports/print/nutritional-status-after-120', [PDFController::class, 'printNutritionalStatusAfter120'])->name('cycle.reports.print.nutritional-status-after-120');
    //--------

    Route::post('/reports/show', [ReportsController::class, 'show'])->name('reports.show');
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/print/masterlist', [PDFController::class, 'printMasterlist'])->name('reports.print.masterlist');
    Route::post('/reports/print/malnourished', [PDFController::class, 'printMalnourish'])->name('reports.print.malnourished');
    Route::post('/reports/print/disabilities', [PDFController::class, 'printDisabilities'])->name('reports.print.disabilities');
    Route::post('/reports/print/undernourished-upon-entry', [PDFController::class, 'printUndernourishedUponEntry'])->name('reports.print.undernourished-upon-entry');
    Route::post('/reports/print/undernourished-after-120', [PDFController::class, 'printUndernourishedAfter120'])->name('reports.print.undernourished-after-120');
    // Route::post('/reports/print/{reportType}', [PDFController::class, 'nutritionalStatusReport'])->name('reports.print');

    Route::post('/reports/print/nutritional-status-upon-entry', [ReportsController::class, 'mergeNutrutionalStatusReport'])->name('reports.print.nutritional-status-upon-entry');
    Route::post('/reports/print/nutritional-status-after-120', [PDFController::class, 'printNutritionalStatusAfter120'])->name('reports.print.nutritional-status-after-120');

    Route::post('/reports/print/age-bracket-upon-entry', [PDFController::class, 'printAgeBracketUponEntry'])->name('reports.print.age-bracket-upon-entry');
    Route::post('/reports/print/age-bracket-after-120', [PDFController::class, 'printAgeBracketAfter120'])->name('reports.print.age-bracket-after-120');
    Route::post('/reports/print/monitoring', [PDFController::class, 'printMonitoring'])->name('reports.print.monitoring');
    Route::post('/reports/print/unfunded', [PDFController::class, 'printUnfunded'])->name('reports.print.unfunded');
    // Route::post('/reports/print/height-for-age-upon-entry', [PDFController::class, 'printHeightForAgeUponEntry'])->name('reports.print.height-for-age-upon-entry');
    // Route::post('/reports/print/weight-for-age-upon-entry', [PDFController::class, 'printWeightForAgeUponEntry'])->name('reports.print.weight-for-age-upon-entry');
    // Route::post('/reports/print/weight-for-height-upon-entry', [PDFController::class, 'printWeightForHeightUponEntry'])->name('reports.print.weight-for-height-upon-entry');



    // Route::post('/milkfeedings/report/{milkfeeding}', [MilkFeedingController::class, 'reportIndex'])->name('milkfeedings.report');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/masterlist', [MilkFeedingController::class, 'printMasterlist'])->name('milkfeedings.print.masterlist');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/malnourish', [MilkFeedingController::class, 'printMalnourish'])->name('milkfeedings.print.malnourished');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/disabilities', [MilkFeedingController::class, 'printDisabilities'])->name('milkfeedings.print.disabilities');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/monitoring', [MilkFeedingController::class, 'printMonitoring'])->name('milkfeedings.print.monitoring');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/undernourished-upon-entry', [MilkFeedingController::class, 'printUndernourishedUponEntry'])->name('milkfeedings.print.undernourished-upon-entry');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/undernourished-after-120', [MilkFeedingController::class, 'printUndernourishedAfter120'])->name('milkfeedings.print.undernourished-after-120');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/age-bracket-upon-entry', [MilkFeedingController::class, 'printEntryAgeBracket'])->name('milkfeedings.print.age-bracket-upon-entry');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/age-bracket-after-120', [MilkFeedingController::class, 'printAfter120AgeBracket'])->name('milkfeedings.print.age-bracket-after-120');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/weight-for-age-upon-entry', [MilkFeedingController::class, 'printWeightForAgeUponEntry'])->name('milkfeedings.print.weight-for-age-upon-entry');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/weight-for-age-after-120', [MilkFeedingController::class, 'printWeightForAgeAfter120'])->name('milkfeedings.print.weight-for-age-after-120');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/weight-for-height-upon-entry', [MilkFeedingController::class, 'printWeightForHeightUponEntry'])->name('milkfeedings.print.weight-for-height-upon-entry');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/weight-for-height-after-120', [MilkFeedingController::class, 'printWeightForHeightAfter120'])->name('milkfeedings.print.weight-for-height-after-120');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/height-for-age-after-120', [MilkFeedingController::class, 'printHeightForAgeAfter120'])->name('milkfeedings.print.height-for-age-after-120');
    // Route::get('/milkfeedings/report/{milkfeeding}/print/unfunded', [MilkFeedingController::class, 'printUnfunded'])->name('reports.print.unfunded');

    Route::get('/activitylogs', [ActivityLogController::class, 'index'])->name('activitylogs.index');
});


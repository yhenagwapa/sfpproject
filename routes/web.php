<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\NutritionalStatusController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\CycleImplementationController;
use App\Http\Controllers\ChildDevelopmentCenterController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\PDFController;


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


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

    Route::get('/child/search', [ChildController::class, 'search'])->name('child.search');

    Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
    Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::put('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');


    Route::get('/attendance/index/{child}', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store/{child}', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('nutritionalstatus/{id}', [NutritionalStatusController::class, 'index'])->name('nutritionalstatus.index');
    Route::post('nutritionalstatus/store', [NutritionalStatusController::class, 'storeUponEntryDetails'])->name('nutritionalstatus.storeUponEntryDetails');
    Route::put('nutritionalstatus/store', [NutritionalStatusController::class, 'storeExitDetails'])->name('nutritionalstatus.storeExitDetails');
    
    Route::get('/centers', [ChildDevelopmentCenterController::class, 'index'])->name(name: 'centers.index');
    Route::get('/centers/create', [ChildDevelopmentCenterController::class, 'create'])->name(name: 'centers.create');
    Route::post('/centers/store', [ChildDevelopmentCenterController::class, 'store'])->name(name: 'centers.store');
    
    Route::get('/centers/{id}/edit', [ChildDevelopmentCenterController::class, 'edit'])->name(name: 'centers.edit');
    Route::put('/centers/{center}/update', [ChildDevelopmentCenterController::class, 'update'])->name(name: 'centers.update');

    Route::get('/cycle', [CycleImplementationController::class, 'index'])->name(name: 'cycle.index');
    Route::get('/cycle/create', [CycleImplementationController::class, 'create'])->name(name: 'cycle.create');
    Route::post('/cycle/store', [CycleImplementationController::class, 'store'])->name(name: 'cycle.store');
    Route::get('/cycle/{id}/edit', [CycleImplementationController::class, 'edit'])->name(name: 'cycle.edit');
    Route::put('/cycle/{cycle}/update', [CycleImplementationController::class, 'update'])->name(name: 'cycle.update');

    Route::post('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/malnourish', [ReportsController::class, 'malnourish'])->name('reports.malnourish');
    Route::post('/reports/disabilities', [ReportsController::class, 'disabilities'])->name('reports.disabilities');
    Route::post('/reports/monitoring', [ReportsController::class, 'monitoring'])->name('reports.monitoring');
    Route::post('/reports/undernourished-upon-entry', [ReportsController::class, 'undernourishedUponEntry'])->name('reports.undernourished-upon-entry');
    Route::post('/reports/undernourished-after-120', [ReportsController::class, 'undernourishedAfter120'])->name('reports.undernourished-after-120');
    Route::post('/reports/age-bracket-upon-entry', [ReportsController::class, 'entryAgeBracket'])->name('reports.age-bracket-upon-entry');
    Route::post('/reports/age-bracket-after-120', [ReportsController::class, 'after120AgeBracket'])->name('reports.age-bracket-after-120');
    Route::post('/reports/weight-for-age-upon-entry', [ReportsController::class, 'weightForAgeUponEntry'])->name('reports.weight-for-age-upon-entry');
    Route::post('/reports/weight-for-age-after-120', [ReportsController::class, 'weightForAgeAfter120'])->name('reports.weight-for-age-after-120');
    Route::post('/reports/weight-for-height-upon-entry', [ReportsController::class, 'weightForHeightUponEntry'])->name('reports.weight-for-height-upon-entry');
    Route::post('/reports/weight-for-height-after-120', [ReportsController::class, 'weightForHeightAfter120'])->name('reports.weight-for-height-after-120');
    Route::post('/reports/height-for-age-upon-entry', [ReportsController::class, 'heightForAgeUponEntry'])->name('reports.height-for-age-upon-entry');
    Route::post('/reports/height-for-age-after-120', [ReportsController::class, 'heightForAgeAfter120'])->name('reports.height-for-age-after-120');
    Route::post('/reports/unfunded', [ReportsController::class, 'unfunded'])->name('reports.unfunded');

    Route::get('/reports/print/masterlist', [ReportsController::class, 'printMasterlist'])->name('reports.print.masterlist');
});


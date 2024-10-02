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

Route::get('/register', [ProfileController::class, 'index'])->name('register');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Route::middleware('auth')->group(function () {
    
   
    Route::resources([
        'roles' => RoleController::class,
        'users' => UserController::class,
        'child' => ChildController::class,
    ]);

    Route::get('/child/search', [ChildController::class, 'search'])->name('child.search');

    // In your routes/web.php
    // Route::get('/location-data', [ChildController::class, 'getLocationData'])->name('location.data');


    // Route::get('/provinces/{region_psgc}', [ChildController::class, 'getProvinces'])->name('get.provinces');
    // Route::get('/cities/{province_psgc}', [ChildController::class, 'getCities'])->name('get.cities');
    // Route::get('/barangays/{city_psgc}', [ChildController::class, 'getBarangays'])->name('get.barangays');
    // Route::get('/psgc/{region_psgc}/{province_psgc}/{city_name_psgc}/{brgy_psgc}', [ChildController::class, 'getPsgcId'])->name('get.psgc_id');
    // Route::get('/location/{psgc_id}', [ChildController::class, 'getLocationData'])->name('location.get');

    Route::post('/child/filter-by-cdc', [ChildController::class, 'filterByCdc'])->name('child.filterByCdc');


    Route::get('/attendance/index/{child}', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store/{child}', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('nutritionalstatus/{id}', [NutritionalStatusController::class, 'index'])->name('nutritionalstatus.index');
    Route::post('nutritionalstatus/store', [NutritionalStatusController::class, 'storeUponEntryDetails'])->name('nutritionalstatus.storeUponEntryDetails');
    Route::put('nutritionalstatus/store', [NutritionalStatusController::class, 'storeExitDetails'])->name('nutritionalstatus.storeExitDetails');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/reports/filter-funded-by-cdc', [ReportsController::class, 'filterFundedByCdc'])->name('reports.filterFundedByCdc');
    Route::post('/reports/filter-unfunded-by-cdc', [ReportsController::class, 'filterUnfundedByCdc'])->name('reports.filterUnfundedByCdc');

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

    // Route::get('/generate-pdf', [ReportsController::class, 'generatePDF'])->name('generate.pdf');
    
// });

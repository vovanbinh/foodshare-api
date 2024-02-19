<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\locationDonateController;

Route::get('/login', function () {
    return view('login');
})->name('showlogin');
Route::post('/login', [AdminController::class, 'login'])->name('login');
Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['checkAdmin'])->group(function () {

    Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin_logout');
    Route::get('/dashboard', [AdminController::class, 'show_dashboard'])->name('show_dashboard');
    Route::get('/manage/donated', [AdminController::class, 'show_manage_donated'])->name('show_manage_donated');
    Route::get('/manage/transactions', [AdminController::class, 'show_manage_transactions'])->name('show_manage_transactions');
    Route::get('/manage/users', [AdminController::class, 'show_manage_users'])->name('show_manage_users');
    //lock_donated
    Route::get('/manage/lock-donated/{lock_id}', [AdminController::class, 'lock_donated'])->name('lock_donated');
    Route::get('/manage/donated/{food_donated_id}', [AdminController::class, 'manage_donated_detail'])->name('manage_donated_detail');
    Route::get('/manage/transaction/lock/{lock_id}', [AdminController::class, 'lock_transaction'])->name('lock_transaction');
    //lock_user
    Route::get('/manage/user/lock/{lock_id}', [AdminController::class, 'lock_user'])->name('lock_user');
    //role_user
    Route::get('/manage/user-role/{user_id}', [AdminController::class, 'show_role_user'])->name('show_role_user');
    Route::get('/manage/user-role/{user_id}/{role}', [AdminController::class, 'role_user'])->name('role_user');

    Route::get('/location/add-new-location', [locationDonateController::class, 'show_add_new_location'])->name('show_add_new_location');
    Route::post('/location/new-location', [locationDonateController::class, 'new_location'])->name('new_location');
    Route::post('/location/edit-location', [locationDonateController::class, 'edit_location'])->name('edit_location');
    Route::get('/location/list-locations', [locationDonateController::class, 'show_list_locations'])->name('show_list_locations');
    Route::get('/location/edit-location/{itemId}', [locationDonateController::class, 'show_edit_location'])->name('show_edit_location');
    Route::Get('/block-location/{itemId}', [locationDonateController::class, 'block_location'])->name('block_location');
    Route::Get('/unlock-location/{itemId}', [locationDonateController::class, 'unlock_location'])->name('unlock_location');
    Route::get('/get-district/{province_id}', [locationDonateController::class, 'get_district'])->name('get_district');
    Route::get('/get-ward/{ward_id}', [locationDonateController::class, 'get_ward'])->name('get_ward');
    Route::get('/charts', [AdminController::class, 'view_Charts'])->name('view_Charts');
    Route::get('/charts/{timeRange}', [AdminController::class, 'getChartData'])->name('getChartData');

    Route::get('/error-notification', [AdminController::class, 'show_error_notification'])->name('show_error_notification');

});

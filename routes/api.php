<?php

use App\Http\Controllers\api\AddreaasController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\api\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Authcontroller;
use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\api\FoodTransactionsController;
use App\Http\Controllers\api\RatingController;
use App\Http\Controllers\locationDonateController;

//chào em
//public router
Route::get('/get-categories', [CategoryController::class, 'index'])->name('indexCategories');
Route::get('/get-detail-food/{foodSlug}', [FoodController::class, 'getDetail'])->name('getFoodDetail');
Route::get('/get-detail-page-receiver-list/{foodId}', [FoodController::class, 'getDetailPageReceiverList'])->name('getDetailPageReceiverList');
Route::get('/getFoodWithCategory/{category}', [FoodController::class, 'getFoodWithCategory'])->name('getFoodWithCategory');
Route::get('/get-provinces', [FoodController::class, 'getProvinces'])->name('getProvinces');
Route::get('/get-all-district-of-provinceId/{provinceID}', [FoodController::class, 'getAllDistrictOfProvinceId'])->name('getAllDistrictOfProvinceId');
Route::get('/get-all-ward-of-districtId/{districtID}', [FoodController::class, 'getAllWardOfDistrictId'])->name('getAllWardOfDistrictId');
Route::get('/getNameProvince/{provinceId}', [FoodController::class, 'getNameProvince'])->name('getNameProvince');
Route::get('/getNameDistrict/{districtId}', [FoodController::class, 'getNameDistrict'])->name('getNameDistrict');
Route::get('/getNameWard/{wardId}', [FoodController::class, 'getNameWard'])->name('getNameWard');
Route::get('/get-locations', [locationDonateController::class, 'getListLocations'])->name('getListLocations');
Route::get('/get-detail-location/{locationSlug}', [locationDonateController::class, 'getDetailLocation'])->name('getDetailLocation');
Route::get('/get-public-profice/{userId}', [UsersController::class, 'getPublicProfice'])->name('getPublicProfice');

//jwt router
Route::middleware('jwt')->group(function () {
    Route::post('/add-to-cart', [FoodTransactionsController::class, 'collectFood'])->name('collect_food');
    Route::get('/get-total-cart/{userId}', [FoodTransactionsController::class, 'getTotalCart'])->name('getTotalCart');
    Route::get('/get-received-list', [FoodTransactionsController::class, 'getReceivedList'])->name('getReceivedList');
    Route::post('/cancel-received', [FoodTransactionsController::class, 'cancelReceived'])->name('cancelReceived');
    Route::get('/history-transactions', [FoodTransactionsController::class, 'historyTransactions'])->name('historyTransactions');
    Route::post('/confirm-received', [FoodTransactionsController::class, 'confirmReceived'])->name('confirmReceived');
    Route::post('/notifi-viewed', [FoodTransactionsController::class, 'notifiViewed'])->name('notifiViewed');
    Route::post('/notifi-confirm', [FoodTransactionsController::class, 'notifiConfirm'])->name('notifiConfirm');
    Route::post('/notifi-refuse', [FoodTransactionsController::class, 'notifiRefuse'])->name('notifiRefuse');
    Route::get('/detail-transaction/{transactionId}', [FoodTransactionsController::class, 'detailTransaction'])->name('detailTransaction');
    Route::post('/rating', [RatingController::class, 'rating'])->name('rating');

    Route::post('/donate-food', [FoodController::class, 'addDonateFood'])->name('addDonateFood');
    Route::get('/food-donated-detail/{foodId}', [FoodController::class, 'foodDonatedDetail'])->name('foodDonatedDetail');
    Route::get('/get-donate-list', [FoodController::class, 'getDonateList'])->name('getDonateList');
    Route::post('/cancel-donate-food', [FoodController::class, 'cancelDonateFood'])->name('cancelDonateFood');
    Route::post('/edit-donate-food', [FoodController::class, 'editDonateFood'])->name('editDonateFood');

    Route::post('/edit-profice', [UsersController::class, 'editProfice'])->name('editProfice');
    Route::get('/get-profice', [UsersController::class, 'getProfice'])->name('getProfice');
    Route::post('/new-image-profice', [UsersController::class, 'newAvatar'])->name('newAvatar');
    Route::post('/new-password', [UsersController::class, 'newPassword'])->name('newPassword');

    Route::post('/add-new-address', [AddreaasController::class, 'addNewAddress'])->name('addNewAddress');
    Route::get('/get-all-address', [AddreaasController::class, 'getAllAddress'])->name('getAllAddress');
    Route::post('/update-address', [AddreaasController::class, 'updateAddress'])->name('updateAddress');
    Route::post('/delete-address', [AddreaasController::class, 'deleteAddress'])->name('deleteAddress');

    Route::get('/get-count-notication', [UsersController::class, 'getCountNotication'])->name('getCountNotication');
    Route::post('/notificationSubscribers', [UsersController::class, 'notificationSubscribers'])->name('notificationSubscribers');
    Route::get('/getNoticeDonatedFoods', [UsersController::class, 'getNoticeDonatedFoods'])->name('getNoticeDonatedFoods');
    Route::post('/notifi-viewed-donatedfood', [FoodTransactionsController::class, 'notifiViewedDonatedfood'])->name('notifiViewedDonatedfood');
    Route::post('/error-notifications', [FoodTransactionsController::class, 'errorNotifications'])->name('errorNotifications');
    Route::get('/get-total-notice-transaction', [UsersController::class, 'getTotalNoticeTransaction'])->name('getTotalNoticeTransaction');
    Route::get('/get-total-notice-sub', [UsersController::class, 'getTotalNoticeSub'])->name('getTotalNoticeSub');

    Route::post('/new-message', [ChatController::class, 'newMessage'])->name('newMessage');
    Route::get('/get-messages/{userId}', [ChatController::class, 'getMessages'])->name('getMessages');

});
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login-google', [AuthController::class, 'loginGoogle'])->name('loginGoogle');
    Route::post('/login-facebook', [AuthController::class, 'loginFacebook'])->name('loginFacebook');
    Route::post('/register', [Authcontroller::class, 'register'])->name('register');
    Route::post('/verification', [Authcontroller::class, 'verification'])->name('verification');
    Route::post('/forgot-password', [Authcontroller::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('/verification-forgot', [Authcontroller::class, 'verificationForgot'])->name('verificationForgot');
    Route::post('/new-password-forgot', [Authcontroller::class, 'NewPasswordForgot'])->name('NewPasswordForgot');
});


Route::post('/get-all-user', [Authcontroller::class, 'getAllUsers'])->name('getAllUsers');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Useroperation;
use App\Http\Controllers\JWTAuthController;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\checkCreatorRole;
use App\Http\Controllers\AdminOprerationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\creatorOpreationController;
use App\Http\Controllers\ProductPriceController;



Route::withoutMiddleware([JwtMiddleware::class, CheckAdminRole::class])->group(function () {
    Route::match(['get', 'post'], 'register', [JWTAuthController::class, 'register']);
    Route::match(['get', 'post'], 'login', [JWTAuthController::class, 'login']);
    Route::match(['get', 'post'], 'sendResetCode', [JWTAuthController::class, 'sendResetCode']);
    Route::match(['get', 'post'], 'verifyResetCode', [JWTAuthController::class, 'verifyResetCode']);
    Route::match(['get', 'post'], 'resetPassword', [JWTAuthController::class, 'resetPassword']);
    
});



Route::middleware([JwtMiddleware::class])->group(function () {
    // Kullanıcı işlemleri
    
    Route::match(['get', 'post'], 'logout', [JWTAuthController::class, 'logout']);
    Route::match(['get', 'post'], 'takeOffDays', [Useroperation::class, 'takeOffDays']);
    Route::match(['get', 'post'], 'takeSpecialTask', [Useroperation::class, 'takeSpecialTask']);
    Route::match(['get', 'post'], 'callenderStore', [Useroperation::class, 'callenderStore']);
    Route::match(['get', 'post'], 'userOfdayStore', [Useroperation::class, 'userOfdayStore']);
    Route::match(['get', 'post'], 'userSpecialTaskStore', [Useroperation::class, 'userSpecialTaskStore']);
    Route::match(['get', 'post'], 'userData', [Useroperation::class, 'userData']);
    Route::match(['get', 'post'], 'deleteOffDays', [Useroperation::class, 'deleteOffDays']);
    Route::match(['get', 'post'], 'registerProfilePhoto', [JWTAuthController::class, 'registerProfilePhoto']);


    
    Route::match(['get', 'post'], 'countOfOffDays', [Useroperation::class, 'countOfOffDays']);


    // AdminID güncelleme
    Route::match(['put', 'post'], 'updateAdminID', [Useroperation::class, 'updateAdminID']);
    Route::match(['get', 'post'], 'departmanStore', [AdminOprerationController::class, 'departmanStore']);
    Route::match(['get', 'post'], 'deleteSpecialTask', [Useroperation::class, 'deleteSpecialTask']);


    // Diğer kullanıcı bilgilerini güncelleme
    Route::match(['put', 'post'], 'updateFirstname', [Useroperation::class, 'updateFirstname']);
    Route::match(['put', 'post'], 'updateLastname', [Useroperation::class, 'updateLastname']);
    Route::match(['put', 'post'], 'updateUsername', [Useroperation::class, 'updateUsername']);
    Route::match(['put', 'post'], 'updateGender', [Useroperation::class, 'updateGender']);
    Route::match(['put', 'post'], 'updatePhoneNumber', [Useroperation::class, 'updatePhoneNumber']);
    Route::match(['put', 'post'], 'updateEmail', [Useroperation::class, 'updateEmail']);
    Route::match(['put', 'post'], 'updatePassword', [Useroperation::class, 'updatePassword']);
    Route::match(['put', 'post'], 'updateProfilePhotoUrl', [Useroperation::class, 'updateProfilePhotoUrl']);
    Route::match(['put', 'post'], 'updateRole', [Useroperation::class, 'updateRole']);



    Route::match(['put', 'post'], 'userTotalShiftCounts', [Useroperation::class, 'userTotalShiftCounts']);
    Route::match(['put', 'post'], 'takeWhichShift', [Useroperation::class, 'takeWhichShift']);
    Route::match(['put', 'post'], 'UserinventorieswishStore', [Useroperation::class, 'UserinventorieswishStore']);

    Route::match(['get', 'post'], 'callenderExportJsonToExcel', [AdminOprerationController::class, 'callenderExportJsonToExcel']);
    Route::match(['get', 'post'], 'adminType', [AdminOprerationController::class, 'adminType'])->name('adminType');

});

Route::middleware([JwtMiddleware::class, CheckAdminRole::class])->group(function () {
    Route::match(['get', 'post'], 'createDepartman', [AdminOprerationController::class, 'createDepartman']);
    Route::match(['get', 'put'], 'responseOffDays', [AdminOprerationController::class, 'responseOffDays']);
    Route::match(['get', 'put'], 'responseSpecialTask', [AdminOprerationController::class, 'responseSpecialTask']);
    Route::match(['get', 'post'], 'createCallendar', [AdminOprerationController::class, 'createCallendar']);
    

    
    Route::match(['get', 'post'], 'assignOffDays', [AdminOprerationController::class, 'assignOffDays']);
    Route::match(['get', 'post'], 'assingSpecialTask', [AdminOprerationController::class, 'assingSpecialTask']);
    Route::match(['get', 'post'], 'assingUser', [AdminOprerationController::class, 'assingUser']);
    
    
    Route::match(['get', 'post'], 'userDepartmanAssing', [AdminOprerationController::class, 'userDepartmanAssing']);
    Route::match(['get', 'post'], 'assingSpecialDepartman', [AdminOprerationController::class, 'assingSpecialDepartman']);
    
    Route::match(['get', 'post'], 'addDepartmanOffday', [AdminOprerationController::class, 'addDepartmanOffday']);
    Route::match(['get', 'post'], 'addOffdayOfWeek', [AdminOprerationController::class, 'addOffdayOfWeek']);


    Route::match(['get', 'post'], 'countOfCallender', [AdminOprerationController::class, 'countOfCallender']);
    Route::match(['get', 'post'], 'callanderDelete', [AdminOprerationController::class, 'callanderDelete']);

    // Store routes
    Route::match(['get', 'post'], 'offdayStore', [AdminOprerationController::class, 'offdayStore']);
    Route::match(['get', 'post'], 'userStore', [AdminOprerationController::class, 'userStore']);
    Route::match(['get', 'post'], 'specialtaskStore', [AdminOprerationController::class, 'specialtaskStore']);
    Route::match(['get', 'post'], 'deleteUser', [AdminOprerationController::class, 'deleteUser']);

    Route::match(['get', 'put'], 'updateDepartmansUserCounts', [AdminOprerationController::class, 'updateDepartmansUserCounts']);
    Route::match(['get', 'post'], 'DepartmanOffDayStore', [AdminOprerationController::class, 'DepartmanOffDayStore']);
    Route::match(['get', 'post'], 'deleteDepartman', [AdminOprerationController::class, 'deleteDepartman']);
    Route::match(['get', 'put'], 'updateDepartmanPriority', [AdminOprerationController::class, 'updateDepartmanPriority']);
    Route::match(['get', 'post'], 'departmanOffDayDelete', [AdminOprerationController::class, 'departmanOffDayDelete']);
    Route::match(['get', 'post'], 'usersTotalShiftCounts', [AdminOprerationController::class, 'usersTotalShiftCounts']);
    Route::match(['get', 'post'], 'CallenderusersshiftcountsStore', [AdminOprerationController::class, 'CallenderusersshiftcountsStore']);
    Route::match(['get', 'post'], 'assingSpecialDepartmanStore', [AdminOprerationController::class, 'assingSpecialDepartmanStore']);
    Route::match(['get', 'post'], 'assingSpecialDepartmanDelete', [AdminOprerationController::class, 'assingSpecialDepartmanDelete']);
    Route::match(['get', 'post'], 'departmanColerUpdate', [AdminOprerationController::class, 'departmanColerUpdate']);
    Route::match(['get', 'put'], 'updateUserinventoriesWhichShift', [AdminOprerationController::class, 'updateUserinventoriesWhichShift']);
    Route::match(['get', 'put'], 'ResponseWhichShift', [AdminOprerationController::class, 'ResponseWhichShift']);
    Route::match(['get', 'post'], 'adminUserinventorieswishesStore', [AdminOprerationController::class, 'adminUserinventorieswishesStore']);
    Route::match(['get', 'post'], 'adminUserInventoriesStore', [AdminOprerationController::class, 'adminUserInventoriesStore']);


    
    Route::match(['get', 'post'], 'makePayment', [PaymentController::class, 'makePayment']);
    
    

});
    Route::match(['get', 'post'], 'cancel_url', [PaymentController::class, 'cancel_url'])->name('cancel_url');
    Route::match(['get', 'post'], 'return_url', [PaymentController::class, 'return_url'])->name('return_url');


Route::middleware([JwtMiddleware::class, checkCreatorRole::class])->group(function () {
    
    Route::match(['get', 'post'], 'usersStore', [creatorOpreationController::class, 'usersStore']);
    
    Route::match(['get', 'put'], 'updateUserRole', [creatorOpreationController::class, 'updateUserRole']);
    Route::match(['get', 'put'], 'updateCallenderCount', [creatorOpreationController::class, 'updateCallenderCount']);
    Route::match(['get', 'put'], 'updateCallender', [creatorOpreationController::class, 'updateCallender']);

    Route::match(['get', 'post'], 'productPriceStore', [ProductPriceController::class, 'productPriceStore']);
    Route::match(['get', 'post'], 'productPriceUpdate', [ProductPriceController::class, 'productPriceUpdate']);
    Route::match(['get', 'post'], 'productPriceDelete', [ProductPriceController::class, 'productPriceDelete']);
    Route::match(['get', 'post'], 'productPriceList', [ProductPriceController::class, 'productPriceList']);
    
    

    
});
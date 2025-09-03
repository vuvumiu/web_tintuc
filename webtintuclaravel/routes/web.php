<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\FrontController;

// Định tuyến cho đăng nhập
Route::get('/login', [UserController::class, 'getLogin'])->name('login');
Route::post('/login', [UserController::class, 'postLogin']);
Route::get('/logout', [UserController::class, 'getLogout']);

Route::get('/', [FrontController::class, 'home']);
Route::get('lien-he', [FrontController::class, 'contact']);

Route::get('ve-chung-toi', [FrontController::class, 'about']);
Route::get('tim-kiem', [FrontController::class, 'search']);


Route::post('dang-ky-nhan-tin-khuyen-mai', [FrontController::class, 'subEmail']);
Route::post('gui-email-lien-he', [FrontController::class, 'contactSendEmail']);

Route::get('{slug}.html', [FrontController::class, 'slugHtml']);
Route::get('{slug}', [FrontController::class, 'slug']);



// Administrator
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    // Welcome to admin
    Route::get('/home', [BackController::class, 'home']);

    // Staff
    Route::group(['prefix' => 'staff'], function () {
        Route::get('profile', [BackController::class, 'staff_profile']);
        Route::post('profile', [BackController::class, 'staff_profile_post']);

        Route::get('list', [BackController::class, 'staff_list']);
        Route::get('add', [BackController::class, 'staff_add']);
        Route::post('add', [BackController::class, 'staff_add_post']);
        Route::get('edit/{id}', [BackController::class, 'staff_edit']);
        Route::post('edit/{id}', [BackController::class, 'staff_edit_post']);
        Route::get('delete/{id}', [BackController::class, 'staff_delete']);

        Route::post('filter', [BackController::class, 'staff_filter']);
    });

    Route::get('/system', [BackController::class, 'system']);
    Route::post('/system', [BackController::class, 'system_post']);

    // page management
    Route::group(['prefix' => 'page'], function () {
        Route::get('list', [BackController::class, 'page_list']);
        Route::get('edit/{id}', [BackController::class, 'page_edit']);
        Route::post('edit/{id}', [BackController::class, 'page_edit_post']);
    });
    // social management
    Route::group(['prefix' => 'social'], function () {
        Route::get('list', [BackController::class, 'social_list']);
        Route::get('edit/{id}', [BackController::class, 'social_edit']);
        Route::post('edit/{id}', [BackController::class, 'social_edit_post']);
    });
    // quản lý nhận tin khuyến mại-----------
    Route::group(['prefix' => 'newsletter'], function () {
        Route::get('list', [BackController::class, 'newsletter_list']);
        Route::get('edit/{id}', [BackController::class, 'newsletter_edit']);
        Route::post('edit/{id}', [BackController::class, 'newsletter_edit_post']);
        Route::get('delete/{id}', [BackController::class, 'newsletter_delete']);
    });
    // quản lý liên hệ-----------
    Route::group(['prefix' => 'contact'], function () {
        Route::get('list', [BackController::class, 'contact_list']);
        Route::get('edit/{id}', [BackController::class, 'contact_edit']);
        Route::post('edit/{id}', [BackController::class, 'contact_edit_post']);
        Route::get('delete/{id}', [BackController::class, 'contact_delete']);
    });
    // quản lý tin tức-----------
    Route::group(['prefix' => 'news_cat'], function () {
        Route::get('list', [BackController::class, 'news_cat_list']);
        Route::get('edit/{id}', [BackController::class, 'news_cat_getedit']);
        Route::post('edit/{id}', [BackController::class, 'news_cat_edit']);
    });

    // News
    Route::group(['prefix' => 'news'], function () {
        Route::get('list', [BackController::class, 'news_list']);
        Route::get('add', [BackController::class, 'news_getadd']);
        Route::post('add', [BackController::class, 'news_add']);
        Route::get('edit/{RowID}', [BackController::class, 'news_getedit']);
        Route::post('edit/{id}', [BackController::class, 'news_edit']);
        Route::get('delete/{RowID}', [BackController::class, 'news_delete']);
        Route::post('sort/{id}', [BackController::class, 'news_cat_update_sort']);
    });

    // Slider
    Route::group(['prefix' => 'slider'], function () {
        Route::get('list', [BackController::class, 'slider_list']);
        Route::get('add', [BackController::class, 'slider_getadd']);
        Route::post('add', [BackController::class, 'slider_add']);
        Route::get('edit/{RowID}', [BackController::class, 'slider_getedit']);
        Route::post('edit/{id}', [BackController::class, 'slider_edit']);
        Route::get('delete/{RowID}', [BackController::class, 'slider_delete']);
    });
});

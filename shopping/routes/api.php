<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CatigoryController;
use App\Http\Controllers\SubcatigoryController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Mail\SendCodeResetPassword;
use App\Http\Middleware\checkToken;
use App\Http\Middleware\AdminToken;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//////////////////////////////////  User ///////////////////////////////////////////////////

    Route::post('/regiser',[UserController::class,'regiser']);
    Route::post('/checkcode',[UserController::class,'checkcode']);
    Route::post('/login',[UserController::class,'login']);
//Route::middleware([checktoken::class])->group(function () {
    Route::get('/logout',[UserController::class,'logout']);
    Route::post('/addrating/{id}',[ProductController::class,'AddRating']);
     ////////////////Views
     Route::get('/viewcatigory',[ViewController::class,'ViewCatigory']);
     Route::get('/viewsubcatigory/{id}',[ViewController::class,'ViewSubCatigory']);
     Route::get('/productinfo/{id}',[ViewController::class,'ProductInfo']);
     Route::get('/viewproducts/{id}',[ViewController::class,'ViewProducts']);
     Route::get('/attribute/{id}',[ViewController::class,'ViewProductAttribute']);
     Route::get('/discountproducts',[ViewController::class,'DiscountProducts']);
     ////////////////comment
     Route::post('/addcomment/{id}',[CommentController::class,'AddComment']);
     Route::post('/editcomment/{id}',[CommentController::class,'EditComment']);
     Route::post('/deletecomment/{id}',[CommentController::class,'DeleteComment']);
     Route::get('/viewcomments/{id}',[CommentController::class,'ViewComments']);
     ////////////////Cart
     Route::post('/add/cart',[CartController::class,'Add']);
     Route::post('/remove/cart',[CartController::class,'Remove']);
     Route::get('/count/product/{id}',[CartController::class,'CountProduct']);
     Route::get('/cart',[CartController::class,'ViewCart']);
     ////////////////favorite
     Route::post('/addfavorite',[FavoriteController::class,'AddFavorite']);
     Route::post('/removefavorite',[FavoriteController::class,'DeleteFavorite']);
     Route::get('/viewfavorite',[FavoriteController::class,'ViewFavorite']);
     ////////////////Order
     Route::post('/add/order',[OrderController::class,'AddOrder']);
     Route::get('/view/order',[OrderController::class,'ViewOrders']);
     ////////////////Search
     Route::get('/searchbyname/{name}',[OrderController::class,'SearchByName']);
     Route::get('/searchbyprice/{price}',[OrderController::class,'SearchByPrice']);
     Route::get('/searchbydescription/{des}',[OrderController::class,'SearchBydescription']);
    //////////////////////forget password & reset password (verfication code)
    Route::post('/user/password/email',[UserController::class,'UserForgetPassword']);
    Route::post('/user/password/code/check',[UserController::class,'UserCheckCode']);
    Route::post('/user/password/reset',[UserController::class,'UserResetPassword']);
    // HomePage
    Route::get('/homepage',[ViewController::class,'HomePage']);
    Route::get('/highestrated',[ViewController::class,'highestRated']);
//});   
//////////////////////////////////  Admin ///////////////////////////////////////////////////
//Route::middleware([AdminToken::class])->group(function(){
        ////////////////Catigory
        Route::post('/addcatigory',[CatigoryController::class,'AddCatigory']);
        Route::post('/editcatigory/{id}',[CatigoryController::class,'EditCatigory']);
        Route::delete('/deletecatigory/{id}',[CatigoryController::class,'DeleteCatigory']);
        ////////////////SubCatigory
        Route::post('/addsubcatigory',[SubcatigoryController::class,'AddSubcatigory']);
        Route::post('/editsubcatigory/{id}',[SubcatigoryController::class,'EditSubcatigory']);
        Route::delete('/deletesubcatigory/{id}',[SubcatigoryController::class,'DeleteSubcatigory']);
        ///////////////product
        Route::post('/addproduct',[ProductController::class,'AddProduct']);
        Route::post('/editproduct/{id}',[ProductController::class,'EditProduct']);
        Route::delete('/deleteproduct/{id}',[ProductController::class,'DeleteProduct']);
        Route::post('/discount/{id}',[ProductController::class,'Discount']);  
        ///////////////attribute
        Route::post('/addattribute',[AttributeController::class,'AddAttribute']);
        Route::post('/editattribute/{id}',[AttributeController::class,'EditAttribute']);
        Route::delete('/deleteattribute/{id}',[AttributeController::class,'DeleteAttribute']);
       
        //////////////// users count
        Route::get('/userscount',[AdminController::class,'Userscounts']);
        ////////////////
        
//});

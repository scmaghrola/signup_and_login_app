<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignupController;
use App\Livewire\Counter;

Route::get('/signup', [SignupController::class, 'index'])->name('signup.form');
Route::post('/signup', [SignupController::class, 'signUp'])->name('signup');
Route::post('/send-otp', [SignupController::class, 'sendOtp'])->name('sendOtp');
Route::get('/send-mail', [SignupController::class, 'sendMail']);
Route::post('/verify-otp', [SignupController::class, 'verifyOtp'])->name('verifyOtp');
Route::get('/home', fn() => view('home'))->name('home');


 
Route::get('/counter', Counter::class);
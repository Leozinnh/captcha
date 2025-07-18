<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaptchaController;

Route::get('/', function () {
    return view('layout.index', ['title' => 'Home']);
});
Route::prefix('captcha')->group(function () {
    // Text Captcha
    Route::get('/text', [CaptchaController::class, 'showText']);
    Route::get('/generate-text', [CaptchaController::class, 'generateText']);
    Route::post('/validate-text', [CaptchaController::class, 'validateText']);

    // Grid Captcha
    Route::get('/grid', [CaptchaController::class, 'showGrid']);
    Route::post('/validate-grid', [CaptchaController::class, 'validateGrid']);

    // Drag & Drop Captcha
    Route::get('/dragdrop', [CaptchaController::class, 'showDragDrop']);
    Route::post('/validate-dragdrop', [CaptchaController::class, 'validateDragDrop']);

    // Robot Captcha
    Route::get('/robot', [CaptchaController::class, 'showRobot']);
    Route::post('/validate-robot', [CaptchaController::class, 'validateRobot']);

    Route::get('/math', [CaptchaController::class, 'showMath'])->name('captcha.math');
    Route::post('/math/validate', [CaptchaController::class, 'validateMath'])->name('captcha.math.validate');
    Route::get('/math/image', [CaptchaController::class, 'generateMathImage'])->name('captcha.math.image');
});

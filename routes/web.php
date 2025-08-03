<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TokenController;
use App\Http\Middleware\ValidateCaptchaToken;

// Route::get('/', function () {
//     // return view('layout.index', ['title' => 'Inicio']);
// });
Route::get('/', [Controller::class, 'index']);

Route::prefix('captcha')->group(function () {
    // Text Captcha
    Route::get('/text', [CaptchaController::class, 'showText']);
    Route::post('/validate-text', [CaptchaController::class, 'validateText']);
    Route::get('/generate-text', [CaptchaController::class, 'generateText']);

    // Grid Captcha
    Route::get('/grid', [CaptchaController::class, 'showGrid']);
    Route::post('/validate-grid', [CaptchaController::class, 'validateGrid']);

    // Drag & Drop Captcha
    Route::get('/dragdrop', [CaptchaController::class, 'showDragDrop']);
    Route::post('/validate-dragdrop', [CaptchaController::class, 'validateDragDrop']);

    // Robot Captcha
    Route::get('/robot', [CaptchaController::class, 'showRobot']);
    Route::post('/validate-robot', [CaptchaController::class, 'validateRobot']);

    // Math Captcha
    Route::get('/math', [CaptchaController::class, 'showMath'])->name('captcha.math');
    Route::post('/math/validate', [CaptchaController::class, 'validateMath'])->name('captcha.math.validate');
    Route::get('/math/image', [CaptchaController::class, 'generateMathImage'])->name('captcha.math.image');

    // Voice Captcha
    Route::get('/voice', [CaptchaController::class, 'showVoice'])->name('captcha.voice');
    Route::post('/validate-voice', [CaptchaController::class, 'validateVoice'])->name('captcha.voice.validate');
});

// Rotas para embeds com autenticação por token
Route::prefix('embed')->middleware(ValidateCaptchaToken::class)->group(function () {
    Route::get('/text', [CaptchaController::class, 'embedText'])->name('embed.text');
    Route::get('/robot', [CaptchaController::class, 'embedRobot'])->name('embed.robot');
    Route::get('/math', [CaptchaController::class, 'embedMath'])->name('embed.math');
    Route::get('/grid', [CaptchaController::class, 'embedGrid'])->name('embed.grid');
    Route::get('/dragdrop', [CaptchaController::class, 'embedDragDrop'])->name('embed.dragdrop');
    Route::get('/voice', [CaptchaController::class, 'embedVoice'])->name('embed.voice');

    // API para validação cross-origin
    Route::post('/validate/{type}', [CaptchaController::class, 'validateEmbed'])->name('embed.validate');
});

// Rota para gerar script de incorporação (sem middleware, mas precisa de token via query)
Route::get('/captcha-widget.js', [CaptchaController::class, 'generateWidget'])->name('widget.js');

// Rota para script plugável de voice captcha
Route::get('/voice-captcha.js', function() {
    $js = file_get_contents(public_path('voice-captcha.js'));
    return response($js)->header('Content-Type', 'application/javascript');
})->name('voice-captcha.js');

// Rotas para gerenciamento de tokens
Route::prefix('admin')->group(function () {
    Route::resource('tokens', TokenController::class);
    Route::patch('tokens/{token}/regenerate', [TokenController::class, 'regenerate'])->name('tokens.regenerate');
    Route::patch('tokens/{token}/toggle', [TokenController::class, 'toggleStatus'])->name('tokens.toggle');
});

// Rota de teste (remover em produção)
Route::get('/test-voice', function() {
    return view('test-voice', ['challenge' => 'casa']);
});

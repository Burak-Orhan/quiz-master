<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Models\Quiz;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index'])->name('home');
Route::post('/start', [GameController::class, 'start'])->name('game.start');

Route::post('/finish-game', [QuizController::class, 'finishGame'])->name('game.finish');
Route::get('/result', [QuizController::class, 'showResult'])->name('game.result');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

    Route::post('/login', [AuthController::class, 'login'])->middleware("throttle:5,1")->name("login.post");
    Route::post('/register', [AuthController::class, 'register'])->middleware("throttle:5,1")->name("register.post");
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [QuizController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/{user}', [ProfileController::class, 'index'])->name('profile.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::prefix('quizzes')->group(function () {
        Route::get('/create', [QuizController::class, 'create'])->name('quizzes.create');
        Route::get('/{quiz}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
        Route::get('/{quiz}/report', [QuizController::class, 'report'])->name('quizzes.report');

        Route::post('/', [QuizController::class, 'store'])->middleware("throttle:5,1")->name('quizzes.store');

        Route::patch('/{quiz}/toggle-status', [QuizController::class, 'toggleStatus'])->name('quizzes.toggle-status');
        Route::put('/{quiz}', [QuizController::class, 'update'])->name('quizzes.update');

        Route::delete('/{quiz}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    });

});

Route::get('/api/quiz/{code}', function ($code) {
    $quiz = Quiz::where('code', strtoupper($code))->first();

    if (!$quiz) {
        return response()->json(['message' => 'Sınav bulunamadı'], 404);
    }

    return response()->json([
        'id' => $quiz->id,
        'title' => $quiz->title,
        'questions' => $quiz->questions,
    ]);
});

<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $quizCode = $request->query('quiz_code');
        $quiz = null;

        if ($quizCode) {
            $quiz = Quiz::where('code', $quizCode)->first();
        }

        return Inertia::render('Game/Setup', [
            'quiz' => $quiz ? [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'code' => $quiz->code,
                'questions' => $quiz->questions,
            ] : null,
        ]);
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|integer',
            'mode' => 'required|string',
            'teams' => 'required|array',
            'timeLimit' => 'required|integer',
            'correctPoints' => 'required|integer',
            'wrongPoints' => 'required|integer',
            'questions' => 'required|array|min:1',
        ]);

        return Inertia::render('Game/Game', [
            'quiz_id' => $validated['quiz_id'],
            'settings' => [
                'mode' => $validated['mode'],
                'timeLimit' => $validated['timeLimit'],
                'autoPass' => $request->autoPass,
                'correctPoints' => $validated['correctPoints'],
                'wrongPoints' => $validated['wrongPoints'],
            ],
            'teams' => collect($validated['teams'])->map(function ($team) {
                return [
                    'id' => $team['id'],
                    'name' => $team['name'],
                    'color' => $team['color'],
                    'score' => 0,
                ];
            })->toArray(),
            'questions' => $validated['questions'],
        ]);
    }

    public function result(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|integer',
            'teams' => 'required|array',
            'history' => 'required|array',
        ]);

        $quizId = $validated['quiz_id'];
        $teams = $validated['teams'];
        $history = $validated['history'];

        if ($quizId && $teams) {
            foreach ($teams as $team) {
                QuizAttempt::create([
                    'quiz_id' => $quizId,
                    'participant_name' => $team['name'],
                    'score' => $team['score'],
                    'correct_answers' => collect($history)->where('teamName', $team['name'])->where('isCorrect', true)->count(),
                    'wrong_answers' => collect($history)->where('teamName', $team['name'])->where('isCorrect', false)->count(),
                ]);
            }
        }

        session()->flash('game_results', [
            'teams' => $teams,
            'history' => $history,
        ]);

        return redirect()->route('game.result');
    }

    public function showResult()
    {
        $results = session('game_results');

        if (! $results) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Game/Result', [
            'teams' => $results['teams'],
            'history' => $results['history'],
        ]);
    }

    public function storeQuiz(Request $request)
    {
        Quiz::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'code' => 'QM-'.strtoupper(Str::random(5)),
            'questions' => $request->questions,
        ]);
    }
}

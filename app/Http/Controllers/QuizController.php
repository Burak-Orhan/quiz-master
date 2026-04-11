<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function dashboard()
    {
        $quizzes = Quiz::where('user_id', auth()->id())
            ->addSelect(['plays_count' => QuizAttempt::selectRaw('count(distinct session_id)')
                ->whereColumn('quiz_id', 'quizzes.id'),
            ])
            ->withAvg('attempts', 'score')
            ->orderByRaw("FIELD(status, 'active', 'archived')")
            ->latest()
            ->get();

        return Inertia::render('Dashboard', [
            'quizzes' => $quizzes->map(fn ($q) => [
                'id' => $q->id,
                'slug' => $q->slug,
                'title' => $q->title,
                'code' => $q->code,
                'status' => $q->status,
                'question_count' => is_array($q->questions) ? count($q->questions) : 0,
                'plays_count' => $q->plays_count ?? 0,
                'success_rate' => $q->attempts_avg_score ? round($q->attempts_avg_score) : 0,
            ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Quizzes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array',
            'questions.*.correct' => 'required|string',
        ]);

        $code = 'QM-'.strtoupper(Str::random(5));

        while (Quiz::where('code', $code)->exists()) {
            $code = 'QM-'.strtoupper(Str::random(5));
        }

        Quiz::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'code' => $code,
            'questions' => $validated['questions'],
        ]);

        return redirect()->route('dashboard');
    }

    public function toggleStatus(Request $request, Quiz $quiz)
    {
        if ($quiz->user_id != auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $validated = $request->validate([
            'status' => 'required|in:active,archived',
        ]);

        $quiz->update([
            'status' => $validated['status'],
        ]);

        $message = $validated['status'] === 'archived'
            ? 'Sınav başarıyla arşivlendi.'
            : 'Sınav arşivden çıkarıldı ve tekrar aktif edildi.';

        return back()->with('success', $message);
    }

    public function edit(Quiz $quiz)
    {
        if ($quiz->user_id != auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return Inertia::render('Quizzes/Edit', [
            'quiz' => $quiz,
        ]);
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'questions' => 'required|array',
            'questions.*.text' => 'required|string',
            'questions.*.options' => 'required|array',
            'questions.*.correct' => 'required|string|in:A,B,C,D',
        ]);

        $quiz->update([
            'title' => $validated['title'],
            'questions' => $validated['questions'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Sınav güncellendi.');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->user_id !== auth()->id()) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        $quiz->delete();

        return back()->with('success', 'Sınav kalıcı olarak silindi.');
    }

    public function submitAttempt(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'score' => 'required|numeric',
            'correct_answers' => 'required|integer',
            'wrong_answers' => 'required|integer',
            'participant_name' => 'nullable|string|max:255',
        ]);

        $quiz->attempts()->create([
            'participant_name' => $validated['participant_name'] ?? 'İsimsiz Oyuncu',
            'score' => $validated['score'],
            'correct_answers' => $validated['correct_answers'],
            'wrong_answers' => $validated['wrong_answers'],
        ]);

        return back()->with('success', 'Sınav sonucunuz kaydedildi!');
    }

    public function finishGame(Request $request)
    {
        $quizId = $request->input('quiz_id');
        $teams = $request->input('teams');
        $fullHistory = $request->input('history');

        $sessionId = Str::uuid()->toString();

        if ($quizId && $teams) {
            foreach ($teams as $team) {
                $user = User::where('name', $team['name'])->first();

                $teamHistory = collect($fullHistory)->where('teamName', $team['name'])->values()->toArray();

                $correctCount = collect($teamHistory)->where('isCorrect', true)->count();
                $wrongCount = collect($teamHistory)->where('isCorrect', false)->count();

                QuizAttempt::create([
                    'session_id' => $sessionId,
                    'quiz_id' => $quizId,
                    'user_id' => $user ? $user->id : null,
                    'participant_name' => $team['name'],
                    'score' => $team['score'],
                    'correct_answers' => $correctCount,
                    'wrong_answers' => $wrongCount,
                    'history' => $teamHistory,
                ]);
            }
        }

        session()->flash('game_results', [
            'teams' => $teams,
            'history' => $fullHistory,
        ]);

        return redirect()->route('game.result');
    }

    public function showResult()
    {
        $results = session('game_results');

        if (! $results) {
            return redirect()->route('home');
        }
        
        return Inertia::render('Game/Result', [
            'teams' => $results['teams'],
            'history' => $results['history'],
        ]);
    }

    public function report(Quiz $quiz)
    {
        $attempts = $quiz->attempts()
            ->with('user:id,slug')
            ->orderByDesc('score')
            ->get()
            ->map(function ($attempt) {
                $data = $attempt->toArray();
                $data['user_slug'] = $attempt->user ? $attempt->user->slug : null;

                return $data;
            });

        return Inertia::render('Quizzes/Report', [
            'quiz' => $quiz,
            'attempts' => $attempts,
        ]);
    }
}

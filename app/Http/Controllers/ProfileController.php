<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index(User $user)
    {
        $isOwnProfile = auth()->id() === $user->id;

        $quizzes = Quiz::where('user_id', $user->id)
            ->addSelect(['plays_count' => QuizAttempt::selectRaw('count(distinct session_id)')
                ->whereColumn('quiz_id', 'quizzes.id'),
            ])
            ->get();

        $totalPlays = $quizzes->sum('plays_count');
        $totalSolved = QuizAttempt::where('participant_name', $user->name)->count();
        $monthsActive = now()->diffInMonths($user->created_at);

        $recentAttempts = QuizAttempt::with(['quiz:id,title,code,user_id', 'quiz.user:id,name'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($attempt) {
                if (! $attempt->quiz) {
                    return null;
                }

                return [
                    'quiz_title' => $attempt->quiz->title,
                    'quiz_code' => $attempt->quiz->code,
                    'creator' => $attempt->quiz->user ? $attempt->quiz->user->name : 'Bilinmeyen Kullanıcı',
                    'score' => $attempt->score,
                    'date' => $attempt->created_at->diffForHumans(),
                ];
            })
            ->filter()
            ->values();

        return Inertia::render('Profile', [
            'profileUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'slug' => $user->slug,
                'email' => $user->email,
            ],
            'isOwnProfile' => $isOwnProfile,
            'stats' => [
                'total_quizzes' => $quizzes->count(),
                'total_plays' => $totalPlays,
                'total_solved' => $totalSolved,
                'months_active' => $monthsActive,
                'join_date' => $user->created_at->translatedFormat('F Y'),
            ],
            'top_quizzes' => $quizzes->where('plays_count', '>', 0)->sortByDesc('plays_count')->take(3)->values(),
            'recentAttempts' => $recentAttempts,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil bilgileri başarıyla güncellendi.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Şifreniz başarıyla değiştirildi.');
    }
}

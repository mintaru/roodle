<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\Course;
use App\Models\TestAttempt;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Отчет об активности пользователей
     */
    public function userActivity()
    {
        $users = User::with('groups', 'testAttempts')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'last_login' => $user->updated_at->format('d.m.Y H:i'),
                    'groups' => $user->groups->pluck('name')->join(', '),
                    'tests_passed' => $user->testAttempts()->distinct('test_id')->count(),
                    'total_attempts' => $user->testAttempts()->count(),
                ];
            });

        return view('admin.reports.user-activity', ['users' => $users]);
    }

    /**
     * Отчет по группам
     */
    public function groupsReport()
    {
        $groups = Group::with('users', 'courses')
            ->get()
            ->map(function ($group) {
                // Вычисляем средний балл по тестам для пользователей в этой группе
                $userIds = $group->users->pluck('id')->toArray();
                $averageScore = TestAttempt::whereIn('user_id', $userIds)
                    ->avg('score') ?? 0;

                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'users_count' => $group->users->count(),
                    'courses_count' => $group->courses->count(),
                    'average_score' => round($averageScore, 2),
                ];
            });

        return view('admin.reports.groups', ['groups' => $groups]);
    }

    /**
     * Отчет по курсам
     */
    public function coursesReport()
    {
        $courses = Course::with('lectures', 'tests', 'groups')
            ->get()
            ->map(function ($course) {
                // Получаем всех пользователей, связанных с курсом через группы
                $groupIds = $course->groups->pluck('id')->toArray();
                $usersCount = DB::table('group_user')
                    ->whereIn('group_id', $groupIds)
                    ->distinct('user_id')
                    ->count('user_id');

                // Если нет пользователей через группы, пытаемся найти через других способов
                if ($usersCount === 0) {
                    $testIds = $course->tests->pluck('id')->toArray();
                    $usersCount = TestAttempt::whereIn('test_id', $testIds)
                        ->distinct('user_id')
                        ->count('user_id');
                }

                // Вычисляем средний результат тестов по курсу
                $testIds = $course->tests->pluck('id')->toArray();
                $averageScore = TestAttempt::whereIn('test_id', $testIds)
                    ->avg('score') ?? 0;

                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'lectures_count' => $course->lectures->count(),
                    'tests_count' => $course->tests->count(),
                    'users_count' => $usersCount,
                    'average_score' => round($averageScore, 2),
                ];
            });

        return view('admin.reports.courses', ['courses' => $courses]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\TeacherCoursePermission;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherCoursePermissionController extends Controller
{
    /**
     * Показать список всех преподавателей и их права доступа
     */
    public function index()
    {
        // Получаем всех учителей с их разрешениями
        $teachers = User::where(function ($query) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'teacher');
            })
            ->orWhereHas('coursePermissions');
        })
        ->with(['coursePermissions.course'])
        ->orderBy('name')
        ->get();

        return view('admin.teacher-permissions.index', [
            'teachers' => $teachers,
        ]);
    }

    /**
     * Показать форму для назначения прав преподавателю для конкретного курса
     */
    public function editTeacher(User $user)
    {
        $user->load(['coursePermissions.course']);
        $allCourses = Course::active()->orderBy('title')->get();

        // Получаем ID курсов, к которым уже есть доступ
        $permittedCourseIds = $user->coursePermissions->pluck('course_id')->toArray();

        return view('admin.teacher-permissions.edit-teacher', [
            'user' => $user,
            'allCourses' => $allCourses,
            'permittedCourseIds' => $permittedCourseIds,
        ]);
    }

    /**
     * Обновить права доступа преподавателя
     */
    public function updateTeacher(Request $request, User $user)
    {
        $validated = $request->validate([
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'permissions' => 'nullable|array',
        ]);

        // Удаляем старые разрешения
        $user->coursePermissions()->delete();

        // Добавляем новые разрешения
        if (isset($validated['courses']) && is_array($validated['courses'])) {
            foreach ($validated['courses'] as $courseId) {
                TeacherCoursePermission::create([
                    'user_id' => $user->id,
                    'course_id' => (int) $courseId,
                    'can_edit' => $request->boolean("permissions.{$courseId}.can_edit"),
                    'can_delete' => $request->boolean("permissions.{$courseId}.can_delete"),
                    'can_manage_students' => $request->boolean("permissions.{$courseId}.can_manage_students"),
                ]);
            }
        }

        return redirect()->route('admin.teacher-permissions.index')
            ->with('success', "Права доступа для {$user->name} успешно обновлены");
    }

    /**
     * Показать курсы и преподавателей с доступом
     */
    public function editCourse(Course $course)
    {
        $course->load('permittedTeachers');
        
        // Получаем всех преподавателей
        $allTeachers = User::whereHas('roles', function ($q) {
            $q->where('name', 'teacher');
        })
        ->orderBy('name')
        ->get();

        // Получаем ID преподавателей с доступом
        $permittedTeacherIds = $course->permittedTeachers->pluck('id')->toArray();

        return view('admin.teacher-permissions.edit-course', [
            'course' => $course,
            'allTeachers' => $allTeachers,
            'permittedTeacherIds' => $permittedTeacherIds,
        ]);
    }

    /**
     * Обновить права для курса
     */
    public function updateCourse(Request $request, Course $course)
    {
        $validated = $request->validate([
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id',
            'permissions' => 'nullable|array',
        ]);

        // Удаляем старые разрешения для этого курса
        $course->teacherPermissions()->delete();

        // Добавляем новые разрешения
        if (isset($validated['teachers']) && is_array($validated['teachers'])) {
            foreach ($validated['teachers'] as $userId) {
                TeacherCoursePermission::create([
                    'user_id' => (int) $userId,
                    'course_id' => $course->id,
                    'can_edit' => $request->boolean("permissions.{$userId}.can_edit"),
                    'can_delete' => $request->boolean("permissions.{$userId}.can_delete"),
                    'can_manage_students' => $request->boolean("permissions.{$userId}.can_manage_students"),
                ]);
            }
        }

        return redirect()->route('admin.teacher-permissions.index')
            ->with('success', "Права доступа для курса \"{$course->title}\" успешно обновлены");
    }

    /**
     * Удалить права доступа
     */
    public function destroy(TeacherCoursePermission $permission)
    {
        $permission->delete();

        return redirect()->back()
            ->with('success', 'Права доступа успешно удалены');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Group;
use App\Models\TeacherCoursePermission;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        $groups = Group::all();
        $courses = Course::active()->orderBy('title')->get();

        return view('admin.users.create', compact('roles', 'groups', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name',
            'teacher_courses' => 'nullable|array',
            'teacher_courses.*' => 'exists:courses,id',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        // Группа только для студентов
        if ($validated['role'] === 'student' && $request->filled('group_id')) {
            $user->groups()->sync([$request->group_id]);
        }

        // Курсы только для учителей
        if ($validated['role'] === 'teacher' && $request->has('teacher_courses')) {
            foreach ($request->teacher_courses as $courseId) {
                TeacherCoursePermission::create([
                    'user_id' => $user->id,
                    'course_id' => (int) $courseId,
                    'can_edit' => $request->boolean("course_permissions.{$courseId}.can_edit"),
                    'can_delete' => $request->boolean("course_permissions.{$courseId}.can_delete"),
                    'can_manage_students' => $request->boolean("course_permissions.{$courseId}.can_manage_students"),
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Пользователь создан');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => "required|string|max:255|unique:users,username,{$user->id}",
            'password' => 'nullable|min:6',
            'role' => 'required|exists:roles,name',
            'teacher_courses' => 'nullable|array',
            'teacher_courses.*' => 'exists:courses,id',
            'group_id' => 'nullable|exists:groups,id',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        $user->syncRoles([$validated['role']]);

        // Группа только для студентов, иначе очищаем
        if ($validated['role'] === 'student' && $request->filled('group_id')) {
            $user->groups()->sync([$request->group_id]);
        } else {
            $user->groups()->sync([]);
        }

        // Курсы только для учителей
        $user->coursePermissions()->delete();

        if ($validated['role'] === 'teacher' && $request->has('teacher_courses')) {
            foreach ($request->teacher_courses as $courseId) {
                TeacherCoursePermission::create([
                    'user_id' => $user->id,
                    'course_id' => (int) $courseId,
                    'can_edit' => $request->boolean("course_permissions.{$courseId}.can_edit"),
                    'can_delete' => $request->boolean("course_permissions.{$courseId}.can_delete"),
                    'can_manage_students' => $request->boolean("course_permissions.{$courseId}.can_manage_students"),
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Пользователь обновлён');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $groups = Group::all();
        $permissions = Permission::all();
        $courses = Course::active()->orderBy('title')->get();

        return view('admin.users.edit', compact('user', 'roles', 'groups', 'permissions', 'courses'));
    }


    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Пользователь удалён');
    }
}

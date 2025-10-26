<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'groups')->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $groups = Group::all();

        return view('admin.users.create', compact('roles', 'groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|exists:roles,name',
        ]);
    
        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
        ]);
    
        $user->assignRole($validated['role']);
    
        if ($request->has('groups')) {
            $user->groups()->sync($request->groups);
        }
    
        return redirect()->route('admin.users.index')->with('success', 'Пользователь создан');
    }
    

    public function edit(User $user)
    {
        $roles = Role::all();
        $groups = Group::all();
        $permissions = Permission::all();

        return view('admin.users.edit', compact('user', 'roles', 'groups', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => "required|string|max:255|unique:users,username,{$user->id}",
            'password' => 'nullable|min:6',
            'role' => 'required|exists:roles,name',
        ]);
    
        $user->name = $validated['name'];
        $user->username = $validated['username'];
    
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
    
        $user->save();
    
        $user->syncRoles([$validated['role']]);
    
        $user->groups()->sync($request->groups ?? []);
        $user->syncPermissions($request->permissions ?? []);
    
        return redirect()->route('admin.users.index')->with('success', 'Пользователь обновлён');
    }
    

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Пользователь удалён');
    }
}

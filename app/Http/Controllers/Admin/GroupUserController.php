<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class GroupUserController extends Controller
{
    public function index()
    {
        $groups = Group::withCount('users')->get();
        return view('admin.groups.index', compact('groups'));
    }

    public function show(Group $group)
    {
        $students = User::role('student')->get();
        $group->load('users');
        return view('admin.groups.show', compact('group', 'students'));
    }

    public function assign(Request $request, Group $group)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $group->users()->syncWithoutDetaching([$request->user_id]);

        return back()->with('success', 'Студент добавлен в группу.');
    }

    public function remove(Group $group, User $user)
    {
        $group->users()->detach($user->id);
        return back()->with('success', 'Студент удалён из группы.');
    }

    // Форма создания группы
public function create()
{
    return view('admin.groups.create');
}

public function destroy(Group $group)
{
    $group->delete();

    return redirect()->route('admin.groups.index')->with('success', 'Группа успешно удалена!');
}

public function edit(Group $group)
{
    return view('admin.groups.edit', compact('group'));
}

// Сохранение новой группы
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|unique:groups,name|max:255',
        'description' => 'nullable|string',
    ]);

    Group::create([
        'name' => $request->name
    ]);

    return redirect()->route('groups.index')->with('success', 'Группа успешно создана.');
}

}

<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserSearch extends BaseAdminSearch
{
    protected function getQuery(): Builder
    {
        return User::with(['roles', 'groups']);
    }

    public function getSearchColumns(): array
    {
        return [
            'name'     => 'Имя',
            'username' => 'Логин',
            'role'     => 'Роль',
            'group'    => 'Группа',
        ];
    }

    protected function getView(): string
    {
        return 'livewire.admin.users';
    }

    public function render()
    {
        $items = $this->getQuery()
            ->when($this->searchValue, function($q) {
                match($this->searchColumn) {
                    'name', 'username' => $q->where($this->searchColumn, 'like', "%{$this->searchValue}%"),
                    'role'  => $q->whereHas('roles',  fn($q) => $q->where('name', 'like', "%{$this->searchValue}%")),
                    'group' => $q->whereHas('groups', fn($q) => $q->where('name', 'like', "%{$this->searchValue}%")),
                    default => null,
                };
            })
            ->get();

        return view($this->getView(), ['items' => $items]);
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;

class GroupSearch extends BaseAdminSearch
{
    protected function getQuery(): Builder
    {
        return Group::withCount('users');
    }

    public function getSearchColumns(): array
    {
        return [
            'name'        => 'Название группы',
            'id'          => 'ID',
            'users_count' => 'Количество студентов',
        ];
    }

    public function render()
    {
        $items = $this->getQuery()
            ->when($this->searchValue, function($q) {
                if ($this->searchColumn === 'users_count') {
                    $q->having('users_count', '=', (int) $this->searchValue);
                } else {
                    $q->where($this->searchColumn, 'like', "%{$this->searchValue}%");
                }
            })
            ->get();

        return view($this->getView(), ['items' => $items]);
    }

    protected function getView(): string
    {
        return 'livewire.admin.groups';
    }
}

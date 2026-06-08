<?php

namespace App\Livewire\Admin;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

abstract class BaseAdminSearch extends Component
{
    public string $searchColumn = '';
    public string $searchValue = '';

    abstract protected function getQuery(): Builder;
    abstract public function getSearchColumns(): array;
    abstract protected function getView(): string;

    public function mount(): void
    {
        $this->searchColumn = array_key_first($this->getSearchColumns());
    }

    public function render()
    {
        $items = $this->getQuery()
            ->when($this->searchValue, fn($q) =>
                $q->where($this->searchColumn, 'like', "%{$this->searchValue}%")
            )
            ->get();

        return view($this->getView(), ['items' => $items]);
    }
}

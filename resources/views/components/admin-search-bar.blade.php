<div class="search-box" style="margin-bottom: 1.5rem;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="admin-search-grid">
        <div>
            <label>Искать по колонке:</label>
            <select wire:model.live="searchColumn">
                @foreach($searchColumns as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Поисковый запрос:</label>
            <input type="text"
                   wire:model.live.debounce.300ms="searchValue"
                   placeholder="Введите текст для поиска...">
        </div>
    </div>
</div>

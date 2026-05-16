<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Информация профиля
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Обновите информацию вашего аккаунта
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6"
          enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Аватарка --}}
        <div>
            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                Аватарка
            </label>

            <div class="flex items-center gap-4">
                {{-- Превью --}}
                <div id="avatar-preview-wrapper"
                     class="w-20 h-20 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center shrink-0">
                    @if ($user->avatar)
                        <img id="avatar-preview"
                             src="{{ Storage::url($user->avatar) }}"
                             alt="Аватар"
                             class="w-full h-full object-cover" />
                    @else
                        <img id="avatar-preview"
                             src=""
                             alt="Аватар"
                             class="w-full h-full object-cover hidden" />
                        <span id="avatar-placeholder" class="text-3xl text-gray-400">👤</span>
                    @endif
                </div>

                {{-- Кнопка выбора файла --}}
                <div>
                    <label for="avatar"
                           class="cursor-pointer inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:border-indigo-400 transition">
                        📁 Выбрать фото
                    </label>
                    <input id="avatar" name="avatar" type="file"
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="hidden" />
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        JPG, PNG, WEBP — до 2 МБ
                    </p>
                </div>
            </div>

            @error('avatar')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>

        {{-- ФИО --}}
        <div>
            <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">ФИ</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}" required
                   class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
            @error('name')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>

        {{-- Логин --}}
        <div>
            <label for="username" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Логин</label>
            <input id="username" name="username" type="text"
                   value="{{ old('username', $user->username) }}" required
                   class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
            @error('username')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>



        <div class="flex items-center gap-4">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Сохранить
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-600 dark:text-green-400">Сохранено успешно</p>
            @endif
        </div>
    </form>
</section>

{{-- Превью выбранного фото --}}
<script>
    document.getElementById('avatar').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-placeholder');

            preview.src = event.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
</script>

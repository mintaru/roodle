<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Информация профиля
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Обновите информацию вашего аккаунта
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">ФИ</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
            @error('name')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="username" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Логин (имя пользователя)</label>
            <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
            @error('username')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
            @error('email')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Сохранить
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-600 dark:text-green-400">Сохранено успешно</p>
            @endif
        </div>
    </form>
</section>

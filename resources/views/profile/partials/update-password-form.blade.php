<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Изменить пароль
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Используйте надежный пароль для вашей безопасности.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Текущий пароль</label>
            <input id="current_password" name="current_password" type="password" class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
            @error('updatePassword.current_password')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Новый пароль</label>
            <input id="password" name="password" type="password" class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
            @error('updatePassword.password')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Подтвердите пароль</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
            @error('updatePassword.password_confirmation')
                <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Сохранить
            </button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-green-600 dark:text-green-400">Пароль обновлен успешно</p>
            @endif
        </div>
    </form>
</section>

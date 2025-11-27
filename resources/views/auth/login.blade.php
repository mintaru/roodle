<x-guest-layout>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-indigo-700 mb-2">Roodle</h1>
        <p class="text-gray-700">Образовательная платформа</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" value="Имя пользователя" class="text-gray-700" />
            <x-text-input id="username"
                          class="block mt-1 w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                          type="text"
                          name="username"
                          :value="old('username')"
                          required autofocus
                          autocomplete="username"
                          placeholder="Введите имя пользователя" />
            <x-input-error :messages="$errors->get('username')" class="mt-2 text-red-600" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Пароль" class="text-gray-700" />
            <x-text-input id="password"
                          class="block mt-1 w-full border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                          type="password"
                          name="password"
                          required autocomplete="current-password"
                          placeholder="Введите пароль" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       class="rounded border-gray-400 text-indigo-600 shadow-sm focus:ring-indigo-500"
                       name="remember">
                <span class="ms-2 text-sm text-gray-700">Запомнить меня</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-indigo-600 hover:text-indigo-800 rounded-md
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                   href="{{ route('password.request') }}">
                    Забыли пароль?
                </a>
            @endif

            <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-lg">
                Войти
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>

<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Восстановление пароля</h2>
        <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">Забыли пароль? Не проблема.</p>
    </div>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Введите адрес электронной почты вашего аккаунта, и мы отправим вам ссылку для восстановления пароля.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="example@mail.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                Вернуться на вход
            </a>

            <x-primary-button>
                Отправить ссылку
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

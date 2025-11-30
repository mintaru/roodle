<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Подтверждение пароля</h2>
    </div>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Это защищенная область приложения. Пожалуйста, подтвердите ваш пароль перед продолжением.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Пароль" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Введите пароль" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-6">
            <x-primary-button>
                Подтвердить
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

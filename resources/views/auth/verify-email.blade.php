<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Подтверждение email</h2>
    </div>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Спасибо за регистрацию! Перед началом работы, пожалуйста, подтвердите ваш адрес электронной почты, нажав на ссылку, которую мы отправили вам. Если вы не получили письмо, мы с удовольствием отправим вам новое.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            Новая ссылка подтверждения была отправлена на адрес электронной почты, который вы указали при регистрации.
        </div>
    @endif

    <div class="mt-6 flex items-center justify-between gap-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Отправить ссылку еще раз
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                Выйти
            </button>
        </form>
    </div>
</x-guest-layout>
</x-guest-layout>

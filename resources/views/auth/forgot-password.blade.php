<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Восстановление пароля</h2>
    </div>

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Введите адрес электронной почты вашего аккаунта, и мы отправим вам ссылку для восстановления пароля.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


</x-guest-layout>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Удалить аккаунт
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            После удаления аккаунта все его данные будут удалены навсегда.
        </p>
    </header>

    <button type="button" onclick="document.getElementById('deleteModal').style.display='block'" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
        Удалить аккаунт
    </button>

    <div id="deleteModal" style="display:none" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Вы уверены, что хотите удалить аккаунт?
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Это действие нельзя отменить. Все ваши данные будут удалены.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" class="mt-6">
                @csrf
                @method('delete')

                <div class="mb-4">
                    <label for="password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Пароль для подтверждения</label>
                    <input id="password" name="password" type="password" class="block w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:border-indigo-500 focus:ring-indigo-500 mt-1" />
                    @error('userDeletion.password')
                        <div class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex justify-end gap-4">
                    <button type="button" onclick="document.getElementById('deleteModal').style.display='none'" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150">
                        Отмена
                    </button>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Удалить аккаунт
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="username" style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--color-text-muted,#9eaab7);margin-bottom:6px;">
                Имя пользователя
            </label>
            <input
                id="username"
                type="text"
                name="username"
                value="{{ old('username') }}"
                required autofocus
                autocomplete="username"
                placeholder="Введите имя пользователя"
                style="width:100%;padding:11px 16px;border:1.5px solid var(--color-border,#e2e8ed);border-radius:var(--r-lg,16px);font-size:14px;font-family:var(--font-body,'Manrope',sans-serif);color:var(--color-text-primary,#111720);background:var(--color-surface,#fff);transition:.2s ease;outline:none;"
                onfocus="this.style.borderColor='var(--teal-400,#26c6b8)';this.style.boxShadow='0 0 0 3px rgba(0,181,165,.12)'"
                onblur="this.style.borderColor='var(--color-border,#e2e8ed)';this.style.boxShadow='none'"
            >
            @error('username')
                <p style="margin-top:6px;font-size:13px;color:var(--red-500,#e53935);">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-top:1.25rem;">
            <label for="password" style="display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:var(--color-text-muted,#9eaab7);margin-bottom:6px;">
                Пароль
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Введите пароль"
                style="width:100%;padding:11px 16px;border:1.5px solid var(--color-border,#e2e8ed);border-radius:var(--r-lg,16px);font-size:14px;font-family:var(--font-body,'Manrope',sans-serif);color:var(--color-text-primary,#111720);background:var(--color-surface,#fff);transition:.2s ease;outline:none;"
                onfocus="this.style.borderColor='var(--teal-400,#26c6b8)';this.style.boxShadow='0 0 0 3px rgba(0,181,165,.12)'"
                onblur="this.style.borderColor='var(--color-border,#e2e8ed)';this.style.boxShadow='none'"
            >
            @error('password')
                <p style="margin-top:6px;font-size:13px;color:var(--red-500,#e53935);">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-top:1rem;">
            <label for="remember_me" style="display:inline-flex;align-items:center;gap:8px;cursor:pointer;">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    style="width:16px;height:16px;accent-color:var(--teal-500,#00b5a5);border-radius:4px;cursor:pointer;"
                >
                <span style="font-size:13px;color:var(--color-text-secondary,#6b7a89);">Запомнить меня</span>
            </label>
        </div>

        <div style="margin-top:1.75rem;">
            <button
                type="submit"
                style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 24px;background:var(--teal-500,#00b5a5);color:#fff;border:none;border-radius:var(--r-full,999px);font-family:var(--font-body,'Manrope',sans-serif);font-size:15px;font-weight:700;cursor:pointer;box-shadow:0 4px 16px rgba(0,181,165,.3);transition:.2s ease;"
                onmouseover="this.style.background='var(--teal-600,#009e90)';this.style.transform='translateY(-1px)'"
                onmouseout="this.style.background='var(--teal-500,#00b5a5)';this.style.transform='none'"
            >
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Войти
            </button>
        </div>
    </form>
</x-guest-layout>

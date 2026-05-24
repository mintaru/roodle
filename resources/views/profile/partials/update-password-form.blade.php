<section>
    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        {{-- Текущий пароль --}}
        <div style="margin-bottom: 1.25rem;">
            <label for="current_password" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--color-text-muted); margin-bottom: 0.5rem;">
                Текущий пароль
            </label>
            <div style="position: relative;">
                <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); opacity: 0.4; pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input id="current_password" name="current_password" type="password"
                       placeholder="••••••••"
                       style="width: 100%; padding: 11px 14px 11px 40px; border: 1.5px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: var(--transition); outline: none;"
                       onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                       onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'" />
            </div>
            @error('updatePassword.current_password')
                <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 13px; color: var(--red-500);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div style="height: 1px; background: var(--color-border); margin-bottom: 1.25rem;"></div>

        {{-- Новый пароль --}}
        <div style="margin-bottom: 1.25rem;">
            <label for="password" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--color-text-muted); margin-bottom: 0.5rem;">
                Новый пароль
            </label>
            <div style="position: relative;">
                <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); opacity: 0.4; pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input id="password" name="password" type="password"
                       placeholder="••••••••"
                       style="width: 100%; padding: 11px 14px 11px 40px; border: 1.5px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: var(--transition); outline: none;"
                       onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                       onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'" />
            </div>
            @error('updatePassword.password')
                <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 13px; color: var(--red-500);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Подтвердите пароль --}}
        <div style="margin-bottom: 1.75rem;">
            <label for="password_confirmation" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--color-text-muted); margin-bottom: 0.5rem;">
                Подтвердите пароль
            </label>
            <div style="position: relative;">
                <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); opacity: 0.4; pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       placeholder="••••••••"
                       style="width: 100%; padding: 11px 14px 11px 40px; border: 1.5px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: var(--transition); outline: none;"
                       onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                       onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'" />
            </div>
            @error('updatePassword.password_confirmation')
                <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 13px; color: var(--red-500);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Кнопка --}}
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Сохранить
            </button>

            @if (session('status') === 'password-updated')
                <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: var(--green-600);">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    Пароль обновлён успешно
                </div>
            @endif
        </div>
    </form>
</section>

<section>


        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('patch')

            {{-- Аватарка --}}
            <div style="margin-bottom: 1.75rem;">
                <label style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--color-text-muted); margin-bottom: 0.75rem;">
                    Фото профиля
                </label>

                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    {{-- Превью --}}
                    <div id="avatar-preview-wrapper"
                         style="width: 72px; height: 72px; border-radius: 50%; overflow: hidden; background: linear-gradient(135deg, var(--sky-400), var(--teal-500)); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 3px solid var(--color-border); box-shadow: var(--shadow-md);">
                        @if ($user->avatar)
                            <img id="avatar-preview"
                                 src="{{ Storage::url($user->avatar) }}"
                                 alt="Аватар"
                                 style="width: 100%; height: 100%; object-fit: cover;" />
                        @else
                            <img id="avatar-preview"
                                 src=""
                                 alt="Аватар"
                                 style="width: 100%; height: 100%; object-fit: cover; display: none;" />
                            <span id="avatar-placeholder" style="font-size: 28px; color: #fff; line-height: 1;">👤</span>
                        @endif
                    </div>

                    {{-- Кнопка выбора файла --}}
                    <div>
                        <label for="avatar" class="btn btn-ghost" style="cursor: pointer; font-size: 13px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            Выбрать фото
                        </label>
                        <input id="avatar" name="avatar" type="file"
                               accept="image/jpeg,image/png,image/jpg,image/webp"
                               style="display: none;" />
                        <p class="text-sm text-muted" style="margin-top: 6px;">JPG, PNG, WEBP — до 2 МБ</p>
                    </div>
                </div>

                @error('avatar')
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 13px; color: var(--red-500);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>


            {{-- ФИО --}}
            <div style="margin-bottom: 1.25rem;">
                <label for="name" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--color-text-muted); margin-bottom: 0.5rem;">
                    ФИО
                </label>
                <div style="position: relative;">
                    <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); opacity: 0.4; pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <input id="name" name="name" type="text"
                           value="{{ old('name', $user->name) }}" required
                           placeholder="Введите имя и фамилию"
                           style="width: 100%; padding: 11px 14px 11px 40px; border: 1.5px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: var(--transition); outline: none;"
                           onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                           onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'" />
                </div>
                @error('name')
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 13px; color: var(--red-500);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Логин --}}
            <div style="margin-bottom: 1.75rem;">
                <label for="username" style="display: block; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--color-text-muted); margin-bottom: 0.5rem;">
                    Логин
                </label>
                <div style="position: relative;">
                    <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); opacity: 0.4; pointer-events: none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span style="position: absolute; left: 40px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); font-size: 14px; pointer-events: none;">@</span>
                    <input id="username" name="username" type="text"
                           value="{{ old('username', $user->username) }}" required
                           placeholder="username"
                           style="width: 100%; padding: 11px 14px 11px 54px; border: 1.5px solid var(--color-border); border-radius: var(--r-md); font-size: 14px; font-family: var(--font-body); color: var(--color-text-primary); background: var(--color-surface); transition: var(--transition); outline: none;"
                           onfocus="this.style.borderColor='var(--teal-400)'; this.style.boxShadow='0 0 0 3px rgba(0,181,165,0.1)'"
                           onblur="this.style.borderColor='var(--color-border)'; this.style.boxShadow='none'" />
                </div>
                @error('username')
                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 8px; font-size: 13px; color: var(--red-500);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Кнопка сохранить --}}
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Сохранить
                </button>

                @if (session('status') === 'profile-updated')
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: var(--green-600);">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Сохранено успешно
                    </div>
                @endif
            </div>
        </form>
</section>

{{-- Превью выбранного фото --}}
<script>
    document.getElementById('avatar').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-placeholder');

            preview.src = event.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    });
</script>

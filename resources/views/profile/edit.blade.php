<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> Редактирование профиля - Roodle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

</head>
<body>

@include("components.menu")



<div style="padding: 2rem 0;">
    <div style="max-width: 720px; margin: 0 auto; padding: 0 1.5rem; display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- Кнопка назад --}}
        <div>
            <a href="{{ route('home') }}" class="btn btn-ghost" style="font-size: 13px; text-decoration: none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Все курсы
            </a>
        </div>

        {{-- Блок: Информация профиля --}}
        <div class="panel">
            <div class="panel__header">
                <div>
                    <h2 class="panel__title">Информация профиля</h2>
                    <p class="text-sm text-muted" style="margin-top: 4px;">Обновите информацию вашего аккаунта</p>
                </div>
                <div class="badge badge-teal">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Профиль
                </div>
            </div>
            <div>
                @include('profile.partials.update-profile-information-form', ['user' => $user])
            </div>
        </div>

        {{-- Блок: Изменение пароля --}}
        <div class="panel">
            <div class="panel__header">
                <div>
                    <h2 class="panel__title">Изменение пароля</h2>
                    <p class="text-sm text-muted" style="margin-top: 4px;">Убедитесь, что используете надёжный пароль</p>
                </div>
                <div class="badge badge-sky">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Безопасность
                </div>
            </div>
            <div>
                @include('profile.partials.update-password-form')
            </div>
        </div>

    </div>
</div>

</body>
</html>

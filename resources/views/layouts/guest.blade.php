<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --teal-50: #e0f7f4;
            --teal-400: #26c6b8;
            --teal-500: #00b5a5;
            --teal-600: #009e90;
            --teal-700: #00837a;
            --sky-400: #29aff5;
            --sky-500: #0a9fe8;
            --gray-50: #f8fafb;
            --gray-100: #f0f3f5;
            --gray-200: #e2e8ed;
            --gray-300: #cdd6df;
            --gray-400: #9eaab7;
            --gray-500: #6b7a89;
            --gray-700: #333d4a;
            --gray-800: #1e2530;
            --gray-900: #111720;
            --red-500: #e53935;
            --color-bg: var(--gray-50);
            --color-surface: #fff;
            --color-border: var(--gray-200);
            --color-border-2: var(--gray-300);
            --color-text-primary: var(--gray-900);
            --color-text-secondary: var(--gray-500);
            --color-text-muted: var(--gray-400);
            --color-accent: var(--teal-500);
            --r-md: 12px;
            --r-lg: 16px;
            --r-xl: 20px;
            --r-2xl: 28px;
            --r-full: 999px;
            --shadow-md: 0 4px 12px rgba(0, 0, 0, .08), 0 1px 3px rgba(0, 0, 0, .04);
            --font-body: 'Manrope', sans-serif;
            --font-display: 'DM Serif Display', serif;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            font-size: 16px;
        }

        body {
            font-family: var(--font-body);
            background: var(--color-bg);
            color: var(--color-text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-shell {
            width: 100%;
            max-width: 420px;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            display: inline-flex;
            vertical-align: middle;
            align-items: center;
            gap: 2px;
            text-decoration: none;
            margin-bottom: 8px;
        }

        .login-logo__icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--teal-500), var(--sky-500));
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-family: var(--font-display);
            font-size: 22px;
            font-style: italic;
            line-height: 1;
        }

        .login-logo__name {
            font-family: var(--font-display);
            vertical-align: middle;
            line-height: 1;
            font-size: 26px;
            color: var(--gray-800);
        }

        .login-brand__sub {
            font-size: 13px;
            color: var(--color-text-muted);
        }

        .login-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-2xl);
            padding: 2rem 2rem;
            box-shadow: var(--shadow-md);
        }
    </style>
</head>

<body>
    <div class="login-shell">
        <div class="login-brand">
            <a href="/" class="login-logo">
                <img src="{{ asset('images/roodle.png') }}" alt="" width="48" height="48">
                <span class="login-logo__name">oodle</span>
            </a>
            <p class="login-brand__sub">Образовательная платформа</p>
        </div>
        <div class="login-card">
            {{ $slot }}
        </div>
    </div>
</body>

</html>

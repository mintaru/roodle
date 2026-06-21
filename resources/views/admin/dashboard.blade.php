<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    @livewireStyles

    <style>
        .admin-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }

        .admin-header {
            margin-bottom: 3rem;
        }

        .admin-header h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--color-text-primary);
            margin-bottom: 0.5rem;
        }

        .admin-header p {
            font-size: 1rem;
            color: var(--color-text-secondary);
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .admin-card {
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--r-lg);
            text-decoration: none;
            color: inherit;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, currentColor, currentColor);
            opacity: 0;
            transition: opacity var(--transition);
        }

        .admin-card:hover {
            border-color: var(--color-border-2);
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .admin-card.accent {
            color: var(--teal-500);
        }

        .admin-card.accent:hover::before {
            opacity: 1;
        }

        .admin-card.sky {
            color: var(--sky-500);
        }

        .admin-card.sky:hover::before {
            opacity: 1;
        }

        .admin-card.green {
            color: var(--green-500);
        }

        .admin-card.green:hover::before {
            opacity: 1;
        }

        .admin-card.red {
            color: var(--red-500);
        }

        .admin-card.red:hover::before {
            opacity: 1;
        }

        .admin-card-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--color-text-primary);
        }

        .admin-card-description {
            font-size: 0.875rem;
            color: var(--color-text-secondary);
            flex-grow: 1;
        }
    </style>
</head>

<body>
    @include('components.menu')

    <div class="admin-container">


        <div class="admin-header">
            <h1>Админ-панель</h1>
            <p>Добро пожаловать, <span style="font-weight: 600;">{{ auth()->user()->name }}</span>!</p>
        </div>

        <div class="admin-grid">
            <a href="{{ route('admin.courses.index') }}" class="admin-card accent">
                <h2 class="admin-card-title"> Список курсов</h2>
                <p class="admin-card-description">Управляйте всеми курсами платформы</p>
            </a>

            <a href="{{ route('admin.lectures.index') }}" class="admin-card sky">
                <h2 class="admin-card-title"> Список лекций</h2>
                <p class="admin-card-description">Управляйте всеми лекциями</p>
            </a>

            <a href="{{ route('admin.tests.index') }}" class="admin-card green">
                <h2 class="admin-card-title"> Список тестов</h2>
                <p class="admin-card-description">Управляйте всеми тестами</p>
            </a>

            <a href="{{ route('admin.question-bank.index') }}" class="admin-card accent">
                <h2 class="admin-card-title"> Банк вопросов</h2>
                <p class="admin-card-description">Управляйте всеми вопросами</p>
            </a>

            <a href="{{ route('admin.groups.index') }}" class="admin-card sky">
                <h2 class="admin-card-title"> Список групп</h2>
                <p class="admin-card-description">Управляйте группами обучающихся</p>
            </a>

            <a href="{{ route('admin.users.index') }}" class="admin-card accent">
                <h2 class="admin-card-title"> Список пользователей</h2>
                <p class="admin-card-description">Управляйте всеми пользователями</p>
            </a>






        </div>
    </div>

    @livewireScripts
</body>

</html>

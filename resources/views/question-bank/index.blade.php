<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Банк вопросов</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/roodle-tokens.css') }}">
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>

<body>
    @include('components.menu')

    <style>
        .admin-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 2rem;
        }

        .admin-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .admin-card h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
        }

        .success-message {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #2e7d32;
            font-size: 14px;
        }

        .btn-primary {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 10px 18px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .search-box {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .search-box label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .search-box select,
        .search-box input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Manrope', sans-serif;
        }

        .search-box select:focus,
        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .groups-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .groups-table thead {
            background: #f5f5f5;
            border-bottom: 2px solid #e0e0e0;
        }

        .groups-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }

        .groups-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .groups-table tbody tr:hover {
            background: #fafafa;
        }

        .table-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .table-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .btn-danger {
            color: #e74c3c;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
            font-family: 'Manrope', sans-serif;
        }

        .btn-danger:hover {
            text-decoration: underline;
        }

        @media (max-width: 1024px) {
            .admin-container {
                padding: 1rem;
            }

            .admin-card {
                padding: 1rem;
            }
        }
    </style>

    <div class="admin-container">
        <div class="admin-card">

            <h1>Банк вопросов</h1>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            @livewire('admin.question-search')

        </div>
    </div>

</body>
</html>

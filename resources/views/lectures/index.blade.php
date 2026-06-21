<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лекции</title>
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

        .search-box {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .search-box form {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 1rem;
            align-items: flex-end;
        }

        .search-box form > div {
            flex: 1;
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

        .btn-search,
        .btn-reset {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            font-family: 'Manrope', sans-serif;
            transition: all 0.2s ease;
        }

        .btn-search {
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-reset {
            background: #e0e0e0;
            color: #333;
        }

        .btn-reset:hover {
            background: #d0d0d0;
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

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-box {
            background: #fff;
            border-radius: 28px;
            padding: 2rem;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, .2);
        }

        .modal-icon {
            width: 52px;
            height: 52px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        .modal-icon--delete {
            background: #ffebee;
        }

        .modal-box h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1e2530;
            margin: 0 0 .5rem;
        }

        .modal-box p {
            font-size: 14px;
            color: #6b7a89;
            line-height: 1.6;
            margin: 0 0 1.5rem;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
        }

        .modal-btn {
            flex: 1;
            padding: 11px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Manrope', sans-serif;
            border: none;
            cursor: pointer;
            transition: .2s ease;
        }

        .modal-btn--cancel {
            background: #f0f3f5;
            color: #4a5668;
        }

        .modal-btn--cancel:hover {
            background: #e2e8ed;
        }

        .modal-btn--confirm-danger {
            background: #e74c3c;
            color: #fff;
        }

        .modal-btn--confirm-danger:hover {
            background: #c62828;
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

            <h1>Лекции</h1>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            @livewire('admin.lecture-search')

        </div>
    </div>

</body>
</html>

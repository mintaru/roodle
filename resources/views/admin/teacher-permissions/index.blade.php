<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Управление правами учителей</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-8">

    <div class="max-w-7xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                ← Вернуться в админку
            </a>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Управление правами учителей</h1>
            <p class="text-gray-600">Настройте какой учитель может редактировать какие курсы</p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Teachers Column -->
            <div class="bg-white rounded-lg shadow">
                <div class="bg-blue-600 text-white p-6 rounded-t-lg">
                    <h2 class="text-2xl font-bold">👨‍🏫 Учителя</h2>
                    <p class="text-sm opacity-90">Выберите учителя для управления его доступом</p>
                </div>
                <div class="p-6">
                    @if ($teachers->count() > 0)
                        <div class="space-y-2">
                            @foreach ($teachers as $teacher)
                                <div class="p-4 border border-gray-300 rounded hover:bg-blue-50 transition cursor-pointer"
                                    onclick="selectTeacher({{ $teacher->id }}, '{{ addslashes($teacher->name) }}')">
                                    <div class="font-semibold text-gray-800">{{ $teacher->name }}</div>
                                    <div class="text-sm text-gray-600">
                                        @if ($teacher->coursePermissions->count() > 0)
                                            Доступ к {{ $teacher->coursePermissions->count() }}
                                            {{ $teacher->coursePermissions->count() == 1 ? 'курсу' : 'курсам' }}
                                        @else
                                            <span class="text-gray-500">Нет доступа к курсам</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Учителей не найдено</p>
                    @endif
                </div>
            </div>

            <!-- Courses Column -->
            <div class="bg-white rounded-lg shadow">
                <div class="bg-purple-600 text-white p-6 rounded-t-lg">
                    <h2 class="text-2xl font-bold">📚 Курсы</h2>
                    <p class="text-sm opacity-90">Выберите курс для настройки доступа</p>
                </div>
                <div class="p-6">
                    @php
                        $courses = \App\Models\Course::active()->orderBy('title')->get();
                    @endphp
                    @if ($courses->count() > 0)
                        <div class="space-y-2">
                            @foreach ($courses as $course)
                                <a href="{{ route('admin.teacher-permissions.edit-course', $course) }}"
                                    class="block p-4 border border-gray-300 rounded hover:bg-purple-50 transition">
                                    <div class="font-semibold text-gray-800">{{ $course->title }}</div>
                                    <div class="text-sm text-gray-600">
                                        @php
                                            $permittedCount = $course->permittedTeachers()->count();
                                        @endphp
                                        @if ($permittedCount > 0)
                                            Доступ для {{ $permittedCount }}
                                            {{ $permittedCount == 1 ? 'учителя' : 'учителей' }}
                                        @else
                                            <span class="text-gray-500">Доступа нет</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Активных курсов не найдено</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Teacher Details Modal-like Section -->
        <div id="teacherDetails" style="display: none;" class="mt-6 bg-white rounded-lg shadow p-6">
            <div class="mb-6">
                <button onclick="closeTeacherDetails()"
                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                    ← Назад
                </button>
            </div>
            <div id="teacherDetailsContent"></div>
        </div>
    </div>

    <script>
        function selectTeacher(teacherId, teacherName) {
            // Redirect to edit page
            window.location.href = `/admin/teacher-permissions/${teacherId}/edit-teacher`;
        }

        function closeTeacherDetails() {
            document.getElementById('teacherDetails').style.display = 'none';
        }
    </script>

</body>

</html>

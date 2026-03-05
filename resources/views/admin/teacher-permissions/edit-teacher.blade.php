<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Управление доступом: {{ $user->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 p-8">

    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('admin.teacher-permissions.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 mb-4">
                ← Вернуться
            </a>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $user->name }}</h1>
            <p class="text-gray-600">Управление доступом к курсам</p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('admin.teacher-permissions.update-teacher', $user) }}"
            class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <h2 class="text-2xl font-bold text-gray-800 mb-6">Выберите курсы и установите права доступа</h2>

            @if ($allCourses->count() > 0)
                <div class="space-y-4 mb-8">
                    @foreach ($allCourses as $course)
                        @php
                            $isPermitted = in_array($course->id, $permittedCourseIds);
                            $permission = $isPermitted
                                ? $user->coursePermissions->firstWhere('course_id', $course->id)
                                : null;
                        @endphp

                        <div class="border border-gray-300 rounded-lg p-6 hover:bg-blue-50 transition">
                            <!-- Course Selection -->
                            <div class="flex items-start gap-4 mb-4">
                                <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                    {{ $isPermitted ? 'checked' : '' }} class="mt-1 w-5 h-5"
                                    onchange="toggleCoursePermissions(this, {{ $course->id }})">
                                <div class="flex-1">
                                    <label class="text-lg font-semibold text-gray-800 cursor-pointer">
                                        {{ $course->title }}
                                    </label>
                                    <p class="text-sm text-gray-600">
                                        @if ($course->author)
                                            Автор: {{ $course->author->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <!-- Permissions -->
                            <div id="permissions-{{ $course->id }}"
                                class="ml-9 grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gray-100 rounded"
                                style="{{ $isPermitted ? '' : 'display: none;' }}">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[{{ $course->id }}][can_edit]" value="1"
                                        {{ $isPermitted && $permission?->can_edit ? 'checked' : '' }}
                                        class="w-4 h-4" onchange="validatePermissions({{ $course->id }})">
                                    <span class="text-sm">✏️ Редактирование курса</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[{{ $course->id }}][can_delete]" value="1"
                                        {{ $isPermitted && $permission?->can_delete ? 'checked' : '' }}
                                        class="w-4 h-4" onchange="validatePermissions({{ $course->id }})">
                                    <span class="text-sm">🗑️ Удаление курса</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="permissions[{{ $course->id }}][can_manage_students]" value="1"
                                        {{ $isPermitted && $permission?->can_manage_students ? 'checked' : '' }}
                                        class="w-4 h-4" onchange="validatePermissions({{ $course->id }})">
                                    <span class="text-sm">👥 Управление студентами</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Активных курсов не найдено</p>
            @endif

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-6 border-t">
                <button type="submit"
                    class="px-6 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">
                    Сохранить изменения
                </button>
                <a href="{{ route('admin.teacher-permissions.index') }}"
                    class="px-6 py-3 bg-gray-600 text-white rounded hover:bg-gray-700 font-semibold">
                    Отмена
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleCoursePermissions(checkbox, courseId) {
            const permissionsDiv = document.getElementById(`permissions-${courseId}`);
            if (permissionsDiv) {
                permissionsDiv.style.display = checkbox.checked ? 'grid' : 'none';
            }
        }

        function validatePermissions(courseId) {
            // You can add validation logic here if needed
            // For example, ensure at least one permission is selected if course is selected
        }
    </script>

</body>

</html>

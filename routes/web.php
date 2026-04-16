<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController; // Импортируем наш созданный контроллер
use App\Models\Test; // Импортируем модель Test
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CourseController;
use App\Models\Lecture;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\CourseSectionController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AssignmentSubmissionController;
use App\Http\Controllers\Admin\GroupUserController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TeacherCoursePermissionController;
use App\Http\Controllers\QuestionBankController;
use App\Http\Controllers\Admin\TestManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\GradeReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




Route::middleware('auth')->group(function () {
    Route::get('/profile-edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::get('/', [CourseController::class, 'index'])->middleware('auth')->name("home");
Route::get('/courses/archived', [CourseController::class, 'archived'])->name('courses.archived');

Route::get('/courses/create', [CourseController::class, 'create'])->middleware('auth')->name('courses.create');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');


// Маршрут для настройки базы данных

// --- Маршруты для управления тестами (используя TestController) ---

// Главная страница: отображает список всех тестов
// Перенаправлен на метод index() в TestController

// Страница создания нового теста
// Перенаправлен на метод create() в TestController
Route::get('/courses/{course}/tests/create', [TestController::class, 'create'])->middleware('auth')->name('tests.create');

// Обработка формы создания теста (POST-запрос)
// Перенаправлен на метод store() в TestController
Route::post('/courses/{course}/tests', [TestController::class, 'store'])->name('tests.store');

Route::get('/tests/{test}/view', [TestController::class, 'view'])->middleware('auth')->name('tests.view');

// Страница просмотра одного теста (включая вопросы)
// Eloquent автоматически найдет тест по ID благодаря Route Model Binding
// Перенаправлен на метод show() в TestController
Route::get('/tests/{test}', [TestController::class, 'show'])->middleware('auth')->name('tests.show');

// Обработка добавления нового вопроса к тесту (POST-запрос)
// Перенаправлен на метод storeQuestion() в TestController
Route::post('/tests/{test}/questions', [TestController::class, 'storeQuestion'])->name('tests.store_question');

// Обновление разбиения вопросов по страницам внутри теста
Route::put('/tests/{test}/layout', [TestController::class, 'updateLayout'])
    ->middleware('auth')
    ->name('tests.update_layout');

// Удаление вопроса из теста (DELETE-запрос)
Route::delete('/tests/{test}/questions/{question}', [TestController::class, 'removeQuestion'])->name('tests.removeQuestion');

// Просмотр результатов теста (обзор тестирования)
Route::get('/tests/{test}/results', [TestController::class, 'results'])->middleware('auth')->name('tests.results');

// Просмотр деталей попытки ученика
Route::get('/test-attempts/{attempt}/details', [TestController::class, 'viewAttemptDetails'])->middleware('auth')->name('test-attempts.details');

// Ручная проверка развёрнутых ответов в попытке
Route::post('/test-attempts/{attempt}/grade-rich-text', [TestController::class, 'gradeRichTextAnswers'])
    ->middleware('auth')
    ->name('test-attempts.grade-rich-text');

// Выдача дополнительных попыток для ученика (по тесту и пользователю)
Route::post('/tests/{test}/users/{user}/grant-attempts', [TestController::class, 'grantExtraAttempts'])
    ->middleware('auth')
    ->name('test-attempts.grant-attempts');

// Эти маршруты показывают, как можно их вынести в контроллер для единообразия.

// Страница для начала прохождения теста
Route::get('/tests/{test}/attempt', [TestController::class, 'attempt'])
    ->name('tests.attempt');

Route::get('/tests/{test}/attempt/{questionIndex?}', [TestController::class, 'attemptPage'])
    ->middleware('auth')
    ->name('tests.attempt.page');

// Обработка сохранения временного ответа (AJAX)
Route::post('/tests/{test}/save-answer', [TestController::class, 'saveAnswer'])
    ->name('tests.save_answer');

// Синхронизация таймера между устройствами
Route::get('/tests/{test}/timer-sync', [TestController::class, 'timerSync'])
    ->middleware('auth')
    ->name('tests.timer_sync');

// Обработка отправки ответов и подсчета результатов
Route::post('/tests/{test}/result', [TestController::class, 'result'])
    ->middleware('auth')
    ->name('tests.result');

Route::get('/courses/{course}/lectures/create', [LectureController::class, 'create'])->middleware('auth')->name('lectures.create');
Route::post('/courses/{course}/lectures', [LectureController::class, 'store'])->middleware('auth')->name('lectures.store');
Route::post('/lectures/upload-attachment', [LectureController::class, 'uploadAttachment'])->middleware('auth')->name('lectures.upload-attachment');


// Новый маршрут для просмотра лекции через курс
Route::get('/courses/{course}/lectures/{lecture}', [LectureController::class, 'show'])->middleware('auth')->name('lectures.show');

// Маршруты для материалов
Route::get('/courses/{course}/materials/create', [MaterialController::class, 'create'])->middleware('auth')->name('materials.create');
Route::post('/courses/{course}/materials', [MaterialController::class, 'store'])->middleware('auth')->name('materials.store');
Route::get('/courses/{course}/materials/{material}/download', [MaterialController::class, 'download'])->middleware('auth')->name('materials.download');
Route::delete('/courses/{course}/materials/{material}', [MaterialController::class, 'destroy'])->middleware('auth')->name('materials.destroy');
Route::patch('/courses/{course}/materials/{material}/toggle-status', [MaterialController::class, 'toggleStatus'])->middleware('auth')->name('materials.toggle-status');

// Маршруты для заданий
Route::get('/courses/{course}/assignments/create', [AssignmentController::class, 'create'])->middleware('auth')->name('assignments.create');
Route::post('/courses/{course}/assignments', [AssignmentController::class, 'store'])->middleware('auth')->name('assignments.store');
Route::get('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'show'])->middleware('auth')->name('assignments.show');
Route::get('/courses/{course}/assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->middleware('auth')->name('assignments.edit');
Route::put('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'update'])->middleware('auth')->name('assignments.update');
Route::delete('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'destroy'])->middleware('auth')->name('assignments.destroy');
Route::delete('/courses/{course}/assignments/{assignment}/files/{file}', [AssignmentController::class, 'deleteFile'])->middleware('auth')->name('assignments.delete-file');
Route::get('/courses/{course}/assignments/{assignment}/files/{file}/download', [AssignmentController::class, 'downloadFile'])->middleware('auth')->name('assignments.download-file');
Route::post('/courses/{course}/assignments/{assignment}/move', [AssignmentController::class, 'move'])->middleware('auth')->name('assignments.move');

// Маршруты для ответов на задания
Route::get('/courses/{course}/assignments/{assignment}/view', [AssignmentSubmissionController::class, 'view'])->middleware('auth')->name('assignments.view');
Route::post('/courses/{course}/assignments/{assignment}/submit', [AssignmentSubmissionController::class, 'submit'])->middleware('auth')->name('assignments.submit');
Route::post('/courses/{course}/assignments/{assignment}/submissions/{submission}/grade', [AssignmentSubmissionController::class, 'grade'])->middleware('auth')->name('assignments.grade');
Route::get('/courses/{course}/assignments/{assignment}/submissions/{submission}/files/{file}/download', [AssignmentSubmissionController::class, 'downloadSubmissionFile'])->middleware('auth')->name('assignments.download-submission-file');
Route::delete('/courses/{course}/assignments/{assignment}/submissions/{submission}/files/{file}', [AssignmentSubmissionController::class, 'deleteSubmissionFile'])->middleware('auth')->name('assignments.delete-submission-file');

// Маршруты для оценок
Route::get('/courses/{course}/grades', [GradeReportController::class, 'courseGrades'])->middleware('auth')->name('courses.grades');

// Подключение маршрутов аутентификации Laravel
Route::post('/courses', [CourseController::class, 'store'])->middleware('auth')->name('courses.store');


Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->middleware('auth')->name('courses.edit'); // форма редактирования
Route::put('/courses/{course}', [CourseController::class, 'update'])->middleware('auth')->name('courses.update');
Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->middleware('auth')->name('courses.destroy');

Route::post('/tests/{test}/add-from-bank', [TestController::class, 'addFromBank'])->name('tests.add_from_bank');

Route::middleware(['auth', 'role:admin|teacher'])->group(function () {
    //для групп
    Route::get('/admin/groups', [GroupUserController::class, 'index'])->name('admin.groups.index');
    Route::get('/admin/groups/create', [GroupUserController::class, 'create'])->name('admin.groups.create');
    Route::post('/admin/groups', [GroupUserController::class, 'store'])->name('admin.groups.store');
    Route::get('/admin/groups/{group}', [GroupUserController::class, 'show'])->name('admin.groups.show');
    Route::post('/admin/groups/{group}/assign', [GroupUserController::class, 'assign'])->name('admin.groups.assign');
    Route::delete('/admin/groups/{group}', [GroupUserController::class, 'destroy'])->name('admin.groups.destroy');
    Route::put('/admin/groups/{group}', [GroupUserController::class, 'update'])->name('admin.groups.update');
    Route::delete('/admin/groups/{group}/remove/{user}', [GroupUserController::class, 'remove'])->name('admin.groups.remove');

    //для юзеров
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

    //для прав доступа преподавателей
    Route::get('/admin/teacher-permissions', [TeacherCoursePermissionController::class, 'index'])->name('admin.teacher-permissions.index');
    Route::get('/admin/teacher-permissions/{user}/edit-teacher', [TeacherCoursePermissionController::class, 'editTeacher'])->name('admin.teacher-permissions.edit-teacher');
    Route::put('/admin/teacher-permissions/{user}/update-teacher', [TeacherCoursePermissionController::class, 'updateTeacher'])->name('admin.teacher-permissions.update-teacher');
    Route::get('/admin/teacher-permissions/{course}/edit-course', [TeacherCoursePermissionController::class, 'editCourse'])->name('admin.teacher-permissions.edit-course');
    Route::put('/admin/teacher-permissions/{course}/update-course', [TeacherCoursePermissionController::class, 'updateCourse'])->name('admin.teacher-permissions.update-course');

    //для курсов
    Route::get('/admin/courses', [CourseController::class, 'index'])->name('admin.courses.index');
    Route::delete('/admin/courses/{course}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');

    //АРХИВИРОВАНИЕ
    Route::patch('/courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');
    Route::patch('/courses/{course}/restore', [CourseController::class, 'restore'])->name('courses.restore');


    // Маршруты для управления лекциями
    Route::get('/lectures', [LectureController::class, 'index'])->name('admin.lectures.index');
    Route::get('/lectures/{lecture}/edit', [LectureController::class, 'edit'])->name('admin.lectures.edit');
    Route::put('/lectures/{lecture}', [LectureController::class, 'update'])->name('admin.lectures.update');
    Route::delete('/lectures/{lecture}', [LectureController::class, 'destroy'])->name('admin.lectures.destroy');
    Route::patch('/lectures/{lecture}/archive', [LectureController::class, 'archive'])->name('admin.lectures.archive');
    Route::patch('/lectures/{lecture}/restore', [LectureController::class, 'restore'])->name('admin.lectures.restore');

    // Маршруты для управления материалами
    Route::patch('/materials/{material}/archive', [MaterialController::class, 'archive'])->name('admin.materials.archive');
    Route::patch('/materials/{material}/restore', [MaterialController::class, 'restore'])->name('admin.materials.restore');

    // Секции курсов и элементы секций
    Route::post('/courses/{course}/sections', [CourseSectionController::class, 'store'])->name('courses.sections.store');
    Route::put('/courses/{course}/sections/{section}', [CourseSectionController::class, 'update'])->name('courses.sections.update');
    Route::delete('/courses/{course}/sections/{section}', [CourseSectionController::class, 'destroy'])->name('courses.sections.destroy');
    Route::post('/courses/{course}/sections/{section}/move', [CourseSectionController::class, 'move'])->name('courses.sections.move');
    Route::post('/courses/{course}/sections/{section}/items', [CourseSectionController::class, 'attachItem'])->name('courses.sections.items.attach');
    Route::post('/courses/{course}/sections/{section}/items/{item}/move', [CourseSectionController::class, 'moveItem'])->name('courses.sections.items.move');
    Route::delete('/courses/{course}/sections/{section}/items/{item}', [CourseSectionController::class, 'detachItem'])->name('courses.sections.items.detach');
    
    // Маршруты для банка вопросов
    Route::get('/question-bank', [QuestionBankController::class, 'index'])->name('admin.question-bank.index');
    Route::get('/question-bank/{question}/edit', [QuestionBankController::class, 'edit'])->name('admin.question-bank.edit');
    Route::put('/question-bank/{question}', [QuestionBankController::class, 'update'])->name('admin.question-bank.update');
    Route::delete('/question-bank/{question}', [QuestionBankController::class, 'destroy'])->name('admin.question-bank.destroy');
    
    // Маршруты для управления тестами
    Route::get('/tests', [TestManagementController::class, 'index'])->name('admin.tests.index');
    Route::get('/tests/{test}/edit', [TestManagementController::class, 'edit'])->name('admin.tests.edit');
    Route::put('/tests/{test}', [TestManagementController::class, 'update'])->name('admin.tests.update');
    Route::delete('/tests/{test}', [TestManagementController::class, 'destroy'])->name('admin.tests.destroy');
    Route::patch('/tests/{test}/archive', [TestManagementController::class, 'archive'])->name('admin.tests.archive');
    Route::patch('/tests/{test}/restore', [TestManagementController::class, 'restore'])->name('admin.tests.restore');
    Route::get('/tests/{id}/attempts', [TestController::class, 'attempts'])->name('admin.tests.attempts');

    Route::post('/questions/upload', [QuestionController::class, 'upload'])->name('questions.upload');

});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Маршруты для отчётов
    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('admin.reports.index');
    Route::get('/reports/user-activity', [ReportController::class, 'userActivity'])->name('admin.reports.user-activity');
    Route::get('/reports/groups', [ReportController::class, 'groupsReport'])->name('admin.reports.groups');
    Route::get('/reports/courses', [ReportController::class, 'coursesReport'])->name('admin.reports.courses');
    



});

Route::get('/profile-update', function () {
    return view('profile.edit');
})->middleware(['auth'])->name('profile.update-profile-information-form');


require __DIR__.'/auth.php';

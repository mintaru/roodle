<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use App\Models\Test;
use App\Models\Assignment;
use App\Models\TestAttempt;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;

class GradeReportController extends Controller
{
    /**
     * Отображение таблицы оценок по курсу
     */
    public function courseGrades(Request $request, Course $course)
    {
        // Проверка прав доступа
        if (!auth()->user()->hasAnyRole('teacher', 'admin')) {
            abort(403);
        }

        // Если это учитель, проверяем, что это его курс
        if (auth()->user()->hasRole('teacher') && $course->user_id !== auth()->id()) {
            abort(403);
        }

        // Получаем группы, связанные с этим курсом
        $groups = $course->groups()->get();

        // Выбранная группа
        $selectedGroupId = $request->get('group_id', null);
        $selectedGroup = null;
        $allGroupsSelected = false;

        if ($selectedGroupId === 'all') {
            // Выбраны все группы
            $allGroupsSelected = true;
        } elseif ($selectedGroupId) {
            $selectedGroup = $groups->find($selectedGroupId);
            if (!$selectedGroup) {
                abort(404);
            }
        } elseif ($groups->count() > 0) {
            $selectedGroup = $groups->first();
            $selectedGroupId = $selectedGroup->id;
        }

        // Режимы фильтрации
        $filterMode = 'group'; // по умолчанию показываем всех студентов группы
        $selectedStudent = null;
        $selectedItem = null;
        $selectedItemType = null;

        // Получаем студентов
        $students = collect();
        if ($allGroupsSelected) {
            // Получаем студентов из всех групп
            foreach ($groups as $group) {
                $groupStudents = $group->users()
                    ->whereHas('roles', function ($q) {
                        $q->where('name', 'student');
                    })
                    ->get();
                $students = $students->merge($groupStudents);
            }
            $students = $students->unique('id');
        } elseif ($selectedGroup) {
            $students = $selectedGroup->users()
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'student');
                })
                ->get();
        }

        // Проверяем фильтр по студенту
        $studentId = $request->get('student_id', null);
        if ($studentId && ($selectedGroup || $allGroupsSelected)) {
            $selectedStudent = $students->find($studentId);
            if ($selectedStudent) {
                $filterMode = 'student';
            }
        }

        // Проверяем фильтр по тесту или заданию
        $itemType = $request->get('item_type', null);
        $itemId = $request->get('item_id', null);
        if ($itemType && $itemId && ($selectedGroup || $allGroupsSelected)) {
            if ($itemType === 'test') {
                $selectedItem = $course->tests()->find($itemId);
                if ($selectedItem) {
                    $selectedItemType = 'test';
                    $filterMode = 'item';
                }
            } elseif ($itemType === 'assignment') {
                $selectedItem = $course->assignments()->find($itemId);
                if ($selectedItem) {
                    $selectedItemType = 'assignment';
                    $filterMode = 'item';
                }
            }
        }

        // Получаем все тесты и задания курса
        $tests = $course->tests()->where('status', 'active')->get();
        $assignments = $course->assignments()->where('status', 'active')->get();

        // Собираем данные в зависимости от режима фильтрации
        $gradesData = [];

        if ($filterMode === 'student' && $selectedStudent) {
            // Режим просмотра оценок одного студента
            $gradesData = $this->getStudentGrades($selectedStudent, $tests, $assignments);
        } elseif ($filterMode === 'item' && $selectedItem) {
            // Режим просмотра оценок по одному тесту/заданию
            $gradesData = $this->getItemGrades($selectedItem, $selectedItemType, $students);
        } else {
            // Режим группы - все студенты со всеми оценками
            foreach ($students as $student) {
                $gradesData[] = $this->buildStudentGradesArray($student, $tests, $assignments);
            }
        }

        return view('courses.grades-report', [
            'course' => $course,
            'groups' => $groups,
            'selectedGroup' => $selectedGroup,
            'selectedGroupId' => $selectedGroupId,
            'allGroupsSelected' => $allGroupsSelected,
            'selectedStudent' => $selectedStudent,
            'selectedItem' => $selectedItem,
            'selectedItemType' => $selectedItemType,
            'filterMode' => $filterMode,
            'students' => $students,
            'tests' => $tests,
            'assignments' => $assignments,
            'gradesData' => $gradesData,
        ]);
    }

    /**
     * Получение оценок одного студента
     */
    private function getStudentGrades($student, $tests, $assignments)
    {
        return [$this->buildStudentGradesArray($student, $tests, $assignments)];
    }

    /**
     * Получение оценок по одному тесту или заданию
     */
    private function getItemGrades($item, $itemType, $students)
    {
        $gradesData = [];

        foreach ($students as $student) {
            if ($itemType === 'test') {
                $lastAttempt = TestAttempt::where('user_id', $student->id)
                    ->where('test_id', $item->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $score = $lastAttempt ? ($lastAttempt->score ?? 0) : '-';
                $maxScore = 100;
            } else {
                $submission = AssignmentSubmission::where('user_id', $student->id)
                    ->where('assignment_id', $item->id)
                    ->first();

                $score = ($submission && $submission->isGraded()) ? ($submission->score ?? 0) : '-';
                $maxScore = $item->max_score ?? 100;
            }

            $gradesData[] = [
                'student' => $student,
                'item_score' => $score,
                'item_max_score' => $maxScore,
            ];
        }

        return $gradesData;
    }

    /**
     * Построение массива оценок студента
     */
    private function buildStudentGradesArray($student, $tests, $assignments)
    {
        $studentGrades = [
            'student' => $student,
            'tests' => [],
            'assignments' => [],
            'totalTestScore' => 0,
            'totalAssignmentScore' => 0,
        ];

        // Собираем оценки по тестам
        foreach ($tests as $test) {
            $lastAttempt = TestAttempt::where('user_id', $student->id)
                ->where('test_id', $test->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($lastAttempt) {
                $studentGrades['tests'][$test->id] = [
                    'name' => $test->title,
                    'score' => $lastAttempt->score ?? 0,
                    'max_score' => 100,
                ];
                $studentGrades['totalTestScore'] += $lastAttempt->score ?? 0;
            } else {
                $studentGrades['tests'][$test->id] = [
                    'name' => $test->title,
                    'score' => '-',
                    'max_score' => 100,
                ];
            }
        }

        // Собираем оценки по заданиям
        foreach ($assignments as $assignment) {
            $submission = AssignmentSubmission::where('user_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->first();

            if ($submission && $submission->isGraded()) {
                $studentGrades['assignments'][$assignment->id] = [
                    'name' => $assignment->title,
                    'score' => $submission->score ?? 0,
                    'max_score' => $assignment->max_score ?? 100,
                ];
                $studentGrades['totalAssignmentScore'] += $submission->score ?? 0;
            } else {
                $studentGrades['assignments'][$assignment->id] = [
                    'name' => $assignment->title,
                    'score' => '-',
                    'max_score' => $assignment->max_score ?? 100,
                ];
            }
        }

        return $studentGrades;
    }
}

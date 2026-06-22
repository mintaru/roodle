<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Group;
use App\Models\Lecture;
use App\Models\Option;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Создаём роли и права
        $adminRole   = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $studentRole = Role::firstOrCreate(['name' => 'student']);
    
        // Создаём права
        Permission::firstOrCreate(['name' => 'edit courses']);
        Permission::firstOrCreate(['name' => 'view courses']);
    
        // Раздаём права ролям
        $adminRole->givePermissionTo(['edit courses', 'view courses']);
        $teacherRole->givePermissionTo(['edit courses', 'view courses']);
        $studentRole->givePermissionTo(['view courses']);

        // 1️⃣ Создаём администратора
        $admin = User::factory()->create([
            'username' => 'admin',
            'name' => 'Administrator',
        ])->assignRole('admin');

        // 2️⃣ Создаём преподавателей
        $teacher = User::factory()->create([
            'username' => 'teacher',
            'name' => 'Teacher',
        ])->assignRole('teacher');

        $teachers = User::factory(3)->create()->each(function ($user) use ($teacherRole) {
            $user->assignRole('teacher');
        });

        // 3️⃣ Создаём студентов
        $student = User::factory()->create([
            'username' => 'student',
            'name' => 'Student',
        ])->assignRole('student');

        $students = User::factory(9)->create()->each(function ($user) use ($studentRole) {
            $user->assignRole('student');
        });

        // 4️⃣ Создаём группы и распределяем студентов
        $groups = [
            Group::factory()->create([
                'name' => 'ПИ-101',
            ]),
            Group::factory()->create([
                'name' => 'ПИ-102',
            ]),
            Group::factory()->create([
                'name' => 'ПИ-201',
            ]),
        ];

        // Распределяем студентов по группам
        $groups[0]->users()->attach(
            collect([$student->id])->merge($students->slice(0, 3)->pluck('id'))->toArray()
        );
        $groups[1]->users()->attach($students->slice(3, 3)->pluck('id'));
        $groups[2]->users()->attach($students->slice(6, 3)->pluck('id'));

        // 5️⃣ Создаём курсы с лекциями и тестами
        $courseData = [
            [
                'title' => 'Введение в программирование',
                'teacher' => $teacher,
                'groups' => [$groups[0], $groups[1]],
            ],
            [
                'title' => 'Веб-разработка. Основы',
                'teacher' => $teachers[0],
                'groups' => [$groups[0]],
            ],
            [
                'title' => 'Проектирование баз данных',
                'teacher' => $teachers[1],
                'groups' => [$groups[1], $groups[2]],
            ],
            [
                'title' => 'Продвинутые концепции ООП',
                'teacher' => $teachers[2],
                'groups' => [$groups[2]],
            ],
        ];

        foreach ($courseData as $data) {
            $course = Course::factory()->create([
                'title' => $data['title'],
                'user_id' => $data['teacher']->id,
                'status' => Course::STATUS_ACTIVE,
            ]);

            // Добавляем группы к курсу
            $course->groups()->attach(
                collect($data['groups'])->pluck('id')->toArray(),
                [
                    'period_start' => now(),
                    'period_end' => now()->addMonths(3),
                ]
            );

            // 6️⃣ Создаём лекции для курса
            for ($i = 1; $i <= 3; $i++) {
                Lecture::factory()->create([
                    'course_id' => $course->id,
                    'title' => "Лекция $i: " . fake()->words(3, true),
                    'status' => Lecture::STATUS_ACTIVE,
                ]);
            }

            // 7️⃣ Создаём тесты для курса
            for ($i = 1; $i <= 2; $i++) {
                $test = Test::factory()->create([
                    'course_id' => $course->id,
                    'is_global' => false,
                    'title' => "Тест $i: " . fake()->words(3, true),
                    'status' => Test::STATUS_ACTIVE,
                ]);

                // 8️⃣ Создаём вопросы с вариантами ответов
                for ($j = 1; $j <= 5; $j++) {
                    $question = Question::factory()->singleChoice()->create([
                        'question_text' => fake()->sentence() . '?',
                    ]);

                    // Создаём варианты ответов (1 правильный и 3 неправильных)
                    Option::factory()->correct()->create([
                        'question_id' => $question->id,
                        'option_text' => 'Correct answer',
                    ]);

                    for ($k = 0; $k < 3; $k++) {
                        Option::factory()->incorrect()->create([
                            'question_id' => $question->id,
                        ]);
                    }

                    // Добавляем вопрос в тест
                    $test->questions()->attach($question->id, [
                        'question_order' => $j,
                    ]);
                }
            }
        }

        // 9️⃣ Создаём тесты в общем банке
        $globalTestTitles = [
            'Основы алгоритмизации',
            'SQL для начинающих',
            'Английский для IT-специалистов',
        ];

        foreach ($globalTestTitles as $title) {
            $test = Test::factory()->globalBank()->create([
                'title' => $title,
                'status' => Test::STATUS_ACTIVE,
            ]);

            for ($j = 1; $j <= 5; $j++) {
                $question = Question::factory()->singleChoice()->create([
                    'question_text' => fake()->sentence() . '?',
                ]);

                Option::factory()->correct()->create([
                    'question_id' => $question->id,
                    'option_text' => 'Correct answer',
                ]);

                for ($k = 0; $k < 3; $k++) {
                    Option::factory()->incorrect()->create([
                        'question_id' => $question->id,
                    ]);
                }

                $test->questions()->attach($question->id, [
                    'question_order' => $j,
                ]);
            }
        }

        // Сообщаем об успехе
        $this->command->info('✅ База данных успешно заполнена!');
        $this->command->info('📊 Создано:');
        $this->command->info('   • 1 Администратор: admin / password');
        $this->command->info('   • 1 Преподаватель: teacher / password');
        $this->command->info('   • 3 Преподавателя с ответственностью за курсы');
        $this->command->info('   • 1 Студент: student / password');
        $this->command->info('   • 9 Студентов, распределённых по 3 группам');
        $this->command->info('   • 4 Активных курса с лекциями и тестами');
        $this->command->info('   • 8 Тестов по 5 вопросов каждый (40 вариантов ответов)');
        $this->command->info('   • 3 Теста в общем банке (доступны всем преподавателям)');
    }
}

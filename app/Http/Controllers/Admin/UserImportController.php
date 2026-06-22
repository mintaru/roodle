<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserImportController extends Controller
{
    public function import(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'mode' => 'required|in:auto,manual',
            'col_name' => 'nullable|integer|min:1',
            'col_username' => 'nullable|integer|min:1',
            'col_password' => 'nullable|integer|min:1',
            'col_group' => 'nullable|integer|min:1',
            'assign_group' => 'nullable|exists:groups,id',
            'default_password' => 'nullable|string|min:4',
            'skip_first_row' => 'nullable|boolean',
        ]);

        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows)) {
            return back()->with('error', 'Файл пуст.');
        }

        if ($request->mode === 'auto') {
            $headers = $rows[array_key_first($rows)] ?? [];
            $headers = array_map(fn($v) => trim((string) $v), $headers);
            $dataRows = array_slice($rows, 1);

            $mapping = $this->autoDetectMapping($headers);

            if ($mapping['name'] === null) {
                return back()->with('error', 'Не удалось определить колонку с именем. Убедитесь, что первая строка содержит заголовки, или используйте ручной режим.');
            }
        } else {
            $dataRows = $request->boolean('skip_first_row') ? array_slice($rows, 1) : $rows;

            $mapping = [
                'name' => $this->manualCol($request->col_name),
                'username' => $this->manualCol($request->col_username),
                'password' => $this->manualCol($request->col_password),
                'group' => $this->manualCol($request->col_group),
            ];
        }

        $created = 0;
        $errors = [];
        $defaultPassword = $request->input('default_password', 'password123');

        foreach ($dataRows as $rowIndex => $row) {
            $name = trim($row[$mapping['name']] ?? '');
            if (empty($name)) {
                continue;
            }

            $username = '';
            if ($mapping['username'] !== null && isset($row[$mapping['username']])) {
                $username = trim($row[$mapping['username']]);
            }
            if (empty($username)) {
                $username = $this->generateUsername($name);
            }

            $password = $defaultPassword;
            if ($mapping['password'] !== null && isset($row[$mapping['password']]) && !empty(trim($row[$mapping['password']]))) {
                $password = trim($row[$mapping['password']]);
            }

            $existingUser = User::where('username', $username)->first();
            if ($existingUser) {
                $errors[] = "Строка {$rowIndex}: пользователь с логином '{$username}' уже существует (пропущен)";
                continue;
            }

            $user = User::create([
                'name' => $name,
                'username' => $username,
                'password' => bcrypt($password),
            ]);

            $user->assignRole('student');

            $groupId = null;
            if ($request->filled('assign_group')) {
                $groupId = (int) $request->assign_group;
            } elseif ($mapping['group'] !== null && isset($row[$mapping['group']]) && !empty(trim($row[$mapping['group']]))) {
                $groupName = trim($row[$mapping['group']]);
                $group = Group::firstOrCreate(['name' => $groupName]);
                $groupId = $group->id;
            }

            if ($groupId) {
                $user->groups()->sync([$groupId]);
            }

            $created++;
        }

        $message = "Импортировано пользователей: {$created}.";
        if (!empty($errors)) {
            $message .= ' Ошибки: ' . implode('; ', array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $message .= '... и ещё ' . (count($errors) - 10) . ' ошибок';
            }
        }

        return back()->with('success', $message);
    }

    private function manualCol($value): ?int
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }
        return (int) $value - 1;
    }

    private function autoDetectMapping(array $headers): array
    {
        $mapping = ['name' => null, 'username' => null, 'password' => null, 'group' => null];

        $patterns = [
            'name' => ['имя', 'фио', 'ф.и.о.', 'фамилия', 'name', 'full name', 'student name', 'fio', 'fullname', 'student'],
            'username' => ['логин', 'username', 'login', 'user', 'ник', 'nickname', 'login name'],
            'password' => ['пароль', 'password', 'pass', 'pwd'],
            'group' => ['группа', 'group', 'класс', 'class', 'курс', 'отделение'],
        ];

        foreach ($headers as $i => $header) {
            $lower = mb_strtolower($header, 'UTF-8');
            foreach ($patterns as $field => $keywords) {
                foreach ($keywords as $kw) {
                    if ($lower === $kw || str_contains($lower, $kw)) {
                        if ($mapping[$field] === null) {
                            $mapping[$field] = $i;
                        }
                        break;
                    }
                }
            }
        }

        return $mapping;
    }

    private function generateUsername(string $name): string
    {
        $base = Str::slug($name, '_');
        if (empty($base)) {
            $base = 'student';
        }
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . '_' . $i++;
        }
        return $username;
    }
}

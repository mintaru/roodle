<div align="center">

# Roodle LMS

**Система управления обучением на базе Laravel + MySQL**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

*Гибкая и мощная платформа для онлайн-обучения*

</div>

---

## О проекте

**Roodle** — это система управления обучением (LMS), разработанная на фреймворке Laravel с использованием MySQL. Платформа предоставляет полный инструментарий для организации учебного процесса: от создания курсов и управления студентами до тестирования и отслеживания прогресса.

---

## Основной функционал

### Для преподавателей
- Создание и управление курсами с разбивкой по модулям и темам
- Загрузка учебных материалов: документы, видео, презентации
- Создание тестов и заданий с гибкой системой оценивания
- Отслеживание прогресса и активности студентов
- Управление записью на курс (ручная / автоматическая)

### Для студентов
- Личный кабинет с дашбордом по всем курсам
- Прохождение тестов и сдача заданий онлайн
- Просмотр оценок
- Журнал активности и прогресс по курсу
- Уведомления о дедлайнах и новых материалах

### Для администраторов
- Управление пользователями и ролями (Администратор / Преподаватель / Студент)
- Настройка категорий курсов
- Просмотр общей статистики платформы
- Управление настройками системы

---

## Технологический стек

| Компонент | Технология |
|-----------|------------|
| Backend | Laravel 12.x (PHP 8.2+) |
| База данных | MySQL 8.0 |
| Frontend | Blade + TailwindCSS / Bootstrap |
| Аутентификация | Laravel Breeze |
| Хранение файлов | Laravel Storage |

---


## Установка (Docker)

### 1. Клонировать репозиторий
```bash
git clone https://github.com/mintaru/roodle.git
cd roodle
```
### 2. Настройка окружения
```bash
cp .env.example .env
```
### 3. Запустить контейнеры
```bash
docker-compose up -d --build
docker-compose build --progress=plain
```
### 4. Сгенерировать ключ приложения
```bash
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan storage:link
```
### 5. Запустить миграции
```bash
docker-compose exec app php artisan migrate 
```
Для заполнения тестовыми данными и пользователями:
```bash
php artisan db:seed
```
Приложение будет доступно по адресу: [http://localhost:8080](http://localhost:8080)

## Установка (обычная)

### Требования

- PHP >= 8.2
- Composer >= 2.x
- MySQL >= 8.0
- Node.js >= 18.x & npm
- Git

### 1. Клонирование репозитория

```bash
git clone https://github.com/mintaru/roodle.git
cd roodle
```

### 2. Установка зависимостей

```bash
composer install
npm install && npm run build
```

### 3. Настройка окружения

```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

Отредактируйте файл `.env`, указав параметры подключения к базе данных:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=roodle
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```


### 4. Запуск миграций и сервера разработки

```bash
php artisan migrate
```
Для заполнения тестовыми данными и пользователями:
```bash
php artisan db:seed
```

## Скриншоты
![Скриншот интерфейса](https://github.com/mintaru/roodle/blob/main/screenshots/screenshot1.jpg)

<div align="center">
</div>

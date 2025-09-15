<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload; // <-- подключаем
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),

                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->image()
                    ->disk('public')
                    ->directory('courses')
                    ->required(false), // можно сделать необязательным
            ]);
    }
}

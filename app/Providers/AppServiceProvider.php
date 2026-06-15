<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        \Illuminate\Support\Facades\DB::listen(function ($query) {
            if (str_contains($query->sql, 'test_attempts')) {
                $msg = date('H:i:s').' SQL: '.$query->sql."\n"
                    .'Bindings: '.json_encode($query->bindings)."\n\n";
                file_put_contents('C:/Users/xDoodle/Desktop/sql_debug.log', $msg, FILE_APPEND);
            }
        });
    }
}

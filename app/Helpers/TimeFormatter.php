<?php

namespace App\Helpers;

class TimeFormatter
{
    /**
     * Форматирует количество минут в читаемый вид
     * Например: 0-1 -> "<1 минуты", 5 -> "5 минут", 45 -> "45 минут"
     */
    public static function formatMinutes($minutes)
    {
        $minutes = intval($minutes);
        
        if ($minutes < 1) {
            return '<1 минуты';
        }
        
        // Определяем правильное окончание
        $remainder = $minutes % 10;
        $hundredRemainder = $minutes % 100;
        
        if ($hundredRemainder >= 11 && $hundredRemainder <= 19) {
            $ending = 'минут';
        } elseif ($remainder === 1) {
            $ending = 'минута';
        } elseif ($remainder >= 2 && $remainder <= 4) {
            $ending = 'минуты';
        } else {
            $ending = 'минут';
        }
        
        return "{$minutes} {$ending}";
    }
}

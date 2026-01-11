<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function upload(Request $request)
    {
        if (! $request->hasFile('file')) {
            return response()->json(['error' => 'Файл не найден'], 400);
        }

        $path = $request->file('file')->store('questions', 'public');

        return response()->json([
            'location' => Storage::url($path),
        ]);
    }
}

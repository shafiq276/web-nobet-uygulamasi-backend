<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Backend is running'
    ]);
});

Route::get('/', function () {
    return view('welcome');
});

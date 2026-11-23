<?php

use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Semua URL otomatis diawali /api oleh Laravel 12.
Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);

// Data pengguna demo disediakan lewat seeder; tidak ada registrasi/auth dalam tugas ini.
Route::get('users', [UserController::class, 'index']);
Route::get('users/{user}', [UserController::class, 'show']);

// Aksi khusus yang mengelola relasi many-to-many.
Route::get('users/{user}/collection', [CollectionController::class, 'index']);
Route::post('users/{user}/collection', [CollectionController::class, 'store']);
Route::delete('users/{user}/collection/{media}', [CollectionController::class, 'destroy']);

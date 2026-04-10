<?php

use App\Http\Controllers\SkController;
use App\Http\Controllers\SkPdfController;
use App\Http\Controllers\SkDocxController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.landing')->name('landing');
Route::view('/about', 'pages.about')->name('about');
Route::view('/guide', 'pages.guide')->name('guide');
Route::get('/sk/create', [SkController::class, 'create'])->name('sk.create');
Route::get('/sk/new', [SkController::class, 'newDraft'])->name('sk.new');
Route::post('/sk/handle', [SkController::class, 'handle'])->name('sk.handle');
Route::get('/sk/pdf', SkPdfController::class)->middleware('sk.ready')->name('sk.pdf');
Route::get('/sk/preview/pdf', [SkPdfController::class, 'preview'])->middleware('sk.ready')->name('sk.preview.pdf');
Route::get('/sk/docx', SkDocxController::class)->middleware('sk.ready')->name('sk.docx');
Route::get('/sk/preview/docx', [SkDocxController::class, 'preview'])->middleware('sk.ready')->name('sk.preview.docx');
Route::get('/sk/preview', [SkController::class, 'preview'])->middleware('sk.ready')->name('sk.preview');
Route::view('/contact', 'pages.contact')->name('contact');

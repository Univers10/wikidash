<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dash.index');
})->name('dash');

Route::get('/articles-signales', function () {
    return view('dash.articles-signales');
})->name('articles-signales');

Route::get('/articles-modifier', function () {
    return view('dash.articles-modifier');
})->name('articles-modifier');

Route::get('/articles', function () {
    return view('dash.articles');
})->name('articles');
Route::get('/articles-recherche', function () {
    return view('dash.articles-recherche');
})->name('articles-recherche');

<?php

use Illuminate\Support\Facades\Route;
Route::get('/kasir', function () {
    return view('kasir');
});

Route::get('/', function () {
    return view('welcome');
});

<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});

// Redirect root ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

// Redirect /admin ke dashboard (jaga-jaga)
Route::get('/admin', function () {
    return redirect('/dashboard');
});




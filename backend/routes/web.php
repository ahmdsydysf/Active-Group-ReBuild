<?php

use Illuminate\Support\Facades\Route;

// API only. The site and dashboard are the Next.js app in frontend/.
Route::get('/', fn () => response()->json(['name' => config('app.name'), 'docs' => url('/api/documentation')]));

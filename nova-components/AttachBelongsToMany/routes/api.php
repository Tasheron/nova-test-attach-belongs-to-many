<?php

use Illuminate\Support\Facades\Route;
use Tasheron\AttachBelongsToMany\Http\Controllers\ResourceController;

Route::post('/find', [ResourceController::class, 'find']);
Route::post('/{resource}/attach/{attachResource}', [ResourceController::class, 'attach']);
Route::post('/{resource}/detach/{attachResource}', [ResourceController::class, 'detach']);
Route::post('/{resource}/changeIndex/{attachResource}', [ResourceController::class, 'changeIndex']);
Route::post('/{resource}/updatePivot/{attachResource}', [ResourceController::class, 'updatePivot']);

<?php

use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GraphicsController;

// Route::get('/graphics', [IndexController::class, 'graphics'])->name('graphicsHome');;

Route::get('/', [IndexController::class, 'index'])->name('home');;
Route::get('/graphics', [GraphicsController::class, 'index'])
    ->name('graphicsHome');
Route::get('/collaborators', [CollaboratorController::class, 'index'])->name('collaborators');
Route::post('/collaborator/new', [CollaboratorController::class, 'create'])->name('collaborators.new');
Route::get('/collaborator/{id}', [CollaboratorController::class, 'find'])->name('collaborator_detail');
Route::match(['get','post'],'/sale/new', [SaleController::class, 'new'])->name('sale_new');
Route::post('/installment/{id}/mark_client_paid', [InstallmentController::class, 'markClientPaid'])->name('markClientPaid');
Route::post('/installment/{id}/mark_collaborator_paid', [InstallmentController::class, 'markCollaboratorPaid'])->name('markCollaboratorPaid');
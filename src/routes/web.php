<?php

use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index']);
Route::get('/collaborators', [CollaboratorController::class, 'index'])->name('collaborators');
Route::post('/collaborator/new', [CollaboratorController::class, 'create'])->name('collaborators.new');
Route::get('/collaborator/{id}', [CollaboratorController::class, 'find'])->name('collaborator_detail');
Route::match(['get','post'],'/sale/new', [SaleController::class, 'new'])->name('sale_new');
Route::post('/installment/{id}/mark_client_paid', [InstallmentController::class, 'markClientPaid']);
Route::post('/installment/{id}/mark_collaborator_paid', [InstallmentController::class, 'markCollaboratorPaid']);
<?php

use App\Http\Controllers\ArticleFlowController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommandeFlowController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VenteController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome-modern')->name('welcome');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('clients', ClientController::class);
    Route::resource('fournisseurs', FournisseurController::class);
    Route::resource('articles', ArticleFlowController::class);
    Route::patch('/articles/{article}/statut', [ArticleFlowController::class, 'updateStatut'])->name('articles.statut');
    Route::resource('ventes', VenteController::class)->except(['edit', 'update']);
    Route::resource('commandes', CommandeFlowController::class);
    Route::patch('/commandes/{commande}/livrer', [CommandeFlowController::class, 'marquerLivree'])->name('commandes.livrer');
    Route::patch('/commandes/{commande}/statut', [CommandeFlowController::class, 'updateStatut'])->name('commandes.statut');
    Route::post('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAsRead');
});

require __DIR__.'/auth.php';

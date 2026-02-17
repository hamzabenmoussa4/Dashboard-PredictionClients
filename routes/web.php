<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\CustomerCrudController;
use App\Http\Controllers\OrderCrudController;
use App\Http\Controllers\RuleCrudController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BadgeActionController;
use App\Http\Controllers\AutomationActionsController;


// Page d’accueil : toujours login
Route::get('/', function () {
    return redirect()->route('login');
});

// Routes protégées : connecté + admin
Route::middleware(['auth', 'admin'])->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    // CRUD CLIENTS
    Route::get('/customers', [CustomerCrudController::class, 'index'])
        ->name('customers.index');

    Route::post('/customers', [CustomerCrudController::class, 'store'])
        ->name('customers.store');

    Route::put('/customers/{customer}', [CustomerCrudController::class, 'update'])
        ->name('customers.update');

    Route::delete('/customers/{customer}', [CustomerCrudController::class, 'destroy'])
        ->name('customers.destroy');

    // CRUD COMMANDES
    Route::get('/orders', [OrderCrudController::class, 'index'])
        ->name('orders.index');

    Route::post('/orders', [OrderCrudController::class, 'store'])
        ->name('orders.store');

    Route::put('/orders/{order}', [OrderCrudController::class, 'update'])
        ->name('orders.update');

    Route::delete('/orders/{order}', [OrderCrudController::class, 'destroy'])
        ->name('orders.destroy');

    // PREDICTIONS
    Route::get('/predictions', [PredictionController::class, 'index'])
        ->name('predictions.index');

    // Route::post('/predictions/run', [PredictionController::class, 'run'])
    //     ->name('predictions.run');

    // Route::post('/predictions/run/{customerId}', [PredictionController::class, 'runForCustomer'])
    //     ->name('predictions.runCustomer');

    // AUTOMATION RULES (CRUD)
    Route::get('/automation/rules', [RuleCrudController::class, 'index'])
        ->name('automation.rules');

    Route::post('/automation/rules', [RuleCrudController::class, 'store'])
        ->name('automation.rules.store');

    Route::put('/automation/rules/{rule}', [RuleCrudController::class, 'update'])
        ->name('automation.rules.update');

    Route::delete('/automation/rules/{rule}', [RuleCrudController::class, 'destroy'])
        ->name('automation.rules.destroy');

    Route::post('/automation/rules/{rule}/toggle', [RuleCrudController::class, 'toggle'])
        ->name('automation.rules.toggle');

    // AUTOMATION ACTIONS (par badge)

Route::get('/automation/actions', [AutomationActionsController::class, 'index'])
    ->name('automation.actions');

Route::post('/automation/actions/run', [AutomationActionsController::class, 'run'])
    ->name('automation.actions.run');


    // AUTOMATION RUN (recalcul badges)
    Route::post('/automation/run', [AutomationController::class, 'run'])
        ->name('automation.run');

    // PROFILE (optionnel)
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';

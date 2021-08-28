<?php

use Illuminate\Support\Facades\Route;

use Bowhead\Http\Controllers\Accounts;
use Bowhead\Http\Controllers\Main;
use Bowhead\Http\Controllers\Markets;
use Bowhead\Http\Controllers\Positions;
use Bowhead\Http\Controllers\Transactions;
use Dingo\Blueprint\Annotation\Transaction;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth.client:admin')->group(function () {
    /** accounts
     * list accounts + balances, transfers, deposit, withdrawal etc
    */
    Route::name('accounts.')->prefix('accounts')->group(function () {
        Route::get('/', [Accounts::class, 'getAllAccounts'])->name('all');
        Route::get('/{id}', [Accounts::class, 'getAccount'])->name('show');
        Route::post('/', [Accounts::class, 'createAccount'])->name('create');
        Route::put('{id}', [Accounts::class, 'updateAccount'])->name('update');
        Route::delete('{id}', [Accounts::class, 'deleteAccount'])->name('delete');
    });
    /**
     * markets
     * list of instruments, prices etc
    */
    Route::name('markets.')->prefix('markets')->group(function () {
        Route::get('/', [Markets::class, 'getAllMarkets'])->name('all');
        Route::get('{id}', [Markets::class, 'getMarket'])->name('show');
        Route::post('/', [Markets::class, 'createMarket'])->name('create');
        Route::put('{id}', [Markets::class, 'updateMarket'])->name('update');
        Route::delete('{id}', [Markets::class, 'deleteMarket'])->name('delete');
    });

    /**
     * positions
     * new/open/close/pending/cancel
    */
    Route::name('positions.')->prefix('positions')->group(function () {
        Route::get('/', [Positions::class, 'getAllPositions'])->name('all');
        Route::get('{id}', [Positions::class, 'getPosition'])->name('show');
        Route::post('/', [Positions::class, 'createPosition'])->name('create');
        Route::put('{id}', [Positions::class, 'updatePosition'])->name('update');
        Route::delete('{id}', [Positions::class, 'deletePosition'])->name('delete');
    });
    /**
     * transactions
     * deposits, withdrawls etc.
    */
    Route::name('transactions.')->prefix('transactions')->group(function () {
        Route::get('/', [Transactions::class, 'getAllTransactions'])->name('all');
        Route::get('{id}', [Transactions::class, 'getTransaction'])->name('show');
    });
});

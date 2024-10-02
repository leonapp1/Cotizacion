<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\CotizacionesController;
use App\Http\Controllers\DetallesCotizacionController;
use App\Http\Controllers\ObservacionesController;
use App\Http\Controllers\ContratosController;
use App\Http\Controllers\DetallesContratosController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return Inertia::render(
        'Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
}); */

// Redirigir la ruta raíz al login
Route::get('/', function () {
    return redirect()->route('login');
});

/* Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard'); */
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/about', fn () => Inertia::render('About'))->name('about');

    Route::get('users', [UserController::class, 'index'])->name('users.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('productos',ProductosController::class);
    Route::post('/productos', [ProductosController::class, 'store'])->name('productos.store');
    Route::post('/productos/{id}', [ProductosController::class, 'update'])->name('productos.update');


    Route::resource('clientes',ClientesController::class);
    Route::post('/clientes/search', [ClientesController::class, 'searchByDni'])->name('clientes.search');

    Route::resource('cotizaciones',CotizacionesController::class);
    Route::resource('detallescotizacion',DetallesCotizacionController::class);
    Route::resource('detallescontratos',DetallesContratosController::class);

    Route::resource('observaciones',ObservacionesController::class);

    Route::resource('contratos',ContratosController::class);


});

Route::get('cotizaciones/{id}/pdf', [CotizacionesController::class, 'generarPDF'])->name('cotizaciones.pdf');
Route::get('contratos/{id}/pdf', [ContratosController::class, 'generarPDF'])->name('contratos.pdf');


require __DIR__.'/auth.php';

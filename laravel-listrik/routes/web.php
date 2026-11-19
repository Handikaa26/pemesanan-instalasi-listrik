<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Konsumen\KonsumenController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes - Pemesanan Instalasi Listrik PT. Eksavindo Mitra Mandiri
|--------------------------------------------------------------------------
*/

// --- Public & Konsumen Routes ---
Route::get('/', [KonsumenController::class, 'home'])->name('home');
Route::get('/katalog', [KonsumenController::class, 'katalog'])->name('katalog');
Route::get('/produk/{id}', [KonsumenController::class, 'detailProduk'])->name('produk.detail');

// Midtrans Webhook Callback
Route::post('/midtrans/callback', [KonsumenController::class, 'midtransCallback'])->name('midtrans.callback');

// --- Authentication Routes ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// --- Konsumen Protected Routes ---
Route::middleware(['auth'])->group(function () {
    Route::get('/keranjang', [KonsumenController::class, 'keranjang'])->name('keranjang');
    Route::post('/keranjang/tambah', [KonsumenController::class, 'tambahKeranjang'])->name('keranjang.tambah');
    Route::post('/keranjang/update', [KonsumenController::class, 'updateKeranjang'])->name('keranjang.update');
    Route::delete('/keranjang/{id}', [KonsumenController::class, 'hapusKeranjang'])->name('keranjang.hapus');

    Route::get('/checkout', [KonsumenController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [KonsumenController::class, 'prosesCheckout'])->name('checkout.proses');

    Route::get('/pesanan-saya', [KonsumenController::class, 'pesananSaya'])->name('pesanan.saya');
    Route::get('/kuitansi/{id}', [KonsumenController::class, 'kuitansi'])->name('kuitansi');
});

// --- Administrator Protected Routes ---
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Pesanan & Penjadwalan
    Route::get('/pesanan', [AdminController::class, 'pesanan'])->name('pesanan');
    Route::get('/pesanan/{id}', [AdminController::class, 'detailPesanan'])->name('pesanan.detail');
    Route::put('/pesanan/{id}', [AdminController::class, 'updatePesanan'])->name('pesanan.update');

    // CRUD Produk
    Route::get('/produk', [AdminController::class, 'produk'])->name('produk');
    Route::post('/produk', [AdminController::class, 'storeProduk'])->name('produk.store');
    Route::put('/produk/{id}', [AdminController::class, 'updateProduk'])->name('produk.update');
    Route::delete('/produk/{id}', [AdminController::class, 'destroyProduk'])->name('produk.destroy');

    // CRUD Kategori
    Route::get('/kategori', [AdminController::class, 'kategori'])->name('kategori');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
    Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');

    // CRUD Bank
    Route::get('/bank', [AdminController::class, 'bank'])->name('bank');
    Route::post('/bank', [AdminController::class, 'storeBank'])->name('bank.store');
    Route::put('/bank/{id}', [AdminController::class, 'updateBank'])->name('bank.update');
    Route::delete('/bank/{id}', [AdminController::class, 'destroyBank'])->name('bank.destroy');

    // CRUD Diskon
    Route::get('/diskon', [AdminController::class, 'diskon'])->name('diskon');
    Route::post('/diskon', [AdminController::class, 'storeDiskon'])->name('diskon.store');
    Route::put('/diskon/{id}', [AdminController::class, 'updateDiskon'])->name('diskon.update');
    Route::delete('/diskon/{id}', [AdminController::class, 'destroyDiskon'])->name('diskon.destroy');
});


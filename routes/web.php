<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Staff\StaffDashboardController;
use App\Http\Controllers\Staff\BookingController as StaffBookingController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\NotificationController as CustomerNotificationController;

// ─── PUBLIC ROUTES ────────────────────────────────────────────────────────────
Route::get('/',        [HomeController::class, 'index'])->name('home');
Route::get('/about',   [HomeController::class, 'about'])->name('about');
Route::get('/rooms',   [HomeController::class, 'rooms'])->name('rooms');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// ─── AUTH ROUTES ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login',   [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[RegisterController::class, 'register'])->name('register.post');

    // Password Reset
    Route::get('/forgot-password',        [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password',       [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password',        [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── PROFILE (all authenticated users) ───────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',           [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile/update',   [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
});

// ─── ADMIN ROUTES ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Staff Management
    Route::get   ('/staff',                [StaffController::class, 'index'])->name('staff.index');
    Route::get   ('/staff/create',         [StaffController::class, 'create'])->name('staff.create');
    Route::post  ('/staff',                [StaffController::class, 'store'])->name('staff.store');
    Route::get   ('/staff/{user}',         [StaffController::class, 'show'])->name('staff.show');
    Route::get   ('/staff/{user}/edit',    [StaffController::class, 'edit'])->name('staff.edit');
    Route::put   ('/staff/{user}',         [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{user}',         [StaffController::class, 'destroy'])->name('staff.destroy');
    Route::post  ('/staff/{user}/approve', [StaffController::class, 'approve'])->name('staff.approve');
    Route::post  ('/staff/{user}/reject',  [StaffController::class, 'reject'])->name('staff.reject');
    Route::post  ('/staff/{user}/toggle',  [StaffController::class, 'toggleStatus'])->name('staff.toggle');

    // Customer Management
    Route::get   ('/customers',                 [CustomerController::class, 'index'])->name('customers.index');
    Route::get   ('/customers/{user}',          [CustomerController::class, 'show'])->name('customers.show');
    Route::post  ('/customers/{user}/toggle',   [CustomerController::class, 'toggleStatus'])->name('customers.toggle');
    Route::delete('/customers/{user}',          [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Room Management
    Route::resource('rooms', RoomController::class);

    // Booking Management
    Route::get   ('/bookings/availability',    [AdminBookingController::class, 'checkAvailability'])->name('bookings.availability');
    Route::get   ('/bookings',                 [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get   ('/bookings/create',          [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post  ('/bookings',                 [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::get   ('/bookings/{booking}',       [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::get   ('/bookings/{booking}/edit',  [AdminBookingController::class, 'edit'])->name('bookings.edit');
    Route::put   ('/bookings/{booking}',       [AdminBookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}',       [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

    // Revenue & Reports
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/logs',    [ReportController::class, 'logs'])->name('logs');
    Route::get('/ratings', [ReportController::class, 'ratings'])->name('ratings');

    // Notifications
    Route::get   ('/notifications',                     [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::post  ('/notifications/send',                [AdminNotificationController::class, 'send'])->name('notifications.send');
    Route::post  ('/notifications/read-all',            [AdminNotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post  ('/notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');
    Route::delete('/notifications/{notification}',      [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
});

// ─── STAFF ROUTES ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get ('/dashboard',                  [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get   ('/bookings',                  [StaffBookingController::class, 'index'])->name('bookings.index');
    Route::get   ('/bookings/all',              [StaffBookingController::class, 'allBookings'])->name('bookings.all');
    Route::get   ('/bookings/{booking}',        [StaffBookingController::class, 'show'])->name('bookings.show');
    Route::get   ('/bookings/{booking}/edit',   [StaffBookingController::class, 'edit'])->name('bookings.edit');
    Route::put   ('/bookings/{booking}',        [StaffBookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}',        [StaffBookingController::class, 'destroy'])->name('bookings.destroy');
    Route::post  ('/bookings/{booking}/status', [StaffBookingController::class, 'updateStatus'])->name('bookings.status');
});

// ─── CUSTOMER ROUTES ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get ('/dashboard',                           [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get ('/bookings',                            [CustomerBookingController::class, 'index'])->name('bookings.index');
    Route::get ('/bookings/create',                     [CustomerBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings',                            [CustomerBookingController::class, 'store'])->name('bookings.store');
    Route::get ('/bookings/{booking}',                  [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel',           [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/rate',             [CustomerBookingController::class, 'rate'])->name('bookings.rate');
    Route::get ('/notifications',                       [CustomerNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',              [CustomerNotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read',   [CustomerNotificationController::class, 'markRead'])->name('notifications.read');
});
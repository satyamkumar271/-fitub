<?php

use Illuminate\Support\Facades\Route;

// === Controllers Ko Import Karna ===

// Public & Auth Controllers
use App\Http\Controllers\FrontPageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GymPlanController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\InquiryController;


// Admin Controller
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC ROUTES (Sabke liye accessible) ---
Route::get('/', [FrontPageController::class, 'index'])->name('frontpage');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
// Resource-style route (optional)





// Search and Directory Routes
Route::get('/search', [SearchController::class, 'handleSearch'])->name('search.handle');
Route::get('/gyms', [GymController::class, 'index'])->name('gyms.index');
Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index');

// === YEH BADLAV KIYA GAYA HAI ===
// Profile page ab sabke liye public hai
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

// Inquiry Route
Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');


// --- 2. GUEST MIDDLEWARE (Sirf unke liye jo login nahi hain) ---
Route::middleware('guest')->group(function () {
    Route::get('auth/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::get('auth/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('auth/login', [AuthController::class, 'login']);
});


// --- 3. AUTH MIDDLEWARE (Sirf logged-in users ke liye) ---
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard', [DashboardController::class, 'updateProfile'])->name('dashboard.update');
     // === NAYE GALLERY ROUTES ===
    Route::post('/dashboard/gallery', [DashboardController::class, 'updateGallery'])->name('dashboard.gallery.update');
    Route::post('/dashboard/gallery/delete', [DashboardController::class, 'deleteGalleryImage'])->name('dashboard.gallery.delete');
    Route::post('/dashboard/leads/{inquiry}/unlock', [DashboardController::class, 'unlockLead'])->name('dashboard.leads.unlock');

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Edit Route (Yeh abhi bhi protected hai, jo ki sahi hai)
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // Gym Plan
    Route::resource('gym-plans', GymPlanController::class);
});


// --- 4. ADMIN ROUTES (Sirf admin ke liye) ---
Route::middleware(['auth', 'is.admin'])->prefix('admin')->name('admin.')->group(function () {
    // Default admin URL
    Route::redirect('/', '/admin/users')->name('dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');
    Route::delete('/users/{user}', [AdminController::class, 'userDestroy'])->name('users.destroy');

    // Inquiries Management
    Route::get('/inquiries', [AdminController::class, 'inquiriesIndex'])->name('inquiries.index');
    Route::post('/inquiries/{inquiry}/forward', [AdminController::class, 'forwardInquiry'])->name('inquiries.forward');
});

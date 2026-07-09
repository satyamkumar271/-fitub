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
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\InquiryChatController;
use App\Http\Controllers\SupportController;


// Admin Controller
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\InquiryReportController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\InquiryBlockController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC ROUTES (Sabke liye accessible) ---
Route::get('/', [FrontPageController::class, 'index'])->name('frontpage');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/faq', 'faq')->name('faq');
Route::view('/privacy-policy', 'privacy')->name('privacy');
Route::view('/terms-and-conditions', 'terms')->name('terms');
Route::view('/refund-policy', 'refund')->name('refund');
// Resource-style route (optional)





// Search and Directory Routes
Route::get('/search', [SearchController::class, 'handleSearch'])->name('search.handle');
Route::get('/gyms', [GymController::class, 'index'])->name('gyms.index');
Route::get('/gyms/{city}', [GymController::class, 'index'])->name('gyms.city');
Route::get('/gym-in-{city}', [GymController::class, 'index'])->name('gyms.seo');

Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index');
Route::get('/trainers/{city}', [TrainerController::class, 'index'])->name('trainers.city');
Route::get('/personal-trainer-in-{city}', [TrainerController::class, 'index'])->name('trainers.seo');

// === YEH BADLAV KIYA GAYA HAI ===
// Profile page ab sabke liye public hai
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

// Inquiry Route
Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');
Route::post('/billing/webhook', [PaymentController::class, 'webhook'])->name('billing.webhook');


// --- 2. GUEST MIDDLEWARE (Sirf unke liye jo login nahi hain) ---
Route::middleware('guest')->group(function () {
    Route::get('auth/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::get('auth/verify-otp/{user}', [AuthController::class, 'showOtpForm'])->name('otp.verify.form');
    Route::post('auth/verify-otp/{user}', [AuthController::class, 'verifyOtp'])->name('otp.verify.submit');
    Route::post('auth/resend-otp/{user}', [AuthController::class, 'resendOtp'])->name('otp.resend');
    Route::get('auth/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('auth/login', [AuthController::class, 'login']);
    
    // Password Reset Routes
    Route::get('password/reset', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('password/reset', [AuthController::class, 'resetPassword'])->name('password.update');
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
    Route::post('/dashboard/gym-services', [DashboardController::class, 'updateGymLeadServices'])->name('dashboard.gym-services.update');
    Route::get('/dashboard/leads', [DashboardController::class, 'leads'])->name('dashboard.leads');
    Route::get('/dashboard/payments', [DashboardController::class, 'payments'])->name('dashboard.payments');
    Route::get('/invoice/{payment}/download', [InvoiceController::class, 'userDownload'])->name('invoice.user.download');
    Route::get('/my-inquiries', [InquiryChatController::class, 'myInquiries'])->name('inquiries.mine');
    Route::get('/conversations', [InquiryChatController::class, 'conversations'])->name('inquiries.conversations');
    Route::get('/inquiries/{inquiry}/chat', [InquiryChatController::class, 'show'])->name('inquiries.chat');
    Route::post('/inquiries/{inquiry}/chat', [InquiryChatController::class, 'send'])->name('inquiries.chat.send');
    Route::post('/inquiries/{inquiry}/report', [InquiryChatController::class, 'report'])->name('inquiries.report');
    Route::post('/inquiries/{inquiry}/block', [InquiryChatController::class, 'block'])->name('inquiries.block');
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply');

    // Logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Edit Route (Yeh abhi bhi protected hai, jo ki sahi hai)
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // Billing / Plans (Trainer & GymOwner)
    Route::get('/billing/plans', [PaymentController::class, 'plans'])->name('billing.plans');
    Route::post('/billing/order', [PaymentController::class, 'createOrder'])->name('billing.order');
    Route::post('/billing/verify', [PaymentController::class, 'verify'])->name('billing.verify');
    Route::post('/billing/cancel', [PaymentController::class, 'cancel'])->name('billing.cancel');
});


// --- 4. ADMIN ROUTES (Sirf admin ke liye) ---

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // PENDING USERS ROUTES (Yahan naam sahi kiye hain)
    Route::get('/pending-users', [AdminController::class, 'pendingUsersIndex'])->name('admin.pending');
    Route::post('/approve/{id}', [AdminController::class, 'approveUser'])->name('admin.approve');
    Route::post('/reject/{id}', [AdminController::class, 'rejectUser'])->name('admin.reject');
    
    // Baaki Routes
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::get('/users/registration-issues', [AdminController::class, 'registrationIssuesIndex'])->name('admin.users.registration-issues');
    Route::delete('/user/{user}', [AdminController::class, 'userDestroy'])->name('admin.user.destroy');
    Route::post('/user/{user}/activate', [AdminController::class, 'activateUser'])->name('admin.users.activate');
    Route::get('/inquiries', [AdminController::class, 'inquiriesIndex'])->name('admin.inquiries.index');
    Route::get('/inquiries/{inquiry}/chat', [AdminController::class, 'inquiryChat'])->name('admin.inquiries.chat');
    Route::get('/credits', [AdminController::class, 'creditHistory'])->name('admin.credits.index');
    Route::post('/inquiry/forward/{inquiry}', [AdminController::class, 'forwardInquiry'])->name('admin.inquiry.forward');
    Route::get('/reports', [InquiryReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/{report}', [InquiryReportController::class, 'show'])->name('admin.reports.show');
    Route::post('/reports/{report}/resolve', [InquiryReportController::class, 'resolve'])->name('admin.reports.resolve');
    
    // Blocked Users Routes
    Route::get('/blocks', [InquiryBlockController::class, 'index'])->name('admin.blocks.index');
    Route::get('/blocks/{block}', [InquiryBlockController::class, 'show'])->name('admin.blocks.show');
    Route::post('/blocks/{block}/warning', [InquiryBlockController::class, 'sendWarning'])->name('admin.blocks.warning');
    Route::post('/blocks/{block}/cancel-registration', [InquiryBlockController::class, 'cancelRegistration'])->name('admin.blocks.cancel');
    Route::post('/blocks/{block}/unblock', [InquiryBlockController::class, 'unblock'])->name('admin.blocks.unblock');
    
    Route::get('/support', [SupportTicketController::class, 'index'])->name('admin.support.index');
    Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('admin.support.show');
    Route::post('/support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('admin.support.reply');
    Route::post('/support/{ticket}/resolve', [SupportTicketController::class, 'resolve'])->name('admin.support.resolve');
    Route::get('/blogs', [AdminBlogController::class, 'index'])->name('admin.blogs.index');
    Route::get('/blogs/create', [AdminBlogController::class, 'create'])->name('admin.blogs.create');
    Route::post('/blogs', [AdminBlogController::class, 'store'])->name('admin.blogs.store');
    Route::get('/blogs/{blog}/edit', [AdminBlogController::class, 'edit'])->name('admin.blogs.edit');
    Route::put('/blogs/{blog}', [AdminBlogController::class, 'update'])->name('admin.blogs.update');
    Route::delete('/blogs/{blog}', [AdminBlogController::class, 'destroy'])->name('admin.blogs.destroy');

    // Payments Dashboard
    Route::get('/payments', [AdminController::class, 'paymentsIndex'])->name('admin.payments.index');
    Route::get('/invoice/{payment}/download', [InvoiceController::class, 'adminDownload'])->name('admin.invoice.download');
    Route::get('/user/{id}', [AdminController::class, 'show'])->name('admin.users.show');
});

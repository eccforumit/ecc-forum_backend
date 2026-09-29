<?php

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegistrationController;
use App\Http\Controllers\Api\CompanyProfileController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\Api\OpportunityController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\QrCodeController;
use App\Http\Controllers\Api\StudentProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Tighter throttling on credential/email endpoints to slow brute-force
    // and email-spam attempts (the global "api" limiter is 60/min).
    Route::middleware('throttle:6,1')->group(function () {
        Route::post('login', [LoginController::class, 'login']);
        Route::post('password/forgot', [PasswordResetController::class, 'request']);
        Route::post('password/reset', [PasswordResetController::class, 'reset']);
        Route::post('verify-email/resend', [EmailVerificationController::class, 'resend']);
    });

    Route::get('verify-email/{uid}/{token}', [EmailVerificationController::class, 'verify']);
    Route::post('student/register', [RegistrationController::class, 'student']);
    // Route::post('company/register', [RegistrationController::class, 'company']); // Route désactivée

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('me', [LoginController::class, 'me']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('me/profile', [ProfileController::class, 'show']);
    Route::post('me/profile', [ProfileController::class, 'update']);

    Route::post('me/qr-code', [QrCodeController::class, 'generateMyQrCode']);
    Route::post('qr-code/student/{studentId}', [QrCodeController::class, 'generateStudentQrCode']);

    // Routes réservées aux entreprises
    Route::middleware('user.company')->group(function () {
        Route::get('company/students', [StudentProfileController::class, 'index']);
        Route::get('profiles/student/{studentProfile}', [StudentProfileController::class, 'show']);

        Route::get('qr-codes/ecc/all', [QrCodeController::class, 'getAllEccQrCodes']);

        Route::post('opportunities', [OpportunityController::class, 'store']);
        Route::put('opportunities/{opportunity}', [OpportunityController::class, 'update']);
        Route::patch('opportunities/{opportunity}', [OpportunityController::class, 'update']);
    });

    // Routes réservées aux étudiants
    Route::middleware('user.student')->group(function () {
        Route::put('profiles/student/{studentProfile}', [StudentProfileController::class, 'update']);
        Route::patch('profiles/student/{studentProfile}', [StudentProfileController::class, 'update']);

        Route::post('cv', [CvController::class, 'store']);
        Route::put('cv/{cvDocument}', [CvController::class, 'update']);
        Route::patch('cv/{cvDocument}', [CvController::class, 'update']);
    });

    // Routes pour les profils d'entreprise (réservées aux entreprises)
    Route::middleware('user.company')->group(function () {
        Route::get('profiles/company/{companyProfile}', [CompanyProfileController::class, 'show']);
        Route::put('profiles/company/{companyProfile}', [CompanyProfileController::class, 'update']);
        Route::patch('profiles/company/{companyProfile}', [CompanyProfileController::class, 'update']);
    });
});

Route::get('cv', [CvController::class, 'index']);
Route::get('cv/{cvDocument}', [CvController::class, 'show']);

Route::get('opportunities', [OpportunityController::class, 'index']);
Route::get('opportunities/{opportunity}', [OpportunityController::class, 'show']);

Route::prefix('contact')->group(function () {
    Route::post('send', [ContactController::class, 'send']);
    Route::get('status', [ContactController::class, 'status']);
});

<?php

use Illuminate\Support\Facades\Route;
use App\Models\Settings;

// Controllers
use App\Http\Controllers\{
    DataFeedController,
    DashboardController,
    CheckoutController,
    PaymentController,
    CourseController,
    UserController,
    SettingsController,
    WebsiteController,
    RoleController,
    PermissionController,
    ChatController,
    TrainingScheduleController,
    InstructorController,
    TestimonialsController,
    EmailSubscriptionController,
    EmailConfigController,
    PaymentGatewayConfigController,
    RegistrationFormController
};

// Student Controllers
use App\Http\Controllers\{
    StudentDashboardController,
    StudentCourseController,
    StudentScheduleController,
    StudentMaterialsController,
    StudentProgressController,
    StudentAttendanceController,
    StudentAssignmentController,
    StudentAnnouncementController
};

// Instructor Controllers
use App\Http\Controllers\Instructor\{
    InstructorDashboardController,
    StudentManagementController,
    AttendanceController,
    MaterialController,
    AnnouncementController,
    ScheduleController
};

// Livewire Components
use App\Livewire\{
    ForumList,
    ForumCreatePost,
    ForumPostPage
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Dynamic Landing Page Configuration
$settings = Settings::first();
$preferredLandingPage = $settings->preferred_landing_page ?? 1;

Route::get('/', function() use ($preferredLandingPage) {
    $controller = app(WebsiteController::class);
    return $preferredLandingPage === 1
        ? $controller->index()
        : $controller->home();
})->name('index');

// Public Website Routes
Route::controller(WebsiteController::class)->group(function () {
    Route::get('/about-us', 'about')->name('about-us');
    Route::get('/courses', 'courses')->name('courses');
    Route::get('/course-information/{name}', 'course_details')->name('course');
    Route::get('/our-instructors', 'instructors')->name('our-instructors');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/email-subscription', 'email_subscription')->name('email-subscription');
});

// Printable (offline) application form, mirroring the online registration form
Route::get('/register/application-form', [RegistrationFormController::class, 'download'])->name('register.form-pdf');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Payment Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('checkout')->controller(CheckoutController::class)->group(function () {
        Route::get('/pay', 'index')->name('checkout.pay');
        Route::post('/pay', 'redirectToPaystack')->name('payment');
        Route::get('/payment/callback', 'handleGatewayCallback')->name('payment.callback');
    });

    /*
    |--------------------------------------------------------------------------
    | Dashboard & Common Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');
    Route::get('/chats', [ChatController::class, 'chatIndex'])->name('chats');
    Route::get('/user-profile', [UserController::class, 'userProfile'])->name('user-profile');

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('student')->name('student.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Courses
        Route::controller(StudentCourseController::class)->group(function () {
            Route::get('/courses', 'index')->name('courses');
            Route::get('/course/{id}', 'show')->name('course-details');
        });

        // Schedule
        Route::controller(StudentScheduleController::class)->prefix('schedule')->group(function () {
            Route::get('/', 'index')->name('schedule');
            Route::get('/calendar', 'calendar')->name('schedule.calendar');
        });

        // Materials
        Route::controller(StudentMaterialsController::class)->prefix('materials')->group(function () {
            Route::get('/', 'index')->name('materials');
            Route::get('/download/{id}', 'download')->name('download-material');
        });

        // Progress
        Route::controller(StudentProgressController::class)->prefix('progress')->group(function () {
            Route::get('/', 'index')->name('progress');
            Route::get('/{courseId}', 'course')->name('progress.course');
        });

        // Attendance
        Route::get('/attendance', [StudentAttendanceController::class, 'index'])->name('attendance');

        // Assignments
        Route::controller(StudentAssignmentController::class)->prefix('assignments')->group(function () {
            Route::get('/', 'index')->name('assignments');
            Route::get('/{id}', 'show')->name('assignment.show');
            Route::post('/{id}/submit', 'submit')->name('assignment.submit');
        });

        // Announcements
        Route::controller(StudentAnnouncementController::class)->prefix('announcements')->group(function () {
            Route::get('/', 'index')->name('announcements');
            Route::get('/{id}', 'show')->name('announcement.show');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Instructor Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('instructor')->name('instructor.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');

        // Students Management
        Route::controller(StudentManagementController::class)->prefix('students')->group(function () {
            Route::get('/', 'index')->name('students');
            Route::get('/course/{courseId}', 'byCourse')->name('course.students');
            Route::get('/{id}', 'show')->name('student.show');
        });

        // Attendance Management
        Route::controller(AttendanceController::class)->prefix('attendance')->group(function () {
            Route::get('/', 'index')->name('attendance');
            Route::get('/mark/{scheduleId}', 'mark')->name('attendance.mark');
            Route::post('/save', 'save')->name('attendance.save');
            Route::get('/report', 'report')->name('attendance.report');
            Route::get('/edit/{scheduleId}', 'edit')->name('attendance.edit');
        });

        // Materials Management
        Route::controller(MaterialController::class)->prefix('materials')->group(function () {
            Route::get('/', 'index')->name('materials');
            Route::get('/course/{courseId}', 'byCourse')->name('materials.course');
            Route::post('/upload', 'upload')->name('materials.upload');
            Route::post('/store', 'store')->name('materials.store');
            Route::get('/{id}/edit', 'edit')->name('materials.edit');
            Route::put('/{id}', 'update')->name('materials.update');
            Route::delete('/{id}', 'destroy')->name('materials.destroy');
            Route::get('/download/{id}', 'download')->name('materials.download');
        });

        // Announcements Management
        Route::controller(AnnouncementController::class)->prefix('announcements')->group(function () {
            Route::get('/', 'index')->name('announcements');
            Route::get('/create', 'create')->name('announcements.create');
            Route::post('/', 'store')->name('announcements.store');
            Route::get('/{id}', 'show')->name('announcements.show');
            Route::get('/{id}/edit', 'edit')->name('announcements.edit');
            Route::put('/{id}', 'update')->name('announcements.update');
            Route::delete('/{id}', 'destroy')->name('announcements.destroy');
            Route::patch('/{id}/toggle', 'toggle')->name('announcements.toggle');
        });

        // Schedule Management
        Route::controller(ScheduleController::class)->prefix('schedule')->group(function () {
            Route::get('/', 'index')->name('schedule');
            Route::get('/calendar', 'calendar')->name('schedule.calendar');
            Route::get('/export', 'export')->name('schedule.export');
            Route::get('/change-requests', 'changeRequests')->name('schedule.change-requests');
            Route::get('/{id}', 'show')->name('schedule.show');
            Route::match(['get', 'post'], '/{id}/request-change', 'requestChange')->name('schedule.request-change');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'indexTiles'])->name('users');
            Route::post('/make-admin', [UserController::class, 'makeAdmin'])->name('make.admin');
            Route::get('/deactivate/{id}', [UserController::class, 'deactivate'])->name('user.deactivate');
            Route::get('/verify/{id}', [UserController::class, 'verifyAccount'])->name('user.verify');
        });

        // Roles & Permissions
        Route::controller(RoleController::class)->prefix('roles')->group(function () {
            Route::get('/', 'index')->name('user-roles');
            Route::post('/store', 'roleStore')->name('user-roles.store');
            Route::get('/destroy/{id}', 'roleDestroy')->name('user-roles.destroy');
        });

        Route::controller(PermissionController::class)->prefix('permissions')->group(function () {
            Route::get('/{roleId}', 'index')->name('user-permissions');
            Route::post('/store', 'permissionStore')->name('permission.store');
            Route::post('/update', 'permissionUpdate')->name('update-role-permission');
        });

        // Instructor Management
        Route::controller(InstructorController::class)->prefix('instructors')->group(function () {
            Route::get('/', 'index')->name('instructors');
            Route::post('/store', 'store')->name('instructor.store');
        });

        // Payment Management
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments');

        // Newsletter Management
        Route::get('/newsletter', [EmailSubscriptionController::class, 'index'])->name('newsletter');

        // Testimonials Management
        Route::controller(TestimonialsController::class)->prefix('testimonials')->group(function () {
            Route::get('/', 'index')->name('testimonials');
            Route::post('/store', 'store')->name('testimonial.store');
            Route::put('/update', 'update')->name('testimonial.update');
            Route::get('/destroy/{id}', 'destroy')->name('testimonial.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Course Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('course')->controller(CourseController::class)->group(function () {
        Route::get('/management', 'index')->name('course-management');
        Route::get('/details/{id}', 'show')->name('course-details');
        Route::put('/details/update', 'updateCourseDetails')->name('course-details.update');
        Route::post('/material/store', 'uploadCourseMaterials')->name('upload-course-materials');
        Route::get('/material/download/{id}', 'downloadMaterial')->name('materials.download');
        Route::get('/material/destroy/{id}', 'materialDestroy')->name('material.delete');
        Route::post('/curriculum/store', 'curriculumStore')->name('curriculum.store');
        Route::get('/curriculum/destroy/{id}', 'curriculumDestroy')->name('curriculum.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Training Schedule Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('training-schedule')->controller(TrainingScheduleController::class)->group(function () {
        Route::get('/', 'index')->name('schedule');
        Route::post('/create', 'create')->name('schedule.create');
        Route::get('/topics/{id}', 'getTopics');
        Route::get('/instructors/{course_id}', 'getInstructors');
        Route::get('/instructor-students/{batch_id}/{course_id}/{instructor_id}', 'getInstructorStudents');
    });

    /*
    |--------------------------------------------------------------------------
    | Settings Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->controller(SettingsController::class)->group(function () {
        // Site Information
        Route::get('/site-information', 'siteInformationIndex')->name('site_settings');
        Route::put('/site-information/update', 'siteInformationUpdate')->name('site_settings.update');

        // About Company
        Route::get('/about-company', 'aboutCompanyIndex')->name('about-company');
        Route::put('/about-company/update', 'aboutCompanyUpdate')->name('about-company.update');

        // Training Objectives
        Route::prefix('objectives')->group(function () {
            Route::get('/', 'trainingObjectiveIndex')->name('objectives');
            Route::post('/store', 'trainingObjectiveStore')->name('objective.store');
            Route::put('/update', 'trainingObjectiveUpdate')->name('objective.update');
            Route::get('/destroy/{id}', 'trainingObjectiveDestroy')->name('objective.destroy');
        });

        // FAQs
        Route::prefix('faqs')->group(function () {
            Route::get('/', 'faqsIndex')->name('faqs');
            Route::post('/store', 'faqsStore')->name('faqs.store');
            Route::put('/update', 'faqsUpdate')->name('faqs.update');
            Route::get('/destroy/{id}', 'faqsDestroy')->name('faqs.destroy');
        });

        // Sliders
        Route::prefix('sliders')->group(function () {
            Route::get('/', 'slidersIndex')->name('sliders');
            Route::post('/store', 'sliderStore')->name('slider.store');
            Route::put('/update', 'sliderUpdate')->name('slider.update');
            Route::get('/destroy/{id}', 'slidersDestroy')->name('sliders.destroy');
        });

        // Other Settings
        Route::get('/founder', 'founderIndex')->name('founder');
        Route::put('/founder/update', 'founderUpdate')->name('founder.update');

        // Custom Services
        Route::prefix('custom-services')->group(function () {
            Route::get('/', 'customServicesIndex')->name('custom-services');
            Route::post('/store', 'customServicesStore')->name('custom-service.store');
            Route::put('/update', 'customServicesUpdate')->name('custom-service.update');
            Route::get('/destroy/{id}', 'customServicesDestroy')->name('custom-service.destroy');
        });

        // Clients
        Route::prefix('clients')->group(function () {
            Route::get('/', 'clientsIndex')->name('clients');
            Route::post('/store', 'clientsStore')->name('client.store');
            Route::get('/destroy/{id}', 'clientsDestroy')->name('client.destroy');
        });

        // Enrollment Batches
        Route::prefix('enrolment-batch')->group(function () {
            Route::get('/', 'enrolmentBatchIndex')->name('enrolment-batches');
            Route::post('/store', 'enrolmentBatchStore')->name('enrolment-batch.store');
            Route::get('/destroy/{id}', 'enrolmentBatchDestroy')->name('enrolment-batch.destroy');
            Route::get('/set-active/{id}', 'setActiveBatch')->name('enrolment-batch.update');
        });

        // Achievements
        Route::prefix('achievements')->group(function () {
            Route::get('/', 'achievementsIndex')->name('achievements');
            Route::post('/store', 'achievementsStore')->name('achievement.store');
            Route::put('/update', 'achievementsUpdate')->name('achievement.update');
            Route::get('/destroy/{id}', 'achievementsDestroy')->name('achievement.destroy');
        });

        // Email Configuration
        Route::get('/email-configuration', [EmailConfigController::class, 'index'])->name('email-config');
        Route::put('/email-configuration/update', [EmailConfigController::class, 'update'])->name('email-config.update');

        // Payment Gateway Configuration
        Route::prefix('payment-gateways')->group(function () {
            Route::get('/', [PaymentGatewayConfigController::class, 'index'])->name('payment-gateway-config');
            Route::put('/{id}', [PaymentGatewayConfigController::class, 'update'])->name('payment-gateway-config.update');
            Route::patch('/{id}/toggle', [PaymentGatewayConfigController::class, 'toggle'])->name('payment-gateway-config.toggle');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Forum Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('forum')->group(function () {
        Route::get('/{category?}', ForumList::class)->name('forum.list');
        Route::get('/create/post', ForumCreatePost::class)->name('forum.create');
        Route::get('/single/{postId}', ForumPostPage::class)->name('forum.post');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback Route
|--------------------------------------------------------------------------
*/
Route::fallback(function() {
    return view('pages.utility.404');
});

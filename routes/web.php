<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\{
    DashboardController,
    CheckoutController,
    ApplicationController,
    CourseController,
    UserController,
    SettingsController,
    WebsiteController,
    ChatController,
    TrainingScheduleController,
    TestimonialsController,
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
    StudentAnnouncementController
};

// Admin Controllers
use App\Http\Controllers\Admin\{
    AdmissionController,
    StaffController,
    RoleController,
    PaymentController,
    BatchController,
    ChangeRequestController,
    EnquiryController,
    NewsletterController,
    ContentController
};
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;

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

// Public Website Routes
Route::controller(WebsiteController::class)->group(function () {
    Route::get('/', 'home')->name('index');
    Route::get('/about-us', 'about')->name('about-us');
    Route::get('/courses', 'courses')->name('courses');
    Route::get('/course-information/{name}', 'course_details')->name('course');
    Route::get('/our-instructors', 'instructors')->name('our-instructors');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactStore')->name('contact.store')->middleware('throttle:5,10');
    Route::get('/privacy-policy', 'privacy')->name('privacy');
    Route::get('/terms-and-conditions', 'terms')->name('terms');
    Route::post('/email-subscription', 'email_subscription')->name('email-subscription')->middleware('throttle:10,10');
});

// Paystack server-to-server notifications (signature-verified, CSRF-exempt)
Route::post('/payment/paystack/webhook', [CheckoutController::class, 'webhook'])->name('payment.webhook');

// Printable (offline) application form, mirroring the online registration form
Route::get('/register/application-form', [RegistrationFormController::class, 'download'])->name('register.form-pdf');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified', 'not-suspended'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Payment Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('checkout')->controller(CheckoutController::class)->group(function () {
        Route::get('/pay', fn () => redirect()->route('application'))->name('checkout.pay');
        Route::post('/pay', 'redirectToPaystack')->name('payment')->middleware('portal:student');
        Route::get('/payment/callback', 'handleGatewayCallback')->name('payment.callback');
    });

    // Applicant's own application: progress, courses, documents
    Route::prefix('application')->controller(ApplicationController::class)->middleware('portal:student')->group(function () {
        Route::get('/', 'show')->name('application');
        Route::put('/courses', 'updateCourses')->name('application.courses');
        Route::post('/documents', 'uploadDocument')->name('application.documents.store');
        Route::delete('/documents/{document}', 'destroyDocument')->name('application.documents.destroy');
    });
    Route::get('/application/documents/{document}', [ApplicationController::class, 'downloadDocument'])->name('application.documents.show');

    /*
    |--------------------------------------------------------------------------
    | Dashboard & Common Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/chats', [ChatController::class, 'chatIndex'])->name('chats');
    Route::get('/user-profile', [UserController::class, 'userProfile'])->name('user-profile');

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('student')->name('student.')->middleware(['portal:student', 'enrolled'])->group(function () {
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
    Route::prefix('instructor')->name('instructor.')->middleware('portal:instructor')->group(function () {
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
    | Every route below is guarded by a permission from config/permissions.php.
    */
    Route::prefix('admin')->group(function () {
        // Admissions: applicants and students
        Route::prefix('admissions')->name('admin.admissions.')->controller(AdmissionController::class)->group(function () {
            Route::get('/', 'index')->name('index')->middleware('permission:read_applicants|read_student_accounts');
            Route::get('/export', 'export')->name('export')->middleware('permission:export_students');
            Route::get('/{student}', 'show')->name('show')->middleware('permission:read_applicants|read_student_accounts');
            Route::post('/{student}/payment', 'recordPayment')->name('payment')->middleware('permission:create_payments');
            Route::middleware('permission:update_applicants')->group(function () {
                Route::put('/{student}/courses', 'updateCourses')->name('courses');
                Route::put('/{student}/batch', 'updateBatch')->name('batch');
                Route::put('/{student}/instructor', 'assignInstructor')->name('instructor');
                Route::put('/{student}/documents/{document}', 'reviewDocument')->name('document');
            });
        });

        // User Management
        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', fn () => redirect()->route('admin.admissions.index', ['status' => 'enrolled']))->name('users')->middleware('permission:read_student_accounts|read_applicants');
            Route::patch('/deactivate/{id}', 'deactivate')->name('user.deactivate')->middleware('permission:update_suspend_user_account');
            Route::patch('/verify/{id}', 'verifyAccount')->name('user.verify')->middleware('permission:update_verify_user_account');
        });

        // Staff
        Route::controller(StaffController::class)->prefix('staff')->group(function () {
            Route::get('/', 'index')->name('instructors')->middleware('permission:read_instructors');
            Route::post('/', 'store')->name('instructor.store')->middleware('permission:create_instructors');
            Route::get('/{staff}/edit', 'edit')->name('admin.staff.edit')->middleware('permission:update_instructors');
            Route::put('/{staff}', 'update')->name('admin.staff.update')->middleware('permission:update_instructors');
            Route::post('/{staff}/password-link', 'sendPasswordLink')->name('admin.staff.password')->middleware('permission:update_instructors');
        });

        // Roles & permissions
        Route::controller(RoleController::class)->prefix('roles')->group(function () {
            Route::get('/', 'index')->name('user-roles')->middleware('permission:read_roles_and_permissions');
            Route::post('/', 'store')->name('user-roles.store')->middleware('permission:create_roles_and_permissions');
            Route::get('/{role}', 'edit')->name('user-permissions')->middleware('permission:read_roles_and_permissions');
            Route::put('/{role}', 'update')->name('admin.roles.update')->middleware('permission:update_roles_and_permissions');
            Route::put('/{role}/permissions', 'updatePermissions')->name('admin.roles.permissions')->middleware('permission:update_roles_and_permissions');
            Route::put('/{role}/courses', 'updateCourses')->name('admin.roles.courses')->middleware('permission:update_role_course_permission');
            Route::delete('/{role}', 'destroy')->name('user-roles.destroy')->middleware('permission:delete_roles_and_permissions');
        });

        // Payments
        Route::controller(PaymentController::class)->prefix('payments')->group(function () {
            Route::get('/', 'index')->name('payments')->middleware('permission:read_payments');
            Route::get('/export', 'export')->name('admin.payments.export')->middleware('permission:export_payments');
        });

        // Intake batches
        Route::controller(BatchController::class)->prefix('batches')->group(function () {
            Route::get('/', 'index')->name('enrolment-batches')->middleware('permission:read_enrolment_batches|manage_enrolment_batches');
            Route::middleware('permission:manage_enrolment_batches')->group(function () {
                Route::post('/', 'store')->name('enrolment-batch.store');
                Route::put('/{batch}', 'update')->name('admin.batches.update');
                Route::patch('/{batch}/activate', 'activate')->name('enrolment-batch.update');
                Route::delete('/{batch}', 'destroy')->name('enrolment-batch.destroy');
            });
        });

        // Schedule change requests from instructors
        Route::controller(ChangeRequestController::class)->prefix('change-requests')->name('admin.change-requests.')->middleware('permission:manage_schedule_change_requests')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/{changeRequest}', 'update')->name('update');
        });

        // School-wide announcements
        Route::controller(AdminAnnouncementController::class)->prefix('announcements')->name('admin.announcements.')->middleware('permission:manage_announcements')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{announcement}', 'update')->name('update');
            Route::patch('/{announcement}/toggle', 'toggle')->name('toggle');
            Route::delete('/{announcement}', 'destroy')->name('destroy');
        });

        // Website enquiries
        Route::controller(EnquiryController::class)->prefix('enquiries')->name('admin.enquiries.')->middleware('permission:read_contact_messages')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{enquiry}', 'show')->name('show');
            Route::patch('/{enquiry}', 'update')->name('update');
            Route::delete('/{enquiry}', 'destroy')->name('destroy');
        });

        // Newsletter subscribers
        Route::controller(NewsletterController::class)->prefix('newsletter')->group(function () {
            Route::get('/', 'index')->name('newsletter')->middleware('permission:read_newsletter');
            Route::get('/export', 'export')->name('admin.newsletter.export')->middleware('permission:read_newsletter');
            Route::delete('/{subscriber}', 'destroy')->name('admin.newsletter.destroy')->middleware('permission:delete_newsletter');
        });

        // Website content hub
        Route::get('/content', [ContentController::class, 'index'])->name('admin.content')->middleware('permission:update_website_management');

        // Testimonials Management
        Route::controller(TestimonialsController::class)->prefix('testimonials')->group(function () {
            Route::get('/', 'index')->name('testimonials')->middleware('permission:read_testimonials');
            Route::post('/store', 'store')->name('testimonial.store')->middleware('permission:create_testimonials');
            Route::put('/update', 'update')->name('testimonial.update')->middleware('permission:update_testimonials');
            Route::delete('/destroy/{id}', 'destroy')->name('testimonial.destroy')->middleware('permission:delete_testimonials');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Course Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('course')->controller(CourseController::class)->group(function () {
        Route::middleware('permission:read_course_management')->group(function () {
            Route::get('/management', 'index')->name('course-management');
            Route::get('/details/{id}', 'show')->name('course-details');
            Route::get('/material/download/{id}', 'downloadMaterial')->name('materials.download');
        });

        Route::middleware('permission:update_course_management')->group(function () {
            Route::put('/details/update', 'updateCourseDetails')->name('course-details.update');
            Route::post('/material/store', 'uploadCourseMaterials')->name('upload-course-materials');
            Route::delete('/material/destroy/{id}', 'materialDestroy')->name('material.delete');
            Route::post('/curriculum/store', 'curriculumStore')->name('curriculum.store');
            Route::delete('/curriculum/destroy/{id}', 'curriculumDestroy')->name('curriculum.destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Training Schedule Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('training-schedule')->controller(TrainingScheduleController::class)->group(function () {
        Route::get('/', 'index')->name('schedule')->middleware('permission:read_training_schedule');
        Route::post('/create', 'create')->name('schedule.create')->middleware('permission:create_training_schedule');
        Route::delete('/{id}', 'destroy')->name('schedule.destroy')->middleware('permission:delete_training_schedule');

        Route::middleware('permission:create_training_schedule|update_training_schedule')->group(function () {
            Route::get('/topics/{id}', 'getTopics');
            Route::get('/instructors/{course_id}', 'getInstructors');
            Route::get('/instructor-students/{batch_id}/{course_id}/{instructor_id}', 'getInstructorStudents');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Settings Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->controller(SettingsController::class)->group(function () {
        // Site Information
        Route::middleware('permission:update_site_settings')->group(function () {
            Route::get('/site-information', 'siteInformationIndex')->name('site_settings');
            Route::put('/site-information/update', 'siteInformationUpdate')->name('site_settings.update');
        });

        // Course catalogue (training objectives) and pricing
        Route::prefix('objectives')->middleware('permission:manage_course_catalogue')->group(function () {
            Route::get('/', 'trainingObjectiveIndex')->name('objectives');
            Route::post('/store', 'trainingObjectiveStore')->name('objective.store');
            Route::put('/update', 'trainingObjectiveUpdate')->name('objective.update');
            Route::delete('/destroy/{id}', 'trainingObjectiveDestroy')->name('objective.destroy');
        });

        // Website content
        Route::middleware('permission:update_website_management')->group(function () {
            Route::get('/about-company', 'aboutCompanyIndex')->name('about-company');
            Route::put('/about-company/update', 'aboutCompanyUpdate')->name('about-company.update');

            Route::prefix('faqs')->group(function () {
                Route::get('/', 'faqsIndex')->name('faqs');
                Route::post('/store', 'faqsStore')->name('faqs.store');
                Route::put('/update', 'faqsUpdate')->name('faqs.update');
                Route::delete('/destroy/{id}', 'faqsDestroy')->name('faqs.destroy');
            });

            Route::prefix('sliders')->group(function () {
                Route::get('/', 'slidersIndex')->name('sliders');
                Route::post('/store', 'sliderStore')->name('slider.store');
                Route::put('/update', 'sliderUpdate')->name('slider.update');
                Route::delete('/destroy/{id}', 'slidersDestroy')->name('sliders.destroy');
            });

            Route::get('/founder', 'founderIndex')->name('founder');
            Route::put('/founder/update', 'founderUpdate')->name('founder.update');

            Route::prefix('custom-services')->group(function () {
                Route::get('/', 'customServicesIndex')->name('custom-services');
                Route::post('/store', 'customServicesStore')->name('custom-service.store');
                Route::put('/update', 'customServicesUpdate')->name('custom-service.update');
                Route::delete('/destroy/{id}', 'customServicesDestroy')->name('custom-service.destroy');
            });

            Route::prefix('clients')->group(function () {
                Route::get('/', 'clientsIndex')->name('clients');
                Route::post('/store', 'clientsStore')->name('client.store');
                Route::delete('/destroy/{id}', 'clientsDestroy')->name('client.destroy');
            });

            Route::prefix('achievements')->group(function () {
                Route::get('/', 'achievementsIndex')->name('achievements');
                Route::post('/store', 'achievementsStore')->name('achievement.store');
                Route::put('/update', 'achievementsUpdate')->name('achievement.update');
                Route::delete('/destroy/{id}', 'achievementsDestroy')->name('achievement.destroy');
            });
        });

        // Email Configuration
        Route::middleware('permission:update_email_configuration')->group(function () {
            Route::get('/email-configuration', [EmailConfigController::class, 'index'])->name('email-config');
            Route::put('/email-configuration/update', [EmailConfigController::class, 'update'])->name('email-config.update');
        });

        // Payment Gateway Configuration
        Route::prefix('payment-gateways')->middleware('permission:update_payment_configuration')->group(function () {
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
Route::fallback(function () {
    abort(404);
});

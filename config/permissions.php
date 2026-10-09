<?php

/*
|--------------------------------------------------------------------------
| Permission catalogue
|--------------------------------------------------------------------------
|
| Every permission the application checks, grouped for the Roles &
| Permissions screen. Names are stable identifiers stored in the
| `permissions` table; labels are what administrators see.
|
| Permissions are enforced on the server through Gates (see
| AuthServiceProvider) and the `permission:` route middleware.
| SuperAdmin bypasses every check.
|
*/

return [

    'groups' => [

        'Dashboard' => [
            'read_dashboard_user_card' => 'See student statistics',
            'read_dashboard_revenue_card' => 'See revenue statistics',
            'read_dashboard_instructor_card' => 'See staff statistics',
        ],

        'Admissions & Students' => [
            'read_applicants' => 'View applicants and their applications',
            'update_applicants' => 'Manage applications (courses, batch, record offline payment)',
            'read_student_accounts' => 'View enrolled students',
            'read_suspend_user_account' => 'View suspended accounts',
            'update_suspend_user_account' => 'Suspend and reactivate accounts',
            'update_verify_user_account' => 'Verify account email addresses',
            'export_students' => 'Export applicant and student lists',
        ],

        'Enrolment Batches' => [
            'read_enrolment_batches' => 'View enrolment batches',
            'manage_enrolment_batches' => 'Create, edit, activate and delete batches',
        ],

        'Staff' => [
            'read_instructors' => 'View staff and instructors',
            'create_instructors' => 'Add staff accounts',
            'update_instructors' => 'Edit staff accounts and their roles',
            'delete_instructors' => 'Remove staff accounts',
        ],

        'Roles & Permissions' => [
            'read_roles_and_permissions' => 'View roles and permissions',
            'create_roles_and_permissions' => 'Create roles',
            'update_roles_and_permissions' => 'Change role permissions',
            'delete_roles_and_permissions' => 'Delete roles',
            'update_role_course_permission' => 'Assign courses to instructor roles',
        ],

        'Courses' => [
            'read_course_management' => 'View courses, curriculum and materials',
            'update_course_management' => 'Edit course content, curriculum and materials',
            'manage_course_catalogue' => 'Create and delete courses, set prices',
        ],

        'Training Schedule' => [
            'read_training_schedule' => 'View the training schedule',
            'create_training_schedule' => 'Create training sessions',
            'update_training_schedule' => 'Edit training sessions',
            'delete_training_schedule' => 'Delete training sessions',
            'manage_schedule_change_requests' => 'Approve or reject schedule change requests',
            'read_attendance_reports' => 'View attendance and progress reports',
        ],

        'Finance' => [
            'read_payments' => 'View payments',
            'create_payments' => 'Record offline payments',
            'export_payments' => 'Export payments',
        ],

        'Communication' => [
            'manage_announcements' => 'Publish school-wide announcements',
            'read_newsletter' => 'View newsletter subscribers',
            'create_newsletter' => 'Add newsletter subscribers',
            'delete_newsletter' => 'Remove newsletter subscribers',
            'read_contact_messages' => 'Read contact form enquiries',
        ],

        'Website' => [
            'update_website_management' => 'Edit website content (pages, sliders, FAQs, services)',
            'read_testimonials' => 'View testimonials',
            'create_testimonials' => 'Add testimonials',
            'update_testimonials' => 'Edit testimonials',
            'delete_testimonials' => 'Delete testimonials',
        ],

        'System' => [
            'update_site_settings' => 'Edit site information and branding',
            'update_email_configuration' => 'Configure the email server',
            'update_payment_configuration' => 'Configure payment gateways',
        ],

        'Community' => [
            'read_chats' => 'Use chats',
            'read_calendar' => 'View the calendar',
            'read_forum' => 'Read the forum',
            'create_forum' => 'Post in the forum',
            'update_forum' => 'Moderate forum posts',
            'delete_forum' => 'Delete forum posts',
        ],

        'Legacy' => [
            'read_management_navigation' => 'Show management navigation (legacy)',
        ],
    ],

    /*
    | Defaults applied once by migration to roles that had no permissions.
    | Roles that already have permissions are never changed automatically.
    */
    'defaults' => [
        'Admin' => ['*', '!create_roles_and_permissions', '!update_roles_and_permissions', '!delete_roles_and_permissions', '!update_email_configuration', '!update_payment_configuration'],
        'Lead Instructor' => [
            'read_dashboard_user_card', 'read_student_accounts', 'read_applicants', 'read_enrolment_batches',
            'read_instructors', 'read_course_management', 'update_course_management',
            'read_training_schedule', 'create_training_schedule', 'update_training_schedule', 'delete_training_schedule',
            'manage_schedule_change_requests', 'read_attendance_reports', 'manage_announcements',
        ],
    ],

];

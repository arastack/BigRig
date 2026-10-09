<?php

namespace App\Support;

use App\Models\Announcement;
use App\Models\ContactMessage;
use App\Models\Role;
use App\Models\ScheduleChangeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * The sidebar menu for each portal. Items are only shown when the user holds the
 * permission the matching route enforces, so the menu never offers a dead end.
 */
class PortalNavigation
{
    public static function for(User $user): array
    {
        $sections = match ($user->portal()) {
            Role::PORTAL_STUDENT => self::student($user),
            Role::PORTAL_INSTRUCTOR => array_merge(self::instructor(), self::admin($user, management: true)),
            default => self::admin($user),
        };

        $sections[] = ['title' => 'Community', 'items' => [
            ['label' => 'Messages', 'route' => 'chats', 'icon' => 'message', 'can' => 'read_chats'],
            ['label' => 'Forum', 'route' => 'forum.list', 'icon' => 'users', 'active' => ['forum.*'], 'can' => 'read_forum'],
        ]];

        // Keep only items the user may open, and sections that still have items
        return collect($sections)
            ->map(function ($section) use ($user) {
                $section['items'] = collect($section['items'])
                    ->filter(fn ($item) => Route::has($item['route']) && (! isset($item['can']) || $user->hasAnyPermission((array) $item['can'])))
                    ->values()
                    ->all();

                return $section;
            })
            ->filter(fn ($section) => $section['items'])
            ->values()
            ->all();
    }

    private static function admin(User $user, bool $management = false): array
    {
        $unreadEnquiries = $user->hasPermission('read_contact_messages') ? once(fn () => ContactMessage::unread()->count()) : 0;
        $pendingChanges = $user->hasPermission('manage_schedule_change_requests') ? once(fn () => ScheduleChangeRequest::where('status', 'pending')->count()) : 0;

        $sections = [
            ['title' => $management ? 'Management' : null, 'items' => array_filter([
                $management ? null : ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'gauge'],
            ])],
            ['title' => 'Admissions', 'items' => [
                ['label' => 'Applicants', 'route' => 'admin.admissions.index', 'params' => ['status' => 'awaiting_payment'], 'icon' => 'file-text', 'can' => 'read_applicants', 'active' => ['admin.admissions.*'], 'query' => ['status' => 'awaiting_payment']],
                ['label' => 'Students', 'route' => 'admin.admissions.index', 'params' => ['status' => 'enrolled'], 'icon' => 'graduation', 'can' => ['read_student_accounts', 'read_applicants'], 'active' => ['admin.admissions.*'], 'query' => ['status' => 'enrolled']],
                ['label' => 'Admissions settings', 'route' => 'admin.admission-settings', 'icon' => 'clipboard', 'can' => 'manage_admission_settings'],
                ['label' => 'Intake batches', 'route' => 'enrolment-batches', 'icon' => 'calendar', 'can' => ['read_enrolment_batches', 'manage_enrolment_batches']],
            ]],
            ['title' => 'Training', 'items' => [
                ['label' => 'Courses', 'route' => 'course-management', 'icon' => 'book', 'can' => 'read_course_management', 'active' => ['course-management', 'course-details']],
                ['label' => 'Schedule', 'route' => 'schedule', 'icon' => 'clock', 'can' => 'read_training_schedule'],
                ['label' => 'Attendance', 'route' => 'admin.attendance', 'icon' => 'clipboard', 'can' => 'read_attendance_reports'],
                ['label' => 'Change requests', 'route' => 'admin.change-requests.index', 'icon' => 'refresh', 'can' => 'manage_schedule_change_requests', 'badge' => $pendingChanges],
                ['label' => 'Announcements', 'route' => 'admin.announcements.index', 'icon' => 'megaphone', 'can' => 'manage_announcements', 'active' => ['admin.announcements.*']],
            ]],
            ['title' => 'Finance', 'items' => [
                ['label' => 'Payments', 'route' => 'payments', 'icon' => 'credit-card', 'can' => 'read_payments'],
            ]],
            ['title' => 'People', 'items' => [
                ['label' => 'Staff', 'route' => 'instructors', 'icon' => 'id-card', 'can' => 'read_instructors', 'active' => ['instructors', 'admin.staff.*']],
                ['label' => 'Roles & permissions', 'route' => 'user-roles', 'icon' => 'shield', 'can' => 'read_roles_and_permissions', 'active' => ['user-roles', 'user-permissions']],
            ]],
            ['title' => 'Engagement', 'items' => [
                ['label' => 'Enquiries', 'route' => 'admin.enquiries.index', 'icon' => 'inbox', 'can' => 'read_contact_messages', 'badge' => $unreadEnquiries, 'active' => ['admin.enquiries.*']],
                ['label' => 'Newsletter', 'route' => 'newsletter', 'icon' => 'mail', 'can' => 'read_newsletter'],
                ['label' => 'Testimonials', 'route' => 'testimonials', 'icon' => 'star', 'can' => 'read_testimonials'],
            ]],
            ['title' => 'Website', 'items' => [
                ['label' => 'Courses & fees', 'route' => 'objectives', 'icon' => 'truck', 'can' => 'manage_course_catalogue', 'active' => ['objectives', 'packages']],
                ['label' => 'Pages & content', 'route' => 'admin.content', 'icon' => 'globe', 'can' => 'update_website_management', 'active' => ['admin.content', 'admin.page-text', 'about-company', 'founder', 'sliders', 'custom-services', 'faqs', 'clients', 'achievements']],
            ]],
            ['title' => 'System', 'items' => [
                ['label' => 'Site settings', 'route' => 'site_settings', 'icon' => 'building', 'can' => 'update_site_settings'],
                ['label' => 'Email server', 'route' => 'email-config', 'icon' => 'send', 'can' => 'update_email_configuration'],
                ['label' => 'Payment gateways', 'route' => 'payment-gateway-config', 'icon' => 'key', 'can' => 'update_payment_configuration'],
            ]],
        ];

        if ($management) {
            // Instructors see management tools under one heading, after their own menu
            $items = collect($sections)->flatMap(fn ($s) => $s['items'])->all();

            return [['title' => 'Management', 'items' => $items]];
        }

        return $sections;
    }

    private static function instructor(): array
    {
        return [
            ['title' => null, 'items' => [
                ['label' => 'Dashboard', 'route' => 'instructor.dashboard', 'icon' => 'gauge'],
            ]],
            ['title' => 'Teaching', 'items' => [
                ['label' => 'My students', 'route' => 'instructor.students', 'icon' => 'graduation', 'active' => ['instructor.students', 'instructor.course.students', 'instructor.student.show']],
                ['label' => 'Schedule', 'route' => 'instructor.schedule', 'icon' => 'calendar', 'active' => ['instructor.schedule', 'instructor.schedule.calendar', 'instructor.schedule.show', 'instructor.schedule.request-change']],
                ['label' => 'Attendance', 'route' => 'instructor.attendance', 'icon' => 'clipboard', 'active' => ['instructor.attendance*']],
                ['label' => 'Materials', 'route' => 'instructor.materials', 'icon' => 'book', 'active' => ['instructor.materials*']],
                ['label' => 'Announcements', 'route' => 'instructor.announcements', 'icon' => 'megaphone', 'active' => ['instructor.announcements*']],
                ['label' => 'My change requests', 'route' => 'instructor.schedule.change-requests', 'icon' => 'refresh'],
            ]],
        ];
    }

    private static function student(User $user): array
    {
        $unread = once(fn () => Announcement::visibleTo($user)
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $user->id))
            ->count());

        return [
            ['title' => null, 'items' => [
                ['label' => 'Dashboard', 'route' => 'student.dashboard', 'icon' => 'gauge'],
                ['label' => 'My application', 'route' => 'application', 'icon' => 'file-text'],
            ]],
            ['title' => 'Learning', 'items' => [
                ['label' => 'My courses', 'route' => 'student.courses', 'icon' => 'book', 'active' => ['student.courses', 'student.course-details']],
                ['label' => 'Schedule', 'route' => 'student.schedule', 'icon' => 'calendar', 'active' => ['student.schedule*']],
                ['label' => 'Materials', 'route' => 'student.materials', 'icon' => 'download'],
                ['label' => 'Attendance', 'route' => 'student.attendance', 'icon' => 'clipboard'],
                ['label' => 'Progress', 'route' => 'student.progress', 'icon' => 'gauge', 'active' => ['student.progress*']],
                ['label' => 'Announcements', 'route' => 'student.announcements', 'icon' => 'megaphone', 'active' => ['student.announcement*'], 'badge' => $unread],
            ]],
        ];
    }
}

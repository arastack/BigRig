@section('title', $staff->full_name)
@php
    $me = auth()->user();
    $isSelf = $staff->is($me);
    $teaches = $staff->roles->flatMap(fn ($role) => $coursesByRole[$role->id] ?? [])->unique();
@endphp
<x-app-layout>
    <x-portal.page :title="$staff->full_name" subtitle="Update this person's details, roles and website listing." :back="route('instructors')">
        <x-slot:actions>
            <x-portal.status :status="$staff->isSuspended() ? 'suspended' : 'active'" class="px-3 py-1 text-sm" />
            @if ($canEditDetails)
                <form method="POST" action="{{ route('admin.staff.password', $staff) }}" onsubmit="return confirm(@js('Email a password reset link to ' . $staff->email . '?'))">
                    @csrf
                    <button class="btn-ghost btn-sm"><x-site.icon name="key" class="h-4 w-4" /> Send password reset link</button>
                </form>
            @endif
            @can('update_suspend_user_account')
                @if (! $isSelf && $canEditDetails)
                    <form method="POST" action="{{ route('user.deactivate', $staff->id) }}" onsubmit="return confirm(@js($staff->isSuspended() ? 'Reactivate this account? They will be able to sign in again.' : 'Suspend this account? They will be signed out, unable to sign in and removed from the website.'))">
                        @csrf @method('PATCH')
                        <button @class(['btn-sm', 'btn-ghost' => $staff->isSuspended(), 'btn bg-white border-red-200 text-red-600 hover:border-red-400' => ! $staff->isSuspended()])>
                            <x-site.icon :name="$staff->isSuspended() ? 'refresh' : 'ban'" class="h-4 w-4" /> {{ $staff->isSuspended() ? 'Reactivate' : 'Suspend' }}
                        </button>
                    </form>
                @endif
            @endcan
        </x-slot:actions>

        @unless ($canEditDetails)
            <p class="mb-6 flex items-center gap-2 rounded-xl border border-brand-500 bg-brand-50 px-4 py-3 text-sm font-medium text-ink-900">
                <x-site.icon name="shield" class="h-4 w-4" /> This is a SuperAdmin account. Only another SuperAdmin can change it.
            </p>
        @endunless

        <form method="POST" action="{{ route('admin.staff.update', $staff) }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-3">
            @csrf @method('PUT')
            <fieldset class="contents" @disabled(! $canEditDetails)>
                <div class="space-y-6 xl:col-span-2">
                    {{-- Details --}}
                    <section class="portal-card">
                        <div class="portal-card-header"><h2 class="portal-card-title">Details</h2></div>
                        <div class="space-y-4 p-5">
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="portal-label" for="firstName">First name</label>
                                    <input id="firstName" name="firstName" value="{{ old('firstName', $staff->firstName) }}" required maxlength="255" class="portal-input">
                                </div>
                                <div>
                                    <label class="portal-label" for="middleName">Middle name</label>
                                    <input id="middleName" name="middleName" value="{{ old('middleName', $staff->middleName) }}" maxlength="255" class="portal-input">
                                </div>
                                <div>
                                    <label class="portal-label" for="lastName">Last name</label>
                                    <input id="lastName" name="lastName" value="{{ old('lastName', $staff->lastName) }}" required maxlength="255" class="portal-input">
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="portal-label" for="gender">Gender</label>
                                    <select id="gender" name="gender" required class="portal-input">
                                        <option value="">Choose…</option>
                                        @foreach (['Male', 'Female'] as $gender)<option @selected(old('gender', $staff->gender) === $gender)>{{ $gender }}</option>@endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="portal-label" for="email">Email</label>
                                    <input id="email" type="email" name="email" value="{{ old('email', $staff->email) }}" required maxlength="255" class="portal-input">
                                </div>
                                <div>
                                    <label class="portal-label" for="mobileNumber">Phone</label>
                                    <input id="mobileNumber" type="tel" name="mobileNumber" value="{{ old('mobileNumber', $staff->mobileNumber) }}" required maxlength="17" class="portal-input">
                                </div>
                            </div>
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                <x-portal.avatar :user="$staff" size="h-16 w-16" />
                                <div class="flex-1">
                                    <label class="portal-label" for="profile_photo">{{ $staff->profile_photo_path ? 'Replace photo' : 'Photo' }} <span class="normal-case tracking-normal text-ink-400">(JPG or PNG up to 2 MB)</span></label>
                                    <input id="profile_photo" type="file" name="profile_photo" accept="image/jpeg,image/png" class="block w-full text-sm text-ink-600 file:mr-3 file:rounded-lg file:border-0 file:bg-ink-950 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white">
                                    @if ($staff->profile_photo_path)
                                        <label class="mt-2 flex items-center gap-2 text-sm text-ink-600"><input type="checkbox" name="remove_photo" value="1" class="rounded border-ink-300 text-ink-950 focus:ring-brand-400"> Remove current photo</label>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Roles --}}
                    <section class="portal-card">
                        <div class="portal-card-header">
                            <h2 class="portal-card-title">Roles</h2>
                            @can('read_roles_and_permissions')<a href="{{ route('user-roles') }}" class="text-sm font-semibold text-ink-600 underline decoration-brand-400 decoration-2 underline-offset-4">Manage roles</a>@endcan
                        </div>
                        <div class="space-y-3 p-5">
                            <p class="text-sm text-ink-500">Roles decide what this person can see and do. Instructor roles linked to a course are used to assign new students automatically.</p>
                            @if ($canEditRoles)<input type="hidden" name="roles_submitted" value="1">@endif
                            @unless ($canEditRoles)
                                <p class="rounded-lg bg-ink-50 px-3 py-2 text-xs text-ink-600">{{ $isSelf ? 'You cannot change your own roles. Ask a SuperAdmin.' : 'Only a SuperAdmin can change the roles of a SuperAdmin.' }}</p>
                            @endunless
                            @include('admin.staff._roles', ['roles' => $roles, 'selected' => old('userRole', $staff->roles->pluck('id')->all()), 'locked' => ! $canEditRoles])
                        </div>
                    </section>
                </div>

                <div class="space-y-6">
                    {{-- Website --}}
                    <section class="portal-card p-5">
                        <h2 class="portal-card-title">Public website</h2>
                        <label class="mt-3 flex items-start gap-3 text-sm">
                            <input type="checkbox" name="website_visibility" value="1" @checked(old('website_visibility', $staff->website_visibility)) @disabled($staff->isSuspended())
                                class="mt-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                            <span>
                                <span class="block font-semibold text-ink-950">Show on the public website</span>
                                <span class="block text-xs text-ink-500">{{ $staff->isSuspended() ? 'Suspended accounts are never shown on the website.' : 'Lists their name, photo and role on the Our Instructors page.' }}</span>
                            </span>
                        </label>
                    </section>

                    {{-- Save --}}
                    @if ($canEditDetails)
                        <div class="portal-card p-5">
                            <button class="btn-brand w-full">Save changes</button>
                        </div>
                    @endif

                    {{-- Account --}}
                    <section class="portal-card p-5 text-sm">
                        <h2 class="portal-card-title">Account</h2>
                        <dl class="mt-3 space-y-2">
                            <div class="flex justify-between gap-3"><dt class="text-ink-500">Joined</dt><dd>{{ $staff->created_at?->format('j M Y') ?? '—' }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-ink-500">Email verified</dt><dd>{{ $staff->email_verified_at ? $staff->email_verified_at->format('j M Y') : 'No' }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-ink-500">Signs in to</dt><dd>{{ $staff->portal() === 'admin' ? 'Administration' : 'Instructor portal' }}</dd></div>
                            @if ($staff->isSuspended())
                                <div class="flex justify-between gap-3"><dt class="text-ink-500">Suspended</dt><dd>{{ $staff->suspended_at->format('j M Y') }}</dd></div>
                            @endif
                        </dl>
                    </section>
                </div>
            </fieldset>
        </form>

        {{-- Teaching --}}
        @if ($teaches->isNotEmpty() || $students->isNotEmpty())
            <section class="portal-card mt-6">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">Assigned students</h2>
                    <span class="text-sm text-ink-500">Teaches {{ $teaches->join(', ') ?: 'no linked courses' }}</span>
                </div>
                @if ($students->isEmpty())
                    <x-portal.empty icon="graduation" title="No students assigned yet" text="Students are assigned when they pay for a course this person's role teaches." />
                @else
                    <div class="overflow-x-auto">
                        <table class="portal-table">
                            <thead><tr><th scope="col">Student</th><th scope="col">Course</th><th scope="col">Assigned</th></tr></thead>
                            <tbody>
                                @foreach ($students as $row)
                                    <tr>
                                        <td>
                                            @if ($row->student)
                                                @can('read_student_accounts')
                                                    <a href="{{ route('admin.admissions.show', $row->student) }}" class="flex items-center gap-3 font-semibold text-ink-950 hover:underline"><x-portal.avatar :user="$row->student" size="h-8 w-8" /> {{ $row->student->firstName }} {{ $row->student->lastName }}</a>
                                                @else
                                                    <span class="flex items-center gap-3 font-semibold text-ink-950"><x-portal.avatar :user="$row->student" size="h-8 w-8" /> {{ $row->student->firstName }} {{ $row->student->lastName }}</span>
                                                @endcan
                                            @else
                                                <span class="text-ink-400">Deleted student</span>
                                            @endif
                                        </td>
                                        <td>{{ $row->course?->objective ?? '—' }}</td>
                                        <td class="whitespace-nowrap text-ink-500">{{ $row->created_at?->format('j M Y') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endif
    </x-portal.page>
</x-app-layout>

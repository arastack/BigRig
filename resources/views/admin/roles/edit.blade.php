@section('title', $role->role_name)
@php
    $me = auth()->user();
    $canUpdate = $me->can('update_roles_and_permissions');
    $canEditPermissions = $permissionError === null;
    $isInstructorRole = $role->portal === \App\Models\Role::PORTAL_INSTRUCTOR;
    $portalBadge = ['admin' => 'badge-dark', 'instructor' => 'badge-yellow', 'student' => 'badge-grey'];
@endphp
<x-app-layout>
    <x-portal.page :title="$role->role_name" :subtitle="$role->role_description ?: 'Choose what people with this role can see and do.'" :back="route('user-roles')">
        <x-slot:actions>
            <span class="{{ $portalBadge[$role->portal] ?? 'badge-grey' }} px-3 py-1 text-sm">{{ \App\Models\Role::PORTALS[$role->portal] ?? $role->portal }}</span>
            @can('delete_roles_and_permissions')
                @if (! $role->isProtected() && $memberCount === 0)
                    <form method="POST" action="{{ route('user-roles.destroy', $role) }}" onsubmit="return confirm(@js('Delete the ' . $role->role_name . ' role? This cannot be undone.'))">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm border-red-200 bg-white text-red-600 hover:border-red-400"><x-site.icon name="trash" class="h-4 w-4" /> Delete role</button>
                    </form>
                @endif
            @endcan
        </x-slot:actions>

        <div class="grid gap-6 xl:grid-cols-3">
            {{-- Permissions --}}
            <div class="space-y-6 xl:col-span-2">
                <section class="portal-card">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Permissions</h2>
                        @if (! $role->isSuperAdmin())
                            <span class="text-sm text-ink-500">{{ count($granted) }} of {{ $groups->sum(fn ($items) => count($items)) }} granted</span>
                        @endif
                    </div>

                    @if ($role->isSuperAdmin())
                        <div class="flex items-start gap-3 p-5">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-ink-950 text-brand-400"><x-site.icon name="shield" class="h-5 w-5" /></span>
                            <div class="text-sm">
                                <p class="font-semibold text-ink-950">SuperAdmin can do everything.</p>
                                <p class="mt-1 text-ink-500">This role automatically has every permission, including any added in future, so there is nothing to tick here. Give it only to people who own the system.</p>
                            </div>
                        </div>
                    @else
                        @if ($permissionError && $canUpdate)
                            <p class="mx-5 mt-5 flex items-center gap-2 rounded-lg bg-brand-50 px-3 py-2 text-sm font-medium text-ink-900"><x-site.icon name="alert" class="h-4 w-4 text-brand-700" /> {{ $permissionError }}</p>
                        @endif
                        <form method="POST" action="{{ route('admin.roles.permissions', $role) }}">
                            @csrf @method('PUT')
                            <div class="divide-y divide-ink-100">
                                @foreach ($groups as $groupName => $items)
                                    <fieldset class="p-5" @disabled(! $canEditPermissions)
                                        x-data="{ all: false, some: false, sync() { const boxes = [...this.$root.querySelectorAll('[data-perm]')]; this.all = boxes.every(c => c.checked); this.some = ! this.all && boxes.some(c => c.checked) } }"
                                        x-init="sync()">
                                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                            <legend class="font-semibold text-ink-950">{{ $groupName }}</legend>
                                            @if ($canEditPermissions)
                                                <label class="flex items-center gap-2 text-xs font-semibold text-ink-500">
                                                    <input type="checkbox" :checked="all" :indeterminate="some"
                                                        @change="$root.querySelectorAll('[data-perm]').forEach(c => c.checked = $event.target.checked); sync()"
                                                        class="rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                                    Select all
                                                </label>
                                            @endif
                                        </div>
                                        <div class="grid gap-2 md:grid-cols-2">
                                            @foreach ($items as $permission)
                                                <label @class(['flex items-start gap-3 rounded-lg border border-ink-100 p-3 has-[:checked]:border-ink-950 has-[:checked]:bg-brand-50', 'cursor-not-allowed' => ! $canEditPermissions])>
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission['id'] }}" data-perm @change="sync()"
                                                        @checked(in_array($permission['id'], $granted))
                                                        class="mt-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                                    <span class="min-w-0">
                                                        <span class="block text-sm font-medium text-ink-900">{{ $permission['label'] }}</span>
                                                        <span class="block break-all font-mono text-[11px] text-ink-400">{{ $permission['name'] }}</span>
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                @endforeach
                            </div>
                            @if ($canEditPermissions)
                                <div class="sticky bottom-0 flex flex-wrap items-center justify-between gap-3 rounded-b-xl border-t border-ink-100 bg-white/95 px-5 py-4 backdrop-blur">
                                    <p class="text-xs text-ink-500">Changes apply the next time each person loads a page.</p>
                                    <button class="btn-brand btn-sm"><x-site.icon name="check" class="h-4 w-4" /> Save permissions</button>
                                </div>
                            @endif
                        </form>
                    @endif
                </section>
            </div>

            <div class="space-y-6">
                {{-- Details --}}
                <section class="portal-card">
                    <div class="portal-card-header"><h2 class="portal-card-title">Role details</h2></div>
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="space-y-4 p-5">
                        @csrf @method('PUT')
                        <fieldset class="space-y-4" @disabled(! $canUpdate)>
                            <div>
                                <label class="portal-label" for="role_name">Name</label>
                                <input id="role_name" name="role_name" value="{{ old('role_name', $role->role_name) }}" required maxlength="255" @readonly($role->isProtected()) class="portal-input read-only:bg-ink-50">
                            </div>
                            <div>
                                <label class="portal-label" for="role_description">Description</label>
                                <input id="role_description" name="role_description" value="{{ old('role_description', $role->role_description) }}" maxlength="255" class="portal-input">
                            </div>
                            <div>
                                <label class="portal-label" for="portal">Signs in to</label>
                                @if ($role->isProtected())
                                    <input type="hidden" name="portal" value="{{ $role->portal }}">
                                    <p class="text-sm text-ink-900">{{ \App\Models\Role::PORTALS[$role->portal] ?? $role->portal }}</p>
                                    <p class="mt-1 text-xs text-ink-500">This is a system role, so its name and portal are fixed and it cannot be deleted.</p>
                                @else
                                    <select id="portal" name="portal" required class="portal-input">
                                        @foreach (\App\Models\Role::PORTALS as $value => $label)<option value="{{ $value }}" @selected(old('portal', $role->portal) === $value)>{{ $label }}</option>@endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-ink-500">People with any administration role sign in to administration, even if they also hold an instructor role.</p>
                                @endif
                            </div>
                            @if ($canUpdate)
                                <button class="btn-dark btn-sm w-full">Save details</button>
                            @endif
                        </fieldset>
                    </form>
                </section>

                {{-- Courses --}}
                @if ($isInstructorRole)
                    <section class="portal-card">
                        <div class="portal-card-header"><h2 class="portal-card-title">Courses this role teaches</h2></div>
                        <form method="POST" action="{{ route('admin.roles.courses', $role) }}" class="space-y-3 p-5">
                            @csrf @method('PUT')
                            <p class="text-sm text-ink-500">When a student pays, each course they chose is assigned to the least busy active instructor whose role teaches that course. If no role teaches a course, any instructor may be picked.</p>
                            <fieldset class="space-y-2" @disabled(! $me->can('update_role_course_permission'))>
                                @forelse ($courses as $course)
                                    <label class="flex items-center gap-3 rounded-lg border border-ink-100 p-3 has-[:checked]:border-ink-950 has-[:checked]:bg-brand-50">
                                        <input type="checkbox" name="courses[]" value="{{ $course->id }}" @checked(in_array($course->id, $roleCourses)) class="rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                        <span class="text-sm font-semibold text-ink-900">{{ $course->objective }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-ink-500">No courses have been created yet.</p>
                                @endforelse
                            </fieldset>
                            @can('update_role_course_permission')
                                @if ($courses->isNotEmpty())<button class="btn-dark btn-sm w-full">Save courses</button>@endif
                            @endcan
                        </form>
                    </section>
                @endif

                {{-- People --}}
                <section class="portal-card">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">People with this role</h2>
                        <span class="badge-grey">{{ number_format($memberCount) }}</span>
                    </div>
                    @if ($members->isEmpty())
                        <x-portal.empty icon="users" title="Nobody holds this role" text="Give it to someone from their page under Staff." />
                    @else
                        <ul class="divide-y divide-ink-100">
                            @foreach ($members as $member)
                                @php
                                    $url = $member->isStudent()
                                        ? ($me->hasAnyPermission(['read_applicants', 'read_student_accounts']) ? route('admin.admissions.show', $member) : null)
                                        : ($me->can('update_instructors') ? route('admin.staff.edit', $member) : null);
                                @endphp
                                <li class="flex items-center gap-3 px-5 py-3">
                                    <x-portal.avatar :user="$member" size="h-8 w-8" />
                                    <div class="min-w-0 flex-1">
                                        @if ($url)
                                            <a href="{{ $url }}" class="block truncate text-sm font-semibold text-ink-950 hover:underline">{{ $member->firstName }} {{ $member->lastName }}</a>
                                        @else
                                            <span class="block truncate text-sm font-semibold text-ink-950">{{ $member->firstName }} {{ $member->lastName }}</span>
                                        @endif
                                        <span class="block truncate text-xs text-ink-400">{{ $member->email }}</span>
                                    </div>
                                    @if ($member->isSuspended())<x-portal.status status="suspended" />@endif
                                </li>
                            @endforeach
                        </ul>
                        @if ($memberCount > $members->count())
                            <p class="border-t border-ink-100 px-5 py-3 text-xs text-ink-500">Showing the first {{ $members->count() }} of {{ number_format($memberCount) }}.
                                @if ($role->portal === 'student')<a href="{{ route('admin.admissions.index', ['status' => 'all']) }}" class="font-semibold underline">See all students</a>@endif
                            </p>
                        @endif
                    @endif
                </section>
            </div>
        </div>
    </x-portal.page>
</x-app-layout>

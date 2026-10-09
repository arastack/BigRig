@section('title', 'Staff')
<x-app-layout>
    <x-portal.page title="Staff" subtitle="Administrators and instructors, the roles they hold and the students assigned to them.">
        <x-slot:actions>
            @can('create_instructors')
                <button type="button" class="btn-brand btn-sm" x-data @click="$dispatch('open-modal', 'add-staff')"><x-site.icon name="plus" class="h-4 w-4" /> Add staff</button>
            @endcan
        </x-slot:actions>

        <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-portal.stat label="Staff accounts" :value="$counts['total']" icon="users" />
            <x-portal.stat label="Active" :value="$counts['active']" icon="check-circle" :href="route('instructors', ['status' => 'active'])" />
            <x-portal.stat label="Suspended" :value="$counts['suspended']" icon="ban" :href="route('instructors', ['status' => 'suspended'])" :tone="$counts['suspended'] ? 'alert' : 'default'" />
            <x-portal.stat label="On the website" :value="$counts['website']" icon="globe" hint="Shown on the Our Instructors page" />
        </div>

        <div class="portal-card">
            {{-- Filters --}}
            <form method="GET" class="grid gap-3 border-b border-ink-100 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_14rem_12rem_auto]">
                <label class="relative">
                    <span class="sr-only">Search</span>
                    <x-site.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email or phone" class="portal-input pl-9">
                </label>
                <label>
                    <span class="sr-only">Role</span>
                    <select name="role" class="portal-input">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)<option value="{{ $role->id }}" @selected(($filters['role'] ?? null) == $role->id)>{{ $role->role_name }}</option>@endforeach
                    </select>
                </label>
                <label>
                    <span class="sr-only">Status</span>
                    <select name="status" class="portal-input">
                        <option value="">Any status</option>
                        <option value="active" @selected(($filters['status'] ?? null) === 'active')>Active</option>
                        <option value="suspended" @selected(($filters['status'] ?? null) === 'suspended')>Suspended</option>
                    </select>
                </label>
                <div class="flex gap-2">
                    <button class="btn-dark btn-sm flex-1"><x-site.icon name="filter" class="h-4 w-4" /> Filter</button>
                    @if (array_filter($filters))
                        <a href="{{ route('instructors') }}" class="btn-ghost btn-sm">Clear</a>
                    @endif
                </div>
            </form>

            @if ($staff->isEmpty())
                <x-portal.empty icon="users" title="No staff found" text="{{ array_filter($filters) ? 'No one matches these filters.' : 'Add your first administrator or instructor to get started.' }}" />
            @else
                <div class="overflow-x-auto">
                    <table class="portal-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Contact</th>
                                <th scope="col">Roles</th>
                                <th scope="col">Teaches</th>
                                <th scope="col" class="text-right">Students</th>
                                <th scope="col">Website</th>
                                <th scope="col">Status</th>
                                <th scope="col"><span class="sr-only">Edit</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($staff as $member)
                                @php
                                    $teaches = $member->roles->flatMap(fn ($role) => $coursesByRole[$role->id] ?? [])->unique();
                                    $canOpen = auth()->user()->can('update_instructors');
                                @endphp
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <x-portal.avatar :user="$member" size="h-9 w-9" />
                                            <span>
                                                @if ($canOpen)
                                                    <a href="{{ route('admin.staff.edit', $member) }}" class="block font-semibold text-ink-950 hover:underline">{{ $member->firstName }} {{ $member->lastName }}</a>
                                                @else
                                                    <span class="block font-semibold text-ink-950">{{ $member->firstName }} {{ $member->lastName }}</span>
                                                @endif
                                                @if ($member->is(auth()->user()))<span class="block text-xs text-ink-400">You</span>@endif
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="block">{{ $member->mobileNumber ?: '—' }}</span>
                                        <span class="block max-w-[14rem] truncate text-xs text-ink-400">{{ $member->email }}</span>
                                    </td>
                                    <td>
                                        <div class="flex max-w-[16rem] flex-wrap gap-1">
                                            @foreach ($member->roles as $role)
                                                <span @class(['badge-dark' => $role->portal === 'admin', 'badge-yellow' => $role->portal === 'instructor', 'badge-grey' => $role->portal === 'student'])>{{ $role->role_name }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="max-w-[16rem]"><span class="line-clamp-2">{{ $teaches->join(', ') ?: '—' }}</span></td>
                                    <td class="text-right font-semibold text-ink-950">{{ $studentCounts[$member->id] ?? 0 }}</td>
                                    <td>
                                        @if ($member->website_visibility)
                                            <span class="badge-green"><x-site.icon name="globe" class="h-3.5 w-3.5" /> Listed</span>
                                        @else
                                            <span class="text-xs text-ink-400">Hidden</span>
                                        @endif
                                    </td>
                                    <td><x-portal.status :status="$member->isSuspended() ? 'suspended' : 'active'" /></td>
                                    <td class="text-right">
                                        @if ($canOpen)
                                            <a href="{{ route('admin.staff.edit', $member) }}" class="btn-ghost btn-xs" aria-label="Edit {{ $member->firstName }}"><x-site.icon name="chevron-right" class="h-4 w-4" /></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($staff->hasPages())
                    <div class="border-t border-ink-100 px-4 py-3">{{ $staff->links() }}</div>
                @endif
            @endif
        </div>

        @can('create_instructors')
            <x-portal.modal name="add-staff" title="Add staff" maxWidth="max-w-2xl">
                <form method="POST" action="{{ route('instructor.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="add-staff">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="portal-label" for="firstName">First name</label>
                            <input id="firstName" name="firstName" value="{{ old('firstName') }}" required maxlength="255" class="portal-input">
                        </div>
                        <div>
                            <label class="portal-label" for="middleName">Middle name <span class="normal-case tracking-normal text-ink-400">(optional)</span></label>
                            <input id="middleName" name="middleName" value="{{ old('middleName') }}" maxlength="255" class="portal-input">
                        </div>
                        <div>
                            <label class="portal-label" for="lastName">Last name</label>
                            <input id="lastName" name="lastName" value="{{ old('lastName') }}" required maxlength="255" class="portal-input">
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="portal-label" for="gender">Gender</label>
                            <select id="gender" name="gender" required class="portal-input">
                                <option value="">Choose…</option>
                                @foreach (['Male', 'Female'] as $gender)<option @selected(old('gender') === $gender)>{{ $gender }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="portal-label" for="email">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" class="portal-input">
                        </div>
                        <div>
                            <label class="portal-label" for="mobileNumber">Phone</label>
                            <input id="mobileNumber" type="tel" name="mobileNumber" value="{{ old('mobileNumber') }}" required maxlength="17" class="portal-input">
                        </div>
                    </div>
                    <div>
                        <label class="portal-label" for="profile_photo">Photo <span class="normal-case tracking-normal text-ink-400">(optional, JPG or PNG up to 2 MB)</span></label>
                        <input id="profile_photo" type="file" name="profile_photo" accept="image/jpeg,image/png" class="block w-full text-sm text-ink-600 file:mr-3 file:rounded-lg file:border-0 file:bg-ink-950 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white">
                    </div>
                    <fieldset>
                        <legend class="portal-label">Roles</legend>
                        @include('admin.staff._roles', ['roles' => $assignableRoles, 'selected' => old('userRole', []), 'locked' => false])
                    </fieldset>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="website_visibility" value="1" @checked(old('website_visibility')) class="rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                        Show on the public website's instructors page
                    </label>
                    <p class="rounded-lg bg-ink-50 px-3 py-2 text-xs text-ink-600">We'll create a temporary password and show it to you once, and email them a link to choose their own.</p>
                    <div class="flex justify-end gap-2 border-t border-ink-100 pt-4">
                        <button type="button" class="btn-ghost btn-sm" @click="open = false">Cancel</button>
                        <button class="btn-brand btn-sm">Create account</button>
                    </div>
                </form>
            </x-portal.modal>
            @if (old('_form') === 'add-staff')
                <div x-data x-init="$nextTick(() => $dispatch('open-modal', 'add-staff'))"></div>
            @endif
        @endcan
    </x-portal.page>
</x-app-layout>

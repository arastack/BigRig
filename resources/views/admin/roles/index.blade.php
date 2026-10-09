@section('title', 'Roles & permissions')
@php $portalBadge = ['admin' => 'badge-dark', 'instructor' => 'badge-yellow', 'student' => 'badge-grey']; @endphp
<x-app-layout>
    <x-portal.page :title="'Roles & permissions'" subtitle="A role is a set of permissions. Give people roles on the Staff page to control what they can see and do.">
        <x-slot:actions>
            @can('create_roles_and_permissions')
                <button type="button" class="btn-brand btn-sm" x-data @click="$dispatch('open-modal', 'new-role')"><x-site.icon name="plus" class="h-4 w-4" /> New role</button>
            @endcan
        </x-slot:actions>

        @foreach (\App\Models\Role::PORTALS as $portal => $portalLabel)
            @php $group = $roles->where('portal', $portal); @endphp
            @continue($group->isEmpty())
            <section class="mb-8">
                <h2 class="mb-3 text-xs font-semibold uppercase tracking-wider text-ink-500">{{ $portalLabel }}</h2>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($group as $role)
                        <article class="portal-card flex flex-col p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="font-display text-2xl font-bold uppercase leading-tight text-ink-950">{{ $role->role_name }}</h3>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <span class="{{ $portalBadge[$role->portal] ?? 'badge-grey' }}">{{ \App\Models\Role::PORTALS[$role->portal] ?? $role->portal }}</span>
                                        @if ($role->isProtected())<span class="badge-grey"><x-site.icon name="shield" class="h-3.5 w-3.5" /> System role</span>@endif
                                    </div>
                                </div>
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-ink-950 text-brand-400"><x-site.icon :name="$role->portal === 'student' ? 'graduation' : ($role->portal === 'instructor' ? 'steering' : 'shield')" class="h-5 w-5" /></span>
                            </div>
                            <p class="mt-3 flex-1 text-sm text-ink-600">{{ $role->role_description ?: 'No description.' }}</p>
                            @if ($role->portal === 'instructor')
                                <p class="mt-2 text-xs text-ink-500"><span class="font-semibold">Teaches:</span> {{ ($coursesByRole[$role->id] ?? collect())->join(', ') ?: 'no courses yet' }}</p>
                            @endif
                            <dl class="mt-4 grid grid-cols-2 gap-3 border-t border-ink-100 pt-4 text-sm">
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">People</dt>
                                    <dd class="font-display text-2xl font-bold text-ink-950">{{ $role->users_count }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold uppercase tracking-wider text-ink-400">Permissions</dt>
                                    <dd class="font-display text-2xl font-bold text-ink-950">{{ $role->isSuperAdmin() ? 'All' : $role->permissions_count . ' / ' . $totalPermissions }}</dd>
                                </div>
                            </dl>
                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('user-permissions', $role) }}" class="btn-dark btn-sm flex-1"><x-site.icon name="pencil" class="h-4 w-4" /> {{ auth()->user()->can('update_roles_and_permissions') ? 'Edit role' : 'View role' }}</a>
                                @can('delete_roles_and_permissions')
                                    @if (! $role->isProtected() && $role->users_count === 0)
                                        <form method="POST" action="{{ route('user-roles.destroy', $role) }}" onsubmit="return confirm(@js('Delete the ' . $role->role_name . ' role? This cannot be undone.'))">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost btn-sm text-red-600" aria-label="Delete {{ $role->role_name }}"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        @can('create_roles_and_permissions')
            <x-portal.modal name="new-role" title="New role">
                <form method="POST" action="{{ route('user-roles.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="_form" value="new-role">
                    <div>
                        <label class="portal-label" for="role_name">Name</label>
                        <input id="role_name" name="role_name" value="{{ old('role_name') }}" required maxlength="255" placeholder="e.g. Front Desk" class="portal-input">
                    </div>
                    <div>
                        <label class="portal-label" for="role_description">Description <span class="normal-case tracking-normal text-ink-400">(optional)</span></label>
                        <input id="role_description" name="role_description" value="{{ old('role_description') }}" maxlength="255" placeholder="What people with this role do" class="portal-input">
                    </div>
                    <div>
                        <label class="portal-label" for="portal">Signs in to</label>
                        <select id="portal" name="portal" required class="portal-input">
                            @foreach (\App\Models\Role::PORTALS as $value => $label)<option value="{{ $value }}" @selected(old('portal', 'admin') === $value)>{{ $label }}</option>@endforeach
                        </select>
                        <p class="mt-1 text-xs text-ink-500">Instructor roles can be linked to courses so new students are assigned to them automatically.</p>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-ink-100 pt-4">
                        <button type="button" class="btn-ghost btn-sm" @click="open = false">Cancel</button>
                        <button class="btn-brand btn-sm">Create role</button>
                    </div>
                </form>
            </x-portal.modal>
            @if (old('_form') === 'new-role')
                <div x-data x-init="$nextTick(() => $dispatch('open-modal', 'new-role'))"></div>
            @endif
        @endcan
    </x-portal.page>
</x-app-layout>

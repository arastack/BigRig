{{-- Role checkboxes for a staff account. Expects $roles, $selected (array of ids), $locked (bool) and $coursesByRole. --}}
@php $selected = array_map('intval', (array) $selected); @endphp
<div class="grid gap-2 sm:grid-cols-2">
    @foreach ($roles as $role)
        @php $teaches = $coursesByRole[$role->id] ?? collect(); @endphp
        <label @class(['flex items-start gap-3 rounded-lg border border-ink-100 p-3 has-[:checked]:border-ink-950 has-[:checked]:bg-brand-50', 'cursor-not-allowed opacity-70' => $locked])>
            <input type="checkbox" name="userRole[]" value="{{ $role->id }}" @checked(in_array($role->id, $selected, true)) @disabled($locked)
                class="mt-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
            <span class="min-w-0 flex-1">
                <span class="flex flex-wrap items-center gap-2 text-sm font-semibold text-ink-950">
                    {{ $role->role_name }}
                    <span @class(['badge-dark' => $role->portal === 'admin', 'badge-yellow' => $role->portal === 'instructor'])>{{ $role->portal === 'admin' ? 'Admin' : 'Instructor' }}</span>
                </span>
                @if ($role->role_description)<span class="mt-0.5 block text-xs text-ink-500">{{ $role->role_description }}</span>@endif
                @if ($teaches->isNotEmpty())<span class="mt-0.5 block text-xs text-ink-400">Teaches {{ $teaches->join(', ') }}</span>@endif
            </span>
        </label>
    @endforeach
</div>

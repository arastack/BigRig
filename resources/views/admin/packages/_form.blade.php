@php
    /** @var \App\Models\CoursePackage|null $package */
    $key = $package ? (string) $package->id : 'new';
    $useOld = old('_package') === $key;
    $groups = $useOld
        ? collect(old('groups', []))->map(fn ($ids) => collect($ids)->filter(fn ($id) => filled($id))->map(fn ($id) => (int) $id)->values()->all())->values()->all()
        : ($package ? $package->groups() : [[], []]);
    while (count($groups) < 2) { $groups[] = []; }
    $field = fn ($name, $default = null) => $useOld ? old($name) : $default;
    $idPrefix = 'pkg_' . $key . '_';
@endphp
<form method="POST" action="{{ $package ? route('package.update', $package) : route('package.store') }}" class="space-y-4"
      x-data="{ groups: @js($groups), max: {{ \App\Http\Controllers\Admin\PackageController::MAX_GROUPS }} }">
    @csrf
    @if ($package) @method('PUT') @endif
    <input type="hidden" name="_package" value="{{ $key }}">

    <div>
        <label class="portal-label" for="{{ $idPrefix }}name">Package name</label>
        <input id="{{ $idPrefix }}name" name="name" value="{{ $field('name', $package?->name) }}" required maxlength="255" placeholder="e.g. CDL + Forklift Combo" class="portal-input">
    </div>
    <div>
        <label class="portal-label" for="{{ $idPrefix }}description">Description <span class="font-normal normal-case tracking-normal text-ink-400">(shown on the website and application form)</span></label>
        <textarea id="{{ $idPrefix }}description" name="description" rows="3" maxlength="2000" class="portal-input">{{ $field('description', $package?->description) }}</textarea>
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="portal-label" for="{{ $idPrefix }}discount">Discount ({{ $currency }})</label>
            <input id="{{ $idPrefix }}discount" type="number" name="discount" min="0" step="0.01" value="{{ $field('discount', $package ? (float) $package->discount : null) }}" required class="portal-input">
        </div>
        <div>
            <label class="portal-label" for="{{ $idPrefix }}weeks">Total weeks <span class="font-normal normal-case tracking-normal text-ink-400">(optional)</span></label>
            <input id="{{ $idPrefix }}weeks" type="number" name="weeks" min="1" max="520" step="1" value="{{ $field('weeks', $package?->weeks) }}" class="portal-input">
        </div>
    </div>

    <fieldset>
        <legend class="portal-label">Courses</legend>
        <p class="mb-3 text-sm text-ink-500">The discount applies when an applicant picks at least one course from <strong>every</strong> group.</p>
        <div class="grid gap-3 sm:grid-cols-2">
            <template x-for="(group, gi) in groups" :key="gi">
                <div class="rounded-lg border border-ink-100 p-3">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm font-semibold text-ink-950" x-text="'Group ' + (gi + 1)"></p>
                        <button type="button" class="btn-ghost btn-xs text-red-600" x-show="groups.length > 2" @click="groups.splice(gi, 1)" :aria-label="'Remove group ' + (gi + 1)"><x-site.icon name="trash" class="h-4 w-4" /></button>
                    </div>
                    <input type="hidden" :name="'groups[' + gi + '][]'" value="">
                    <div class="space-y-1.5">
                        @foreach ($courses as $course)
                            <label class="flex gap-2 text-sm text-ink-700">
                                <input type="checkbox" :name="'groups[' + gi + '][]'" value="{{ $course->id }}" x-model.number="groups[gi]" class="mt-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                <span>{{ $course->objective }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </template>
        </div>
        <button type="button" class="btn-ghost btn-sm mt-3" x-show="groups.length < max" @click="groups.push([])"><x-site.icon name="plus" class="h-4 w-4" /> Add group</button>
    </fieldset>

    <label class="flex gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked($useOld ? old('is_active') : ($package ? $package->is_active : true)) class="mt-0.5 rounded border-ink-300 text-ink-950 focus:ring-brand-400">
        <span>Active — offer this discount on the website and application form</span>
    </label>

    <div class="flex justify-end gap-2 pt-2">
        <button type="button" class="btn-ghost btn-sm" @click="$dispatch('close-modal')">Cancel</button>
        <button class="{{ $package ? 'btn-dark' : 'btn-brand' }} btn-sm">{{ $package ? 'Save changes' : 'Create package' }}</button>
    </div>
</form>

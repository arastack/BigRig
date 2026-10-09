@section('title', 'Packages & VAT')
<x-app-layout>
    <x-portal.page title="Packages & VAT" subtitle="Combo discounts for enrolling on several courses together, and the VAT added to tuition.">
        <x-slot:actions>
            <button type="button" class="btn-brand btn-sm" @click="$dispatch('open-modal', 'package-create')"><x-site.icon name="plus" class="h-4 w-4" /> New package</button>
        </x-slot:actions>

        @php $courseNames = $courses->pluck('objective', 'id'); @endphp

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Packages --}}
            <div class="portal-card lg:col-span-2">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">Course packages</h2>
                </div>
                @if ($packages->isEmpty())
                    <x-portal.empty icon="award" title="No packages yet" text="Create a package to give a discount when applicants enrol on courses together.">
                        <button type="button" class="btn-dark btn-sm mt-4" @click="$dispatch('open-modal', 'package-create')"><x-site.icon name="plus" class="h-4 w-4" /> New package</button>
                    </x-portal.empty>
                @else
                    <div class="overflow-x-auto">
                        <table class="portal-table">
                            <thead>
                                <tr>
                                    <th scope="col">Package</th>
                                    <th scope="col">Courses (one from each group)</th>
                                    <th scope="col" class="text-right">Discount</th>
                                    <th scope="col" class="text-right">Weeks</th>
                                    <th scope="col">Status</th>
                                    <th scope="col"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $package)
                                    <tr>
                                        <td>
                                            <span class="font-semibold text-ink-950">{{ $package->name }}</span>
                                            @if ($package->description)<span class="mt-0.5 block max-w-xs text-xs text-ink-400">{{ \Illuminate\Support\Str::limit($package->description, 90) }}</span>@endif
                                        </td>
                                        <td>
                                            <ol class="space-y-1 text-sm">
                                                @foreach ($package->groups() as $i => $ids)
                                                    <li>
                                                        <span class="text-xs font-semibold uppercase tracking-wider text-ink-400">Group {{ $i + 1 }}:</span>
                                                        {{ collect($ids)->map(fn ($id) => $courseNames[$id] ?? 'Deleted course')->join(' or ') ?: '—' }}
                                                    </li>
                                                @endforeach
                                            </ol>
                                        </td>
                                        <td class="whitespace-nowrap text-right font-semibold text-ink-950">{{ $currency }}{{ number_format((float) $package->discount, 2) }}</td>
                                        <td class="text-right">{{ $package->weeks ?? '—' }}</td>
                                        <td>
                                            {{-- Quick on/off: resubmits the package with is_active flipped --}}
                                            <form method="POST" action="{{ route('package.update', $package) }}">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="_package" value="{{ $package->id }}">
                                                <input type="hidden" name="name" value="{{ $package->name }}">
                                                <input type="hidden" name="description" value="{{ $package->description }}">
                                                <input type="hidden" name="discount" value="{{ (float) $package->discount }}">
                                                <input type="hidden" name="weeks" value="{{ $package->weeks }}">
                                                @foreach ($package->groups() as $i => $ids)
                                                    @foreach ($ids as $id)<input type="hidden" name="groups[{{ $i }}][]" value="{{ $id }}">@endforeach
                                                @endforeach
                                                @unless ($package->is_active)<input type="hidden" name="is_active" value="1">@endunless
                                                <button class="{{ $package->is_active ? 'badge-green' : 'badge-grey' }} cursor-pointer" title="{{ $package->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                                    {{ $package->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" class="btn-ghost btn-xs" @click="$dispatch('open-modal', 'package-edit-{{ $package->id }}')" aria-label="Edit {{ $package->name }}"><x-site.icon name="pencil" class="h-4 w-4" /></button>
                                                <form method="POST" action="{{ route('package.destroy', $package) }}" onsubmit="return confirm(@js('Delete ' . $package->name . '? Applications already submitted keep their discount.'))">
                                                    @csrf @method('DELETE')
                                                    <button class="btn-ghost btn-xs text-red-600" aria-label="Delete {{ $package->name }}"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- VAT --}}
            <div class="portal-card self-start">
                <div class="portal-card-header">
                    <h2 class="portal-card-title">VAT</h2>
                </div>
                <form method="POST" action="{{ route('packages.vat') }}" class="space-y-4 p-5">
                    @csrf @method('PUT')
                    <div>
                        <label class="portal-label" for="vat_rate">VAT rate (%)</label>
                        <input id="vat_rate" type="number" name="vat_rate" min="0" max="100" step="0.01" value="{{ old('vat_rate', rtrim(rtrim(number_format($vatPercent, 2, '.', ''), '0'), '.')) }}" required class="portal-input">
                    </div>
                    <p class="text-sm text-ink-500">Added to tuition (after any package discount) on the website, the application form and the PDF form. Applications already submitted keep the amount they were quoted.</p>
                    <div class="flex justify-end">
                        <button class="btn-dark btn-sm">Save VAT rate</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Create --}}
        <x-portal.modal name="package-create" title="New package" max-width="max-w-2xl">
            @include('admin.packages._form', ['package' => null])
        </x-portal.modal>

        {{-- Edit, one per package --}}
        @foreach ($packages as $package)
            <x-portal.modal :name="'package-edit-' . $package->id" title="Edit package" max-width="max-w-2xl">
                @include('admin.packages._form', ['package' => $package])
            </x-portal.modal>
        @endforeach

        {{-- Reopen the form that failed validation --}}
        @if ($errors->any() && old('_package'))
            <div x-data x-init="$nextTick(() => $dispatch('open-modal', @js(old('_package') === 'new' ? 'package-create' : 'package-edit-' . old('_package'))))"></div>
        @endif
    </x-portal.page>
</x-app-layout>

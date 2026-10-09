<div class="grow">

    <!-- Panel body -->
    <div class="p-6 flex space-x-4">
        <section class="w-1/2 px-6 border-r-2">
            <div class="mb-8">
                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-4">Clients</h2>
            </div>

            <!-- Payment Details -->
            <div class="py-5 border-b">
                <form action="{{ route('client.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                    <div>
                        <input type="text" class="hidden" value="client" name="type">
                        <div>
                            <div class="flex gap-2">
                                <div class="w-full">
                                    <label class="flex-col text-sm font-medium mb-1" for="client_logo">Client Logo <span class="text-red-500">*</span></label>
                                    <input id="client_logo" class="form-input w-full" type="file" name="client_logo" required />
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3/4">
                                <label class="text-sm font-medium mb-1" for="client_name">Client Name <span class="text-red-500">*</span></label>
                                <input id="client_name" class="form-input w-full" type="text" placeholder="FRSC" name="client_name" required />
                            </div>
                            <div class="w-1/4 text-right">
                                <button type="submit" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white mt-6">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <table class="table-auto w-full dark:text-gray-400">
                <!-- Table header -->
                <thead class="text-xs uppercase text-gray-400 dark:text-gray-500">
                    <tr class="flex flex-wrap md:table-row md:flex-no-wrap border-b border-gray-200 dark:border-gray-700/60 py-2 md:py-0">
                        <th class="w-full hidden md:w-auto md:table-cell py-2">
                            <div class="font-semibold text-left">Clients</div>
                        </th>
                        <th class="w-full hidden md:w-auto md:table-cell py-2">
                            <div class="font-semibold text-left">Action</div>
                        </th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm">
                    <!-- Row -->
                    @if ($clients->isEmpty())
                        <tr class="flex flex-wrap md:table-row md:flex-no-wrap border-b border-gray-200 dark:border-gray-700/60 py-2 md:py-0">
                            <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2" colspan="2">
                                <div class="font-medium text-center">No record found.</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($clients as $client)
                            <tr class="flex flex-wrap md:table-row md:flex-no-wrap border-b border-gray-200 dark:border-gray-700/60 py-2 md:py-0">
                                <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2">
                                    <div class="flex items-center">
                                        <div class="shrink-0 rounded-full mr-2 sm:mr-3">
                                            <img class="rounded-sm border-2 border-white dark:border-gray-800 box-content" src="{{ Storage::url($client->logo) }}" width="60" height="40" alt="User 01" />
                                        </div>
                                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $client->name }}</div>
                                    </div>
                                </td>
                                <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2 whitespace-nowrap">
                                    <button type="button" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600" @click="$dispatch('open-modal', 'client-edit-{{ $client->id }}')" aria-label="Edit {{ $client->name }}">
                                        <x-site.icon name="pencil" class="h-4 w-4 text-ink-600" />
                                    </button>
                                    <button type="button" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600" @click="$store.deleteModal.open({{ $client->id }})" aria-controls="delete-modal">
                                        <svg class="fill-current text-red-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                                            <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

        </section>

        <section class="w-1/2 px-6">
            <div class="mb-8">
                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-4">Partners</h2>
            </div>

            <!-- Payment Details -->
            <div class="pt-5 pb-3 border-b">
                <form action="{{ route('client.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                    <div>
                        <input type="text" class="hidden" value="partner" name="type">
                        <div class="flex gap-2">
                            <div class="w-full">
                                <label class="flex-col text-sm font-medium mb-1" for="client_name">Partner Name <span class="text-red-500">*</span></label>
                                <input id="client_name" class="form-input w-full" type="text" placeholder="BigRig Truck Driving School" name="client_name" required />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <div class="mb-4">
                            <button type="submit" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">Save</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <table class="table-auto w-full dark:text-gray-400">
                <!-- Table header -->
                <thead class="text-xs uppercase text-gray-400 dark:text-gray-500">
                    <tr class="flex flex-wrap md:table-row md:flex-no-wrap border-b border-gray-200 dark:border-gray-700/60 py-2 md:py-0">
                        <th class="w-full hidden md:w-auto md:table-cell py-2">
                            <div class="font-semibold text-left">Partner Name</div>
                        </th>
                        <th class="w-full hidden md:w-auto md:table-cell py-2">
                            <div class="font-semibold text-left">Action</div>
                        </th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm">
                    <!-- Row -->
                    @if ($partners->isEmpty())
                        <tr class="flex flex-wrap md:table-row md:flex-no-wrap border-b border-gray-200 dark:border-gray-700/60 py-2 md:py-0">
                            <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2" colspan="2">
                                <div class="font-medium text-center">No record found.</div>
                            </td>
                        </tr>
                    @else
                        @foreach ($partners as $partner)
                            <tr class="flex flex-wrap md:table-row md:flex-no-wrap border-b border-gray-200 dark:border-gray-700/60 py-2 md:py-0">
                                <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2">
                                    <div class="flex items-center">
                                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $partner->name }}</div>
                                    </div>
                                </td>
                                <td class="w-full block md:w-auto md:table-cell py-0.5 md:py-2 whitespace-nowrap">
                                    <button type="button" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600" @click="$dispatch('open-modal', 'client-edit-{{ $partner->id }}')" aria-label="Edit {{ $partner->name }}">
                                        <x-site.icon name="pencil" class="h-4 w-4 text-ink-600" />
                                    </button>
                                    <button type="button" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600" @click="$store.deleteModal.open({{ $partner->id }})" aria-controls="delete-modal">
                                        <svg class="fill-current text-red-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                                            <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

        </section>

    </div>
</div>

{{-- Edit modals, one per client / partner --}}
@foreach (collect($clients->items())->concat($partners->items()) as $item)
    <x-portal.modal :name="'client-edit-' . $item->id" :title="'Edit ' . ($item->type === 'client' ? 'client' : 'partner')">
        <form method="POST" action="{{ route('client.update') }}" enctype="multipart/form-data" class="space-y-4" x-data="{ type: @js($item->type) }">
            @csrf @method('PUT')
            <input type="hidden" name="id" value="{{ $item->id }}">
            <div>
                <label class="portal-label" for="client_name_{{ $item->id }}">Name</label>
                <input id="client_name_{{ $item->id }}" name="client_name" value="{{ $item->name }}" required maxlength="255" class="portal-input">
            </div>
            <div>
                <label class="portal-label" for="client_type_{{ $item->id }}">Show as</label>
                <select id="client_type_{{ $item->id }}" name="type" x-model="type" class="portal-input">
                    <option value="client">Client (logo)</option>
                    <option value="partner">Partner (name)</option>
                </select>
            </div>
            <div>
                <label class="portal-label" for="client_logo_{{ $item->id }}">Logo</label>
                @if ($item->logo)
                    <img src="{{ Storage::url($item->logo) }}" alt="" class="mb-2 h-12 w-auto rounded border border-ink-100">
                @endif
                <input id="client_logo_{{ $item->id }}" type="file" name="client_logo" accept=".jpg,.jpeg,.png" class="portal-input" @if (! $item->logo) x-bind:required="type === 'client'" @endif>
                <p class="mt-1 text-xs text-ink-400">.jpg or .png, up to 2 MB.{{ $item->logo ? ' Leave empty to keep the current logo.' : '' }}</p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost btn-sm" @click="$dispatch('close-modal')">Cancel</button>
                <button class="btn-dark btn-sm">Save changes</button>
            </div>
        </form>
    </x-portal.modal>
@endforeach

<!-- Delete Clients Modal -->
@include('components.modals.delete-client')
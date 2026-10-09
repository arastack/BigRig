<div class="bg-white dark:bg-gray-900 mt-10">
    <div x-data="handleSelect">

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full dark:text-gray-300">
                <!-- Table header -->
                <thead class="text-xs font-semibold uppercase text-gray-500 border-t border-b border-gray-200 dark:border-gray-700/60">
                    <tr>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                            <div class="flex items-center">
                                <label class="inline-flex">
                                    <span class="sr-only">Select all</span>
                                    <input id="parent-checkbox" class="form-checkbox" type="checkbox" @click="toggleAll" />
                                </label>
                            </div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">User Image</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Full Name</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Testimony</div>
                        </th>
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Rating</div>
                        </th>
                        @if (Auth::user()->hasPermission('update_website_management'))
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                <div class="font-semibold text-left">Website Visibility</div>
                            </th>
                        @endif
                        <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                            <div class="font-semibold text-left">Action</div>
                        </th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody class="text-sm divide-y divide-gray-100 dark:divide-gray-700/60 border-b border-gray-200 dark:border-gray-700/60">
                    <!-- Row -->
                    @if ($testimonials->isEmpty())
                        <tr class="text-center">
                            <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap" colspan="7">
                                <div class="text-center">No record found.</div>
                            </td>
                        </tr>
                    @else
                        @foreach($testimonials as $testimonial)                 
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="flex items-center">
                                        <label class="inline-flex">
                                            <span class="sr-only">Select</span>
                                            <input class="table-item form-checkbox" type="checkbox" @click.stop="uncheckParent" />
                                        </label>
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 shrink-0 mr-2 sm:mr-3">
                                            <img class="rounded-full h-10 w-10" src="{{ $testimonial->image_url ? Storage::url($testimonial->image_url) : Storage::url('users/avatar.png') }}" width="40" height="40" alt="{{ $testimonial->full_name }}" />
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-left">{{ $testimonial->full_name }}</div>                              
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-normal break-words">
                                    <div class="flex items-center">
                                        <div class="text-left">{!! clean_html($testimonial->testimony) !!}</div>
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-center">{{ $testimonial->rating }}</div>
                                    </div>
                                </td>
                                @if (Auth::user()->hasPermission('update_website_management'))
                                    <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                        @livewire('testimonial-visibility', ['testimonial' => $testimonial])
                                    </td>                            
                                @endif
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    <div class="font-medium">
                                        <!-- Footer -->
                                        <div class="flex justify-between items-center mt-3">
                                            <!-- Avatars -->
                                            <div class="flex items-center space-x-2">
                                                <!-- Edit Button -->
                                                <button type="button" @click="$store.editModal.open({
                                                    id: {{ $testimonial->id }},
                                                    full_name: '{{ $testimonial->full_name }}',
                                                    rating: '{{ $testimonial->rating }}',
                                                    testimony: @js(clean_html($testimonial->testimony)),
                                                })" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600">
                                                    <svg class="fill-current text-gray-400 dark:text-gray-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                                                        <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z" />
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button type="button" class="btn bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600" @click="$store.deleteModal.open({{ $testimonial->id }})" aria-controls="delete-modal">
                                                    <svg class="fill-current text-red-500 shrink-0" width="16" height="16" viewBox="0 0 16 16">
                                                        <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- Create Testimony Modal -->
@include('components.modals.create-testimony-modal')

<!-- Edit Testimony Modal -->
@include('components.modals.edit-testimony-modal')

<!-- Delete Testimony Modal -->
@include('components.modals.delete-testimony-modal')

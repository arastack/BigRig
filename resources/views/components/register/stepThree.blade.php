<!-- Progress bar -->
<div class="px-4 pt-4 pb-8">
    <div class="max-w-2xl mx-auto w-full">
        <div class="relative">
            <div class="absolute left-0 top-1/2 -mt-4 w-full h-0.5 bg-gray-200 dark:bg-gray-700/60" aria-hidden="true"></div>
            <ul class="relative flex justify-between w-full">
                <li>
                    <a class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-violet-500 text-white" href="javascript:void(0)">1</a>
                    <div class="mt-1">
                        <div class="flex text-sm font-medium text-gray-400 dark:text-gray-500 space-x-2">
                            <span class="text-gray-500 dark:text-gray-400">Personal Information</span>
                        </div>
                    </div>                    
                </li>
                <li>
                    <a class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-violet-500 text-white" href="javascript:void(0)">2</a>
                    <div class="mt-1">
                        <div class="flex text-sm font-medium text-gray-400 dark:text-gray-500 space-x-2">
                            <span>-&gt;</span>
                            <span class="text-gray-500 dark:text-gray-400">Medical History</span>
                        </div>
                    </div>
                </li>
                <li>
                    <a class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-violet-500 text-white" href="javascript:void(0)">3</a>
                    <div class="mt-1">
                        <div class="flex text-sm font-medium text-gray-400 dark:text-gray-500 space-x-2">
                            <span>-&gt;</span>
                            <span class="text-violet-500">Training Objective(s)</span>
                        </div>
                    </div>
                </li>
                <li>
                    <a class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400" href="javascript:void(0)">4</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('Choose Your Training Objective') }}</h1> -->

<div class="space-y-6 mt-6">
    <div class="max-w-5xl mx-auto flex flex-col lg:flex-row lg:space-x-2 xl:space-x-2">
        <div class="mb-6 lg:mb-0">
            @foreach ($courses as $course)
                <div class="sm:flex space-y-3 sm:space-y-0 sm:space-x-4 mb-8">
                    <label class="flex-1 relative block cursor-pointer">
                        <input type="checkbox" name="selected_objective[]" class="peer sr-only" value="{{ $course->id }}" {{ is_array(old('selected_objective')) && in_array($course->id, old('selected_objective')) ? 'checked' : '' }} />
                        <div class="sm:flex h-full px-4 py-6 rounded-lg border-b border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 shadow-sm transition">
                            <img class="rounded-sm block mb-4 sm:mb-0 mr-5 shrink-0" src="{{ Storage::url($course->image_url) }}" width="200" height="142" alt="{{ $course->objective }}" />
                            <div class="grow">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ $course->objective }}</h3>
                                <div class="text-sm mb-2">{!! $course->requirement !!}</div>
                                <!-- Product meta -->
                                <div class="flex flex-wrap justify-between items-center">
                                    <!-- Rating and price -->
                                    <div class="flex flex-wrap items-center space-x-2 mr-2">
                                        <!-- Rating -->
                                        <div class="flex items-center space-x-2">
                                            <!-- Rate -->
                                            <div class="inline-flex text-sm font-medium text-yellow-600">{{ $course->duration }} Weeks</div>
                                        </div>
                                        <div class="text-gray-400 dark:text-gray-600">·</div>
                                        <!-- Price -->
                                        <div>
                                            <div class="inline-flex text-sm font-medium bg-green-500/20 text-green-700 rounded-full text-center px-2 py-0.5">{{ $settings->base_currency }}{{ number_format($course->price, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-0 border-2 border-transparent peer-checked:border-violet-400 dark:peer-checked:border-violet-500 rounded-lg pointer-events-none" aria-hidden="true"></div>
                    </label>
                </div>
            @endforeach

        </div>

        <!-- Sidebar -->
        <div class="max-w-sm mx-auto lg:max-w-none">
            <div class="bg-white dark:bg-gray-800 p-5 shadow-sm rounded-xl lg:w-72 xl:w-80">
                <div class="text-gray-800 dark:text-gray-100 font-semibold mb-2">Order Summary</div>
                <!-- Order details -->
                <ul class="mb-4">
                    <span class="order-summary-list">
                    </span>
                    <li class="text-sm w-full flex justify-between py-3 border-b border-gray-200 dark:border-gray-700/60">
                        <div>Subtotal</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100 subtotal"></div>
                    </li>
                    <li class="text-sm w-full flex justify-between py-3 border-b border-gray-200 dark:border-gray-700/60">
                        <div>Taxes</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100 taxes"></div>
                    </li>
                    <li class="text-sm w-full flex justify-between py-3 border-b border-gray-200 dark:border-gray-700/60">
                        <div>Total due (including taxes)</div>
                        <div class="font-medium text-green-600 total-due"></div>
                    </li>
                </ul>
                <!-- <div class="mb-4">
                    <x-button id="payNowButton" class="btn w-full bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white" disabled>
                        {{ __('Proceed To Pay') }}
                    </x-button>
                </div> -->
                <div class="text-xs text-gray-500 italic text-center training-duration-note">Note that your total training period is 22 weeks.</div>
            </div>
        </div>
    </div>
</div>


<script src="{{asset('assets/js/jquery-3.3.1.min.js')}}"></script>
<script>
    $(document).ready(function () {
        // Order summary variables
        let orderSummary = [];
        let totalPrice = 0;
        let totalDuration = 0;

        // Function to update the order summary dynamically
        function updateOrderSummary() {
            const $orderSummaryList = $('.order-summary-list');
            const $subtotal = $('.subtotal');
            const $taxes = $('.taxes');
            const $totalDue = $('.total-due');
            const $note = $('.training-duration-note');

            // Clear the current order summary display
            $orderSummaryList.empty();

            // Update the order summary list
            orderSummary.forEach((item) => {
                $orderSummaryList.append(`
                    <li class="text-sm w-full flex justify-between py-3 border-b border-gray-200 dark:border-gray-700/60">
                        <div>${item.name}</div>
                        <div class="font-medium text-gray-800 dark:text-gray-100">{{ $settings->base_currency }}${item.price.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                    </li>
                `);
            });

            // Calculate totals
            const taxes = (totalPrice * 0.075).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            const cleanTotalPrice = parseFloat(totalPrice.toString().replace(/,/g, ''));
            const cleanTaxes = parseFloat(taxes.toString().replace(/,/g, ''));
            const totalDue = cleanTotalPrice + cleanTaxes

            // Update totals in the DOM
            $subtotal.text(`{{ $settings->base_currency }}${totalPrice.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
            $taxes.text(`{{ $settings->base_currency }}${taxes.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
            $totalDue.text(`{{ $settings->base_currency }}${totalDue.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);

            // Update the training duration note
            if (totalDuration > 0) {
                $note.text(`Note that your total training period is ${totalDuration} weeks.`);
            } else {
                $note.text(`Please select a training objective.`);
            }
        }

        // Event listener for checkbox changes
        const $objectives = $('input[name="selected_objective[]"]');

        $objectives.on('change', function () {
            const $checkbox = $(this);
            const isChecked = $checkbox.is(':checked');
            const $label = $checkbox.closest('label');
            const name = $label.find('h3').text().trim();
            const priceText = $label.find('.inline-flex.text-green-700').text().replace('{{ $settings->base_currency }}', '').replace(/,/g, '').trim();
            const durationText = $label.find('.inline-flex.text-yellow-600').text().replace(' Weeks', '').trim();

            // Validate data
            const price = parseFloat(priceText);
            const duration = parseInt(durationText);

            if (isNaN(price) || isNaN(duration)) {
                return;
            }

            if (isChecked) {
                // Add item to the order summary
                orderSummary.push({ name, price, duration });
                totalPrice += price;
                totalDuration += duration;
            } else {
                // Remove item from the order summary
                const index = orderSummary.findIndex(item => item.name === name);
                if (index > -1) {
                    totalPrice -= orderSummary[index].price;
                    totalDuration -= orderSummary[index].duration;
                    orderSummary.splice(index, 1);
                }
            }

            // Update the order summary display
            updateOrderSummary();
        });

        // Initialize order summary on page load, including objectives re-checked after a failed submission
        updateOrderSummary();
        $objectives.filter(':checked').trigger('change');
    });
</script>
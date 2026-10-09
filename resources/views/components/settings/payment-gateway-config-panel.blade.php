<div class="grow">

    <!-- Panel body -->
    <div class="p-6 flex flex-wrap">
    @foreach ($gateways as $gateway)
        <section class="w-1/3 md:w-1/3 px-6 border-r-2">
            <div class="mb-8">
                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-4">{{ $gateway->name }} Settings</h2>
            </div>

            <!-- Gateway Activation Toggle -->
            <div class="mb-4 flex items-center" 
                x-data="{ checked: {{ $gateway->is_active ? 'true' : 'false' }} }">
                <form method="POST" action="{{ route('payment-gateway-config.toggle', $gateway->id) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="is_active" :value="checked ? 1 : 0">
                    <div class="form-switch">
                        <input type="checkbox" id="toggle-gateway-{{ $gateway->id }}" class="sr-only"
                            x-model="checked" @change="$el.form.submit()" />
                        <label for="toggle-gateway-{{ $gateway->id }}" class="bg-gray-400 dark:bg-gray-700">
                            <span class="bg-white shadow-sm" aria-hidden="true"></span>
                            <span class="sr-only">Toggle Gateway</span>
                        </label>
                    </div>
                </form>
                <div class="text-sm text-gray-500 italic ml-2" 
                    x-text="checked ? 'Gateway Active' : 'Gateway Inactive'">
                </div>
            </div>

            <!-- Existing Form -->
            <div class="py-5">
                <form action="{{ route('payment-gateway-config.update', $gateway->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <!-- Public Key -->
                        <div class="w-full">
                            <label class="text-sm font-medium mb-1" for="public_key_{{ $gateway->id }}">Public Key</label>
                            <input id="public_key_{{ $gateway->id }}" class="form-input w-full" type="text" name="public_key" value="{{ $gateway->public_key ?? '' }}" required />
                        </div>

                        <!-- Secret Key -->
                        <div class="w-full">
                            <label class="text-sm font-medium mb-1" for="secret_key_{{ $gateway->id }}">Secret Key</label>
                            <input id="secret_key_{{ $gateway->id }}" class="form-input w-full" type="password" name="secret_key" value="" autocomplete="new-password" placeholder="{{ $gateway->has_secret_key ? '•••••••• stored — leave blank to keep' : 'Enter secret key' }}" />
                        </div>

                        <!-- Public Key -->
                        <div class="w-full">
                            <label class="text-sm font-medium mb-1" for="merchant_email_{{ $gateway->id }}">Merchant Email</label>
                            <input id="merchant_email_{{ $gateway->id }}" class="form-input w-full" type="text" name="merchant_email" value="{{ $gateway->merchant_email ?? '' }}" required />
                        </div>

                        <!-- Sandbox Mode -->
                        <div class="w-full">
                            <label class="text-sm font-medium mb-1" for="sandbox_{{ $gateway->id }}">Sandbox Mode</label>
                            <select id="sandbox_{{ $gateway->id }}" name="sandbox" class="form-select w-full">
                                <option value="1" {{ $gateway->sandbox ? 'selected' : '' }}>Enabled (Test Mode)</option>
                                <option value="0" {{ !$gateway->sandbox ? 'selected' : '' }}>Disabled (Live Mode)</option>
                            </select>
                        </div>

                        <!-- Save Button -->
                        <div class="w-full text-left">
                            <button type="submit" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white mt-6">
                                Save {{ $gateway->name }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    @endforeach
</div>

</div>
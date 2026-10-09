<div class="grow">

    <!-- Panel body -->
    <div class="p-6 flex space-x-4">
        <section class="w-1/2 px-6">
            <div class="mb-8">
                <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-4">SMTP Settings</h2>
            </div>

            <!-- Payment Details -->
            <div class="py-5">
                <form action="{{ route('email-config.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <div class="w-full">
                                    <label class="text-sm font-medium mb-1" for="from_name">From Name <span class="text-red-500">*</span></label>
                                    <input id="from_name" class="form-input w-full" type="text" value="{{ $emailConfig->from_name ?? '' }}" name="from_name" required />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-full">
                                    <label class="text-sm font-medium mb-1" for="from_email">From Email <span class="text-red-500">*</span></label>
                                    <input id="from_email" class="form-input w-full" type="text" value="{{ $emailConfig->from_email ?? '' }}" name="from_email" required />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-full">
                                    <label class="text-sm font-medium mb-1" for="smtp_username">SMTP Username <span class="text-red-500">*</span></label>
                                    <input id="smtp_username" class="form-input w-full" type="text" value="{{ $emailConfig->smtp_username ?? '' }}" name="smtp_username" required />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-full">
                                    <label class="text-sm font-medium mb-1" for="smtp_password">SMTP Password <span class="text-red-500">*</span></label>
                                    <input id="smtp_password" class="form-input w-full" type="password" value="" name="smtp_password" autocomplete="new-password" placeholder="{{ filled($emailConfig?->smtp_password) ? '•••••••• stored — leave blank to keep' : 'Enter SMTP password' }}" @if (blank($emailConfig?->smtp_password)) required @endif />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-full">
                                    <label class="text-sm font-medium mb-1" for="smtp_host">SMTP Host <span class="text-red-500">*</span></label>
                                    <input id="smtp_host" class="form-input w-full" type="text" value="{{ $emailConfig->smtp_host ?? '' }}" name="smtp_host" required />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-full">
                                    <label class="text-sm font-medium mb-1" for="smtp_port">SMTP Port <span class="text-red-500">*</span></label>
                                    <input id="smtp_port" class="form-input w-full" type="text" value="{{ $emailConfig->smtp_port ?? '' }}" placeholder="465" name="smtp_port" required />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="w-full">
                                    <label class="text-sm font-medium mb-1" for="smtp_encryption">SMTP Encryption <span class="text-red-500">*</span></label>
                                    <select id="smtp_encryption" name="smtp_encryption" class="form-select w-full">
                                        <option value="ssl" {{ ($emailConfig->smtp_encryption ?? '') === 'ssl' ? 'selected' : '' }}>SSL - Recommended</option>
                                        <option value="tls" {{ ($emailConfig->smtp_encryption ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="starttls" {{ ($emailConfig->smtp_encryption ?? '') === 'starttls' ? 'selected' : '' }}>STARTTLS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="w-full text-left">
                                    <button type="submit" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white mt-6">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>                  
                </form>
            </div>
        </section>
    </div>
</div>
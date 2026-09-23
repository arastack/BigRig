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
                            <span class="text-violet-500">Personal Information</span>
                        </div>
                    </div>     
                </li>
                <li>
                    <a class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400" href="javascript:void(0)">2</a>
                    <div class="mt-1">
                        <div class="flex text-sm font-medium text-gray-400 dark:text-gray-500 space-x-2">
                            <span>-&gt;</span>
                            <span class="text-gray-500 dark:text-gray-400">Medical History</span>
                        </div>
                    </div>
                </li>
                <li>
                    <a class="flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400" href="javascript:void(0)">3</a>
                    <div class="mt-1">
                        <div class="flex text-sm font-medium text-gray-400 dark:text-gray-500 space-x-2">
                            <span>-&gt;</span>
                            <span class="text-gray-500 dark:text-gray-400">Training Objective(s)</span>
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

<!-- <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('Create your Account') }}</h1> -->

<div class="space-y-6 mt-6">
    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="firstName">{{ __('First Name') }} <span class="text-red-500">*</span></x-label>
            <x-input id="firstName" type="text" name="firstName" :value="old('firstName')" required autofocus autocomplete="firstName" />
        </div>
        <div class="flex-1">
            <x-label for="middleName">{{ __('Middle Name') }} </x-label>
            <x-input id="middleName" type="text" name="middleName" :value="old('middleName')" autocomplete="middleName" />
        </div>
    </div>

    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="lastName">{{ __('Last Name') }} <span class="text-red-500">*</span></x-label>
            <x-input id="lastName" type="text" name="lastName" :value="old('lastName')" required autocomplete="lastName" />
        </div>
        <div class="flex-1">
            <x-label for="gender">{{ __('Gender') }} <span class="text-red-500">*</span></x-label>
            <div class="flex flex-wrap items-center -m-3">

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="gender_male" name="gender" value="Male" {{ old('gender', 'Male') == 'Male' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Male</span>
                    </label>
                    <!-- End -->
                </div>
        
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="gender_female" name="gender" value="Female" {{ old('gender') == 'Female' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Female</span>
                    </label>
                    <!-- End -->
                </div>                        
            </div>
        </div>
    </div>

    <div>
        <x-label for="mobileNumber">{{ __('Mobile Number') }} <span class="text-red-500">*</span></x-label>
        <x-input id="mobileNumber" type="text" name="mobileNumber" :value="old('mobileNumber')" required autocomplete="tel" inputmode="tel" />
    </div>

    <div>
        <x-label for="email">{{ __('Email Address') }} <span class="text-red-500">*</span></x-label>
        <x-input id="email" type="email" name="email" :value="old('email')" required />
    </div>

    <div>
        <x-label for="password">{{ __('Password') }} <span class="text-red-500">*</span></x-label>
        <x-input id="password" type="password" name="password" required autocomplete="new-password" />
    </div>

    <div>
        <x-label for="password_confirmation">{{ __('Confirm Password') }} <span class="text-red-500">*</span></x-label>
        <x-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
    </div>
</div>
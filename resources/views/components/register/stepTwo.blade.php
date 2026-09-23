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
                            <span class="text-violet-500">Medical History</span>
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

<!-- <h1 class="text-3xl text-gray-800 dark:text-gray-100 font-bold mb-6">{{ __('Tell Us About Your Medicals') }}</h1> -->

<div class="space-y-6 mt-6">
    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="weight">{{ __('Weight (kg)') }} </x-label>
            <x-input id="weight" type="text" name="weight" :value="old('weight')" inputmode="decimal" placeholder="e.g. 75" />
        </div>
        <div class="flex-1">
            <x-label for="height">{{ __('Height (ft)') }} </x-label>
            <x-input id="height" type="text" name="height" :value="old('height')" inputmode="decimal" placeholder="e.g. 5.8" />
        </div>
    </div>


    <h3 class="text-md text-gray-800 dark:text-gray-100 font-bold mt-6">Answer the disability check below appropriately:</h3>

    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="gender">{{ __('Visual Impairment') }} <span class="text-red-500">*</span></x-label>
            <div class="flex flex-wrap items-center -m-3">

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="vi_no" name="visual_impairment" value="None" {{ old('visual_impairment') == 'None' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">None</span>
                    </label>
                    <!-- End -->
                </div>
        
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="vi_ls" name="visual_impairment" value="Long Sightedness" {{ old('visual_impairment') == 'Long Sightedness' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Long Sightedness</span>
                    </label>
                    <!-- End -->
                </div> 
                
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="vi_ss" name="visual_impairment" value="Short Sightedness" {{ old('visual_impairment') == 'Short Sightedness' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Short Sightedness</span>
                    </label>
                    <!-- End -->
                </div> 

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="vi_cb" name="visual_impairment" value="Color Blindness" {{ old('visual_impairment') == 'Color Blindness' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Color Blindness</span>
                    </label>
                    <!-- End -->
                </div> 
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="gender">{{ __('Hearing Aid') }} <span class="text-red-500">*</span></x-label>
            <div class="flex flex-wrap items-center -m-3">

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="ha_no" name="hearing_aid" value="None" {{ old('hearing_aid') == 'None' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">None</span>
                    </label>
                    <!-- End -->
                </div>
        
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="ha_bte" name="hearing_aid" value="BTE" {{ old('hearing_aid') == 'BTE' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">BTE</span>
                    </label>
                    <!-- End -->
                </div> 
                
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="ha_ite" name="hearing_aid" value="ITE" {{ old('hearing_aid') == 'ITE' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">ITE</span>
                    </label>
                    <!-- End -->
                </div> 

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="ha_rite" name="hearing_aid" value="RITE" {{ old('hearing_aid') == 'RITE' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">RITE</span>
                    </label>
                    <!-- End -->
                </div>
                
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="ha_itc" name="hearing_aid" value="ITC" {{ old('hearing_aid') == 'ITC' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">ITC</span>
                    </label>
                    <!-- End -->
                </div>

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="ha_cros" name="hearing_aid" value="CROS" {{ old('hearing_aid') == 'CROS' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">CROS/BiCros</span>
                    </label>
                    <!-- End -->
                </div>
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="gender">{{ __('Physical Disability') }} <span class="text-red-500">*</span></x-label>
            <div class="flex flex-wrap items-center -m-3">

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="pd_no" name="physical_disability" value="None" {{ old('physical_disability') == 'None' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">None</span>
                    </label>
                    <!-- End -->
                </div>
        
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="pd_ul" name="physical_disability" value="Ulcer" {{ old('physical_disability') == 'Ulcer' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Ulcer</span>
                    </label>
                    <!-- End -->
                </div> 
                
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="pd_ca" name="physical_disability" value="Cancer" {{ old('physical_disability') == 'Cancer' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Cancer</span>
                    </label>
                    <!-- End -->
                </div> 

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="pd_ha" name="physical_disability" value="HIV" {{ old('physical_disability') == 'HIV' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">HIV/AIDS</span>
                    </label>
                    <!-- End -->
                </div>
                
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="pd_ap" name="physical_disability" value="Abdominal Pain" {{ old('physical_disability') == 'Abdominal Pain' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Abdominal Pain</span>
                    </label>
                    <!-- End -->
                </div>
            </div>
        </div>
    </div>

    <h3 class="text-md text-gray-800 dark:text-gray-100 font-bold mt-6">Answer the drug test check below appropriately:</h3>

    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="gender">{{ __('Marijuana(Weed) / Cocaine') }} <span class="text-red-500">*</span></x-label>
            <div class="flex flex-wrap items-center -m-3">

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="weed_yes" name="weed" value="Yes" {{ old('weed') == 'Yes' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Yes</span>
                    </label>
                    <!-- End -->
                </div>
        
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="weed_no" name="weed" value="No" {{ old('weed') == 'No' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">No</span>
                    </label>
                    <!-- End -->
                </div>
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="gender">{{ __('Alcohol') }} <span class="text-red-500">*</span></x-label>
            <div class="flex flex-wrap items-center -m-3">

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="alcohol_no" name="alcohol" value="No" {{ old('alcohol') == 'No' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">No</span>
                    </label>
                    <!-- End -->
                </div>

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="alcohol_often" name="alcohol" value="Often" {{ old('alcohol') == 'Often' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Often</span>
                    </label>
                    <!-- End -->
                </div>
        
                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="alcohol_casual" name="alcohol" value="Casually" {{ old('alcohol') == 'Casually' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Casually</span>
                    </label>
                    <!-- End -->
                </div>

                <div class="m-3">
                    <!-- Start -->
                    <label class="flex items-center">
                    <input type="radio" class="form-radio" id="alcohol_daily" name="alcohol" value="Daily User" {{ old('alcohol') == 'Daily User' ? 'checked' : '' }} required />
                        <span class="text-sm ml-2">Daily User</span>
                    </label>
                    <!-- End -->
                </div>
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="gender">{{ __('Are you currently taking any prescribed medications?') }}</x-label>
            <div class="flex flex-wrap items-center -m-3">
                <div class="m-3 w-full">
                    <textarea id="prescribed_meds" name="prescribed_medication" class="form-textarea w-full focus:border-gray-300" rows="4" placeholder="">{{ old('prescribed_medication') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="flex space-x-4">
        <div class="flex-1">
            <x-label for="gender">{{ __('Have you ever failed a drug test?') }}</x-label>
            <div class="flex flex-wrap items-center -m-3">
                <div class="m-3 w-full">
                    <textarea id="drug_test" name="failed_drug_test" class="form-textarea w-full focus:border-gray-300" rows="4" placeholder="">{{ old('failed_drug_test') }}</textarea>
                </div>
            </div>
        </div>
    </div>
    
</div>
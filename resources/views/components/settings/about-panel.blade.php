@props(['about'])

<div class="grow">
    <form action="{{ route('about-company.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <!-- Panel body -->
        <div class="p-6 space-y-6">
            <!-- <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-5">My Account</h2> -->

            <!-- Business Logo -->
            <section>
                <h3 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-1">Banner Section</h3>
                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                    <div class="sm:w-2/5">
                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1" for="banner_title">Banner Title <span class="text-red-500">*</span></label>
                            <div class="mr-4">
                                <input id="banner_title" class="form-input w-full" type="text" value="{{ $about->banner_title }}" name="banner_title" required/>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" for="banner_picture">Banner Picture</label>
                            <div class="mr-4">
                                <input id="banner_picture" class="form-input w-full px-2 py-1" type="file" name="banner_picture" />
                            </div>
                        </div>
                    </div>
                    <div class="sm:w-3/5">
                        <label class="block text-sm font-medium mb-1" for="banner_picture">Current Banner Picture</label>
                        <div class="mr-4">
                            <img class="w-full h-26" src="{{ Storage::url($about->banner_picture) }}" width="80" height="80" alt="{{ $about->banner_title }}" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Business Information -->
            <section>
                <h3 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-1">Company History</h3>
                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                    <div class="sm:w-1/2">
                        <label class="block text-sm font-medium mb-1" for="history_title">History Title <span class="text-red-500">*</span></label>
                        <input id="history_title" class="form-input w-full" type="text" value="{{ $about->history_title }}" name="history_title" required/>
                    </div>
                    <div class="sm:w-1/2">
                        <label class="block text-sm font-medium mb-1" for="training_hours">Total Training Hours</label>
                        <input id="training_hours" class="form-input w-full" type="text" value="{{ $about->training_hours }}" name="training hours"/>
                    </div>
                </div>
                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                    <div class="sm:w-full">
                        <label class="block text-sm font-medium mb-1" for="company_history">About The Company / History of The Company <span class="text-red-500">*</span></label>
                        <textarea name="company_history" class="form-textarea w-full px-2 py-1" id="editor" rows="10" cols="80">{!! clean_html($about->company_history) !!}</textarea>
                    </div>
                </div>
                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                    <div class="sm:w-full">
                        <label class="block text-sm font-medium mb-1" for="mission_statement">Mission Statement <span class="text-red-500">*</span></label>
                        <textarea name="mission_statement" class="form-textarea w-full px-2 py-1" id="editor1" rows="10" cols="80">{!! clean_html($about->mission_statement) !!}</textarea>
                    </div>
                </div>
            </section>

            <!-- Contact Details -->
            <section>
                <h3 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-1">Counters</h3>
                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                    <div class="sm:w-1/2">
                        <label class="block text-sm font-medium mb-1" for="students_count">Total Number of Students</label>
                        <input id="students_count" class="form-input w-full" type="text" value="{{ $about->students_count }}" name="students_count"/>
                    </div>
                    <div class="sm:w-1/2">
                        <label class="block text-sm font-medium mb-1" for="years_of_existence">Company Years of Existence</label>
                        <input id="years_of_existence" class="form-input w-full" type="text" value="{{ $about->years_of_existence }}" name="years_of_existence"/>
                    </div>
                </div>
                <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                    <div class="sm:w-1/2">
                        <label class="block text-sm font-medium mb-1" for="instructors_count">Number of Professional Instructors</label>
                        <input id="instructors_count" class="form-input w-full" type="text" value="{{ $about->instructors_count }}" name="instructors_count"/>
                    </div>
                    <div class="sm:w-1/2">
                        <label class="block text-sm font-medium mb-1" for="pass_rate">Student's Pass/Success Rate <span class="text-red-500">*</span></label>
                        <input id="pass_rate" class="form-input w-full" type="text" value="{{ $about->pass_rate }}" name="pass_rate" required/>
                    </div>
                </div>
            </section>
        </div>

        <!-- Panel footer -->
        <footer>
            <div class="flex flex-col px-6 py-5 border-t border-gray-200 dark:border-gray-700/60">
                <div class="flex self-end">
                    <button type="submit" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white ml-3">Save Changes</button>
                </div>
            </div>
        </footer>
    </form>
</div>
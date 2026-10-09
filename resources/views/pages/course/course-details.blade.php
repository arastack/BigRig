@section('title', $objective->objective)
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full">

        <!-- Page content -->
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row lg:space-x-2 xl:space-x-4">

            <!-- Content -->
            <div>
                <div class="mb-6">
                    <a class="btn-sm px-3 bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300" href="{{ route('course-management') }}">
                        <svg class="fill-current text-gray-400 dark:text-gray-500 mr-2" width="7" height="12" viewBox="0 0 7 12">
                            <path d="M5.4.6 6.8 2l-4 4 4 4-1.4 1.4L0 6z" />
                        </svg>
                        <span>Back To Courses</span>
                    </a>
                </div>

                <div class="grid grid-cols-12 gap-4">
                    <div class="flex flex-col col-span-full sm:col-span-4 xl:col-span-4 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-5 py-3">
                            <header class="flex justify-between items-start mb-2">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Course Name</h2>
                            </header>
                            <div class="flex items-start">
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mr-2">{{ $objective->objective }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col col-span-full sm:col-span-4 xl:col-span-4 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-5 py-3">
                            <header class="flex justify-between items-start mb-2">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Course Duration</h2>
                            </header>
                            <div class="flex items-start">
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mr-2">{{ $objective->duration }} weeks</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col col-span-full sm:col-span-4 xl:col-span-4 bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-5 py-3">
                            <header class="flex justify-between items-start mb-2">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Course Price</h2>
                            </header>
                            <div class="flex items-start">
                                <div class="text-2xl font-bold text-gray-800 dark:text-gray-100 mr-2">{{ $settings->base_currency }}{{ number_format($objective->price, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col col-span-full sm:col-span-full xl:col-span-full bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-5 py-3">
                            <header class="flex justify-between items-start mb-2">
                                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">REQUIREMENT FOR ENROLLING FOR THIS COURSE</h2>
                            </header>
                            <div class="items-start"> 
                                <p class="mb-3">For you to be eligible for enrollment for this course at BigRig International Truck Driving School:</p>                             
                                <div class="course-details space-y-6">
                                    {!! clean_html($objective->requirement) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company information (mobile) -->
                <div class="bg-white dark:bg-gray-800 p-5 shadow-lg rounded-xl mb-6 lg:hidden">
                    <div class="text-center mb-6">
                        <div class="inline-flex mb-3">
                            <img class="w-full h-32" src="{{ Storage::url($objective->image_url) }}" width="64" height="64" alt="{{ $objective->objective }}" />
                        </div>
                        <div class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">{{ $objective->objective }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 italic">{{ count($materials) }} Course Materials</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 italic">{{ count($curriculum) }} Course Curriculum</div>
                    </div>
                </div>

                <hr class="my-6 border-t border-gray-100 dark:border-gray-700/60" />

                <!-- About You -->
                <div>
                    <header class="pb-2 flex items-center">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Course Details</h2>
                    </header>
                    <div class="course-details space-y-6">
                        {!! clean_html($objective->course_details) !!}
                    </div>
                </div>

                <hr class="my-6 border-t border-gray-100 dark:border-gray-700/60" />

                <!-- Things You Might Do -->
                <div>
                    <h2 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-2">Course Materials</h2>
                    <div class="space-y-6">
                        <div class="grid grid-cols-12 gap-6">
                            @foreach ($materials as $material)
                                <div class="relative h-48 col-span-full sm:col-span-6 xl:col-span-3 bg-white dark:bg-gray-800 shadow-sm rounded-xl dark:border-transparent overflow-hidden">
                                    <!-- File Preview -->
                                    @if (in_array(pathinfo($material->file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                        <img class="absolute w-full h-48 object-cover" src="{{ Storage::url($material->file_url) }}" width="286" height="160"/>
                                    @else
                                        <div class="absolute w-full h-full bg-gray-200 flex items-center justify-center">
                                            <img class="w-16 h-16" src="{{ Storage::url('icons/' . pathinfo($material->file_name, PATHINFO_EXTENSION) . '.png') }}" alt="File Icon"/>
                                        </div>
                                    @endif

                                    <!-- Gradient -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-800 to-transparent" aria-hidden="true"></div>
                                    
                                    <!-- Content -->
                                    <div class="relative h-full p-5 flex flex-col justify-end">
                                        <h3 class="text-md text-white font-semibold mt-3 mb-0.5">{{ ucfirst($material->file_name) }}</h3>
                                        
                                        <!-- Button container -->
                                        <div class="absolute bottom-16 right-4 flex flex-col space-y-2">
                                            <!-- Download Button -->
                                            <a href="{{ route('materials.download', $material->id) }}" class="inline-flex items-center text-lg font-medium px-2 py-0.5 bg-yellow-500 text-white rounded-full hover:bg-yellow-600">
                                                <svg class="w-5 h-5 fill-current text-white" viewBox="0 0 32 32">
                                                    <path d="M16 20c.3 0 .5-.1.7-.3l5.7-5.7-1.4-1.4-4 4V8h-2v8.6l-4-4L9.6 14l5.7 5.7c.2.2.4.3.7.3zM9 22h14v2H9z" />
                                                </svg>
                                            </a>

                                            @if (Auth::user()->hasPermission('update_course_management'))
                                            <!-- Delete Button -->
                                            <button type="button" class="text-red-500 hover:text-red-600 rounded-full" @click="$store.deleteMaterial.open({{ $material->id }})" aria-controls="delete-material-modal">
                                                <span class="sr-only">Delete</span>
                                                <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                                    <path d="M13 15h2v6h-2zM17 15h2v6h-2z" />
                                                    <path d="M20 9c0-.6-.4-1-1-1h-6c-.6 0-1 .4-1 1v2H8v2h1v10c0 .6.4 1 1 1h12c.6 0 1-.4 1-1V13h1v-2h-4V9zm-6 1h4v1h-4v-1zm7 3v9H11v-9h10z" />
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Apply section -->
                <div class="mt-6">
                    <div class="flex justify-between items-center">
                        @if (Auth::user()->hasPermission('update_course_management'))
                        <div class="space-y-2">
                            <button class="btn w-full bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white" @click="$store.uploadCourseMaterials.open({id: {{ $objective->id }} })" aria-controls="course-materials-modal">Upload New Material</button>
                        </div>
                        @endif
                    </div>
                </div>

                <hr class="my-6 border-t border-gray-100 dark:border-gray-700/60" />

                <!-- Related Jobs -->
                <div>
                    <h2 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-6">Course Curriculum</h2>
                    <div class="space-y-2 mt-6">
                        @php $canEditCurriculum = Auth::user()->hasPermission('update_course_management'); @endphp
                        @foreach ($curriculum as $curricula)
                            @php $editingThis = $errors->any() && (string) old('curriculum_id') === (string) $curricula->id; @endphp
                            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl px-5 py-4" x-data="{ editing: @js($editingThis) }">
                                <div class="md:flex justify-between items-center space-y-4 md:space-y-0 space-x-2" x-show="!editing">
                                    <!-- Left side -->
                                    <div class="flex items-start space-x-3 md:space-x-4">
                                        <div class="w-9 h-9 shrink-0 mt-1">
                                            <img class="w-9 h-9 rounded-full" src="{{ asset('images/company-icon-03.svg') }}" width="36" height="36" alt="Course Picture" />
                                        </div>
                                        <div>
                                            <a class="inline-flex font-semibold text-gray-800 dark:text-gray-100" href="javascript:void(0)">{{ $curricula->topic }}</a>
                                            <div class="text-sm">{{ $curricula->summary }}</div>
                                        </div>
                                    </div>
                                    <!-- Right side -->
                                    @if ($canEditCurriculum)
                                        <div class="flex items-center space-x-4 pl-10 md:pl-0">
                                            <button type="button" class="text-ink-500 hover:text-ink-950 rounded-full p-1.5" @click="editing = true" aria-label="Edit {{ $curricula->topic }}">
                                                <x-site.icon name="pencil" class="h-5 w-5" />
                                            </button>
                                            <button type="button" class="text-red-500 hover:text-red-600 rounded-full" @click="$store.deleteModal.open({{ $curricula->id }})" aria-controls="delete-modal">
                                                <span class="sr-only">Delete</span>
                                                <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                                    <path d="M13 15h2v6h-2zM17 15h2v6h-2z" />
                                                    <path d="M20 9c0-.6-.4-1-1-1h-6c-.6 0-1 .4-1 1v2H8v2h1v10c0 .6.4 1 1 1h12c.6 0 1-.4 1-1V13h1v-2h-4V9zm-6 1h4v1h-4v-1zm7 3v9H11v-9h10z" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @if ($canEditCurriculum)
                                    <form x-show="editing" x-cloak method="POST" action="{{ route('curriculum.update', $curricula->id) }}" class="space-y-3">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="curriculum_id" value="{{ $curricula->id }}">
                                        <div>
                                            <label class="portal-label" for="curriculum_topic_{{ $curricula->id }}">Topic</label>
                                            <input id="curriculum_topic_{{ $curricula->id }}" name="topic" value="{{ $editingThis ? old('topic') : $curricula->topic }}" required maxlength="255" class="portal-input">
                                        </div>
                                        <div>
                                            <label class="portal-label" for="curriculum_summary_{{ $curricula->id }}">Summary</label>
                                            <textarea id="curriculum_summary_{{ $curricula->id }}" name="summary" rows="3" required class="portal-input">{{ $editingThis ? old('summary') : $curricula->summary }}</textarea>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" class="btn-ghost btn-sm" @click="editing = false">Cancel</button>
                                            <button class="btn-dark btn-sm">Save topic</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="hidden lg:block space-y-4">

                <!-- Company information (desktop) -->
                <div class="bg-white dark:bg-gray-800 p-5 shadow-sm rounded-xl lg:w-72 xl:w-80">
                    <div class="text-center mb-6">
                        <div class="inline-flex mb-3">
                            <img class="w-full h-48" src="{{ Storage::url($objective->image_url) }}" width="64" height="64" alt="{{ $objective->objective }}" />
                        </div>
                        <div class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-1">{{ $objective->objective }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 italic">{{ count($materials) }} Course Materials</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 italic">{{ count($curriculum) }} Course Curriculum</div>
                    </div>
                    @if (Auth::user()->hasPermission('update_course_management'))
                    <div class="space-y-2">
                        <button class="btn w-full bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white" @click="$store.updateCourseDetails.open({id: {{ $objective->id }}})" aria-controls="course-details-modal">Update Course Details -&gt;</button>
                    </div>                    
                    @endif
                </div>

                <div class="py-8 px-4 lg:px-3 3xl:px-9">
                    <div class="max-w-sm mx-auto lg:max-w-none">
                        <h2 class="text-lg text-gray-800 dark:text-gray-100 font-bold mb-6">New Curriculum Form</h2>
                        <div class="space-y-6">
                            <form action="{{ route('curriculum.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                <div>
                                    <div class="space-y-4">
                                        <!-- New curriculm topic -->
                                        <div>
                                            <label class="block text-sm font-medium mb-1" for="card-nr">Topic <span class="text-red-500">*</span></label>
                                            <input type="hidden" name="objective_id" value="{{ $objective->id }}" required>
                                            <input id="card-nr" class="form-input w-full" type="text" name="topic" placeholder="Introduction to Commercial Trucking" required/>
                                        </div>

                                        <!-- New Curriculum Summary -->
                                        <div>
                                            <label class="block text-sm font-medium mb-1" for="card-name">Summary <span class="text-red-500">*</span></label>
                                            <textarea id="feedback" class="form-textarea w-full focus:border-gray-300" name="summary" rows="4" placeholder="Overview of the trucking industry, career opportunities, and job responsibilities." required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <div class="mb-4">
                                        <button type="submit" class="btn w-full bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">Submit</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Edit Course Details Modal -->
    @include('components.modals.edit-course-details-modal')
    <!-- End -->

    <!-- Upload Course Materials Modal -->
    @include('components.modals.upload-course-materials-modal')
    <!-- End -->

    <!-- Delete Course Curriculum Modal -->
    @include('components.modals.delete-course-curriculum-modal')
    <!-- End -->

    <!-- Delete Course Material Modal -->
    @include('components.modals.delete-course-material-modal')
    <!-- End -->
</x-app-layout>

@include('pages.app-layout-scripts.course-details')

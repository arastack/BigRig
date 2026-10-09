{{-- resources/views/student/course-details.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Course Header --}}
        <div class="mb-8">
            {{-- Back Button --}}
            <a href="{{ route('student.courses') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Courses
            </a>

            {{-- Course Title Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                @if($course->image_url)
                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ Storage::url($course->image_url) }}')">
                        <div class="h-full bg-gradient-to-t from-black/60 to-transparent flex items-end">
                            <div class="p-6 text-white">
                                <h1 class="text-3xl font-bold">{{ $course->objective }}</h1>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6">
                        <h1 class="text-3xl font-bold text-white">{{ $course->objective }}</h1>
                    </div>
                @endif

                {{-- Course Info Bar --}}
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Instructor</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">
                                @if($instructorAssignment && $instructorAssignment->instructor)
                                    {{ $instructorAssignment->instructor->firstName }} {{ $instructorAssignment->instructor->lastName }}
                                @else
                                    <span class="text-yellow-600">Pending Assignment</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Duration</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $course->duration }} weeks</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Price</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">₦{{ number_format($course->price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Examination</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $course->examination }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Progress Overview --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Overall Progress Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Overall Progress</h3>
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                Progress
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-blue-600">
                                {{ $stats['overall_progress'] }}%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                        <div style="width:{{ $stats['overall_progress'] }}%"
                             class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"></div>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Classes Completed</span>
                        <span class="font-medium">{{ $stats['completed_classes'] }}/{{ $stats['total_classes'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Topics Covered</span>
                        <span class="font-medium">{{ $stats['total_topics'] }} topics</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Materials Available</span>
                        <span class="font-medium">{{ $stats['total_materials'] }} files</span>
                    </div>
                </div>
            </div>

            {{-- Theory Progress Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Theory Sessions</h3>
                <div class="flex items-center justify-center mb-4">
                    <div class="relative">
                        <svg class="w-32 h-32">
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200 dark:text-gray-700"></circle>
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none"
                                    class="text-purple-600"
                                    stroke-dasharray="{{ 352 * $stats['theory_progress'] / 100 }} 352"
                                    stroke-dashoffset="0"
                                    transform="rotate(-90 64 64)"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['theory_progress'] }}%</span>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $theoryCompleted }} of {{ $theoryTotal }} completed</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $course->theory_session }} hours total</p>
                </div>
            </div>

            {{-- Practical Progress Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Practical Sessions</h3>
                <div class="flex items-center justify-center mb-4">
                    <div class="relative">
                        <svg class="w-32 h-32">
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200 dark:text-gray-700"></circle>
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="none"
                                    class="text-green-600"
                                    stroke-dasharray="{{ 352 * $stats['practical_progress'] / 100 }} 352"
                                    stroke-dashoffset="0"
                                    transform="rotate(-90 64 64)"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['practical_progress'] }}%</span>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $practicalCompleted }} of {{ $practicalTotal }} completed</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $course->practical_session }} hours total</p>
                </div>
            </div>
        </div>

        {{-- Today's Class & Next Class --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            {{-- Today's Class --}}
            @if($todaySchedule)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Today's Class</h3>
                    </div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $todaySchedule->topic->topic }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ $todaySchedule->time_start }} - {{ $todaySchedule->time_stop }}
                    </p>
                    <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full
                        {{ $todaySchedule->session_type == 'theory' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($todaySchedule->session_type) }}
                    </span>
                </div>
            @endif

            {{-- Next Class --}}
            @if($nextClass)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
                    <div class="flex items-center mb-3">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Next Class</h3>
                    </div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $nextClass->topic->topic }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        {{ Carbon::parse($nextClass->schedule_date)->format('l, M d, Y') }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $nextClass->time_start }} - {{ $nextClass->time_stop }}
                    </p>
                    <span class="inline-block mt-2 px-2 py-1 text-xs rounded-full
                        {{ $nextClass->session_type == 'theory' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($nextClass->session_type) }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Tabs Navigation --}}
        <div class="mb-8">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button onclick="showTab('curriculum')" id="curriculum-tab"
                        class="tab-button px-3 py-2 font-medium text-sm rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                    Curriculum
                </button>
                <button onclick="showTab('materials')" id="materials-tab"
                        class="tab-button px-3 py-2 font-medium text-sm rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Materials
                </button>
                <button onclick="showTab('schedule')" id="schedule-tab"
                        class="tab-button px-3 py-2 font-medium text-sm rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Schedule
                </button>
                <button onclick="showTab('requirements')" id="requirements-tab"
                        class="tab-button px-3 py-2 font-medium text-sm rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    Requirements
                </button>
            </nav>
        </div>

        {{-- Tab Contents --}}
        {{-- Curriculum Tab --}}
        <div id="curriculum-content" class="tab-content">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Course Curriculum</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $curriculum->count() }} topics to master</p>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($curriculum as $index => $topic)
                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $index + 1 }}</span>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">
                                        {{ $topic->topic }}
                                    </h3>
                                    @if($topic->summary)
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $topic->summary }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Materials Tab --}}
        <div id="materials-content" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Course Materials</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $materials->count() }} files available</p>
                </div>
                @if($materials->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($materials as $material)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        @php
                                            $extension = pathinfo($material->file_name, PATHINFO_EXTENSION);
                                            $iconColor = match(strtolower($extension)) {
                                                'pdf' => 'text-red-500',
                                                'doc', 'docx' => 'text-blue-500',
                                                default => 'text-gray-500'
                                            };
                                        @endphp
                                        <svg class="w-8 h-8 {{ $iconColor }} mr-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $material->file_name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                                Uploaded {{ $material->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.download-material', $material->id) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No materials uploaded yet</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Schedule Tab --}}
        <div id="schedule-content" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Upcoming Classes</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $upcomingSchedules->count() }} classes scheduled</p>
                </div>
                @if($upcomingSchedules->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($upcomingSchedules as $schedule)
                            <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">
                                            {{ $schedule->topic->topic }}
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ Carbon::parse($schedule->schedule_date)->format('l, M d, Y') }} •
                                            {{ $schedule->time_start }} - {{ $schedule->time_stop }}
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $schedule->session_type == 'theory' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                        {{ ucfirst($schedule->session_type) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No upcoming classes scheduled</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Requirements Tab --}}
        <div id="requirements-content" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Course Requirements</h2>
                <div class="prose dark:prose-invert max-w-none">
                    {!! clean_html($course->requirement) !!}
                </div>

                @if($course->course_details)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Additional Details</h3>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! clean_html($course->course_details) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab Switching Script --}}
    <script>
        function showTab(tabName) {
            // Hide all content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900/50', 'dark:text-blue-300');
                button.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });

            // Show selected content
            document.getElementById(tabName + '-content').classList.remove('hidden');

            // Add active class to selected tab
            const activeTab = document.getElementById(tabName + '-tab');
            activeTab.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            activeTab.classList.add('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900/50', 'dark:text-blue-300');
        }
    </script>
</x-app-layout>

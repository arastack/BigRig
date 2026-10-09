{{-- resources/views/instructor/students/show.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Back Button --}}
        <a href="{{ route('instructor.students') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Students
        </a>

        {{-- Student Header --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="h-16 w-16 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                            <span class="text-xl font-bold text-gray-700 dark:text-gray-300">
                                {{ substr($student->firstName, 0, 1) }}{{ substr($student->lastName, 0, 1) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $student->firstName }} {{ $student->lastName }}
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400">{{ $student->email }}</p>
                            <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500 dark:text-gray-400">
                                <span>📱 {{ $student->phoneNumber }}</span>
                                <span>🏫 Batch: {{ $student->enrolment_batch_id }}</span>
                                @if($subscription)
                                    <span>📅 Enrolled: {{ $subscription->created_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold {{ $overallStats['average_attendance'] >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'text-green-600' : 'text-red-600' }}">
                            {{ round($overallStats['average_attendance']) }}%
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Overall Attendance</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-full p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Courses</p>
                        <p class="text-lg font-semibold">{{ $overallStats['total_courses'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-full p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Classes Attended</p>
                        <p class="text-lg font-semibold">{{ $overallStats['total_classes_attended'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-full p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Classes</p>
                        <p class="text-lg font-semibold">{{ $overallStats['total_classes'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-full p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Status</p>
                        <p class="text-lg font-semibold">
                            @if($overallStats['average_attendance'] >= \App\Support\AdmissionSettings::attendanceThreshold())
                                <span class="text-green-600">Good</span>
                            @elseif($overallStats['average_attendance'] >= 60)
                                <span class="text-yellow-600">Warning</span>
                            @else
                                <span class="text-red-600">Critical</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column - Course Progress --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            Course Progress
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($enrolledCourses as $course)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $course->objective }}
                                        </h3>
                                        <span class="text-sm font-semibold {{ $course->attendance_rate >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $course->attendance_rate }}% Attendance
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Theory Progress</p>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $course->theory_progress }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $course->theory_progress }}% Complete</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Practical Progress</p>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $course->practical_progress }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $course->practical_progress }}% Complete</p>
                                        </div>
                                    </div>

                                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                                        <span>Classes: {{ $course->classes_attended }}/{{ $course->total_classes }}</span>
                                        <span>Duration: {{ $course->duration }} weeks</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Recent Attendance --}}
            <div>
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            Recent Attendance
                        </h2>
                    </div>
                    <div class="p-6">
                        @if($recentAttendance->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentAttendance as $record)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ Carbon::parse($record->schedule->schedule_date)->format('M d, Y') }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $record->schedule->topic->topic ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($record->status == 'present') bg-green-100 text-green-800
                                            @elseif($record->status == 'late') bg-yellow-100 text-yellow-800
                                            @elseif($record->status == 'absent') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-gray-500 dark:text-gray-400">No attendance records yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

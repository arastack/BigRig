{{-- resources/views/student/progress.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                My Progress
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Track your learning journey and achievements
            </p>
        </div>

        {{-- Overall Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Total Courses</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ $overallStats['total_courses'] }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Average Progress</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ round($overallStats['average_progress']) }}%
                        </h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Attendance Rate</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ round($overallStats['average_attendance']) }}%
                        </h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Classes Completed</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ $overallStats['completed_classes'] }}/{{ $overallStats['total_classes'] }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Course Progress Cards --}}
        <div class="space-y-6">
            @foreach($coursesProgress as $progress)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
                    {{-- Course Header --}}
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-bold">{{ $progress['course']->objective }}</h2>
                                <p class="text-blue-100 mt-1">
                                    Instructor:
                                    @if($progress['instructor'])
                                        {{ $progress['instructor']->firstName }} {{ $progress['instructor']->lastName }}
                                    @else
                                        <span class="text-yellow-300">Not Assigned</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold">{{ $progress['overall_progress'] }}%</div>
                                <div class="text-sm text-blue-100">Overall Progress</div>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Details --}}
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Theory Progress --}}
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Theory Sessions</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $progress['theory_completed'] }}/{{ $progress['theory_total'] }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-purple-600 h-2.5 rounded-full"
                                         style="width: {{ $progress['theory_total'] > 0 ? ($progress['theory_completed'] / $progress['theory_total']) * 100 : 0 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $progress['course']->theory_session }} hours total
                                </p>
                            </div>

                            {{-- Practical Progress --}}
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Practical Sessions</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $progress['practical_completed'] }}/{{ $progress['practical_total'] }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full"
                                         style="width: {{ $progress['practical_total'] > 0 ? ($progress['practical_completed'] / $progress['practical_total']) * 100 : 0 }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $progress['course']->practical_session }} hours total
                                </p>
                            </div>

                            {{-- Attendance --}}
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Attendance Rate</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $progress['attendance_rate'] }}%
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full {{ $progress['attendance_rate'] >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'bg-blue-600' : 'bg-yellow-600' }}"
                                         style="width: {{ $progress['attendance_rate'] }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $progress['attendance_rate'] >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'Good Standing' : 'Needs Improvement' }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-6 flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $progress['classes_completed'] }} classes completed
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $progress['classes_total'] - $progress['classes_completed'] }} remaining
                                </span>
                            </div>
                            <a href="{{ route('student.course-details', $progress['course']->id) }}"
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Empty State --}}
        @if($coursesProgress->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Progress to Track</h3>
                <p class="text-gray-500 dark:text-gray-400">You need to be enrolled in courses to track your progress.</p>
            </div>
        @endif
    </div>
</x-app-layout>

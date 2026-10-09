{{-- resources/views/instructor/students/course.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Back Button --}}
        <a href="{{ route('instructor.students') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to All Students
        </a>

        {{-- Course Header --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $course->objective }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Student enrollment and progress for this course
                        </p>
                        <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500 dark:text-gray-400">
                            <span>📚 Duration: {{ $course->duration }} weeks</span>
                            <span>💰 Price: ₦{{ number_format($course->price, 2) }}</span>
                            <span>📝 Examination: {{ $course->examination }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">{{ $students->count() }}</div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Students</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Course Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Enrolled</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $courseStats['total_students'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Avg Attendance</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ round($courseStats['average_attendance']) }}%</h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Sessions</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $courseStats['completed_sessions'] }}/{{ $courseStats['total_sessions'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Progress</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ $courseStats['total_sessions'] > 0 ? round(($courseStats['completed_sessions'] / $courseStats['total_sessions']) * 100) : 0 }}%
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Students Table --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Enrolled Students
                    </h2>
                    <div class="flex items-center space-x-2">
                        <input type="text"
                               id="searchInput"
                               placeholder="Search students..."
                               class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">

                        <button onclick="exportAttendance()"
                                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Export
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Attendance
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Classes
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Last Present
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($students as $index => $student)
                            <tr class="student-row hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                data-name="{{ strtolower($student->firstName . ' ' . $student->lastName) }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr($student->firstName, 0, 1) }}{{ substr($student->lastName, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $student->firstName }} {{ $student->lastName }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                ID: {{ $student->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $student->email }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $student->phoneNumber }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm font-semibold
                                                    {{ $student->attendance_rate >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'text-green-600' : ($student->attendance_rate >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                                    {{ $student->attendance_rate }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $student->attendance_rate >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'bg-green-600' : ($student->attendance_rate >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}"
                                                     style="width: {{ $student->attendance_rate }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="text-gray-900 dark:text-gray-100">
                                        Present: {{ $student->classes_attended }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Absent: {{ $student->classes_absent }} | Late: {{ $student->classes_late }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($student->last_attendance)
                                        {{ \Carbon\Carbon::parse($student->last_attendance)->format('M d, Y') }}
                                    @else
                                        Never
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($student->attendance_rate >= \App\Support\AdmissionSettings::attendanceThreshold())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Good
                                        </span>
                                    @elseif($student->attendance_rate >= 60)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Warning
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Critical
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('instructor.student.show', $student->id) }}"
                                       class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Students Enrolled</h3>
                                    <p class="text-gray-500 dark:text-gray-400">No students are currently enrolled in this course.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Attendance Summary --}}
        @if($students->count() > 0)
            <div class="mt-6 bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    Attendance Summary
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $students->where('attendance_rate', '>=', \App\Support\AdmissionSettings::attendanceThreshold())->count() }}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Good Attendance (≥{{ \App\Support\AdmissionSettings::attendanceThreshold() }}%)</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">
                            {{ $students->where('attendance_rate', '>=', 60)->where('attendance_rate', '<', \App\Support\AdmissionSettings::attendanceThreshold())->count() }}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Warning (60-74%)</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">
                            {{ $students->where('attendance_rate', '<', 60)->count() }}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Critical (<60%)</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- JavaScript for search --}}
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');

            rows.forEach(row => {
                const name = row.dataset.name;
                if (name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Export functionality (placeholder)
        function exportAttendance() {
            alert('Export functionality will be implemented soon!');
            // You can implement CSV export here
        }
    </script>
</x-app-layout>

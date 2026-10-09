{{-- resources/views/instructor/students/index.blade.php --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                My Students
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Manage and track student progress across all your courses
            </p>
        </div>

        {{-- Filter Tabs --}}
        <div class="mb-6">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button onclick="filterStudents('all')"
                        class="filter-tab active px-3 py-2 font-medium text-sm rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                    All Students ({{ $students->count() }})
                </button>
                @foreach($courses as $course)
                    <button onclick="filterStudents('{{ $course->id }}')"
                            class="filter-tab px-3 py-2 font-medium text-sm rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        {{ $course->objective }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Students Table --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Student List
                    </h2>
                    <div class="flex items-center space-x-2">
                        <input type="text"
                               id="searchInput"
                               placeholder="Search students..."
                               class="px-3 py-1.5 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Courses
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Attendance
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Classes
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
                        @foreach($students as $student)
                            <tr class="student-row hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                data-courses="{{ $student->courses->pluck('id')->join(',') }}"
                                data-name="{{ strtolower($student->firstName . ' ' . $student->lastName) }}">
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
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($student->courses as $course)
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                                {{ Str::limit($course->objective, 20) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            <div class="flex items-center">
                                                <span class="text-sm font-semibold
                                                    {{ $student->attendance_rate >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $student->attendance_rate }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                <div class="h-1.5 rounded-full {{ $student->attendance_rate >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'bg-green-600' : 'bg-red-600' }}"
                                                     style="width: {{ $student->attendance_rate }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $student->classes_attended }}/{{ $student->total_classes }}
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($students->isEmpty())
                <div class="p-12 text-center">
                    <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No Students Assigned</h3>
                    <p class="text-gray-500 dark:text-gray-400">Students will appear here once they are assigned to your courses.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- JavaScript for filtering and search --}}
    <script>
        function filterStudents(courseId) {
            const rows = document.querySelectorAll('.student-row');
            const tabs = document.querySelectorAll('.filter-tab');

            // Update active tab
            tabs.forEach(tab => {
                tab.classList.remove('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900/50', 'dark:text-blue-300');
                tab.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            });
            event.target.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-300');
            event.target.classList.add('bg-blue-100', 'text-blue-700', 'dark:bg-blue-900/50', 'dark:text-blue-300');

            // Filter rows
            rows.forEach(row => {
                if (courseId === 'all') {
                    row.style.display = '';
                } else {
                    const courses = row.dataset.courses.split(',');
                    if (courses.includes(courseId)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }

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
    </script>
</x-app-layout>

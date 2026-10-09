@php
use Carbon\Carbon;
@endphp
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    My Schedule
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    View and manage your teaching schedule
                </p>
            </div>
            <div class="flex items-center space-x-2">
                {{-- View Toggle --}}
                <div class="flex bg-white dark:bg-gray-800 rounded-lg shadow">
                    <a href="{{ route('instructor.schedule', ['view' => 'week']) }}"
                       class="px-3 py-2 text-sm font-medium rounded-l-lg {{ $view == 'week' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                        Week
                    </a>
                    <a href="{{ route('instructor.schedule', ['view' => 'month']) }}"
                       class="px-3 py-2 text-sm font-medium {{ $view == 'month' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                        Month
                    </a>
                    <a href="{{ route('instructor.schedule', ['view' => 'list']) }}"
                       class="px-3 py-2 text-sm font-medium {{ $view == 'list' ? 'bg-blue-600 text-white' : 'text-gray-600 hover:text-gray-900' }}">
                        List
                    </a>
                    <a href="{{ route('instructor.schedule.calendar') }}"
                       class="px-3 py-2 text-sm font-medium rounded-r-lg text-gray-600 hover:text-gray-900">
                        Calendar
                    </a>
                </div>

                {{-- Export Button --}}
                <div class="relative">
                    <button onclick="toggleExportMenu()"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export
                    </button>
                    <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-10">
                        <a href="{{ route('instructor.schedule.export', ['format' => 'pdf', 'range' => 'week']) }}"
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Export Week as PDF
                        </a>
                        <a href="{{ route('instructor.schedule.export', ['format' => 'pdf', 'range' => 'month']) }}"
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Export Month as PDF
                        </a>
                        <a href="{{ route('instructor.schedule.export', ['format' => 'excel', 'range' => 'all']) }}"
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Export All as Spreadsheet (CSV)
                        </a>
                        <a href="{{ route('instructor.schedule.export', ['format' => 'ics', 'range' => 'all']) }}"
                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Export to Calendar (ICS)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Today</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['classes_today'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">This Week</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total_classes_week'] }}</h3>
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
                        <span class="text-gray-500 text-sm">This Month</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total_classes_month'] }}</h3>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-full p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <span class="text-gray-500 text-sm">Pending Requests</span>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['pending_requests'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Schedule View --}}
        @if($view == 'week')
            {{-- Week View --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Week of {{ $currentDate->startOfWeek()->format('M d') }} - {{ $currentDate->endOfWeek()->format('M d, Y') }}
                    </h2>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('instructor.schedule', ['view' => 'week', 'date' => $currentDate->copy()->subWeek()->format('Y-m-d')]) }}"
                           class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                        <a href="{{ route('instructor.schedule', ['view' => 'week', 'date' => Carbon::now()->format('Y-m-d')]) }}"
                           class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            Today
                        </a>
                        <a href="{{ route('instructor.schedule', ['view' => 'week', 'date' => $currentDate->copy()->addWeek()->format('Y-m-d')]) }}"
                           class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-7 gap-4">
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $index => $day)
                            @php
                                $dayDate = $currentDate->copy()->startOfWeek()->addDays($index);
                                $daySchedules = $schedules->filter(function($schedule) use ($dayDate) {
                                    return Carbon::parse($schedule->schedule_date)->isSameDay($dayDate);
                                });
                            @endphp
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 {{ $dayDate->isToday() ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ $day }}
                                    <div class="text-xs text-gray-500">{{ $dayDate->format('M d') }}</div>
                                </div>
                                @if($daySchedules->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($daySchedules as $schedule)
                                            <a href="{{ route('instructor.schedule.show', $schedule->id) }}"
                                               class="block p-2 bg-{{ $schedule->session_type == 'theory' ? 'purple' : 'green' }}-100 dark:bg-{{ $schedule->session_type == 'theory' ? 'purple' : 'green' }}-900/50 rounded text-xs hover:shadow">
                                                <div class="font-medium text-{{ $schedule->session_type == 'theory' ? 'purple' : 'green' }}-800 dark:text-{{ $schedule->session_type == 'theory' ? 'purple' : 'green' }}-200">
                                                    {{ $schedule->time_start }} - {{ $schedule->time_stop }}
                                                </div>
                                                <div class="text-gray-600 dark:text-gray-400 truncate">
                                                    {{ Str::limit($schedule->course->objective, 20) }}
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 italic">No classes</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        @elseif($view == 'list')
            {{-- List View --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                        Upcoming Classes
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Course
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Topic
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Batch
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($schedules as $schedule)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ Carbon::parse($schedule->schedule_date)->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $schedule->time_start }} - {{ $schedule->time_stop }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $schedule->course->objective }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $schedule->topic->topic }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $schedule->session_type == 'theory' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                            {{ ucfirst($schedule->session_type ?? 'theory') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $schedule->batch->batch_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('instructor.schedule.show', $schedule->id) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                View
                                            </a>
                                            <a href="{{ route('instructor.schedule.request-change', $schedule->id) }}"
                                               class="text-yellow-600 hover:text-yellow-900">
                                                Request Change
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        No scheduled classes found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($view == 'list' && method_exists($schedules, 'links'))
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $schedules->links() }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Today's Classes --}}
        @if($todaySchedules->count() > 0)
            <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-4">
                    Today's Classes
                </h3>
                <div class="space-y-3">
                    @foreach($todaySchedules as $schedule)
                        <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $schedule->time_start }} - {{ $schedule->time_stop }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $schedule->course->objective }} - {{ $schedule->topic->topic }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $schedule->session_type == 'theory' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($schedule->session_type ?? 'theory') }}
                                </span>
                                <a href="{{ route('instructor.attendance.mark', $schedule->id) }}"
                                   class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded">
                                    Mark Attendance
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        function toggleExportMenu() {
            document.getElementById('exportMenu').classList.toggle('hidden');
        }

        // Close export menu when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.bg-green-600')) {
                var dropdowns = document.getElementsByClassName("absolute");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (!openDropdown.classList.contains('hidden')) {
                        openDropdown.classList.add('hidden');
                    }
                }
            }
        }
    </script>
</x-app-layout>

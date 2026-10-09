
@php
    use Carbon\Carbon;
@endphp
<x-app-layout>

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        {{-- Header --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">
                    Attendance Report
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    Comprehensive attendance analysis for your students
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="window.print()"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Report
                </button>
                <button onclick="exportToExcel()"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export to Excel
                </button>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    Report Filters
                </h2>
            </div>
            <form method="GET" action="{{ route('instructor.attendance.report') }}" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Course
                        </label>
                        <select name="course_id" id="course_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->objective }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="batch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Batch
                        </label>
                        <select name="batch_id" id="batch_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">All Batches</option>
                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->batch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            From Date
                        </label>
                        <input type="date" name="date_from" id="date_from"
                               value="{{ request('date_from', Carbon::now()->subMonth()->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            To Date
                        </label>
                        <input type="date" name="date_to" id="date_to"
                               value="{{ request('date_to', Carbon::now()->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        {{-- Summary Statistics --}}
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="text-center">
                    <h3 class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['total_sessions'] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Sessions</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="text-center">
                    <h3 class="text-3xl font-bold text-green-600">{{ $stats['total_present'] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Present</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="text-center">
                    <h3 class="text-3xl font-bold text-red-600">{{ $stats['total_absent'] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Absent</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="text-center">
                    <h3 class="text-3xl font-bold text-yellow-600">{{ $stats['total_late'] }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Late</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-5">
                <div class="text-center">
                    <h3 class="text-3xl font-bold text-blue-600">{{ round($stats['average_attendance']) }}%</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Avg Attendance</p>
                </div>
            </div>
        </div>

        {{-- Chart Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Attendance Trend Chart --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    Attendance Trend
                </h3>
                <div style="position: relative; height:300px;">
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>

            {{-- Status Distribution Chart --}}
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                    Status Distribution
                </h3>
                <div style="position: relative; height:300px;">
                    <canvas id="statusDistributionChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Detailed Report Table --}}
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                    Student Attendance Details
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Course
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total Classes
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Present
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Absent
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Late
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Excused
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Attendance %
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($studentReports as $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr($report['student']->firstName, 0, 1) }}{{ substr($report['student']->lastName, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $report['student']->firstName }} {{ $report['student']->lastName }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $report['student']->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ isset($report['course']) ? $report['course']->objective : 'All Courses' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $report['total_classes'] }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-green-600 font-medium">
                                    {{ $report['present'] }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-red-600 font-medium">
                                    {{ $report['absent'] }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-yellow-600 font-medium">
                                    {{ $report['late'] }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-blue-600 font-medium">
                                    {{ $report['excused'] }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center">
                                        <span class="text-sm font-bold
                                            {{ $report['attendance_rate'] >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'text-green-600' : ($report['attendance_rate'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $report['attendance_rate'] }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                        <div class="h-1.5 rounded-full {{ $report['attendance_rate'] >= \App\Support\AdmissionSettings::attendanceThreshold() ? 'bg-green-600' : ($report['attendance_rate'] >= 60 ? 'bg-yellow-600' : 'bg-red-600') }}"
                                             style="width: {{ $report['attendance_rate'] }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($report['attendance_rate'] >= \App\Support\AdmissionSettings::attendanceThreshold())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Good
                                        </span>
                                    @elseif($report['attendance_rate'] >= 60)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Warning
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Critical
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    No attendance data available for the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Chart.js Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prepare data
            const presentCount = {{ $stats['total_present'] ?? 0 }};
            const absentCount = {{ $stats['total_absent'] ?? 0 }};
            const lateCount = {{ $stats['total_late'] ?? 0 }};
            const excusedCount = {{ isset($stats['total_excused']) ? $stats['total_excused'] : 0 }};

            // Check if we have data for charts
            const hasData = presentCount + absentCount + lateCount + excusedCount > 0;

            // Attendance Trend Chart
            const trendCanvas = document.getElementById('attendanceTrendChart');
            if (trendCanvas) {
                const trendCtx = trendCanvas.getContext('2d');

                @if(isset($chartData) && !empty($chartData['dates']))
                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($chartData['dates'] ?? []) !!},
                            datasets: [{
                                label: 'Attendance Rate (%)',
                                data: {!! json_encode($chartData['rates'] ?? []) !!},
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.y + '%';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: {
                                        callback: function(value) {
                                            return value + '%';
                                        }
                                    }
                                }
                            }
                        }
                    });
                @else
                    // Show message when no trend data
                    trendCtx.font = "16px Arial";
                    trendCtx.fillStyle = "#9CA3AF";
                    trendCtx.textAlign = "center";
                    trendCtx.fillText("No trend data available", trendCanvas.width / 2, trendCanvas.height / 2);
                @endif
            }

            // Status Distribution Chart
            const statusCanvas = document.getElementById('statusDistributionChart');
            if (statusCanvas) {
                const statusCtx = statusCanvas.getContext('2d');

                if (hasData) {
                    new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Present', 'Absent', 'Late', 'Excused'],
                            datasets: [{
                                data: [presentCount, absentCount, lateCount, excusedCount],
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(239, 68, 68, 0.8)',
                                    'rgba(245, 158, 11, 0.8)',
                                    'rgba(59, 130, 246, 0.8)'
                                ],
                                borderColor: [
                                    'rgb(34, 197, 94)',
                                    'rgb(239, 68, 68)',
                                    'rgb(245, 158, 11)',
                                    'rgb(59, 130, 246)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        usePointStyle: true
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    // Show message when no data
                    statusCtx.font = "16px Arial";
                    statusCtx.fillStyle = "#9CA3AF";
                    statusCtx.textAlign = "center";
                    statusCtx.fillText("No attendance data available", statusCanvas.width / 2, statusCanvas.height / 2);
                }
            }
        });

        // Export to Excel function
        function exportToExcel() {
            alert('Export functionality will be implemented with Laravel Excel package');
        }
    </script>
</x-app-layout>

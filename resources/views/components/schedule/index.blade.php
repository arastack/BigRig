<div class="space-y-8">
    <!-- Alert -->
     @if (Auth::user()->hasPermission('create_training_schedule'))
    <div class="relative bg-[linear-gradient(135deg,var(--tw-gradient-stops))] from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04] rounded-lg p-5">
        <div class="relative">
            <div class="text-sm font-medium text-gray-800 dark:text-violet-200 mb-2">Assign Training Schedule</div>
      
            <!-- Business Information -->
            <form action="{{ route('schedule.create') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <section>
                    <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                        <div class="sm:w-1/6">
                            <label class="block text-sm font-medium mb-1" for="batch_id">Batch <span class="text-red-500">*</span></label>
                            <select id="batch_0" name="batch_id" class="form-select w-full dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium">
                                <option>-Select-</option>
                                @foreach ($batches as $batch)
                                    <option value="{{ $batch->id }}" {{ $batch->active_batch ? 'selected' : '' }}>
                                        {{ $batch->batch_name }}
                                    </option>                                
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:w-1/6">
                            <label class="block text-sm font-medium mb-1" for="course_id">Course <span class="text-red-500">*</span></label>
                            <select id="course_0" name="course_id" class="form-select w-full dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium">
                                <option value="">-Select-</option>
                                @foreach ($objectives as $objective)
                                    <option value="{{ $objective->id }}">{{ $objective->objective }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:w-1/6">
                            <label class="block text-sm font-medium mb-1" for="instructor_id">Instructor <span class="text-red-500">*</span></label>
                            <select id="instructor_0" name="instructor_id" class="form-select w-full dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium">
                                <option>-Select Batch & Course First-</option>
                            </select>
                        </div>
                        <div class="sm:w-1/6">
                            <label class="block text-sm font-medium mb-1" for="topic_id">Topic <span class="text-red-500">*</span></label>
                            <select id="topic_0" name="topic_id" class="form-select w-full dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium">
                                <option value="">-Select Course First-</option>
                                <!-- Topics will be loaded here based on the selected course -->
                            </select>
                        </div>
                        <div class="sm:w-1/6">
                            <label class="block text-sm font-medium mb-1" for="lectureDays_id">No. of Days <span class="text-red-500">*</span></label>
                            <input id="lecture_days_0" type="number" min="1" name="lectureDays_id" value="0" class="form-input w-full dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium">
                        </div>
                        <div class="sm:w-1/6">
                            <label class="block text-sm font-medium mb-1" for="totalStudents">No. of Students <span class="text-red-500">*</span></label>
                            <input id="total_student_0" type="number" readonly name="totalStudents" value="0" class="form-input w-full dark:bg-gray-800 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100 font-medium">
                        </div>
                    </div>
                </section>
                <section id="schedule-container">

                </section>
                <div class="text-right p-5">
                    {{-- <a class="text-sm font-medium text-violet-500 hover:text-violet-600" href="javascript:void(0)" onclick="addNewRow()">Add New Row -&gt;</a> --}}
                    <button type="submit" class="btn-sm bg-purple-500 hover:bg-purple-600 text-white">Submit</a>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if (Auth::user()->hasPermission('read_training_schedule'))
    <!-- White box -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5 min-w-60">
        <div class="grid md:grid-cols-1 xl:grid-cols-1 gap-6">
            <!-- Group 1 -->
            <div>
                <h2 class="text-gray-800 dark:text-gray-100 font-semibold mb-2 border-b border-gray-200 dark:border-gray-700/60 pb-2">
                    Scheduled Classes
                    @if(Auth::user()->hasRole('student'))
                        For You
                    @endif
                </h2>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="table-auto w-full dark:text-gray-300">
                            <!-- Table header -->
                            <thead class="text-xs uppercase text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 dark:bg-opacity-50 rounded-sm">
                                <tr>
                                    <th class="p-2">
                                        <div class="font-semibold text-left">SN</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-left">Instructor</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-left">Course</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-left">Topic</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-left">Schedule Date &amp; Time</div>
                                    </th>
                                    <th class="p-2">
                                        <div class="font-semibold text-left">Assigned Students</div>
                                    </th>
                                    @if (Auth::user()->hasAnyPermission(['delete_training_schedule', 'update_training_schedule']))
                                    <th class="p-2">
                                        <div class="font-semibold text-center">Action</div>
                                    </th>
                                    @endif
                                </tr>
                            </thead>
                            <!-- Table body -->
                            <tbody class="text-sm font-medium divide-y divide-gray-100 dark:divide-gray-700/60">
                                <!-- Row -->
                                @if ($schedules->isEmpty())
                                    <tr class="text-center">
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap" colspan="7">
                                            <div class="text-center">No record found.</div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($schedules as $schedule)
                                        <tr>
                                            <td class="p-2">
                                                <div class="text-left">
                                                    {{ $loop->index + 1 }}
                                                </div>
                                            </td>
                                            <td class="p-2">
                                                <div class="flex items-center">
                                                    <div class="text-gray-800 dark:text-gray-100">{{ $schedule->instructor->firstName }} {{ $schedule->instructor->lastName }}</div>
                                                </div>
                                            </td>
                                            <td class="p-2">
                                                <div class="text-left">{{ $schedule->course->objective }}</div>
                                            </td>
                                            <td class="p-2">
                                                <div class="text-left">{{ $schedule->topic->topic }}</div>
                                            </td>
                                            <td class="p-2">
                                                <div class="text-left">
                                                    <div>
                                                        {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('M d, Y') }}
                                                    </div>
                                                    <div class="mt-2">
                                                        <span class="text-green-500">
                                                            {{ \Carbon\Carbon::parse($schedule->time_start)->format('h:i A') }}
                                                        </span> - 
                                                        <span class="text-red-500">
                                                            {{ \Carbon\Carbon::parse($schedule->time_stop)->format('h:i A') }}
                                                        </span>
                                                    </div>
                                                </div>                                      
                                            </td>
                                            <td class="p-2">
                                                <div class="text-left" title="{{ $schedule->studentNames }}">
                                                    {{ count($schedule->studentIds()) }}
                                                </div>
                                            </td>
                                            @if (Auth::user()->hasAnyPermission(['delete_training_schedule', 'update_training_schedule']))
                                            <td class="p-2">
                                                <div class="flex items-center space-x-4 pl-10 md:pl-0">
                                                    @can('update_training_schedule')
                                                        <a href="{{ route('schedule.edit', $schedule->id) }}" class="btn-ghost btn-xs"><x-site.icon name="pencil" class="h-4 w-4" /> Edit</a>
                                                    @endcan
                                                    @can('delete_training_schedule')
                                                    <button type="button" @click="$store.deleteModal.open({{ $schedule->id }})" aria-controls="delete-modal" class="text-red-500 hover:text-red-600 rounded-full">
                                                        <span class="sr-only">Delete</span>
                                                        <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                                            <path d="M13 15h2v6h-2zM17 15h2v6h-2z" />
                                                            <path d="M20 9c0-.6-.4-1-1-1h-6c-.6 0-1 .4-1 1v2H8v2h1v10c0 .6.4 1 1 1h12c.6 0 1-.4 1-1V13h1v-2h-4V9zm-6 1h4v1h-4v-1zm7 3v9H11v-9h10z" />
                                                        </svg>
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delete Schedule Modal -->
@include('components.modals.delete-schedule-modal')
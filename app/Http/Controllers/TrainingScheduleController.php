<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\TrainingObjective;
use App\Models\TrainingSchedule;
use App\Models\Curriculum;
use App\Models\User;
use App\Models\EnrolmentBatch;
use App\Models\RoleCourse;
use App\Models\StudentInstructorDistribution;

class TrainingScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve training objectives, instructors, students, and batches
        $objectives = TrainingObjective::orderBy('price', 'asc')->get();

        $instructors = User::where('user_active', 1)
            ->whereHas('roles', function ($query) {
                $query->where('roles.id', '!=', 10);
            })
            ->orderBy('firstName', 'asc')
            ->get();

        $students = User::where('user_active', 1)
            ->whereHas('roles', function ($query) {
                $query->where('roles.id', 10);
            })
            ->orderBy('firstName', 'asc')
            ->get();

        $batches = EnrolmentBatch::orderBy('id', 'asc')->get();

        // Retrieve schedules with relationships
        $schedules = TrainingSchedule::with(['instructor', 'course', 'topic'])
            ->orderBy('schedule_date', 'asc')
            ->paginate(10);

        // Attach student names to each schedule
        $schedules->transform(function ($schedule) {
            $studentIds = json_decode($schedule->students, true); // Decode JSON array of student IDs

            // Fetch student names
            $schedule->studentNames = User::whereIn('id', $studentIds)
                ->get(['firstName', 'lastName'])
                ->map(fn($s) => $s->firstName . ' ' . $s->lastName)
                ->implode("\n"); // Convert array to newline-separated string

            return $schedule;
        });

        return view('pages/schedule/index', compact('objectives', 'instructors', 'students', 'schedules', 'batches'));
    }

    public function getTopics($id)
    {
        // Fetch topics where objective_id matches the selected course ID
        $topics = Curriculum::where('objective_id', $id)->orderBy('id', 'asc')->get(['id', 'topic']);

        // Return the topics as JSON
        return response()->json($topics);
    }

    public function getInstructors($course_id)
    {
        // Get all role IDs linked to the course
        $roleIds = RoleCourse::where('course_id', $course_id)
            ->pluck('role_id');

        // Fetch users who have any of those roles
        $instructors = User::whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('roles.id', $roleIds);
            })
            ->orderBy('firstName', 'asc')
            ->get(['id', 'firstName', 'lastName']);

        // Return as JSON
        return response()->json($instructors);
    }

    public function getInstructorStudents($batch_id, $course_id, $instructor_id)
    {
        // Fetch all student_ids from StudentInstructorDistribution
        $studentIds = StudentInstructorDistribution::where('instructor_id', $instructor_id)
                        ->where('course_id', $course_id)
                        ->where('enrolment_batch_id', $batch_id)
                        ->pluck('student_id');

        // Fetch students only if student IDs exist
        $students = $studentIds->isNotEmpty()
            ? User::whereIn('id', $studentIds)->orderBy('id', 'asc')->get(['id', 'firstName', 'lastName'])
            : collect(); // Return an empty collection if no students found

        return response()->json($students);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:enrolment_batches,id',
            'course_id' => 'required|exists:training_objectives,id',
            'instructor_id' => 'required|exists:users,id',
            'topic_id' => 'required|exists:curriculum,id',
            // 'time_start.*' => 'required|date_format:h:i A',
            // 'time_stop.*' => 'required|date_format:h:i A|after:time_start.*',
        ]);

        $batchID = $request->input('batch_id');
        $courseID = $request->input('course_id');
        $instructorID = $request->input('instructor_id');
        $topicID = $request->input('topic_id');
        $scheduleDate = $request->input('schedule_date');
        $timeStart = $request->input('time_start');
        $timeStop = $request->input('time_stop');
        $studentNo = $request->input('distributed_students');


        // Validate each schedule for conflicts
        for ($i = 0; $i < count($scheduleDate); $i++) {
            $formattedDate = Carbon::createFromFormat('M d, Y', $scheduleDate[$i])->format('Y-m-d');
            $formattedTimeStart = Carbon::createFromFormat('h:i A', $timeStart[$i])->format('H:i');
            $formattedTimeStop = Carbon::createFromFormat('h:i A', $timeStop[$i])->format('H:i');

            // Check for existing schedule conflict
            $existingSchedule = TrainingSchedule::where('instructor_id', $instructorID)
            ->where('schedule_date', $formattedDate)
            ->where(function ($query) use ($formattedTimeStart, $formattedTimeStop) {
                $query->where(function ($q) use ($formattedTimeStart, $formattedTimeStop) {
                    $q->whereBetween('time_start', [$formattedTimeStart, $formattedTimeStop])
                    ->orWhereBetween('time_stop', [$formattedTimeStart, $formattedTimeStop]);
                })
                ->orWhere(function ($q) use ($formattedTimeStart, $formattedTimeStop) {
                    $q->where('time_start', '<', $formattedTimeStart) // Use < instead of <=
                    ->where('time_stop', '>', $formattedTimeStop); // Use > instead of >=
                });
            })
            ->where('time_stop', '!=', $formattedTimeStart) // Exclude exact end-to-start matches
            ->where('time_start', '!=', $formattedTimeStop) // Exclude exact start-to-end matches
            ->first();


            if ($existingSchedule) {
                $instructor = User::where('id', $instructorID)->first(['firstName', 'lastName']);
                $existingSchedule_formattedDate = Carbon::createFromFormat('Y-m-d', $existingSchedule->schedule_date)->format('M d, Y');
                $existingSchedule_formattedTimeStart = Carbon::createFromFormat('H:i:s', $existingSchedule->time_start)->format('h:i A');
                $existingSchedule_formattedTimeStop = Carbon::createFromFormat('H:i:s', $existingSchedule->time_stop)->format('h:i A');

                if ($instructor) {
                    return redirect()->back()->withErrors([
                        'error' => "{$instructor->firstName} {$instructor->lastName} already has a schedule for {$existingSchedule_formattedDate} from {$existingSchedule_formattedTimeStart} to {$existingSchedule_formattedTimeStop}."
                    ]);
                }
            }
        }

        $studentIds = StudentInstructorDistribution::where('instructor_id', $instructorID)
                ->where('enrolment_batch_id', $batchID)
                ->pluck('student_id')
                ->toArray(); // Convert collection to an array

        $students = $studentIds ? User::whereIn('id', $studentIds)->orderBy('id', 'asc')->pluck('id')->toArray() : [];

        $studentIndex = 0; // Track the index for student allocation

        // Save the schedules if no conflicts are found
        for ($i = 0; $i < count($scheduleDate); $i++) {
            $formattedDate = Carbon::createFromFormat('M d, Y', $scheduleDate[$i])->format('Y-m-d');
            $formattedTimeStart = Carbon::createFromFormat('h:i A', $timeStart[$i])->format('H:i:s');
            $formattedTimeStop = Carbon::createFromFormat('h:i A', $timeStop[$i])->format('H:i:s');

            // Extract the required number of students
            $numStudents = $studentNo[$i]; // Number of students needed in this loop
            $assignedStudents = array_slice($students, $studentIndex, $numStudents); // Slice students
            $studentIndex += $numStudents; // Move the pointer forward

            TrainingSchedule::create([
                'batch_id' => $batchID,
                'course_id' => $courseID,
                'instructor_id' => $instructorID,
                'topic_id' => $topicID,
                'schedule_date' => $formattedDate,
                'time_start' => $formattedTimeStart,
                'time_stop' => $formattedTimeStop,
                'students' => json_encode($assignedStudents), // Store as JSON array
            ]);
        }

        return redirect()->back()->with('success', 'Training schedule added successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $schedule = TrainingSchedule::findOrFail($id);

        if (StudentAttendance::where('schedule_id', $schedule->id)->exists()) {
            return redirect()->back()->with('error', 'This session already has attendance recorded and cannot be deleted.');
        }

        $schedule->delete();

        return redirect()->back()->with('success', 'Training session deleted.');
    }
}

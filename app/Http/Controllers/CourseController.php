<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\TrainingObjective;
use App\Models\TrainingSchedule;
use App\Models\Curriculum;
use App\Models\CourseMaterial;
use App\Models\StudentInstructorDistribution;

class CourseController extends Controller
{
    public function index()
    {
        $courses = TrainingObjective::orderByDesc('price')
            ->withCount(['curriculum', 'materials'])
            ->get();

        $enrolled = StudentInstructorDistribution::whereHas('student', fn ($q) => $q->whereNull('suspended_at'))
            ->selectRaw('course_id, COUNT(DISTINCT student_id) as total')
            ->groupBy('course_id')
            ->pluck('total', 'course_id');

        $upcoming = TrainingSchedule::whereBetween('schedule_date', [today(), today()->addDays(7)])
            ->selectRaw('course_id, COUNT(*) as total')
            ->groupBy('course_id')
            ->pluck('total', 'course_id');

        $thisWeek = TrainingSchedule::with(['instructor', 'course', 'topic'])
            ->whereBetween('schedule_date', [today(), today()->addDays(7)])
            ->orderBy('schedule_date')->orderBy('time_start')
            ->take(10)
            ->get();

        return view('pages/course/index', compact('courses', 'enrolled', 'upcoming', 'thisWeek'));
    }

    public function show($id)
    {
        $course = TrainingObjective::findOrFail($id);
        $curriculum = Curriculum::where('objective_id', $id)->orderBy('id')->get();
        $materials = CourseMaterial::with('uploadedBy')->where('objective_id', $id)->latest()->get();

        $students = StudentInstructorDistribution::with(['student', 'instructor', 'batch'])
            ->where('course_id', $id)
            ->whereHas('student', fn ($q) => $q->whereNull('suspended_at'))
            ->get()
            ->sortBy(fn ($row) => $row->student?->firstName)
            ->values();

        $sessions = TrainingSchedule::with(['instructor', 'topic'])
            ->where('course_id', $id)
            ->where('schedule_date', '>=', today())
            ->orderBy('schedule_date')->orderBy('time_start')
            ->take(20)
            ->get();

        $topicSessions = TrainingSchedule::where('course_id', $id)->whereNotNull('topic_id')
            ->selectRaw('topic_id, COUNT(*) as total')->groupBy('topic_id')->pluck('total', 'topic_id');

        return view('pages/course/course-details', compact('course', 'curriculum', 'materials', 'students', 'sessions', 'topicSessions'));
    }

    public function updateCourseDetails(Request $request)
    {
        // Validate the request data
        $request->validate([
            'id' => 'required|exists:training_objectives,id',
            'course_details' => 'required|string',
        ]);

        $objective = TrainingObjective::findOrFail($request->id);

        // Update other fields
        $objective->update([
            'course_details' => $request->input('course_details'),
        ]);

        return redirect()->back()->with('success', 'Course updated successfully!');
    }

    public function curriculumStore(Request $request)
    {
        $request->validate([
            'objective_id' => 'required|integer|exists:training_objectives,id',
            'topic' => 'required|string|max:255',
            'summary' => 'required|string',
        ]);
        
        Curriculum::create([
            'objective_id' => $request->input('objective_id'),
            'topic' => $request->input('topic'),
            'summary' => $request->input('summary'),
        ]);
                    

        return redirect()->back()->with('success', 'New curriculum added successfully.');
    }

    public function curriculumUpdate(Request $request, $id)
    {
        $curriculum = Curriculum::findOrFail($id);

        $data = $request->validate([
            'topic' => 'required|string|max:255',
            'summary' => 'required|string',
        ]);

        $curriculum->update($data);

        return redirect()->back()->with('success', 'Curriculum topic updated.');
    }

    public function curriculumDestroy($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        $curriculum->delete();

        return redirect()->back()->with('success', 'Curriculum deleted successfully.');
    }

    public function uploadCourseMaterials(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|exists:training_objectives,id',
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png|max:20480',
            'description' => 'nullable|string|max:500',
        ], [
            'files.required' => 'Please choose at least one file.',
            'files.*.mimes' => 'Only PDF, Word, PowerPoint, Excel and image files are allowed.',
            'files.*.max' => 'Each file must be 20 MB or smaller.',
        ]);

        foreach ($request->file('files') as $file) {
            // Stored name comes from the detected type, never the uploaded file name
            $filePath = $file->store('course_materials', 'public');

            CourseMaterial::create([
                'objective_id' => $request->input('id'),
                'file_name' => mb_substr($file->getClientOriginalName(), 0, 250),
                'file_url' => $filePath,
                'file_type' => strtolower((string) $file->extension()),
                'file_size' => $this->formatSize($file->getSize()),
                'description' => $request->input('description'),
                'uploaded_by' => $request->user()->id,
            ]);
        }

        $count = count($request->file('files'));

        return redirect()->back()->with('success', $count === 1 ? 'Material uploaded.' : "{$count} materials uploaded.");
    }

    public function downloadMaterial($id)
    {
        $material = CourseMaterial::findOrFail($id);
        $filePath = storage_path('app/public/' . $material->file_url);

        if (file_exists($filePath)) {
            return response()->download($filePath, $material->file_name);
        }

        return redirect()->back()->with('error', 'File not found.');
    }

    public function materialDestroy($id)
    {
        $material = CourseMaterial::findOrFail($id);
        $filePath = $material->file_url;
        $material->delete();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        return redirect()->back()->with('success', 'Material deleted successfully.');
    }

    private function formatSize(int $bytes): string
    {
        return $bytes >= 1048576 ? round($bytes / 1048576, 1) . ' MB' : max(1, round($bytes / 1024)) . ' KB';
    }
}

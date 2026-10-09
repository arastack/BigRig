<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\TrainingObjective;
use App\Models\TrainingSchedule;
use App\Models\Curriculum;
use App\Models\CourseMaterial;

class CourseController extends Controller
{
    public function index()
    {
        // Retrieve all training objectives in a specific order (e.g., by `id`)
        $objectives = TrainingObjective::orderBy('price', 'asc')->get();
        $schedules = TrainingSchedule::with(['instructor', 'course', 'topic'])
            ->whereBetween('schedule_date', [
                Carbon::now()->startOfWeek(),  // Start of the week (Sunday)
                Carbon::now()->endOfWeek()     // End of the week (Saturday)
            ])
            ->orderBy('schedule_date', 'asc')
            ->get();

        return view('pages/course/index', compact('objectives', 'schedules'));  
    }

    public function show($id)
    {
        $objective = TrainingObjective::findOrFail($id);
        $curriculum = Curriculum::where('objective_id', $id)->get();
        $materials = CourseMaterial::where('objective_id', $id)->get();

        return view('pages/course/course-details', compact('objective', 'curriculum', 'materials'));
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
            'files.*' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ], [
            'files.required' => 'Please upload at least one file.',
            'files.*.required' => 'Each file is required.',
            'files.*.mimes' => 'Only PDF, DOC, DOCX, PPT, PPTX file types are allowed.',
            'files.*.max' => 'The file size must not exceed 10 MB.',
        ]);        

        $objectiveId = $request->input('id');
        $uploadedFiles = $request->file('files'); // Retrieve the uploaded files

        foreach ($uploadedFiles as $file) {
            // Store the file and get the path
            $filePath = $file->store('course_materials', 'public'); // Save to storage/app/public/course_materials

            // Save the file information in the database
            CourseMaterial::create([
                'objective_id' => $objectiveId,
                'file_name' => $file->getClientOriginalName(),
                'file_url' => $filePath,
            ]);
        }

        return redirect()->back()->with('success', 'Course materials uploaded successfully!');
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

}

<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\CourseMaterial;
use App\Models\TrainingObjective;
use App\Models\StudentInstructorDistribution;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    /**
     * Display materials overview
     */
    public function index()
    {
        $instructor = Auth::user();

        // Get courses assigned to this instructor
        $assignedCourseIds = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->pluck('course_id')
            ->unique();

        $courses = TrainingObjective::whereIn('id', $assignedCourseIds)->get();

        // Get materials uploaded by this instructor
        $materials = CourseMaterial::with('objective')
            ->whereIn('objective_id', $assignedCourseIds)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get materials statistics
        $stats = [
            'total_materials' => CourseMaterial::count(),
            'total_size' => $this->formatFileSize(
                CourseMaterial::sum(\DB::raw("CAST(REPLACE(REPLACE(file_size, 'MB', ''), 'KB', '') AS DECIMAL(10,2))"))
            ),
            'courses_with_materials' => CourseMaterial::distinct('objective_id')
                ->count('objective_id'),
            'recent_uploads' => CourseMaterial::where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        return view('instructor.materials.index', compact('courses', 'materials', 'stats'));
    }

    /**
     * Upload new material
     */
    public function upload(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:training_objectives,id',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,jpg,jpeg,png,gif|max:20480', // 20MB max
            'description' => 'nullable|string|max:500'
        ]);

        $instructor = Auth::user();

        // Verify instructor has access to this course
        $hasAccess = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->where('course_id', $request->course_id)
            ->exists();

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to upload materials for this course.');
        }

        $file = $request->file('file');
        $course = TrainingObjective::find($request->course_id);

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        // Name the stored file from its detected content type, never the client-supplied extension
        $extension = strtolower((string) $file->extension());
        $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'gif'];
        if (! in_array($extension, $allowed, true)) {
            return redirect()->back()->with('error', 'This file type is not allowed.');
        }
        $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '.' . $extension;

        // Create directory structure: materials/course_id/
        $directory = 'materials/' . $request->course_id;

        // Store file
        $path = $file->storeAs($directory, $fileName, 'public');

        // Get file size
        $fileSize = $this->formatFileSize($file->getSize());

        // Save to database
        CourseMaterial::create([
            'objective_id' => $request->course_id,
            'file_name' => $originalName,
            'file_url' => $path,
            'file_size' => $fileSize,
            'file_type' => $extension,
            'description' => $request->description,
            'uploaded_by' => $instructor->id,
        ]);

        return redirect()->back()->with('success', 'Material uploaded successfully!');
    }

    /**
     * Delete material
     */
    public function destroy($id)
    {
        $instructor = Auth::user();
        $material = CourseMaterial::findOrFail($id);

        // Verify the instructor uploaded this material
        if ($material->uploaded_by != $instructor->id) {
            return redirect()->back()->with('error', 'You do not have permission to delete this material.');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($material->file_url)) {
            Storage::disk('public')->delete($material->file_url);
        }

        // Delete from database
        $material->delete();

        return redirect()->back()->with('success', 'Material deleted successfully!');
    }

    /**
     * View materials by course
     */
    public function byCourse($courseId)
    {
        $instructor = Auth::user();

        // Verify instructor has access to this course
        $hasAccess = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->where('course_id', $courseId)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Unauthorized access to this course.');
        }

        $course = TrainingObjective::findOrFail($courseId);

        // Get materials for this course
        $materials = CourseMaterial::where('objective_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Group materials by type
        $materialsByType = $materials->groupBy(function($item) {
            $extension = strtolower($item->file_type);

            if (in_array($extension, ['pdf'])) return 'PDFs';
            if (in_array($extension, ['doc', 'docx'])) return 'Documents';
            if (in_array($extension, ['xls', 'xlsx'])) return 'Spreadsheets';
            if (in_array($extension, ['ppt', 'pptx'])) return 'Presentations';
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) return 'Images';
            if (in_array($extension, ['zip', 'rar'])) return 'Archives';

            return 'Others';
        });

        return view('instructor.materials.course', compact('course', 'materials', 'materialsByType'));
    }

    /**
     * Download material (for instructor preview)
     */
    // public function download($id)
    // {
    //     $material = CourseMaterial::findOrFail($id);
    //     $filePath = storage_path('app/public/' . $material->file_url);

    //     if (file_exists($filePath)) {
    //         return response()->download($filePath, $material->file_name);
    //     }

    //     return redirect()->back()->with('error', 'File not found.');
    // }

    /**
     * Download a material file
     */
    public function download($id)
    {
        $instructor = Auth::user();

        // Get the material
        $material = CourseMaterial::findOrFail($id);

        // Verify instructor has access to this material's course
        $hasAccess = StudentInstructorDistribution::where('instructor_id', $instructor->id)
            ->where('course_id', $material->objective_id)
            ->exists();

        // Also check if instructor uploaded it
        if (!$hasAccess && $material->uploaded_by != $instructor->id) {
            abort(403, 'You do not have permission to download this material.');
        }

        // Build file path
        $filePath = storage_path('app/public/' . $material->file_url);

        // Check if file exists
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Return file download
        return response()->download($filePath, $material->file_name);
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}

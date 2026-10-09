<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\EnrolmentBatch;
use App\Models\TrainingObjective;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Every announcement in the school, from any author. Admins can address everyone, a course and/or a batch.
 */
class AnnouncementController extends Controller
{
    public const STATUSES = [
        'live' => 'Live',
        'scheduled' => 'Scheduled',
        'expired' => 'Expired',
        'inactive' => 'Turned off',
    ];

    public function index(Request $request)
    {
        $filters = $request->only(['q', 'status', 'course', 'batch']);

        $query = Announcement::query()->with(['course', 'batch', 'creator'])->withCount('reads');

        if ($search = trim((string) $request->input('q'))) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%"));
        }

        if (array_key_exists($status = (string) $request->input('status'), self::STATUSES)) {
            $this->whereStatus($query, $status);
        }

        match ($course = (string) $request->input('course')) {
            '' => null,
            'none' => $query->whereNull('course_id'),
            default => $query->where('course_id', (int) $course),
        };

        match ($batch = (string) $request->input('batch')) {
            '' => null,
            'none' => $query->whereNull('batch_id'),
            default => $query->where('batch_id', (int) $batch),
        };

        $counts = collect(self::STATUSES)->keys()->mapWithKeys(fn ($key) => [$key => $this->whereStatus(Announcement::query(), $key)->count()]);

        return view('admin.announcements.index', [
            'announcements' => $query->latest()->paginate(20)->withQueryString(),
            'filters' => $filters,
            'statuses' => self::STATUSES,
            'counts' => $counts,
            'courses' => TrainingObjective::orderBy('objective')->get(),
            'batches' => EnrolmentBatch::orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $announcement = Announcement::create($this->validated($request) + [
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', $announcement->is_active && (! $announcement->publish_date || $announcement->publish_date->isPast())
            ? 'Announcement published. Students in the audience can see it now.'
            : 'Announcement saved. It will appear ' . ($announcement->is_active ? 'from ' . $announcement->publish_date->format('j M Y, g:ia') : 'once you turn it on') . '.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement->update($this->validated($request));

        return back()->with('success', 'Announcement updated.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);

        return back()->with('success', $announcement->is_active ? 'Announcement turned on.' : 'Announcement turned off. Students no longer see it.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'urgent' => ['nullable', 'boolean'],
            'course_id' => ['nullable', 'exists:training_objectives,id'],
            'batch_id' => ['nullable', 'exists:enrolment_batches,id'],
            'publish_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', $request->filled('publish_date') ? 'after:publish_date' : 'after:now'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'expiry_date.after' => $request->filled('publish_date') ? 'The end date must be after the start date.' : 'The end date must be in the future.',
        ], [
            'publish_date' => 'show from',
            'expiry_date' => 'show until',
            'course_id' => 'course',
            'batch_id' => 'batch',
        ]);

        // The type follows the audience, unless it is flagged as urgent
        $type = match (true) {
            $request->boolean('urgent') => 'urgent',
            ! empty($data['course_id']) => 'course',
            ! empty($data['batch_id']) => 'batch',
            default => 'general',
        };

        return [
            'title' => $data['title'],
            'content' => $data['content'],
            'priority' => $data['priority'],
            'type' => $type,
            'course_id' => $data['course_id'] ?? null,
            'batch_id' => $data['batch_id'] ?? null,
            'publish_date' => $data['publish_date'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function whereStatus(Builder $query, string $status): Builder
    {
        $now = now();

        return match ($status) {
            'inactive' => $query->where('is_active', false),
            'scheduled' => $query->where('is_active', true)->where('publish_date', '>', $now),
            'expired' => $query->where('is_active', true)->where('expiry_date', '<', $now)
                ->where(fn ($q) => $q->whereNull('publish_date')->orWhere('publish_date', '<=', $now)),
            'live' => $query->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('publish_date')->orWhere('publish_date', '<=', $now))
                ->where(fn ($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', $now)),
        };
    }
}

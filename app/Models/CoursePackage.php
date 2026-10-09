<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * A combo discount: applies when the selection includes at least one course from every group.
 */
class CoursePackage extends Model
{
    protected $fillable = ['name', 'description', 'discount', 'weeks', 'course_groups', 'is_active'];

    protected $casts = [
        'course_groups' => 'array',
        'discount' => 'decimal:2',
        'weeks' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Course ids per group, as integers.
     *
     * @return array<int, array<int, int>>
     */
    public function groups(): array
    {
        return collect($this->course_groups ?? [])
            ->map(fn ($ids) => array_values(array_map('intval', (array) $ids)))
            ->values()->all();
    }

    /**
     * Whether the given course ids include at least one course from every group.
     */
    public function appliesTo(Collection|array $courseIds): bool
    {
        $ids = collect($courseIds)->map(fn ($id) => (int) $id);
        $groups = $this->groups();

        return count($groups) > 0
            && collect($groups)->every(fn ($group) => $ids->intersect($group)->isNotEmpty());
    }
}

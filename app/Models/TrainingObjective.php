<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Curriculum;
use App\Models\CourseMaterial;
use Illuminate\Support\Str;

class TrainingObjective extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'objective',
        'duration',
        'theory_session',
        'practical_session',
        'examination',
        'price',
        'course_details',
        'requirement',
        'image_url',
        'video_thumbnail_url',
        'video_url',
    ];

    protected $table = 'training_objectives';

    public function curriculum()
    {
        return $this->hasMany(Curriculum::class, 'objective_id');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class, 'objective_id');
    }

    public function courseMaterials()
    {
        return $this->hasMany(CourseMaterial::class, 'objective_id');
    }

    /**
     * URL slug used by the public course pages, e.g. "forklift-training-certification".
     */
    public function getSlugAttribute(): string
    {
        return Str::slug($this->objective);
    }

    public static function findBySlug(string $slug): ?self
    {
        $slug = Str::slug($slug);
        $courses = static::all();

        // Exact match, otherwise a renamed course whose new name extends the old one
        // (e.g. "cdl-commercial-vehicle-training" -> "...-standard-transmission")
        return $courses->first(fn ($course) => $course->slug === $slug)
            ?? $courses->sortBy('id')->first(fn ($course) => str_starts_with($course->slug, $slug . '-'));
    }

    /**
     * The requirement rich text as a plain list of bullet points.
     */
    public function requirementList(): array
    {
        return collect(preg_split('/<\/(li|p|div)>|<br\s*\/?>/i', (string) $this->requirement))
            ->map(fn ($part) => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($part)))))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * YouTube video id from any common YouTube URL format.
     */
    public function youtubeId(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/|live/))([\w-]{11})~', $this->video_url, $m);

        return $m[1] ?? null;
    }

    public function totalSessions(): int
    {
        return (int) $this->theory_session + (int) $this->practical_session;
    }
}

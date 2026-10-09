<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ApplicantDocument extends Model
{
    /** Private disk: documents are only downloadable through an authorised controller. */
    public const DISK = 'local';

    public const TYPES = [
        'drivers_licence' => "Driver's licence",
        'nin_slip' => 'NIN slip or card',
        'medical_report' => 'Medical report',
        'other' => 'Other document',
    ];

    protected $fillable = ['user_id', 'type', 'path', 'original_name', 'mime_type', 'size', 'status', 'reviewed_by', 'reviewed_at', 'review_note'];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Store an upload for a user, replacing any earlier document of the same type.
     */
    public static function storeFor(User $user, string $type, UploadedFile $file): self
    {
        $path = $file->storeAs(
            'applicant-documents/' . $user->id,
            $type . '-' . now()->format('YmdHis') . '.' . strtolower((string) $file->extension()),
            self::DISK
        );

        $user->documents()->where('type', $type)->get()->each->deleteWithFile();

        return $user->documents()->create([
            'type' => $type,
            'path' => $path,
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 250),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'status' => 'pending',
        ]);
    }

    public function deleteWithFile(): void
    {
        Storage::disk(self::DISK)->delete($this->path);
        $this->delete();
    }
}

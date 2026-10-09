<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Saved wording for one editable block of website text. Defaults live in config/page-text.php.
 */
class PageText extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Every editable field from the catalogue, keyed by its text key.
     *
     * @return array<string, array{label: string, type: string, default: string, help?: ?string}>
     */
    public static function catalogue(): array
    {
        return once(fn () => collect(config('page-text', []))
            ->flatMap(fn ($group) => $group['fields'] ?? [])
            ->all());
    }

    /**
     * Saved values keyed by text key, loaded once per request.
     *
     * @return array<string, string|null>
     */
    public static function values(): array
    {
        return once(function () {
            try {
                return static::query()->pluck('value', 'key')->all();
            } catch (\Throwable) {
                return []; // table not migrated yet — fall back to defaults
            }
        });
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageText;
use App\Models\TrainingObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Once;

/**
 * Edit the headings, introductions and other copy typed into the public website's
 * templates. Defaults come from config/page-text.php; saved wording overrides them.
 */
class PageTextController extends Controller
{
    public function index()
    {
        $saved = PageText::values();
        $courseSlug = TrainingObjective::query()->orderBy('id')->first()?->slug;

        $groups = collect(config('page-text', []))->map(function ($group, $id) use ($saved, $courseSlug) {
            $route = $group['route'] ?? null;
            $url = match (true) {
                $route === 'course' => $courseSlug ? route('course', $courseSlug) : null,
                $route !== null => route($route),
                default => null,
            };

            $fields = collect($group['fields'])->map(fn ($field, $key) => $field + [
                'key' => $key,
                'value' => filled($saved[$key] ?? null) ? $saved[$key] : $field['default'],
                'customised' => filled($saved[$key] ?? null),
            ]);

            return [
                'id' => $id,
                'label' => $group['label'],
                'help' => $group['help'] ?? null,
                'url' => $url,
                'fields' => $fields->values(),
                'customised' => $fields->where('customised', true)->count(),
            ];
        })->values();

        return view('admin.page-text.index', ['groups' => $groups]);
    }

    public function update(Request $request)
    {
        $catalogue = PageText::catalogue();
        $input = $request->input('text', []);

        $request->validate([
            'text' => ['required', 'array'],
            'text.*' => ['nullable', 'string', 'max:200000'],
        ]);

        $changed = 0;

        DB::transaction(function () use ($catalogue, $input, &$changed) {
            $existing = PageText::query()->whereIn('key', array_keys($catalogue))->get()->keyBy('key');

            foreach ($input as $key => $value) {
                if (! isset($catalogue[$key])) {
                    continue; // only keys from the catalogue can be saved
                }

                $field = $catalogue[$key];
                $value = $field['type'] === 'rich' ? clean_html((string) $value) : trim(str_replace("\r\n", "\n", (string) $value));
                $isDefault = blank(trim(strip_tags($value, '<img>'))) || $this->same($value, $field['default']);
                $row = $existing->get($key);

                if ($isDefault) {
                    if ($row) {
                        $row->delete();
                        $changed++;
                    }
                } elseif (! $row || ! $this->same($row->value, $value)) {
                    PageText::updateOrCreate(['key' => $key], ['value' => $value]);
                    $changed++;
                }
            }
        });

        Once::flush();

        $tab = $request->input('tab');

        return redirect()->route('admin.page-text', $tab ? ['tab' => $tab] : [])
            ->with('success', $changed ? 'Page text saved. The website now shows your wording.' : 'Nothing changed.');
    }

    /**
     * Compare two values ignoring whitespace differences the rich editor introduces.
     */
    private function same(?string $a, ?string $b): bool
    {
        $normalise = fn (?string $s) => trim(preg_replace(['/>\s+</', '/\s+/'], ['><', ' '], html_entity_decode((string) $s, ENT_QUOTES | ENT_HTML5)));

        return $normalise($a) === $normalise($b);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medical;
use App\Support\AdmissionSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Health declaration questions, next-of-kin relationships and the attendance threshold.
 */
class AdmissionSettingsController extends Controller
{
    public function index()
    {
        // How many applicants gave each stored answer, so admins see what an option is used by
        $usage = [];
        foreach (AdmissionSettings::WEB_FIELDS as $field) {
            $usage[$field] = Medical::query()->whereNotNull($field)->groupBy($field)->selectRaw("{$field} as answer, count(*) as total")->pluck('total', 'answer')->all();
        }

        return view('admin.admission-settings.index', [
            'questions' => AdmissionSettings::healthQuestions('all'),
            'relationships' => AdmissionSettings::relationships(),
            'threshold' => AdmissionSettings::attendanceThreshold(),
            'usage' => $usage,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*.field' => ['nullable', 'string', 'max:60'],
            'questions.*.form' => ['required', Rule::in(array_keys(AdmissionSettings::FORMS))],
            'questions.*.question' => ['nullable', 'string', 'max:255'],
            'questions.*.short' => ['required', 'string', 'max:60'],
            'questions.*.help' => ['nullable', 'string', 'max:255'],
            'questions.*.required' => ['nullable', 'boolean'],
            'questions.*.write_in' => ['nullable', 'string', 'max:40'],
            'questions.*.write_in_tick' => ['nullable', 'boolean'],
            'questions.*.paper_group' => ['nullable', 'string', 'max:60'],
            'questions.*.options' => ['required', 'array', 'min:1'],
            'questions.*.options.*.value' => ['nullable', 'string', 'max:255'],
            'questions.*.options.*.label' => ['required', 'string', 'max:100'],
            'questions.*.options.*.paper_label' => ['nullable', 'string', 'max:100'],
            'relationships' => ['required', 'array', 'min:1'],
            'relationships.*' => ['nullable', 'string', 'max:50'],
            'attendance_threshold' => ['required', 'integer', 'between:1,100'],
        ], [
            'questions.*.short.required' => 'Give each health question a short label.',
            'questions.*.options.required' => 'Each health question needs at least one answer.',
            'questions.*.options.*.label.required' => 'Answers cannot be blank — remove the row instead.',
            'relationships.required' => 'Keep at least one relationship.',
        ]);

        $current = collect(AdmissionSettings::all()['health_questions'])->keyBy('field');
        $questions = [];
        $usedFields = AdmissionSettings::WEB_FIELDS;
        $seen = [];

        foreach ($data['questions'] as $key => $input) {
            $field = $input['field'] ?? null;
            $stored = $field && AdmissionSettings::isStoredField($field);
            $existing = $field ? $current->get($field) : null;

            if ($stored) {
                if (in_array($field, $seen, true)) {
                    continue;
                }
                $seen[] = $field;
                $form = $input['form'];
                if ($form !== AdmissionSettings::FORM_PAPER && count($input['options']) < 2) {
                    throw ValidationException::withMessages(["questions.{$key}.options" => "\"{$input['short']}\" is asked online, so it needs at least two answers."]);
                }
            } else {
                // Questions the school adds are printed only: there is nowhere to save online answers
                if (! $existing || AdmissionSettings::isStoredField($field)) {
                    $field = $this->uniqueField($input['short'], $usedFields);
                    $existing = null;
                }
                $form = AdmissionSettings::FORM_PAPER;
            }
            $usedFields[] = $field;

            $questions[] = [
                'field' => $field,
                'form' => $form,
                'question' => trim($input['question'] ?? '') ?: trim($input['short']),
                'short' => trim($input['short']),
                'help' => trim($input['help'] ?? '') ?: null,
                'required' => (bool) ($input['required'] ?? false),
                'write_in' => trim($input['write_in'] ?? '') ?: null,
                'write_in_tick' => (bool) ($input['write_in_tick'] ?? false),
                'paper_group' => Str::squish($input['paper_group'] ?? '') ?: null,
                'options' => $this->options($input['options'], $existing['options'] ?? [], "questions.{$key}.options"),
            ];
        }

        $missing = array_diff(AdmissionSettings::WEB_FIELDS, $seen);
        if ($missing) {
            throw ValidationException::withMessages(['questions' => 'A health question is missing — reload the page and try again.']);
        }

        $relationships = collect($data['relationships'])->map(fn ($r) => Str::squish((string) $r))->filter()->unique(fn ($r) => mb_strtolower($r))->values()->all();
        if (! $relationships) {
            throw ValidationException::withMessages(['relationships' => 'Keep at least one relationship.']);
        }

        AdmissionSettings::save([
            'health_questions' => $questions,
            'relationships' => $relationships,
            'attendance_threshold' => (int) $data['attendance_threshold'],
        ]);

        return redirect()->route('admin.admission-settings')->with('success', 'Admissions settings saved.');
    }

    /**
     * Keep stored values of existing options (only their labels change); give new
     * options a value derived from the label. Values the browser invents are ignored.
     */
    private function options(array $rows, array $existing, string $errorKey): array
    {
        $labels = collect($rows)->map(fn ($row) => mb_strtolower(Str::squish($row['label'])));
        if ($labels->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages([$errorKey => 'Two answers have the same wording: "' . Str::squish(collect($rows)->firstWhere(fn ($r) => mb_strtolower(Str::squish($r['label'])) === $labels->duplicates()->first())['label']) . '".']);
        }

        $known = array_column($existing, 'value');
        $kept = collect($rows)->pluck('value')->filter(fn ($v) => in_array($v, $known, true))->all();
        $taken = $kept;
        $options = [];

        foreach ($rows as $row) {
            $label = Str::squish($row['label']);
            $value = $row['value'] ?? null;
            if (! in_array($value, $known, true) || in_array($value, array_column($options, 'value'), true)) {
                $value = AdmissionSettings::valueFor($label, $taken);
                $taken[] = $value;
            }
            $paper = Str::squish($row['paper_label'] ?? '');
            $options[] = ['value' => $value, 'label' => $label, 'paper_label' => $paper !== '' && $paper !== $label ? $paper : null];
        }

        return $options;
    }

    private function uniqueField(string $label, array $taken): string
    {
        $base = Str::slug($label, '_') ?: 'question';
        $field = $base;
        for ($n = 2; in_array($field, $taken, true); $n++) {
            $field = "{$base}_{$n}";
        }

        return $field;
    }
}

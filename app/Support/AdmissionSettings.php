<?php

namespace App\Support;

use App\Models\AdmissionSetting;
use Illuminate\Support\Str;

/**
 * Admissions settings the school edits in the admin panel: the health declaration
 * questions (web form + printable PDF form), next-of-kin relationships and the
 * attendance "at risk" threshold. Saved values override the defaults below.
 *
 * Each option has a stored VALUE (what lands in the medicals table, never changed
 * once saved) and a display LABEL (freely editable).
 */
class AdmissionSettings
{
    /** Health questions answered on the web form; each maps to a medicals column. */
    public const WEB_FIELDS = ['visual_impairment', 'hearing_aid', 'physical_disability', 'weed', 'alcohol'];

    /** Asked online and printed on the PDF form. */
    public const FORM_BOTH = 'both';

    /** Online application only. */
    public const FORM_WEB = 'web';

    /** Printed PDF form only. */
    public const FORM_PAPER = 'paper';

    public const FORMS = [self::FORM_BOTH => 'Online and paper', self::FORM_WEB => 'Online only', self::FORM_PAPER => 'Paper only'];

    private static ?array $cache = null;

    /**
     * Health questions in their saved order. 'web' = asked on the online form;
     * 'paper' = printed on the PDF form; 'all' = every question.
     *
     * @return list<array{field: string, form: string, question: string, short: string, help: ?string, required: bool, write_in: ?string, write_in_tick: bool, paper_group: ?string, options: list<array{value: string, label: string, paper_label: ?string}>}>
     */
    public static function healthQuestions(string $form = 'web'): array
    {
        $questions = self::all()['health_questions'];

        return array_values(match ($form) {
            'web' => array_filter($questions, fn ($q) => $q['form'] !== self::FORM_PAPER),
            'paper' => array_filter($questions, fn ($q) => $q['form'] !== self::FORM_WEB),
            default => $questions,
        });
    }

    /** Is this question saved to a medicals column (and so can't be deleted)? */
    public static function isStoredField(string $field): bool
    {
        return in_array($field, self::WEB_FIELDS, true);
    }

    /**
     * PDF cells: consecutive questions sharing a paper_group are printed together in one cell.
     *
     * @return list<array{title: string, required: bool, questions: list<array>, grouped: bool}>
     */
    public static function paperCells(): array
    {
        $cells = [];
        foreach (self::healthQuestions('paper') as $q) {
            $group = $q['paper_group'];
            $last = array_key_last($cells);
            if ($group && $last !== null && $cells[$last]['grouped'] && mb_strtolower($cells[$last]['title']) === mb_strtolower($group)) {
                $cells[$last]['questions'][] = $q;
                $cells[$last]['required'] = $cells[$last]['required'] || $q['required'];
                continue;
            }
            $cells[] = ['title' => $group ?: $q['short'], 'required' => $q['required'], 'questions' => [$q], 'grouped' => (bool) $group];
        }

        return $cells;
    }

    public static function question(string $field): ?array
    {
        return collect(self::all()['health_questions'])->firstWhere('field', $field);
    }

    /** Values currently accepted for a question. */
    public static function optionValues(string $field): array
    {
        return array_column(self::question($field)['options'] ?? [], 'value');
    }

    /** Display label for a stored value; falls back to the raw value for retired options. */
    public static function optionLabel(string $field, ?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        foreach (self::question($field)['options'] ?? [] as $option) {
            if ($option['value'] === $value) {
                return $option['label'];
            }
        }

        return $value;
    }

    public static function relationships(): array
    {
        return self::all()['relationships'];
    }

    /** Students whose attendance is below this percentage are flagged "at risk". */
    public static function attendanceThreshold(): int
    {
        return (int) self::all()['attendance_threshold'];
    }

    /**
     * Saved settings merged over the defaults, loaded once per request.
     */
    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $saved = [];
        try {
            $saved = AdmissionSetting::query()->pluck('value', 'key')->all();
        } catch (\Throwable) {
            // table not migrated yet — fall back to defaults
        }

        $settings = array_merge(self::defaults(), array_filter($saved, fn ($v) => $v !== null));

        // The questions saved to medicals columns always exist, whatever was saved
        $settings['health_questions'] = array_map([self::class, 'normalise'], array_values($settings['health_questions']));
        $fields = array_column($settings['health_questions'], 'field');
        foreach (self::defaults()['health_questions'] as $default) {
            if (self::isStoredField($default['field']) && ! in_array($default['field'], $fields, true)) {
                $settings['health_questions'][] = $default;
            }
        }

        return self::$cache = $settings;
    }

    public static function save(array $values): void
    {
        foreach ($values as $key => $value) {
            AdmissionSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        self::flush();
    }

    public static function flush(): void
    {
        self::$cache = null;
    }

    /**
     * A new option's stored value, derived from its label and unique within the question.
     */
    public static function valueFor(string $label, array $taken): string
    {
        $base = Str::limit(Str::squish($label), 90, '');
        $value = $base;
        $n = 2;
        $lower = array_map('mb_strtolower', $taken);
        while (in_array(mb_strtolower($value), $lower, true)) {
            $value = "{$base} ({$n})";
            $n++;
        }

        return $value;
    }

    /**
     * Fill keys added after a question was first saved, so older rows stay readable.
     */
    public static function normalise(array $q): array
    {
        $q += ['help' => null, 'required' => true, 'write_in' => null, 'write_in_tick' => false, 'paper_group' => null, 'options' => []];
        $q['short'] = ($q['short'] ?? '') ?: ($q['question'] ?? $q['field']);
        $q['options'] = array_map(fn ($o) => $o + ['paper_label' => null], $q['options']);

        return $q;
    }

    /**
     * Wording the school launched with (also seeded by the migrations). Order and paper
     * wording follow the academy's printed application form.
     */
    public static function defaults(): array
    {
        // [value => label] or [value => [label, paper label]]; a plain list item is value = label
        $opts = fn (array $pairs) => collect($pairs)->map(function ($label, $value) {
            [$label, $paper] = is_array($label) ? $label : [$label, null];

            return ['value' => (string) (is_int($value) ? $label : $value), 'label' => $label, 'paper_label' => $paper];
        })->values()->all();
        $q = fn (string $field, string $form, string $question, string $short, ?string $help, bool $required, array $options, array $extra = []) => [
            'field' => $field, 'form' => $form, 'question' => $question, 'short' => $short, 'help' => $help, 'required' => $required,
            'write_in' => $extra['write_in'] ?? null, 'write_in_tick' => $extra['write_in_tick'] ?? false, 'paper_group' => $extra['paper_group'] ?? null,
            'options' => $opts($options),
        ];
        $drugs = ['paper_group' => 'Drug & Alcohol'];

        return [
            'health_questions' => [
                $q('visual_impairment', self::FORM_BOTH, 'Do you have any vision condition?', 'Vision', 'Glasses or contact lenses are fine — tell us so instructors know.', true,
                    ['None', 'Long Sightedness' => ['Long-sightedness', 'Long Sightedness'], 'Short Sightedness' => ['Short-sightedness', 'Short Sightedness'], 'Color Blindness' => ['Colour blindness', 'Color Blindness']]),
                $q('hearing_aid', self::FORM_BOTH, 'Do you use a hearing aid?', 'Hearing', null, true,
                    ['None', 'BTE' => ['Behind the ear (BTE)', 'BTE'], 'ITE' => ['In the ear (ITE)', 'ITE'], 'RITE' => ['Receiver in the ear (RITE)', 'RITE'], 'ITC' => ['In the canal (ITC)', 'ITC'], 'CROS' => ['CROS / BiCROS', 'CROS/BiCros']]),
                $q('physical_mobility', self::FORM_PAPER, 'Do you have any physical or mobility condition?', 'Physical / Mobility', null, true,
                    ['None', 'Yes'], ['write_in' => 'Explain']),
                $q('other_conditions', self::FORM_PAPER, 'Do you have any other medical condition?', 'Other Conditions', null, false,
                    ['None', 'Diabetes', 'Heart/Cardiovascular', 'Epilepsy/Seizure', 'High Blood Pressure', 'Asthma'], ['write_in' => 'Other', 'write_in_tick' => true]),
                $q('prescription_medication', self::FORM_PAPER, 'Are you taking any prescription medication?', 'Prescription Medication', null, true,
                    ['Yes', 'No'], ['write_in' => 'If yes']),
                $q('drug_test_history', self::FORM_PAPER, 'Have you ever failed a drug test?', 'Failed drug test?', null, true,
                    ['Yes', 'No'], $drugs),
                $q('weed', self::FORM_BOTH, 'Do you use marijuana (weed), cocaine or other recreational drugs?', 'Marijuana/Cocaine', 'All students take a pre-employment drug screening.', true,
                    ['Yes', 'No'], $drugs),
                $q('alcohol', self::FORM_BOTH, 'How often do you drink alcohol?', 'Alcohol', null, true,
                    ['No' => ['Never', 'None'], 'Casually' => ['Casually', 'Casual'], 'Often', 'Daily User' => ['Daily', 'Daily']], $drugs),
                $q('physical_disability', self::FORM_WEB, 'Do you have any of these conditions?', 'Medical conditions', 'Choose the one that applies, or None.', true,
                    ['None', 'Ulcer', 'Cancer', 'HIV' => 'HIV/AIDS', 'Abdominal Pain' => 'Chronic abdominal pain']),
            ],
            'relationships' => ['Parent', 'Spouse', 'Sibling', 'Child', 'Relative', 'Guardian', 'Friend', 'Employer'],
            'attendance_threshold' => 75,
        ];
    }
}

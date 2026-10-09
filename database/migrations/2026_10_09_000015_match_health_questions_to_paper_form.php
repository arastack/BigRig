<?php

use App\Support\AdmissionSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Reshape the saved health questions to match the academy's paper form: each
     * question is online+paper, online only or paper only; answers may have separate
     * paper wording; drug & alcohol questions share one cell on the PDF; and the saved
     * order is the print order. Stored answer values and admin wording are kept.
     */
    public function up(): void
    {
        $row = DB::table('admission_settings')->where('key', 'health_questions')->first();
        if (! $row) {
            return; // nothing saved — the defaults already have this shape
        }

        $saved = collect(json_decode($row->value, true) ?: [])->keyBy('field');
        $questions = [];

        foreach (AdmissionSettings::defaults()['health_questions'] as $default) {
            $existing = $saved->pull($default['field']);
            if (! $existing) {
                if (AdmissionSettings::isStoredField($default['field'])) {
                    $questions[] = $default;
                }
                continue; // a paper question the school removed stays removed
            }

            $defaultOptions = collect($default['options'])->keyBy('value');
            $order = $defaultOptions->keys()->flip();
            $options = collect($existing['options'] ?? [])
                ->sortBy(fn ($o, $i) => [$order[$o['value']] ?? PHP_INT_MAX, $i])
                ->map(fn ($o) => [
                    'value' => (string) $o['value'],
                    'label' => $o['label'],
                    'paper_label' => $o['paper_label'] ?? $defaultOptions[$o['value']]['paper_label'] ?? null,
                ])->values()->all();

            $questions[] = [
                'field' => $default['field'],
                'form' => $default['form'],
                'question' => $existing['question'] ?? $default['question'],
                'short' => $default['paper_group'] ? $default['short'] : ($existing['short'] ?? $default['short']),
                'help' => $existing['help'] ?? null,
                'required' => (bool) ($existing['required'] ?? $default['required']),
                'write_in' => $existing['write_in'] ?? $default['write_in'],
                'write_in_tick' => $default['write_in_tick'],
                'paper_group' => $default['paper_group'],
                'options' => $options,
            ];
        }

        // Questions the school added themselves go at the end
        foreach ($saved as $extra) {
            $questions[] = AdmissionSettings::normalise($extra);
        }

        DB::table('admission_settings')->where('key', 'health_questions')->update(['value' => json_encode($questions), 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Older code ignores the extra keys; nothing to undo
    }
};

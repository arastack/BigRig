@section('title', 'Admissions settings')
@php
    // After a failed save, rebuild the form from what was submitted
    $source = old('questions', $questions);
    $state = [
        'questions' => collect($source)->map(fn ($q) => [
            'field' => (string) ($q['field'] ?? ''),
            'form' => (string) ($q['form'] ?? \App\Support\AdmissionSettings::FORM_PAPER),
            'question' => (string) ($q['question'] ?? ''),
            'short' => (string) ($q['short'] ?? ''),
            'help' => (string) ($q['help'] ?? ''),
            'required' => ! empty($q['required']),
            'write_in' => (string) ($q['write_in'] ?? ''),
            'write_in_tick' => ! empty($q['write_in_tick']),
            'paper_group' => (string) ($q['paper_group'] ?? ''),
            'stored' => \App\Support\AdmissionSettings::isStoredField((string) ($q['field'] ?? '')),
            'options' => collect($q['options'] ?? [])->map(fn ($o) => [
                'value' => (string) ($o['value'] ?? ''),
                'label' => (string) ($o['label'] ?? ''),
                'paper_label' => (string) ($o['paper_label'] ?? ''),
            ])->values()->all(),
        ])->values()->all(),
        'relationships' => array_values(old('relationships', $relationships)),
    ];
    $forms = \App\Support\AdmissionSettings::FORMS;
    $known = collect($questions)->mapWithKeys(fn ($q) => [$q['field'] => array_column($q['options'], 'value')]);
    $retired = collect($usage)->map(fn ($counts, $field) => collect($counts)->except($known[$field] ?? [])->map(fn ($n, $v) => "{$v} ({$n})")->values()->join(', '))->filter();
@endphp
<x-app-layout>
    <x-portal.page title="Admissions settings" subtitle="The health questions applicants answer, next-of-kin choices and when a student's attendance is flagged. Changes apply straight away.">
        <x-slot:actions>
            <a href="{{ route('register') }}" target="_blank" rel="noopener" class="btn-ghost btn-sm"><x-site.icon name="external" class="h-4 w-4" /> Application form</a>
            <a href="{{ route('register.form-pdf') }}" target="_blank" rel="noopener" class="btn-ghost btn-sm"><x-site.icon name="download" class="h-4 w-4" /> PDF form</a>
        </x-slot:actions>

        <form method="POST" action="{{ route('admin.admission-settings.update') }}" class="space-y-6"
              x-data="admissionSettings(@js($state))">
            @csrf
            @method('PUT')

            {{-- Health declaration --}}
            <section class="portal-card">
                <div class="portal-card-header">
                    <div class="min-w-0">
                        <h2 class="portal-card-title">Health declaration</h2>
                        <p class="mt-0.5 text-sm text-ink-500">Questions on the online application and the printed PDF form, in the order shown. Answers applicants already gave are kept, even if you reword or remove that answer here.</p>
                    </div>
                    <button type="button" class="btn-ghost btn-sm" @click="addPaper()"><x-site.icon name="plus" class="h-4 w-4" /> Add paper question</button>
                </div>

                <div class="divide-y divide-ink-100">
                    <template x-for="(q, qi) in questions" :key="q._k">
                        <div class="grid gap-4 px-5 py-5 lg:grid-cols-[16rem_1fr] lg:gap-6">
                            {{-- Summary and controls --}}
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <p class="font-display text-lg font-bold uppercase leading-tight tracking-wide text-ink-950" x-text="q.short || 'New question'"></p>
                                    <div class="flex shrink-0 gap-1">
                                        <button type="button" class="btn-ghost btn-xs" @click="move(qi, -1)" :disabled="qi === 0" :class="qi === 0 && 'opacity-50 cursor-not-allowed'" :aria-label="`Move ${q.short || 'question'} up`" title="Move up"><x-site.icon name="chevron-down" class="h-4 w-4 rotate-180" /></button>
                                        <button type="button" class="btn-ghost btn-xs" @click="move(qi, 1)" :disabled="qi === questions.length - 1" :class="qi === questions.length - 1 && 'opacity-50 cursor-not-allowed'" :aria-label="`Move ${q.short || 'question'} down`" title="Move down"><x-site.icon name="chevron-down" class="h-4 w-4" /></button>
                                    </div>
                                </div>
                                <span class="mt-1" :class="{ 'badge-dark': q.form === 'both', 'badge-yellow': q.form === 'web', 'badge-grey': q.form === 'paper' }" x-text="forms[q.form]"></span>
                                <input type="hidden" :name="`questions[${qi}][field]`" :value="q.field">

                                <div class="mt-3">
                                    <label class="portal-label" :for="`q-${q._k}-form`">Shown on</label>
                                    <template x-if="q.stored">
                                        <select :id="`q-${q._k}-form`" :name="`questions[${qi}][form]`" x-model="q.form" class="portal-input">
                                            @foreach ($forms as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </template>
                                    <template x-if="!q.stored">
                                        <div>
                                            <input type="hidden" :name="`questions[${qi}][form]`" value="paper">
                                            <p class="text-sm text-ink-700">Paper only</p>
                                        </div>
                                    </template>
                                </div>
                                <label class="mt-3 flex items-center gap-2 text-sm font-medium text-ink-700">
                                    <input type="checkbox" :name="`questions[${qi}][required]`" value="1" x-model="q.required" class="rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                    Required (*)
                                </label>
                                <p class="mt-3 text-xs text-ink-400" x-show="q.stored">Saved to the applicant's record, so it can be reworded or hidden but not deleted.</p>
                                <button type="button" x-show="!q.stored" class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-red-600 hover:text-red-700" @click="if (confirm('Remove this question from the PDF form?')) questions.splice(qi, 1)">
                                    <x-site.icon name="trash" class="h-4 w-4" /> Remove question
                                </button>
                            </div>

                            {{-- Wording --}}
                            <div class="space-y-4">
                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div class="sm:col-span-2">
                                        <label class="portal-label" :for="`q-${q._k}-question`">
                                            <span x-text="q.form === 'paper' ? 'Full question' : 'Question (online form)'"></span>
                                            <span class="font-normal normal-case tracking-normal text-ink-400" x-show="q.form === 'paper'">(for reference)</span>
                                        </label>
                                        <input :id="`q-${q._k}-question`" type="text" :name="`questions[${qi}][question]`" x-model="q.question" :required="q.form !== 'paper'" maxlength="255" class="portal-input">
                                    </div>
                                    <div>
                                        <label class="portal-label" :for="`q-${q._k}-short`">Label on paper form</label>
                                        <input :id="`q-${q._k}-short`" type="text" :name="`questions[${qi}][short]`" x-model="q.short" required maxlength="60" class="portal-input" placeholder="e.g. Vision">
                                    </div>
                                </div>
                                <div x-show="q.form !== 'paper'">
                                    <label class="portal-label" :for="`q-${q._k}-help`">Help text <span class="font-normal normal-case tracking-normal text-ink-400">(online, optional)</span></label>
                                    <input :id="`q-${q._k}-help`" type="text" :name="`questions[${qi}][help]`" x-model="q.help" maxlength="255" class="portal-input" placeholder="Shown under the question">
                                </div>

                                <div>
                                    <div class="flex gap-2">
                                        <p class="portal-label min-w-0 flex-1">Answer<span x-show="q.form !== 'paper'"> (online)</span></p>
                                        <p class="portal-label min-w-0 flex-1" x-show="q.form !== 'web'">On paper <span class="font-normal normal-case tracking-normal text-ink-400">(if different)</span></p>
                                        <span class="hidden w-32 shrink-0 sm:block" x-show="q.stored"></span>
                                        <span class="w-8 shrink-0"></span>
                                    </div>
                                    <ul class="space-y-2">
                                        <template x-for="(option, i) in q.options" :key="option._k">
                                            <li class="flex items-center gap-2">
                                                <input type="hidden" :name="`questions[${qi}][options][${i}][value]`" :value="option.value">
                                                <input type="text" :name="`questions[${qi}][options][${i}][label]`" x-model="option.label" required maxlength="100" class="portal-input min-w-0 flex-1" :aria-label="`Answer ${i + 1}`">
                                                <input type="text" :name="`questions[${qi}][options][${i}][paper_label]`" x-model="option.paper_label" maxlength="100" class="portal-input min-w-0 flex-1" :placeholder="option.label" :aria-label="`Paper wording for answer ${i + 1}`" x-show="q.form !== 'web'">
                                                <span class="hidden w-32 shrink-0 text-xs text-ink-400 sm:block" x-show="q.stored" x-text="usageText(q.field, option.value)"></span>
                                                <button type="button" class="btn-ghost btn-xs text-red-600" @click="removeOption(q, i)"
                                                        :disabled="q.options.length <= minOptions(q)" :class="q.options.length <= minOptions(q) && 'opacity-50 cursor-not-allowed'"
                                                        :aria-label="`Remove ${option.label || 'answer'}`"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                            </li>
                                        </template>
                                    </ul>
                                    <button type="button" class="btn-ghost btn-xs mt-2" @click="addOption(q.options)"><x-site.icon name="plus" class="h-4 w-4" /> Add answer</button>
                                    <p class="mt-2 text-xs text-ink-400" x-show="retired[q.field]" x-text="`No longer offered but kept on file: ${retired[q.field]}.`"></p>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-3" x-show="q.form !== 'web'">
                                    <div>
                                        <label class="portal-label" :for="`q-${q._k}-group`">Share a box with <span class="font-normal normal-case tracking-normal text-ink-400">(optional)</span></label>
                                        <input :id="`q-${q._k}-group`" type="text" :name="`questions[${qi}][paper_group]`" x-model="q.paper_group" maxlength="60" class="portal-input" placeholder="e.g. Drug & Alcohol">
                                    </div>
                                    <div>
                                        <label class="portal-label" :for="`q-${q._k}-write`">Write-in line <span class="font-normal normal-case tracking-normal text-ink-400">(optional)</span></label>
                                        <input :id="`q-${q._k}-write`" type="text" :name="`questions[${qi}][write_in]`" x-model="q.write_in" maxlength="40" class="portal-input" placeholder="e.g. If yes">
                                    </div>
                                    <label class="flex items-center gap-2 text-sm font-medium text-ink-700 self-end pb-2" x-show="q.write_in">
                                        <input type="checkbox" :name="`questions[${qi}][write_in_tick]`" value="1" x-model="q.write_in_tick" class="rounded border-ink-300 text-ink-950 focus:ring-brand-400">
                                        Tick box before the line
                                    </label>
                                </div>
                                <p class="text-xs text-ink-400" x-show="q.form !== 'web'">Questions next to each other with the same "share a box" name print together in one box, under that name.</p>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Relationships --}}
                <section class="portal-card self-start">
                    <div class="portal-card-header">
                        <div class="min-w-0">
                            <h2 class="portal-card-title">Next-of-kin relationships</h2>
                            <p class="mt-0.5 text-sm text-ink-500">The choices in the "Relationship" list on the application form.</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <ul class="space-y-2">
                            <template x-for="(rel, i) in relationships" :key="rel._k">
                                <li class="flex items-center gap-2">
                                    <input type="text" name="relationships[]" x-model="rel.label" required maxlength="50" class="portal-input" :aria-label="`Relationship ${i + 1}`">
                                    <button type="button" class="btn-ghost btn-xs text-red-600" @click="relationships.splice(i, 1)" :disabled="relationships.length <= 1"
                                            :class="relationships.length <= 1 && 'opacity-50 cursor-not-allowed'" :aria-label="`Remove ${rel.label || 'relationship'}`"><x-site.icon name="trash" class="h-4 w-4" /></button>
                                </li>
                            </template>
                        </ul>
                        <button type="button" class="btn-ghost btn-xs mt-2" @click="relationships.push({ label: '', _k: uid() })"><x-site.icon name="plus" class="h-4 w-4" /> Add relationship</button>
                    </div>
                </section>

                {{-- Attendance --}}
                <section class="portal-card self-start">
                    <div class="portal-card-header">
                        <h2 class="portal-card-title">Attendance</h2>
                    </div>
                    <div class="space-y-4 p-5">
                        <div class="sm:w-1/2">
                            <label class="portal-label" for="attendance_threshold">"At risk" below (%)</label>
                            <input id="attendance_threshold" type="number" name="attendance_threshold" min="1" max="100" step="1" required value="{{ old('attendance_threshold', $threshold) }}" class="portal-input">
                        </div>
                        <p class="text-sm text-ink-500">Students who attend less than this share of their recorded sessions are flagged in the attendance report, and see an alert on their own dashboard and course progress.</p>
                    </div>
                </section>
            </div>

            {{-- Save bar --}}
            <div class="sticky bottom-0 z-10 -mx-4 flex flex-col gap-3 border-t border-ink-100 bg-white/95 px-4 py-4 backdrop-blur sm:-mx-6 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:-mx-8 lg:px-8">
                <p class="text-sm text-ink-500">Saves every section at once. Applications already submitted keep their answers.</p>
                <button class="btn-brand btn-sm shrink-0"><x-site.icon name="check" class="h-4 w-4" /> Save settings</button>
            </div>
        </form>
    </x-portal.page>

    <script>
        function admissionSettings(state) {
            let n = 0;
            const uid = () => ++n;
            const rows = (options) => options.map(o => ({ paper_label: '', ...o, _k: uid() }));
            const usage = @js($usage);

            return {
                questions: state.questions.map(q => ({ ...q, options: rows(q.options), _k: uid() })),
                relationships: state.relationships.map(label => ({ label, _k: uid() })),
                forms: @js($forms),
                retired: @js($retired),
                uid,
                count(field, value) {
                    return value ? (usage[field]?.[value] ?? 0) : 0;
                },
                usageText(field, value) {
                    if (!value) return 'New';
                    const n = this.count(field, value);
                    return n ? `In use by ${n} applicant${n === 1 ? '' : 's'}` : 'Not used yet';
                },
                minOptions(q) {
                    return q.form === 'paper' ? 1 : 2;
                },
                move(i, step) {
                    const j = i + step;
                    if (j < 0 || j >= this.questions.length) return;
                    const [q] = this.questions.splice(i, 1);
                    this.questions.splice(j, 0, q);
                },
                addOption(options) {
                    options.push({ value: '', label: '', paper_label: '', _k: uid() });
                },
                removeOption(q, i) {
                    const n = q.stored ? this.count(q.field, q.options[i].value) : 0;
                    if (n && !confirm(`${n} applicant${n === 1 ? ' has' : 's have'} given this answer. They keep it on file, but new applicants won't see it. Remove?`)) return;
                    q.options.splice(i, 1);
                },
                addPaper() {
                    this.questions.push({ field: '', form: 'paper', question: '', short: '', help: '', required: false, write_in: '', write_in_tick: false, paper_group: '', stored: false, options: rows([{ value: '', label: 'Yes' }, { value: '', label: 'No' }]), _k: uid() });
                },
            };
        }
    </script>
</x-app-layout>

@php
    $currency = '<span class="naira">₦</span>';
    $contact = collect([$settings?->headquarters, $settings?->business_contact, $settings?->business_email])->filter()->join(' | ');
    $box = '<span class="box">&#9744;</span>';
    $ticks = fn (array $options) => collect($options)->map(fn ($o) => $box . ' ' . e($o))->join('&nbsp;&nbsp;');
    $field = fn ($label, $required = false) => '<div class="lbl">' . e($label) . ($required ? ' *' : '') . '</div><div class="line"></div>';
    $requirements = fn ($course) => collect($course->requirementList())->join('; ');
    $yesNo = [page_text('pdf.yes'), page_text('pdf.no')];
    $other = e(page_text('pdf.other'));
    // Package discounts are listed straight after the last course that belongs to a package (or at the end)
    $packages = \App\Support\Pricing::packages();
    $vatLabel = \App\Support\Pricing::vatLabel();
    $packageCourseIds = collect($packages)->pluck('groups')->flatten()->map(fn ($id) => (int) $id)->unique();
    $lastInPackage = $courses->keys()->filter(fn ($i) => $packageCourseIds->contains((int) $courses[$i]->id))->last();
    $comboAfter = $lastInPackage ?? $courses->count() - 1;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ page_text('pdf.doc_title') }} - {{ $settings->site_name ?? config('app.name') }}</title>
    <style>
        @page { margin: 26px 30px 30px; }
        * { font-family: 'DejaVu Serif', serif; }
        body { font-size: 8.6px; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }

        .masthead { background: #000; border-bottom: 3px solid #FFE500; text-align: center; padding: 7px 0 6px; }
        .masthead .name { color: #FFE500; font-size: 22px; font-weight: bold; letter-spacing: .5px; }
        .masthead .sub { color: #fff; font-size: 9px; font-weight: bold; letter-spacing: 1px; margin-top: 2px; }
        .contact { font-size: 7.4px; margin: 5px 0 4px; }
        .title { font-size: 16px; font-weight: bold; text-align: center; margin: 2px 0; }
        .note { font-size: 7.6px; font-weight: bold; margin-bottom: 3px; }

        .bar { background: #FFE500; font-size: 10px; font-weight: bold; padding: 3px 6px; margin-top: 7px; }
        .grid td { border: 1px solid #000; padding: 4px 5px 5px; }
        .lbl { font-weight: bold; font-size: 7.8px; }
        .line { border-bottom: 1px solid #000; height: 15px; margin-right: 6px; }
        .box { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; line-height: 1; }
        .naira { font-family: 'DejaVu Sans', sans-serif; }
        .opts { margin-top: 3px; line-height: 1.6; }
        .passport { text-align: center; vertical-align: middle !important; font-weight: bold; font-size: 8px; width: 92px; }
        .passport div { border: 1px dashed #000; height: 70px; padding-top: 38px; }

        .courses th { background: #000; color: #FFE500; font-size: 7.8px; text-align: left; padding: 4px 5px; }
        .courses td { border: 1px solid #000; padding: 4px 5px; }
        .courses .req { font-size: 7.2px; margin-top: 1px; }
        .price { color: #c00; font-weight: bold; font-size: 10px; white-space: nowrap; }

        .pay td { border: 1px solid #000; padding: 4px 6px; }
        .pay td.k { font-weight: bold; width: 45%; }
        .declaration { font-size: 8px; line-height: 1.45; margin: 4px 0 6px; }
        .footer { text-align: center; color: #b39500; font-weight: bold; font-size: 7.6px; margin-top: 10px; letter-spacing: .4px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

@foreach ([1, 2, 3] as $page)
    <div class="masthead">
        <div class="name">{{ page_text('pdf.masthead.name') }}</div>
        <div class="sub">{{ page_text('pdf.masthead.subtitle') }}</div>
    </div>
    <div class="contact">{{ $contact }}</div>
    <div class="title">{{ page_text('pdf.title') }}</div>

    @if ($page === 1)
        <div class="note">{{ page_text('pdf.note') }}</div>

        <div class="bar">{{ page_text('pdf.s1.title') }}</div>
        <table class="grid">
            <tr>
                <td style="width:25%">{!! $field(page_text('pdf.s1.first_name'), true) !!}</td>
                <td style="width:25%">{!! $field(page_text('pdf.s1.middle_name')) !!}</td>
                <td style="width:25%">{!! $field(page_text('pdf.s1.last_name'), true) !!}</td>
                <td rowspan="3" class="passport"><div>{!! nl2br(e(page_text('pdf.s1.photo')), false) !!}</div></td>
            </tr>
            <tr>
                <td>{!! $field(page_text('pdf.s1.dob'), true) !!}</td>
                <td><div class="lbl">{{ page_text('pdf.s1.gender') }} *</div><div class="opts">{!! $ticks([page_text('pdf.s1.male'), page_text('pdf.s1.female')]) !!}</div></td>
                <td><div class="lbl">{{ page_text('pdf.s1.nationality') }} *</div><div class="opts">{!! $ticks([page_text('pdf.s1.nigerian'), page_text('pdf.s1.ecowas')]) !!}<br>{!! $box !!} {!! $other !!} ________</div></td>
            </tr>
            <tr>
                <td>{!! $field(page_text('pdf.s1.nin'), true) !!}</td>
                <td>{!! $field(page_text('pdf.s1.mobile'), true) !!}</td>
                <td>{!! $field(page_text('pdf.s1.email'), true) !!}</td>
            </tr>
        </table>
        <table class="grid">
            <tr><td colspan="3">{!! $field(page_text('pdf.s1.address'), true) !!}</td></tr>
            <tr>
                <td style="width:34%">{!! $field(page_text('pdf.s1.city'), true) !!}</td>
                <td style="width:33%">{!! $field(page_text('pdf.s1.state'), true) !!}</td>
                <td style="width:33%">{!! $field(page_text('pdf.s1.lga'), true) !!}</td>
            </tr>
            <tr>
                <td>{!! $field(page_text('pdf.s1.kin_name'), true) !!}</td>
                <td>{!! $field(page_text('pdf.s1.kin_relationship'), true) !!}</td>
                <td>{!! $field(page_text('pdf.s1.kin_phone'), true) !!}</td>
            </tr>
        </table>

        <div class="bar">{{ page_text('pdf.s2.title') }}</div>
        <table class="grid">
            <tr>
                <td style="width:34%">{!! $field(page_text('pdf.s2.number'), true) !!}</td>
                <td style="width:33%">{!! $field(page_text('pdf.s2.class'), true) !!}</td>
                <td style="width:33%">{!! $field(page_text('pdf.s2.state'), true) !!}</td>
            </tr>
            <tr>
                <td>{!! $field(page_text('pdf.s2.issued'), true) !!}</td>
                <td>{!! $field(page_text('pdf.s2.expiry'), true) !!}</td>
                <td>{!! $field(page_text('pdf.s2.experience'), true) !!}</td>
            </tr>
            <tr>
                <td><span class="lbl">{{ page_text('pdf.s2.valid') }}</span> {!! $ticks($yesNo) !!}</td>
                <td><span class="lbl">{{ page_text('pdf.s2.copy') }}</span> {!! $ticks($yesNo) !!}</td>
                <td><span class="lbl">{{ page_text('pdf.s2.manual') }}</span> {!! $ticks($yesNo) !!}</td>
            </tr>
        </table>

        <div class="bar">{{ page_text('pdf.s3.title') }}</div>
        <table class="grid">
            @foreach ([page_text('pdf.s3.q1'), page_text('pdf.s3.q2'), page_text('pdf.s3.q3'), page_text('pdf.s3.q4')] as $question)
                <tr><td><span class="lbl">{{ $question }}</span> {!! $ticks($yesNo) !!}</td></tr>
            @endforeach
            <tr><td><div class="lbl">{{ page_text('pdf.s3.explain') }}</div><div class="line"></div><div class="line"></div></td></tr>
        </table>

        <div class="footer">{{ page_text('pdf.footer.page', [':pages' => 3, ':page' => 1]) }} | {{ page_text('pdf.footer.1') }}</div>
    @elseif ($page === 2)
        <div class="bar">{{ page_text('pdf.s4.title') }}</div>
        <table class="grid">
            <tr>
                <td style="width:50%">{!! $field('Weight (kg)', true) !!}</td>
                <td style="width:50%">{!! $field('Height (ft)', true) !!}</td>
            </tr>
        </table>
        {{-- Health questions come from Admin → Admissions settings, in their saved order; grouped questions share a cell --}}
        @php
            $healthCells = array_chunk(\App\Support\AdmissionSettings::paperCells(), 3);
            $healthAnswers = function (array $q) use ($box) {
                $html = collect($q['options'])->map(fn ($o) => '<span style="display:inline-block">' . $box . ' ' . e(($o['paper_label'] ?? null) ?: $o['label']) . '</span>')->join(' ');
                if ($q['write_in']) {
                    $html .= ($q['write_in_tick'] ? ' <span style="display:inline-block">' . $box . ' ' . e($q['write_in']) . ': ______________</span>' : '<br>' . e($q['write_in']) . ': ______________');
                }

                return $html;
            };
        @endphp
        <table class="grid" style="table-layout:fixed; width:100%;">
            @foreach ($healthCells as $row => $cells)
                <tr>
                    @foreach ($cells as $i => $cell)
                        <td style="width:33.33%; word-wrap:break-word;">
                            <div class="lbl">{{ chr(65 + $row * 3 + $i) }}. {{ $cell['title'] }}{{ $cell['required'] ? ' *' : '' }}</div>
                            <div class="opts">
                                @if ($cell['grouped'])
                                    @foreach ($cell['questions'] as $q)
                                        <div><span style="display:inline-block">{{ $q['short'] }}{{ preg_match('/[?:]$/', $q['short']) ? '' : ':' }}</span> {!! $healthAnswers($q) !!}</div>
                                    @endforeach
                                @else
                                    {!! $healthAnswers($cell['questions'][0]) !!}
                                @endif
                            </div>
                        </td>
                    @endforeach
                    @for ($pad = count($cells); $pad < 3; $pad++)<td style="width:33.33%;"></td>@endfor
                </tr>
            @endforeach
        </table>

        <div class="bar">{{ page_text('pdf.s5.title') }}</div>
        <table class="grid">
            <tr><td>
                <span class="lbl">{{ page_text('pdf.s5.question') }}</span> {!! $ticks($yesNo) !!}
                <div class="lbl" style="margin-top:6px;">{{ page_text('pdf.s5.details') }}</div><div class="line"></div>
            </td></tr>
        </table>

        <div class="bar">{{ page_text('pdf.s6.title') }}</div>
        <div class="note" style="margin-top:3px;">{{ page_text('pdf.s6.note') }}</div>
        <table class="courses">
            <thead>
                <tr>
                    <th style="width:16px;"></th>
                    <th>{{ page_text('pdf.s6.program') }}</th>
                    <th style="width:62px;">{{ page_text('pdf.s6.duration') }}</th>
                    <th style="width:78px;">{{ page_text('pdf.s6.price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $course)
                    <tr>
                        <td><span class="box">&#9744;</span></td>
                        <td>
                            <strong>{{ $course->objective }}</strong>
                            @if ($requirements($course))<div class="req">{{ $requirements($course) }}</div>@endif
                        </td>
                        <td><strong>{{ page_text('pdf.s6.weeks', [':weeks' => $course->duration]) }}</strong></td>
                        <td class="price">{!! $currency !!}{{ number_format($course->price) }}</td>
                    </tr>
                    @if ($loop->index === $comboAfter)
                        @foreach ($packages as $combo)
                            <tr>
                                <td><span class="box">&#9744;</span></td>
                                <td><strong>{{ $combo['name'] }}</strong>@if ($combo['description'])<div class="req">{!! str_replace('₦', $currency, e($combo['description'])) !!}</div>@endif</td>
                                <td><strong>{{ $combo['weeks'] ? page_text('pdf.s6.weeks', [':weeks' => $combo['weeks']]) : '—' }}</strong></td>
                                <td class="price">{!! $currency !!}{{ number_format($combo['discount']) }}<br>{{ page_text('pdf.s6.discount') }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="note" style="margin-top:4px; font-weight:normal;">{{ page_text('pdf.s6.vat_note', [':vat' => $vatLabel]) }}</div>

        <div class="footer">{{ page_text('pdf.footer.page', [':pages' => 3, ':page' => 2]) }} | {{ page_text('pdf.footer.2') }}</div>
    @else
        <div class="bar">{{ page_text('pdf.s7.title') }}</div>
        <table class="grid">
            <tr>
                <td style="width:30%">{!! $field(page_text('pdf.s7.start'), true) !!}</td>
                <td style="width:36%"><div class="lbl">{{ page_text('pdf.s7.location') }} *</div><div class="opts">{!! $ticks([page_text('pdf.s7.campus')]) !!}&nbsp;&nbsp;{!! $box !!} {!! $other !!} ________</div></td>
                <td style="width:34%"><div class="lbl">{{ page_text('pdf.s7.class') }} *</div><div class="opts">{!! $ticks([page_text('pdf.s7.full_time'), page_text('pdf.s7.part_time')]) !!}<br>{!! $ticks([page_text('pdf.s7.morning'), page_text('pdf.s7.afternoon'), page_text('pdf.s7.weekend')]) !!}</div></td>
            </tr>
        </table>

        <div class="bar">{{ page_text('pdf.s8.title') }}</div>
        <table class="grid">
            <tr>
                <td style="width:50%" class="opts">{!! $ticks([page_text('pdf.s8.1')]) !!}<br>{!! $ticks([page_text('pdf.s8.2')]) !!}<br>{!! $ticks([page_text('pdf.s8.3')]) !!}<br>{!! $ticks([page_text('pdf.s8.4')]) !!}</td>
                <td style="width:50%" class="opts">{!! $ticks([page_text('pdf.s8.5')]) !!}<br>{!! $ticks([page_text('pdf.s8.6')]) !!}<br>{!! $ticks([page_text('pdf.s8.7')]) !!}<br>{!! $ticks([page_text('pdf.s8.8')]) !!}</td>
            </tr>
        </table>

        <div class="bar">{{ page_text('pdf.s9.title') }}</div>
        <table class="pay">
            @foreach ([page_text('pdf.s9.registration'), page_text('pdf.s9.training'), page_text('pdf.s9.discount'), page_text('pdf.s9.vat', [':vat' => $vatLabel]), page_text('pdf.s9.total'), page_text('pdf.s9.paid'), page_text('pdf.s9.balance')] as $row)
                <tr><td class="k">{{ $row }}</td><td>{!! $currency !!} ______________________________</td></tr>
            @endforeach
            <tr><td class="k">{{ page_text('pdf.s9.method') }}</td><td>{!! $ticks([page_text('pdf.s9.cash'), page_text('pdf.s9.transfer'), page_text('pdf.s9.pos'), page_text('pdf.s9.method_other')]) !!}</td></tr>
            <tr><td class="k">{{ page_text('pdf.s9.plan') }}</td><td>{!! $ticks([page_text('pdf.s9.full'), page_text('pdf.s9.installment')]) !!}</td></tr>
            <tr><td class="k">{{ page_text('pdf.s9.receipt') }}</td><td>________________________________</td></tr>
            <tr><td class="k">{{ page_text('pdf.s9.next_due') }}</td><td>________________________________</td></tr>
        </table>

        <div class="bar">{{ page_text('pdf.s10.title') }}</div>
        <div class="declaration">{!! nl2br(e(page_text('pdf.s10.declaration')), false) !!}</div>
        <table class="grid">
            <tr>
                <td style="width:38%">{!! $field(page_text('pdf.s10.name'), true) !!}</td>
                <td style="width:38%">{!! $field(page_text('pdf.s10.signature'), true) !!}</td>
                <td style="width:24%">{!! $field(page_text('pdf.s10.date'), true) !!}</td>
            </tr>
        </table>

        <div class="bar">{{ page_text('pdf.s11.title') }}</div>
        <table class="grid">
            <tr>
                <td style="width:34%"><div class="lbl">{{ page_text('pdf.s11.status') }}</div><div class="opts">{!! $ticks([page_text('pdf.s11.approved'), page_text('pdf.s11.conditional'), page_text('pdf.s11.rejected')]) !!}</div></td>
                <td style="width:33%">{!! $field(page_text('pdf.s11.student_id')) !!}</td>
                <td style="width:33%">{!! $field(page_text('pdf.s11.batch')) !!}</td>
            </tr>
            <tr>
                <td>{!! $field(page_text('pdf.s11.instructor')) !!}</td>
                <td>{!! $field(page_text('pdf.s11.start')) !!}</td>
                <td>{!! $field(page_text('pdf.s11.completion')) !!}</td>
            </tr>
            <tr>
                <td>{!! $field(page_text('pdf.s11.received_by')) !!}</td>
                <td>{!! $field(page_text('pdf.s11.received_on')) !!}</td>
                <td>{!! $field(page_text('pdf.s11.reference')) !!}</td>
            </tr>
            <tr>
                <td>{!! $field(page_text('pdf.s11.officer')) !!}</td>
                <td>{!! $field(page_text('pdf.s11.signature')) !!}</td>
                <td>{!! $field(page_text('pdf.s11.date')) !!}</td>
            </tr>
        </table>

        <div class="footer">{{ page_text('pdf.footer.page', [':pages' => 3, ':page' => 3]) }} | {{ page_text('pdf.footer.3') }}</div>
    @endif

    @unless ($loop->last)<div class="page-break"></div>@endunless
@endforeach

</body>
</html>

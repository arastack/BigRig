<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Application Form - {{ $settings->site_name ?? config('app.name') }}</title>
    <style>
        @page { margin: 28px 34px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10.5px; color: #1f2937; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .header td { vertical-align: middle; }
        .school-name { font-size: 16px; font-weight: bold; color: #111827; }
        .muted { color: #6b7280; font-size: 9.5px; }
        .title { font-size: 14px; font-weight: bold; text-align: center; letter-spacing: 1px; text-transform: uppercase; margin: 12px 0 4px; }
        .passport { width: 110px; height: 135px; border: 1.5px dashed #6b7280; text-align: center; vertical-align: middle; color: #6b7280; font-size: 9px; }
        .section { background: #111827; color: #fff; font-weight: bold; font-size: 11px; padding: 5px 8px; margin: 14px 0 8px; text-transform: uppercase; letter-spacing: .5px; }
        .label { font-weight: bold; padding: 6px 0 2px; }
        .req { color: #dc2626; }
        .line { border-bottom: 1px solid #9ca3af; height: 20px; }
        .box { font-size: 13px; margin-right: 3px; }
        .option { display: inline-block; margin-right: 16px; white-space: nowrap; line-height: 20px; }
        .pad-r { padding-right: 14px; }
        .courses th { background: #f3f4f6; text-align: left; padding: 6px; border: 1px solid #d1d5db; font-size: 10px; }
        .courses td { padding: 6px; border: 1px solid #d1d5db; }
        .courses .req-text { color: #4b5563; font-size: 9px; margin-top: 2px; }
        .right { text-align: right; }
        .totals td { padding: 5px 6px; border: 1px solid #d1d5db; }
        .note { font-size: 9.5px; color: #4b5563; }
        .keep { page-break-inside: avoid; }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header">
        <tr>
            <td style="width: 90px;">
                @if ($logo)
                    <img src="{{ $logo }}" style="width: 80px;" alt="Logo">
                @endif
            </td>
            <td>
                <div class="school-name">{{ $settings->site_name ?? config('app.name') }}</div>
                @if ($settings?->headquarters)<div class="muted">{{ $settings->headquarters }}</div>@endif
                <div class="muted">
                    {{ $settings->business_contact ?? '' }}@if ($settings?->business_contact && $settings?->business_email) &nbsp;|&nbsp; @endif{{ $settings->business_email ?? '' }}
                </div>
            </td>
            <td style="width: 115px;" class="right">
                <table><tr><td class="passport">Affix recent<br>passport<br>photograph<br>here</td></tr></table>
            </td>
        </tr>
    </table>

    <div class="title">Student Application Form</div>
    <div class="note" style="text-align:center;">Please complete in BLOCK LETTERS. Fields marked <span class="req">*</span> are required.</div>

    @php
        $options = config('admissions.options');
        $labels = config('admissions.labels');
        $line = fn ($label, $required = false) => '<div class="label">' . e($label) . ($required ? ' <span class="req">*</span>' : '') . '</div><div class="line"></div>';
        $ticks = fn ($values, $field = null) => collect($values)->map(fn ($v) => '<span class="option"><span class="box">&#9744;</span>' . e($field ? ($labels[$field][$v] ?? $v) : $v) . '</span>')->implode(' ');
    @endphp

    {{-- Mirrors the online application: About you --}}
    <div class="section">1. Personal Details</div>
    <table>
        <tr>
            <td class="pad-r" style="width:33%">{!! $line('First Name', true) !!}</td>
            <td class="pad-r" style="width:33%">{!! $line('Middle Name') !!}</td>
            <td style="width:34%">{!! $line('Last Name', true) !!}</td>
        </tr>
        <tr>
            <td class="pad-r"><div class="label">Gender <span class="req">*</span></div>{!! $ticks(['Male', 'Female']) !!}</td>
            <td class="pad-r">{!! $line('Date of Birth (DD/MM/YYYY)', true) !!}</td>
            <td>{!! $line('NIN (11 digits)', true) !!}</td>
        </tr>
    </table>

    <div class="section">2. Contact Details</div>
    <table>
        <tr>
            <td class="pad-r" style="width:50%">{!! $line('Phone Number', true) !!}</td>
            <td style="width:50%">{!! $line('Email Address', true) !!}</td>
        </tr>
        <tr><td colspan="2">{!! $line('Home Address', true) !!}</td></tr>
        <tr>
            <td class="pad-r">{!! $line('Town / City', true) !!}</td>
            <td>{!! $line('State', true) !!}</td>
        </tr>
    </table>

    <div class="section">3. Driving Licence <span style="font-weight:normal; text-transform:none;">(if you have one — required for CDL and BRT/School Bus)</span></div>
    <table>
        <tr>
            <td class="pad-r" style="width:50%">{!! $line('Licence Number') !!}</td>
            <td style="width:50%">{!! $line('Expiry Date') !!}</td>
        </tr>
    </table>

    <div class="section">4. Next of Kin</div>
    <table>
        <tr>
            <td class="pad-r" style="width:40%">{!! $line('Full Name', true) !!}</td>
            <td class="pad-r" style="width:30%">{!! $line('Phone Number', true) !!}</td>
            <td style="width:30%">{!! $line('Relationship', true) !!}</td>
        </tr>
    </table>

    {{-- Mirrors the online application: Health --}}
    <div>
        <div class="section">5. Health Declaration</div>
        <div class="note">Operating heavy vehicles safely depends on your health. This information is confidential.</div>
        <table>
            <tr>
                <td class="pad-r" style="width:50%">{!! $line('Weight (kg)') !!}</td>
                <td style="width:50%">{!! $line('Height (ft)') !!}</td>
            </tr>
        </table>
        @foreach ([
            'visual_impairment' => 'Do you have any vision condition?',
            'hearing_aid' => 'Do you use a hearing aid?',
            'physical_disability' => 'Do you have any of these conditions?',
            'weed' => 'Do you use marijuana (weed), cocaine or other recreational drugs?',
            'alcohol' => 'How often do you drink alcohol?',
        ] as $field => $question)
            <div class="label">{{ $question }} <span class="req">*</span></div>
            {!! $ticks($options[$field], $field) !!}
        @endforeach

        <div class="label">Are you taking any prescribed medication?</div>
        <div class="line"></div>
        <div class="label">Have you ever failed a drug test? If yes, when and why?</div>
        <div class="line"></div>

        <p class="note" style="margin-top:8px;"><span class="box">&#9744;</span> I confirm this health information is true and complete, and I consent to BigRig processing it to assess my fitness to train safely. <span class="req">*</span></p>
    </div>

    {{-- Step 3: Training Objectives --}}
    <div class="keep">
        <div class="section">6. Course(s)</div>
        <div class="note" style="margin-bottom:6px;">Tick at least one training objective. <span class="req">*</span></div>
        <table class="courses">
            <thead>
                <tr>
                    <th style="width:22px;"></th>
                    <th>Training Objective</th>
                    <th style="width:70px;">Duration</th>
                    <th style="width:95px;" class="right">Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $course)
                    <tr>
                        <td><span class="box">&#9744;</span></td>
                        <td>
                            <strong>{{ $course->objective }}</strong>
                            @if ($course->requirement)
                                <div class="req-text">{{ collect(preg_split('/<\/(li|p|div)>|<br\s*\/?>/i', $course->requirement))->map(fn ($part) => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($part)))))->filter()->implode(' • ') }}</div>
                            @endif
                        </td>
                        <td>{{ $course->duration }} Weeks</td>
                        <td class="right">{{ $settings->base_currency ?? '' }}{{ number_format($course->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals" style="width: 55%; margin-left: 45%; margin-top: 8px;">
            <tr><td>Subtotal</td><td style="width:45%"></td></tr>
            <tr><td>Taxes (7.5%)</td><td></td></tr>
            <tr><td><strong>Total due (including taxes)</strong></td><td></td></tr>
            <tr><td>Total training period</td><td class="right">Weeks</td></tr>
        </table>
    </div>

    {{-- Mirrors the online application: Documents --}}
    <div class="keep">
        <div class="section">7. Documents Checklist</div>
        <div class="note">Please submit this form with:</div>
        <div style="margin-top:4px;"><span class="option"><span class="box">&#9744;</span>Recent passport photograph (affixed above)</span></div>
        <div><span class="option"><span class="box">&#9744;</span>Photocopy of NIN slip or card</span></div>
        <div><span class="option"><span class="box">&#9744;</span>Photocopy of driver's licence (CDL and BRT/School Bus applicants)</span></div>
    </div>

    {{-- Declaration --}}
    <div class="keep">
        <div class="section">Declaration</div>
        <div class="note">I declare that the information given in this form is true and complete to the best of my knowledge. I understand that providing false information, particularly regarding my medical history and drug use, may lead to the cancellation of my enrolment. I agree to the school's terms &amp; conditions and privacy policy, available at {{ preg_replace('#^https?://#', '', url('/')) }}.</div>
        <table style="margin-top: 18px;">
            <tr>
                <td class="pad-r" style="width:60%"><div class="line"></div><div class="muted">Applicant's Signature</div></td>
                <td style="width:40%"><div class="line"></div><div class="muted">Date</div></td>
            </tr>
        </table>

        <table style="margin-top: 16px; border: 1px solid #d1d5db;">
            <tr>
                <td style="padding: 8px;">
                    <div class="label" style="padding-top:0;">For Office Use Only</div>
                    <table>
                        <tr>
                            <td class="pad-r"><div class="muted">Received by</div><div class="line"></div></td>
                            <td class="pad-r"><div class="muted">Date received</div><div class="line"></div></td>
                            <td><div class="muted">Payment reference</div><div class="line"></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>

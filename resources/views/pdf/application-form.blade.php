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

    {{-- Step 1: Personal Information --}}
    <div class="section">1. Personal Information</div>
    <table>
        <tr>
            <td class="pad-r" style="width:50%"><div class="label">First Name <span class="req">*</span></div><div class="line"></div></td>
            <td style="width:50%"><div class="label">Middle Name</div><div class="line"></div></td>
        </tr>
        <tr>
            <td class="pad-r"><div class="label">Last Name <span class="req">*</span></div><div class="line"></div></td>
            <td>
                <div class="label">Gender <span class="req">*</span></div>
                <span class="option"><span class="box">&#9744;</span>Male</span>
                <span class="option"><span class="box">&#9744;</span>Female</span>
            </td>
        </tr>
        <tr>
            <td class="pad-r"><div class="label">Mobile Number <span class="req">*</span></div><div class="line"></div></td>
            <td><div class="label">Email Address <span class="req">*</span></div><div class="line"></div></td>
        </tr>
    </table>

    {{-- Step 2: Medical History --}}
    <div class="section">2. Medical History</div>
    <table>
        <tr>
            <td class="pad-r" style="width:50%"><div class="label">Weight (kg)</div><div class="line"></div></td>
            <td style="width:50%"><div class="label">Height (ft)</div><div class="line"></div></td>
        </tr>
    </table>

    <div class="label" style="margin-top:8px;">Answer the disability check below appropriately:</div>

    <div class="label">Visual Impairment <span class="req">*</span></div>
    @foreach (['None', 'Long Sightedness', 'Short Sightedness', 'Color Blindness'] as $option)
        <span class="option"><span class="box">&#9744;</span>{{ $option }}</span>
    @endforeach

    <div class="label">Hearing Aid <span class="req">*</span></div>
    @foreach (['None', 'BTE', 'ITE', 'RITE', 'ITC', 'CROS/BiCros'] as $option)
        <span class="option"><span class="box">&#9744;</span>{{ $option }}</span>
    @endforeach

    <div class="label">Physical Disability <span class="req">*</span></div>
    @foreach (['None', 'Ulcer', 'Cancer', 'HIV/AIDS', 'Abdominal Pain'] as $option)
        <span class="option"><span class="box">&#9744;</span>{{ $option }}</span>
    @endforeach

    <div class="label" style="margin-top:8px;">Answer the drug test check below appropriately:</div>

    <div class="label">Marijuana (Weed) / Cocaine <span class="req">*</span></div>
    @foreach (['Yes', 'No'] as $option)
        <span class="option"><span class="box">&#9744;</span>{{ $option }}</span>
    @endforeach

    <div class="label">Alcohol <span class="req">*</span></div>
    @foreach (['No', 'Often', 'Casually', 'Daily User'] as $option)
        <span class="option"><span class="box">&#9744;</span>{{ $option }}</span>
    @endforeach

    <div class="label">Are you currently taking any prescribed medications?</div>
    <div class="line"></div><div class="line"></div>

    <div class="label">Have you ever failed a drug test?</div>
    <div class="line"></div><div class="line"></div>

    {{-- Step 3: Training Objectives --}}
    <div class="keep">
        <div class="section">3. Training Objective(s)</div>
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

    {{-- Declaration --}}
    <div class="keep">
        <div class="section">Declaration</div>
        <div class="note">I declare that the information given in this form is true and complete to the best of my knowledge. I understand that providing false information, particularly regarding my medical history and drug use, may lead to the cancellation of my enrolment.</div>
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

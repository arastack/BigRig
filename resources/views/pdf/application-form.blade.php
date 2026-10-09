@php
    $currency = '<span class="naira">₦</span>';
    $contact = collect([$settings?->headquarters, $settings?->business_contact, $settings?->business_email])->filter()->join(' | ');
    $box = '<span class="box">&#9744;</span>';
    $ticks = fn (array $options) => collect($options)->map(fn ($o) => $box . ' ' . e($o))->join('&nbsp;&nbsp;');
    $field = fn ($label, $required = false) => '<div class="lbl">' . e($label) . ($required ? ' *' : '') . '</div><div class="line"></div>';
    $requirements = fn ($course) => collect($course->requirementList())->join('; ');
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
    <title>Student Application Form - {{ $settings->site_name ?? config('app.name') }}</title>
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
        <div class="name">BigRig</div>
        <div class="sub">INTERNATIONAL TRUCK DRIVING ACADEMY</div>
    </div>
    <div class="contact">{{ $contact }}</div>
    <div class="title">STUDENT APPLICATION FORM</div>

    @if ($page === 1)
        <div class="note">Please complete in BLOCK LETTERS. Fields marked * are required.</div>

        <div class="bar">1. PERSONAL INFORMATION</div>
        <table class="grid">
            <tr>
                <td style="width:25%">{!! $field('First Name', true) !!}</td>
                <td style="width:25%">{!! $field('Middle Name') !!}</td>
                <td style="width:25%">{!! $field('Last Name', true) !!}</td>
                <td rowspan="3" class="passport"><div>AFFIX RECENT<br>PASSPORT<br>PHOTOGRAPH<br>HERE</div></td>
            </tr>
            <tr>
                <td>{!! $field('Date of Birth', true) !!}</td>
                <td><div class="lbl">Gender *</div><div class="opts">{!! $ticks(['Male', 'Female']) !!}</div></td>
                <td><div class="lbl">Nationality *</div><div class="opts">{!! $box !!} Nigerian&nbsp;&nbsp;{!! $box !!} ECOWAS<br>{!! $box !!} Other: ________</div></td>
            </tr>
            <tr>
                <td>{!! $field('NIN Number', true) !!}</td>
                <td>{!! $field('Mobile Number', true) !!}</td>
                <td>{!! $field('Email Address', true) !!}</td>
            </tr>
        </table>
        <table class="grid">
            <tr><td colspan="3">{!! $field('Residential Address', true) !!}</td></tr>
            <tr>
                <td style="width:34%">{!! $field('City', true) !!}</td>
                <td style="width:33%">{!! $field('State', true) !!}</td>
                <td style="width:33%">{!! $field('LGA', true) !!}</td>
            </tr>
            <tr>
                <td>{!! $field('Emergency Contact Name', true) !!}</td>
                <td>{!! $field('Relationship', true) !!}</td>
                <td>{!! $field('Emergency Contact Phone', true) !!}</td>
            </tr>
        </table>

        <div class="bar">2. DRIVER'S LICENSE INFORMATION</div>
        <table class="grid">
            <tr>
                <td style="width:34%">{!! $field("Driver's License Number", true) !!}</td>
                <td style="width:33%">{!! $field('License Class', true) !!}</td>
                <td style="width:33%">{!! $field('State of Issue', true) !!}</td>
            </tr>
            <tr>
                <td>{!! $field('Issue Date', true) !!}</td>
                <td>{!! $field('Expiration Date', true) !!}</td>
                <td>{!! $field('Years / Months of Driving Experience', true) !!}</td>
            </tr>
            <tr>
                <td><span class="lbl">Valid license?</span> {!! $ticks(['Yes', 'No']) !!}</td>
                <td><span class="lbl">Copy attached?</span> {!! $ticks(['Yes', 'No']) !!}</td>
                <td><span class="lbl">Manual transmission?</span> {!! $ticks(['Yes', 'No']) !!}</td>
            </tr>
        </table>

        <div class="bar">3. DRIVING HISTORY</div>
        <table class="grid">
            @foreach (['License ever suspended or revoked?', 'Serious road accident?', 'Major traffic offense?', 'Previous commercial/truck driving experience?'] as $question)
                <tr><td><span class="lbl">{{ $question }}</span> {!! $ticks(['Yes', 'No']) !!}</td></tr>
            @endforeach
            <tr><td><div class="lbl">If yes, explain:</div><div class="line"></div><div class="line"></div></td></tr>
        </table>

        <div class="footer">PAGE 1 OF 3 | SAFETY &bull; SKILLS &bull; OPPORTUNITY &bull; GLOBAL CAREER</div>
    @elseif ($page === 2)
        <div class="bar">4. MEDICAL &amp; FITNESS DECLARATION</div>
        <table class="grid">
            <tr>
                <td style="width:50%">{!! $field('Weight (kg)', true) !!}</td>
                <td style="width:50%">{!! $field('Height (ft)', true) !!}</td>
            </tr>
        </table>
        <table class="grid">
            <tr>
                <td style="width:34%"><div class="lbl">A. Vision *</div><div class="opts">{!! $ticks(['None', 'Long Sightedness']) !!}<br>{!! $ticks(['Short Sightedness', 'Color Blindness']) !!}</div></td>
                <td style="width:33%"><div class="lbl">B. Hearing *</div><div class="opts">{!! $ticks(['None', 'BTE', 'ITE']) !!}<br>{!! $ticks(['RITE', 'ITC', 'CROS/BiCros']) !!}</div></td>
                <td style="width:33%"><div class="lbl">C. Physical / Mobility *</div><div class="opts">{!! $ticks(['None', 'Yes']) !!}<br>Explain: ____________________</div></td>
            </tr>
            <tr>
                <td><div class="lbl">D. Other Conditions</div><div class="opts">{!! $ticks(['None', 'Diabetes', 'Heart/Cardiovascular']) !!}<br>{!! $ticks(['Epilepsy/Seizure', 'High Blood Pressure']) !!}<br>{!! $ticks(['Asthma']) !!}&nbsp;&nbsp;{!! $box !!} Other: __________</div></td>
                <td><div class="lbl">E. Prescription Medication *</div><div class="opts">{!! $ticks(['Yes', 'No']) !!}<br>If yes: ______________________</div></td>
                <td><div class="lbl">F. Drug &amp; Alcohol *</div><div class="opts">Failed drug test? {!! $ticks(['Yes', 'No']) !!}<br>Marijuana/Cocaine: {!! $ticks(['Yes', 'No']) !!}<br>Alcohol: {!! $ticks(['None', 'Often', 'Casual', 'Daily']) !!}</div></td>
            </tr>
        </table>

        <div class="bar">5. CRIMINAL HISTORY DECLARATION</div>
        <table class="grid">
            <tr><td>
                <span class="lbl">Have you ever been convicted of an offense that may make you ineligible under the Academy's published admission requirements?</span> {!! $ticks(['Yes', 'No']) !!}
                <div class="lbl" style="margin-top:6px;">Details:</div><div class="line"></div>
            </td></tr>
        </table>

        <div class="bar">6. TRAINING OBJECTIVE(S)</div>
        <div class="note" style="margin-top:3px;">Tick at least one training objective. Current academy fees are shown below.</div>
        <table class="courses">
            <thead>
                <tr>
                    <th style="width:16px;"></th>
                    <th>PROGRAM</th>
                    <th style="width:62px;">DURATION</th>
                    <th style="width:78px;">PRICE</th>
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
                        <td><strong>{{ $course->duration }} Weeks</strong></td>
                        <td class="price">{!! $currency !!}{{ number_format($course->price) }}</td>
                    </tr>
                    @if ($loop->index === $comboAfter)
                        @foreach ($packages as $combo)
                            <tr>
                                <td><span class="box">&#9744;</span></td>
                                <td><strong>{{ $combo['name'] }}</strong>@if ($combo['description'])<div class="req">{!! str_replace('₦', $currency, e($combo['description'])) !!}</div>@endif</td>
                                <td><strong>{{ $combo['weeks'] ? $combo['weeks'] . ' Weeks' : '—' }}</strong></td>
                                <td class="price">{!! $currency !!}{{ number_format($combo['discount']) }}<br>DISCOUNT</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="note" style="margin-top:4px; font-weight:normal;">Fees exclude VAT at {{ $vatLabel }}%, which is added to the total due.</div>

        <div class="footer">PAGE 2 OF 3 | PROFESSIONAL TRAINING &bull; CERTIFIED DRIVERS &bull; BRIGHTER FUTURES</div>
    @else
        <div class="bar">7. ENROLLMENT PREFERENCES</div>
        <table class="grid">
            <tr>
                <td style="width:30%">{!! $field('Preferred Start Date', true) !!}</td>
                <td style="width:36%"><div class="lbl">Training Location *</div><div class="opts">{!! $box !!} Ibadan (Main Campus)&nbsp;&nbsp;{!! $box !!} Other: ________</div></td>
                <td style="width:34%"><div class="lbl">Class Preference *</div><div class="opts">{!! $ticks(['Full-Time', 'Part-Time']) !!}<br>{!! $ticks(['Morning', 'Afternoon', 'Weekend']) !!}</div></td>
            </tr>
        </table>

        <div class="bar">8. REQUIRED DOCUMENT CHECKLIST</div>
        <table class="grid">
            <tr>
                <td style="width:50%" class="opts">{!! $box !!} Passport Photograph<br>{!! $box !!} Valid Driver's License (copy)<br>{!! $box !!} NIN Slip (copy)<br>{!! $box !!} Valid ID / Passport (copy)</td>
                <td style="width:50%" class="opts">{!! $box !!} Medical Fitness Certificate<br>{!! $box !!} Vision Test Result<br>{!! $box !!} Drug Screening Result<br>{!! $box !!} Proof of Payment</td>
            </tr>
        </table>

        <div class="bar">9. PAYMENT INFORMATION</div>
        <table class="pay">
            @foreach (['Registration / Application Fee', 'Training Fee (as selected)', 'Discount (if applicable)', 'VAT (' . $vatLabel . '%)', 'Total Due', 'Amount Paid', 'Outstanding Balance'] as $row)
                <tr><td class="k">{{ $row }}</td><td>{!! $currency !!} ______________________________</td></tr>
            @endforeach
            <tr><td class="k">Payment Method</td><td>{!! $ticks(['Cash', 'Bank Transfer', 'POS', 'Other']) !!}</td></tr>
            <tr><td class="k">Payment Plan</td><td>{!! $ticks(['Full Payment', 'Installment']) !!}</td></tr>
            <tr><td class="k">Receipt / Transaction Number</td><td>________________________________</td></tr>
            <tr><td class="k">Next Payment Due Date</td><td>________________________________</td></tr>
        </table>

        <div class="bar">10. STUDENT AGREEMENT &amp; DECLARATION</div>
        <div class="declaration">I declare that the information provided in this application is true and complete to the best of my knowledge. I understand that false or misleading information may result in rejection or cancellation of enrollment. I acknowledge the Academy's attendance, safety, testing, drug/alcohol, tuition/payment, refund, conduct, dismissal, and completion requirements, and I agree to follow instructor directions and safety procedures during classroom and practical training.</div>
        <table class="grid">
            <tr>
                <td style="width:38%">{!! $field("Applicant's Full Name", true) !!}</td>
                <td style="width:38%">{!! $field("Applicant's Signature", true) !!}</td>
                <td style="width:24%">{!! $field('Date', true) !!}</td>
            </tr>
        </table>

        <div class="bar">11. FOR OFFICE USE ONLY</div>
        <table class="grid">
            <tr>
                <td style="width:34%"><div class="lbl">Application Status</div><div class="opts">{!! $ticks(['Approved', 'Conditional', 'Rejected']) !!}</div></td>
                <td style="width:33%">{!! $field('Student ID Number') !!}</td>
                <td style="width:33%">{!! $field('Class / Batch Number') !!}</td>
            </tr>
            <tr>
                <td>{!! $field('Assigned Instructor') !!}</td>
                <td>{!! $field('Training Start Date') !!}</td>
                <td>{!! $field('Expected Completion Date') !!}</td>
            </tr>
            <tr>
                <td>{!! $field('Received By') !!}</td>
                <td>{!! $field('Date Received') !!}</td>
                <td>{!! $field('Payment Reference') !!}</td>
            </tr>
            <tr>
                <td>{!! $field('Authorized Officer Name') !!}</td>
                <td>{!! $field('Signature') !!}</td>
                <td>{!! $field('Date') !!}</td>
            </tr>
        </table>

        <div class="footer">PAGE 3 OF 3 | DRIVE TODAY. BUILD A BETTER TOMORROW.</div>
    @endif

    @unless ($loop->last)<div class="page-break"></div>@endunless
@endforeach

</body>
</html>

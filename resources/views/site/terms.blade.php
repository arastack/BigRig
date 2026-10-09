@php $name = $settings->site_name ?? 'BigRig International Truck Driving School'; $email = $settings->business_email ?? null; @endphp
<x-site.layout title="Terms and conditions" description="Terms for applying to, paying for and training at BigRig International Truck Driving School.">
    <x-site.page-hero eyebrow="Legal" title="Terms & conditions" :crumbs="['Terms & conditions' => null]" lead="Last updated {{ \Carbon\Carbon::parse('2026-10-09')->format('j F Y') }}" />

    <section class="section">
        <div class="container-site">
            <div class="prose-site mx-auto max-w-3xl text-ink-600">
                <p>These terms apply when you use this website, apply for a programme or train with {{ $name }}. By submitting an application you agree to them.</p>

                <h2>1. Applications</h2>
                <p>You must provide accurate and complete information, including your health declaration. Providing false information — particularly about medical conditions or substance use — may lead to your application being refused or your enrolment being cancelled. You must meet the entry requirements published for each course.</p>

                <h2>2. Fees and payment</h2>
                <p>Tuition for each programme is shown on our website. Value Added Tax (VAT) at the applicable rate is added at checkout. Your place is confirmed, and your instructor assigned, once full payment has been received. Online payments are processed securely by Paystack.</p>

                <h2>3. Training</h2>
                <ul>
                    <li>Training is delivered in theory and practical sessions according to the schedule published in your student portal.</li>
                    <li>You must attend sessions on time and follow all safety instructions given by your instructor.</li>
                    <li>You must not attend training under the influence of alcohol or drugs. Doing so will result in removal from the session and may lead to dismissal without refund.</li>
                    <li>Missed sessions should be reported in advance; rescheduling is subject to instructor availability.</li>
                </ul>

                <h2>4. Certification</h2>
                <p>Certificates are issued on successful completion of the required sessions and examination. Government licences are issued by the relevant authorities; we prepare and support you through the process but cannot guarantee the outcome of external tests.</p>

                <h2>5. Refunds and transfers</h2>
                <p>Requests to withdraw, transfer to another batch or change programme must be made in writing to admissions. Refunds, where applicable, are assessed case by case and may be reduced by the cost of training already delivered.</p>

                <h2>6. Conduct</h2>
                <p>We expect respectful behaviour towards staff, instructors and fellow students, and care for our vehicles and equipment. Damage caused deliberately or through gross negligence may be charged.</p>

                <h2>7. Website</h2>
                <p>We work to keep information on this website accurate but it may change without notice. Course content, schedules and fees are confirmed at the time of enrolment.</p>

                <h2>8. Contact</h2>
                <p>Questions about these terms? Contact us @if ($email) at <a href="mailto:{{ $email }}">{{ $email }}</a>@endif.</p>
            </div>
        </div>
    </section>
</x-site.layout>

@php $name = $settings->site_name ?? 'BigRig International Truck Driving School'; $email = $settings->business_email ?? null; @endphp
<x-site.layout title="Privacy policy" description="How BigRig collects, uses and protects your personal, medical and payment information.">
    <x-site.page-hero eyebrow="Legal" title="Privacy policy" :crumbs="['Privacy policy' => null]" lead="Last updated {{ \Carbon\Carbon::parse('2026-10-09')->format('j F Y') }}" />

    <section class="section">
        <div class="container-site">
            <div class="prose-site mx-auto max-w-3xl text-ink-600">
                <p>{{ $name }} ("BigRig", "we", "us") respects your privacy. This policy explains what personal data we collect when you visit our website, apply for a course or train with us, why we collect it, and the choices you have. We process personal data in line with the Nigeria Data Protection Act 2023.</p>

                <h2>Information we collect</h2>
                <ul>
                    <li><strong>Identity and contact details</strong> — your name, gender, phone number, email address and, where you provide them, your address and identification details.</li>
                    <li><strong>Health and fitness declarations</strong> — weight, height, vision, hearing, medical conditions, medication and substance use you declare on the application form. We ask for this because operating heavy and commercial vehicles safely depends on it.</li>
                    <li><strong>Training records</strong> — your courses, batch, instructors, schedule, attendance, progress and examination results.</li>
                    <li><strong>Payment information</strong> — the amount, reference and status of your payments. Card and bank details are entered directly with our payment processor, Paystack; we never see or store your full card details.</li>
                    <li><strong>Enquiries</strong> — messages you send through our contact form, email, phone or WhatsApp, and newsletter subscriptions.</li>
                    <li><strong>Technical data</strong> — basic information such as your IP address and browser, used to keep the website secure and working.</li>
                </ul>

                <h2>How we use it</h2>
                <ul>
                    <li>To assess your application and confirm you meet the entry and safety requirements for your course.</li>
                    <li>To enrol you, assign instructors, schedule training and issue certificates.</li>
                    <li>To process payments and keep financial records.</li>
                    <li>To contact you about your application, training and important changes.</li>
                    <li>To send newsletters and intake announcements if you subscribed — you can unsubscribe at any time.</li>
                    <li>To meet legal and regulatory obligations, including those of road safety and licensing authorities.</li>
                </ul>

                <h2>Health information</h2>
                <p>Health declarations are sensitive personal data. We collect them only with your consent on the application form, use them only to ensure safe training, and restrict access to authorised admissions and training staff.</p>

                <h2>Who we share it with</h2>
                <p>We do not sell your personal data. We share it only with service providers who help us run the school (such as Paystack for payments and our hosting and email providers), with licensing or road safety authorities where required for your certification or licence, or where the law requires it.</p>

                <h2>How long we keep it</h2>
                <p>We keep training and payment records for as long as needed to provide our services and meet legal and accounting obligations. Applications that are never completed are deleted after a reasonable period.</p>

                <h2>Your rights</h2>
                <p>You may ask to access, correct or delete your personal data, object to or restrict how we use it, withdraw consent, or ask for a copy in a portable format. You may also complain to the Nigeria Data Protection Commission.</p>

                <h2>Contact</h2>
                <p>For any privacy question or request, contact us @if ($email) at <a href="mailto:{{ $email }}">{{ $email }}</a>@endif or visit us at {{ $settings->headquarters ?? 'our office' }}.</p>
            </div>
        </div>
    </section>
</x-site.layout>

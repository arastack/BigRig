<?php

/*
|--------------------------------------------------------------------------
| Editable website text
|--------------------------------------------------------------------------
|
| Every block of copy on the public website that is typed into a template
| rather than stored with other content. Each group is one tab in the admin
| "Page text" editor; each field has a label, a type (text, textarea or rich
| for HTML) and the default shown until the school saves its own wording.
| Read values with page_text('key'). Saved values live in the page_texts table.
|
*/

$privacy = <<<'HTML'
<p>{site_name} ("BigRig", "we", "us") respects your privacy. This policy explains what personal data we collect when you visit our website, apply for a course or train with us, why we collect it, and the choices you have. We process personal data in line with the Nigeria Data Protection Act 2023.</p>
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
<p>For any privacy question or request, contact us at {email} or visit us at {address}.</p>
HTML;

$terms = <<<'HTML'
<p>These terms apply when you use this website, apply for a programme or train with {site_name}. By submitting an application you agree to them.</p>
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
<p>Questions about these terms? Contact us at {email}.</p>
HTML;

$t = fn (string $label, string $default) => ['label' => $label, 'type' => 'text', 'default' => $default];
$ta = fn (string $label, string $default) => ['label' => $label, 'type' => 'textarea', 'default' => $default];
$rich = fn (string $label, string $default, ?string $help = null) => ['label' => $label, 'type' => 'rich', 'default' => $default, 'help' => $help];

$legalHelp = 'Write {site_name}, {email} or {address} to insert the school name, email link or address from Settings. The "Last updated" date changes when you save new wording.';

return [

    'home' => [
        'label' => 'Home',
        'route' => 'index',
        'fields' => [
            'home.hero.eyebrow' => $t('Hero: small line above the headline', 'Ibadan · Professional driver training'),
            'home.hero.title' => $t('Hero: headline when no homepage slides are set up', 'Drive professionally. Drive with BigRig.'),
            'home.hero.button_primary' => $t('Hero: main button', 'Start your application'),
            'home.hero.button_secondary' => $t('Hero: second button', 'Explore courses'),
            'home.stats.1' => $t('Numbers strip: students label', 'Drivers trained'),
            'home.stats.2' => $t('Numbers strip: pass rate label', 'First-time pass rate'),
            'home.stats.3' => $t('Numbers strip: instructors label', 'Certified instructors'),
            'home.stats.4' => $t('Numbers strip: courses label', 'Licence programmes'),
            'home.courses.eyebrow' => $t('Courses: small heading', 'Licence programmes'),
            'home.courses.title' => $t('Courses: heading', 'Pick the wheel you want to master'),
            'home.courses.lead' => $ta('Courses: introduction', 'Structured theory and hands-on practical sessions, an examination at the end, and an instructor assigned to you from day one.'),
            'home.courses.link' => $t('Courses: link to all courses', 'Compare all courses'),
            'home.steps.eyebrow' => $t('Admission steps: small heading', 'How admission works'),
            'home.steps.title' => $t('Admission steps: heading', "From application to the driver's seat"),
            'home.steps.lead' => $ta('Admission steps: introduction', 'No queues and no paperwork runs. Apply online, pay securely, and track every step from your student portal.'),
            'home.steps.button_primary' => $t('Admission steps: main button', 'Apply online'),
            'home.steps.button_secondary' => $t('Admission steps: paper form button', 'Paper form'),
            'home.steps.1.title' => $t('Step 1: title', 'Choose your course'),
            'home.steps.1.text' => $ta('Step 1: text', 'Compare programmes, durations and fees. Not sure which fits? Call or WhatsApp us.'),
            'home.steps.2.title' => $t('Step 2: title', 'Apply in 10 minutes'),
            'home.steps.2.text' => $ta('Step 2: text', 'Fill in your personal details, a short medical declaration and your course choice.'),
            'home.steps.3.title' => $t('Step 3: title', 'Pay securely'),
            'home.steps.3.text' => $ta('Step 3: text', 'Pay online by card, bank transfer or USSD through Paystack. You get confirmation instantly.'),
            'home.steps.4.title' => $t('Step 4: title', 'Start training'),
            'home.steps.4.text' => $ta('Step 4: text', 'Your instructor and timetable appear in your student portal, along with your study materials.'),
            'home.why.eyebrow' => $t('Why BigRig: small heading', 'Why BigRig'),
            'home.why.title' => $t('Why BigRig: heading (used when the About page has no history title)', 'Trained by professionals. Ready for the road.'),
            'home.why.badge' => $t('Why BigRig: text under the weeks badge', 'weeks from first lesson to certification'),
            'home.why.points.1' => $t('Why BigRig: point 1', 'Hands-on practice on real trucks, buses, forklifts and cars'),
            'home.why.points.2' => $t('Why BigRig: point 2', 'A dedicated instructor for every course you take'),
            'home.why.points.3' => $t('Why BigRig: point 3', 'Safety, defensive driving and road regulations built in'),
            'home.why.points.4' => $t('Why BigRig: point 4', 'Licensing support and job placement assistance'),
            'home.why.button' => $t('Why BigRig: button', 'More about us'),
            'home.services.eyebrow' => $t('Services: small heading', 'What we offer'),
            'home.services.title' => $t('Services: heading', 'More than a licence'),
            'home.services.cta_title' => $t('Services: yellow box heading', 'Not sure which training fits?'),
            'home.services.cta_text' => $ta('Services: yellow box text', "Tell us about your experience and goals — we'll recommend the right programme and next intake."),
            'home.services.cta_link' => $t('Services: yellow box link', 'Talk to admissions'),
            'home.team.eyebrow' => $t('Instructors: small heading', 'Your instructors'),
            'home.team.title' => $t('Instructors: heading', 'Experienced drivers who teach'),
            'home.team.link' => $t('Instructors: link', 'Meet the whole team'),
            'home.testimonials.eyebrow' => $t('Testimonials: small heading', 'Student stories'),
            'home.testimonials.title' => $t('Testimonials: heading', "Hear it from the driver's seat"),
            'home.faq.eyebrow' => $t('Questions: small heading', 'Questions'),
            'home.faq.title' => $t('Questions: heading', 'Before you apply'),
            'home.faq.lead_before' => $t('Questions: text before the link', "Can't find your answer?"),
            'home.faq.lead_link' => $t('Questions: link text', 'Send us a message'),
            'home.faq.lead_after' => $t('Questions: text after the link', 'and our admissions team will help.'),
            'home.faq.all_link' => $t('Questions: link to all questions', 'See all questions'),
            'home.partners.eyebrow' => $t('Partner logos: heading', 'Recognised by'),
        ],
    ],

    'courses' => [
        'label' => 'Courses',
        'route' => 'courses',
        'fields' => [
            'courses.meta.title' => $t('Browser tab title', 'Courses & Fees'),
            'courses.meta.description' => $ta('Search engine description', 'Truck (CDL), forklift, school bus/BRT and professional car driving courses in Ibadan. Compare durations, sessions and tuition, then apply online.'),
            'courses.hero.eyebrow' => $t('Header: small heading', 'Licence programmes'),
            'courses.hero.title' => $t('Header: page heading', 'Courses & fees'),
            'courses.hero.lead' => $ta('Header: introduction', 'Every programme combines classroom theory, supervised practical sessions and a final examination, taught by an instructor assigned to you.'),
            'courses.compare.eyebrow' => $t('Comparison: small heading', 'Side by side'),
            'courses.compare.title' => $t('Comparison: heading', 'Compare programmes'),
            'courses.compare.lead' => $ta('Comparison: introduction (:vat is replaced by the current VAT rate)', 'Tuition shown excludes :vat% VAT, which is added at checkout. You can enrol in more than one programme on the same application.'),
            'courses.combo.button' => $t('Combo discount: button', 'Apply for the combo'),
            'courses.services.eyebrow' => $t('Services: small heading', 'Also available'),
            'courses.services.title' => $t('Services: heading', 'Specialist training & support'),
            'courses.services.lead' => $ta('Services: introduction', 'Ask admissions about these programmes and services — several are arranged on request for individuals and companies.'),
        ],
    ],

    'course' => [
        'label' => 'Course page',
        'route' => 'course',
        'help' => 'Headings shared by every course page. Course names, prices and descriptions are edited under Courses & fees.',
        'fields' => [
            'course.hero.eyebrow' => $t('Header: small heading (:weeks is replaced by the course length)', ':weeks-week programme'),
            'course.hero.button_primary' => $t('Header: main button', 'Apply for this course'),
            'course.hero.button_secondary' => $t('Header: second button', 'Ask a question'),
            'course.video.title' => $t('Video: heading', 'See the training'),
            'course.curriculum.eyebrow' => $t('Curriculum: small heading', 'Curriculum'),
            'course.curriculum.title' => $t('Curriculum: heading', 'What you will learn'),
            'course.sidebar.tuition' => $t('Price box: label', 'Tuition'),
            'course.sidebar.certificate' => $t('Price box: certificate value', 'On successful completion'),
            'course.sidebar.apply' => $t('Price box: apply button', 'Apply now'),
            'course.sidebar.paper' => $t('Price box: paper form button', 'Paper form'),
            'course.sidebar.enrolling' => $t('Price box: current intake label', 'Now enrolling:'),
            'course.requirements.title' => $t('Entry requirements: heading', 'Entry requirements'),
            'course.faq.eyebrow' => $t('Questions: small heading', 'Good to know'),
            'course.faq.title' => $t('Questions: heading', 'Common questions'),
            'course.other.title' => $t('Other courses: heading', 'Other programmes'),
            'course.cta.title' => $t('Bottom banner heading (:course is replaced by the course name)', 'Ready to start :course?'),
        ],
    ],

    'about' => [
        'label' => 'About',
        'route' => 'about-us',
        'help' => 'The story, mission and banner title themselves are edited under About the school.',
        'fields' => [
            'about.meta.title' => $t('Browser tab title', 'About us'),
            'about.hero.eyebrow' => $t('Header: small heading', 'About BigRig'),
            'about.hero.title' => $t('Header: heading (used when no banner title is set)', 'About our driving school'),
            'about.story.eyebrow' => $t('Story: small heading', 'Our story'),
            'about.story.title' => $t('Story: heading (used when no history title is set)', 'Driving excellence'),
            'about.mission.title' => $t('Mission box: heading', 'Our mission'),
            'about.stats.1' => $t('Mission box: students label', 'Drivers trained'),
            'about.stats.2' => $t('Mission box: pass rate label', 'First-time pass rate'),
            'about.stats.3' => $t('Mission box: instructors label', 'Certified instructors'),
            'about.stats.4' => $t('Mission box: founding year label', 'Training drivers in Ibadan'),
            'about.milestones.eyebrow' => $t('Milestones: small heading', 'Milestones'),
            'about.milestones.title' => $t('Milestones: heading', 'Recognition along the way'),
            'about.team.eyebrow' => $t('Team: small heading', 'The people behind the wheel'),
            'about.team.title' => $t('Team: heading', 'Meet the team'),
            'about.team.link' => $t('Team: link', 'All instructors'),
        ],
    ],

    'instructors' => [
        'label' => 'Instructors',
        'route' => 'our-instructors',
        'fields' => [
            'instructors.meta.title' => $t('Browser tab title', 'Our instructors'),
            'instructors.meta.description' => $ta('Search engine description', 'Meet the certified driving instructors and team who train BigRig students in truck, forklift, bus and car driving.'),
            'instructors.hero.eyebrow' => $t('Header: small heading', 'Our team'),
            'instructors.hero.title' => $t('Header: page heading', 'Instructors who have driven the miles'),
            'instructors.hero.lead' => $ta('Header: introduction', 'Every BigRig student is assigned a dedicated instructor for each course — experienced professionals who teach safe, confident and defensive driving.'),
            'instructors.empty' => $t('Message when no instructors are listed', 'Our instructor profiles are coming soon.'),
            'instructors.values.1.title' => $t('Value 1: title', 'Safety first'),
            'instructors.values.1.text' => $ta('Value 1: text', 'Defensive driving and road regulations are part of every programme, not an optional extra.'),
            'instructors.values.2.title' => $t('Value 2: title', 'Small groups'),
            'instructors.values.2.text' => $ta('Value 2: text', 'Practical sessions are scheduled so you get real time behind the wheel with your instructor.'),
            'instructors.values.3.title' => $t('Value 3: title', 'Exam ready'),
            'instructors.values.3.text' => $ta('Value 3: text', 'Mock tests and inspection practice prepare you for certification and your licence test.'),
        ],
    ],

    'faq' => [
        'label' => 'FAQ',
        'route' => 'faq',
        'help' => 'The questions and answers themselves are edited under FAQs.',
        'fields' => [
            'faq.meta.title' => $t('Browser tab title', 'Frequently asked questions'),
            'faq.meta.description' => $ta('Search engine description', 'Answers about BigRig courses, admission requirements, fees, certification and our location in Ibadan.'),
            'faq.hero.eyebrow' => $t('Header: small heading', 'Help'),
            'faq.hero.title' => $t('Header: page heading', 'Frequently asked questions'),
            'faq.hero.lead' => $ta('Header: introduction', 'Everything you need to know about courses, requirements, payment and certification.'),
            'faq.aside.title' => $t('Side box: heading', 'Still have a question?'),
            'faq.aside.text' => $ta('Side box: text', 'Our admissions team is happy to help you choose the right course.'),
            'faq.aside.button' => $t('Side box: contact button', 'Contact admissions'),
            'faq.aside.call' => $t('Side box: call button', 'Call us'),
        ],
    ],

    'contact' => [
        'label' => 'Contact',
        'route' => 'contact',
        'help' => 'Phone numbers, emails and the address come from Settings.',
        'fields' => [
            'contact.meta.title' => $t('Browser tab title', 'Contact us'),
            'contact.meta.description' => $ta('Search engine description', 'Call, WhatsApp, email or visit BigRig International Truck Driving School in Ibadan. Send us a message and our admissions team will get back to you.'),
            'contact.hero.eyebrow' => $t('Header: small heading', 'Get in touch'),
            'contact.hero.title' => $t('Header: page heading', 'Talk to admissions'),
            'contact.hero.lead' => $ta('Header: introduction', 'Questions about courses, fees, requirements or the next intake? Send a message and our admissions team will get back to you.'),
            'contact.visit.title' => $t('Address box: heading', 'Visit our campus'),
            'contact.visit.link' => $t('Address box: directions link', 'Get directions'),
            'contact.call.title' => $t('Phone box: heading', 'Call us'),
            'contact.email.title' => $t('Email box: heading', 'Email'),
            'contact.whatsapp.title' => $t('WhatsApp box: heading', 'Chat on WhatsApp'),
            'contact.whatsapp.text' => $t('WhatsApp box: text', 'Fastest way to reach admissions'),
            'contact.form.title' => $t('Form: heading', 'Send a message'),
            'contact.form.button' => $t('Form: send button', 'Send message'),
        ],
    ],

    'cta' => [
        'label' => 'Call-to-action band',
        'route' => 'index',
        'help' => 'The yellow "apply" band near the bottom of most pages.',
        'fields' => [
            'cta.title' => $t('Heading', 'Your road to a professional licence starts here'),
            'cta.lead' => $ta('Text', 'Apply online in about 10 minutes. Choose your course, tell us a little about yourself, and pay securely — your instructor is assigned as soon as your fees are confirmed.'),
            'cta.button_primary' => $t('Main button', 'Apply online'),
            'cta.button_secondary' => $t('Form download button', 'PDF form'),
        ],
    ],

    'footer' => [
        'label' => 'Footer',
        'route' => 'index',
        'fields' => [
            'footer.newsletter.title' => $t('Newsletter: heading', 'Get intake dates first'),
            'footer.newsletter.text' => $ta('Newsletter: text', 'News on new batches, schedules and driving careers. No spam — unsubscribe any time.'),
            'site.title_suffix' => $t('Homepage browser tab title (after the school name)', 'Professional Driver Training in Nigeria'),
        ],
    ],

    'auth' => [
        'label' => 'Sign-in panel',
        'route' => 'login',
        'help' => 'The dark panel beside the sign-in, sign-up and password pages.',
        'fields' => [
            'auth.eyebrow' => $t('Small heading', 'Professional driver training'),
            'auth.title' => $t('Heading', 'Your licence. Your career. Your road.'),
            'auth.points.1' => $t('Point 1', 'Track your application and payment'),
            'auth.points.2' => $t('Point 2', 'See your instructor and timetable'),
            'auth.points.3' => $t('Point 3', 'Download course materials any time'),
        ],
    ],

    'privacy' => [
        'label' => 'Privacy policy',
        'route' => 'privacy',
        'fields' => [
            'privacy.meta.title' => $t('Browser tab title', 'Privacy policy'),
            'privacy.meta.description' => $ta('Search engine description', 'How BigRig collects, uses and protects your personal, medical and payment information.'),
            'privacy.hero.eyebrow' => $t('Header: small heading', 'Legal'),
            'privacy.title' => $t('Page heading', 'Privacy policy'),
            'privacy.body' => $rich('Policy text', $privacy, $legalHelp),
        ],
    ],

    'terms' => [
        'label' => 'Terms',
        'route' => 'terms',
        'fields' => [
            'terms.meta.title' => $t('Browser tab title', 'Terms and conditions'),
            'terms.meta.description' => $ta('Search engine description', 'Terms for applying to, paying for and training at BigRig International Truck Driving School.'),
            'terms.hero.eyebrow' => $t('Header: small heading', 'Legal'),
            'terms.title' => $t('Page heading', 'Terms & conditions'),
            'terms.body' => $rich('Terms text', $terms, $legalHelp),
        ],
    ],

];

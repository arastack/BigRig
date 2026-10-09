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
            'courses.table.programme' => $t('Comparison table: "Programme" column heading', 'Programme'),
            'courses.table.duration' => $t('Comparison table: "Duration" column heading', 'Duration'),
            'courses.table.theory' => $t('Comparison table: "Theory" column heading', 'Theory'),
            'courses.table.practical' => $t('Comparison table: "Practical" column heading', 'Practical'),
            'courses.table.exam' => $t('Comparison table: "Exam" column heading', 'Exam'),
            'courses.table.tuition' => $t('Comparison table: "Tuition" column heading', 'Tuition'),
            'courses.table.weeks' => $t('Comparison table: course length (:weeks is replaced by the number of weeks)', ':weeks weeks'),
            'courses.table.sessions' => $t('Comparison table: theory/practical sessions (:count is replaced by the number)', ':count sessions'),
            'courses.table.exam_default' => $t('Comparison table: exam value when none is set', 'Included'),
            'courses.table.apply' => $t('Comparison table: apply button', 'Apply'),
            'courses.combo.title' => $t('Combo discount: heading (:name is the package name, :amount the saving)', ':name — save :amount'),
            'courses.combo.weeks' => $t('Combo discount: length sentence (:weeks is replaced by the number of weeks)', 'Complete both in about :weeks weeks.'),
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
            'course.sidebar.vat' => $t('Price box: VAT line (:amount is the VAT amount, :vat the VAT rate)', '+ :amount VAT (:vat%)'),
            'course.sidebar.duration' => $t('Price box: "Duration" label', 'Duration'),
            'course.sidebar.weeks' => $t('Price box: course length (:weeks is replaced by the number of weeks)', ':weeks weeks'),
            'course.sidebar.theory' => $t('Price box: "Theory sessions" label', 'Theory sessions'),
            'course.sidebar.practical' => $t('Price box: "Practical sessions" label', 'Practical sessions'),
            'course.sidebar.examination' => $t('Price box: "Examination" label', 'Examination'),
            'course.sidebar.exam_default' => $t('Price box: examination value when none is set', 'Included'),
            'course.sidebar.certificate_label' => $t('Price box: "Certificate" label', 'Certificate'),
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
            'about.stats.since' => $t('Mission box: founding year value (:year is replaced by the year from Settings)', 'Since :year'),
            'about.founder.caption' => $t("Founder's message: caption under the name (also on the Home page)", 'Founder'),
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
            'instructors.card.role' => $t('Team cards: job title shown for staff who are not instructors (also on Home and About)', 'Management'),
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
            'footer.newsletter.email_label' => $t('Newsletter: email box label (read out by screen readers)', 'Email address'),
            'footer.newsletter.placeholder' => $t('Newsletter: example text inside the email box', 'you@example.com'),
            'footer.newsletter.button' => $t('Newsletter: subscribe button', 'Subscribe'),
            'footer.explore' => $t('Column heading: page links', 'Explore'),
            'footer.admissions' => $t('Column heading: admissions links', 'Admissions'),
            'footer.links.apply' => $t('Admissions link: apply online', 'Apply online'),
            'footer.links.pdf' => $t('Admissions link: paper form', 'Download application form (PDF)'),
            'footer.links.track' => $t('Admissions link: sign in to track an application', 'Track my application'),
            'footer.links.faq' => $t('Admissions link: questions', 'Admission questions'),
            'footer.visit' => $t('Column heading: address and contact details', 'Visit us'),
            'footer.rights' => $t('Copyright line (after the year and school name)', 'All rights reserved.'),
            'footer.privacy' => $t('Bottom link: privacy policy (also the breadcrumb on that page)', 'Privacy policy'),
            'footer.terms' => $t('Bottom link: terms (also the breadcrumb on that page)', 'Terms & conditions'),
            'footer.whatsapp_message' => $ta('WhatsApp button: message typed in for the visitor', 'Hello BigRig, I would like to know more about your courses.'),
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
            'privacy.updated' => $t('Header: date line (:date is replaced by the date the policy was last saved)', 'Last updated :date'),
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
            'terms.updated' => $t('Header: date line (:date is replaced by the date the terms were last saved)', 'Last updated :date'),
            'terms.body' => $rich('Terms text', $terms, $legalHelp),
        ],
    ],

    'nav' => [
        'label' => 'Header & menu',
        'route' => 'index',
        'help' => 'The menu at the top of every page. The same page names are used in the mobile menu, the footer "Explore" links and the breadcrumbs under each page heading.',
        'fields' => [
            'nav.home' => $t('Menu: Home', 'Home'),
            'nav.courses' => $t('Menu: Courses', 'Courses'),
            'nav.about' => $t('Menu: About', 'About'),
            'nav.instructors' => $t('Menu: Instructors', 'Instructors'),
            'nav.faq' => $t('Menu: FAQ', 'FAQ'),
            'nav.contact' => $t('Menu: Contact', 'Contact'),
            'nav.apply' => $t('Apply button', 'Apply now'),
            'nav.sign_in' => $t('Sign-in link', 'Sign in'),
            'nav.portal' => $t('Portal button (shown when signed in, also on error pages)', 'My portal'),
            'nav.admissions_open' => $t('Top bar: admissions open link (:batch is replaced by the intake name)', 'Admissions open — :batch'),
            'nav.skip' => $t('Keyboard "skip to content" link', 'Skip to content'),
        ],
    ],

    'course_card' => [
        'label' => 'Course cards',
        'route' => 'courses',
        'help' => 'The course boxes on the Home, Courses and course pages.',
        'fields' => [
            'course_card.weeks' => $t('Badge on the picture (:weeks is replaced by the number of weeks)', ':weeks weeks'),
            'course_card.theory' => $t('"Theory" label', 'Theory'),
            'course_card.practical' => $t('"Practical" label', 'Practical'),
            'course_card.exam' => $t('"Exam" label', 'Exam'),
            'course_card.exam_default' => $t('Exam value when none is set', 'Yes'),
            'course_card.tuition' => $t('Price label', 'Tuition'),
        ],
    ],

    'contact_form' => [
        'label' => 'Contact form',
        'route' => 'contact',
        'help' => 'The "Send a message" form on the Contact page. Its heading and send button are on the Contact tab.',
        'fields' => [
            'contact_form.required_note' => $t('Note under the heading (the * is shown in red)', 'Fields marked * are required.'),
            'contact_form.error' => $t('Message when something is missing', 'Please check the highlighted fields and try again.'),
            'contact_form.name' => $t('"Full name" label', 'Full name'),
            'contact_form.email' => $t('"Email" label', 'Email'),
            'contact_form.phone' => $t('"Phone" label', 'Phone'),
            'contact_form.course' => $t('"Course of interest" label', 'Course of interest'),
            'contact_form.course_none' => $t('Course list: first option', 'Not sure yet'),
            'contact_form.subject' => $t('"Subject" label', 'Subject'),
            'contact_form.subject_placeholder' => $t('Subject: example text inside the box', 'e.g. Next intake date'),
            'contact_form.message' => $t('"Message" label', 'Message'),
            'contact_form.privacy' => $rich('Privacy note beside the send button', 'We use your details only to answer your enquiry. See our <a href="{privacy_url}">privacy policy</a>.', 'Keep {privacy_url} as the link address to point at the privacy policy page.'),
            'contact_form.sending' => $t('Send button while sending', 'Sending…'),
        ],
    ],

    'errors' => [
        'label' => 'Error pages',
        'help' => 'Shown when a page cannot be found or something goes wrong.',
        'fields' => [
            'errors.404.title' => $t('Page not found (404): heading', 'Wrong turn'),
            'errors.404.message' => $ta('Page not found (404): message', 'The page you are looking for has moved or no longer exists.'),
            'errors.403.title' => $t('No access (403): heading', 'No entry'),
            'errors.403.message' => $ta('No access (403): message (used when no specific reason is given)', "You don't have permission to view this page. If you think this is a mistake, contact the school."),
            'errors.419.title' => $t('Session expired (419): heading', 'Session expired'),
            'errors.419.message' => $ta('Session expired (419): message', 'Your session timed out for security. Please go back, refresh the page and try again.'),
            'errors.429.title' => $t('Too many requests (429): heading', 'Slow down'),
            'errors.429.message' => $ta('Too many requests (429): message', 'Too many requests in a short time. Please wait a minute and try again.'),
            'errors.500.title' => $t('Server error (500): heading', 'Engine trouble'),
            'errors.500.message' => $ta('Server error (500): message', 'Something went wrong on our side. Please try again in a moment.'),
            'errors.503.title' => $t('Maintenance (503): heading', 'Back shortly'),
            'errors.503.message' => $ta('Maintenance (503): message', "We're doing some maintenance. Please check back soon."),
            'errors.home' => $t('Button: back to the homepage', 'Back to home'),
            'errors.contact' => $t('Button: contact page (for visitors who are not signed in)', 'Contact us'),
        ],
    ],

    'register' => [
        'label' => 'Application form',
        'route' => 'register',
        'help' => 'The online application. The health questions and next-of-kin relationships are edited under Admissions settings.',
        'fields' => [
            'register.meta.title' => $t('Browser tab title', 'Apply'),
            'register.eyebrow' => $t('Header: small heading', 'Online application'),
            'register.title' => $t('Header: page heading', 'Apply to BigRig'),
            'register.lead' => $rich('Header: introduction', 'Takes about 10 minutes. Have your NIN and a passport photograph ready. Prefer paper? <a href="{pdf_url}">Download the PDF form</a>.', 'Keep {pdf_url} as the link address to point at the paper form download.'),
            'register.closed' => $rich('Banner when admissions are closed (no active intake)', '<strong>Admissions are currently closed.</strong> You can still look around, but applications will open with the next intake. <a href="{contact_url}">Contact us</a> to be notified.', 'Keep {contact_url} as the link address to point at the Contact page.'),
            'register.steps.1' => $t('Progress bar: step 1 name', 'Course'),
            'register.steps.2' => $t('Progress bar: step 2 name', 'About you'),
            'register.steps.3' => $t('Progress bar: step 3 name', 'Health'),
            'register.steps.4' => $t('Progress bar: step 4 name', 'Documents'),
            'register.errors.title' => $t('Error box: heading', "Some details need attention — we've opened the step that needs fixing."),
            'register.errors.text' => $ta('Error box: text', 'For your security, please re-enter your password and re-attach your passport photograph and any documents.'),
            'register.buttons.back' => $t('Button: back', 'Back'),
            'register.buttons.continue' => $t('Button: continue', 'Continue'),
            'register.buttons.submit' => $t('Button: submit', 'Submit application'),
            'register.buttons.submitting' => $t('Button: while submitting', 'Submitting…'),
            'register.password_mismatch' => $t('Message when the two passwords differ', 'Passwords do not match.'),
            'register.summary.title' => $t('Summary box: heading', 'Your selection'),
            'register.summary.empty' => $t('Summary box: when no course is chosen', 'No course selected yet.'),
            'register.summary.subtotal' => $t('Summary box: "Subtotal" label', 'Subtotal'),
            'register.summary.vat' => $t('Summary box: VAT label (:vat is replaced by the VAT rate)', 'VAT (:vat%)'),
            'register.summary.total' => $t('Summary box: "Total due" label', 'Total due'),
            'register.summary.length' => $t('Summary box: programme length (:weeks is replaced by the number of weeks)', 'Programme length: :weeks weeks'),
            'register.summary.length_multi' => $t('Summary box: added after the length when more than one course is chosen', 'if taken one after another'),
            'register.summary.intake' => $t('Summary box: intake (:batch is replaced by the intake name)', 'Intake: :batch'),
            'register.summary.note' => $ta('Summary box: payment note', 'You pay after submitting. Your place is confirmed when payment is received.'),

            'register.course.title' => $t('Step 1 – Course: heading', 'Choose your course'),
            'register.course.lead' => $ta('Step 1 – Course: introduction', 'Select one or more programmes. You can change your selection until you pay.'),
            'register.course.error' => $t('Step 1 – Course: message when no course is ticked', 'Please choose at least one course to continue.'),
            'register.course.combo' => $t('Step 1 – Course: combo note when the package has no description (:amount is the saving)', 'Save :amount when you enrol for these courses together.'),
            'register.course.weeks' => $t('Step 1 – Course: course length (:weeks is replaced by the number of weeks)', ':weeks weeks'),
            'register.course.sessions' => $t('Step 1 – Course: sessions (:theory and :practical are replaced by the numbers)', ':theory theory · :practical practical'),
            'register.course.requirements' => $t('Step 1 – Course: "Entry requirements" link', 'Entry requirements'),

            'register.personal.title' => $t('Step 2 – About you: heading', 'About you'),
            'register.personal.lead' => $ta('Step 2 – About you: introduction', 'Enter your details exactly as they appear on your ID.'),
            'register.personal.legend' => $t('Step 2 – Section heading: personal details', 'Personal details'),
            'register.personal.first_name' => $t('Step 2 – First name label', 'First name'),
            'register.personal.middle_name' => $t('Step 2 – Middle name label', 'Middle name'),
            'register.personal.last_name' => $t('Step 2 – Last name label', 'Last name'),
            'register.personal.gender' => $t('Step 2 – Gender label', 'Gender'),
            'register.personal.male' => $t('Step 2 – Gender: male button', 'Male'),
            'register.personal.female' => $t('Step 2 – Gender: female button', 'Female'),
            'register.personal.dob' => $t('Step 2 – Date of birth label', 'Date of birth'),
            'register.personal.dob_hint' => $t('Step 2 – Date of birth hint', 'You must be 18 or older.'),
            'register.personal.nin' => $t('Step 2 – NIN label', 'NIN'),
            'register.personal.nin_hint' => $t('Step 2 – NIN hint', '11-digit National Identification Number'),
            'register.contact.legend' => $t('Step 2 – Section heading: contact', 'Contact'),
            'register.contact.phone' => $t('Step 2 – Phone number label', 'Phone number'),
            'register.contact.phone_placeholder' => $t('Step 2 – Phone number example text', '0803 123 4567'),
            'register.contact.email' => $t('Step 2 – Email address label', 'Email address'),
            'register.contact.email_hint' => $t('Step 2 – Email address hint', "We'll send your application updates here."),
            'register.contact.address' => $t('Step 2 – Home address label', 'Home address'),
            'register.contact.city' => $t('Step 2 – Town / city label', 'Town / city'),
            'register.contact.state' => $t('Step 2 – State label', 'State'),
            'register.contact.state_placeholder' => $t('Step 2 – State list: first option', 'Select state'),
            'register.licence.legend' => $t('Step 2 – Section heading: driving licence', 'Driving licence'),
            'register.licence.legend_note' => $t('Step 2 – Driving licence: note beside the heading', '— if you have one'),
            'register.licence.note' => $t('Step 2 – Driving licence: text under the heading', 'Required for the CDL and BRT/School Bus programmes.'),
            'register.licence.number' => $t('Step 2 – Licence number label', 'Licence number'),
            'register.licence.expiry' => $t('Step 2 – Licence expiry date label', 'Expiry date'),
            'register.kin.legend' => $t('Step 2 – Section heading: next of kin', 'Next of kin'),
            'register.kin.note' => $t('Step 2 – Next of kin: text under the heading', 'Who should we contact in an emergency?'),
            'register.kin.name' => $t('Step 2 – Next of kin: full name label', 'Full name'),
            'register.kin.phone' => $t('Step 2 – Next of kin: phone number label', 'Phone number'),
            'register.kin.relationship' => $t('Step 2 – Next of kin: relationship label', 'Relationship'),

            'register.documents.title' => $t('Step 4 – Documents: heading', 'Documents & account'),
            'register.documents.lead' => $ta('Step 4 – Documents: introduction', 'Upload your passport photograph and create a password to track your application.'),
            'register.documents.photo' => $t('Step 4 – Passport photograph label (also shown in the empty photo box)', 'Passport photograph'),
            'register.documents.photo_tip_1' => $t('Step 4 – Photo guideline 1', 'Recent, in colour, with a plain light background'),
            'register.documents.photo_tip_2' => $t('Step 4 – Photo guideline 2', 'Face clearly visible, no sunglasses or caps'),
            'register.documents.photo_tip_3' => $t('Step 4 – Photo guideline 3', 'JPG or PNG, up to 3 MB'),
            'register.documents.licence' => $t("Step 4 – Driver's licence upload label", "Driver's licence (front)"),
            'register.documents.nin' => $t('Step 4 – NIN slip upload label', 'NIN slip or card'),
            'register.documents.optional' => $t('Step 4 – "optional" note beside the upload labels', '— optional'),
            'register.documents.file_hint' => $t('Step 4 – Upload hint', 'PDF or image, up to 5 MB. You can also upload this later.'),
            'register.password.legend' => $t('Step 4 – Section heading: password', 'Create your password'),
            'register.password.password' => $t('Step 4 – Password label', 'Password'),
            'register.password.hint' => $t('Step 4 – Password hint', 'At least 8 characters.'),
            'register.password.confirm' => $t('Step 4 – Confirm password label', 'Confirm password'),
            'register.terms' => $rich('Step 4 – Agreement tick box', 'I confirm the information in this application is accurate, and I agree to the <a href="{terms_url}" target="_blank">terms &amp; conditions</a> and <a href="{privacy_url}" target="_blank">privacy policy</a>.', 'Keep {terms_url} and {privacy_url} as the link addresses to point at the terms and privacy pages.'),
        ],
    ],

    'application' => [
        'label' => 'Application tracker',
        'help' => 'The page applicants see after applying, where they follow their progress, pay and upload documents.',
        'fields' => [
            'application.meta.title' => $t('Browser tab title', 'My application'),
            'application.number' => $t('Header: application number (:number is replaced by the number)', 'Application #:number'),
            'application.greeting' => $t('Header: greeting (:name is replaced by the first name)', 'Hello, :name'),
            'application.progress.title' => $t('Progress: heading', 'Your progress'),
            'application.progress.next' => $t('Progress: badge on the current step', 'Next step'),
            'application.courses.title' => $t('Courses: heading', 'Your courses'),
            'application.courses.change' => $t('Courses: change link', 'Change courses'),
            'application.courses.cancel' => $t('Courses: cancel link', 'Cancel'),
            'application.courses.detail' => $t('Courses: course details (:weeks, :theory and :practical are replaced by the numbers)', ':weeks weeks · :theory theory, :practical practical sessions'),
            'application.courses.weeks' => $t('Courses: course length when changing courses (:weeks is replaced by the number)', ':weeks weeks'),
            'application.courses.none' => $t('Courses: when no course is chosen', 'No course selected.'),
            'application.courses.save' => $t('Courses: save button', 'Save selection'),
            'application.documents.title' => $t('Documents: heading', 'Documents'),
            'application.documents.lead' => $ta('Documents: introduction', 'Uploads are private and only visible to you and the admissions team.'),
            'application.documents.photo' => $t('Documents: passport photograph', 'Passport photograph'),
            'application.documents.required' => $t('Documents: "required" note', 'required'),
            'application.documents.optional' => $t('Documents: "optional" note', 'optional'),
            'application.documents.uploaded' => $t('Documents: photo uploaded', 'Uploaded'),
            'application.documents.missing' => $t('Documents: photo missing', 'Missing'),
            'application.documents.not_uploaded' => $t('Documents: document not uploaded', 'Not uploaded'),
            'application.documents.pending' => $t('Documents: status awaiting review', 'Awaiting review'),
            'application.documents.approved' => $t('Documents: status approved', 'Approved'),
            'application.documents.rejected' => $t('Documents: status rejected', 'Rejected'),
            'application.documents.upload' => $t('Documents: upload button', 'Upload'),
            'application.documents.replace' => $t('Documents: replace button', 'Replace'),
            'application.documents.uploading' => $t('Documents: button while uploading', 'Uploading…'),
            'application.paid.title' => $t('Paid box: small heading', 'Fees'),
            'application.paid.status' => $t('Paid box: status', 'Paid'),
            'application.paid.amount' => $t('Paid box: "Amount" label', 'Amount'),
            'application.paid.reference' => $t('Paid box: "Reference" label', 'Reference'),
            'application.paid.method' => $t('Paid box: "Method" label', 'Method'),
            'application.paid.portal' => $t('Paid box: portal button', 'Open student portal'),
            'application.due.title' => $t('Payment box: small heading', 'Amount due'),
            'application.due.tuition' => $t('Payment box: "Tuition" label', 'Tuition'),
            'application.due.discount' => $t('Payment box: discount label', 'Combo discount'),
            'application.due.vat' => $t('Payment box: VAT label (:vat is replaced by the VAT rate)', 'VAT (:vat%)'),
            'application.due.pay' => $t('Payment box: pay button', 'Pay securely now'),
            'application.due.redirecting' => $t('Payment box: button while redirecting', 'Redirecting to Paystack…'),
            'application.due.note' => $ta('Payment box: note under the button', "Card, bank transfer or USSD via Paystack. You'll return here automatically."),
            'application.other.title' => $t('Other payment: heading', 'Paying another way?'),
            'application.other.text' => $ta('Other payment: text (:number is replaced by the application number, in bold)', 'If you paid at the school or by direct transfer, admissions will record it and your status updates here. Quote application :number.'),
            'application.other.link' => $t('Other payment: contact link', 'Contact admissions'),
            'application.details.title' => $t('Your details: heading', 'Your details'),
            'application.details.intake' => $t('Your details: intake label', 'Intake:'),
        ],
    ],

    'auth_pages' => [
        'label' => 'Sign-in & password pages',
        'route' => 'login',
        'help' => 'Sign in, forgotten password, new password, email verification and two-step verification. The dark side panel is on the "Sign-in panel" tab.',
        'fields' => [
            'auth_pages.sign_out' => $t('Top bar (application pages): sign-out button', 'Sign out'),
            'auth_pages.applied' => $t('Top bar (application form): text before the sign-in link', 'Already applied?'),
            'auth_pages.sign_in' => $t('Top bar (application form): sign-in link', 'Sign in'),
            'auth_pages.privacy' => $t('Bottom link: privacy', 'Privacy'),
            'auth_pages.terms' => $t('Bottom link: terms', 'Terms'),
            'auth_pages.errors' => $t('Error box: heading', 'Please fix the following:'),
            'login.meta.title' => $t('Sign in: browser tab title', 'Sign in'),
            'login.title' => $t('Sign in: heading', 'Welcome back'),
            'login.lead' => $ta('Sign in: introduction', 'Sign in to track your application, pay fees or open your student or staff portal.'),
            'login.email' => $t('Sign in: email label', 'Email address'),
            'login.password' => $t('Sign in: password label', 'Password'),
            'login.forgot' => $t('Sign in: forgotten password link', 'Forgot password?'),
            'login.show' => $t('Sign in: show password button', 'Show'),
            'login.hide' => $t('Sign in: hide password button', 'Hide'),
            'login.remember' => $t('Sign in: remember me tick box', 'Keep me signed in'),
            'login.button' => $t('Sign in: button', 'Sign in'),
            'login.sending' => $t('Sign in: button while signing in', 'Signing in…'),
            'login.new.title' => $t('Sign in: new applicant box heading', 'New to BigRig?'),
            'login.new.text' => $rich('Sign in: new applicant box text', 'Apply online in about 10 minutes, or <a href="{pdf_url}">download the paper form</a>.', 'Keep {pdf_url} as the link address to point at the paper form download.'),
            'login.new.button' => $t('Sign in: new applicant box button', 'Start an application'),
            'forgot.meta.title' => $t('Forgot password: browser tab title', 'Reset your password'),
            'forgot.title' => $t('Forgot password: heading', 'Forgot your password?'),
            'forgot.lead' => $ta('Forgot password: introduction', "Enter the email address you applied with and we'll send you a link to choose a new password."),
            'forgot.email' => $t('Forgot password: email label', 'Email address'),
            'forgot.button' => $t('Forgot password: button', 'Email me a reset link'),
            'forgot.back' => $t('Forgot password: back link', 'Back to sign in'),
            'reset.meta.title' => $t('New password: browser tab title', 'Choose a new password'),
            'reset.title' => $t('New password: heading', 'Choose a new password'),
            'reset.lead' => $ta('New password: introduction', 'Use at least 8 characters. A mix of words, numbers and symbols is strongest.'),
            'reset.email' => $t('New password: email label', 'Email address'),
            'reset.password' => $t('New password: password label', 'New password'),
            'reset.confirm' => $t('New password: confirm label', 'Confirm new password'),
            'reset.button' => $t('New password: button', 'Save new password'),
            'confirm.meta.title' => $t('Confirm password: browser tab title', 'Confirm password'),
            'confirm.title' => $t('Confirm password: heading', "Confirm it's you"),
            'confirm.lead' => $ta('Confirm password: introduction', 'This is a secure area. Please enter your password to continue.'),
            'confirm.password' => $t('Confirm password: password label', 'Password'),
            'confirm.button' => $t('Confirm password: button', 'Confirm'),
            'verify.meta.title' => $t('Verify email: browser tab title', 'Verify your email'),
            'verify.title' => $t('Verify email: heading', 'Check your inbox'),
            'verify.lead' => $ta('Verify email: introduction (:email is replaced by their email address, in bold)', 'We sent a verification link to :email. Click it to confirm your email address.'),
            'verify.sent' => $t('Verify email: message after resending', 'A new verification link has been sent.'),
            'verify.resend' => $t('Verify email: resend button', 'Resend verification email'),
            'verify.sign_out' => $t('Verify email: sign-out button', 'Sign out'),
            'two_factor.meta.title' => $t('Two-step verification: browser tab title', 'Two-factor authentication'),
            'two_factor.title' => $t('Two-step verification: heading', 'Two-step verification'),
            'two_factor.lead' => $t('Two-step verification: introduction (code)', 'Enter the 6-digit code from your authenticator app.'),
            'two_factor.lead_recovery' => $t('Two-step verification: introduction (recovery code)', 'Enter one of your emergency recovery codes.'),
            'two_factor.code' => $t('Two-step verification: code label', 'Authentication code'),
            'two_factor.recovery' => $t('Two-step verification: recovery code label', 'Recovery code'),
            'two_factor.button' => $t('Two-step verification: button', 'Continue'),
            'two_factor.use_code' => $t('Two-step verification: switch to code link', 'Use an authentication code'),
            'two_factor.use_recovery' => $t('Two-step verification: switch to recovery code link', 'Use a recovery code'),
        ],
    ],

    'pdf' => [
        'label' => 'Paper application form (PDF)',
        'route' => 'register.form-pdf',
        'help' => 'The printable form visitors download. Course names and fees come from Courses & fees; the medical questions in section 4 come from Admissions settings. "View page" downloads the PDF.',
        'fields' => [
            'pdf.doc_title' => $t('Document title (shown by PDF viewers, before the school name)', 'Student Application Form'),
            'pdf.masthead.name' => $t('Masthead: name', 'BigRig'),
            'pdf.masthead.subtitle' => $t('Masthead: line under the name', 'INTERNATIONAL TRUCK DRIVING ACADEMY'),
            'pdf.title' => $t('Form title', 'STUDENT APPLICATION FORM'),
            'pdf.note' => $t('Instruction under the title', 'Please complete in BLOCK LETTERS. Fields marked * are required.'),
            'pdf.yes' => $t('Tick box option: Yes', 'Yes'),
            'pdf.no' => $t('Tick box option: No', 'No'),
            'pdf.other' => $t('Tick box option: Other (followed by a blank line)', 'Other:'),
            'pdf.footer.page' => $t('Footer: page number (:page and :pages are replaced by the numbers)', 'PAGE :page OF :pages'),
            'pdf.footer.1' => $t('Footer: page 1 tagline', 'SAFETY • SKILLS • OPPORTUNITY • GLOBAL CAREER'),
            'pdf.footer.2' => $t('Footer: page 2 tagline', 'PROFESSIONAL TRAINING • CERTIFIED DRIVERS • BRIGHTER FUTURES'),
            'pdf.footer.3' => $t('Footer: page 3 tagline', 'DRIVE TODAY. BUILD A BETTER TOMORROW.'),

            'pdf.s1.title' => $t('Section 1: heading', '1. PERSONAL INFORMATION'),
            'pdf.s1.first_name' => $t('Section 1: first name', 'First Name'),
            'pdf.s1.middle_name' => $t('Section 1: middle name', 'Middle Name'),
            'pdf.s1.last_name' => $t('Section 1: last name', 'Last Name'),
            'pdf.s1.photo' => $ta('Section 1: passport photo box (one word or phrase per line)', "AFFIX RECENT\nPASSPORT\nPHOTOGRAPH\nHERE"),
            'pdf.s1.dob' => $t('Section 1: date of birth', 'Date of Birth'),
            'pdf.s1.gender' => $t('Section 1: gender', 'Gender'),
            'pdf.s1.male' => $t('Section 1: gender option male', 'Male'),
            'pdf.s1.female' => $t('Section 1: gender option female', 'Female'),
            'pdf.s1.nationality' => $t('Section 1: nationality', 'Nationality'),
            'pdf.s1.nigerian' => $t('Section 1: nationality option Nigerian', 'Nigerian'),
            'pdf.s1.ecowas' => $t('Section 1: nationality option ECOWAS', 'ECOWAS'),
            'pdf.s1.nin' => $t('Section 1: NIN', 'NIN Number'),
            'pdf.s1.mobile' => $t('Section 1: mobile number', 'Mobile Number'),
            'pdf.s1.email' => $t('Section 1: email', 'Email Address'),
            'pdf.s1.address' => $t('Section 1: address', 'Residential Address'),
            'pdf.s1.city' => $t('Section 1: city', 'City'),
            'pdf.s1.state' => $t('Section 1: state', 'State'),
            'pdf.s1.lga' => $t('Section 1: local government area', 'LGA'),
            'pdf.s1.kin_name' => $t('Section 1: emergency contact name', 'Emergency Contact Name'),
            'pdf.s1.kin_relationship' => $t('Section 1: emergency contact relationship', 'Relationship'),
            'pdf.s1.kin_phone' => $t('Section 1: emergency contact phone', 'Emergency Contact Phone'),

            'pdf.s2.title' => $t('Section 2: heading', "2. DRIVER'S LICENSE INFORMATION"),
            'pdf.s2.number' => $t('Section 2: licence number', "Driver's License Number"),
            'pdf.s2.class' => $t('Section 2: licence class', 'License Class'),
            'pdf.s2.state' => $t('Section 2: state of issue', 'State of Issue'),
            'pdf.s2.issued' => $t('Section 2: issue date', 'Issue Date'),
            'pdf.s2.expiry' => $t('Section 2: expiry date', 'Expiration Date'),
            'pdf.s2.experience' => $t('Section 2: driving experience', 'Years / Months of Driving Experience'),
            'pdf.s2.valid' => $t('Section 2: valid licence question', 'Valid license?'),
            'pdf.s2.copy' => $t('Section 2: copy attached question', 'Copy attached?'),
            'pdf.s2.manual' => $t('Section 2: manual transmission question', 'Manual transmission?'),

            'pdf.s3.title' => $t('Section 3: heading', '3. DRIVING HISTORY'),
            'pdf.s3.q1' => $t('Section 3: question 1', 'License ever suspended or revoked?'),
            'pdf.s3.q2' => $t('Section 3: question 2', 'Serious road accident?'),
            'pdf.s3.q3' => $t('Section 3: question 3', 'Major traffic offense?'),
            'pdf.s3.q4' => $t('Section 3: question 4', 'Previous commercial/truck driving experience?'),
            'pdf.s3.explain' => $t('Section 3: explanation lines', 'If yes, explain:'),

            'pdf.s4.title' => $t('Section 4: heading (the questions come from Admissions settings)', '4. MEDICAL & FITNESS DECLARATION'),

            'pdf.s5.title' => $t('Section 5: heading', '5. CRIMINAL HISTORY DECLARATION'),
            'pdf.s5.question' => $ta('Section 5: question', "Have you ever been convicted of an offense that may make you ineligible under the Academy's published admission requirements?"),
            'pdf.s5.details' => $t('Section 5: details line', 'Details:'),

            'pdf.s6.title' => $t('Section 6: heading', '6. TRAINING OBJECTIVE(S)'),
            'pdf.s6.note' => $t('Section 6: instruction', 'Tick at least one training objective. Current academy fees are shown below.'),
            'pdf.s6.program' => $t('Section 6: "Program" column heading', 'PROGRAM'),
            'pdf.s6.duration' => $t('Section 6: "Duration" column heading', 'DURATION'),
            'pdf.s6.price' => $t('Section 6: "Price" column heading', 'PRICE'),
            'pdf.s6.weeks' => $t('Section 6: course length (:weeks is replaced by the number of weeks)', ':weeks Weeks'),
            'pdf.s6.discount' => $t('Section 6: word under a combo saving', 'DISCOUNT'),
            'pdf.s6.vat_note' => $t('Section 6: VAT note (:vat is replaced by the VAT rate)', 'Fees exclude VAT at :vat%, which is added to the total due.'),

            'pdf.s7.title' => $t('Section 7: heading', '7. ENROLLMENT PREFERENCES'),
            'pdf.s7.start' => $t('Section 7: preferred start date', 'Preferred Start Date'),
            'pdf.s7.location' => $t('Section 7: training location', 'Training Location'),
            'pdf.s7.campus' => $t('Section 7: main campus option', 'Ibadan (Main Campus)'),
            'pdf.s7.class' => $t('Section 7: class preference', 'Class Preference'),
            'pdf.s7.full_time' => $t('Section 7: option full-time', 'Full-Time'),
            'pdf.s7.part_time' => $t('Section 7: option part-time', 'Part-Time'),
            'pdf.s7.morning' => $t('Section 7: option morning', 'Morning'),
            'pdf.s7.afternoon' => $t('Section 7: option afternoon', 'Afternoon'),
            'pdf.s7.weekend' => $t('Section 7: option weekend', 'Weekend'),

            'pdf.s8.title' => $t('Section 8: heading', '8. REQUIRED DOCUMENT CHECKLIST'),
            'pdf.s8.1' => $t('Section 8: document 1', 'Passport Photograph'),
            'pdf.s8.2' => $t('Section 8: document 2', "Valid Driver's License (copy)"),
            'pdf.s8.3' => $t('Section 8: document 3', 'NIN Slip (copy)'),
            'pdf.s8.4' => $t('Section 8: document 4', 'Valid ID / Passport (copy)'),
            'pdf.s8.5' => $t('Section 8: document 5', 'Medical Fitness Certificate'),
            'pdf.s8.6' => $t('Section 8: document 6', 'Vision Test Result'),
            'pdf.s8.7' => $t('Section 8: document 7', 'Drug Screening Result'),
            'pdf.s8.8' => $t('Section 8: document 8', 'Proof of Payment'),

            'pdf.s9.title' => $t('Section 9: heading', '9. PAYMENT INFORMATION'),
            'pdf.s9.registration' => $t('Section 9: registration fee row', 'Registration / Application Fee'),
            'pdf.s9.training' => $t('Section 9: training fee row', 'Training Fee (as selected)'),
            'pdf.s9.discount' => $t('Section 9: discount row', 'Discount (if applicable)'),
            'pdf.s9.vat' => $t('Section 9: VAT row (:vat is replaced by the VAT rate)', 'VAT (:vat%)'),
            'pdf.s9.total' => $t('Section 9: total row', 'Total Due'),
            'pdf.s9.paid' => $t('Section 9: amount paid row', 'Amount Paid'),
            'pdf.s9.balance' => $t('Section 9: balance row', 'Outstanding Balance'),
            'pdf.s9.method' => $t('Section 9: payment method row', 'Payment Method'),
            'pdf.s9.cash' => $t('Section 9: method option cash', 'Cash'),
            'pdf.s9.transfer' => $t('Section 9: method option bank transfer', 'Bank Transfer'),
            'pdf.s9.pos' => $t('Section 9: method option POS', 'POS'),
            'pdf.s9.method_other' => $t('Section 9: method option other', 'Other'),
            'pdf.s9.plan' => $t('Section 9: payment plan row', 'Payment Plan'),
            'pdf.s9.full' => $t('Section 9: plan option full payment', 'Full Payment'),
            'pdf.s9.installment' => $t('Section 9: plan option instalments', 'Installment'),
            'pdf.s9.receipt' => $t('Section 9: receipt number row', 'Receipt / Transaction Number'),
            'pdf.s9.next_due' => $t('Section 9: next payment date row', 'Next Payment Due Date'),

            'pdf.s10.title' => $t('Section 10: heading', '10. STUDENT AGREEMENT & DECLARATION'),
            'pdf.s10.declaration' => $ta('Section 10: declaration text', "I declare that the information provided in this application is true and complete to the best of my knowledge. I understand that false or misleading information may result in rejection or cancellation of enrollment. I acknowledge the Academy's attendance, safety, testing, drug/alcohol, tuition/payment, refund, conduct, dismissal, and completion requirements, and I agree to follow instructor directions and safety procedures during classroom and practical training."),
            'pdf.s10.name' => $t("Section 10: applicant's name", "Applicant's Full Name"),
            'pdf.s10.signature' => $t("Section 10: applicant's signature", "Applicant's Signature"),
            'pdf.s10.date' => $t('Section 10: date', 'Date'),

            'pdf.s11.title' => $t('Section 11: heading', '11. FOR OFFICE USE ONLY'),
            'pdf.s11.status' => $t('Section 11: application status', 'Application Status'),
            'pdf.s11.approved' => $t('Section 11: status option approved', 'Approved'),
            'pdf.s11.conditional' => $t('Section 11: status option conditional', 'Conditional'),
            'pdf.s11.rejected' => $t('Section 11: status option rejected', 'Rejected'),
            'pdf.s11.student_id' => $t('Section 11: student ID', 'Student ID Number'),
            'pdf.s11.batch' => $t('Section 11: class / batch', 'Class / Batch Number'),
            'pdf.s11.instructor' => $t('Section 11: instructor', 'Assigned Instructor'),
            'pdf.s11.start' => $t('Section 11: start date', 'Training Start Date'),
            'pdf.s11.completion' => $t('Section 11: completion date', 'Expected Completion Date'),
            'pdf.s11.received_by' => $t('Section 11: received by', 'Received By'),
            'pdf.s11.received_on' => $t('Section 11: date received', 'Date Received'),
            'pdf.s11.reference' => $t('Section 11: payment reference', 'Payment Reference'),
            'pdf.s11.officer' => $t('Section 11: officer name', 'Authorized Officer Name'),
            'pdf.s11.signature' => $t('Section 11: signature', 'Signature'),
            'pdf.s11.date' => $t('Section 11: date', 'Date'),
        ],
    ],

];

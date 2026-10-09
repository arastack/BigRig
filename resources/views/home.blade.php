@extends('layouts.main')

@section('title', 'Home') <!-- Sets the title for the page -->

@section('content')

    <!-- banner-section -->
    <section class="banner-section">
        <div class="banner-carousel owl-theme owl-carousel owl-nav-none">
            @foreach ($sliders as $slider)
                <div class="slide-item">
                    <div class="shape">
                        <div class="shape-1" style="background-image: url({{asset('assets/images/shape/shape-2.png')}});"></div>
                        <div class="shape-2" style="background-image: url({{asset('assets/images/shape/shape-3.png')}});"></div>
                        <div class="shape-3" style="background-image: url({{asset('assets/images/shape/shape-4.png')}});"></div>
                        <div class="shape-4" style="background-image: url({{asset('assets/images/shape/shape-5.png')}});"></div>
                        <div class="shape-5" style="background-image: url({{asset('assets/images/shape/shape-6.png')}});"></div>
                        <div class="shape-6" style="background-image: url({{asset('assets/images/shape/shape-7.png')}});"></div>
                        <div class="shape-7" style="background-image: url({{asset('assets/images/shape/shape-8.png')}});"></div>
                        <div class="shape-8" style="background-image: url({{asset('assets/images/shape/shape-9.png')}});"></div>
                    </div>
                    <div class="curve-text">
                        <div class="icon-box"><img src="{{asset('assets/images/icons/icon-2.png')}}" alt=""></div>
                        <span class="curved-circle">BigRig Truck Driving School – because you all need it –</span>
                        <h6><a href="{{ route('register') }}">Join Now</a></h6>
                    </div>
                    <div class="outer-container clearfix">
                        <div class="content-inner pull-left">
                            <div class="content-box">
                                <h2>{{ $slider-> title }}</h2>
                                <p>{{ $slider->text }} </p>
                                @if ($slider->button_name)
                                    <div class="btn-box">
                                        <a href="{{ $slider->button_url }}" class="theme-btn btn-two">{{ $slider->button_name }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="image-box pull-right">
                            <figure class="image overlay-layer">
                                <img class="slider-image" src="{{ Storage::url($slider->image_url_2) }}" alt="{{ $slider->title }}">
                            </figure>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <!-- banner-section end -->


    <!-- about-section -->
    <section class="about-section">
        <div class="auto-container">
            <div class="row clearfix">
                <!-- Determine column width dynamically -->
                <div class="{{ optional($founder)->show_founder ? 'col-lg-8' : 'col-lg-12' }} col-md-12 col-sm-12 big-column">
                    <div class="inner-box">
                        <div class="title-text">
                            <h2>{{ optional($founder)->speech_title }}</h2>
                        </div>
                        <div class="row clearfix">
                            <!-- Image Column -->
                            <div class="{{ optional($founder)->show_founder ? 'col-lg-6' : 'col-lg-4' }} col-md-6 col-sm-12 image-column">
                                <div class="image-box">
                                    <figure class="image">
                                        <img src="{{ optional($founder)->secondary_picture ? Storage::url($founder->secondary_picture) : '' }}" alt="">
                                    </figure>
                                </div>
                            </div>
    
                            <!-- Content Column -->
                            <div class="{{ optional($founder)->show_founder ? 'col-lg-6' : 'col-lg-8' }} col-md-6 col-sm-12 content-column">
                                <div class="content-box">
                                    <h6>Since</h6>
                                    <h2>{{ $settings->commence_year }}</h2>
                                    <div class="text">
                                        <p>{!! clean_html(optional($founder)->speech_content) !!}</p>
                                    </div>
    
                                    @if (optional($founder)->show_founder)
                                        <!-- Founder Details -->
                                        <div class="inner">
                                            <figure class="signature">
                                                <img src="{{ optional($founder)->signature ? Storage::url($founder->signature) : '' }}" alt="">
                                            </figure>
                                            <div class="author">
                                                <h4>{{ optional($founder)->founder_name }}</h4>
                                                <span class="designation">Founder</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                @if (optional($founder)->show_founder)
                    <!-- Founder Image & Social Links -->
                    <div class="col-lg-4 col-md-12 col-sm-12 small-column">
                        <div class="image-box">
                            <div class="shape" style="background-image: url({{ asset('assets/images/shape/shape-11.png') }});"></div>
                            <figure class="image">
                                <img src="{{ Storage::url($founder->founder_picture) }}" alt="">
                            </figure>
                            <div class="social-links">
                                <h6>Follow Me On</h6>
                                <ul class="social-list clearfix">
                                    @foreach (['facebook' => 'facebook-f', 'twitter' => 'twitter', 'linkedin' => 'linkedin-in', 'instagram' => 'instagram'] as $platform => $icon)
                                        @if (!empty($founder->{$platform . '_handle'}))
                                            <li>
                                                <a href="{{ optional($founder)->{$platform . '_handle'} }}" target="_blank">
                                                    <i class="fab fa-{{ $icon }}"></i>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
    
    <!-- about-section end -->


    <!-- service-section -->
    <section class="service-section">
        <div class="shape">
            <div class="shape-1 float-bob-x" style="background-image: url({{asset('assets/images/shape/shape-12.png')}});"></div>
            <div class="shape-2 float-bob-x" style="background-image: url({{asset('assets/images/shape/shape-12.png')}});"></div>
        </div>
        <div class="auto-container">
            <div class="sec-title light centred">
                <h2>Services customized for you</h2>
            </div>
            <div class="three-item-carousel owl-carousel owl-theme">
                @foreach ($services as $service)
                    <div class="service-block-one">
                        <div class="inner-box">
                            <div class="image-box">
                                <div class="icon-box"><img src="{{asset('assets/images/icons/icon-3.png')}}" alt=""></div>
                                <figure class="image"><img class="custom-service-image" src="{{ Storage::url($service->service_picture) }}" alt=""></figure>
                            </div>
                            <div class="lower-content">
                                <div class="text">
                                    {{-- <h4><a href="javascript:void(0)">{{ $service->service_name }}</a></h4> --}}
                                    <p>{{ $service->service_description }}</p>
                                </div>
                                <div class="lower-box">
                                    <ul class="arrow-icon clearfix">
                                        <li><i class="flaticon-right-arrow-1"></i></li>
                                        {{-- <li><i class="flaticon-right-arrow-1"></i></li>
                                        <li><i class="flaticon-right-arrow-1"></i></li>
                                        <li><i class="flaticon-right-arrow-1"></i></li>
                                        <li><i class="flaticon-right-arrow-1"></i></li> --}}
                                    </ul>
                                    <div class="link">
                                        <a href="javascript:void(0)"><i class="flaticon-right-arrow-1"></i>{{ $service->service_name }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- service-section end -->


    <!-- highlights-section -->
    <section class="highlights-section">
        <div class="outer-container">
            <div class="highlights-carousel owl-carousel owl-theme owl-dots-none owl-nav-none">
                @foreach ($partners as $partner)
                    <div class="single-item">
                        <h4><i class="flaticon-right-arrow-2"></i>{{ $partner->name }}</h4>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- highlights-section end -->


    <!-- find-section -->
    <section class="find-section">
        <div class="bg-layer" style="background-image: url({{asset('assets/images/background/find-bg.jpg')}});"></div>
        <div class="upper-box">
            <div class="bg-color"></div>
            <div class="shape">
                <div class="shape-1" style="background-image: url({{asset('assets/images/shape/shape-16.png')}});"></div>
                <div class="shape-2" style="background-image: url({{asset('assets/images/shape/shape-17.png')}});"></div>
                <div class="shape-3" style="background-image: url({{asset('assets/images/shape/shape-18.png')}});"></div>
            </div>
            <div class="auto-container">
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-12 col-sm-12 content-column">
                        <div class="content-box">
                            <div class="sec-title">
                                <h2>{{ $about->pass_rate }}% of students <br />pass the driving test <br />on first try</h2>
                            </div>
                            <p>Subscribe to our newsletter.</p>
                            <form action="{{ route('email-subscription') }}" method="post">
                            @csrf
                                <div class="form-group">
                                    <input type="text" name="email" placeholder="Email*" required="">
                                    <button type="submit" class="theme-btn btn-two">Subscribe</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lower-box">
            <div class="bg-color"></div>
            <div class="clients-carousel owl-carousel owl-theme owl-dots-none owl-nav-none">
                @foreach ($clients as $client)
                    <figure class="clients-logo p-4">
                        <a href="javascript:void(0)">
                            <img class="client-logo-image" src="{{ Storage::url($client->logo) }}" alt="">
                        </a>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>
    <!-- find-section end -->


    <!-- process-section -->
    <section class="process-section">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="col-lg-6 col-md-12 col-sm-12 left-column">
                    <div class="inner-box">
                        <div class="shape" style="background-image: url({{asset('assets/images/shape/shape-19.png')}});"></div>
                        <div class="sec-title">
                            <h2>Start the process</h2>
                            <p>Review our range of courses and choose the program that best fits your driving career goals, whether it’s commercial driving, licensing preparation, or specialized training.</p>
                        </div>
                        <div class="image-box">
                            <figure class="image"><img src="{{asset('assets/images/resource/video-1.jpg')}}" alt=""></figure>
                            <div class="video-btn">
                                <a href="https://www.youtube.com/watch?v=nfP5N9Yc72A&amp;t=28s" class="lightbox-image" data-caption=""><i class="flaticon-play-button-1"></i></a>
                            </div>
                        </div> 
                        <div class="single-item">
                            <div class="icon"><img src="{{asset('assets/images/icons/icon-8.png')}}" alt=""></div>
                            <h3>Get Your First <br />Free Online Lesson Today…</h3>
                            <p>Schedule your class as your convenient.<a href="{{route('contact')}}"><i class="flaticon-right-arrow-1"></i>Schedule Now</a></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 right-column">
                    <div class="inner-box">
                        <div class="single-item">
                            <div class="arrow-shape" style="background-image: url({{asset('assets/images/shape/shape-20.png')}});"></div>
                            <span class="count-text">01</span>
                            <div class="icon"><img src="{{asset('assets/images/icons/icon-9.png')}}" alt=""></div>
                            <h4><a href="{{ route('index') }}">Consultation</a></h4>
                            <p>Meet with our advisors to customize your learning path and address any questions before training begins.</p>
                        </div>
                        <div class="single-item">
                            <div class="arrow-shape-2" style="background-image: url({{asset('assets/images/shape/shape-21.png')}});"></div>
                            <span class="count-text">02</span>
                            <div class="icon"><img src="{{asset('assets/images/icons/icon-10.png')}}" alt=""></div>
                            <h4><a href="{{ route('index') }}">Buy Your Course</a></h4>
                            <p>Enroll in your chosen courses and secure your spot on the path to becoming a professional driver.</p>
                        </div>
                        <div class="single-item">
                            <div class="arrow-shape" style="background-image: url({{asset('assets/images/shape/shape-20.png')}});"></div>
                            <span class="count-text">03</span>
                            <div class="icon"><img src="{{asset('assets/images/icons/icon-11.png')}}" alt=""></div>
                            <h4><a href="{{ route('index') }}">Start Your Training</a></h4>
                            <p>Begin hands-on training with expert instructors to build essential skills for real-world driving success.</p>
                        </div>
                        <div class="btn-box">
                            <a href="{{route('register')}}" class="theme-btn btn-two">Get Started</a> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- process-section end -->


    <!-- pricing-section -->
    <section class="pricing-section">
        <div class="auto-container">
            <div class="sec-title centred">
                <h2>Valuable Packages & Offers</h2>
            </div>
            <div class="row clearfix">
                @foreach ($objectives as $objective)
                    @php
                        $maxPrice = $objectives->max('price');
                    @endphp
                    <div class="col-lg-4 col-md-6 mb-5 col-sm-12 pricing-block">
                        <div class="mb-5 pricing-block-one @if($objective->price == $maxPrice) active-block @endif wow fadeInUp animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                            <div class="pricing-table">
                                <div class="table-header">
                                    @php
                                        $price = number_format($objective->price, 2); // Ensure two decimal places
                                        [$dollars, $cents] = explode('.', $price); // Split into dollars and cents
                                    @endphp
                                    @if ($objective->price == $maxPrice)
                                        <span class="popular">popular</span>
                                    @endif
                                    <h2>
                                        <span class="symble"></span>{{ $settings->base_currency }}{{ $dollars }}
                                        <span class="text">.{{ $cents }}</span>
                                        <span class="course">per Course</span>
                                    </h2>                                
                                </div>
                                <div class="package-box">
                                    @php
                                        $objectiveParts = explode(' ', $objective->objective, 2); // Split the string into two parts
                                        $firstWord = $objectiveParts[0]; // First word
                                        $remainingWords = $objectiveParts[1] ?? ''; // Remaining words, if any
                                    @endphp

                                    <h4>{{ $firstWord }}</h4>
                                    <p>{{ $remainingWords }}</p>
                                </div>
                                <div class="table-content">
                                    <ul class="feature-list clearfix">
                                        <li>Duration<span>{{ $objective->duration }} Weeks</span></li>
                                        <li>Theory<span>{{ $objective->theory_session }} Hours</span></li>
                                        <li>Practical<span>{{ $objective->practical_session }} Hours</span></li>
                                        <li>Certificate<span>Yes</span></li>
                                    </ul>
                                    <div class="btn-box">
                                        <a href="{{ route('register') }}" class="theme-btn btn-two">Enrol Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- pricing-section end -->


    <!-- highlights-style-two -->
    <section class="highlights-style-two">
        <div class="bg-layer" style="background-image: url({{asset('assets/images/background/highlights-bg.png')}});"></div>
        <div class="shape">
            <div class="shape-1" style="background-image: url({{asset('assets/images/shape/shape-22.png')}});"></div>
            <div class="shape-2 float-bob-x" style="background-image: url({{asset('assets/images/shape/shape-12.png')}});"></div>
        </div>
        <div class="auto-container">
            <div class="sec-title light">
                <h2>Here to help you become a great driver</h2>
                <p>We are committed to equipping you with the skills, knowledge, and confidence needed to excel on the road. Our proven training approach combines practical experience with industry insights to make you a safe and skilled driver, ready for any driving environment.</p>
            </div>
            <div class="row clearfix">
                <div class="col-lg-6 col-md-6 col-sm-12 single-column">
                    <div class="single-item wow fadeInLeft animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                        <div class="inner">
                            <ul class="icon-list">
                                <li><i class="flaticon-right-arrow-1"></i></li>
                                <li><i class="flaticon-right-arrow-2"></i></li>
                                <li><i class="flaticon-right-arrow-1"></i></li>
                            </ul>
                            <h4><a href="javascript:void(0)">Approved Institute</a></h4>
                            <p>Certified and recognized for delivering high-quality driver education programs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 single-column">
                    <div class="single-item wow fadeInRight animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                        <div class="inner">
                            <ul class="icon-list">
                                <li><i class="flaticon-right-arrow-1"></i></li>
                                <li><i class="flaticon-right-arrow-2"></i></li>
                                <li><i class="flaticon-right-arrow-1"></i></li>
                            </ul>
                            <h4><a href="javascript:void(0)">Experienced & Trusted</a></h4>
                            <p>A reputable school with a proven track record in driver training excellence.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 single-column">
                    <div class="single-item wow fadeInLeft animated" data-wow-delay="300ms" data-wow-duration="1500ms">
                        <div class="inner">
                            <ul class="icon-list">
                                <li><i class="flaticon-right-arrow-1"></i></li>
                                <li><i class="flaticon-right-arrow-2"></i></li>
                                <li><i class="flaticon-right-arrow-1"></i></li>
                            </ul>
                            <h4><a href="javascript:void(0)">Modern Techniques</a></h4>
                            <p>Advanced training methods for efficient, effective, and practical learning experiences.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 single-column">
                    <div class="single-item wow fadeInRight animated" data-wow-delay="300ms" data-wow-duration="1500ms">
                        <div class="inner">
                            <ul class="icon-list">
                                <li><i class="flaticon-right-arrow-1"></i></li>
                                <li><i class="flaticon-right-arrow-2"></i></li>
                                <li><i class="flaticon-right-arrow-1"></i></li>
                            </ul>
                            <h4><a href="javascript:void(0)">Trained Instructors</a></h4>
                            <p>Highly qualified instructors dedicated to developing safe, skilled drivers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- highlights-style-two end -->


    <!-- advanced-section -->
    <section class="advanced-section">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="col-lg-4 col-md-6 col-sm-12 title-column">
                    <div class="title-inner">
                        <div class="sec-title">
                            <h2>Get yourself<br />familiarized with traffic rules</h2>
                        </div>
                        <div class="download-box">
                            <div class="icon"><i class="flaticon-download"></i></div>
                            <h4><a href="{{ Storage::url('miscellaneous/traffic_rules.pdf') }}" download>Download</a></h4>
                            <h5>pdf.3mb</h5>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 image-column">
                    <div class="image-box">
                        <div class="shape" style="background-image: url({{asset('assets/images/shape/shape-23.png')}});"></div>
                        <figure class="image"><img src="{{asset('assets/images/resource/car-1.png')}}" alt=""></figure>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 text-column">
                    <div class="text-inner">
                        <p>Our advanced course is crafted to elevate your driving skills, offering in-depth training and expert insights to prepare you for professional-level driving and complex road scenarios.</p>
                        <ul class="list-style-one clearfix">
                            <li>Responsibilities as a Driver</li>
                            <li>Vehicle Controls</li>
                            <li>Traffics Signs & Control Devices</li>
                            <li>Rules of the Road</li>
                            <li>Encountering other Road Users</li>
                            <li>Driving in Dangerous Condition</li>
                            <li>Incase of Accident</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- advanced-section end -->


    <!-- testimonial-section -->
    <section class="testimonial-section">
        <div class="bg-layer" style="background-image: url({{asset('assets/images/background/testimonial-bg.jpg')}});"></div>
        <div class="pattern-layer float-bob-x" style="background-image: url({{asset('assets/images/shape/shape-5.png')}});"></div>
        <div class="auto-container">
            <div class="row clearfix">
                <div class="col-lg-4 col-md-12 col-sm-12 title-column">
                    <div class="title-inner">
                        <div class="sec-title light">
                            <h2>Reviews & Testimonials</h2>
                            {{-- <p>Denounce with righteous indignation & dislike men who are so beguiled demoralized.</p> --}}
                        </div>
                        <div class="rating-box">
                            <h4>Excellent</h4>
                            <ul class="rating clearfix">
                                <li><i class="flaticon-star"></i></li>
                                <li><i class="flaticon-star"></i></li>
                                <li><i class="flaticon-star"></i></li>
                                <li><i class="flaticon-star"></i></li>
                                <li><i class="flaticon-star-half-empty"></i></li>
                            </ul>
                        </div>
                        {{-- <div class="review-box">
                            <p>Trust Score 4.5 (Based on 2,500 reviews)</p>
                            <a href="{{ route('index') }}" class="theme-btn btn-two">all Reviews</a>
                        </div> --}}
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-sm-12 testimonial-column">
                    <div class="testimonial-content">
                        <div class="row clearfix">
                            @foreach ($testimonials as $testimonial)
                                <div class="col-lg-6 col-md-6 col-sm-12 testimonial-block">
                                    <div class="testimonil-block-one">
                                        <div class="inner-box">
                                            <div class="border-box"></div>
                                            <div class="bg-shape"></div>
                                            <ul class="rating clearfix">
                                                @for ($i = 1; $i <= floor($testimonial->rating); $i++)
                                                    <li><i class="flaticon-star"></i></li>
                                                @endfor
                                            
                                                {{-- @if ($testimonial->rating - floor($testimonial->rating) >= 0.5)
                                                    <li><i class="flaticon-star-half-empty"></i></li>
                                                @endif
                                            
                                                @for ($i = ceil($testimonial->rating); $i < 5; $i++)
                                                    <li><i class="flaticon-star-empty"></i></li>
                                                @endfor --}}
                                            </ul>
                                            {{-- <h4>This is the Best Driving <br />School in City.</h4> --}}
                                            <p>{!! clean_html($testimonial->testimony) !!}</p>
                                            <div class="author-box">
                                                <figure class="author-thumb"><img src="{{ $testimonial->image_url ? Storage::url($testimonial->image_url) : Storage::url('users/avatar.png') }}" width="40" height="40" alt="{{ $testimonial->full_name }}"></figure>
                                                <h4>{{ $testimonial->full_name }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- testimonial-section end -->


    <!-- newsletter-section -->
    <section class="newsletter-section">
        <div class="shape">
            <div class="shape-1" style="background-image: url({{asset('assets/images/shape/shape-24.png')}});"></div>
            <div class="shape-2" style="background-image: url({{asset('assets/images/shape/shape-25.png')}});"></div>
            <div class="shape-3" style="background-image: url({{asset('assets/images/shape/shape-5.png')}});"></div>
            <div class="shape-4" style="background-image: url({{asset('assets/images/shape/shape-26.png')}});"></div>
            <div class="shape-5" style="background-image: url({{asset('assets/images/shape/shape-27.png')}});"></div>
            <div class="shape-6" style="background-image: url({{asset('assets/images/shape/shape-28.png')}});"></div>
            <div class="shape-7" style="background-image: url({{asset('assets/images/shape/shape-29.png')}});"></div>
            <div class="shape-8"></div>
            <div class="shape-9" style="background-image: url({{asset('assets/images/shape/shape-30.png')}});"></div>
        </div>
        <div class="auto-container">
            {{-- <span class="big-text">BigRig</span> --}}
            <div class="row clearfix">
                <div class="col-lg-6 col-md-12 col-sm-12 single-column">
                    <div class="single-item">
                        <figure class="image"><img src="{{asset('assets/images/resource/newsletter-1.jpg')}}" alt=""></figure>
                        <div class="content-box">
                            <div class="icon-box"><i class="flaticon-traffic-lights"></i></div>
                            <h2>Learn <br />Traffic Signs</h2>
                            <p>Follow the rules while you drive.</p>
                            <div class="download-box">
                                <h6>Download.pdf <a href="{{ Storage::url('miscellaneous/traffic_rules.pdf') }}" download><i class="flaticon-download"></i></a></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 single-column">
                    <div class="single-item">
                        <figure class="image"><img src="{{asset('assets/images/resource/newsletter-2.jpg')}}" alt=""></figure>
                        <div class="content-box">
                            <div class="icon-box"><i class="flaticon-email"></i></div>
                            <h2>Don’t <br />Miss Anything</h2>
                            <p>Subscribe to get offers & updates.</p>
                            <div class="subscribe-form">
                                <form action="{{ route('email-subscription') }}" method="post">
                                    <div class="form-group">
                                        <input type="email" name="email" placeholder="Email Address*" required="">
                                        <button type="submit"><i class="flaticon-send"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- newsletter-section end -->

    <!-- Include footer -->
    {{-- @include('partials.footer') --}}

@endsection

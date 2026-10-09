@extends('layouts.main')

@section('title', 'Home') <!-- Sets the title for the page -->

@section('content')

    <!-- banner-section -->
    <section class="banner-style-two">
        <div class="shape" style="background-image: url({{ asset('assets/images/shape/shape-50.png') }});"></div>
        <div class="banner-carousel owl-theme owl-carousel">
            @foreach ($sliders as $slider)
                <div class="slide-item">
                    <div class="image-layer" style="background-image:url({{ asset('assets/images/banner/banner-1.jpg') }})"></div>
                    <figure class="banner-img"><img src="{{ Storage::url($slider->image_url) }}" alt="{{ $slider->title }}"></figure>
                    <div class="auto-container">
                        <div class="content-box">
                            <h2>{{ $slider->title }}</h2>
                            <p>{{ $slider->text }}</p>
                            @if ($slider->button_name)
                                <div class="btn-box">
                                    <a href="{{ $slider->button_url }}" class="theme-btn btn-two">{{ $slider->button_name }}</a>
                                </div>
                            @endif
                            <div class="curve-text">
                                <div class="icon-box"><i class="flaticon-driving-school"></i></div>
                                <span class="curved-circle">BigRig Truck Driving School – because you all need it –</span>
                                <h6><a href="{{ route('register') }}">Join Now</a></h6>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    <!-- banner-section end -->

    <!-- service-style-two -->
    <section class="service-section service-section-2">
        <div class="shape">
            <div class="shape-1 float-bob-x" style="background-image: url({{asset('assets/images/shape/shape-12.png')}});"></div>
            <div class="shape-2 float-bob-x" style="background-image: url({{asset('assets/images/shape/shape-12.png')}});"></div>
        </div>
        <div class="auto-container">
            <div class="sec-title light centred">
                <h2 class="text-black">Services customized for you</h2>
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
    <!-- service-style-two end -->

    <!-- course-style-three -->
    <section class="course-style-three">
        <div class="shape">
            <div class="shape-1" style="background-image: url({{ asset('assets/images/shape/shape-51.png') }});"></div>
            <div class="shape-2 float-bob-x" style="background-image: url({{ asset('assets/images/shape/shape-52.png') }});"></div>
        </div>
        <div class="auto-container">
            <div class="sec-title-two">
                <h5>Courses</h5>
                <h2>Training Offered</h2>
            </div>
            <div class="two-item-carousel owl-carousel owl-theme owl-dots-none">
                @foreach ($objectives as $course)
                    <div class="course-block-one">
                        <div class="inner-box">
                            <div class="image-box">
                                <div class="price">
                                    @php
                                        $formattedPrice = number_format($course->price, 2, '.', ',');
                                        [$whole, $decimal] = explode('.', $formattedPrice);
                                    @endphp
                                    <h2><span class="text">From</span><span class="symble">{{ $settings->base_currency }}</span>{!! $whole !!}<sub>.{{ $decimal }}</sub></h2>
                                </div>
                                <figure class="image"><img src="{{ Storage::url($course->image_url) }}" alt="{{ $course->objective }}"></figure>
                            </div>
                            {{-- <div class="content-box">
                                <p>Course Overview</p>
                                <div class="single-box">
                                    <div class="single-item">
                                        <div class="icon-box"><img src="assets/images/icons/icon-39.png" alt=""></div>
                                        <h6>Course Duration</h6>
                                        <span>{{ $course->duration }} weeks</span>
                                    </div>
                                    <div class="single-item">
                                        <div class="icon-box"><img src="assets/images/icons/icon-40.png" alt=""></div>
                                        <h6>Theory Session</h6>
                                        <span>{{ $course->theory_session }} Hours</span>
                                    </div>
                                    <div class="single-item">
                                        <div class="icon-box"><img src="assets/images/icons/icon-40.png" alt=""></div>
                                        <h6>Practical Session</h6>
                                        <span>{{ $course->practical_session }} Hours</span>
                                    </div>
                                </div>
                                <div class="btn-box">
                                    <a href="{{ route('course', ['name' => Str::slug($course->objective)]) }}">Course Details</a>
                                </div>  
                            </div> --}}
                        </div>
                        <div class="lower-box">
                            <div class="icon-box"><img src="assets/images/icons/icon-41.png" alt=""></div>
                            <div class="icon-box-2"><img src="assets/images/icons/icon-42.png" alt=""></div>
                            <h6>{{ $settings->site_name }}</h6>
                            <h3><a href="{{ route('course', ['name' => Str::slug($course->objective)]) }}">{{ $course->objective }}</a></h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- course-style-three end -->

    <!-- ourcars-section -->
    {{-- <section class="ourcars-section">
        <div class="auto-container">
            <div class="sec-title-two centred">
                <h5>Get world-class car</h5>
                <h2>Services customized for you</h2>
                <p>Mistaken denouncing pleasure and praising pain was born and <br />we will give you complete account.</p>
            </div>
            <div class="four-item-carousel owl-carousel owl-theme">
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-2.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Manual</h6>
                                <h4><a href="index.html">Mini Cooper</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-2.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Automatic</h6>
                                <h4><a href="index.html">Toyota Corolla</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-3.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Manual</h6>
                                <h4><a href="index.html">Honda Ridgeline</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-4.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Automatic</h6>
                                <h4><a href="index.html">Mercedes-Benz</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-2.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Manual</h6>
                                <h4><a href="index.html">Mini Cooper</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-2.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Automatic</h6>
                                <h4><a href="index.html">Toyota Corolla</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-3.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Manual</h6>
                                <h4><a href="index.html">Honda Ridgeline</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-4.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Automatic</h6>
                                <h4><a href="index.html">Mercedes-Benz</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-2.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Manual</h6>
                                <h4><a href="index.html">Mini Cooper</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-2.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Automatic</h6>
                                <h4><a href="index.html">Toyota Corolla</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-3.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Manual</h6>
                                <h4><a href="index.html">Honda Ridgeline</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="car-block-one">
                    <div class="inner-box">
                        <figure class="image-box"><img src="{{ asset('assets/images/resource/car-4.jpg') }}" alt=""></figure>
                        <div class="content-box">
                            <div class="text">
                                <h6>Automatic</h6>
                                <h4><a href="index.html">Mercedes-Benz</a></h4>
                            </div>
                            <div class="lower-box">
                                <ul class="arrow-icon clearfix">
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                    <li><i class="flaticon-right-arrow-1"></i></li>
                                </ul>
                                <div class="link">
                                    <a href="service-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- ourcars-section end -->

    <!-- about-style-two -->
    <section class="about-style-two">
        <div class="bg-layer-2" style="background-image: url({{ asset('assets/images/background/about-bg-2.jpg') }});"></div>
        <div class="bg-layer-1" style="background-image: url({{ asset('assets/images/background/about-bg.jpg') }});"></div>
        <div class="pattern-layer" style="background-image: url({{ asset('assets/images/shape/shape-56.png') }});"></div>
        <div class="auto-container">
            <div class="upper-box">
                <div class="title-text">
                    <h5>Since</h5>
                    <h2>{{ $settings->commence_year }}</h2>
                </div>
                <div class="inner-box">
                    {!! clean_html($about->mission_statement) !!}
                    <div class="inner">
                        <div class="icon-box"><i class="flaticon-clock"></i></div>
                        <div class="count-outer count-box">
                            <span class="count-text" data-speed="1500" data-stop="12.6">0</span><span>k</span>
                        </div>
                        <h4>Total Training Hours</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="lower-box">
            <div class="shape">
                <div class="shape-1" style="background-image: url({{ asset('assets/images/shape/shape-54.png') }});"></div>
                <div class="shape-2" style="background-image: url({{ asset('assets/images/shape/shape-55.png') }});"></div>
            </div>
            <div class="row clearfix">
                <div class="col-lg-6 col-md-6 col-sm-12 single-column">
                    <div class="single-item">
                        <div class="icon-box"><i class="flaticon-mission"></i></div>
                        <h3>Vision Statement</h3>
                        <p>To be the leading driving school recognized for producing skilled, responsible, and safety-conscious drivers who set the standard for excellence in the transportation industry.</p>
                        {{-- <h6><i class="flaticon-right-arrow-1"></i><a href="index.html">Read More</a></h6> --}}
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 single-column">
                    <div class="single-item">
                        <div class="icon-box"><i class="flaticon-target"></i></div>
                        <h3>Smart Goal</h3>
                        <p>To equip drivers with the skills and confidence needed for safe, professional driving while promoting road safety and efficiency in the transportation industry.</p>
                        {{-- <h6><i class="flaticon-right-arrow-1"></i><a href="index.html">Read More</a></h6> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about-style-two end -->

    <!-- team-section -->
    <section class="team-section">
        <div class="auto-container">
            <div class="sec-title-two centred">
                <h5>Team Members</h5>
                <h2>Meet our expert team</h2>
            </div>
            <div class="row clearfix">
                @foreach ($instructors as $instructor)
                    <div class="col-lg-4 col-md-6 col-sm-12 team-block">
                        <div class="team-block-one">
                            <div class="inner-box">
                                <div class="image-box">
                                    <ul class="social-links clearfix">
                                        @foreach (['facebook' => 'facebook-f', 'twitter' => 'twitter', 'linkedin' => 'linkedin-in',] as $platform => $icon)
                                            @if (!empty($instructor->{$platform . '_handle'}))
                                                <li>
                                                    <a href="{{ $instructor->{$platform . '_handle'} }}" target="_blank">
                                                        <i class="fab fa-{{ $icon }}"></i>
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                    <div class="shape">
                                        <div class="shape-1" style="background-image: url({{ asset('assets/images/shape/shape-57.png') }});"></div>
                                        <div class="shape-2" style="background-image: url({{ asset('assets/images/shape/shape-58.png') }});"></div>
                                        <div class="shape-3" style="background-image: url({{ asset('assets/images/shape/shape-59.png') }});"></div>
                                    </div>
                                    <span class="text">{{ $settings->site_name }}</span>
                                    <figure class="image"><img src="{{ $instructor->profile_photo_path ? Storage::url($instructor->profile_photo_path) : Storage::url('users/avatar.png') }}" width="40" height="40" alt="{{ $instructor->firstName }} {{ $instructor->lastName }}"></figure>
                                </div>
                                <div class="lower-content">
                                    <h4><a href="index.html">{{ $instructor->firstName }} {{ $instructor->lastName }}</a></h4>
                                    <span class="designation">{{ $instructor->roles->pluck('role_name')->implode(', ') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- team-section end -->

    <!-- video-section -->
    @if ($truck && $truck->video_url) 
        <section class="video-section centred">
            <div class="bg-layer" style="background-image: url({{ Storage::url($truck->video_thumbnail_url) }});"></div>
            <div class="auto-container">
                <div class="video-btn">
                    <a href="{{ $truck->video_url }}" class="lightbox-image" data-caption="">
                        <i class="flaticon-play-button-1"></i><span>Training Video</span>
                    </a>
                </div>
            </div>
        </section>
    @endif
    <!-- video-section end -->

    <!-- chooseus-section -->
    <section class="chooseus-section">
        <div class="shape">
            <div class="shape-1" style="background-image: url({{ asset('assets/images/shape/shape-60.png') }});"></div>
            <div class="shape-2" style="background-image: url({{ asset('assets/images/shape/shape-61.png') }});"></div>
        </div>
        <div class="auto-container">
            <div class="title-inner">
                <div class="row clearfix">
                    <div class="col-lg-6 col-md-6 col-sm-12 title-column">
                        <div class="sec-title-two">
                            <h5>Why Choose Us</h5>
                            <h2>Reason for choosing us</h2>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-column">
                        <div class="text">
                            <p>Mistaken denouncing pleasure and praising pain was born and we will give you complete account.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <div class="col-lg-4 col-md-6 col-sm-12 chooseus-block">
                    <div class="chooseus-block-one wow fadeInLeft animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <span class="count-text">01</span>
                            <div class="icon-box"><i class="flaticon-driver"></i></div>
                            <div class="light-icon"><img src="{{ asset('assets/images/icons/icon-18.png') }}" alt=""></div>
                            <h4><a href="index.html">Trained <br />Instructors</a></h4>
                            <p>Highly qualified instructors dedicated to developing safe, skilled drivers.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 chooseus-block">
                    <div class="chooseus-block-one wow fadeInUp animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <span class="count-text">02</span>
                            <div class="icon-box"><i class="flaticon-dollar-symbol"></i></div>
                            <div class="light-icon"><img src="{{ asset('assets/images/icons/icon-19.png') }}" alt=""></div>
                            <h4><a href="index.html">Fair <br />Pricing Plans</a></h4>
                            <p>In a free hour when our power of choice when nothing well prevents.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 chooseus-block">
                    <div class="chooseus-block-one wow fadeInRight animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <span class="count-text">03</span>
                            <div class="icon-box"><i class="flaticon-car"></i></div>
                            <div class="light-icon"><img src="{{ asset('assets/images/icons/icon-20.png') }}" alt=""></div>
                            <h4><a href="index.html">Well Maintained <br />Vehicles</a></h4>
                            <p>Advanced training methods for efficient and practical learning experiences.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 chooseus-block">
                    <div class="chooseus-block-one wow fadeInLeft animated" data-wow-delay="300ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <span class="count-text">04</span>
                            <div class="icon-box"><i class="flaticon-cone"></i></div>
                            <div class="light-icon"><img src="{{ asset('assets/images/icons/icon-21.png') }}" alt=""></div>
                            <h4><a href="index.html">Best <br />Safety Measures</a></h4>
                            <p>The claims of or the obligation business frequently occur that pleasure.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 chooseus-block">
                    <div class="chooseus-block-one wow fadeInUp animated" data-wow-delay="300ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <span class="count-text">05</span>
                            <div class="icon-box"><i class="flaticon-overtime"></i></div>
                            <div class="light-icon"><img src="{{ asset('assets/images/icons/icon-22.png') }}" alt=""></div>
                            <h4><a href="index.html">Approved <br />Institute</a></h4>
                            <p>Certified and recognized for delivering high-quality driver education programs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 chooseus-block">
                    <div class="chooseus-block-one wow fadeInRight animated" data-wow-delay="300ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <span class="count-text">06</span>
                            <div class="icon-box"><i class="flaticon-event"></i></div>
                            <div class="light-icon"><img src="{{ asset('assets/images/icons/icon-23.png') }}" alt=""></div>
                            <h4><a href="index.html">Experienced & <br />Trusted</a></h4>
                            <p>A reputable school with a proven track record in driver training excellence.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- chooseus-section end -->

    <!-- gallery-section -->
    {{-- <section class="gallery-section">
        <div class="auto-container">
            <div class="sec-title-two centred">
                <h5>Photo Gallery</h5>
                <h2>Gallery to know our work</h2>
                <p>Mistaken denouncing pleasure and praising pain was born and <br />we will give you complete account.</p>
            </div>
            <div class="row clearfix">
                <div class="col-lg-4 col-md-6 col-sm-12 gallery-block">
                    <div class="gallery-block-one">
                        <div class="inner-box">
                            <figure class="image-box"><img src="{{ asset('assets/images/gallery/gallery-1.jpg') }}" alt=""></figure>
                            <div class="text">
                                <h4><a href="index.html">Teaching <br />Driving Tips & Tricks</a></h4>
                            </div>
                            <ul class="list clearfix">
                                <li><a href="{{ asset('assets/images/gallery/gallery-1.jpg') }}" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-crossed-arrows"></i></a></li>
                                <li><a href="index.html"><i class="flaticon-share-1"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 gallery-block">
                    <div class="gallery-block-one">
                        <div class="inner-box">
                            <figure class="image-box"><img src="{{ asset('assets/images/gallery/gallery-2.jpg') }}" alt=""></figure>
                            <div class="text">
                                <h4><a href="index.html">Teaching <br />Driving Tips & Tricks</a></h4>
                            </div>
                            <ul class="list clearfix">
                                <li><a href="{{ asset('assets/images/gallery/gallery-2.jpg') }}" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-crossed-arrows"></i></a></li>
                                <li><a href="index.html"><i class="flaticon-share-1"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 gallery-block">
                    <div class="gallery-block-one">
                        <div class="inner-box">
                            <figure class="image-box"><img src="{{ asset('assets/images/gallery/gallery-3.jpg') }}" alt=""></figure>
                            <div class="text">
                                <h4><a href="index.html">Teaching <br />Driving Tips & Tricks</a></h4>
                            </div>
                            <ul class="list clearfix">
                                <li><a href="{{ asset('assets/images/gallery/gallery-3.jpg') }}" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-crossed-arrows"></i></a></li>
                                <li><a href="index.html"><i class="flaticon-share-1"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 gallery-block">
                    <div class="gallery-block-one">
                        <div class="inner-box">
                            <figure class="image-box"><img src="{{ asset('assets/images/gallery/gallery-4.jpg') }}" alt=""></figure>
                            <div class="text">
                                <h4><a href="index.html">Teaching <br />Driving Tips & Tricks</a></h4>
                            </div>
                            <ul class="list clearfix">
                                <li><a href="{{ asset('assets/images/gallery/gallery-4.jpg') }}" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-crossed-arrows"></i></a></li>
                                <li><a href="index.html"><i class="flaticon-share-1"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 gallery-block">
                    <div class="gallery-block-one">
                        <div class="inner-box">
                            <figure class="image-box"><img src="{{ asset('assets/images/gallery/gallery-5.jpg') }}" alt=""></figure>
                            <div class="text">
                                <h4><a href="index.html">Teaching <br />Driving Tips & Tricks</a></h4>
                            </div>
                            <ul class="list clearfix">
                                <li><a href="{{ asset('assets/images/gallery/gallery-5.jpg') }}" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-crossed-arrows"></i></a></li>
                                <li><a href="index.html"><i class="flaticon-share-1"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 gallery-block">
                    <div class="gallery-block-one">
                        <div class="inner-box">
                            <figure class="image-box"><img src="{{ asset('assets/images/gallery/gallery-6.jpg') }}" alt=""></figure>
                            <div class="text">
                                <h4><a href="index.html">Teaching <br />Driving Tips & Tricks</a></h4>
                            </div>
                            <ul class="list clearfix">
                                <li><a href="{{ asset('assets/images/gallery/gallery-6.jpg') }}" class="lightbox-image" data-fancybox="gallery"><i class="flaticon-crossed-arrows"></i></a></li>
                                <li><a href="index.html"><i class="flaticon-share-1"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="more-btn centred">
                <a href="index.html" class="theme-btn btn-four">View More</a>
            </div>
        </div>
    </section> --}}
    <!-- gallery-section end -->

    <!-- location-section -->
    {{-- <section class="location-section">
        <div class="auto-container">
            <div class="tabs-box">
                <div class="row clearfix">
                    <div class="col-lg-5 col-md-12 col-sm-12 content-column">
                        <div class="content-box">
                            <div class="sec-title-two">
                                <h5>Our Locations</h5>
                                <h2>Nearest locations to learn your driving course</h2>
                            </div>
                            <p>Mistaken denouncing pleasure and praising pain was born and we will give you complete account.</p>
                            <a href="index.html" class="theme-btn btn-four">View More</a>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-sm-12 location-column">
                        <div class="location-box">
                            <div class="map" style="background-image: url({{ asset('assets/images/shape/shape-62.png') }});"></div>
                            <ul class="tab-btns tab-buttons clearfix">
                                <li class="tab-btn" data-tab="#tab-1">
                                    <div class="text">
                                        <h5>New Mexico</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn" data-tab="#tab-2">
                                    <div class="text">
                                        <h5>New York</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn" data-tab="#tab-3">
                                    <div class="text">
                                        <h5>London</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn" data-tab="#tab-4">
                                    <div class="text">
                                        <h5>California</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn active-btn" data-tab="#tab-5">
                                    <div class="text">
                                        <h5>Chicago</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn" data-tab="#tab-6">
                                    <div class="text">
                                        <h5>Australia</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn" data-tab="#tab-7">
                                    <div class="text">
                                        <h5>Canada</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn" data-tab="#tab-8">
                                    <div class="text">
                                        <h5>Japan</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn" data-tab="#tab-9">
                                    <div class="text">
                                        <h5>China</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                                <li class="tab-btn" data-tab="#tab-10">
                                    <div class="text">
                                        <h5>Los Angeles</h5>
                                        <span>3 Locations</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tabs-content">
                    <div class="tab" id="tab-1">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, New Mexico USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, New Mexico USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, New Mexico USA 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab" id="tab-2">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, New York USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, New York USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, New York USA 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab" id="tab-3">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, London UK 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, London UK 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, London UK 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab" id="tab-4">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, California USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, California USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, California USA 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab active-tab" id="tab-5">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, Chicago USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, Chicago USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, Chicago USA 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab" id="tab-6">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, Australia 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, Australia 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, Australia 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab" id="tab-7">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, Canada 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, Canada 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, Canada 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab" id="tab-8">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, Japan 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, Japan 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, Japan 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab" id="tab-9">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, China 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, China 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, China 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab" id="tab-10">
                        <div class="row clearfix">
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>880 Nulla Street, Los Angeles USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>Corpus Christi College</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>11/82 Park Street, Los Angeles USA 87503.</p>
                                    </div>
                                    <h5>Near by: <span>King of Prussia Mall</span></h5>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 location-block">
                                <div class="single-location">
                                    <div class="text">
                                        <div class="icon"><i class="flaticon-pin"></i></div>
                                        <p>142 Oldham Street, Los Angeles USA 40032.</p>
                                    </div>
                                    <h5>Near by: <span>Merrol Hyde Magnet School</span></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- location-section end -->

    <!-- testimonial-style-two -->
    <section class="testimonial-style-two">
        <figure class="image-layer"><img src="{{ asset('assets/images/resource/testimonial-1.png') }}" alt=""></figure>
        <div class="shape">
            <div class="shape-1" style="background-image: url({{ asset('assets/images/shape/shape-63.png') }});"></div>
            <div class="shape-2" style="background-image: url({{ asset('assets/images/shape/shape-64.png') }});"></div>
            <div class="shape-3" style="background-image: url({{ asset('assets/images/shape/shape-65.png') }});"></div>
        </div>
        <div class="auto-container">
            <div class="row clearfix">
                <div class="col-xl-8 col-lg-12 col-md-12 offset-xl-4 content-column">
                    <div class="content-inner">
                        <div class="sec-title-two">
                            <h5>Testimonials</h5>
                            <h2>Feedback from leaners</h2>
                        </div>
                        <div class="testimonial-inner">
                            @foreach ($testimonials as $testimonial)
                                <div class="testimonial-content">
                                    <figure class="thumb-box"><img src="{{ $testimonial->image_url ? Storage::url($testimonial->image_url) : Storage::url('users/avatar.png') }}" width="60" height="60" alt="{{ $testimonial->full_name }}"></figure>
                                    <div class="inner-box">
                                        <div class="text">
                                            <div class="shape-layer"></div>
                                            <div class="quote-box"><i class="flaticon-quote"></i></div>
                                            {{-- <h4>This is the best Driving School.</h4> --}}
                                            <p>{!! clean_html($testimonial->testimony) !!}</p>
                                        </div>
                                        <div class="author-box">
                                            <h5>{{ $testimonial->full_name }}</h5>
                                            <ul class="rating clearfix">
                                                @for ($i = 1; $i <= floor($testimonial->rating); $i++)
                                                    <li><i class="flaticon-star"></i></li>
                                                @endfor
                                            </ul>
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
    <!-- testimonial-style-two end -->

    <!-- clients-section -->
    <section class="clients-section">
        <div class="auto-container">
            <div class="inner-box">
                <div class="four-item-carousel owl-carousel owl-theme owl-dots-none owl-nav-none">
                    @foreach ($clients as $client)
                        <a href="javascript:void(0)">
                            <img class="client-logo-image" src="{{ Storage::url($client->logo) }}" alt="">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- clients-section end -->

    <!-- news-style-two -->
    {{-- <section class="news-style-two">
        <div class="auto-container">
            <div class="sec-title-two centred">
                <h5>News & Updates</h5>
                <h2>Latest form our blog post</h2>
                <p>Mistaken denouncing pleasure and praising pain was born and <br />we will give you complete account.</p>
            </div>
            <div class="row clearfix">
                <div class="col-lg-3 col-md-6 col-sm-12 news-block">
                    <div class="news-block-two wow fadeInUp animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <div class="image-box">
                                <span class="category">Driving Tips</span>
                                <figure class="image"><a href="blog-details.html"><img src="{{ asset('assets/images/news/news-4.jpg') }}" alt=""></a></figure>
                            </div>
                            <div class="lower-content">
                                <div class="post-date"><h5><i class="far fa-calendar"></i>05 Mar, 2022</h5></div>
                                <h4><a href="blog-details.html">Tips that Cause You to a Far Better Driver</a></h4>
                                <div class="link"><a href="blog-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 news-block">
                    <div class="news-block-two wow fadeInUp animated" data-wow-delay="200ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <div class="image-box">
                                <span class="category">RTO Service</span>
                                <figure class="image"><a href="blog-details.html"><img src="{{ asset('assets/images/news/news-5.jpg') }}" alt=""></a></figure>
                            </div>
                            <div class="lower-content">
                                <div class="post-date"><h5><i class="far fa-calendar"></i>14 Feb, 2022</h5></div>
                                <h4><a href="blog-details.html">Need to Know About Car Parallel Parking</a></h4>
                                <div class="link"><a href="blog-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 news-block">
                    <div class="news-block-two wow fadeInUp animated" data-wow-delay="400ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <div class="image-box">
                                <span class="category">Driving test</span>
                                <figure class="image"><a href="blog-details.html"><img src="{{ asset('assets/images/news/news-6.jpg') }}" alt=""></a></figure>
                            </div>
                            <div class="lower-content">
                                <div class="post-date"><h5><i class="far fa-calendar"></i>09 Feb, 2022</h5></div>
                                <h4><a href="blog-details.html">Ten Best Roads to Practice Driving in Our City</a></h4>
                                <div class="link"><a href="blog-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 news-block">
                    <div class="news-block-two wow fadeInUp animated" data-wow-delay="600ms" data-wow-duration="1500ms">
                        <div class="inner-box">
                            <div class="image-box">
                                <span class="category">Safety Tips</span>
                                <figure class="image"><a href="blog-details.html"><img src="{{ asset('assets/images/news/news-7.jpg') }}" alt=""></a></figure>
                            </div>
                            <div class="lower-content">
                                <div class="post-date"><h5><i class="far fa-calendar"></i>26 Jan, 2022</h5></div>
                                <h4><a href="blog-details.html">Teaching Kids Safety in and Around Your Cars</a></h4>
                                <div class="link"><a href="blog-details.html"><i class="flaticon-right-arrow-1"></i>Read More</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    <!-- news-style-two end -->

    <!-- Include footer -->
    {{-- @include('partials.footer-2') --}}

@endsection

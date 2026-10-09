@extends('layouts.main')

@section('title', 'Courses') <!-- Sets the title for the page -->

@section('content')

    <!-- Page Title -->
    <section class="page-title">
        <div class="bg-layer" style="background-image: url({{asset('assets/images/background/page-title.jpg') }});"></div>
        <div class="auto-container">
            <div class="content-box">
                <h1>{{ $course->objective }} <br />Course</h1>
                <ul class="bread-crumb clearfix">
                    <li><a href="index.html">Home</a></li>
                    <li>Courses</li>
                    <li>{{ $course->objective }}</li>
                </ul>
            </div>
        </div>
    </section>
    <!-- End Page Title -->

    <!-- course-details -->
    <section class="course-details">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="col-lg-9 col-md-12 col-sm-12 content-column">
                    <div class="content-box">
                        <div class="sec-title">
                            <h2>Course details</h2>
                        </div>
                        <div class="text">
                            {!! clean_html($course->course_details) !!}
                        </div>
                        <div class="btn-box">
                            <a href="{{ route('register') }}" class="theme-btn btn-five">Enrol Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-sm-12 inner-column">
                    <div class="inner-box">
                        <div class="upper-box">
                            <div class="price-box">
                                <h6>From</h6>
                                @php
                                    $priceParts = explode('.', number_format($course->price, 2, '.', ''));
                                @endphp

                                <h2>{{ $priceParts[0] }}<sup>.{{ $priceParts[1] }}</sup><span>{{ $settings->base_currency }}</span></h2>

                                <h5>Per Person</h5>
                            </div>
                            <div class="inner">
                                <h4>Excellent</h4>
                                <ul class="rating clearfix">
                                    <li><i class="flaticon-star"></i></li>
                                    <li><i class="flaticon-star"></i></li>
                                    <li><i class="flaticon-star"></i></li>
                                    <li><i class="flaticon-star"></i></li>
                                    <li><i class="flaticon-star-half-empty"></i></li>
                                </ul>
                                <p>Trust Score 4.5 (Based on 1,200 reviews)</p>
                                <div class="feedback">
                                    <div class="icon-box"><img src="{{ asset('assets/images/icons/icon-43.png') }}" alt=""></div>
                                    <h6><a href="javascript:void(0)">Read Feedback</a></h6>
                                </div>
                            </div>
                        </div>
                        <div class="lower-box">
                            <div class="border-one"></div>
                            <div class="border-two"></div>                            
                            <div class="border-three"></div>
                            <div class="row clearfix">
                                <div class="col-lg-12 col-md-12 col-sm-12 single-column">
                                    <div class="single-item">
                                        <div class="icon-box"><img src="{{ asset('assets/images/icons/icon-39.png') }}" alt=""></div>
                                        <h6>Theory Session</h6>
                                        <span>{{ $course->theory_session }} Hours</span>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 single-column">
                                    <div class="single-item">
                                        <div class="icon-box"><img src="{{ asset('assets/images/icons/icon-40.png') }}" alt=""></div>
                                        <h6>Practical Session</h6>
                                        <span>{{ $course->practical_session }} Hours</span>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 single-column">
                                    <div class="single-item">
                                        <div class="icon-box"><img src="{{ asset('assets/images/icons/icon-44.png') }}" alt=""></div>
                                        <h6>Examination</h6>
                                        <span>{{ $course->examination }}</span>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 single-column">
                                    <div class="single-item">
                                        <div class="icon-box"><img src="{{ asset('assets/images/icons/icon-45.png') }}" alt=""></div>
                                        <h6>Certificate</h6>
                                        <span>Course Completion</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- course-details end -->

    <!-- video-section -->
    <section class="video-section centred">
        <div class="bg-layer" style="background-image: url({{ Storage::url($course->video_thumbnail_url) }});"></div>
        <div class="auto-container">
            <div class="video-btn">
                <a href="{{ $course->video_url }}" class="lightbox-image" data-caption=""><i class="flaticon-play-button-1"></i><span>Training Video</span></a>
            </div>
        </div>
    </section>
    <!-- video-section end -->

    <!-- advanced-section -->
    <section class="advanced-section">
        <div class="auto-container">
            <div class="row clearfix">
                {{-- <div class="col-lg-4 col-md-6 col-sm-12 title-column">
                    <div class="title-inner">
                        <div class="sec-title">
                            <h2>The advanced level course <br />is designed for our leaners</h2>
                        </div>
                        <div class="download-box">
                            <div class="icon"><i class="flaticon-download"></i></div>
                            <h4><a href="index.html">Download Course Content</a></h4>
                            <h5>pdf.4mb</h5>
                        </div>
                    </div>
                </div> --}}
                <div class="col-lg-4 col-md-12 col-sm-12 image-column">
                    <div class="image-box">
                        <div class="shape" style="background-image: url({{ asset('assets/images/shape/shape-88.png') }});"></div>
                        <figure class="image"><img src="{{ asset('assets/images/resource/car-1.png') }}" alt=""></figure>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12 col-sm-12 text-column">
                    <header class="flex justify-between items-start mb-2">
                        <h2 class="text-md font-semibold text-gray-800 dark:text-gray-100">Course Requirement</h2>
                    </header>
                    <p class="mb-3">For you to be eligible for enrollment for this course at BigRig International Truck Driving School:</p>
                    <div class="course-requirement text-inner">
                        {!! clean_html($course->requirement) !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- advanced-section end -->

    <!-- team-section -->
    <section class="team-section course-details">
        <div class="auto-container">
            <div class="sec-title light centred">
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

    <!-- trainers-section end -->
    <section class="trainers-section">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="col-lg-6 col-md-12 col-sm-12 image-column">
                    <div class="image-box">
                        <figure class="image"><img src="{{ asset('assets/images/resource/trainers-1.jpg') }}" alt=""></figure>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 content-column">
                    <div class="content-box">
                        <div class="sec-title">
                            <h2>Female trainers <br />for our female leaners</h2>
                        </div>
                        <div class="text">
                            <p>The master-builder of human happiness. No one rejects dislikes or avoids pleasure itself, because it is pleasure, but because those who know how too pursue pleasure rationally encounters consequences that are extremely painful. Nor again is there anyone who loves or pursues or desires to obtain pain.</p>
                            <p>Let us start your journey!.. With your comfordable.</p>
                        </div>
                        <div class="support-box">
                            <div class="icon-box"><img src="{{ asset('assets/images/icons/icon-46.png') }}" alt=""></div>
                            <h5>Dial to Drive</h5>
                            <h4><a href="tel:04488812345">{{ $settings->business_contact }}</a></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- trainers-section end -->

    <!-- Include footer -->
    {{-- @include('partials.footer') --}}

@endsection

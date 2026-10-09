<!-- sidebar cart item -->
<div class="xs-sidebar-group info-group info-sidebar">
    <div class="xs-overlay xs-bg-black"></div>
    <div class="xs-overlay xs-overlay-2 xs-bg-black"></div>
    <div class="xs-overlay xs-overlay-3 xs-bg-black"></div>
    <div class="xs-overlay xs-overlay-4 xs-bg-black"></div>
    <div class="xs-overlay xs-overlay-5 xs-bg-black"></div>
    <div class="xs-sidebar-widget">
        <div class="sidebar-widget-container">
            <div class="widget-heading">
                <a href="#" class="close-side-widget"><i class="fa fa-times"></i></a>
            </div>
            <div class="sidebar-textwidget">
                <div class="sidebar-info-contents">
                    <div class="content-inner">
                        <div class="logo">
                            <figure class="logo"><a href="{{ route('index') }}"><img class="logo-image" src="{{ Storage::url($site->light_theme_logo) }}" alt=""></a></figure>
                        </div>
                        <div class="content-box">
                            <h4>About Us</h4>
                            {!! clean_html($about->mission_statement) !!}
                            <a href="{{url('about')}}" class="theme-btn btn-one">About Us</a>
                        </div>
                        <div class="contact-info">
                            <h4>Contact Info</h4>
                            <ul>
                                <li>{{ $settings->headquarters }}</li>
                                <li><a href="tel:{{ $settings->business_contact }}">{{ $settings->business_contact }}</a></li>
                                <li><a href="mailto:{{ $settings->business_email }}">{{ $settings->business_email }}</a></li>
                            </ul>
                        </div>
                        <ul class="social-box">
                            @foreach (['facebook' => 'facebook-f', 'twitter' => 'twitter', 'youtube' => 'youtube', 'linkedin' => 'linkedin-in', 'instagram' => 'instagram'] as $platform => $icon)
                                @if (!empty($settings->{$platform . '_handle'}))
                                    <li>
                                        <a href="{{ $settings->{$platform . '_handle'} }}" target="_blank">
                                            <i class="fab fa-{{ $icon }}"></i>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END sidebar widget item -->
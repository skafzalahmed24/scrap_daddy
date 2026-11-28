    <!-- Footer -->
    <footer class="d-none d-lg-block" style="background-color: #f8f9fa; border-top: 1px solid #eeeeee; position: relative; overflow: hidden; margin-top: 0;">
        <!-- Footer Watermark -->
        <i class="fa-solid fa-recycle" style="position: absolute; right: -5%; bottom: -10%; font-size: 35rem; color: rgba(46,125,50,0.03); z-index: 0; pointer-events: none; transform: rotate(-15deg);"></i>
        
        <div class="container" style="padding-top: 60px; padding-bottom: 40px; position: relative; z-index: 1;">
            <div class="row g-4">
                <!-- Column 1: Logo & Info -->
                <div class="col-lg-4 col-md-6">
                    <a href="{{ url('/') }}" class="brand-logo mb-3 d-flex align-items-center" style="text-decoration: none;">
                        <img src="{{ $site['logo_url'] }}" alt="Scrap Daddy Logo" style="height: 60px;">
                    </a>
                    <p class="text-muted mb-4" style="font-size: 0.95rem;">
                        {{ $site['footer_about'] }}
                    </p>
                    @php
                        $waDigits = preg_replace('/\D+/', '', $site['whatsapp'] ?? '');
                        $socials = array_filter([
                            ['url' => $site['instagram'], 'icon' => 'fa-instagram', 'label' => 'Instagram'],
                            ['url' => $site['facebook'], 'icon' => 'fa-facebook-f', 'label' => 'Facebook'],
                            ['url' => $site['youtube'], 'icon' => 'fa-youtube', 'label' => 'YouTube'],
                            ['url' => $site['twitter'], 'icon' => 'fa-x-twitter', 'label' => 'X (Twitter)'],
                            ['url' => $site['linkedin'], 'icon' => 'fa-linkedin-in', 'label' => 'LinkedIn'],
                            ['url' => $waDigits ? 'https://wa.me/' . $waDigits : '', 'icon' => 'fa-whatsapp', 'label' => 'WhatsApp'],
                        ], fn ($s) => !empty($s['url']));
                    @endphp
                    @if(count($socials))
                    <div class="d-flex gap-3">
                        @foreach($socials as $s)
                            <a href="{{ $s['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $s['label'] }}" title="{{ $s['label'] }}" style="color: var(--primary-blue); font-size: 1.2rem;"><i class="fa-brands {{ $s['icon'] }}"></i></a>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Column 2: My Account / Links -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold mb-4" style="color: #000;">Quick Links</h5>
                    <ul class="list-unstyled" style="line-height: 2;">
                        <li><a href="{{ url('/') }}" class="text-muted text-decoration-none footer-link">Home</a></li>
                        <li><a href="{{ url('/#categories') }}" class="text-muted text-decoration-none footer-link">Categories</a></li>
                        <li><a href="{{ route('site.services') }}" class="text-muted text-decoration-none footer-link">Services</a></li>
                        <li><a href="{{ route('site.about') }}" class="text-muted text-decoration-none footer-link">About Us</a></li>
                        <li><a href="{{ route('site.faqs') }}" class="text-muted text-decoration-none footer-link">FAQs</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-muted text-decoration-none footer-link">Blog</a></li>
                        <li><a href="{{ route('site.contact') }}" class="text-muted text-decoration-none footer-link">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Column 3: Contact Us -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="fw-bold mb-4" style="color: #000;">Contact Us</h5>
                    <div class="mb-3">
                        <small class="text-muted d-block">Our Address</small>
                        <strong class="d-block" style="color: #000;">{{ $site['company_name'] }}</strong>
                        <span class="text-muted" style="font-size: 0.9rem;">{{ $site['address_line1'] }},@if($site['address_line2'])<br>{{ $site['address_line2'] }},@endif<br>{{ $site['city'] }}, {{ $site['state'] }}.<br>{{ $site['country'] }} - {{ $site['pincode'] }}</span>
                    </div>
                    @if($site['email'])
                    <div class="mb-3">
                        <small class="text-muted d-block">Send Mail</small>
                        <a href="mailto:{{ $site['email'] }}" class="text-decoration-none"><strong style="color: #000;">{{ $site['email'] }}</strong></a>
                    </div>
                    @endif
                    @if($site['phone'])
                    <div>
                        <small class="text-muted d-block">Call</small>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $site['phone']) }}" class="text-decoration-none"><strong style="color: #000;">{{ $site['phone'] }}</strong></a>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Bottom Bar -->
        <div style="border-top: 1px solid #e9ecef; background-color: #f8f9fa;">
            <div class="container py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <span class="text-muted" style="font-size: 0.85rem;">Copyright &copy; {{ date('Y') }}. All rights reserved @ {{ $site['company_name'] }}</span>
                    </div>
                    <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                        <a href="{{ route('page.show', 'terms-and-conditions') }}" class="text-muted text-decoration-none me-4" style="font-size: 0.85rem;">Terms & Conditions</a>
                        <a href="{{ route('page.show', 'privacy-policy') }}" class="text-muted text-decoration-none" style="font-size: 0.85rem;">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>

        
    </footer>

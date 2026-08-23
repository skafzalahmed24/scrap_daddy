@extends('layouts.app')

@section('title', 'FAQs - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .faq-accordion .accordion-item {
            border: 1px solid var(--border-color, #e0e0e0);
            border-radius: 8px !important;
            margin-bottom: 1rem;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .faq-accordion .accordion-button {
            font-weight: 600;
            color: var(--primary-blue, #0d2b4d);
            background-color: #fff;
            padding: 1.2rem;
            box-shadow: none !important;
        }
        .faq-accordion .accordion-button:not(.collapsed) {
            color: var(--primary-green, #1b5e20);
            background-color: rgba(27, 94, 32, 0.05);
        }
        .faq-accordion .accordion-button::after {
            background-size: 1rem;
        }
        .faq-accordion .accordion-body {
            color: #555;
            line-height: 1.6;
            padding: 1.2rem;
            background-color: #fafafa;
        }
    </style>
@endpush

@section('content')

<div class="dashboard-container">
    <div class="row g-4">
        
        @include('partials.customer.sidebar')

        <!-- Main Content -->
        <div class="col-xl-9 col-lg-9">
            
            <div class="middle-card">
                <!-- Hero Banner -->
                @include('partials.customer.hero-banner', [
                    'title' => 'Frequently Asked Questions',
                    'subtitle' => 'Find answers to common questions about Scrap Daddy.'
                ])

                <div class="card border-0 shadow-sm rounded-4" style="overflow: hidden; margin-top: 15px;">
                    <div class="card-body p-4 p-md-5">
                        
                        @if($faqs->isEmpty())
                            <div class="text-center py-5">
                                <i class="fa-regular fa-circle-question fa-3x text-muted mb-3 opacity-50"></i>
                                <h4 class="fw-bold text-secondary">No FAQs Available</h4>
                                <p class="text-muted">Check back later for updates.</p>
                            </div>
                        @else
                            <div class="accordion faq-accordion" id="faqAccordion">
                                @foreach($faqs as $index => $faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                                                {{ $faq->question }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                            <div class="accordion-body">
                                                {!! nl2br(e($faq->answer)) !!}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
</div>

@endsection

{{-- "How it works" cards. Expects $steps (HowStep collection). --}}
@if($steps->count())
<div class="st-grid {{ $steps->count() >= 4 ? 'c4' : 'c3' }}">
    @foreach($steps as $i => $step)
        <div class="st-step">
            <div class="ic" style="background: {{ $step->color }};"><i class="fa-solid fa-{{ $step->icon }}"></i></div>
            <b>{{ $i + 1 }}. {{ $step->title }}</b>
            @if($step->description)<p>{{ $step->description }}</p>@endif
        </div>
    @endforeach
</div>
@endif

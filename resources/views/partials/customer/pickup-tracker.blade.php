{{-- Tracker + status box only. Expects $order. --}}
@once
<style>
    .sd-card { background: #fff; border: 1px solid #f1f1f1; border-radius: 20px; padding: 20px; box-shadow: 0 4px 18px rgba(0,0,0,.05); flex: 0 0 auto; width: 100%; max-width: 100%; scroll-snap-align: start; }
    @media (min-width: 768px) { .sd-card { width: 480px; } }
    .sd-card-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; gap: 10px; }
    .sd-card-head .sd-ttl { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .sd-card-head .sd-truck-ic { width: 34px; height: 34px; flex: 0 0 34px; border-radius: 50%; background: rgba(74,154,88,.1); color: #4A9A58; display: flex; align-items: center; justify-content: center; }
    .sd-card-head h6 { margin: 0; font-weight: 700; color: #222; font-size: 1rem; line-height: 1.2; }
    .sd-card-head .sd-ref { display: block; font-size: .7rem; font-weight: 500; color: #8a8a8a; }
    .sd-badge { font-size: .7rem; font-weight: 700; color: #fff; padding: 5px 12px; border-radius: 50px; white-space: nowrap; }
    .sd-badge.pending { background: #ffc107; color: #212529; }
    .sd-badge.accepted, .sd-badge.assigned { background: #0d6efd; }
    .sd-badge.completed { background: #198754; }
    .sd-badge.cancelled { background: #dc3545; }
    .sd-track { position: relative; padding: 16px 0 8px; }
    .sd-track-line { position: absolute; left: 12.5%; right: 12.5%; top: 36px; height: 3px; border-radius: 2px; background: repeating-linear-gradient(90deg, #d3d3d3 0 8px, transparent 8px 16px); }
    .sd-track-fill { position: absolute; left: 0; top: 0; height: 3px; width: 0; border-radius: 2px; background: #4CAF50; transition: width 2s cubic-bezier(.22, 1, .36, 1); }
    .sd-track-fill.is-cancelled { background: #dc3545; }
    .sd-track-steps { position: relative; z-index: 2; display: flex; }
    .sd-step { flex: 1; min-width: 0; text-align: center; padding: 0 2px; }
    .sd-ic { width: 42px; height: 42px; margin: 0 auto; display: flex; align-items: center; justify-content: center; }
    .sd-dot { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f5f5f5; color: #9e9e9e; font-size: .85rem; }
    .sd-dot.is-check { background: #fff; border: 2px solid #4CAF50; color: #4CAF50; }
    .sd-dot.is-cancelled { background: #fff; border: 2px solid #dc3545; color: #dc3545; }
    .sd-truck { width: 40px; height: auto; background: transparent; animation: sdTruckIn .6s ease-out both; }
    @keyframes sdTruckIn { from { transform: translateX(-14px); opacity: 0; } to { transform: none; opacity: 1; } }
    .sd-title { font-size: .75rem; font-weight: 700; margin-top: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sd-sub { font-size: .625rem; color: #8a8a8a; line-height: 1.25; margin-top: 4px; }
    .sd-info { background: #f3f9ff; border-radius: 16px; padding: 16px; margin-top: 8px; }
    .sd-info h6 { font-size: 1rem; font-weight: 700; color: #222; margin: 0 0 10px; }
    .sd-info p { font-size: .75rem; color: #2f3943; margin: 0; line-height: 1.4; }
    .sd-info.is-ok { background: rgba(25,135,84,.1); border: 1px solid #198754; } .sd-info.is-ok h6, .sd-info.is-ok p { color: #198754; }
    .sd-info.is-bad { background: rgba(220,53,69,.1); border: 1px solid #dc3545; } .sd-info.is-bad h6, .sd-info.is-bad p { color: #dc3545; }
    .sd-info.is-plain { background: #f8f9fa; }
    .sd-card-foot { text-align: center; margin-top: 14px; }
    .sd-card-foot a { color: #6c757d; text-decoration: none; font-size: .85rem; font-weight: 500; }
    .sd-card-foot a i { color: #4A9A58; margin-right: 6px; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sd-track').forEach(function (t) {
            var fill = t.querySelector('.sd-track-fill');
            requestAnimationFrame(function () { fill.style.width = (t.dataset.fill || 0) + '%'; });
        });
    });
</script>
@endonce
@php
    $st = $order->status;
    $isCancelled = ($st === 'cancelled');
    $trackStep = $st === 'completed' ? 3 : ($st === 'assigned' ? 2 : ($st === 'accepted' ? 1 : 0));
    $trackFill = $isCancelled ? 100 : round($trackStep / 3 * 100);
    $trackSteps = [
        ['title' => 'Submitted', 'sub' => $order->created_at ? $order->created_at->format('d M, h:i A') : 'Waiting', 'icon' => 'fa-solid fa-check', 'color' => '#4CAF50'],
        ['title' => 'Confirmed', 'sub' => $trackStep >= 1 ? 'Confirmed' : 'Waiting confirmation', 'icon' => 'fa-regular fa-clock', 'color' => '#FFB300'],
        ['title' => 'Assigned', 'sub' => $trackStep >= 2 ? 'Driver Assigned' : 'Pending Driver assignment', 'icon' => 'fa-solid fa-user', 'color' => '#5A5A5A'],
        ['title' => 'Completed', 'sub' => $isCancelled ? 'Cancelled' : ($trackStep >= 3 ? 'Done' : 'Pending Delivery completion'), 'icon' => 'fa-solid fa-flag', 'color' => '#5A5A5A'],
    ];
@endphp
    <div class="sd-track" data-fill="{{ $trackFill }}">
        <div class="sd-track-line"><div class="sd-track-fill {{ $isCancelled ? 'is-cancelled' : '' }}"></div></div>
        <div class="sd-track-steps">
            @foreach($trackSteps as $i => $s)
                <div class="sd-step">
                    <div class="sd-ic">
                        @if(!$isCancelled && $i === $trackStep)
                            <img src="{{ asset('green_truck.png') }}" alt="Truck" class="sd-truck" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            {{-- shown only if the truck picture can not load --}}
                            <span class="sd-dot" style="display:none;"><i class="{{ $s['icon'] }}"></i></span>
                        @elseif($isCancelled)
                            <span class="sd-dot is-cancelled"><i class="{{ $i === 3 ? 'fa-solid fa-xmark' : $s['icon'] }}"></i></span>
                        @else
                            <span class="sd-dot {{ $i === 0 || $i < $trackStep ? 'is-check' : '' }}"><i class="{{ $s['icon'] }}"></i></span>
                        @endif
                    </div>
                    <div class="sd-title" style="color: {{ $isCancelled ? '#dc3545' : ($i <= $trackStep ? '#4CAF50' : $s['color']) }};">{{ $s['title'] }}</div>
                    <div class="sd-sub">{{ $s['sub'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    @include('partials.customer.pickup-info', ['order' => $order])

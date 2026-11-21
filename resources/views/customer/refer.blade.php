@extends('layouts.app')

@section('title', 'Refer a Friend - Scrap Daddy')

@push('styles')
    @include('partials.customer.styles')
    <style>
        .rf-wrap { max-width: 640px; margin: 0 auto; }
        .rf-hero { background: linear-gradient(135deg, #e9f5ea 0%, #f6fbf6 100%); border: 1px solid #d9eadb; border-radius: 24px; padding: 28px 22px; text-align: center; }
        .rf-hero .ic { width: 68px; height: 68px; border-radius: 50%; background: #fff; color: #4a8f55; display: inline-flex; align-items: center; justify-content: center; font-size: 1.7rem; box-shadow: 0 6px 18px rgba(74,143,85,.18); margin-bottom: 12px; }
        .rf-code { display: inline-flex; align-items: center; gap: 12px; background: #fff; border: 2px dashed #9ccc9f; border-radius: 16px; padding: 12px 18px; margin: 14px 0 6px; font-size: 1.5rem; font-weight: 800; letter-spacing: .12em; color: #2f6b38; }
        .rf-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 16px; }
        .rf-stat { background: #fff; border: 1px solid rgba(16,24,40,.06); border-radius: 18px; padding: 14px; text-align: center; box-shadow: 0 4px 14px rgba(16,24,40,.04); }
        .rf-stat b { display: block; font-size: 1.4rem; color: #2f6b38; }
        .rf-stat span { font-size: .78rem; color: #6b7770; }
        .rf-steps li { margin-bottom: 8px; color: #44504a; }
    </style>
@endpush

@section('content')
<div class="container py-4">
    <div class="rf-wrap">
        <div class="rf-hero">
            <div class="ic"><i class="fa-solid fa-gift"></i></div>
            <h3 class="fw-bold mb-1">Refer a Friend</h3>
            <p class="text-muted mb-0">You earn <strong>{{ $ref['referrer_bonus_coins'] }} coins</strong> and your friend gets <strong>{{ $ref['friend_bonus_coins'] }} coins</strong> after their first completed pickup.</p>
            <div class="rf-code"><span id="refCode">{{ $ref['code'] }}</span></div>
            <div class="d-flex flex-wrap gap-2 justify-content-center mt-3">
                <button type="button" class="btn btn-primary px-4" id="refShare"><i class="fa-solid fa-share-nodes me-2"></i>Share</button>
                <a class="btn btn-outline-success px-4" target="_blank" rel="noopener" href="https://wa.me/?text={{ urlencode($ref['share_message']) }}"><i class="fa-brands fa-whatsapp me-2"></i>WhatsApp</a>
                <button type="button" class="btn btn-outline-secondary px-4" id="refCopy"><i class="fa-regular fa-copy me-2"></i>Copy link</button>
            </div>
        </div>

        <div class="rf-stats">
            <div class="rf-stat"><b>{{ $ref['friends_joined'] }}</b><span>Friends joined</span></div>
            <div class="rf-stat"><b>{{ $ref['friends_rewarded'] }}</b><span>Completed 1st pickup</span></div>
            <div class="rf-stat"><b>{{ $ref['coins_earned'] }}</b><span>Coins earned</span></div>
        </div>

        <h5 class="fw-bold mt-4">How it works</h5>
        <ol class="rf-steps ps-3">
            <li>Share your code or link with a friend.</li>
            <li>They sign up with your code.</li>
            <li>When their first pickup is paid, coins are added to both wallets.</li>
        </ol>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const msg = @json($ref['share_message']);
        const link = @json($ref['link']);
        const toast = t => (window.sdToast ? sdToast(t) : alert(t));
        document.getElementById('refShare').addEventListener('click', async () => {
            try {
                if (navigator.share) { await navigator.share({ title: 'Join me on ScrapDaddy', text: msg, url: link }); return; }
                await navigator.clipboard.writeText(msg);
                toast('Invite message copied. Paste it to your friends!');
            } catch (e) { /* cancelled */ }
        });
        document.getElementById('refCopy').addEventListener('click', async () => {
            try { await navigator.clipboard.writeText(link); toast('Link copied'); } catch (e) { toast(link); }
        });
    })();
</script>
@endpush

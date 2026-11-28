{{-- PWA: registers the service worker and shows an "Install app" banner (Add to Home Screen) --}}
<style>
    #sdInstall { position: fixed; left: 12px; right: 12px; bottom: 14px; z-index: 2000; display: none; justify-content: center; pointer-events: none; }
    #sdInstall .sd-in { pointer-events: auto; max-width: 460px; width: 100%; background: #fff; border: 1px solid #D8E8D9; border-radius: 18px; padding: 12px 14px; display: flex; align-items: center; gap: 12px; box-shadow: 0 12px 34px rgba(15,23,42,.18); }
    #sdInstall img { width: 46px; height: 46px; border-radius: 12px; flex: none; }
    #sdInstall .sd-t { flex: 1; min-width: 0; line-height: 1.25; }
    #sdInstall .sd-t b { display: block; font-size: .95rem; color: #14201A; }
    #sdInstall .sd-t span { font-size: .8rem; color: #64748B; }
    #sdInstall button, #sdIosSheet button { border: 0; border-radius: 12px; font-weight: 700; font-size: .88rem; padding: 9px 14px; cursor: pointer; }
    #sdInstall .sd-go, #sdIosSheet .sd-go { background: #3D8B4D; color: #fff; }
    #sdInstall .sd-x { background: transparent; color: #94A3B8; padding: 6px 8px; font-size: 1.1rem; }
    @media (max-width: 991px) { #sdInstall { bottom: 84px; } }

    /* iPhone / iPad: step-by-step sheet (Apple has no install prompt) */
    #sdIosSheet { position: fixed; inset: 0; z-index: 2100; display: none; align-items: flex-end; justify-content: center; background: rgba(15,23,42,.45); }
    #sdIosSheet .sd-card { width: 100%; max-width: 460px; background: #fff; border-radius: 22px 22px 0 0; padding: 20px 20px calc(22px + env(safe-area-inset-bottom)); }
    #sdIosSheet h4 { margin: 0 0 4px; font-size: 1.1rem; color: #14201A; }
    #sdIosSheet p.sub { margin: 0 0 14px; color: #64748B; font-size: .88rem; }
    #sdIosSheet ol { margin: 0 0 16px; padding: 0; list-style: none; }
    #sdIosSheet li { display: flex; gap: 12px; align-items: center; padding: 10px 0; border-bottom: 1px dashed #E2E8F0; font-size: .95rem; color: #14201A; }
    #sdIosSheet li:last-child { border: 0; }
    #sdIosSheet .n { width: 28px; height: 28px; border-radius: 50%; background: #3D8B4D; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; flex: none; }
    #sdIosSheet svg { width: 20px; height: 20px; vertical-align: -4px; }
    #sdIosSheet .sd-go { width: 100%; padding: 13px; font-size: 1rem; }
</style>

<div id="sdInstall" role="dialog" aria-label="Install Scrap Daddy">
    <div class="sd-in">
        <img src="{{ asset('images/pwa/icon-192.png') }}" alt="">
        <div class="sd-t"><b>Install Scrap Daddy</b><span id="sdInstallHint">Add to your home screen for faster pickups.</span></div>
        <button type="button" class="sd-go" id="sdInstallBtn">Install</button>
        <button type="button" class="sd-x" id="sdInstallClose" aria-label="Close">&times;</button>
    </div>
</div>

<div id="sdIosSheet" role="dialog" aria-label="Install on iPhone">
    <div class="sd-card">
        <h4>Install Scrap Daddy on your iPhone</h4>
        <p class="sub">It only takes a few seconds:</p>
        <ol>
            <li><span class="n">1</span><span>Tap the <b>Share</b> button
                <svg viewBox="0 0 24 24" fill="none" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="M8 7l4-4 4 4"/><path d="M5 11v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8"/></svg>
                at the bottom (or top) of Safari</span></li>
            <li><span class="n">2</span><span>Scroll down and tap <b>Add to Home Screen</b></span></li>
            <li><span class="n">3</span><span>Tap <b>Add</b>. The Scrap Daddy icon appears on your home screen.</span></li>
        </ol>
        <button type="button" class="sd-go" id="sdIosClose">Got it</button>
    </div>
</div>

<script>
(function () {
    // 1) service worker
    if ('serviceWorker' in navigator && (location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function () {});
        });
    }

    var box = document.getElementById('sdInstall');
    var sheet = document.getElementById('sdIosSheet');
    if (!box) return;

    var standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    if (standalone) return;                       // already installed / opened from the home screen

    var KEY = 'sdInstallDismissed';
    var force = /[?&]pwa=1\b/.test(location.search);   // add ?pwa=1 to the URL to show the banner again for testing
    function dismissedRecently() {
        if (force) return false;
        try { var t = parseInt(localStorage.getItem(KEY) || '0', 10); return t && (Date.now() - t) < 7 * 24 * 3600 * 1000; } catch (e) { return false; }
    }
    function dismiss() { try { localStorage.setItem(KEY, String(Date.now())); } catch (e) {} box.style.display = 'none'; }
    document.getElementById('sdInstallClose').addEventListener('click', dismiss);
    document.getElementById('sdIosClose').addEventListener('click', function () { sheet.style.display = 'none'; });
    sheet.addEventListener('click', function (e) { if (e.target === sheet) sheet.style.display = 'none'; });
    if (dismissedRecently()) return;

    // iPhone / iPad (iPadOS reports itself as a Mac with touch). Not inside Facebook/Instagram/etc in-app browsers.
    var ua = navigator.userAgent || '';
    var isIos = (/iphone|ipad|ipod/i.test(ua) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1)) && !window.MSStream;
    var inApp = /FBAN|FBAV|Instagram|Line\/|Twitter|MicroMessenger|GSA\//i.test(ua);

    if (isIos) {
        if (inApp) return;
        var btn = document.getElementById('sdInstallBtn');
        document.getElementById('sdInstallHint').textContent = 'Tap Install to see how to add it to your home screen.';
        btn.addEventListener('click', function () { sheet.style.display = 'flex'; });
        box.style.display = 'flex';
        return;
    }

    // Android / desktop Chrome, Edge: real install prompt
    var deferred = null;
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferred = e;
        box.style.display = 'flex';
    });
    document.getElementById('sdInstallBtn').addEventListener('click', function () {
        if (!deferred) return;
        deferred.prompt();
        deferred.userChoice.finally(function () { deferred = null; box.style.display = 'none'; });
    });
    window.addEventListener('appinstalled', function () { box.style.display = 'none'; });
})();
</script>

{{-- Home sections driven by the admin panel (Home Content): Trending, Bulk enquiry, Sell bulk, Scrap vehicle, Offers, Know your scrap.
     Loaded from /api/customer/home-content. Sections are soft tinted panels (no divider lines). --}}
<div id="hsRoot" class="hs-root" style="display:none;">

    {{-- ===== Trending ===== --}}
    <section id="hsTrending" class="hs-panel hs-mint" style="display:none;">
        <div class="hs-h"><span class="hs-ic ic-fire"><i class="fa-solid fa-fire"></i></span><div><h5 id="hsTrendingTitle"></h5><p id="hsTrendingDesc"></p></div></div>
        <div class="hs-trend-row" id="hsTrendRow"></div>
        <div class="hs-cartbar" id="hsCartBar" style="display:none;">
            <span><b id="hsCartCount">0</b> <span id="hsCartLabel">items</span> added</span>
            <button type="button" id="hsCartGo">Continue <i class="fa-solid fa-arrow-right ms-1"></i></button>
        </div>
    </section>

    {{-- ===== Bulk enquiry hero ===== --}}
    <section id="hsBulk" class="hs-panel hs-bulkband" style="display:none;">
        <div class="hs-bulk">
            <i class="hs-orb hs-orb1"></i><i class="hs-orb hs-orb2"></i>
            <div class="hs-bulk-ic"><i class="fa-solid fa-boxes-stacked"></i></div>
            <h5 id="hsBulkTitle"></h5>
            <p id="hsBulkDesc"></p>
            <button type="button" class="hs-bulk-btn" id="hsBulkBtn"><span>Start enquiry</span><i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </section>

    {{-- ===== Sell bulk scrap / Scrap your vehicle: header, soft hashtags, scrolling banners ===== --}}
    <section id="hsSellBulk" class="hs-panel hs-sand" style="display:none;">
        <div class="hs-h"><span class="hs-ic ic-sand"><i class="fa-solid fa-truck-ramp-box"></i></span><div><h5 data-t="title"></h5><p data-t="desc"></p></div></div>
        <div class="hs-tags" data-t="tags"></div>
        <div class="hs-slider" data-slider></div>
    </section>

    <section id="hsVehicle" class="hs-panel hs-sky" style="display:none;">
        <div class="hs-h"><span class="hs-ic ic-sky"><i class="fa-solid fa-car-side"></i></span><div><h5 data-t="title"></h5><p data-t="desc"></p></div></div>
        <div class="hs-tags" data-t="tags"></div>
        <div class="hs-slider" data-slider></div>
    </section>

    {{-- ===== Offers ===== --}}
    <section id="hsOffers" class="hs-panel hs-lilac" style="display:none;">
        <div class="hs-h"><span class="hs-ic ic-lilac"><i class="fa-solid fa-tags"></i></span><div><h5 data-t="title"></h5><p data-t="desc"></p></div></div>
        <div class="hs-slider" data-slider></div>
    </section>

    {{-- ===== Know your scrap ===== --}}
    <section id="hsKnow" class="hs-panel hs-cream" style="display:none;">
        <div class="hs-h"><span class="hs-ic ic-gold"><i class="fa-solid fa-lightbulb"></i></span><div><h5 data-t="title"></h5><p data-t="desc"></p></div></div>
        <div id="hsKnowFeatured"></div>
        <div class="hs-know" id="hsKnowGrid"></div>
        <a class="hs-viewall" href="{{ route('blog.index') }}">View all articles <i class="fa-solid fa-arrow-right"></i></a>
    </section>
</div>

{{-- Bulk enquiry form --}}
<div class="modal fade" id="hsEnquiryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" id="hsEnquiryForm" style="border-radius: 28px; border: 0;" novalidate>
            <div class="modal-header border-0 pb-0 align-items-center">
                <span class="hs-ic ic-green me-2" style="width:44px;height:44px;"><i class="fa-solid fa-boxes-stacked"></i></span>
                <h5 class="modal-title fw-bold flex-grow-1" id="hsEnquiryTitle">Bulk enquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="hsEnquiryDone" style="display:none;" class="text-center py-4">
                    <div class="hs-done-ic"><i class="fa-solid fa-circle-check"></i></div>
                    <h6 class="fw-bold mt-3 mb-1">Enquiry sent!</h6>
                    <p class="text-muted small mb-0" id="hsEnquiryDoneMsg"></p>
                </div>
                <div id="hsEnquiryFields">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Your name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control hs-input" id="hsEnqName" maxlength="120" autocomplete="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Phone number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control hs-input" id="hsEnqPhone" maxlength="15" inputmode="tel" autocomplete="tel">
                    </div>
                    <div id="hsDynamicFields"></div>
                    <div class="text-danger small" id="hsEnquiryError" style="display:none;"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0" id="hsEnquiryFooter">
                <button type="submit" class="btn w-100 fw-bold text-white py-2" id="hsEnquirySubmit" style="background:linear-gradient(135deg,#4A9A58,#3D8B4D);border-radius:16px;">Submit enquiry</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Full-width bands: each section runs edge to edge and its rounded top curve overlaps the band above (no cards, no lines). */
    html, body { overflow-x: clip; }
    .hs-root { --hs-curve: 36px; --hs-pad: max(16px, calc((100vw - 1100px) / 2)); width: 100vw; margin: 8px 0 0 calc(50% - 50vw); display: block; }

    /* ---- panels: soft tint + generous radius + space. No divider lines anywhere. */
    .hs-panel { position: relative; border-radius: var(--hs-curve) var(--hs-curve) 0 0; margin-top: calc(var(--hs-curve) * -1); padding: 34px var(--hs-pad) calc(var(--hs-curve) + 26px); overflow: hidden; }
    .hs-panel.hs-first { margin-top: 0; }
    /* scrolling rows run to the screen edges */
    .hs-trend-row, .hs-slider { margin-left: calc(var(--hs-pad) * -1); margin-right: calc(var(--hs-pad) * -1); padding-left: var(--hs-pad); padding-right: var(--hs-pad); scroll-padding-left: var(--hs-pad); }
    .hs-mint  { background: linear-gradient(180deg, #e8f6ec, #f6fbf7); }
    .hs-sand  { background: linear-gradient(180deg, #fff4dc, #fffbf1); }
    .hs-sky   { background: linear-gradient(180deg, #e6f2ff, #f5faff); }
    .hs-lilac { background: linear-gradient(180deg, #f1eaff, #faf7ff); }
    .hs-cream { background: linear-gradient(180deg, #f3f6ee, #fafbf7); }
    .hs-bulkband { background: linear-gradient(135deg, #17603a, #3D8B4D 55%, #69be7b); color: #fff; }

    .hs-h { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .hs-ic { width: 46px; height: 46px; flex: 0 0 46px; border-radius: 15px; background: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; box-shadow: 0 6px 14px rgba(16,24,40,.10); }
    .hs-ic i { font-size: 1.15rem; }
    .hs-ic.ic-fire { color: #f97316; } .hs-ic.ic-sand { color: #b7791f; } .hs-ic.ic-sky { color: #2b6cb0; }
    .hs-ic.ic-lilac { color: #6b46c1; } .hs-ic.ic-gold { color: #d69e2e; } .hs-ic.ic-green { color: #3D8B4D; }
    .hs-h h5 { margin: 0; font-weight: 800; font-size: 1.15rem; color: #1d2939; line-height: 1.2; }
    .hs-h p { margin: 2px 0 0; font-size: .82rem; color: #667085; line-height: 1.35; }
    .hs-h p:empty { display: none; }

    /* ---- Trending: floating white cards */
    .hs-trend-row { display: flex; gap: 12px; overflow-x: auto; padding: 6px 2px 14px; scroll-snap-type: x proximity; scrollbar-width: none; }
    .hs-trend-row::-webkit-scrollbar { display: none; }
    .hs-card { flex: 0 0 116px; scroll-snap-align: start; background: #fff; border-radius: 24px; padding: 12px 10px; text-align: center; box-shadow: 0 8px 20px rgba(16,24,40,.09); transition: transform .15s ease; }
    .hs-card:active { transform: scale(.98); }
    .hs-circle { width: 84px; height: 84px; margin: 0 auto; border-radius: 50%; display: flex; align-items: center; justify-content: center; padding: 12px; background: #ddf3e4; }
    .hs-card:nth-child(6n+2) .hs-circle { background: #ffe9c7; }
    .hs-card:nth-child(6n+3) .hs-circle { background: #dcebff; }
    .hs-card:nth-child(6n+4) .hs-circle { background: #f1e1ff; }
    .hs-card:nth-child(6n+5) .hs-circle { background: #ffdfe0; }
    .hs-card:nth-child(6n+6) .hs-circle { background: #d9f3f0; }
    .hs-circle img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .hs-circle .hs-fallback { font-size: 1.7rem; color: #4A9A58; }
    .hs-name { font-size: .8rem; font-weight: 600; color: #1d2939; margin: 8px 0 10px; line-height: 1.25; min-height: 2.5em; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .hs-add { border: 0; border-radius: 999px; padding: 7px 22px; background: linear-gradient(135deg, #4A9A58, #3D8B4D); color: #fff; font-weight: 700; font-size: .82rem; box-shadow: 0 4px 10px rgba(61,139,77,.3); }
    .hs-step { display: none; align-items: center; background: #eaf6ea; border: 1.5px solid #4A9A58; border-radius: 999px; }
    .hs-step button { border: 0; background: transparent; color: #3D8B4D; width: 32px; height: 32px; font-weight: 700; font-size: 1.1rem; line-height: 1; padding: 0; }
    .hs-step span { min-width: 22px; text-align: center; font-weight: 700; font-size: .88rem; color: #1d2939; }
    .hs-card.has-qty .hs-add { display: none; }
    .hs-card.has-qty .hs-step { display: inline-flex; }

    /* continue pill */
    .hs-cartbar { display: flex; align-items: center; justify-content: space-between; background: #1d2939; color: #fff; border-radius: 999px; padding: 6px 6px 6px 20px; font-size: .9rem; font-weight: 600; box-shadow: 0 10px 24px rgba(16,24,40,.25); }
    .hs-cartbar button { border: 0; background: linear-gradient(135deg, #4A9A58, #3D8B4D); color: #fff; font-weight: 700; border-radius: 999px; padding: 9px 20px; }

    /* ---- Bulk enquiry hero */
    .hs-bulk { position: relative; color: #fff; }
    .hs-orb { position: absolute; border-radius: 50%; background: rgba(255,255,255,.10); pointer-events: none; }
    .hs-orb1 { width: 170px; height: 170px; right: -50px; top: -60px; }
    .hs-orb2 { width: 130px; height: 130px; right: 30px; bottom: -70px; background: rgba(255,255,255,.08); }
    .hs-bulk-ic { position: relative; width: 52px; height: 52px; border-radius: 16px; background: rgba(255,255,255,.22); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 14px; }
    .hs-bulk h5 { position: relative; font-weight: 800; font-size: 1.35rem; margin: 0 0 6px; line-height: 1.2; }
    .hs-bulk p { position: relative; margin: 0 0 18px; font-size: .9rem; opacity: .94; line-height: 1.45; max-width: 82%; }
    .hs-bulk-btn { position: relative; border: 0; background: #fff; color: #1f6f43; font-weight: 800; border-radius: 999px; padding: 8px 8px 8px 22px; display: inline-flex; align-items: center; gap: 10px; font-size: .95rem; }
    .hs-bulk-btn i { width: 30px; height: 30px; border-radius: 50%; background: #3D8B4D; color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: .8rem; }

    /* ---- hashtags: soft filled chips */
    .hs-tags { display: flex; flex-wrap: wrap; gap: 8px; margin: -2px 0 14px; }
    .hs-tags span { border-radius: 999px; padding: 6px 13px; font-size: .74rem; font-weight: 700; }
    .hs-sand .hs-tags span { background: rgba(183,121,31,.13); color: #b7791f; }
    .hs-sky .hs-tags span { background: rgba(43,108,176,.13); color: #2b6cb0; }

    /* ---- banner sliders */
    .hs-slider { display: flex; gap: 12px; overflow-x: auto; scroll-snap-type: x mandatory; scrollbar-width: none; padding: 4px 2px 10px; }
    .hs-slider::-webkit-scrollbar { display: none; }
    .hs-slide { flex: 0 0 88%; max-width: 560px; scroll-snap-align: center; position: relative; border-radius: 24px; overflow: hidden; background: #fff; aspect-ratio: 2 / 1; box-shadow: 0 10px 24px rgba(16,24,40,.16); cursor: pointer; border: 0; padding: 0; display: block; text-align: left; }
    .hs-slide.is-static { cursor: default; }
    .hs-slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .hs-count { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,.38); color: #fff; font-size: .7rem; font-weight: 600; border-radius: 999px; padding: 3px 10px; }
    .hs-cap { position: absolute; left: 0; right: 0; bottom: 0; padding: 28px 12px 12px 16px; background: linear-gradient(transparent, rgba(0,0,0,.65)); color: #fff; display: flex; align-items: center; gap: 10px; }
    .hs-cap div { flex: 1; min-width: 0; }
    .hs-cap b { display: block; font-size: 1rem; }
    .hs-cap small { opacity: .92; font-size: .78rem; }
    .hs-cap i { width: 30px; height: 30px; border-radius: 50%; background: #fff; color: #1d2939; display: inline-flex; align-items: center; justify-content: center; font-size: .75rem; flex: 0 0 auto; }
    .hs-dots { display: flex; justify-content: center; gap: 6px; margin-top: 2px; }
    .hs-dots i { width: 7px; height: 7px; border-radius: 50%; background: rgba(0,0,0,.15); transition: all .2s; }
    .hs-dots i.on { width: 22px; border-radius: 6px; background: #4A9A58; }
    .hs-sand .hs-dots i.on { background: #b7791f; }
    .hs-sky .hs-dots i.on { background: #2b6cb0; }
    .hs-lilac .hs-dots i.on { background: #6b46c1; }

    /* ---- Know your scrap: one featured card + tidy grid */
    .hs-feat { position: relative; display: block; border-radius: 24px; overflow: hidden; aspect-ratio: 16 / 9; background: #ddeedd; box-shadow: 0 10px 24px rgba(16,24,40,.16); text-decoration: none; color: #fff; margin-bottom: 14px; }
    .hs-feat img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .hs-feat .hs-cap { padding-top: 44px; display: block; }
    .hs-feat .hs-cap b { font-size: 1.15rem; line-height: 1.25; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .hs-feat .hs-cap small { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-top: 3px; }
    .hs-feat .hs-pill { display: inline-block; margin-top: 8px; background: #fff; color: #1f6f43; font-weight: 700; font-size: .75rem; border-radius: 999px; padding: 5px 14px; }
    .hs-know { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
    @media (min-width: 768px) { .hs-know { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    .hs-article { display: block; text-decoration: none; background: #fff; border-radius: 22px; overflow: hidden; box-shadow: 0 8px 20px rgba(16,24,40,.09); color: inherit; }
    .hs-article img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; display: block; background: #eaf6ea; }
    .hs-article .hs-a-body { padding: 10px 12px 12px; }
    .hs-article b { display: block; font-size: .9rem; color: #1d2939; line-height: 1.25; }
    .hs-article span { margin-top: 4px; font-size: .75rem; color: #667085; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .hs-article em { display: inline-block; margin-top: 8px; font-style: normal; font-size: .76rem; font-weight: 700; color: #3D8B4D; }

    .hs-viewall { display: inline-flex; align-items: center; gap: 8px; margin-top: 16px; font-weight: 700; font-size: .88rem; color: #3D8B4D; text-decoration: none; background: #fff; border-radius: 999px; padding: 9px 18px; box-shadow: 0 6px 14px rgba(16,24,40,.08); }
    .hs-input { border-radius: 14px; padding: 11px 14px; border-color: #e4e7ec; }
    .hs-done-ic { width: 72px; height: 72px; border-radius: 50%; background: #e4f4e8; color: #2b7a3d; display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; }
    /* ===== Stacked cards: each band pins near the top while you scroll and the next band slides up over it ===== */
    .hs-panel { position: sticky; top: 10px; transform-origin: 50% 0; will-change: transform; box-shadow: 0 -14px 30px rgba(16,24,40,.12); }
    @media (prefers-reduced-motion: reduce) { .hs-panel { position: relative; top: auto !important; } }

    /* ===== Modern "handle" line on each curve ===== */
    .hs-panel::before { content: ''; position: absolute; top: 12px; left: 50%; transform: translateX(-50%); width: 40px; height: 4px; border-radius: 4px; background: rgba(16,24,40,.16); }
    .hs-bulkband::before { background: rgba(255,255,255,.45); }
</style>

<script>
    (function () {
        const API = "{{ url('/api/customer') }}";
        const ASSET = "{{ asset('') }}";
        const REQUEST_URL = "{{ url('/customer/request-pickup') }}";
        const VEHICLE_URL = "{{ url('/customer/scrap-vehicles') }}";
        const BLOG_URL = "{{ url('/blog') }}";
        const $ = id => document.getElementById(id);
        const esc = t => String(t == null ? '' : t).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const cart = {}; // uuid -> qty
        document.body.appendChild($('hsEnquiryModal'));

        // Customer Home has a desktop block and a mobile block: show the sections in the one that is visible
        function placeRoot() {
            const slot = document.querySelector(window.matchMedia('(min-width: 992px)').matches ? '#hsSlotDesktop' : '#hsSlotMobile');
            const root = $('hsRoot');
            if (slot && root.parentNode !== slot) slot.appendChild(root);
        }
        placeRoot();
        window.addEventListener('resize', placeRoot);
        let fields = [];

        // ---- stacked cards ----
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const bands = () => Array.from($('hsRoot').children).filter(x => x.style.display !== 'none');
        // where each band pins: earlier bands peek out a little above the later ones; a band taller than the screen pins by its bottom
        function layoutStack() {
            if (reduce) return;
            const bottomReserve = window.innerWidth < 992 ? 92 : 16;   // the phone bottom bar
            bands().forEach((el, i) => {
                const top = Math.min(10 + Math.min(i, 5) * 8, window.innerHeight - bottomReserve - el.offsetHeight);
                el.style.top = top + 'px';
            });
        }
        // the band being covered shrinks and dims a little, so the new one feels like it lands on top
        let ticking = false;
        function depth() {
            ticking = false;
            if (reduce) return;
            const list = bands();
            list.forEach((el, i) => {
                const next = list[i + 1];
                if (!next) { el.style.transform = ''; el.style.filter = ''; return; }
                const pinned = parseFloat(el.style.top) || 0;
                const gap = next.getBoundingClientRect().top - pinned;
                const p = Math.max(0, Math.min(1, 1 - gap / (window.innerHeight * 0.55)));
                el.style.transform = p > 0 ? 'scale(' + (1 - 0.05 * p).toFixed(4) + ')' : '';
                el.style.filter = p > 0 ? 'brightness(' + (1 - 0.12 * p).toFixed(3) + ')' : '';
            });
        }
        window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(depth); } }, { passive: true });
        window.addEventListener('resize', () => { layoutStack(); depth(); });
        window.addEventListener('load', () => { layoutStack(); depth(); });

        const show = el => {
            el.style.display = ''; $('hsRoot').style.display = '';
            layoutStack(); depth();
            // the first visible band sits flat under whatever is above; the rest tuck their curve over the band before
            Array.from($('hsRoot').children).filter(x => x.style.display !== 'none').forEach((x, i) => x.classList.toggle('hs-first', i === 0));
        };

        // ---------- Trending
        function updateBar() {
            const kinds = Object.keys(cart).length;
            $('hsCartBar').style.display = kinds ? 'flex' : 'none';
            $('hsCartCount').textContent = kinds;
            $('hsCartLabel').textContent = kinds === 1 ? 'item' : 'items';
        }
        function setQty(card, uuid, q) {
            q = Math.max(0, Math.min(999, q));
            if (q) cart[uuid] = q; else delete cart[uuid];
            card.classList.toggle('has-qty', q > 0);
            card.querySelector('.hs-qty').textContent = q;
            updateBar();
        }
        function renderTrending(sec, items) {
            if (!sec || !sec.is_active || !items.length) return;
            $('hsTrendingTitle').textContent = sec.title || '';
            $('hsTrendingDesc').textContent = sec.description || '';
            if (sec.button_text) $('hsCartGo').firstChild.textContent = sec.button_text + ' ';
            $('hsTrendRow').innerHTML = items.map(it => `
                <div class="hs-card" data-uuid="${esc(it.uuid)}">
                    <div class="hs-circle">
                        ${it.image ? `<img src="${esc(ASSET + it.image)}" alt="${esc(it.name)}" loading="lazy" onerror="this.outerHTML='<i class=&quot;fa-solid fa-recycle hs-fallback&quot;></i>'">` : '<i class="fa-solid fa-recycle hs-fallback"></i>'}
                    </div>
                    <div class="hs-name">${esc(it.name)}</div>
                    <button type="button" class="hs-add" aria-label="Add ${esc(it.name)}">+ Add</button>
                    <div class="hs-step">
                        <button type="button" class="hs-minus" aria-label="Less">&minus;</button>
                        <span class="hs-qty">0</span>
                        <button type="button" class="hs-plus" aria-label="More">+</button>
                    </div>
                </div>`).join('');
            $('hsTrendRow').querySelectorAll('.hs-card').forEach(card => {
                const uuid = card.dataset.uuid;
                card.querySelector('.hs-add').addEventListener('click', () => setQty(card, uuid, 1));
                card.querySelector('.hs-plus').addEventListener('click', () => setQty(card, uuid, (cart[uuid] || 0) + 1));
                card.querySelector('.hs-minus').addEventListener('click', () => setQty(card, uuid, (cart[uuid] || 0) - 1));
            });
            show($('hsTrending'));
        }
        // Continue: pickup form with the chosen items already added
        $('hsCartGo').addEventListener('click', function () {
            const list = Object.entries(cart).map(([u, q]) => u + ':' + q).join(',');
            if (list) window.location.href = REQUEST_URL + '?items=' + encodeURIComponent(list);
        });

        // ---------- Bulk enquiry
        function renderBulk(sec) {
            if (!sec || !sec.is_active) return;
            $('hsBulkTitle').textContent = sec.title || '';
            $('hsBulkDesc').textContent = sec.description || '';
            $('hsBulkDesc').style.display = sec.description ? '' : 'none';
            if (sec.button_text) $('hsBulkBtn').firstElementChild.textContent = sec.button_text;
            $('hsEnquiryTitle').textContent = sec.title || 'Bulk enquiry';
            show($('hsBulk'));
        }
        function renderFields() {
            $('hsDynamicFields').innerHTML = fields.map(f => {
                const id = 'hsf_' + f.id, req = f.required ? ' <span class="text-danger">*</span>' : '';
                let input;
                if (f.type === 'dropdown') {
                    input = `<select class="form-select hs-input" id="${id}"><option value="">Select</option>${(f.options || []).map(o => `<option value="${esc(o)}">${esc(o)}</option>`).join('')}</select>`;
                } else if (f.type === 'textarea') {
                    input = `<textarea class="form-control hs-input" id="${id}" rows="3" maxlength="1000"></textarea>`;
                } else {
                    const type = f.type === 'number' ? 'number' : (f.type === 'email' ? 'email' : (f.type === 'phone' ? 'tel' : 'text'));
                    input = `<input type="${type}" class="form-control hs-input" id="${id}" maxlength="255" ${f.type === 'number' ? 'min="0" inputmode="decimal"' : ''}>`;
                }
                return `<div class="mb-3"><label class="form-label fw-semibold small" for="${id}">${esc(f.label)}${req}</label>${input}</div>`;
            }).join('');
        }
        function openEnquiry() {
            $('hsEnquiryDone').style.display = 'none';
            $('hsEnquiryFields').style.display = '';
            $('hsEnquiryFooter').style.display = '';
            $('hsEnquiryError').style.display = 'none';
            try {
                const u = JSON.parse(localStorage.getItem('user_data') || 'null');
                if (u) { if (!$('hsEnqName').value && u.full_name) $('hsEnqName').value = u.full_name; if (!$('hsEnqPhone').value && u.phone_number) $('hsEnqPhone').value = u.phone_number; }
            } catch (e) {}
            renderFields();
            bootstrap.Modal.getOrCreateInstance($('hsEnquiryModal')).show();
        }
        $('hsBulkBtn').addEventListener('click', openEnquiry);

        $('hsEnquiryForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const err = $('hsEnquiryError'); err.style.display = 'none';
            const fail = m => { err.textContent = m; err.style.display = 'block'; };
            const name = $('hsEnqName').value.trim(), phone = $('hsEnqPhone').value.trim();
            if (!name) return fail('Please enter your name.');
            if (!/^[0-9+\-\s]{8,15}$/.test(phone)) return fail('Please enter a valid phone number.');
            const answers = {};
            for (const f of fields) {
                const v = ($('hsf_' + f.id).value || '').trim();
                if (f.required && !v) return fail('Please fill in: ' + f.label);
                if (v) answers[f.id] = v;
            }
            const btn = $('hsEnquirySubmit'); const label = btn.textContent;
            btn.disabled = true; btn.textContent = 'Sending...';
            try {
                const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };
                const token = localStorage.getItem('auth_token'); if (token) headers['Authorization'] = 'Bearer ' + token;
                const res = await fetch(API + '/bulk-enquiry', { method: 'POST', headers, body: JSON.stringify({ name, phone, answers }) });
                const data = await res.json();
                if (data && data.status === 1) {
                    $('hsEnquiryFields').style.display = 'none';
                    $('hsEnquiryFooter').style.display = 'none';
                    $('hsEnquiryDoneMsg').textContent = data.message || 'Our team will contact you shortly.';
                    $('hsEnquiryDone').style.display = 'block';
                } else {
                    fail((data && data.message) || 'Could not send your enquiry. Please try again.');
                }
            } catch (x) { fail('Network problem. Please try again.'); }
            btn.disabled = false; btn.textContent = label;
        });

        // ---------- Banner sections (Sell bulk scrap / Scrap your vehicle / Offers)
        function bannerTap(b) {
            switch (b.link_type) {
                case 'bulk_enquiry': openEnquiry(); break;
                case 'pickup': window.location.href = REQUEST_URL; break;
                case 'vehicle': window.location.href = VEHICLE_URL; break;
                case 'url': if (b.link_value) window.open(b.link_value, '_blank', 'noopener'); break;
            }
        }
        function renderSection(id, sec, list) {
            const el = $(id);
            if (!el || !sec || !sec.is_active || !list || !list.length) return;
            el.querySelector('[data-t="title"]').textContent = sec.title || '';
            el.querySelector('[data-t="desc"]').textContent = sec.description || '';
            const tg = el.querySelector('[data-t="tags"]');
            if (tg) { tg.innerHTML = (sec.tags || []).map(x => '<span>#' + esc(x) + '</span>').join(''); tg.style.display = (sec.tags || []).length ? '' : 'none'; }
            const slider = el.querySelector('[data-slider]');
            slider.innerHTML = list.map((b, i) => `
                <button type="button" class="hs-slide ${b.link_type === 'none' ? 'is-static' : ''}" data-i="${i}">
                    <img src="${esc(ASSET + b.image)}" alt="${esc(b.title || '')}" loading="lazy">
                    ${list.length > 1 ? `<span class="hs-count">${i + 1}/${list.length}</span>` : ''}
                    ${(b.title || b.subtitle || b.link_type !== 'none') ? `<div class="hs-cap"><div>${b.title ? '<b>' + esc(b.title) + '</b>' : ''}${b.subtitle ? '<small>' + esc(b.subtitle) + '</small>' : ''}</div>${b.link_type !== 'none' ? '<i class="fa-solid fa-arrow-right"></i>' : ''}</div>` : ''}
                </button>`).join('');
            slider.querySelectorAll('.hs-slide').forEach(btn => btn.addEventListener('click', () => bannerTap(list[+btn.dataset.i])));
            if (list.length > 1) {
                const dots = document.createElement('div'); dots.className = 'hs-dots';
                dots.innerHTML = list.map((_, i) => '<i class="' + (i === 0 ? 'on' : '') + '"></i>').join('');
                slider.after(dots);
                slider.addEventListener('scroll', () => {
                    const w = slider.children[0].getBoundingClientRect().width + 12;
                    const i = Math.min(list.length - 1, Math.round(slider.scrollLeft / w));
                    dots.querySelectorAll('i').forEach((x, k) => x.classList.toggle('on', k === i));
                }, { passive: true });
            }
            show(el);
        }

        // ---------- Know your scrap: featured first article + grid
        function renderKnow(sec, list) {
            const el = $('hsKnow');
            if (!sec || !sec.is_active || !list || !list.length) return;
            el.querySelector('[data-t="title"]').textContent = sec.title || '';
            el.querySelector('[data-t="desc"]').textContent = sec.description || '';
            const more = esc(sec.button_text || 'Read more');
            const f = list[0];
            $('hsKnowFeatured').innerHTML = `
                <a class="hs-feat" href="${BLOG_URL}/${f.slug}">
                    <img src="${esc(ASSET + (f.cover_image || ''))}" alt="${esc(f.title)}" onerror="this.style.visibility='hidden'">
                    <div class="hs-cap"><b>${esc(f.title)}</b>${f.summary ? '<small>' + esc(f.summary) + '</small>' : ''}<span class="hs-pill">${more} &rarr;</span></div>
                </a>`;
            $('hsKnowGrid').innerHTML = list.slice(1).map(a => `
                <a class="hs-article" href="${BLOG_URL}/${a.slug}">
                    <img src="${esc(ASSET + (a.cover_image || ''))}" alt="${esc(a.title)}" loading="lazy" onerror="this.style.visibility='hidden'">
                    <div class="hs-a-body"><b>${esc(a.title)}</b>${a.summary ? '<span>' + esc(a.summary) + '</span>' : ''}<em>${more} &rarr;</em></div>
                </a>`).join('');
            show(el);
        }

        // ---------- load content from the admin panel
        fetch(API + '/home-content', { method: 'POST', headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(d => {
                if (!d || d.status !== 1) return;
                const s = d.data.sections || {};
                fields = d.data.bulk_fields || [];
                renderTrending(s.trending, d.data.trending || []);
                renderBulk(s.bulk_enquiry);
                const bn = d.data.banners || {};
                renderSection('hsSellBulk', s.sell_bulk, bn.sell_bulk);
                renderSection('hsVehicle', s.scrap_vehicle, bn.scrap_vehicle);
                renderSection('hsOffers', s.offers, bn.offers);
                renderKnow(s.know_scrap, d.data.articles || []);
            })
            .catch(() => {});
    })();
</script>

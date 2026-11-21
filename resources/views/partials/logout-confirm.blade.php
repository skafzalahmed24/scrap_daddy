<style>
    .sd-logout-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.5); display: none; align-items: center; justify-content: center; z-index: 20000; padding: 16px; }
    .sd-logout-backdrop.show { display: flex; }
    .sd-logout-box { background: #fff; border-radius: 14px; padding: 24px; width: 100%; max-width: 360px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,.25); font-family: inherit; }
    .sd-logout-box .sd-icon { width: 56px; height: 56px; border-radius: 50%; background: #fdecec; color: #dc3545; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 22px; }
    .sd-logout-box h5 { margin: 0 0 6px; font-weight: 700; color: #222; }
    .sd-logout-box p { margin: 0 0 20px; color: #666; font-size: 14px; }
    .sd-logout-actions { display: flex; gap: 10px; }
    .sd-logout-actions button { flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd; background: #fff; font-weight: 600; cursor: pointer; }
    .sd-logout-actions button.sd-confirm { background: #dc3545; border-color: #dc3545; color: #fff; }
</style>
<div class="sd-logout-backdrop" id="sdLogoutModal" role="dialog" aria-modal="true" aria-labelledby="sdLogoutTitle">
    <div class="sd-logout-box">
        <div class="sd-icon"><i class="fa-solid fa-right-from-bracket"></i></div>
        <h5 id="sdLogoutTitle">Log out?</h5>
        <p>Are you sure you want to log out?</p>
        <div class="sd-logout-actions">
            <button type="button" id="sdLogoutCancel">Cancel</button>
            <button type="button" class="sd-confirm" id="sdLogoutConfirm">Logout</button>
        </div>
    </div>
</div>
<script>
    (function () {
        const modal = document.getElementById('sdLogoutModal');
        let pending = null;
        const close = () => { modal.classList.remove('show'); pending = null; };
        window.confirmLogout = function (onConfirm) { pending = onConfirm; modal.classList.add('show'); };
        document.getElementById('sdLogoutCancel').addEventListener('click', close);
        document.getElementById('sdLogoutConfirm').addEventListener('click', () => { const fn = pending; close(); if (fn) fn(); });
        modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
    })();
</script>

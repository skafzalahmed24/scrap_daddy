{{-- Shared toast: window.sdToast(message, 'error'|'success'). Also shows Laravel flash / validation messages. --}}
@once
<style>
    #sdToastBox { position: fixed; top: 16px; left: 50%; transform: translateX(-50%); z-index: 20000; display: flex; flex-direction: column; gap: 8px; width: min(420px, calc(100% - 24px)); pointer-events: none; }
    .sd-toast { pointer-events: auto; display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 12px; background: #fff; color: #222; font-size: .9rem; line-height: 1.35; box-shadow: 0 8px 28px rgba(0,0,0,.18); border-left: 5px solid #dc3545; animation: sdToastIn .25s ease-out both; }
    .sd-toast i { margin-top: 2px; color: #dc3545; }
    .sd-toast.success { border-left-color: #4A9A58; } .sd-toast.success i { color: #4A9A58; }
    .sd-toast .sd-toast-x { margin-left: auto; background: none; border: 0; color: #999; font-size: 1.1rem; line-height: 1; padding: 0 2px; cursor: pointer; }
    @keyframes sdToastIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
</style>
<script>
    window.sdToast = function (message, type) {
        type = type || 'error';
        let box = document.getElementById('sdToastBox');
        if (!box) { box = document.createElement('div'); box.id = 'sdToastBox'; document.body.appendChild(box); }
        const t = document.createElement('div');
        t.className = 'sd-toast ' + type;
        t.setAttribute('role', type === 'error' ? 'alert' : 'status');
        const ic = document.createElement('i'); ic.className = type === 'success' ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-exclamation';
        const tx = document.createElement('span'); tx.textContent = message;
        const x = document.createElement('button'); x.type = 'button'; x.className = 'sd-toast-x'; x.innerHTML = '&times;'; x.setAttribute('aria-label', 'Close');
        x.addEventListener('click', () => t.remove());
        t.append(ic, tx, x);
        box.appendChild(t);
        setTimeout(() => t.remove(), type === 'error' ? 6000 : 4500);
    };
</script>
@endonce
@if(session('success') || session('error') || $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success')) sdToast(@json(session('success')), 'success'); @endif
        @if(session('error')) sdToast(@json(session('error')), 'error'); @endif
        @if($errors->any()) sdToast(@json($errors->first()), 'error'); @endif
    });
</script>
@endif

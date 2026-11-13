@php
    $needsPasswordChange = (bool) session('pasak_dashboard_needs_password_change', false);
    $activeUser = session('pasak_dashboard_user', config('pasak.dashboard.auth.username', 'admin'));
    $hasSecret = !empty(config('pasak.dashboard.secret', env('PASAK_DASHBOARD_SECRET')));
@endphp

<!-- Modal Update Password Dasbor Pasak -->
<div class="sec-modal-backdrop" id="pasakUpdatePasswordModal" {{ $needsPasswordChange ? '' : 'hidden' }} style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); z-index: 99999; display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div class="sec-modal-box" role="dialog" aria-modal="true" aria-labelledby="pasakUpdatePasswordTitle" style="background: var(--sec-card); border: 1px solid var(--sec-border); border-radius: var(--sec-radius); width: 100%; max-width: 520px; box-shadow: 0 20px 40px -15px rgba(0,0,0,0.7); overflow: hidden; display: flex; flex-direction: column; animation: pasakModalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
        <!-- Modal Header -->
        <div class="sec-modal-header" style="display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; padding: 20px 24px; border-bottom: 1px solid var(--sec-border); background: var(--sec-card-hover);">
            <div style="display: flex; gap: 14px; align-items: flex-start;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); display: flex; align-items: center; justify-content: center; color: var(--sec-warning); font-size: 20px; flex-shrink: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        <circle cx="12" cy="16" r="1"/>
                    </svg>
                </div>
                <div>
                    <h3 id="pasakUpdatePasswordTitle" style="font-size: 16px; font-weight: 700; color: var(--sec-text); margin: 0 0 4px 0;">
                        {{ __('Pembaruan Password Dasbor Pasak') }}
                    </h3>
                    <p style="font-size: 12.5px; color: var(--sec-text-muted); margin: 0; line-height: 1.45;">
                        {{ __('Demi keamanan server, perbarui password bawaan (Admin) Anda sekarang.') }}
                    </p>
                </div>
            </div>
            <button type="button" class="sec-btn sec-btn-ghost" onclick="window.pasakClosePasswordModal();" title="{{ __('Tutup') }}" style="width: 32px; height: 32px; padding: 0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: var(--sec-text-muted); border: 1px solid transparent;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <!-- Security Notice Alert -->
        @if ($needsPasswordChange)
        <div style="margin: 16px 24px 0 24px; padding: 12px 14px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: var(--sec-radius-sm); display: flex; gap: 10px; align-items: flex-start; font-size: 12px; color: var(--sec-warning);">
            <span style="font-size: 15px; line-height: 1;">⚠️</span>
            <div style="line-height: 1.45;">
                <strong>{{ __('Peringatan Keamanan:') }}</strong>
                {{ __('Anda berhasil masuk menggunakan password bawaan. Segera ganti dengan password unik agar dasbor terlindungi dari upaya penyusupan.') }}
            </div>
        </div>
        @endif

        <!-- Modal Form Body -->
        <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('pasak.dashboard.password.update') ? route('pasak.dashboard.password.update') : url('/pasak/password') }}" style="padding: 20px 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <!-- Username Info -->
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label style="font-size: 12px; font-weight: 600; color: var(--sec-text-muted);">
                    {{ __('Username Dasbor Aktif') }}
                </label>
                <div style="padding: 9px 12px; background: var(--sec-card-hover); border: 1px solid var(--sec-border); border-radius: var(--sec-radius-sm); font-size: 13px; color: var(--sec-text); font-family: ui-monospace, monospace; display: flex; align-items: center; justify-content: space-between;">
                    <span>{{ $activeUser }}</span>
                    <span style="font-size: 10.5px; color: var(--sec-text-muted); font-family: inherit;">.env: PASAK_DASHBOARD_USERNAME</span>
                </div>
            </div>

            <!-- New Password Input -->
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label for="pasak_new_password" style="font-size: 12px; font-weight: 600; color: var(--sec-text);">
                    {{ __('Password Baru') }} <span style="color: var(--sec-danger);">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input id="pasak_new_password" type="password" name="password" required minlength="6" placeholder="{{ __('Masukkan password baru (minimal 6 karakter)') }}" autocomplete="new-password" style="width: 100%; height: 38px; padding: 0 40px 0 12px; background: var(--sec-card-hover); border: 1px solid var(--sec-border); border-radius: var(--sec-radius-sm); color: var(--sec-text); font-size: 13px; outline: none; transition: border-color 0.15s ease;">
                    <button type="button" onclick="window.pasakTogglePasswordInput('pasak_new_password', this);" title="{{ __('Lihat / Sembunyikan Password') }}" style="position: absolute; right: 4px; width: 32px; height: 30px; background: none; border: none; border-radius: 4px; color: var(--sec-text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Confirm Password Input -->
            <div style="display: flex; flex-direction: column; gap: 6px;">
                <label for="pasak_new_password_confirmation" style="font-size: 12px; font-weight: 600; color: var(--sec-text);">
                    {{ __('Konfirmasi Password Baru') }} <span style="color: var(--sec-danger);">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input id="pasak_new_password_confirmation" type="password" name="password_confirmation" required minlength="6" placeholder="{{ __('Ketik ulang password baru Anda') }}" autocomplete="new-password" style="width: 100%; height: 38px; padding: 0 40px 0 12px; background: var(--sec-card-hover); border: 1px solid var(--sec-border); border-radius: var(--sec-radius-sm); color: var(--sec-text); font-size: 13px; outline: none; transition: border-color 0.15s ease;">
                    <button type="button" onclick="window.pasakTogglePasswordInput('pasak_new_password_confirmation', this);" title="{{ __('Lihat / Sembunyikan Password') }}" style="position: absolute; right: 4px; width: 32px; height: 30px; background: none; border: none; border-radius: 4px; color: var(--sec-text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Format Description & Example -->
            <div style="padding: 10px 12px; background: rgba(59, 130, 246, 0.06); border: 1px dashed rgba(59, 130, 246, 0.25); border-radius: var(--sec-radius-sm); font-size: 11.5px; color: var(--sec-text-muted); line-height: 1.45;">
                <div style="color: var(--sec-primary); font-weight: 600; margin-bottom: 2px;">
                    💡 {{ __('Rekomendasi Format Password:') }}
                </div>
                <div>
                    {{ __('Gunakan minimal 8 karakter (huruf besar, kecil, angka, simbol). Contoh:') }} <code style="font-family: ui-monospace, monospace; color: var(--sec-primary); background: rgba(59,130,246,0.1); padding: 1px 4px; border-radius: 3px;">P@sak#VPS2026!</code>
                </div>
                <div style="margin-top: 4px; font-size: 11px; opacity: 0.9;">
                    🔒 {{ __('Password di-hash secara aman dengan HMAC SHA-256 berbasis') }} <code style="font-family: ui-monospace, monospace; color: var(--sec-primary);">PASAK_DASHBOARD_SECRET</code> {{ __('dan disimpan ke database.') }}
                </div>
            </div>

            <!-- Emergency Recovery Note -->
            <div style="padding: 10px 12px; background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: var(--sec-radius-sm); font-size: 11.5px; color: var(--sec-text-muted); line-height: 1.45;">
                <span style="color: var(--sec-warning); font-weight: 700;">🚨 {{ __('Lupa Password di Masa Depan?') }}</span>
                {{ __('Anda dapat menggunakan nilai PASAK_DASHBOARD_SECRET dari berkas .env sebagai password darurat untuk membuka kembali dasbor dan mereset password.') }}
            </div>

            <!-- Modal Action Buttons -->
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: 6px; padding-top: 14px; border-top: 1px solid var(--sec-border);">
                @if (\Illuminate\Support\Facades\Route::has('pasak.dashboard.settings'))
                <a href="{{ route('pasak.dashboard.settings') }}" class="sec-btn sec-btn-ghost sec-btn-sm" style="font-size: 12px; color: var(--sec-text-muted); text-decoration: none; padding: 0 10px; height: 36px; display: inline-flex; align-items: center; gap: 5px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <span>{{ __('Menu Pengaturan') }}</span>
                </a>
                @else
                <div></div>
                @endif

                <div style="display: flex; align-items: center; gap: 8px;">
                    <button type="button" class="sec-btn sec-btn-ghost sec-btn-sm" onclick="window.pasakClosePasswordModal();" style="font-size: 12px; height: 36px;">
                        {{ __('Ingatkan Nanti') }}
                    </button>
                    <button type="submit" class="sec-btn sec-btn-primary sec-btn-sm" style="font-size: 12px; height: 36px; padding: 0 16px; font-weight: 600;">
                        {{ __('Simpan ke Database') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        window.pasakOpenPasswordModal = function() {
            var modal = document.getElementById('pasakUpdatePasswordModal');
            if (modal) {
                modal.removeAttribute('hidden');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                var passInput = document.getElementById('pasak_new_password');
                if (passInput) passInput.focus();
            }
        };

        window.pasakClosePasswordModal = function() {
            var modal = document.getElementById('pasakUpdatePasswordModal');
            if (modal) {
                modal.setAttribute('hidden', '');
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        };

        window.pasakTogglePasswordInput = function(inputId, btn) {
            var input = document.getElementById(inputId);
            if (!input) return;
            var isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            if (btn) {
                btn.style.color = isPass ? 'var(--sec-primary)' : 'var(--sec-text-muted)';
            }
        };

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                var modal = document.getElementById('pasakUpdatePasswordModal');
                if (modal && !modal.hasAttribute('hidden') && modal.style.display !== 'none') {
                    window.pasakClosePasswordModal();
                }
            }
        });

        // Close on backdrop click
        var modalEl = document.getElementById('pasakUpdatePasswordModal');
        if (modalEl) {
            modalEl.addEventListener('click', function(e) {
                if (e.target === modalEl) {
                    window.pasakClosePasswordModal();
                }
            });
        }
    })();
</script>

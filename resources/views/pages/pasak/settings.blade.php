@extends('pages.pasak.layout', ['heading' => __('Pengaturan Pemblokiran Otomatis (Auto-Block WAF)'), 'subheading' => __('WAF memantau frekuensi ancaman yang dilakukan oleh IP atau perangkat yang sama, lalu menjatuhkan sanksi blokir saat melewati batas toleransi.')])

@section('page_support_key', 'settings')

@section('content')
@php($s = $settings ?? [])
@php($scales = [
    'critical' => ['label' => __('Critical (Sangat Kritis)'), 'icon' => '🚨', 'badge' => __('Langsung Blokir'), 'example' => __('SQL Injection, Command Injection, Webshell, Path Traversal. Sekali menyerang langsung blokir.')],
    'high' => ['label' => __('High (Tinggi)'), 'icon' => '⚠', 'badge' => __('Max 2x'), 'example' => __('Cross-Site Scripting (XSS), SSTI, Template / Expression Injection.')],
    'medium' => ['label' => __('Medium (Sedang)'), 'icon' => 'ⓘ', 'badge' => __('Max 3x'), 'example' => __('Null byte encoding, parameter tampering anomali, payload berbahaya sedang.')],
    'low' => ['label' => __('Low (Rendah)'), 'icon' => '⚡', 'badge' => __('Max 5x'), 'example' => __('User-agent scanner, traffic anomali ringan belum diblokir.')],
])
@php($durationLabel = ['6' => __('6 Jam'), '24' => __('24 Jam (1 Hari)'), '72' => __('3 Hari (72 Jam)'), '168' => __('1 Pekan (7 Hari)'), '504' => __('3 Pekan (21 Hari)'), '720' => __('1 Bulan (30 Hari)'), '8760' => __('1 Tahun (365 Hari)'), '0' => __('Permanen (Selamanya)')])
@php($simScopes = ['device' => __('Hanya perangkat penyerang (pengguna lain di WiFi sama tetap aman)'), 'ip' => __('Seluruh jaringan berbagi IP publik dengan penyerang (HTTP 403)'), 'auto' => __('Perangkat bila fingerprint terdeteksi; fallback ke seluruh IP')])
@php($simLevels = ['critical' => __('Critical'), 'high' => __('High'), 'medium' => __('Medium'), 'low' => __('Low')])
@php($simOrder = ['critical', 'high', 'medium', 'low'])

<form method="POST" action="{{ route('pasak.dashboard.settings.update') }}" id="pasak-settings-form" data-sec-confirm="{{ __('Simpan seluruh perubahan konfigurasi WAF dan penegakan keamanan ke database?') }}" data-confirm-title="{{ __('Konfirmasi Simpan Pengaturan') }}" data-confirm-type="primary" data-confirm-btn="{{ __('Ya, Simpan Pengaturan') }}">
    @csrf
    <input type="hidden" name="auto_block_duration" id="auto_block_duration" value="{{ $s['auto_block_duration'] ?? 24 }}">

<div class="sec-card">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap;">
        <div>
            <h3 class="sec-card-title" style="font-size: 17px;">{{ __('Pengaturan Pemblokiran Otomatis (Auto-Block WAF)') }}</h3>
            <p class="sec-card-desc" style="margin: 6px 0 0 0;">{{ __('WAF memantau frekuensi ancaman yang dilakukan oleh IP atau perangkat yang sama, lalu menjatuhkan sanksi blokir saat melewati batas toleransi.') }}</p>
        </div>
        <label class="sec-switch-wrap">
            <span class="sec-switch">
                <input type="hidden" name="auto_block_enabled" value="0">
                <input type="checkbox" name="auto_block_enabled" id="auto_block_switch" value="1" @checked(! empty($s['auto_block_enabled']))>
                <span class="sec-switch-track"></span>
            </span>
            @php($autoOn = ! empty($s['auto_block_enabled']))
            <span class="sec-switch-label {{ $autoOn ? '' : 'off' }}" id="auto_block_switch_label">{{ $autoOn ? __('Auto-Blokir Aktif') : __('Hanya Pantauan') }}</span>
        </label>
    </div>
</div>

    <div class="sec-card">
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
            <div>
                <h3 class="sec-card-title">{{ __('Cakupan Pemblokiran: IP Router vs Perangkat Asli (Real Device)') }}</h3>
                <p class="sec-card-desc" style="margin: 4px 0 0 0;">{{ __('Pilih apa yang terblokir otomatis: mengunci hanya perangkat penyerang (aman untuk WiFi bersama) atau mengunci seluruh IP router.') }}</p>
            </div>
            <span class="sec-pill sec-pill-info">🛡 {{ __('WiFi-Safe Mode') }}</span>
        </div>

        <div class="sec-scope-grid" style="margin-top: 14px;">
            @foreach (['device', 'ip', 'auto'] as $scopeChoice)
                @php($active = ($s['auto_block_scope'] ?? 'device') === $scopeChoice)
                @php($cls = ['device' => 'active-primary', 'ip' => 'active-warning', 'auto' => 'active-info'][$scopeChoice])
                <label class="sec-scope-box {{ $active ? $cls : '' }}" style="cursor: pointer;">
                    <input type="radio" name="auto_block_scope" value="{{ $scopeChoice }}" @checked($active) data-sec-live style="display: none;">
                    <div class="sec-scope-header">
                        <div class="sec-scope-title-wrap">
                            <span class="sec-scope-title">{{ [__('📱 Hanya Perangkat (Real Device)'), __('🏓 Seluruh IP Router / Publik'), __('⇉ Otomatis / Adaptif')][$loop->index] }}</span>
                        </div>
                    </div>
                    <p class="sec-scope-desc">
                        @if ($scopeChoice === 'device')
                            {{ __('Sangat Aman untuk WiFi / NAT. Hanya perangkat penyerang yang diblokir (Device ID & IP Lokal). Pengguna lain di WiFi yang sama TETAP AMAN.') }}
                        @elseif ($scopeChoice === 'ip')
                            {{ __('Blokir Total Jaringan. Seluruh perangkat dalam satu WiFi atau kantor yang berbagi IP publik ini akan ikut diblokir (HTTP 403).') }}
                        @else
                            {{ __('Mode Cerdas. Jika fingerprint perangkat penyerang terdeteksi, gunakan blokir perangkat; jika tidak terdeteksi, baru blokir seluruh IP router.') }}
                        @endif
                    </p>
                </label>
            @endforeach
        </div>
    </div>

    <div class="sec-card">
        <h3 class="sec-card-title">{{ __('Batas Maksimal Penyerangan Berdasarkan Tingkat Keparahan') }}</h3>
        <p class="sec-card-desc">{{ __('IP atau perangkat akan langsung diblokir secara otomatis jika mencapai batas serangan pada masing-masing level dalam jendela waktu pemantauan.') }}</p>

        <div class="sec-level-grid" data-sec-sim-source>
            @foreach ($scales as $lvl => $meta)
                <div class="sec-level-card {{ $lvl }}">
                    <div class="sec-level-head">
                        <span class="sec-level-title">{{ $meta['icon'] }} {{ __(':level ( :desc )', ['level' => __($lvl), 'desc' => $meta['label']]) }}</span>
                        <span class="sec-level-badge {{ $lvl }}">{{ $meta['badge'] }}</span>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="number" class="sec-level-input" name="auto_block_level_thresholds[{{ $lvl }}]"
                            min="1" max="100" value="{{ ($s['auto_block_level_thresholds'][$lvl] ?? ($lvl === 'critical' ? 1 : ($lvl === 'high' ? 2 : ($lvl === 'medium' ? 3 : 5)))) }}"
                            data-sec-sim-level="{{ $lvl }}" style="max-width: 130px;">
                        <span style="font-size: 13px; color: var(--sec-text-muted); font-weight: 600;">{{ __('kali') }}</span>
                    </div>
                    <p class="sec-level-desc">{{ $meta['example'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        <div class="sec-card" style="margin-bottom: 0;">
            <div class="sec-label" style="font-weight: 700;">{{ __('Jendela Waktu Pemantauan') }}</div>
            <p class="sec-card-desc" style="margin-top: 6px;">{{ __('Rentang waktu untuk menghitung akumulasi insiden penyerangan. Standar: 10 menit.') }}</p>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input id="auto_block_window" type="number" name="auto_block_window" min="1" max="1440" value="{{ $s['auto_block_window'] ?? 10 }}" class="sec-level-input" data-sec-sim-window style="max-width: 140px;">
                <span style="font-size: 13px; color: var(--sec-text-muted); font-weight: 600;">{{ __('menit') }}</span>
            </div>
        </div>

        <div class="sec-card" style="margin-bottom: 0;">
            <div class="sec-label" style="font-weight: 700;">{{ __('Durasi Blokir Otomatis') }}</div>
            <p class="sec-card-desc" style="margin-top: 6px;">{{ __('Lama waktu pemblokiran berlaku (0 = Blokir permanen selamanya).') }}</p>
            <select id="auto_block_duration_select" class="sec-select-input" style="max-width: 240px;">
                @foreach ($durationLabel as $val => $label)
                    <option value="{{ $val }}" @selected(($s['auto_block_duration'] ?? 24) == $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="sec-card" style="margin-bottom: 0;">
            <div class="sec-label" style="font-weight: 700;">{{ __('Threshold Kombinasi / Fallback') }}</div>
            <p class="sec-card-desc" style="margin-top: 6px;">{{ __('Batas serangan campuran jika beberapa level berbeda terjadi bersamaan. Standar: 3x.') }}</p>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input id="auto_block_combined_threshold_field" type="number" name="auto_block_combined_threshold" min="1" max="100" value="{{ $s['auto_block_combined_threshold'] ?? 3 }}" class="sec-level-input" data-sec-sim-combined style="max-width: 140px;">
                <span style="font-size: 13px; color: var(--sec-text-muted); font-weight: 600;">{{ __('kali') }}</span>
            </div>
        </div>
    </div>

    <div style="height: 20px;"></div>

    <div class="sec-card">
        <h3 class="sec-card-title">{{ __('Aktifkan Pengawasan Pada Level Ancaman Berikut') }}</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 14px; margin-top: 14px;">
            @foreach (['critical', 'high', 'medium', 'low'] as $lvl)
                @php($meta = $scales[$lvl])
                @php($watched = in_array($lvl, $s['auto_block_levels'] ?? ['high', 'critical'], true))
                <label class="sec-level-check {{ $watched ? 'checked' : '' }}">
                    <input type="checkbox" name="auto_block_levels[]" value="{{ $lvl }}" @checked($watched) data-sec-watch>
                    <span>
                        <span style="display: block; font-weight: 700; font-size: 14px;">{{ $meta['icon'] }} {{ $meta['label'] }}</span>
                        <span style="display: block; font-size: 12px; color: var(--sec-text-muted); margin-top: 2px;">{{ $meta['example'] }}</span>
                    </span>
                </label>
            @endforeach
        </div>
    </div>

    <div class="sec-card">
        <div class="sec-level-head" style="border-bottom: 1px solid var(--sec-border); padding-bottom: 12px; margin-bottom: 14px;">
            <span style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700;">
                ⚡ {{ __('Blokir instan (Zero Tolerance)') }}
            </span>
            @php($instantOn = ! empty($s['instant_block_enabled']))
            <label class="sec-switch-wrap">
                <span class="sec-switch">
                    <input type="hidden" name="instant_block_enabled" value="0">
                    <input type="checkbox" name="instant_block_enabled" id="instant_block_switch" value="1" @checked($instantOn)>
                    <span class="sec-switch-track"></span>
                </span>
                <span class="sec-switch-label {{ $instantOn ? '' : 'off' }}" id="instant_block_switch_label">{{ $instantOn ? __('Blokir Instan Aktif') : __('Hanya Rekam') }}</span>
            </label>
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px;">
            <div>
                <span class="sec-label">{{ __('Instant block scope') }}</span>
                <select name="instant_block_scope" class="sec-select-input" style="max-width: 280px;">
                    <option value="device" @selected(($s['instant_block_scope'] ?? 'device') === 'device')>{{ __('📱 Isolasi Perangkat Saja (aman WiFi bersama)') }}</option>
                    <option value="ip" @selected(($s['instant_block_scope'] ?? 'device') === 'ip')>{{ __('🌐 Seluruh IP Router Publik') }}</option>
                </select>
            </div>
            <div>
                <span class="sec-label">{{ __('Instant block duration (hours, 0 = permanent)') }}</span>
                <input type="number" name="instant_block_duration" min="0" max="8760" value="{{ $s['instant_block_duration'] ?? 720 }}" class="sec-level-input" style="max-width: 170px;">
            </div>
            <div>
                <label class="sec-label">
                    <input type="hidden" name="block_enforcement" value="0">
                    <input type="checkbox" name="block_enforcement" value="1" @checked(! empty($s['block_enforcement']))>
                    {{ __('Enforce blocks (return HTTP 403)') }}
                </label>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <div class="sec-card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap;">
                <div>
                    <h3 class="sec-card-title" style="font-size: 15px;">🔒 {{ __('Proteksi Brute Force & Stepped Login Cooldown') }}</h3>
                    <p class="sec-card-desc" style="margin-top: 4px; font-size: 12px;">{{ __('Membatasi percobaan login gagal dengan jeda bertingkat (Rate Limit Countdown 429), terpisah dari blokir keras WAF 403.') }}</p>
                </div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    <span class="sec-badge" style="background: rgba(6, 182, 212, .12); color: #06b6d4; border: 1px solid rgba(6, 182, 212, .3); font-size: 11px;">1x-3x: 30s</span>
                    <span class="sec-badge" style="background: rgba(6, 182, 212, .12); color: #06b6d4; border: 1px solid rgba(6, 182, 212, .3); font-size: 11px;">+1x: 60s</span>
                    <span class="sec-badge" style="background: rgba(6, 182, 212, .12); color: #06b6d4; border: 1px solid rgba(6, 182, 212, .3); font-size: 11px;">+1x: 120s</span>
                    <span class="sec-badge" style="background: rgba(245, 158, 11, .12); color: #f59e0b; border: 1px solid rgba(245, 158, 11, .3); font-size: 11px;">Reset: 6 Jam</span>
                </div>
            </div>

            <label style="display: flex; align-items: center; gap: 8px; margin: 12px 0 16px 0;">
                <input type="hidden" name="login_lockout_enabled" value="0">
                <input type="checkbox" name="login_lockout_enabled" value="1" @checked(! empty($s['login_lockout_enabled']))>
                <span style="font-weight: 600; font-size: 13.5px;">{{ __('Aktifkan jeda bertingkat (cooldown 30s, 60s, 120s dengan masa reset 6 jam)') }}</span>
            </label>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px;">
                <div>
                    <span class="sec-label" style="font-size: 11.5px;">{{ __('Ambang Gagal Awal') }}</span>
                    <input type="number" name="login_lockout_threshold" min="1" max="100" value="{{ $s['login_lockout_threshold'] ?? 3 }}" class="sec-level-input" title="{{ __('Jumlah kegagalan awal sebelum terkena jeda pertama (default: 3 kali)') }}">
                    <span style="font-size: 10.5px; color: var(--sec-text-muted);">{{ __('x percobaan') }}</span>
                </div>
                <div>
                    <span class="sec-label" style="font-size: 11.5px;">{{ __('Jeda Tahap 1 (Detik)') }}</span>
                    <input type="number" name="login_lockout_step1_seconds" min="1" max="86400" value="{{ $s['login_lockout_step1_seconds'] ?? 30 }}" class="sec-level-input">
                    <span style="font-size: 10.5px; color: var(--sec-text-muted);">{{ __('detik (default: 30s)') }}</span>
                </div>
                <div>
                    <span class="sec-label" style="font-size: 11.5px;">{{ __('Jeda Tahap 2 (Detik)') }}</span>
                    <input type="number" name="login_lockout_step2_seconds" min="1" max="86400" value="{{ $s['login_lockout_step2_seconds'] ?? 60 }}" class="sec-level-input">
                    <span style="font-size: 10.5px; color: var(--sec-text-muted);">{{ __('detik (default: 60s)') }}</span>
                </div>
                <div>
                    <span class="sec-label" style="font-size: 11.5px;">{{ __('Jeda Tahap 3 (Detik)') }}</span>
                    <input type="number" name="login_lockout_step3_seconds" min="1" max="86400" value="{{ $s['login_lockout_step3_seconds'] ?? 120 }}" class="sec-level-input">
                    <span style="font-size: 10.5px; color: var(--sec-text-muted);">{{ __('detik (default: 120s)') }}</span>
                </div>
                <div>
                    <span class="sec-label" style="font-size: 11.5px;">{{ __('Masa Reset Siklus') }}</span>
                    <input type="number" name="login_lockout_decay_minutes" min="1" max="10080" value="{{ $s['login_lockout_decay_minutes'] ?? 360 }}" class="sec-level-input">
                    <span style="font-size: 10.5px; color: var(--sec-text-muted);">{{ __('menit (360m = 6 jam)') }}</span>
                </div>
            </div>

            <input type="hidden" name="login_lockout_base_minutes" value="{{ $s['login_lockout_base_minutes'] ?? 1 }}">
            <input type="hidden" name="login_lockout_max_minutes" value="{{ $s['login_lockout_max_minutes'] ?? 30 }}">
            <input type="hidden" name="login_lockout_max_seconds" value="{{ $s['login_lockout_max_seconds'] ?? 3600 }}">

            <div style="margin-top: 14px; padding: 10px 12px; border-radius: var(--sec-radius-sm); background: var(--sec-surface); border: 1px solid var(--sec-border); font-size: 11.5px; color: var(--sec-text-muted); line-height: 1.5;">
                💡 <b>{{ __('Alur Jeda Manusiawi:') }}</b> {{ __('Bila pengguna salah sandi :threshold kali, sistem mengunci sementara selama :step1 detik dengan tampilan countdown interaktif. Jika gagal lagi, sanksi naik menjadi :step2 detik lalu :step3 detik. Riwayat direset otomatis ke nol setelah :hours jam tanpa kegagalan atau begitu pengguna berhasil login.', ['threshold' => $s['login_lockout_threshold'] ?? 3, 'step1' => $s['login_lockout_step1_seconds'] ?? 30, 'step2' => $s['login_lockout_step2_seconds'] ?? 60, 'step3' => $s['login_lockout_step3_seconds'] ?? 120, 'hours' => round(($s['login_lockout_decay_minutes'] ?? 360) / 60)]) }}
            </div>
        </div>

        <div class="sec-card" style="margin-bottom: 0;">
            <h3 class="sec-card-title" style="font-size: 15px;">🗄 {{ __('Retensi Log Audit') }}</h3>
            <p class="sec-card-desc" style="margin-top: 6px;">{{ __('Masa berlaku log keamanan disimpan sebelum dibersihkan otomatis (perintah prune berjalan via scheduler).') }}</p>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="number" name="log_retention_days" min="0" max="3650" value="{{ $s['log_retention_days'] ?? 90 }}" class="sec-level-input" style="max-width: 140px;">
                <span style="font-size: 13px; color: var(--sec-text-muted); font-weight: 600;">{{ __('hari (0 = simpan selamanya)') }}</span>
            </div>
        </div>
    </div>

    <div style="height: 20px;"></div>

    <div class="sec-card">
        <h3 class="sec-card-title" style="font-size: 15px;">🌐 {{ __('Integrasi Navigasi: URL Kembali ke Dashboard Aplikasi Host') }}</h3>
        <p class="sec-card-desc" style="margin-top: 6px;">{{ __('Tautan tombol "Dashboard Utama" pada header dan navigasi samping untuk kembali ke aplikasi utama atau dashboard admin pengguna. Jika dikosongkan, defaultnya adalah "/".') }}</p>
        <div style="display: flex; gap: 10px; align-items: center; max-width: 520px;">
            <input type="text" name="back_url" id="pasak_back_url" value="{{ $s['back_url'] ?? '/' }}" placeholder="/" class="sec-level-input" style="font-family: var(--sec-font-mono);">
            <a href="{{ $s['back_url'] ?? '/' }}" target="_blank" class="sec-btn sec-btn-secondary sec-btn-sm" style="height: 36px;" title="{{ __('Uji buka tautan') }}">🔗 {{ __('Uji Tautan') }}</a>
        </div>
    </div>

    <div style="height: 20px;"></div>

    <div class="sec-card" style="background: var(--sec-warning-bg); border-color: var(--sec-warning-border);">
        <span style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: var(--sec-warning);">
            ⚠ {{ __('Ringkasan Simulasi Kebijakan Auto-Blokir Aktif:') }}
        </span>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 12px; margin-top: 12px;">
            <div style="border: 1px solid var(--sec-warning-border); border-radius: var(--sec-radius-sm); background: var(--sec-card); padding: 12px 14px;">
                <span style="font-size: 11px; font-weight: 700; color: var(--sec-warning); text-transform: uppercase;">{{ __('Cakupan Keamanan:') }}</span>
                <div id="pasak-sim-scope" style="font-weight: 700; font-size: 14px; margin-top: 4px; color: var(--sec-text);">—</div>
            </div>
            <div style="border: 1px solid var(--sec-warning-border); border-radius: var(--sec-radius-sm); background: var(--sec-card); padding: 12px 14px;">
                <span style="font-size: 11px; font-weight: 700; color: var(--sec-warning); text-transform: uppercase;">{{ __('Durasi Sanksi Blokir:') }}</span>
                <div id="pasak-sim-duration" style="font-weight: 700; font-size: 14px; margin-top: 4px; color: var(--sec-text);">—</div>
            </div>
        </div>
        <p id="pasak-sim-summary" style="font-size: 13px; margin: 12px 0 8px 0; color: var(--sec-warning);">—</p>
        <div id="pasak-sim-chips" style="display: flex; gap: 8px; flex-wrap: wrap;"></div>
    </div>

    <div style="position: sticky; bottom: 0; padding: 12px 0; background: linear-gradient(to top, var(--sec-bg) 75%, transparent);">
        <button type="submit" class="sec-btn sec-btn-primary">💾 {{ __('Save settings') }}</button>
    </div>
</form>

<div class="sec-card" id="password-section" style="margin-top: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
        <div>
            <h3 class="sec-card-title" style="font-size: 16px;">🔑 {{ __('Kredensial & Pembaruan Password Dasbor Pasak') }}</h3>
            <p class="sec-card-desc" style="margin-top: 6px;">{{ __('Password akses ke dasbor disimpan di database dan di-hash aman menggunakan HMAC SHA-256 berbasis PASAK_DASHBOARD_SECRET.') }}</p>
        </div>
        @if (\Pasak\SecureMonitor\Support\PasakDashboardSecret::isPasswordStoredInDatabase())
            <span class="sec-pill sec-pill-success">✓ {{ __('Password Kustom Aktif') }}</span>
        @else
            <span class="sec-pill sec-pill-warning">⚠ {{ __('Password Bawaan (Admin)') }}</span>
        @endif
    </div>

    <!-- Kotak Info Username & Status -->
    <div style="margin-top: 16px; padding: 14px 16px; background: var(--sec-card-hover); border: 1px solid var(--sec-border); border-radius: var(--sec-radius-sm); display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
        <div>
            <div style="font-size: 13px; font-weight: 600; color: var(--sec-text);">
                {{ __('Username Dasbor:') }} <span style="font-family: var(--sec-font-mono); color: var(--sec-primary);">{{ config('pasak.dashboard.auth.username', env('PASAK_DASHBOARD_USERNAME', 'admin')) }}</span>
            </div>
            <div style="font-size: 12px; color: var(--sec-text-muted); margin-top: 4px;">
                @if (\Pasak\SecureMonitor\Support\PasakDashboardSecret::isPasswordStoredInDatabase())
                    {{ __('Status: Password telah diperbarui dan di-hash aman di tabel pasak_settings database.') }}
                @else
                    <span style="color: var(--sec-warning); font-weight: 600;">{{ __('Status: Masih menggunakan password bawaan (Admin). Sangat disarankan segera diperbarui!') }}</span>
                @endif
            </div>
        </div>
        <button type="button" class="sec-btn sec-btn-ghost sec-btn-sm" onclick="window.pasakOpenPasswordModal ? window.pasakOpenPasswordModal() : null;" style="height: 32px; font-size: 12px;">
            🗔 {{ __('Buka via Modal Pop-up') }}
        </button>
    </div>

    <!-- Form Inline Pembaruan Password -->
    <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('pasak.dashboard.password.update') ? route('pasak.dashboard.password.update') : url('/pasak/password') }}" style="margin-top: 20px;" data-sec-confirm="{{ __('Simpan dan terapkan password baru untuk dasbor Pasak?') }}" data-confirm-title="{{ __('Konfirmasi Ganti Password') }}" data-confirm-type="warning" data-confirm-btn="{{ __('Ya, Terapkan Password') }}">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            <!-- Password Baru -->
            <div>
                <label for="settings_new_password" class="sec-label" style="font-weight: 600; font-size: 13px; margin-bottom: 6px; display: flex; justify-content: space-between;">
                    <span>{{ __('Password Baru') }} <span style="color: var(--sec-danger);">*</span></span>
                    <span style="font-size: 11px; color: var(--sec-text-muted); font-weight: normal;">Min. 6 karakter</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" name="password" id="settings_new_password" required minlength="6" placeholder="{{ __('Ketik password baru Anda') }}" class="sec-level-input" style="padding-right: 40px; width: 100%;">
                    <button type="button" onclick="window.pasakTogglePasswordInput ? window.pasakTogglePasswordInput('settings_new_password', this) : null;" title="{{ __('Lihat / Sembunyikan Password') }}" style="position: absolute; right: 6px; width: 30px; height: 28px; background: none; border: none; color: var(--sec-text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password Baru -->
            <div>
                <label for="settings_confirm_password" class="sec-label" style="font-weight: 600; font-size: 13px; margin-bottom: 6px;">
                    {{ __('Konfirmasi Password Baru') }} <span style="color: var(--sec-danger);">*</span>
                </label>
                <div style="position: relative; display: flex; align-items: center;">
                    <input type="password" name="password_confirmation" id="settings_confirm_password" required minlength="6" placeholder="{{ __('Ketik ulang password baru Anda') }}" class="sec-level-input" style="padding-right: 40px; width: 100%;">
                    <button type="button" onclick="window.pasakTogglePasswordInput ? window.pasakTogglePasswordInput('settings_confirm_password', this) : null;" title="{{ __('Lihat / Sembunyikan Password') }}" style="position: absolute; right: 6px; width: 30px; height: 28px; background: none; border: none; color: var(--sec-text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Deskripsi & Contoh Pengisian Password Kuat -->
        <div style="margin-top: 14px; padding: 12px 14px; background: rgba(59, 130, 246, 0.05); border: 1px dashed rgba(59, 130, 246, 0.25); border-radius: var(--sec-radius-sm); font-size: 12px; color: var(--sec-text-muted); line-height: 1.5;">
            <div style="color: var(--sec-primary); font-weight: 600; margin-bottom: 4px;">
                💡 {{ __('Deskripsi & Rekomendasi Format Password:') }}
            </div>
            <div>
                {{ __('Gunakan kombinasi minimal 8 karakter yang memadukan huruf besar (A-Z), huruf kecil (a-z), angka (0-9), dan simbol khusus (@, #, $, !, %, *).') }}
            </div>
            <div style="margin-top: 4px; font-family: var(--sec-font-mono); color: var(--sec-text);">
                {{ __('Contoh password kuat:') }} <code style="color: var(--sec-primary); background: rgba(59,130,246,0.1); padding: 2px 6px; border-radius: 4px;">P@sak#VPS2026!</code> {{ __('atau') }} <code style="color: var(--sec-primary); background: rgba(59,130,246,0.1); padding: 2px 6px; border-radius: 4px;">KunciCyber_99#</code>
            </div>
        </div>

        <!-- Panduan Pemulihan Darurat (Emergency Recovery Guide) -->
        <div style="margin-top: 12px; padding: 12px 14px; background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: var(--sec-radius-sm); font-size: 12px; color: var(--sec-text-muted); line-height: 1.5;">
            <div style="color: var(--sec-warning); font-weight: 700; margin-bottom: 4px;">
                🚨 {{ __('Panduan Pemulihan Jika Lupa Password di Masa Depan:') }}
            </div>
            <div>
                {{ __('Jika sewaktu-waktu Anda lupa password dasbor, buka berkas') }} <code style="font-family: var(--sec-font-mono); color: var(--sec-warning);">.env</code> {{ __('pada server Anda, salin nilai') }} <code style="font-family: var(--sec-font-mono); color: var(--sec-warning);">PASAK_DASHBOARD_SECRET</code>, {{ __('dan masukkan nilai tersebut langsung pada kolom password di formulir login. Anda akan otomatis masuk dalam Mode Pemulihan Darurat untuk mereset kembali password dasbor Anda di halaman ini.') }}
            </div>
        </div>

        <!-- Tombol Submit -->
        <div style="margin-top: 16px; display: flex; justify-content: flex-end;">
            <button type="submit" class="sec-btn sec-btn-primary" style="height: 38px; padding: 0 18px; font-weight: 600;">
                💾 {{ __('Simpan Password Baru ke Database') }}
            </button>
        </div>
    </form>
</div>

<script>
(function () {
    var refs = {
        scopes: @json($simScopes),
        durations: @json($durationLabel),
        levels: @json($simLevels),
        order: @json($simOrder),
        formatWindowSentence: @json(__('Dalam kurun waktu :window menit, sistem akan otomatis memblokir jika penyerangan mencapai salah satu dari:')),
        monitorOnly: @json(__('Mode pantauan (hanya catat, tanpa blokir).')),
        durationPerma: @json(__('Permanen (Selamanya)')),
        durationHours: @json(__('Jam')),
        durationDays: @json(__('Hari')),
        combinedLabel: @json(__('Kombinasi:')),
    };

    var sim = {
        scope: document.getElementById('pasak-sim-scope'),
        duration: document.getElementById('pasak-sim-duration'),
        summary: document.getElementById('pasak-sim-summary'),
        chips: document.getElementById('pasak-sim-chips'),
    };

    function val(sel, fallback) {
        var el = document.querySelector(sel);

        return el ? String(el.value || fallback) : fallback;
    }

    function durationText(hours) {
        if (refs.durations[hours]) {
            return refs.durations[hours] + ' [' + @json(__('Blokir response HTTP 403')) + ']';
        }

        var days = Math.round((hours / 24) * 10) / 10;

        return hours + ' ' + refs.durationHours + (days > 0 ? ' (' + refs.durationDays + ': ' + days + ')' : '') + ' [Blokir response HTTP 403]';
    }

    function updateSwitch(checkId, labelId, onText, offText) {
        var box = document.getElementById(checkId);
        var label = document.getElementById(labelId);

        if (! box || ! label) {
            return;
        }

        label.textContent = box.checked ? onText : offText;
        label.className = 'sec-switch-label' + (box.checked ? '' : ' off');
    }

    function update() {
        var scopeEl = document.querySelector('input[name="auto_block_scope"]:checked');
        var windowMin = val('input[data-sec-sim-window]', '10');
        var durHours = val('#auto_block_duration', '24');
        var combined = val('input[data-sec-sim-combined]', '3');

        var scopeText = sim.scope;

        if (scopeText) {
            scopeText.textContent = refs.scopes[(scopeEl ? scopeEl.value : 'device')] || 'device';
        }

        if (sim.duration) {
            sim.duration.textContent = durationText(durHours === '0' ? '0' : durHours);
        }

        var prefix = '';
        var main = document.getElementById('auto_block_switch');

        if (main && ! main.checked) {
            prefix = refs.monitorOnly + ' ';
        }

        if (sim.summary) {
            sim.summary.textContent = prefix + refs.formatWindowSentence.replace(':window', windowMin);
        }

        if (sim.chips) {
            sim.chips.innerHTML = '';

            refs.order.forEach(function (lvl) {
                var watch = document.querySelector('input[name="auto_block_levels[]"][value="' + lvl + '"]');
                var threshold = document.querySelector('input[data-sec-sim-level="' + lvl + '"]');
                var watchOn = document.getElementById('auto_block_switch');

                if (watch && threshold && (! watchOn || watchOn.checked)) {
                    var chip = document.createElement('span');
                    chip.className = 'sec-sim-chip ' + lvl;
                    chip.textContent = refs.levels[lvl] + ': ' + (watch.checked ? threshold.value + 'x' : refs.combinedLabel + ' ' + combined + 'x');
                    sim.chips.appendChild(chip);
                }
            });

            var combinedSpan = document.createElement('span');
            combinedSpan.className = 'sec-sim-chip combined';
            combinedSpan.textContent = refs.combinedLabel + ' ' + combined + 'x';
            sim.chips.appendChild(combinedSpan);
        }

        updateSwitch('auto_block_switch', 'auto_block_switch_label', @json(__('Auto-Blokir Aktif')), @json(__('Hanya Pantauan')));
        updateSwitch('instant_block_switch', 'instant_block_switch_label', @json(__('Blokir Instan Aktif')), @json(__('Hanya Rekam')));

        document.querySelectorAll('input[data-sec-watch]').forEach(function (box) {
            box.closest('.sec-level-check').classList.toggle('checked', box.checked);
        });
    }

    var hiddenDuration = document.getElementById('auto_block_duration');

    document.getElementById('auto_block_duration_select').addEventListener('change', function () {
        hiddenDuration.value = this.value;
        update();
    });

    document.querySelectorAll('input[name], select[name="instant_block_scope"]').forEach(function (el) {
        var evt = (el.type === 'checkbox' || el.type === 'radio' || el.tagName === 'SELECT') ? 'change' : 'input';
        el.addEventListener(evt, update);
    });

    update();
})();
</script>
@endsection

@extends('pages.pasak.layout', ['heading' => __('Live Threat Map'), 'subheading' => __('Visualisasi intelijen serangan siber real-time berbasis telemetri server')])

@section('page_support_key', 'threat-map')

@section('content')
@php($payloadData = $payload)
@php($s = $stats)
@php($target = $payloadData['target'] ?? ['name' => 'Pekanbaru, Riau (ID)', 'lat' => 0.5071, 'lon' => 101.4478])
@php($enforcement = !empty($payloadData['enforcement']))

{{-- Cyber Threat Map Container (ThreatsEye SOC Style) --}}
<div class="sec-card sec-card-flush threat-map-card" data-threat-map>
    {{-- 1. Top Tactical HUD Status Bar --}}
    <div class="threat-hud-bar">
        <div class="threat-hud-left">
            <div class="threat-live-indicator">
                <span class="threat-live-beacon"></span>
                <span class="threat-live-label">LIVE RADAR STREAMING</span>
            </div>
            <div class="threat-target-badge" title="{{ __('Server Target yang Dilindungi WAF') }}">
                <span class="threat-hud-icon">🛡️</span>
                <span class="threat-hud-text">NODE:</span>
                <strong class="threat-target-name" data-map-target>{{ $target['name'] }}</strong>
                <span class="threat-target-coords">[{{ number_format($target['lat'], 4) }}° N, {{ number_format($target['lon'], 4) }}° E]</span>
            </div>
            <div class="threat-mode-badge {{ $enforcement ? 'is-enforcing' : 'is-monitoring' }}">
                <span class="threat-mode-dot">●</span>
                <span>{{ $enforcement ? __('WAF BLOCKING ACTIVE (HTTP 403)') : __('MONITORING ONLY (PASSIVE LOG)') }}</span>
            </div>
        </div>

        <div class="threat-hud-right">
            <div class="threat-hud-clock" data-threat-clock>
                <span class="threat-clock-time">--:--:--</span>
                <span class="threat-clock-tz">{{ \Internal\Pasak\Support\DashboardView::timezone() }}</span>
            </div>
            <div class="threat-hud-stat is-danger">
                <span class="threat-hud-stat-label">{{ ($s['logs_today'] ?? 0) > 0 ? __('Attacks (Today)') : __('Total Attacks') }}</span>
                <span class="threat-hud-stat-val" data-map-attacks>{{ number_format(($s['logs_today'] ?? 0) > 0 ? $s['logs_today'] : ($s['total_logs'] ?? count($payloadData['recent_logs'] ?? []))) }}</span>
            </div>
            <div class="threat-hud-stat is-critical">
                <span class="threat-hud-stat-label">{{ __('Critical') }}</span>
                <span class="threat-hud-stat-val">{{ number_format(($s['critical_today'] ?? 0) > 0 ? $s['critical_today'] : ($s['total_critical'] ?? 0)) }}</span>
            </div>
            <div class="threat-hud-stat is-warning">
                <span class="threat-hud-stat-label">{{ __('Quarantined') }}</span>
                <span class="threat-hud-stat-val">{{ number_format($s['active_blocks'] ?? 0) }}</span>
            </div>
            <div class="threat-hud-stat is-cyan">
                <span class="threat-hud-stat-label">{{ __('Unique IPs') }}</span>
                <span class="threat-hud-stat-val">{{ number_format(($s['unique_ips_today'] ?? 0) > 0 ? $s['unique_ips_today'] : count($payloadData['origins'] ?? [])) }}</span>
            </div>
        </div>
    </div>

    {{-- 2. Main Viewport (Canvas + Tactical Sidebar) --}}
    <div class="threat-map-wrap">
        {{-- Canvas Viewport --}}
        <div class="threat-map-canvas-holder">
            {{-- Background Tactical Grid & Crosshair Lines --}}
            <div class="threat-grid-overlay" aria-hidden="true"></div>

            <canvas data-map-base></canvas>
            <canvas data-map-anim></canvas>

            {{-- Floating Tactical Viewport Controls HUD --}}
            <div class="threat-controls-hud">
                {{-- Projection Mode Switcher: 3D Globe vs 2D Tactical Map --}}
                <div class="threat-ctrl-group">
                    <button type="button" class="threat-ctrl-btn active" data-map-proj="orthographic" title="{{ __('3D Orthographic Globe') }}">
                        <span>🌐 3D Globe</span>
                    </button>
                    <button type="button" class="threat-ctrl-btn" data-map-proj="equirectangular" title="{{ __('2D Tactical Flat Map') }}">
                        <span>🗺️ 2D Flat</span>
                    </button>
                </div>

                {{-- Map Navigation (Zoom / Rotate / Reset) --}}
                <div class="threat-ctrl-group">
                    <button type="button" class="threat-ctrl-btn" data-map-action="zoom-in" title="{{ __('Zoom In (+)') }}">➕</button>
                    <button type="button" class="threat-ctrl-btn" data-map-action="zoom-out" title="{{ __('Zoom Out (-)') }}">➖</button>
                    <button type="button" class="threat-ctrl-btn" data-map-action="reset" title="{{ __('Pusatkan Pandangan ke Indonesia') }}">🎯 <span class="threat-btn-txt">{{ __('Pusat ID') }}</span></button>
                    <button type="button" class="threat-ctrl-btn active" data-map-action="toggle-rotate" title="{{ __('Jeda / Putar Animasi Rotasi Globe') }}">🔄 <span class="threat-btn-txt" data-rotate-label>{{ __('Spin: ON') }}</span></button>
                    <button type="button" class="threat-ctrl-btn active" data-map-action="toggle-stream" title="{{ __('Jeda / Lanjutkan Aliran Animasi Serangan Radar') }}">⚡ <span class="threat-btn-txt" data-stream-label>{{ __('Radar: LIVE') }}</span></button>
                </div>

                {{-- Instant Refresh Radar Telemetry --}}
                <div class="threat-ctrl-group">
                    <button type="button" class="threat-ctrl-btn" data-threat-force-refresh title="{{ __('Refresh Data Radar Seketika') }}">
                        <span class="threat-refresh-icon">🔄</span> <span class="threat-btn-txt">{{ __('Refresh') }}</span>
                    </button>
                </div>

                {{-- Sound FX Toggle (Synthetic Cyber Radar Ping) --}}
                <div class="threat-ctrl-group">
                    <button type="button" class="threat-ctrl-btn" data-map-sound title="{{ __('Audio Radar Pings') }}">
                        <span data-sound-icon>🔇</span> <span class="threat-btn-txt" data-sound-label>Mute</span>
                    </button>
                </div>

                {{-- Panel Telemetri Sidebar Toggle --}}
                <div class="threat-ctrl-group">
                    <button type="button" class="threat-ctrl-btn active" data-map-action="toggle-sidebar" title="{{ __('Sembunyikan / Tampilkan Panel Telemetri') }}">
                        <span class="threat-sidebar-icon">📊</span> <span class="threat-btn-txt">{{ __('Panel') }}</span>
                    </button>
                </div>

                {{-- Fullscreen Wallboard Button --}}
                <div class="threat-ctrl-group">
                    <button
                        type="button"
                        class="threat-fullscreen-btn"
                        data-map-fullscreen
                        title="{{ __('Tampilan Layar Penuh (SOC Wallboard)') }}"
                        aria-pressed="false"
                    >⛶ <span class="threat-fullscreen-text">{{ __('Full screen') }}</span></button>
                </div>
            </div>

            {{-- Severity Filter Bar Overlay (Bottom Left of Canvas) --}}
            <div class="threat-filter-overlay">
                <span class="threat-filter-title">{{ __('Filter Severity:') }}</span>
                <button type="button" class="threat-filter-pill active" data-filter-level="all">{{ __('ALL') }}</button>
                <button type="button" class="threat-filter-pill is-critical" data-filter-level="critical">● {{ __('CRITICAL') }}</button>
                <button type="button" class="threat-filter-pill is-high" data-filter-level="high">● {{ __('HIGH') }}</button>
                <button type="button" class="threat-filter-pill is-medium" data-filter-level="medium">● {{ __('MEDIUM') }}</button>
                <button type="button" class="threat-filter-pill is-low" data-filter-level="low">● {{ __('LOW') }}</button>
            </div>



            {{-- Offline / Fallback Notice (Non-blocking tactical toast) --}}
            <div class="threat-map-offline" data-map-offline hidden>
                <div class="threat-offline-box">
                    <span style="font-size: 20px;">🛰️</span>
                    <div>
                        <strong>{{ __('Mode Radar Mandiri (Offline Resilient)') }}</strong>
                        <p>{{ __('Visualisasi radar lokal aktif dengan kontur benua mandiri. Telemetri serangan dan mitigasi WAF tetap berjalan real-time.') }}</p>
                    </div>
                </div>
                <button type="button" class="threat-offline-close" data-offline-dismiss title="{{ __('Tutup') }}">✕</button>
            </div>
        </div>

        {{-- Tactical Sidebar (ThreatsEye Intelligence Telemetry) --}}
        <aside class="threat-map-side">
            {{-- Tabs Selector --}}
            <div class="threat-side-tabs">
                <button type="button" class="threat-tab-btn active" data-tab-target="live-feed">
                    <span class="threat-tab-dot"></span>
                    <span>{{ __('Live Feed') }}</span>
                </button>
                <button type="button" class="threat-tab-btn" data-tab-target="top-origins">
                    <span>{{ __('Top Origins') }}</span>
                </button>
                <button type="button" class="threat-tab-btn" data-tab-target="attack-vectors">
                    <span>{{ __('Vectors') }}</span>
                </button>
            </div>

            {{-- Tab 1: Live Incident Telemetry Feed --}}
            <div class="threat-tab-panel is-active" id="tab-live-feed">
                <div class="threat-panel-head">
                    <div class="threat-panel-title">
                        <span class="threat-pulse-cyan"></span>
                        <span>{{ __('Live Incident Telemetry') }}</span>
                    </div>
                    <span class="threat-panel-count" data-stream-count>0 events</span>
                </div>
                <div class="threat-log-scroll" data-map-logs>
                    {{-- Diisi secara dinamis oleh JavaScript dari data live real --}}
                </div>
            </div>

            {{-- Tab 2: Top Attacking Origins Leaderboard --}}
            <div class="threat-tab-panel" id="tab-top-origins" style="display: none;">
                <div class="threat-panel-head">
                    <div class="threat-panel-title">
                        <span>🌍</span>
                        <span>{{ __('Top Threat Origins') }}</span>
                    </div>
                    <span class="threat-panel-sub">{{ __('By Total Incidents') }}</span>
                </div>
                <div class="threat-origins-list" data-origins-list>
                    {{-- Diisi secara dinamis dari $payloadData['origins'] --}}
                </div>
            </div>

            {{-- Tab 3: Attack Vectors Breakdown --}}
            <div class="threat-tab-panel" id="tab-attack-vectors" style="display: none;">
                <div class="threat-panel-head">
                    <div class="threat-panel-title">
                        <span>⚡</span>
                        <span>{{ __('Attack Vectors Breakdown') }}</span>
                    </div>
                    <span class="threat-panel-sub">{{ __('Detected Signatures') }}</span>
                </div>
                <div class="threat-vectors-list" data-vectors-list>
                    {{-- Diisi secara dinamis dari sampel telemetri --}}
                </div>
            </div>
        </aside>
    </div>
    {{-- Modal Dialog: Blokir Alamat IP (Tactical Dark SOC Style) --}}
    <div class="sec-modal-backdrop" id="blockIpModal" hidden style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); backdrop-filter: blur(6px); z-index: 2147483647; padding: 16px;">
        <div class="sec-modal-box" role="dialog" aria-modal="true" aria-labelledby="blockModalTitle" style="max-width: 520px; width: 100%; border-radius: 12px; background: #0b1329; border: 1px solid rgba(6, 182, 212, 0.4); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8), 0 0 25px rgba(6, 182, 212, 0.2); overflow: hidden; color: #e2e8f0;">
            {{-- Header Modal --}}
            <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 20px 24px 0 24px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px; line-height: 1;">🚫</span>
                    <h3 id="blockModalTitle" style="font-size: 18px; font-weight: 700; color: #f8fafc; margin: 0;">
                        {{ __("Blokir Alamat IP") }}
                    </h3>
                </div>
                <button type="button" onclick="closeBlockModal()" aria-label="Close" style="border: none; background: transparent; font-size: 18px; color: #94a3b8; cursor: pointer; padding: 4px; line-height: 1; border-radius: 6px;">
                    ✕
                </button>
            </div>

            <p style="padding: 6px 24px 0 24px; margin: 0 0 16px 0; font-size: 13px; color: #94a3b8; line-height: 1.5;">
                {{ __("Semua permintaan dari IP ini akan ditolak (HTTP 403) sampai blokir dibuka atau kedaluwarsa.") }}
            </p>

            {{-- Form Blokir IP --}}
            <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('pasak.dashboard.blocked-ips.store') ? route('pasak.dashboard.blocked-ips.store') : '#' }}" style="padding: 0 24px 20px 24px;">
                @csrf
                <input type="hidden" id="blockModalScope" name="block_scope" value="ip">
                <input type="hidden" id="blockModalDeviceId" name="device_id" value="">
                <input type="hidden" id="blockModalLocalIp" name="local_ip" value="">

                {{-- Alamat IP --}}
                <div style="margin-bottom: 14px;">
                    <label class="sec-label" for="blockModalIp" style="font-weight: 600; font-size: 13px; color: #f1f5f9; margin-bottom: 6px; display: block;">
                        {{ __("Alamat IP") }}
                    </label>
                    <input type="text" id="blockModalIp" name="ip_address" required class="sec-input" style="width: 100%; font-family: var(--sec-font-mono, monospace); font-size: 13.5px; padding: 9px 12px; border-radius: 8px; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(6, 182, 212, 0.3); color: #fff;" placeholder="203.0.113.50">
                </div>

                {{-- Cakupan Pemblokiran --}}
                <div style="margin-bottom: 14px;">
                    <label class="sec-label" style="font-weight: 600; font-size: 13px; color: #f1f5f9; margin-bottom: 6px; display: block;">
                        {{ __("Cakupan Pemblokiran") }}
                    </label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        {{-- Card 1: Hanya Perangkat --}}
                        <div id="scopeCardDevice" onclick="selectBlockScope('device')" style="cursor: pointer; border: 1.5px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 12px; background: rgba(15, 23, 42, 0.6); transition: all 0.2s ease;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="20" x2="22" y2="20"></line></svg>
                                <span style="font-size: 13px; font-weight: 700; color: #38bdf8;">{{ __("Hanya Perangkat") }}</span>
                            </div>
                            <div style="font-size: 11px; color: #94a3b8; line-height: 1.4;">
                                {{ __("Aman untuk Wi-Fi / NAT. Pengguna lain di IP yang sama tidak terblokir.") }}
                            </div>
                        </div>

                        {{-- Card 2: Seluruh IP Router --}}
                        <div id="scopeCardIp" onclick="selectBlockScope('ip')" style="cursor: pointer; border: 2px solid #ef4444; border-radius: 10px; padding: 12px; background: rgba(239, 68, 68, 0.12); transition: all 0.2s ease;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="5" r="3"></circle><circle cx="6" cy="19" r="3"></circle><circle cx="18" cy="19" r="3"></circle><path d="M12 8v4m0 0l-6 4m6-4l6 4"></path></svg>
                                <span style="font-size: 13px; font-weight: 700; color: #ef4444;">{{ __("Seluruh IP Router") }}</span>
                            </div>
                            <div style="font-size: 11px; color: #94a3b8; line-height: 1.4;">
                                {{ __("Memblokir semua perangkat dari alamat IP publik ini.") }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Durasi Blokir --}}
                <div style="margin-bottom: 14px;">
                    <label class="sec-label" for="blockModalDuration" style="font-weight: 600; font-size: 13px; color: #f1f5f9; margin-bottom: 6px; display: block;">
                        {{ __("Durasi Blokir") }}
                    </label>
                    <select id="blockModalDuration" name="duration_hours" class="sec-select" style="width: 100%; padding: 9px 12px; font-size: 13.5px; border-radius: 8px; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(6, 182, 212, 0.3); color: #fff;">
                        <option value="1">{{ __("1 Jam") }}</option>
                        <option value="6">{{ __("6 Jam") }}</option>
                        <option value="12">{{ __("12 Jam") }}</option>
                        <option value="24" selected>{{ __("24 Jam") }}</option>
                        <option value="72">{{ __("3 Hari (72 Jam)") }}</option>
                        <option value="168">{{ __("7 Hari (1 Minggu)") }}</option>
                        <option value="720">{{ __("30 Hari (1 Bulan)") }}</option>
                        <option value="0">{{ __("Permanen") }}</option>
                    </select>
                </div>

                {{-- Alasan --}}
                <div style="margin-bottom: 14px;">
                    <label class="sec-label" for="blockModalReason" style="font-weight: 600; font-size: 13px; color: #f1f5f9; margin-bottom: 6px; display: block;">
                        {{ __("Alasan") }}
                    </label>
                    <input type="text" id="blockModalReason" name="reason" class="sec-input" style="width: 100%; padding: 9px 12px; font-size: 13.5px; border-radius: 8px; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(6, 182, 212, 0.3); color: #fff;" placeholder="{{ __("Alasan pemblokiran...") }}">
                </div>

                {{-- Catatan Internal --}}
                <div style="margin-bottom: 18px;">
                    <label class="sec-label" for="blockModalNotes" style="font-weight: 600; font-size: 13px; color: #f1f5f9; margin-bottom: 6px; display: block;">
                        {{ __("Catatan Internal") }}
                    </label>
                    <textarea id="blockModalNotes" name="notes" rows="2" class="sec-input" style="width: 100%; padding: 9px 12px; font-size: 13px; border-radius: 8px; resize: vertical; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(6, 182, 212, 0.3); color: #fff;" placeholder="{{ __("Opsional, misalnya nomor tiket atau hasil verifikasi.") }}"></textarea>
                </div>

                {{-- Footer Tombol --}}
                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                    <button type="button" class="threat-ctrl-btn" onclick="closeBlockModal()" style="border-radius: 8px; padding: 8px 18px; font-size: 13px;">
                        {{ __("Batal") }}
                    </button>
                    <button type="submit" id="blockModalSubmitBtn" style="border: 1px solid #ef4444; background: rgba(239, 68, 68, 0.25); color: #fca5a5; border-radius: 8px; font-size: 13px; font-weight: 700; padding: 8px 18px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
                        <span style="font-size: 14px;">🚫</span>
                        <span id="blockModalSubmitLabel">{{ __("Blokir Seluruh IP") }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- ThreatsEye Tactical CSS Styling --}}
<style>
    /* ================= Cyber ThreatsEye Theme Palette ================= */
    .threat-map-card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(6, 182, 212, 0.25);
        background: #020714;
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.8), 0 0 20px rgba(6, 182, 212, 0.08);
        color: #e2e8f0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        position: relative;
    }

    /* 1. Top HUD Status Bar */
    .threat-hud-bar {
        background: rgba(3, 11, 25, 0.95);
        border-bottom: 1px solid rgba(6, 182, 212, 0.2);
        padding: 10px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        backdrop-filter: blur(8px);
    }
    .threat-hud-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .threat-live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 4px 10px;
        border-radius: 6px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .threat-live-beacon {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 10px #10b981;
        animation: threat-radar-pulse 1.4s infinite ease-out;
    }
    .threat-live-label {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #10b981;
        text-transform: uppercase;
    }
    @keyframes threat-radar-pulse {
        0% { transform: scale(0.9); opacity: 1; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1.1); opacity: 0.8; box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.9); opacity: 1; box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .threat-target-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        background: rgba(6, 182, 212, 0.08);
        border: 1px solid rgba(6, 182, 212, 0.25);
        font-size: 11.5px;
    }
    .threat-target-name {
        color: #00f0ff;
        font-weight: 700;
    }
    .threat-target-coords {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 10px;
        color: #94a3b8;
    }

    .threat-mode-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .threat-mode-badge.is-enforcing {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.35);
        color: #ef4444;
    }
    .threat-mode-badge.is-monitoring {
        background: rgba(245, 158, 11, 0.12);
        border: 1px solid rgba(245, 158, 11, 0.35);
        color: #fbbf24;
    }

    .threat-hud-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .threat-hud-clock {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 12px;
        background: rgba(255, 255, 255, 0.04);
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
    }
    .threat-clock-tz {
        font-size: 9.5px;
        color: #06b6d4;
        margin-left: 4px;
    }

    .threat-hud-stat {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        padding: 2px 8px;
        border-left: 1px solid rgba(255, 255, 255, 0.08);
    }
    .threat-hud-stat-label {
        font-size: 9.5px;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }
    .threat-hud-stat-val {
        font-size: 14px;
        font-weight: 800;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        line-height: 1.1;
    }
    .threat-hud-stat.is-danger .threat-hud-stat-val { color: #ef4444; text-shadow: 0 0 8px rgba(239, 68, 68, 0.4); }
    .threat-hud-stat.is-critical .threat-hud-stat-val { color: #f87171; }
    .threat-hud-stat.is-warning .threat-hud-stat-val { color: #fbbf24; }
    .threat-hud-stat.is-cyan .threat-hud-stat-val { color: #06b6d4; }

    /* 2. Main Viewport */
    .threat-map-wrap {
        display: flex;
        flex-direction: row;
        height: calc(100vh - 200px);
        min-height: 640px;
        background: #020617;
        position: relative;
    }

    .threat-map-side.is-hidden {
        display: none !important;
    }

    .threat-map-canvas-holder {
        position: relative;
        flex: 1;
        min-width: 0;
        min-height: 0;
        cursor: grab;
        overflow: hidden;
        touch-action: none;
        background: radial-gradient(circle at 50% 50%, #06152d 0%, #020617 80%);
    }
    .threat-map-canvas-holder:active { cursor: grabbing; }

    .threat-grid-overlay {
        position: absolute;
        inset: 0;
        pointer-events: none;
        opacity: 0.15;
        background-size: 40px 40px;
        background-image:
            linear-gradient(to right, rgba(6, 182, 212, 0.3) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(6, 182, 212, 0.3) 1px, transparent 1px);
        z-index: 1;
    }

    .threat-map-canvas-holder canvas {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
    }

    /* Floating Viewport Controls HUD */
    .threat-controls-hud {
        position: absolute;
        top: 14px;
        left: 14px;
        right: 14px;
        z-index: 5;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        pointer-events: none;
        flex-wrap: wrap;
    }
    .threat-ctrl-group {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(3, 11, 23, 0.85);
        border: 1px solid rgba(6, 182, 212, 0.3);
        border-radius: 8px;
        padding: 4px;
        backdrop-filter: blur(8px);
        pointer-events: auto;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }
    .threat-ctrl-btn {
        background: transparent;
        border: 1px solid transparent;
        color: #cbd5e1;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s ease;
        line-height: 1;
    }
    .threat-ctrl-btn:hover {
        background: rgba(6, 182, 212, 0.15);
        color: #00f0ff;
        border-color: rgba(6, 182, 212, 0.3);
    }
    .threat-ctrl-btn.active {
        background: rgba(6, 182, 212, 0.25);
        color: #00f0ff;
        border-color: #06b6d4;
        font-weight: 700;
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.3);
    }

    .threat-fullscreen-btn {
        background: rgba(6, 182, 212, 0.15);
        border: 1px solid rgba(6, 182, 212, 0.4);
        color: #00f0ff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .threat-fullscreen-btn:hover {
        background: rgba(6, 182, 212, 0.3);
        border-color: #00f0ff;
        box-shadow: 0 0 12px rgba(6, 182, 212, 0.4);
    }

    /* Severity Filter Overlay (Bottom Left) */
    .threat-filter-overlay {
        position: absolute;
        bottom: 14px;
        left: 14px;
        z-index: 5;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(3, 11, 23, 0.88);
        border: 1px solid rgba(6, 182, 212, 0.25);
        border-radius: 8px;
        padding: 5px 10px;
        backdrop-filter: blur(8px);
        font-size: 11px;
    }
    .threat-filter-title {
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 9.5px;
        margin-right: 2px;
    }
    .threat-filter-pill {
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.04);
        color: #cbd5e1;
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .threat-filter-pill:hover { background: rgba(255, 255, 255, 0.1); }
    .threat-filter-pill.active {
        background: #06b6d4;
        color: #020813;
        border-color: #06b6d4;
        box-shadow: 0 0 8px rgba(6, 182, 212, 0.5);
    }
    .threat-filter-pill.is-critical { color: #f87171; }
    .threat-filter-pill.is-critical.active { background: #ef4444; color: #fff; border-color: #ef4444; box-shadow: 0 0 8px rgba(239, 68, 68, 0.5); }
    .threat-filter-pill.is-high { color: #fb923c; }
    .threat-filter-pill.is-high.active { background: #f97316; color: #fff; border-color: #f97316; box-shadow: 0 0 8px rgba(249, 115, 22, 0.5); }
    .threat-filter-pill.is-medium { color: #facc15; }
    .threat-filter-pill.is-medium.active { background: #eab308; color: #000; border-color: #eab308; box-shadow: 0 0 8px rgba(234, 179, 8, 0.5); }
    .threat-filter-pill.is-low { color: #34d399; }
    .threat-filter-pill.is-low.active { background: #10b981; color: #fff; border-color: #10b981; box-shadow: 0 0 8px rgba(16, 185, 129, 0.5); }

    /* Offline Notice (Non-blocking tactical toast) */
    .threat-map-offline {
        position: absolute;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 30;
        display: flex;
        align-items: center;
        gap: 14px;
        color: #e2e8f0;
        background: rgba(15, 23, 42, 0.96);
        border: 1px solid rgba(6, 182, 212, 0.4);
        border-radius: 8px;
        backdrop-filter: blur(8px);
        padding: 10px 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.7);
        max-width: 90%;
        pointer-events: auto;
        animation: threatFadeIn 0.3s ease;
    }
    .threat-map-offline[hidden] {
        display: none !important;
    }
    .threat-offline-box {
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
    }
    .threat-offline-box strong {
        color: #06b6d4;
        font-size: 12px;
        display: block;
        margin-bottom: 2px;
    }
    .threat-offline-box p {
        color: #94a3b8;
        font-size: 11px;
        margin: 0;
        line-height: 1.4;
    }
    .threat-offline-close {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #94a3b8;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        line-height: 1;
        padding: 6px 10px;
        border-radius: 6px;
        pointer-events: auto;
        position: relative;
        z-index: 31;
        transition: color 0.15s, background 0.15s, border-color 0.15s;
    }
    .threat-offline-close:hover {
        color: #fff;
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.5);
    }

    /* 3. Tactical Sidebar (ThreatsEye Intelligence) */
    .threat-map-side {
        width: 380px;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        border-left: 1px solid rgba(6, 182, 212, 0.2);
        background: #020917;
        overflow: hidden;
    }

    .threat-side-tabs {
        display: flex;
        background: rgba(3, 11, 25, 0.9);
        border-bottom: 1px solid rgba(6, 182, 212, 0.2);
        padding: 6px 8px;
        gap: 6px;
    }
    .threat-tab-btn {
        flex: 1;
        background: transparent;
        border: 1px solid transparent;
        color: #94a3b8;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 7px 6px;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.15s ease;
    }
    .threat-tab-btn:hover {
        color: #00f0ff;
        background: rgba(6, 182, 212, 0.08);
    }
    .threat-tab-btn.active {
        background: rgba(6, 182, 212, 0.18);
        color: #00f0ff;
        border-color: rgba(6, 182, 212, 0.35);
    }
    .threat-tab-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #ef4444;
        box-shadow: 0 0 6px #ef4444;
        animation: threat-radar-pulse 1.2s infinite ease-out;
    }

    .threat-tab-panel {
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        padding: 12px;
    }
    .threat-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px solid rgba(6, 182, 212, 0.15);
    }
    .threat-panel-title {
        font-size: 11px;
        font-weight: 800;
        color: #00f0ff;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .threat-pulse-cyan {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #00f0ff;
        box-shadow: 0 0 8px #00f0ff;
    }
    .threat-panel-count, .threat-panel-sub {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 10px;
        color: #64748b;
    }

    /* Incident Stream Scroll */
    .threat-log-scroll {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-right: 4px;
        scrollbar-width: thin;
        scrollbar-color: #06b6d4 rgba(0, 0, 0, 0.3);
    }
    .threat-log-scroll::-webkit-scrollbar { width: 4px; }
    .threat-log-scroll::-webkit-scrollbar-thumb { background: #06b6d4; border-radius: 4px; }

    .threat-entry {
        background: rgba(15, 23, 42, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-left: 3px solid #64748b;
        padding: 8px 10px;
        border-radius: 6px;
        font-size: 11px;
        color: #cbd5e1;
        animation: threat-slide-in 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        transition: transform 0.15s ease, background 0.15s ease;
    }
    .threat-entry:hover {
        background: rgba(15, 23, 42, 0.95);
        border-color: rgba(6, 182, 212, 0.3);
        transform: translateX(2px);
    }
    .threat-entry.l-critical { border-left-color: #ef4444; }
    .threat-entry.l-high { border-left-color: #f97316; }
    .threat-entry.l-medium { border-left-color: #eab308; }
    .threat-entry.l-low { border-left-color: #10b981; }

    .threat-entry-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }
    .threat-entry-time {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 10px;
        color: #94a3b8;
    }
    .threat-entry-badge {
        font-size: 9.5px;
        font-weight: 800;
        padding: 1px 5px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .threat-entry-badge.l-critical { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4); }
    .threat-entry-badge.l-high { background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.4); }
    .threat-entry-badge.l-medium { background: rgba(234, 179, 8, 0.2); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.4); }
    .threat-entry-badge.l-low { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.4); }

    .threat-entry-body {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .threat-entry-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
    }
    .threat-entry-ip {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-weight: 700;
        color: #f1f5f9;
    }
    .threat-entry-place {
        color: #00f0ff;
        font-size: 10.5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    .threat-entry-desc {
        font-size: 11px;
        font-weight: 700;
        color: #f8fafc;
    }
    .threat-entry-path {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 10px;
        color: #94a3b8;
        word-break: break-all;
        background: rgba(0, 0, 0, 0.35);
        padding: 3px 6px;
        border-radius: 4px;
    }
    .threat-entry-status {
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: 0.05em;
    }
    .threat-entry-status.is-blocked { color: #10b981; }
    .threat-entry-status.is-logged { color: #fbbf24; }

    @keyframes threat-slide-in {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Top Origins & Vectors Lists */
    .threat-origins-list, .threat-vectors-list {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding-right: 4px;
    }
    .threat-rank-card {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 6px;
        padding: 8px 10px;
        display: flex;
        flex-direction: column;
        gap: 6px;
        transition: background 0.15s ease;
    }
    .threat-rank-card:hover { background: rgba(15, 23, 42, 0.9); border-color: rgba(6, 182, 212, 0.3); }
    .threat-rank-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11.5px;
    }
    .threat-rank-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .threat-rank-idx {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 10px;
        font-weight: 800;
        color: #06b6d4;
        width: 16px;
    }
    .threat-rank-label {
        font-weight: 700;
        color: #f1f5f9;
    }
    .threat-rank-val {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-weight: 800;
        color: #00f0ff;
        font-size: 11px;
    }
    .threat-rank-bar-bg {
        height: 4px;
        background: rgba(255, 255, 255, 0.06);
        border-radius: 2px;
        overflow: hidden;
    }
    .threat-rank-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #06b6d4, #3b82f6);
        border-radius: 2px;
        transition: width 0.4s ease;
    }
    .threat-rank-bar-fill.is-red { background: linear-gradient(90deg, #f97316, #ef4444); }
    .threat-rank-bar-fill.is-yellow { background: linear-gradient(90deg, #eab308, #f59e0b); }

    /* 4. Fullscreen Wallboard Mode (Separate rules for zero vendor-prefix drop) */
    .threat-map-card.is-fullscreen {
        position: fixed !important;
        inset: 0 !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 2147483647 !important;
        width: 100% !important;
        height: 100% !important;
        width: 100vw !important;
        height: 100vh !important;
        max-width: none !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 0 !important;
        border: none !important;
        display: flex !important;
        flex-direction: column !important;
        box-sizing: border-box !important;
        background: #020714 !important;
    }
    .threat-map-card:fullscreen {
        position: fixed !important;
        inset: 0 !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 2147483647 !important;
        width: 100% !important;
        height: 100% !important;
        max-width: none !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 0 !important;
        border: none !important;
        display: flex !important;
        flex-direction: column !important;
        box-sizing: border-box !important;
        background: #020714 !important;
    }
    .threat-map-card:-webkit-full-screen {
        position: fixed !important;
        inset: 0 !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 2147483647 !important;
        width: 100% !important;
        height: 100% !important;
        max-width: none !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 0 !important;
        border: none !important;
        display: flex !important;
        flex-direction: column !important;
        box-sizing: border-box !important;
        background: #020714 !important;
    }
    .threat-map-card:-moz-full-screen {
        position: fixed !important;
        inset: 0 !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 2147483647 !important;
        width: 100% !important;
        height: 100% !important;
        max-width: none !important;
        max-height: none !important;
        margin: 0 !important;
        padding: 0 !important;
        border-radius: 0 !important;
        border: none !important;
        display: flex !important;
        flex-direction: column !important;
        box-sizing: border-box !important;
        background: #020714 !important;
    }

    .threat-map-card.is-fullscreen .threat-hud-bar,
    .threat-map-card:fullscreen .threat-hud-bar,
    .threat-map-card:-webkit-full-screen .threat-hud-bar,
    .threat-map-card:-moz-full-screen .threat-hud-bar {
        flex-shrink: 0 !important;
        border-radius: 0 !important;
        margin: 0 !important;
    }

    .threat-map-card.is-fullscreen .threat-map-wrap {
        flex: 1 1 0% !important;
        height: 100% !important;
        min-height: 0 !important;
        max-height: none !important;
        display: flex !important;
        flex-direction: row !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .threat-map-card:fullscreen .threat-map-wrap {
        flex: 1 1 0% !important;
        height: 100% !important;
        min-height: 0 !important;
        max-height: none !important;
        display: flex !important;
        flex-direction: row !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .threat-map-card:-webkit-full-screen .threat-map-wrap {
        flex: 1 1 0% !important;
        height: 100% !important;
        min-height: 0 !important;
        max-height: none !important;
        display: flex !important;
        flex-direction: row !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .threat-map-card:-moz-full-screen .threat-map-wrap {
        flex: 1 1 0% !important;
        height: 100% !important;
        min-height: 0 !important;
        max-height: none !important;
        display: flex !important;
        flex-direction: row !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    .threat-map-card.is-fullscreen .threat-map-canvas-holder {
        height: 100% !important;
        width: 100% !important;
        flex: 1 1 0% !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .threat-map-card:fullscreen .threat-map-canvas-holder {
        height: 100% !important;
        width: 100% !important;
        flex: 1 1 0% !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .threat-map-card:-webkit-full-screen .threat-map-canvas-holder {
        height: 100% !important;
        width: 100% !important;
        flex: 1 1 0% !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .threat-map-card:-moz-full-screen .threat-map-canvas-holder {
        height: 100% !important;
        width: 100% !important;
        flex: 1 1 0% !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .threat-map-card.is-fullscreen .threat-map-side {
        width: 400px !important;
        height: 100% !important;
        max-height: 100% !important;
        margin: 0 !important;
    }
    .threat-map-card:fullscreen .threat-map-side {
        width: 400px !important;
        height: 100% !important;
        max-height: 100% !important;
        margin: 0 !important;
    }
    .threat-map-card:-webkit-full-screen .threat-map-side {
        width: 400px !important;
        height: 100% !important;
        max-height: 100% !important;
        margin: 0 !important;
    }
    .threat-map-card:-moz-full-screen .threat-map-side {
        width: 400px !important;
        height: 100% !important;
        max-height: 100% !important;
        margin: 0 !important;
    }

    html.pasak-threat-fs,
    body.pasak-threat-fs {
        overflow: hidden !important;
        width: 100vw !important;
        height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    body.pasak-threat-fs .sec-header,
    body.pasak-threat-fs .sec-sidebar,
    body.pasak-threat-fs .sec-nav-list,
    body.pasak-threat-fs .sec-container > *:not(.sec-main) {
        display: none !important;
    }
    body.pasak-threat-fs .sec-root,
    body.pasak-threat-fs .sec-container,
    body.pasak-threat-fs .sec-main {
        max-width: 100vw !important;
        width: 100vw !important;
        height: 100vh !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    /* 5. Responsive Breakpoints */
    @media (max-width: 1024px) {
        .threat-map-card:not(.is-fullscreen) .threat-map-wrap {
            flex-direction: column;
            height: auto;
        }
        .threat-map-card:not(.is-fullscreen) .threat-map-canvas-holder {
            height: 460px;
            min-height: 460px;
        }
        .threat-map-card:not(.is-fullscreen) .threat-map-side {
            width: 100%;
            height: 380px;
            border-left: none;
            border-top: 1px solid rgba(6, 182, 212, 0.2);
        }
    }
    @media (max-width: 640px) {
        .threat-hud-bar { padding: 10px 12px; }
        .threat-target-coords { display: none; }
        .threat-controls-hud { top: 10px; left: 10px; right: 10px; }
        .threat-btn-txt { display: none; }
        .threat-filter-overlay { display: none; }
    }
</style>

{{-- D3.js and TopoJSON CDNs with resilient failover --}}
<script src="https://cdn.jsdelivr.net/npm/d3@7/dist/d3.min.js" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/d3/7.9.0/d3.min.js';"></script>
<script src="https://cdn.jsdelivr.net/npm/topojson-client@3/dist/topojson-client.min.js" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/topojson/3.0.2/topojson.min.js';"></script>

{{-- ThreatsEye Tactical Radar Engine Script --}}
<script>
(function () {
    'use strict';

    var card = document.querySelector('[data-threat-map]');
    if (!card) return;

    var MAP = @json($payload);
    var COLORS = {
        critical: '#ef4444',
        high: '#f97316',
        medium: '#eab308',
        low: '#10b981'
    };
    var LEVEL_ORDER = ['critical', 'high', 'medium', 'low'];

    var SCROLL = card.querySelector('[data-map-logs]');
    var ORIGINS_LIST = card.querySelector('[data-origins-list]');
    var VECTORS_LIST = card.querySelector('[data-vectors-list]');
    var STREAM_COUNT = card.querySelector('[data-stream-count]');

    var originsIndex = {};
    var vectorStats = {};
    var countryStats = {};
    var pendingLogs = [];
    var isGlobeReady = false;
    var lastId = 0;
    var totalLoggedIncidents = 0;
    var activeSeverityFilter = 'all';

    // Projection Mode: 'orthographic' (3D Globe) vs 'equirectangular' (2D Tactical Flat Map)
    var currentProjType = 'orthographic';
    var autoRotate = true;
    var soundEnabled = false;
    var streamActive = true;
    var originsPool = [];
    var livePriorityQueue = [];

    // Web Audio API for synthetic high-tech radar impact blips
    var audioCtx = null;
    var lastBlipTime = 0;
    function playRadarBlip(level) {
        if (!soundEnabled) return;
        var nowMs = Date.now();
        if (nowMs - lastBlipTime < 60) return; // Throttle to prevent audio clipping during attack bursts
        lastBlipTime = nowMs;
        try {
            if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            if (audioCtx.state === 'suspended') audioCtx.resume();

            var osc = audioCtx.createOscillator();
            var gain = audioCtx.createGain();
            var freq = level === 'critical' ? 880 : (level === 'high' ? 660 : 440);

            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(freq / 2, audioCtx.currentTime + 0.08);

            gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.08);

            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.08);
        } catch (e) { /* ignore audio failure */ }
    }

    // Live Clock Ticker (Waktu Server / App Timezone)
    function updateClock() {
        var clockEl = card.querySelector('[data-threat-clock] .threat-clock-time');
        if (!clockEl) return;
        var now = new Date();
        var tz = {!! json_encode(\Internal\Pasak\Support\DashboardView::timezone()) !!};
        try {
            clockEl.textContent = new Intl.DateTimeFormat('en-GB', {
                timeZone: tz,
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(now);
        } catch (e) {
            var hours = String(now.getHours()).padStart(2, '0');
            var mins = String(now.getMinutes()).padStart(2, '0');
            var secs = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = hours + ':' + mins + ':' + secs;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    function esc(val) {
        var d = document.createElement('div');
        d.textContent = val === null || val === undefined ? '' : String(val);
        return d.innerHTML;
    }

    function placeOf(location) {
        var p = [];
        if (location && location.place) p.push(location.place);
        if (location && location.country && location.place !== location.country) p.push(location.country);
        return p.length === 0 ? '{{ __("Unknown origin") }}' : p.join(', ');
    }

    function levelOf(entry) {
        return LEVEL_ORDER.indexOf(entry.level) === -1 ? 'low' : entry.level;
    }

    // Render an incident into the live telemetry feed
    function addLog(entry, location) {
        var emptyMsg = SCROLL ? SCROLL.querySelector('.threat-empty-msg') : null;
        if (emptyMsg) emptyMsg.remove();

        totalLoggedIncidents++;
        if (STREAM_COUNT) STREAM_COUNT.textContent = totalLoggedIncidents + ' events';

        // Track stats for vectors & countries
        var countryName = (location && location.country) ? location.country : 'Unknown';
        countryStats[countryName] = (countryStats[countryName] || 0) + 1;

        var vectorName = entry.desc || entry.event || 'Generic Probe';
        vectorStats[vectorName] = (vectorStats[vectorName] || 0) + 1;

        if (!SCROLL) return;

        var level = levelOf(entry);
        var timeStr = entry.time
            ? new Date(entry.time).toLocaleTimeString('id-ID', { hour12: false })
            : new Date().toLocaleTimeString('id-ID', { hour12: false });

        var el = document.createElement('div');
        el.className = 'threat-entry l-' + level;
        el.setAttribute('data-level', level);
        el.setAttribute('data-entry-id', entry.id || '');

        if (activeSeverityFilter !== 'all' && activeSeverityFilter !== level) {
            el.style.display = 'none';
        }

        var isBlocked = entry.blocked;
        var statusBadge = isBlocked
            ? '<span class="threat-entry-status is-blocked">● BLOCKED (403)</span>'
            : '<span class="threat-entry-status is-logged">● DETECTED</span>';

        var blockBtnHtml = isBlocked
            ? ''
            : '<button type="button" class="threat-ctrl-btn" style="padding:2px 6px; font-size:10px;" onclick="openBlockModal && openBlockModal({ip:\'' + esc(entry.ip) + '\', reason:\'' + esc(entry.desc || entry.event) + '\'})">🚫 {{ __("Block IP") }}</button>';

        el.innerHTML =
            '<div class="threat-entry-head">' +
                '<span class="threat-entry-time">[' + esc(timeStr) + ']</span>' +
                '<span class="threat-entry-badge l-' + level + '">' + esc(level.toUpperCase()) + '</span>' +
            '</div>' +
            '<div class="threat-entry-body">' +
                '<div class="threat-entry-meta">' +
                    '<span class="threat-entry-ip">' + esc(entry.ip) + '</span>' +
                    '<span class="threat-entry-place">📍 ' + esc(placeOf(location)) + '</span>' +
                '</div>' +
                '<div class="threat-entry-desc">' + esc(entry.desc || entry.event) + '</div>' +
                '<div class="threat-entry-path">&gt; ' + esc((entry.method || 'GET') + ' ' + (entry.path || '/')) + '</div>' +
                '<div style="display:flex; justify-content:space-between; align-items:center; margin-top:2px;">' +
                    statusBadge +
                    blockBtnHtml +
                '</div>' +
            '</div>';

        SCROLL.prepend(el);

        while (SCROLL.children.length > 50) {
            SCROLL.removeChild(SCROLL.lastChild);
        }

        renderLeaderboard();
        renderVectors();
    }

    // Render Leaderboard Top Attacking Origins
    function renderLeaderboard() {
        if (!ORIGINS_LIST) return;
        var sorted = Object.keys(countryStats).map(function (c) {
            return { country: c, count: countryStats[c] };
        }).sort(function (a, b) { return b.count - a.count; }).slice(0, 8);

        var max = sorted.length > 0 ? sorted[0].count : 1;
        var html = '';

        sorted.forEach(function (item, idx) {
            var pct = Math.round((item.count / max) * 100);
            html +=
                '<div class="threat-rank-card">' +
                    '<div class="threat-rank-top">' +
                        '<div class="threat-rank-left">' +
                            '<span class="threat-rank-idx">#' + (idx + 1) + '</span>' +
                            '<span class="threat-rank-label">' + esc(item.country) + '</span>' +
                        '</div>' +
                        '<span class="threat-rank-val">' + item.count + ' hits</span>' +
                    '</div>' +
                    '<div class="threat-rank-bar-bg">' +
                        '<div class="threat-rank-bar-fill' + (idx === 0 ? ' is-red' : '') + '" style="width:' + pct + '%;"></div>' +
                    '</div>' +
                '</div>';
        });

        ORIGINS_LIST.innerHTML = html || '<div style="color:#64748b; font-size:12px; padding:12px;">{{ __("Belum ada data serangan terdeteksi.") }}</div>';
    }

    // Render Attack Vectors Breakdown
    function renderVectors() {
        if (!VECTORS_LIST) return;
        var sorted = Object.keys(vectorStats).map(function (v) {
            return { vector: v, count: vectorStats[v] };
        }).sort(function (a, b) { return b.count - a.count; }).slice(0, 8);

        var max = sorted.length > 0 ? sorted[0].count : 1;
        var html = '';

        sorted.forEach(function (item, idx) {
            var pct = Math.round((item.count / max) * 100);
            html +=
                '<div class="threat-rank-card">' +
                    '<div class="threat-rank-top">' +
                        '<span class="threat-rank-label" style="font-size:11px;">' + esc(item.vector) + '</span>' +
                        '<span class="threat-rank-val" style="color:#f97316;">' + item.count + '</span>' +
                    '</div>' +
                    '<div class="threat-rank-bar-bg">' +
                        '<div class="threat-rank-bar-fill is-yellow" style="width:' + pct + '%;"></div>' +
                    '</div>' +
                '</div>';
        });

        VECTORS_LIST.innerHTML = html || '<div style="color:#64748b; font-size:12px; padding:12px;">{{ __("Belum ada jenis vektor teridentifikasi.") }}</div>';
    }

    // Seed data dari payload awal
    (MAP.origins || []).forEach(function (origin) {
        originsIndex[origin.ip] = origin;
        originsPool.push(origin);
    });

    if (MAP.locations) {
        Object.keys(MAP.locations).forEach(function (ip) {
            if (!originsIndex[ip]) {
                var loc = MAP.locations[ip];
                originsIndex[ip] = {
                    ip: ip,
                    lat: loc.lat,
                    lon: loc.lon,
                    place: loc.place,
                    country: loc.country,
                    hits: 1,
                    samples: []
                };
                originsPool.push(originsIndex[ip]);
            }
        });
    }

    var initialLogs = [];
    if (MAP.recent_logs && Array.isArray(MAP.recent_logs) && MAP.recent_logs.length > 0) {
        // Muat seluruh riwayat log serangan riil dari database (sama persis dengan Security Logs)
        initialLogs = MAP.recent_logs.slice().reverse();
    } else {
        (MAP.origins || []).forEach(function (origin) {
            (origin.samples || []).forEach(function (entry) {
                initialLogs.push(entry);
            });
        });
        initialLogs.sort(function (a, b) {
            return (a.id || 0) - (b.id || 0);
        });
    }

    var playbackQueue = [];
    initialLogs.forEach(function (entry) {
        if (typeof entry.id === 'number' && entry.id > lastId) {
            lastId = entry.id;
        }
        var origin = originsIndex[entry.ip] || {
            ip: entry.ip,
            lat: (MAP.locations && MAP.locations[entry.ip]) ? MAP.locations[entry.ip].lat : (MAP.target ? MAP.target.lat : 0),
            lon: (MAP.locations && MAP.locations[entry.ip]) ? MAP.locations[entry.ip].lon : (MAP.target ? MAP.target.lon : 0),
            place: (MAP.locations && MAP.locations[entry.ip]) ? MAP.locations[entry.ip].place : 'Unknown',
            country: (MAP.locations && MAP.locations[entry.ip]) ? MAP.locations[entry.ip].country : 'Unknown',
            samples: [entry]
        };
        playbackQueue.push({
            origin: origin,
            entry: entry,
            isPlayback: true
        });
    });

    if (playbackQueue.length === 0 && SCROLL) {
        SCROLL.innerHTML = '<div class="threat-empty-msg" style="text-align: center; color: #64748b; font-size: 12px; padding: 32px;">{{ __("Belum ada log serangan terdeteksi.") }}</div>';
    }

    // Sidebar tab buttons switching
    card.querySelectorAll('[data-tab-target]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = 'tab-' + btn.getAttribute('data-tab-target');
            card.querySelectorAll('[data-tab-target]').forEach(function (b) { b.classList.remove('active'); });
            card.querySelectorAll('.threat-tab-panel').forEach(function (p) { p.style.display = 'none'; });

            btn.classList.add('active');
            var panel = card.querySelector('#' + targetId);
            if (panel) panel.style.display = 'flex';
        });
    });

    // Severity Filter Buttons
    card.querySelectorAll('[data-filter-level]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var lvl = btn.getAttribute('data-filter-level');
            activeSeverityFilter = lvl;
            card.querySelectorAll('[data-filter-level]').forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            if (SCROLL) {
                SCROLL.querySelectorAll('.threat-entry').forEach(function (entryEl) {
                    var elLevel = entryEl.getAttribute('data-level');
                    entryEl.style.display = (lvl === 'all' || elLevel === lvl) ? 'block' : 'none';
                });
            }
        });
    });

    // Sound FX Button Toggle
    var soundBtn = card.querySelector('[data-map-sound]');
    if (soundBtn) {
        soundBtn.addEventListener('click', function () {
            soundEnabled = !soundEnabled;
            var icon = soundBtn.querySelector('[data-sound-icon]');
            var lbl = soundBtn.querySelector('[data-sound-label]');
            if (soundEnabled) {
                if (icon) icon.textContent = '🔊';
                if (lbl) lbl.textContent = 'Audio';
                soundBtn.classList.add('active');
                playRadarBlip('critical');
            } else {
                if (icon) icon.textContent = '🔇';
                if (lbl) lbl.textContent = 'Mute';
                soundBtn.classList.remove('active');
            }
        });
    }

    // Global realistic tactical nodes for 24/7 continuous radar visualization
    var TACTICAL_GLOBAL_NODES = [
        { ip: '185.220.101.5', lat: 52.5200, lon: 13.4050, country: 'Germany', place: 'Berlin' },
        { ip: '198.51.100.24', lat: 37.7749, lon: -122.4194, country: 'United States', place: 'San Francisco' },
        { ip: '103.253.144.10', lat: 1.3521, lon: 103.8198, country: 'Singapore', place: 'Singapore' },
        { ip: '194.26.29.112', lat: 55.7558, lon: 37.6173, country: 'Russia', place: 'Moscow' },
        { ip: '139.162.122.88', lat: 35.6762, lon: 139.6503, country: 'Japan', place: 'Tokyo' },
        { ip: '177.54.144.20', lat: -23.5505, lon: -46.6333, country: 'Brazil', place: 'Sao Paulo' },
        { ip: '51.89.155.42', lat: 51.5074, lon: -0.1278, country: 'United Kingdom', place: 'London' },
        { ip: '139.99.130.60', lat: -33.8688, lon: 151.2093, country: 'Australia', place: 'Sydney' },
        { ip: '114.119.130.12', lat: 39.9042, lon: 116.4074, country: 'China', place: 'Beijing' },
        { ip: '102.165.30.90', lat: -26.2041, lon: 28.0473, country: 'South Africa', place: 'Johannesburg' },
        { ip: '182.253.18.99', lat: -6.2088, lon: 106.8456, country: 'Indonesia', place: 'Jakarta' },
        { ip: '202.158.33.15', lat: -7.2575, lon: 112.7521, country: 'Indonesia', place: 'Surabaya' },
        { ip: '103.10.66.45', lat: 3.5952, lon: 98.6722, country: 'Indonesia', place: 'Medan' }
    ];

    var TACTICAL_ATTACK_SIGNATURES = [
        { event: 'sql_injection', desc: 'SQL Injection in Query Params', level: 'critical', path: '/api/v1/query?id=1%20OR%201=1', blocked: false, method: 'POST' },
        { event: 'path_traversal', desc: 'Directory Traversal Attempt', level: 'high', path: '/public/../../etc/passwd', blocked: false, method: 'GET' },
        { event: 'wp_xmlrpc_brute', desc: 'WordPress XML-RPC Brute Force', level: 'medium', path: '/xmlrpc.php', blocked: false, method: 'POST' },
        { event: 'webshell_probe', desc: 'Webshell Probe Attempt', level: 'critical', path: '/shell.php', blocked: false, method: 'GET' },
        { event: 'xss_payload', desc: 'Cross-Site Scripting Injection', level: 'high', path: '/search?keyword=%3Cscript%3Ealert(1)%3C%2Fscript%3E', blocked: false, method: 'GET' },
        { event: 'env_file_leak', desc: 'Environment File Probe (.env)', level: 'critical', path: '/.env', blocked: false, method: 'GET' },
        { event: 'crawler_bot', desc: 'Malicious Vulnerability Scanner', level: 'low', path: '/robots.txt', blocked: false, method: 'GET' },
        { event: 'login_throttle', desc: 'Auth Rate Limit Exceeded', level: 'high', path: '/login', blocked: false, method: 'POST' }
    ];

    // Aliran Animasi Serangan Radar Kontinu — Menampilkan data serangan riil satu per satu
    function scheduleContinuousStream() {
        if (!isGlobeReady) {
            setTimeout(scheduleContinuousStream, 200);
            return;
        }

        if (streamActive && typeof window.__threatMapFire === 'function') {
            var nextShot = null;
            var isLiveEvent = false;
            var isPlaybackEvent = false;

            if (livePriorityQueue.length > 0) {
                // Serangan riil baru yang masuk secara real-time via polling API
                nextShot = livePriorityQueue.shift();
                isLiveEvent = true;
            } else if (playbackQueue.length > 0) {
                // Alirkan data serangan riil database satu per satu ke radar & sidebar
                nextShot = playbackQueue.shift();
                isPlaybackEvent = true;
            } else {
                // Setelah seluruh data serangan riil selesai dialirkan ke sidebar,
                // radar visual tetap melakukan scanning perimeter di canvas antar origin riil yang ada
                var pool = (originsPool.length > 0) ? originsPool : (MAP.origins && MAP.origins.length > 0 ? MAP.origins : TACTICAL_GLOBAL_NODES);
                if (pool && pool.length > 0) {
                    var randOrigin = pool[Math.floor(Math.random() * pool.length)];
                    var randEntry = null;
                    if (randOrigin.samples && randOrigin.samples.length > 0) {
                        randEntry = randOrigin.samples[Math.floor(Math.random() * randOrigin.samples.length)];
                    }
                    if (!randEntry && MAP.recent_logs && MAP.recent_logs.length > 0) {
                        randEntry = MAP.recent_logs[Math.floor(Math.random() * MAP.recent_logs.length)];
                    }
                    if (!randEntry) {
                        randEntry = {
                            id: 'ambient-' + Date.now(),
                            ip: randOrigin.ip,
                            event: 'radar_ping',
                            desc: 'Perimeter Radar Scan',
                            level: 'low',
                            path: '/',
                            blocked: false,
                            method: 'PING',
                            time: new Date().toISOString()
                        };
                    }
                    nextShot = {
                        origin: randOrigin,
                        entry: randEntry,
                        isAmbient: true
                    };
                }
            }

            if (nextShot && nextShot.origin) {
                // Tembakkan animasi arc di kanvas radar
                window.__threatMapFire(MAP, nextShot.origin, nextShot.entry);

                // HANYA masukkan ke log sidebar jika ini data serangan nyata (livePriorityQueue atau playback database)!
                // JANGAN masukkan data ambient ke sidebar agar feed tetap 100% konsisten dengan logs.blade.php
                if (isLiveEvent || isPlaybackEvent) {
                    addLog(nextShot.entry, nextShot.origin);
                }
            }
        }

        // Interval tempo: jika live event 300ms, jika streaming riwayat 450-700ms, jika ambient idle 1800-3000ms
        var delay = 800;
        if (livePriorityQueue.length > 0) {
            delay = 300;
        } else if (playbackQueue.length > 0) {
            delay = 450 + Math.random() * 250;
        } else {
            delay = 1800 + Math.random() * 1200;
        }
        setTimeout(scheduleContinuousStream, delay);
    }

    function processIncomingFeed(data) {
        if (!data) return;
        if (typeof data.last_id === 'number' && data.last_id > lastId) {
            lastId = data.last_id;
        }
        var attacksEl = card.querySelector('[data-map-attacks]');
        if (attacksEl && data.stats) {
            var countVal = (data.stats.logs_today && data.stats.logs_today > 0)
                ? data.stats.logs_today
                : (data.stats.total_logs || attacksEl.innerText);
            attacksEl.innerText = Number(countVal || 0).toLocaleString('id-ID');
        }
        if (data.logs && Array.isArray(data.logs)) {
            data.logs.forEach(function (entry) {
                var loc = (data.locations && data.locations[entry.ip]) ? data.locations[entry.ip] : null;
                var originObj = (loc && loc.lat !== null) ? {
                    ip: entry.ip,
                    lat: loc.lat,
                    lon: loc.lon,
                    place: loc.place,
                    country: loc.country,
                    samples: [entry]
                } : (originsIndex[entry.ip] || null);

                if (originObj) {
                    if (!originsIndex[entry.ip]) {
                        originsIndex[entry.ip] = originObj;
                        originsPool.push(originObj);
                    }
                    livePriorityQueue.push({ origin: originObj, entry: entry });
                    if (livePriorityQueue.length > 80) {
                        livePriorityQueue.splice(0, livePriorityQueue.length - 80);
                    }
                } else {
                    addLog(entry, null);
                }
            });
        }
    }

    // Polling Real-Time Feed via API
    function poll() {
        if (isGlobeReady) {
            var sep = String(MAP.feedUrl).indexOf('?') === -1 ? '?' : '&';
            fetch(MAP.feedUrl + sep + 'after=' + lastId, { credentials: 'same-origin' }).then(function (res) {
                return res.ok ? res.json() : null;
            }).then(function (data) {
                processIncomingFeed(data);
            }).catch(function () { /* retry next interval */ });
        }
        setTimeout(poll, (MAP.refresh || 6) * 1000);
    }
    poll();

    // Instant Force-Refresh Radar Telemetry
    function forceRefreshTelemetry(callback) {
        var refreshBtn = card.querySelector('[data-threat-force-refresh]');
        if (refreshBtn) {
            refreshBtn.classList.add('active');
            var icon = refreshBtn.querySelector('.threat-refresh-icon');
            if (icon) icon.style.animation = 'sec-spin 0.8s linear infinite';
        }
        var sep = String(MAP.feedUrl).indexOf('?') === -1 ? '?' : '&';
        fetch(MAP.feedUrl + sep + 'after=' + lastId + '&t=' + Date.now(), { credentials: 'same-origin' })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (data) {
                processIncomingFeed(data);
            })
            .catch(function () {})
            .finally(function () {
                if (refreshBtn) {
                    refreshBtn.classList.remove('active');
                    var icon = refreshBtn.querySelector('.threat-refresh-icon');
                    if (icon) icon.style.animation = '';
                }
                if (typeof callback === 'function') callback();
            });
    }
    window.pasakCustomRefresh = forceRefreshTelemetry;

    var hudRefreshBtn = card.querySelector('[data-threat-force-refresh]');
    if (hudRefreshBtn) {
        hudRefreshBtn.addEventListener('click', function () {
            forceRefreshTelemetry();
        });
    }

    // ==================== Dual-Engine Tactical Radar System ====================
    var onEngineResize = null;

    // Dismiss offline toast
    var offlineNotice = card.querySelector('[data-map-offline]');
    var offlineClose = card.querySelector('[data-offline-dismiss]');
    if (offlineClose && offlineNotice) {
        var dismissOffline = function (e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            offlineNotice.hidden = true;
            offlineNotice.setAttribute('hidden', '');
            offlineNotice.style.display = 'none';
        };
        offlineClose.addEventListener('click', dismissOffline);
        offlineClose.addEventListener('mousedown', function (e) { e.stopPropagation(); });
        offlineClose.addEventListener('pointerdown', function (e) { e.stopPropagation(); });
    }

    // Embedded lightweight GeoJSON continents geometry fallback (100% offline resilience)
    var FALLBACK_WORLD_GEOJSON = {
        type: 'FeatureCollection',
        features: [
            {
                type: 'Feature',
                properties: { name: 'North America' },
                geometry: {
                    type: 'Polygon',
                    coordinates: [[
                        [-168, 65], [-160, 70], [-125, 72], [-85, 76], [-75, 72], [-60, 60],
                        [-65, 45], [-75, 35], [-80, 25], [-90, 20], [-105, 20], [-120, 32],
                        [-125, 40], [-130, 50], [-140, 58], [-160, 58], [-168, 65]
                    ]]
                }
            },
            {
                type: 'Feature',
                properties: { name: 'South America' },
                geometry: {
                    type: 'Polygon',
                    coordinates: [[
                        [-80, 8], [-60, 10], [-50, 0], [-35, -5], [-38, -15], [-50, -30],
                        [-60, -40], [-68, -55], [-75, -50], [-72, -35], [-80, -5], [-80, 8]
                    ]]
                }
            },
            {
                type: 'Feature',
                properties: { name: 'Eurasia' },
                geometry: {
                    type: 'Polygon',
                    coordinates: [[
                        [-10, 36], [0, 42], [10, 44], [30, 46], [40, 40], [50, 42], [60, 38],
                        [70, 25], [80, 15], [90, 22], [100, 10], [105, 20], [120, 22], [122, 32],
                        [130, 40], [140, 48], [170, 65], [180, 68], [140, 72], [90, 74], [40, 70],
                        [20, 70], [5, 62], [-5, 52], [-10, 36]
                    ]]
                }
            },
            {
                type: 'Feature',
                properties: { name: 'Africa' },
                geometry: {
                    type: 'Polygon',
                    coordinates: [[
                        [-17, 15], [-5, 36], [10, 37], [25, 32], [32, 31], [43, 12], [51, 12],
                        [40, -10], [32, -28], [28, -34], [18, -34], [12, -15], [5, 5], [-15, 10], [-17, 15]
                    ]]
                }
            },
            {
                type: 'Feature',
                properties: { name: 'Australia' },
                geometry: {
                    type: 'Polygon',
                    coordinates: [[
                        [114, -22], [124, -15], [135, -12], [142, -11], [146, -20], [153, -28],
                        [150, -37], [140, -38], [130, -32], [116, -35], [114, -22]
                    ]]
                }
            },
            {
                type: 'Feature',
                properties: { name: 'Southeast Asia & Indonesia' },
                geometry: {
                    type: 'Polygon',
                    coordinates: [[
                        [95, 5], [105, 5], [115, 7], [118, -4], [125, 2], [130, -3],
                        [140, -3], [140, -8], [120, -10], [106, -7], [96, 3], [95, 5]
                    ]]
                }
            },
            {
                type: 'Feature',
                properties: { name: 'Antarctica' },
                geometry: {
                    type: 'Polygon',
                    coordinates: [[
                        [-180, -65], [-120, -68], [-60, -65], [0, -68], [60, -65], [120, -68], [180, -65], [180, -88], [-180, -88], [-180, -65]
                    ]]
                }
            }
        ]
    };

    var worldData = FALLBACK_WORLD_GEOJSON;

    function loadWorldAtlas(onReady) {
        var jsdelivrUrl = 'https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json';
        var unpkgUrl = 'https://unpkg.com/world-atlas@2/countries-110m.json';

        function fetchWithTimeout(url, ms) {
            return new Promise(function (resolve, reject) {
                var timer = setTimeout(function () { reject(new Error('timeout')); }, ms);
                fetch(url).then(function (res) {
                    clearTimeout(timer);
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                }).then(resolve).catch(function (err) {
                    clearTimeout(timer);
                    reject(err);
                });
            });
        }

        fetchWithTimeout(jsdelivrUrl, 3500)
            .then(function (data) {
                if (typeof topojson !== 'undefined' && data.objects && data.objects.countries) {
                    worldData = topojson.feature(data, data.objects.countries);
                } else {
                    worldData = data;
                }
                onReady(false);
            })
            .catch(function () {
                fetchWithTimeout(unpkgUrl, 3500)
                    .then(function (data2) {
                        if (typeof topojson !== 'undefined' && data2.objects && data2.objects.countries) {
                            worldData = topojson.feature(data2, data2.objects.countries);
                        } else {
                            worldData = data2;
                        }
                        onReady(false);
                    })
                    .catch(function () {
                        worldData = FALLBACK_WORLD_GEOJSON;
                        onReady(true);
                    });
            });
    }

    // Engine 1: D3.js 3D Globe & 2D Tactical Vector Engine
    function initD3GlobeEngine() {
        var holder = card.querySelector('.threat-map-canvas-holder');
        var baseCanvas = card.querySelector('[data-map-base]');
        var animCanvas = card.querySelector('[data-map-anim]');
        var baseCtx = baseCanvas.getContext('2d');
        var animCtx = animCanvas.getContext('2d');

        var width, height, projection, geoPath;
        var centerX, centerY;
        var baseScale = 1;
        var currentZoom = 1;
        var activeAttacks = [];
        var ripples = [];
        var graticule = d3.geoGraticule();

        // Indonesia Archipelago Geographic Center (Titik Tengah Nusantara)
        // Sabang (95° BT) s/d Merauke (141° BT) -> Titik tengah: 118.0° BT, 2.0° LS
        var INDO_CENTER_LON = 118.0;
        var INDO_CENTER_LAT = -2.0;

        function createProjection(type) {
            if (type === 'equirectangular') {
                return d3.geoEquirectangular()
                    .translate([centerX, centerY])
                    .scale(baseScale * 1.55 * currentZoom)
                    .rotate([-INDO_CENTER_LON, 0, 0]);
            }
            return d3.geoOrthographic()
                .translate([centerX, centerY])
                .scale(baseScale * currentZoom)
                .rotate([-INDO_CENTER_LON, -INDO_CENTER_LAT, 0]);
        }

        // Projection Mode Buttons
        card.querySelectorAll('[data-map-proj]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var mode = btn.getAttribute('data-map-proj');
                currentProjType = mode;
                card.querySelectorAll('[data-map-proj]').forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');

                projection = createProjection(currentProjType);
                geoPath = d3.geoPath().projection(projection).context(baseCtx);
                updateRotateButtonState();
                drawBaseMap();
            });
        });

        function updateRotateButtonState() {
            var rotateBtn = card.querySelector('[data-map-action="toggle-rotate"]');
            if (!rotateBtn) return;
            if (currentProjType === 'equirectangular') {
                rotateBtn.style.opacity = '0.45';
                rotateBtn.style.pointerEvents = 'none';
                rotateBtn.title = '{{ __("Rotasi hanya aktif pada mode 3D Globe") }}';
            } else {
                rotateBtn.style.opacity = '';
                rotateBtn.style.pointerEvents = '';
                rotateBtn.title = autoRotate ? '{{ __("Jeda Animasi Rotasi Globe") }}' : '{{ __("Putar Animasi Rotasi Globe") }}';
            }
        }

        // Zoom & Pan behaviors
        var zoom = d3.zoom()
            .scaleExtent([0.6, 6])
            .on('zoom', function (event) {
                currentZoom = event.transform.k;
                if (currentProjType === 'equirectangular') {
                    projection.scale(baseScale * 1.55 * currentZoom);
                } else {
                    projection.scale(baseScale * currentZoom);
                }
                drawBaseMap();
            });

        var isDragging = false;
        var drag = d3.drag()
            .on('start', function () {
                isDragging = true;
            })
            .on('drag', function (event) {
                if (currentProjType === 'orthographic') {
                    var rot = projection.rotate();
                    var k = 70 / projection.scale();
                    projection.rotate([rot[0] + event.dx * k, rot[1] - event.dy * k, rot[2]]);
                } else {
                    var rot2d = projection.rotate();
                    var k2d = 100 / (projection.scale() * 1.55);
                    projection.rotate([rot2d[0] + event.dx * k2d, 0, 0]);
                }
                drawBaseMap();
            })
            .on('end', function () {
                setTimeout(function () {
                    isDragging = false;
                }, 400);
            });

        d3.select(holder).call(drag).call(zoom);

        // Zoom and Action Controls
        card.querySelectorAll('[data-map-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = btn.getAttribute('data-map-action');
                if (action === 'zoom-in') {
                    d3.select(holder).transition().duration(250).call(zoom.scaleBy, 1.3);
                } else if (action === 'zoom-out') {
                    d3.select(holder).transition().duration(250).call(zoom.scaleBy, 0.75);
                } else if (action === 'reset') {
                    currentZoom = 1;
                    d3.select(holder).transition().duration(350).call(zoom.transform, d3.zoomIdentity);
                    if (currentProjType === 'orthographic') {
                        projection.rotate([-INDO_CENTER_LON, -INDO_CENTER_LAT, 0]);
                    } else {
                        projection.translate([centerX, centerY]);
                        projection.rotate([-INDO_CENTER_LON, 0, 0]);
                    }
                    drawBaseMap();
                } else if (action === 'toggle-rotate') {
                    autoRotate = !autoRotate;
                    btn.classList.toggle('active', autoRotate);
                    var labelEl = btn.querySelector('[data-rotate-label]');
                    if (labelEl) {
                        labelEl.textContent = autoRotate ? '{{ __("Spin: ON") }}' : '{{ __("Spin: OFF") }}';
                    }
                    btn.title = autoRotate ? '{{ __("Jeda Animasi Rotasi Globe") }}' : '{{ __("Putar Animasi Rotasi Globe") }}';
                } else if (action === 'toggle-stream') {
                    streamActive = !streamActive;
                    btn.classList.toggle('active', streamActive);
                    var labelStream = btn.querySelector('[data-stream-label]');
                    if (labelStream) {
                        labelStream.textContent = streamActive ? '{{ __("Radar: LIVE") }}' : '{{ __("Radar: PAUSED") }}';
                    }
                    btn.title = streamActive ? '{{ __("Jeda Aliran Animasi Serangan Radar") }}' : '{{ __("Lanjutkan Aliran Animasi Serangan Radar") }}';
                } else if (action === 'toggle-sidebar') {
                    var side = card.querySelector('.threat-map-side');
                    if (side) {
                        var isClosed = side.classList.toggle('is-hidden');
                        btn.classList.toggle('active', !isClosed);
                        triggerEngineResize();
                    }
                }
            });
        });

        function resize() {
            var rect = holder.getBoundingClientRect();
            var dpr = window.devicePixelRatio || 1;
            width = rect.width;
            height = rect.height;

            if (!width || !height) {
                width = holder.clientWidth || window.innerWidth;
                height = holder.clientHeight || window.innerHeight;
            }

            [baseCanvas, animCanvas].forEach(function (c) {
                c.width = width * dpr;
                c.height = height * dpr;
                var ctx = c.getContext('2d');
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.scale(dpr, dpr);
            });

            centerX = width / 2;
            centerY = height / 2;
            baseScale = Math.min(width, height) / 2.3;

            projection = createProjection(currentProjType);
            geoPath = d3.geoPath().projection(projection).context(baseCtx);

            if (worldData) drawBaseMap();
        }

        window.addEventListener('resize', resize);
        if (typeof ResizeObserver === 'function') {
            new ResizeObserver(resize).observe(holder);
        }
        onEngineResize = resize;

        function isVisible(coords) {
            if (currentProjType === 'equirectangular') return true;
            var rot = projection.rotate();
            var centerLon = -rot[0];
            var centerLat = -rot[1];
            return d3.geoDistance(coords, [centerLon, centerLat]) < Math.PI / 2;
        }

        function getElevatedPoint(geoCoords, progress) {
            var pt = projection(geoCoords);
            if (!pt) return null;
            if (currentProjType === 'equirectangular') {
                var arcElevation = Math.sin(progress * Math.PI) * 45 * currentZoom;
                return [pt[0], pt[1] - arcElevation];
            }
            var dx = pt[0] - centerX;
            var dy = pt[1] - centerY;
            var distFromCenter = Math.sqrt(dx * dx + dy * dy);
            var globeRadius = projection.scale();
            var altitude = Math.sin(progress * Math.PI) * (globeRadius * 0.22);
            var factor = distFromCenter > 0 ? (distFromCenter + altitude) / distFromCenter : 1;
            return [centerX + dx * factor, centerY + dy * factor];
        }

        function drawBaseMap() {
            baseCtx.clearRect(0, 0, width, height);

            // 3D Globe Sphere Background
            if (currentProjType === 'orthographic') {
                baseCtx.beginPath();
                baseCtx.arc(centerX, centerY, projection.scale(), 0, Math.PI * 2);
                var oceanGrad = baseCtx.createRadialGradient(centerX - projection.scale() * 0.3, centerY - projection.scale() * 0.3, 10, centerX, centerY, projection.scale());
                oceanGrad.addColorStop(0, '#041530');
                oceanGrad.addColorStop(1, '#010816');
                baseCtx.fillStyle = oceanGrad;
                baseCtx.fill();
            }

            // Graticule Lines
            baseCtx.beginPath();
            geoPath(graticule());
            baseCtx.strokeStyle = 'rgba(6, 182, 212, 0.08)';
            baseCtx.lineWidth = 0.5;
            baseCtx.stroke();

            // World Countries Fill & Outlines
            if (worldData) {
                baseCtx.beginPath();
                geoPath(worldData);
                baseCtx.fillStyle = '#0a1d3d';
                baseCtx.fill();

                baseCtx.strokeStyle = '#06b6d4';
                baseCtx.lineWidth = 0.8;
                baseCtx.shadowColor = 'rgba(6, 182, 212, 0.5)';
                baseCtx.shadowBlur = 4;
                baseCtx.lineJoin = 'round';
                baseCtx.stroke();
                baseCtx.shadowBlur = 0;
            }

            // 3D Globe Outer Atmosphere Rim Glow
            if (currentProjType === 'orthographic') {
                baseCtx.beginPath();
                baseCtx.arc(centerX, centerY, projection.scale(), 0, Math.PI * 2);
                baseCtx.strokeStyle = 'rgba(0, 240, 255, 0.5)';
                baseCtx.lineWidth = 1.2;
                baseCtx.shadowColor = 'rgba(0, 240, 255, 0.6)';
                baseCtx.shadowBlur = 24;
                baseCtx.stroke();
                baseCtx.shadowBlur = 0;
            }

            // Target Server Node Marker (Pekanbaru / configured target)
            var targetPoint = projection([MAP.target.lon, MAP.target.lat]);
            if (targetPoint && isVisible([MAP.target.lon, MAP.target.lat])) {
                // Concentric Radar Rings
                baseCtx.beginPath();
                baseCtx.arc(targetPoint[0], targetPoint[1], 12 * currentZoom, 0, Math.PI * 2);
                baseCtx.strokeStyle = 'rgba(0, 240, 255, 0.35)';
                baseCtx.lineWidth = 1;
                baseCtx.stroke();

                baseCtx.beginPath();
                baseCtx.arc(targetPoint[0], targetPoint[1], 6 * currentZoom, 0, Math.PI * 2);
                baseCtx.fillStyle = '#00f0ff';
                baseCtx.shadowColor = '#00f0ff';
                baseCtx.shadowBlur = 15;
                baseCtx.fill();
                baseCtx.shadowBlur = 0;

                // Target Crosshair Center
                baseCtx.beginPath();
                baseCtx.arc(targetPoint[0], targetPoint[1], 2 * currentZoom, 0, Math.PI * 2);
                baseCtx.fillStyle = '#ffffff';
                baseCtx.fill();

                // Target Node Tactical Name Label
                baseCtx.font = 'bold ' + Math.max(9, Math.round(10 * currentZoom)) + 'px ui-monospace, SFMono-Regular, monospace';
                baseCtx.fillStyle = '#00f0ff';
                baseCtx.textAlign = 'center';
                baseCtx.fillText(MAP.target.name || 'NODE TARGET', targetPoint[0], targetPoint[1] - 14 * currentZoom);
            }
        }

        // Fire an animated attack trajectory
        window.__threatMapFire = function (map, origin, entry) {
            if (!origin || typeof origin.lat !== 'number' || typeof origin.lon !== 'number') return;
            var lvl = levelOf(entry);
            var jitter = 2;
            activeAttacks.push({
                sourceCoords: [origin.lon + (Math.random() - 0.5) * jitter, origin.lat + (Math.random() - 0.5) * jitter],
                targetCoords: [map.target.lon, map.target.lat],
                progress: 0,
                speed: 0.006 + Math.random() * 0.005,
                color: COLORS[lvl] || '#ef4444',
                level: lvl
            });
        };

        // Animation Loop (Particle Arcs + Ripple Shockwaves)
        var lastSpinTime = Date.now();
        function animate() {
            animCtx.clearRect(0, 0, width, height);

            // Smooth Auto-Rotate when in 3D Orthographic mode, not dragging, and autoRotate is ON
            if (autoRotate && !isDragging && currentProjType === 'orthographic' && isGlobeReady) {
                var now = Date.now();
                if (now - lastSpinTime > 40) {
                    var rot = projection.rotate();
                    projection.rotate([rot[0] + 0.15, rot[1], rot[2]]);
                    drawBaseMap();
                    lastSpinTime = now;
                }
            }

            // Draw Shockwave Ripples at Server Target
            for (var r = ripples.length - 1; r >= 0; r--) {
                var rip = ripples[r];
                rip.radius += rip.speed;
                rip.alpha -= rip.fade;
                if (rip.alpha <= 0) {
                    ripples.splice(r, 1);
                    continue;
                }
                var tPt = projection([MAP.target.lon, MAP.target.lat]);
                if (tPt && isVisible([MAP.target.lon, MAP.target.lat])) {
                    animCtx.beginPath();
                    animCtx.arc(tPt[0], tPt[1], rip.radius, 0, Math.PI * 2);
                    animCtx.strokeStyle = 'rgba(' + rip.rgb + ', ' + rip.alpha + ')';
                    animCtx.lineWidth = 1.8;
                    animCtx.stroke();
                }
            }

            // Draw Animated Cyber Attack Arcs
            for (var i = activeAttacks.length - 1; i >= 0; i--) {
                var atk = activeAttacks[i];
                atk.progress += atk.speed;

                if (atk.progress >= 1) {
                    // Detonation ripple at server node
                    var rgbMap = {
                        '#ef4444': '239, 68, 68',
                        '#f97316': '249, 115, 22',
                        '#eab308': '234, 179, 8',
                        '#10b981': '16, 185, 129'
                    };
                    ripples.push({
                        radius: 5 * currentZoom,
                        speed: 1.8 * currentZoom,
                        alpha: 1,
                        fade: 0.04,
                        rgb: rgbMap[atk.color] || '239, 68, 68'
                    });
                    playRadarBlip(atk.level);
                    activeAttacks.splice(i, 1);
                    continue;
                }

                var interpolator = d3.geoInterpolate(atk.sourceCoords, atk.targetCoords);
                var currentGeo = interpolator(atk.progress);

                // Draw glowing comet head
                if (isVisible(currentGeo)) {
                    var head = getElevatedPoint(currentGeo, atk.progress);
                    if (head) {
                        animCtx.beginPath();
                        animCtx.arc(head[0], head[1], 3.5 * currentZoom, 0, Math.PI * 2);
                        animCtx.fillStyle = '#ffffff';
                        animCtx.shadowColor = atk.color;
                        animCtx.shadowBlur = 10;
                        animCtx.fill();
                        animCtx.shadowBlur = 0;
                    }
                }

                // Draw arched laser beam trail
                var trailSegments = 24;
                var trailLength = 0.16;
                for (var j = 0; j < trailSegments; j++) {
                    var t1 = atk.progress - (j / trailSegments) * trailLength;
                    var t2 = atk.progress - ((j + 1) / trailSegments) * trailLength;
                    if (t1 <= 0) break;
                    t2 = Math.max(0, t2);

                    var g1 = interpolator(t1);
                    var g2 = interpolator(t2);
                    if (!isVisible(g1) && !isVisible(g2)) continue;

                    var p1 = getElevatedPoint(g1, t1);
                    var p2 = getElevatedPoint(g2, t2);
                    if (!p1 || !p2) continue;

                    animCtx.beginPath();
                    animCtx.moveTo(p1[0], p1[1]);
                    animCtx.lineTo(p2[0], p2[1]);
                    animCtx.strokeStyle = atk.color;
                    animCtx.lineWidth = Math.max(0.6, 4.0 - j * 0.16) * currentZoom;
                    animCtx.globalAlpha = Math.max(0, 1 - j / trailSegments);

                    if (j === 0) {
                        animCtx.shadowBlur = 12;
                        animCtx.shadowColor = atk.color;
                    } else {
                        animCtx.shadowBlur = 0;
                    }
                    animCtx.stroke();
                    animCtx.globalAlpha = 1;
                }
            }

            requestAnimationFrame(animate);
        }

        resize();
        isGlobeReady = true;
        animate();
        scheduleContinuousStream();

        // Asynchronously load high-detail 110m world atlas
        loadWorldAtlas(function (isFallback) {
            drawBaseMap();
            if (isFallback && offlineNotice) {
                offlineNotice.hidden = false;
                offlineNotice.removeAttribute('hidden');
                offlineNotice.style.display = 'flex';
            } else if (offlineNotice) {
                offlineNotice.hidden = true;
                offlineNotice.setAttribute('hidden', '');
                offlineNotice.style.display = 'none';
            }
        });
    }

    // Engine 2: Native Canvas 2D Cyber Radar Engine (Offline Standalone Mode)
    function initFallbackRadarEngine() {
        var holder = card.querySelector('.threat-map-canvas-holder');
        var baseCanvas = card.querySelector('[data-map-base]');
        var animCanvas = card.querySelector('[data-map-anim]');
        var baseCtx = baseCanvas.getContext('2d');
        var animCtx = animCanvas.getContext('2d');
        var width, height, centerX, centerY, radius;
        var radarZoom = 1;
        var sweepAngle = 0;
        var activeAttacks = [];
        var ripples = [];

        // Show offline notice pill
        if (offlineNotice) {
            offlineNotice.hidden = false;
            offlineNotice.removeAttribute('hidden');
            offlineNotice.style.display = 'flex';
        }

        function resize() {
            var rect = holder.getBoundingClientRect();
            var dpr = window.devicePixelRatio || 1;
            width = rect.width;
            height = rect.height;

            if (!width || !height) {
                width = holder.clientWidth || window.innerWidth;
                height = holder.clientHeight || window.innerHeight;
            }

            [baseCanvas, animCanvas].forEach(function (c) {
                c.width = width * dpr;
                c.height = height * dpr;
                var ctx = c.getContext('2d');
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.scale(dpr, dpr);
            });

            centerX = width / 2;
            centerY = height / 2;
            radius = Math.min(width, height) * 0.44;
            drawBaseRadar();
        }

        window.addEventListener('resize', resize);
        if (typeof ResizeObserver === 'function') {
            new ResizeObserver(resize).observe(holder);
        }
        onEngineResize = resize;

        // Action Controls for Fallback Radar
        card.querySelectorAll('[data-map-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = btn.getAttribute('data-map-action');
                if (action === 'zoom-in') {
                    radarZoom = Math.min(2.5, radarZoom * 1.25);
                    drawBaseRadar();
                } else if (action === 'zoom-out') {
                    radarZoom = Math.max(0.6, radarZoom * 0.8);
                    drawBaseRadar();
                } else if (action === 'reset') {
                    radarZoom = 1;
                    drawBaseRadar();
                } else if (action === 'toggle-stream') {
                    streamActive = !streamActive;
                    btn.classList.toggle('active', streamActive);
                    var labelStreamFb = btn.querySelector('[data-stream-label]');
                    if (labelStreamFb) {
                        labelStreamFb.textContent = streamActive ? '{{ __("Radar: LIVE") }}' : '{{ __("Radar: PAUSED") }}';
                    }
                    btn.title = streamActive ? '{{ __("Jeda Aliran Animasi Serangan Radar") }}' : '{{ __("Lanjutkan Aliran Animasi Serangan Radar") }}';
                } else if (action === 'toggle-sidebar') {
                    var side = card.querySelector('.threat-map-side');
                    if (side) {
                        var isClosed = side.classList.toggle('is-hidden');
                        btn.classList.toggle('active', !isClosed);
                        triggerEngineResize();
                    }
                }
            });
        });

        function drawBaseRadar() {
            baseCtx.clearRect(0, 0, width, height);

            // Radar background gradient
            var bgGrad = baseCtx.createRadialGradient(centerX, centerY, 0, centerX, centerY, radius * radarZoom);
            bgGrad.addColorStop(0, '#041738');
            bgGrad.addColorStop(0.8, '#020b1c');
            bgGrad.addColorStop(1, '#01050e');
            baseCtx.fillStyle = bgGrad;
            baseCtx.beginPath();
            baseCtx.arc(centerX, centerY, radius * radarZoom, 0, Math.PI * 2);
            baseCtx.fill();

            // Range rings
            var rings = [0.25, 0.5, 0.75, 1.0];
            rings.forEach(function (pct) {
                var r = radius * radarZoom * pct;
                baseCtx.beginPath();
                baseCtx.arc(centerX, centerY, r, 0, Math.PI * 2);
                baseCtx.strokeStyle = 'rgba(6, 182, 212, ' + (pct === 1.0 ? '0.45' : '0.15') + ')';
                baseCtx.lineWidth = pct === 1.0 ? 1.5 : 0.8;
                if (pct < 1.0) baseCtx.setLineDash([4, 4]);
                baseCtx.stroke();
                baseCtx.setLineDash([]);
            });

            // Crosshair Axes (N-S, E-W)
            baseCtx.beginPath();
            baseCtx.moveTo(centerX - radius * radarZoom, centerY);
            baseCtx.lineTo(centerX + radius * radarZoom, centerY);
            baseCtx.moveTo(centerX, centerY - radius * radarZoom);
            baseCtx.lineTo(centerX, centerY + radius * radarZoom);
            baseCtx.strokeStyle = 'rgba(6, 182, 212, 0.25)';
            baseCtx.lineWidth = 1;
            baseCtx.stroke();

            // Diagonal Crosshairs
            var diag = radius * radarZoom * 0.7071;
            baseCtx.beginPath();
            baseCtx.moveTo(centerX - diag, centerY - diag);
            baseCtx.lineTo(centerX + diag, centerY + diag);
            baseCtx.moveTo(centerX - diag, centerY + diag);
            baseCtx.lineTo(centerX + diag, centerY - diag);
            baseCtx.strokeStyle = 'rgba(6, 182, 212, 0.1)';
            baseCtx.lineWidth = 0.6;
            baseCtx.stroke();

            // Cardinal labels
            baseCtx.font = '10px monospace';
            baseCtx.fillStyle = '#06b6d4';
            baseCtx.textAlign = 'center';
            baseCtx.fillText('N', centerX, centerY - radius * radarZoom + 14);
            baseCtx.fillText('S', centerX, centerY + radius * radarZoom - 6);
            baseCtx.fillText('E', centerX + radius * radarZoom - 12, centerY + 3);
            baseCtx.fillText('W', centerX - radius * radarZoom + 12, centerY + 3);

            // Target Node Marker Center
            baseCtx.beginPath();
            baseCtx.arc(centerX, centerY, 5, 0, Math.PI * 2);
            baseCtx.fillStyle = '#00f0ff';
            baseCtx.shadowColor = '#00f0ff';
            baseCtx.shadowBlur = 12;
            baseCtx.fill();
            baseCtx.shadowBlur = 0;
        }

        var INDO_CENTER_LON = 118.0;
        var INDO_CENTER_LAT = -2.0;

        // Relative Geo to Polar Radar Mapping (centered on Indonesia)
        function geoToRadar(lon, lat) {
            var dLon = (lon - INDO_CENTER_LON) * Math.PI / 180;
            var dLat = (lat - INDO_CENTER_LAT) * Math.PI / 180;
            var dist = Math.min(1, Math.sqrt(dLon * dLon + dLat * dLat) / (Math.PI / 1.3));
            var angle = Math.atan2(dLat, dLon);
            var r = dist * radius * radarZoom * 0.88;
            return [centerX + r * Math.cos(angle), centerY - r * Math.sin(angle)];
        }

        window.__threatMapFire = function (map, origin, entry) {
            if (!origin || typeof origin.lat !== 'number' || typeof origin.lon !== 'number') return;
            var pt = geoToRadar(origin.lon, origin.lat);
            var lvl = levelOf(entry);
            activeAttacks.push({
                startX: pt[0],
                startY: pt[1],
                targetX: centerX,
                targetY: centerY,
                progress: 0,
                speed: 0.008 + Math.random() * 0.006,
                color: COLORS[lvl] || '#ef4444',
                level: lvl
            });
        };

        function animate() {
            animCtx.clearRect(0, 0, width, height);

            sweepAngle = (sweepAngle + 0.02) % (Math.PI * 2);

            // Radar sweep sector
            var sweepGrad = animCtx.createRadialGradient(centerX, centerY, 0, centerX, centerY, radius * radarZoom);
            sweepGrad.addColorStop(0, 'rgba(6, 182, 212, 0.25)');
            sweepGrad.addColorStop(1, 'rgba(6, 182, 212, 0.0)');
            animCtx.beginPath();
            animCtx.moveTo(centerX, centerY);
            animCtx.arc(centerX, centerY, radius * radarZoom, sweepAngle - 0.28, sweepAngle);
            animCtx.closePath();
            animCtx.fillStyle = sweepGrad;
            animCtx.fill();

            // Active attack beams
            for (var i = activeAttacks.length - 1; i >= 0; i--) {
                var atk = activeAttacks[i];
                atk.progress += atk.speed;

                if (atk.progress >= 1) {
                    ripples.push({
                        x: centerX,
                        y: centerY,
                        r: 4,
                        alpha: 1,
                        color: atk.color
                    });
                    playRadarBlip(atk.level);
                    activeAttacks.splice(i, 1);
                    continue;
                }

                var curX = atk.startX + (atk.targetX - atk.startX) * atk.progress;
                var curY = atk.startY + (atk.targetY - atk.startY) * atk.progress;

                animCtx.beginPath();
                animCtx.moveTo(atk.startX, atk.startY);
                animCtx.lineTo(curX, curY);
                animCtx.strokeStyle = atk.color;
                animCtx.lineWidth = 2;
                animCtx.stroke();

                animCtx.beginPath();
                animCtx.arc(curX, curY, 3.5, 0, Math.PI * 2);
                animCtx.fillStyle = '#ffffff';
                animCtx.shadowColor = atk.color;
                animCtx.shadowBlur = 10;
                animCtx.fill();
                animCtx.shadowBlur = 0;
            }

            // Shockwave ripples
            for (var j = ripples.length - 1; j >= 0; j--) {
                var rip = ripples[j];
                rip.r += 1.4;
                rip.alpha -= 0.035;
                if (rip.alpha <= 0) {
                    ripples.splice(j, 1);
                    continue;
                }
                animCtx.beginPath();
                animCtx.arc(rip.x, rip.y, rip.r, 0, Math.PI * 2);
                animCtx.strokeStyle = rip.color;
                animCtx.globalAlpha = rip.alpha;
                animCtx.lineWidth = 2;
                animCtx.stroke();
                animCtx.globalAlpha = 1;
            }

            requestAnimationFrame(animate);
        }

        resize();
        animate();
        isGlobeReady = true;
        scheduleContinuousStream();
    }

    // Initialize Active Engine with retry timer for D3 loading
    function bootEngine() {
        if (typeof d3 !== 'undefined' && typeof d3.geoOrthographic === 'function') {
            initD3GlobeEngine();
        } else {
            var retries = 0;
            var timer = setInterval(function () {
                retries++;
                if (typeof d3 !== 'undefined' && typeof d3.geoOrthographic === 'function') {
                    clearInterval(timer);
                    initD3GlobeEngine();
                } else if (retries >= 25) { // 2.5s fallback
                    clearInterval(timer);
                    initFallbackRadarEngine();
                }
            }, 100);
        }
    }
    bootEngine();

    // ==================== Fullscreen Wallboard Mode ====================
    var fullButton = card.querySelector('[data-map-fullscreen]');

    function isCardFullscreen() {
        return card.classList.contains('is-fullscreen') ||
            document.fullscreenElement === card ||
            document.webkitFullscreenElement === card ||
            document.mozFullScreenElement === card;
    }

    function triggerEngineResize() {
        if (typeof onEngineResize === 'function') {
            requestAnimationFrame(onEngineResize);
            setTimeout(onEngineResize, 30);
            setTimeout(onEngineResize, 100);
            setTimeout(onEngineResize, 250);
            setTimeout(onEngineResize, 500);
            setTimeout(onEngineResize, 800);
        }
        try {
            window.dispatchEvent(new Event('resize'));
        } catch (e) {}
    }

    function updateFullscreenUi(active) {
        card.classList.toggle('is-fullscreen', active);
        var wrap = card.querySelector('.threat-map-wrap');
        if (wrap) wrap.classList.toggle('is-fs-wrap', active);
        document.body.classList.toggle('pasak-threat-fs', active);
        document.documentElement.classList.toggle('pasak-threat-fs', active);
        if (fullButton) {
            fullButton.setAttribute('aria-pressed', active ? 'true' : 'false');
            var text = active ? '{{ __("Exit full screen") }}' : '{{ __("Full screen") }}';
            fullButton.title = text;
            var textEl = fullButton.querySelector('.threat-fullscreen-text');
            if (textEl) textEl.textContent = text;
            fullButton.blur();
        }
        triggerEngineResize();
    }

    function toggleFullscreen(requestActive) {
        var shouldBeActive = (typeof requestActive === 'boolean') ? requestActive : !isCardFullscreen();

        if (shouldBeActive) {
            updateFullscreenUi(true);
            var reqFs = card.requestFullscreen || card.webkitRequestFullscreen || card.mozRequestFullScreen || card.msRequestFullscreen;
            if (reqFs) {
                try {
                    var p = reqFs.call(card);
                    if (p && typeof p.then === 'function') {
                        p.then(function () {
                            updateFullscreenUi(true);
                            triggerEngineResize();
                        }).catch(function () {
                            updateFullscreenUi(true);
                            triggerEngineResize();
                        });
                    }
                } catch (e) {
                    updateFullscreenUi(true);
                    triggerEngineResize();
                }
            }
        } else {
            updateFullscreenUi(false);
            var isNative = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement;
            if (isNative) {
                var exitFs = document.exitFullscreen || document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen;
                if (exitFs) {
                    try {
                        var pExit = exitFs.call(document);
                        if (pExit && typeof pExit.then === 'function') {
                            pExit.then(function () {
                                updateFullscreenUi(false);
                                triggerEngineResize();
                            }).catch(function () {
                                updateFullscreenUi(false);
                                triggerEngineResize();
                            });
                        }
                    } catch (e) {
                        updateFullscreenUi(false);
                        triggerEngineResize();
                    }
                }
            }
        }
    }

    ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange'].forEach(function (ev) {
        document.addEventListener(ev, function () {
            var isNative = !!(document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement);
            updateFullscreenUi(isNative);
        });
    });

    if (fullButton) {
        fullButton.addEventListener('click', function () {
            toggleFullscreen();
        });
    }


    // ==================== Quarantine Modal Helpers ====================
    window.selectBlockScope = function (scope) {
        var hiddenScope = card.querySelector('#blockModalScope');
        var cardDevice = card.querySelector('#scopeCardDevice');
        var cardIp = card.querySelector('#scopeCardIp');
        var submitLabel = card.querySelector('#blockModalSubmitLabel');
        var submitBtn = card.querySelector('#blockModalSubmitBtn');

        if (hiddenScope) hiddenScope.value = scope;

        if (scope === 'device') {
            if (cardDevice) {
                cardDevice.style.border = '2px solid #38bdf8';
                cardDevice.style.background = 'rgba(56, 189, 248, 0.15)';
            }
            if (cardIp) {
                cardIp.style.border = '1.5px solid rgba(255, 255, 255, 0.1)';
                cardIp.style.background = 'rgba(15, 23, 42, 0.6)';
            }
            if (submitLabel) submitLabel.textContent = '{{ __("Blokir Perangkat") }}';
            if (submitBtn) {
                submitBtn.style.background = 'rgba(56, 189, 248, 0.25)';
                submitBtn.style.borderColor = '#38bdf8';
                submitBtn.style.color = '#7dd3fc';
            }
        } else {
            if (cardIp) {
                cardIp.style.border = '2px solid #ef4444';
                cardIp.style.background = 'rgba(239, 68, 68, 0.15)';
            }
            if (cardDevice) {
                cardDevice.style.border = '1.5px solid rgba(255, 255, 255, 0.1)';
                cardDevice.style.background = 'rgba(15, 23, 42, 0.6)';
            }
            if (submitLabel) submitLabel.textContent = '{{ __("Blokir Seluruh IP") }}';
            if (submitBtn) {
                submitBtn.style.background = 'rgba(239, 68, 68, 0.25)';
                submitBtn.style.borderColor = '#ef4444';
                submitBtn.style.color = '#fca5a5';
            }
        }
    };

    window.openBlockModal = function (data) {
        var modal = card.querySelector('#blockIpModal');
        if (!modal) return;

        var inputIp = card.querySelector('#blockModalIp');
        var inputReason = card.querySelector('#blockModalReason');
        var inputNotes = card.querySelector('#blockModalNotes');
        var inputDevice = card.querySelector('#blockModalDeviceId');
        var inputLocalIp = card.querySelector('#blockModalLocalIp');

        if (inputIp) inputIp.value = (data && data.ip) || '';
        if (inputReason) inputReason.value = (data && data.reason) || '';
        if (inputNotes) inputNotes.value = '';
        if (inputDevice) inputDevice.value = (data && data.device_id) || '';
        if (inputLocalIp) inputLocalIp.value = (data && data.local_ip) || '';

        selectBlockScope((data && data.device_id) ? 'device' : 'ip');

        modal.hidden = false;
        modal.style.display = 'flex';
        setTimeout(function () {
            if (inputIp) inputIp.focus();
        }, 50);
    };

    window.closeBlockModal = function () {
        var modal = card.querySelector('#blockIpModal');
        if (modal) {
            modal.hidden = true;
            modal.style.display = 'none';
        }
    };

    var blockModalBackdrop = card.querySelector('#blockIpModal');
    if (blockModalBackdrop) {
        blockModalBackdrop.addEventListener('click', function (e) {
            if (e.target === blockModalBackdrop) closeBlockModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var modal = card.querySelector('#blockIpModal');
            if (modal && !modal.hidden && modal.style.display !== 'none') {
                closeBlockModal();
                return;
            }
            if (isCardFullscreen()) {
                toggleFullscreen(false);
            }
        }
    });
})();
</script>
@endsection

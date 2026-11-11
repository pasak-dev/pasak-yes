@extends('pages.pasak.layout', ['heading' => __('User Sessions'), 'subheading' => __('Sesi login aktif, riwayat perangkat, dan IP terpercaya')])

@section('page_support_key', 'sessions')

@section('content')
@php
    $DV = \Internal\Pasak\Support\DashboardView::class;
@endphp
<div class="sec-grid-4">
    <div class="sec-stat-card">
        <div class="sec-stat-label">{{ __('Online now (5 min)') }}</div>
        <div class="sec-stat-value" style="color: var(--sec-success);">{{ number_format($onlineCount ?? 0) }}</div>
    </div>
    <div class="sec-stat-card">
        <div class="sec-stat-label">{{ __('Trusted IPs') }}</div>
        <div class="sec-stat-value">{{ number_format(count($trustedIps ?? [])) }}</div>
    </div>
</div>

<div class="sec-card">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap;">
        <div>
            <h3 class="sec-card-title">{{ __('Trust my current IP') }}</h3>
            <p class="sec-card-subtitle" style="margin-top: 4px;">
                {{ __('Hanya IP dari sesi login Anda saat ini yang dapat disimpan sebagai IP terpercaya, bukan seluruh riwayat login.') }}
            </p>
        </div>
        <div>
            @if ($isCurrentIpTrusted ?? false)
                <span class="sec-badge sec-badge-success" style="font-size: 12px; padding: 6px 12px;">
                    🛡️ {{ __('IP Saat Ini Terverifikasi') }}
                </span>
            @else
                <span class="sec-badge sec-badge-low" style="font-size: 12px; padding: 6px 12px;">
                    {{ __('IP Belum Terdaftar') }}
                </span>
            @endif
        </div>
    </div>

    <div style="margin-top: 16px; padding: 14px 16px; background: var(--sec-bg-subtle); border: 1px solid var(--sec-border); border-radius: 8px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 24px; flex-wrap: wrap;">
            <div>
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--sec-text-muted); font-weight: 600;">{{ __('Akun Aktif') }}</div>
                <div style="font-size: 13.5px; font-weight: 600; color: var(--sec-text); margin-top: 2px;">
                    {{ $currentUser?->name ?? auth()->user()?->name ?? 'User' }}
                    <span style="font-size: 12px; font-weight: normal; color: var(--sec-text-muted);">({{ $currentUser?->email ?? auth()->user()?->email ?? '-' }})</span>
                </div>
            </div>
            <div>
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--sec-text-muted); font-weight: 600;">{{ __('IP Sesi Saat Ini') }}</div>
                <div class="sec-font-mono" style="font-size: 14px; font-weight: 700; color: var(--sec-primary); margin-top: 2px;">
                    {{ $currentIp ?? request()->ip() }}
                </div>
            </div>
            <div>
                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--sec-text-muted); font-weight: 600;">{{ __('Status Proteksi Akun') }}</div>
                <div style="margin-top: 4px;">
                    @if ($isCurrentIpTrusted ?? false)
                        <span style="font-size: 12px; color: var(--sec-success); font-weight: 600;">✓ {{ __('Terpercaya (Bypass Quota/Karantina)') }}</span>
                    @else
                        <span style="font-size: 12px; color: var(--sec-text-muted);">{{ __('Akses Standar (Sesi Aktif)') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
            @if ($isCurrentIpTrusted ?? false)
                @if (!empty($currentTrusted?->id))
                    <form method="POST" action="{{ route('pasak.dashboard.trusted-ips.destroy', $currentTrusted->id) }}" data-sec-confirm="{{ __('Hapus IP saat ini (:ip) dari daftar IP terpercaya akun Anda?', ['ip' => $currentIp ?? request()->ip()]) }}" data-confirm-title="{{ __('Konfirmasi Hapus IP Terpercaya') }}" data-confirm-type="danger" data-confirm-btn="{{ __('Ya, Lepas Status') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="sec-btn sec-btn-secondary">{{ __('Hapus Dari IP Terpercaya') }}</button>
                    </form>
                @endif
            @else
                <form method="POST" action="{{ route('pasak.dashboard.trusted-ips.store-my-ip') }}" data-sec-confirm="{{ __('Simpan IP sesi saat ini (:ip) sebagai IP terpercaya untuk akun Anda? IP login riwayat lain tidak akan terpengaruh.', ['ip' => $currentIp ?? request()->ip()]) }}" data-confirm-title="{{ __('Konfirmasi Simpan IP') }}" data-confirm-type="primary" data-confirm-btn="{{ __('Ya, Simpan IP') }}">
                    @csrf
                    <button type="submit" class="sec-btn sec-btn-primary">🛡️ {{ __('Save my IP') }}</button>
                </form>
            @endif
        </div>
    </div>
</div>

<div class="sec-card">
    <form method="GET" action="{{ route('pasak.dashboard.sessions') }}" class="sec-toolbar" style="margin-bottom: 0;">
        <div class="sec-toolbar-group" style="flex: 1;">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('Search IP, browser, user...') }}" class="sec-input" style="min-width: 200px; flex: 1;">
            <div style="display: inline-flex; align-items: center; gap: 6px;">
                <label for="filter_date_from" style="font-size: 12px; color: var(--sec-text-muted); font-weight: 500;">{{ __('From') }}:</label>
                <input id="filter_date_from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="sec-input" style="font-size: 13px;">
            </div>
            <div style="display: inline-flex; align-items: center; gap: 6px;">
                <label for="filter_date_to" style="font-size: 12px; color: var(--sec-text-muted); font-weight: 500;">{{ __('To') }}:</label>
                <input id="filter_date_to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="sec-input" style="font-size: 13px;">
            </div>
            <div style="display: inline-flex; align-items: center; gap: 6px;">
                <label for="filter_per_page" style="font-size: 12px; color: var(--sec-text-muted); font-weight: 500;">{{ __('Show') }}:</label>
                <select id="filter_per_page" name="per_page" class="sec-select" onchange="this.form.submit()" style="font-size: 13px;">
                    @foreach ([10, 25, 50, 100] as $n)
                        <option value="{{ $n }}" @selected(($filters['per_page'] ?? 25) == $n)>{{ $n }} / {{ __('page') }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="sec-btn sec-btn-primary">{{ __('Filter') }}</button>
            <a href="{{ route('pasak.dashboard.sessions') }}" class="sec-btn sec-btn-secondary">{{ __('Reset') }}</a>
        </div>
    </form>
</div>

<div class="sec-card sec-card-flush">
    <div class="sec-card-header">
        <h3 class="sec-card-title">{{ __('Login history') }} ({{ number_format($logins->total()) }})</h3>
    </div>
    <div class="sec-table-wrap">
        <table class="sec-table">
            <thead>
                <tr>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('IP address') }}</th>
                    <th>{{ __('Browser') }}</th>
                    <th>{{ __('Last activity') }}</th>
                    <th style="text-align: right;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse (($logins ?? []) as $login)
                    @php
                        $thisIp = $login->ip_address;
                        $isCurrentSession = ($login->session_id && $login->session_id === session()->getId()) || ($thisIp === ($currentIp ?? request()->ip()) && $login->user_id === ($currentUser?->id ?? auth()->id()));
                    @endphp
                    <tr @if ($isCurrentSession) style="background: rgba(37, 99, 235, 0.04);" @endif>
                        <td>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span>{{ $login->user?->name ?? $login->user?->email ?? '-' }}</span>
                                @if ($isCurrentSession)
                                    <span class="sec-badge sec-badge-success" style="font-size: 10px; padding: 2px 6px;">{{ __('Sesi Ini') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="sec-font-mono" style="font-weight: 600;">{{ $login->ip_address }}</td>
                        <td style="font-size: 12px;">{{ $login->browser }} · {{ $login->operating_system }}</td>
                        <td style="font-size: 12px; color: var(--sec-text-muted);" title="{{ $login->last_activity_at ? $DV::diffForHumans($login->last_activity_at) : '' }}">{{ $login->last_activity_at ? $DV::formatDate($login->last_activity_at, 'd M Y H:i:s') : '—' }}</td>
                        <td style="text-align: right; white-space: nowrap;">
                            @if ($login->session_id)
                                <form method="POST" action="{{ route('pasak.dashboard.sessions.destroy', $login->session_id) }}" style="display: inline;" data-sec-confirm="{{ __('Paksa keluar (logout) sesi pengguna ini sekarang?') }}" data-confirm-title="{{ __('Konfirmasi Putus Sesi') }}" data-confirm-type="warning" data-confirm-btn="{{ __('Ya, Logout Sesi') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sec-btn sec-btn-secondary sec-btn-sm">{{ __('Logout') }}</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('pasak.dashboard.user-sessions.destroy', $login->id) }}" style="display: inline;" data-sec-confirm="{{ __('Hapus rekaman sesi login ini secara permanen?') }}" data-confirm-title="{{ __('Konfirmasi Hapus Riwayat') }}" data-confirm-type="danger" data-confirm-btn="{{ __('Ya, Hapus') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="sec-btn sec-btn-ghost sec-btn-sm">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--sec-text-muted); padding: 32px;">{{ __('No sessions recorded.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="sec-datatable-footer">
        <div>
            @if ($logins->total() > 0)
                {{ __('Showing') }} <span style="font-weight: 600; color: var(--sec-text);">{{ $logins->firstItem() }}</span>
                {{ __('to') }} <span style="font-weight: 600; color: var(--sec-text);">{{ $logins->lastItem() }}</span>
                {{ __('of') }} <span style="font-weight: 600; color: var(--sec-text);">{{ number_format($logins->total()) }}</span> {{ __('entries') }}
            @else
                {{ __('Showing 0 entries') }}
            @endif
        </div>
        <div>
            {{ $logins->links('pages.pasak.pagination') }}
        </div>
    </div>
</div>

<div class="sec-card sec-card-flush">
    <div class="sec-card-header">
        <h3 class="sec-card-title">{{ __('Trusted IPs') }}</h3>
    </div>
    <div class="sec-table-wrap">
        <table class="sec-table">
            <thead>
                <tr>
                    <th>{{ __('User') }}</th>
                    <th>{{ __('IP address') }}</th>
                    <th>{{ __('Device') }}</th>
                    <th style="text-align: right;">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse (($trustedIps ?? []) as $ip)
                    <tr>
                        <td>{{ $ip->user?->name ?? $ip->user?->email ?? '-' }}</td>
                        <td class="sec-font-mono" style="font-weight: 600;">
                            {{ $ip->ip_address }}
                            @if ($ip->ip_address === ($currentIp ?? request()->ip()))
                                <span class="sec-badge sec-badge-success" style="font-size: 10px; padding: 2px 6px; margin-left: 4px;">{{ __('IP Anda Saat Ini') }}</span>
                            @endif
                        </td>
                        <td style="font-size: 12px;">{{ $ip->device_name ?? '-' }}</td>
                        <td style="text-align: right;">
                            <form method="POST" action="{{ route('pasak.dashboard.trusted-ips.destroy', $ip->id) }}" data-sec-confirm="{{ __('Hapus IP ini dari daftar IP terpercaya?') }}" data-confirm-title="{{ __('Konfirmasi Hapus IP Terpercaya') }}" data-confirm-type="danger" data-confirm-btn="{{ __('Ya, Hapus') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="sec-btn sec-btn-ghost sec-btn-sm">🗑️</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--sec-text-muted); padding: 32px;">{{ __('No trusted IPs.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

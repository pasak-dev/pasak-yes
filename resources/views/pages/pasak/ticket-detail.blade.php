@extends('pages.pasak.layout', ['heading' => __('Ticket :number', ['number' => $ticket->ticket_number]), 'subheading' => $ticket->name . ' — ' . $ticket->ip_address])

@section('page_support_key', 'ticket-detail')

@section('content')
@php
    $DV = \Internal\Pasak\Support\DashboardView::class;
@endphp
<a href="{{ $urls['index'] }}" class="sec-back-link">← {{ __('Back to appeals') }}</a>

<div class="sec-card">
    <div class="sec-detail-grid">
        <div class="sec-detail-item">
            <div class="sec-detail-label">{{ __('Status') }}</div>
            <div class="sec-detail-value">
                <span class="sec-badge {{ $ticket->status === 'approved' ? 'sec-badge-success' : ($ticket->status === 'rejected' ? 'sec-badge-critical' : 'sec-badge-high') }}">
                    {{ $ticket->status }}
                </span>
            </div>
        </div>
        <div class="sec-detail-item">
            <div class="sec-detail-label">{{ __('IP address') }}</div>
            <div class="sec-detail-value sec-font-mono" style="font-weight: 600;">{{ $ticket->ip_address }}</div>
        </div>
        <div class="sec-detail-item">
            <div class="sec-detail-label">{{ __('Requester') }}</div>
            <div class="sec-detail-value">{{ $ticket->name }}</div>
        </div>
        <div class="sec-detail-item">
            <div class="sec-detail-label">{{ __('Email') }}</div>
            <div class="sec-detail-value">{{ $ticket->email }}</div>
        </div>
        @if ($ticket->phone)
            <div class="sec-detail-item">
                <div class="sec-detail-label">{{ __('Phone') }}</div>
                <div class="sec-detail-value">{{ $ticket->phone }}</div>
            </div>
        @endif
        <div class="sec-detail-item">
            <div class="sec-detail-label">{{ __('Submitted') }}</div>
            <div class="sec-detail-value">{{ $DV::formatDate($ticket->created_at, 'd M Y H:i:s') }} <span style="font-size: 12px; color: var(--sec-text-muted); margin-left: 6px;">({{ $DV::diffForHumans($ticket->created_at) }})</span></div>
        </div>
        @if ($ticket->device_id || $ticket->local_ip)
            <div class="sec-detail-item">
                <div class="sec-detail-label">{{ __('Device') }}</div>
                <div class="sec-detail-value sec-font-mono">{{ $ticket->device_id ?? '—' }} / {{ $ticket->local_ip ?? '—' }}</div>
            </div>
        @endif
        @if ($ticket->resolved_at)
            <div class="sec-detail-item">
                <div class="sec-detail-label">{{ __('Resolved') }}</div>
                <div class="sec-detail-value">{{ $ticket->resolved_at ? $DV::formatDate($ticket->resolved_at, 'd M Y H:i:s') : '—' }}{{ $ticket->resolver ? ' — ' . $ticket->resolver->name : '' }}</div>
            </div>
        @endif
    </div>
</div>

<div class="sec-card">
    <div class="sec-card-body">
        <div class="sec-detail-label">{{ __('Requester reason') }}</div>
        <pre class="sec-code-block">{{ $ticket->reason }}</pre>
        @if ($ticket->user_agent)
            <div class="sec-detail-label" style="margin-top: 14px;">{{ __('User agent') }}</div>
            <div class="sec-detail-value" style="font-size: 12.5px;">{{ $ticket->user_agent }}</div>
        @endif
        @if ($ticket->admin_notes)
            <div class="sec-detail-label" style="margin-top: 14px;">{{ __('Admin notes') }}</div>
            <pre class="sec-code-block">{{ $ticket->admin_notes }}</pre>
        @endif
    </div>
</div>

@if ($ticket->blockedIp)
    <div class="sec-card">
        <div class="sec-card-header">
            <h3 class="sec-card-title">{{ __('Related block') }}</h3>
            <a href="{{ route('pasak.dashboard.blocked-ips.show', $ticket->blockedIp->id) }}" class="sec-btn sec-btn-secondary sec-btn-sm">{{ __('View block') }}</a>
        </div>
        <div class="sec-card-body">
            <div class="sec-detail-grid">
                <div class="sec-detail-item">
                    <div class="sec-detail-label">{{ __('Status') }}</div>
                    <div class="sec-detail-value">{{ $ticket->blockedIp->is_active ? __('Active') : __('Inactive') }}</div>
                </div>
                <div class="sec-detail-item">
                    <div class="sec-detail-label">{{ __('Reason') }}</div>
                    <div class="sec-detail-value">{{ $ticket->blockedIp->reason ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>
@endif

@php($leadLogs = $logsLeadingToBlock ?? $relatedLogs ?? collect())
@if ($leadLogs->isNotEmpty())
    {{-- Kronologi Aktivitas Serangan Lengkap Sampai IP Diblokir --}}
    <div class="sec-card sec-card-flush">
        <div class="sec-card-header">
            <div>
                <h3 class="sec-card-title" style="display: flex; align-items: center; gap: 8px;">
                    <span>🚨</span>
                    <span>{{ __('Kronologi Aktivitas IP Menuju Pemblokiran') }}</span>
                    <span class="sec-badge sec-badge-critical" style="font-size: 11px;">{{ $leadLogs->count() }} {{ __('kejadian tercatat') }}</span>
                </h3>
                <p class="sec-card-subtitle">{{ __('Seluruh jejak serangan, request, dan anomali yang dilakukan oleh IP ini secara berurutan hingga WAF menerapkan karantina.') }}</p>
            </div>
        </div>
        <div class="sec-table-wrap">
            <table class="sec-table">
                <thead>
                    <tr>
                        <th style="width: 44px; text-align: center;">#</th>
                        <th>{{ __('Waktu') }}</th>
                        <th>{{ __('Metode & Path Target') }}</th>
                        <th>{{ __('Level & Jenis Event') }}</th>
                        <th>{{ __('Aturan / Deskripsi') }}</th>
                        <th>{{ __('Bukti Cuplikan (Evidence / Payload)') }}</th>
                        <th>{{ __('Tindakan') }}</th>
                        <th style="text-align: right;">{{ __('Detail') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leadLogs as $idx => $log)
                        @php($lvl = strtolower($log->threat_level ?? 'low'))
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--sec-text-muted); font-size: 11px;" class="sec-font-mono">
                                {{ $idx + 1 }}
                            </td>
                            <td class="sec-font-mono" style="font-size: 12px; white-space: nowrap;">
                                <div style="font-weight: 600;">{{ $DV::formatDate($log->created_at, 'd M Y H:i:s') }}</div>
                                <div style="font-size: 11px; color: var(--sec-text-muted);">{{ $log->created_at ? $DV::diffForHumans($log->created_at) : '' }}</div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="sec-badge sec-badge-zinc sec-font-mono" style="font-size: 10.5px; font-weight: 800;">{{ $log->method ?? 'GET' }}</span>
                                    <span class="sec-font-mono" style="font-size: 12px; word-break: break-all; max-width: 240px;" title="{{ $log->path }}">{{ $log->path ?? '/' }}</span>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                    <span class="sec-badge {{ $lvl === 'critical' ? 'sec-badge-critical' : ($lvl === 'high' ? 'sec-badge-high' : ($lvl === 'medium' ? 'sec-badge-medium' : 'sec-badge-low')) }}">
                                        {{ $lvl }}
                                    </span>
                                    <span style="font-size: 11.5px; font-weight: 600; color: var(--sec-text);">{{ $log->event_type ?? '-' }}</span>
                                </div>
                            </td>
                            <td style="font-size: 12px; color: var(--sec-text-muted); max-width: 200px;" title="{{ $log->rule_label }}">
                                {{ $log->rule_label ?? '-' }}
                            </td>
                            <td style="max-width: 260px;">
                                @if (! empty($log->evidence))
                                    <pre class="sec-font-mono" style="margin: 0; padding: 4px 8px; font-size: 11px; background: rgba(0,0,0,0.25); border-radius: 4px; color: var(--sec-danger); max-height: 60px; overflow-y: auto; white-space: pre-wrap; word-break: break-all;" title="{{ $log->evidence }}">{{ \Illuminate\Support\Str::limit((string) $log->evidence, 140) }}</pre>
                                @else
                                    <span style="color: var(--sec-text-subtle); font-size: 11px;">—</span>
                                @endif
                            </td>
                            <td>
                                @if (($log->action_taken ?? '') === 'blocked' || $log->blocked)
                                    <span class="sec-badge sec-badge-critical">BLOCKED (403)</span>
                                @elseif (($log->action_taken ?? '') === 'quarantined')
                                    <span class="sec-badge sec-badge-critical">QUARANTINED</span>
                                @else
                                    <span class="sec-badge sec-badge-warning">DETECTED</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('pasak.dashboard.logs.show', $log->id) }}" class="sec-btn sec-btn-ghost sec-btn-sm" style="font-size: 11px;">🔍 {{ __('Detail') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Titik Pemutusan Blokir WAF --}}
        @if (!empty($blockedAt))
            <div style="padding: 14px 18px; background: rgba(239, 68, 68, 0.1); border-top: 1px solid rgba(239, 68, 68, 0.25); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 22px;">🛑</span>
                    <div>
                        <strong style="color: var(--sec-danger); font-size: 13.5px;">{{ __('TITIK PEMBLOKIRAN DITERAPKAN OLEH SISTEM') }}</strong>
                        <div style="margin-top: 2px; font-size: 12.5px; color: var(--sec-text);">
                            {{ __('IP ini mencapai batas ambang ancaman dan otomatis dikarantina pada') }}
                            <strong class="sec-font-mono" style="color: var(--sec-danger);">{{ $blockedAt }}</strong>
                            @if ($ticket->blockedIp && $ticket->blockedIp->reason)
                                · {{ __('Alasan:') }} <em>{{ $ticket->blockedIp->reason }}</em>
                            @endif
                        </div>
                    </div>
                </div>
                <span class="sec-badge sec-badge-critical" style="font-size: 11px; padding: 4px 10px;">⛔ {{ __('STATUS: DIBLOKIR') }}</span>
            </div>
        @endif
    </div>
@endif

{{-- Aktivitas Pasca-Pemblokiran (Request lanjutan yang langsung ditolak 403) --}}
@if (isset($logsAfterBlock) && $logsAfterBlock->isNotEmpty())
    <div class="sec-card sec-card-flush">
        <div class="sec-card-header">
            <div>
                <h3 class="sec-card-title" style="display: flex; align-items: center; gap: 8px;">
                    <span>🛡️</span>
                    <span>{{ __('Aktivitas Pasca-Pemblokiran (Percobaan Ditolak HTTP 403)') }}</span>
                    <span class="sec-badge sec-badge-zinc" style="font-size: 11px;">{{ $logsAfterBlock->count() }} hits</span>
                </h3>
                <p class="sec-card-subtitle">{{ __('Request yang tetap dikirimkan oleh IP ini setelah diblokir, dan langsung ditolak oleh pertahanan WAF.') }}</p>
            </div>
        </div>
        <div class="sec-table-wrap">
            <table class="sec-table">
                <thead>
                    <tr>
                        <th>{{ __('Waktu') }}</th>
                        <th>{{ __('Metode & Path') }}</th>
                        <th>{{ __('Jenis Event') }}</th>
                        <th>{{ __('Respon WAF') }}</th>
                        <th style="text-align: right;">{{ __('Detail') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logsAfterBlock as $pLog)
                        <tr>
                            <td class="sec-font-mono" style="font-size: 12px; white-space: nowrap;" title="{{ $DV::diffForHumans($pLog->created_at) }}">{{ $DV::formatDate($pLog->created_at, 'Y-m-d H:i:s') }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span class="sec-badge sec-badge-zinc sec-font-mono" style="font-size: 10px;">{{ $pLog->method ?? 'GET' }}</span>
                                    <span class="sec-font-mono" style="font-size: 12px;" title="{{ $pLog->path }}">{{ $pLog->path ?? '/' }}</span>
                                </div>
                            </td>
                            <td style="font-size: 12px;">{{ $pLog->event_type ?? '-' }}</td>
                            <td><span class="sec-badge sec-badge-critical">BLOCKED (HTTP 403)</span></td>
                            <td style="text-align: right;">
                                <a href="{{ route('pasak.dashboard.logs.show', $pLog->id) }}" class="sec-btn sec-btn-ghost sec-btn-sm" style="font-size: 11px;">🔍 {{ __('Detail') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if ($ticket->isPending())
    <div class="sec-card">
        <div class="sec-card-header">
            <h3 class="sec-card-title">{{ __('Respond to appeal') }}</h3>
        </div>
        <div class="sec-card-body">
            <form method="POST" action="{{ $urls['respond'] }}" style="display: flex; flex-direction: column; gap: 12px;">
                @csrf
                <div>
                    <label class="sec-label" for="admin_notes">{{ __('Admin notes') }}</label>
                    <textarea id="admin_notes" name="admin_notes" rows="3" class="sec-textarea" placeholder="{{ __('Reason for approval or rejection...') }}"></textarea>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" name="action" value="approve" class="sec-btn sec-btn-primary" data-sec-confirm="{{ __('Setujui permohonan banding dan buka blokir IP ini secara otomatis?') }}" data-confirm-title="{{ __('Konfirmasi Persetujuan Banding') }}" data-confirm-type="primary" data-confirm-btn="{{ __('Ya, Setujui & Buka') }}">{{ __('Approve & unblock') }}</button>
                    <button type="submit" name="action" value="reject" class="sec-btn sec-btn-danger" data-sec-confirm="{{ __('Tolak permohonan banding pembukaan blokir ini?') }}" data-confirm-title="{{ __('Konfirmasi Penolakan Banding') }}" data-confirm-type="warning" data-confirm-btn="{{ __('Ya, Tolak Banding') }}">{{ __('Reject') }}</button>
                </div>
            </form>
        </div>
    </div>
@else
    <form method="POST" action="{{ $urls['destroy'] }}" data-sec-confirm="{{ __('Hapus tiket banding ini secara permanen?') }}" data-confirm-title="{{ __('Konfirmasi Hapus Tiket') }}" data-confirm-type="danger" data-confirm-btn="{{ __('Ya, Hapus') }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="sec-btn sec-btn-danger sec-btn-sm">{{ __('Delete ticket') }}</button>
    </form>
@endif
@endsection

@php
    $appLocale = config('app.locale', env('APP_LOCALE', 'en'));
    if (! in_array($appLocale, ['en', 'id'], true)) {
        $appLocale = 'en';
    }
    $currentLocale = (string) (request()->query('lang')
        ?? (request()->hasSession() ? session('pasak_locale') : null)
        ?? request()->cookie('pasak_locale')
        ?? $appLocale);
    if (! in_array($currentLocale, ['en', 'id'], true)) {
        $currentLocale = $appLocale;
    }
    app()->setLocale($currentLocale);
    \Illuminate\Support\Carbon::setLocale($currentLocale);
    if (class_exists(\Carbon\Carbon::class)) {
        \Carbon\Carbon::setLocale($currentLocale);
    }
    $pasakTimezone = \Internal\Pasak\Support\DashboardView::timezone();
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($heading ?? 'Dashboard') }} — Pasak</title>
    @php
        $customFavicon = config("pasak.dashboard.favicon");
        $defaultFavicon = "data:image/png;base64,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";
        $faviconUrl = $customFavicon ?: $defaultFavicon;
    @endphp
    @hasSection("favicon")
        @yield("favicon")
    @else
        <link rel="icon" type="image/png" href="{{ $faviconUrl }}">
        <link rel="shortcut icon" type="image/png" href="{{ $faviconUrl }}">
        <link rel="apple-touch-icon" href="{{ $faviconUrl }}">
    @endif
    <script>
        // Terapkan tema tersimpan sebelum paint agar tidak flicker.
        (function() {
            try {
                var mode = localStorage.getItem('pasak-theme') || 'auto';
                var isDark = mode === 'dark' || (mode !== 'light' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (mode === 'dark') {
                    document.documentElement.setAttribute('data-theme', 'dark');
                    document.documentElement.classList.add('dark');
                } else if (mode === 'light') {
                    document.documentElement.setAttribute('data-theme', 'light');
                    document.documentElement.classList.remove('dark');
                } else {
                    document.documentElement.removeAttribute('data-theme');
                    if (isDark) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            } catch (e) {
                /* abaikan: tetap ikuti sistem */ }
        })();
    </script>
    <style>
        :root,
        html,
        body.sec-body,
        .sec-root {
            --sec-font: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            --sec-font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            --sec-bg: #f8fafc;
            --sec-card: #ffffff;
            --sec-card-hover: #f1f5f9;
            --sec-border: #e2e8f0;
            --sec-border-light: #f1f5f9;
            --sec-text: #0f172a;
            --sec-text-muted: #64748b;
            --sec-text-subtle: #94a3b8;
            --sec-primary: #2563eb;
            --sec-primary-hover: #1d4ed8;
            --sec-primary-fg: #ffffff;
            --sec-danger: #ef4444;
            --sec-danger-bg: #fef2f2;
            --sec-danger-border: #fecaca;
            --sec-danger-hover: #dc2626;
            --sec-warning: #f59e0b;
            --sec-warning-bg: #fffbeb;
            --sec-warning-border: #fde68a;
            --sec-success: #10b981;
            --sec-success-bg: #ecfdf5;
            --sec-success-border: #a7f3d0;
            --sec-radius: 10px;
            --sec-radius-sm: 6px;
            --sec-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.07), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
            --sec-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            color-scheme: light;
        }

        body.sec-body {
            background: var(--sec-bg);
            color: var(--sec-text);
            font-family: var(--sec-font);
            margin: 0;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .sec-root {
            font-family: var(--sec-font);
            color: var(--sec-text);
            width: 100%;
            box-sizing: border-box;
        }

        /* Mode Gelap Otomatis dari Sistem OS (hanya berlaku jika pengguna memilih 'auto' / tanpa data-theme="light") */
        @media (prefers-color-scheme: dark) {

            :root:not([data-theme="light"]),
            html:not([data-theme="light"]),
            html:not([data-theme="light"]) body.sec-body,
            html:not([data-theme="light"]) .sec-root {
                color-scheme: dark;
                --sec-bg: #090d16;
                --sec-card: #111827;
                --sec-card-hover: #1e293b;
                --sec-border: #1e293b;
                --sec-border-light: #162032;
                --sec-text: #f9fafb;
                --sec-text-muted: #9ca3af;
                --sec-text-subtle: #6b7280;
                --sec-primary: #3b82f6;
                --sec-primary-hover: #2563eb;
                --sec-danger: #f87171;
                --sec-danger-bg: rgba(239, 68, 68, 0.15);
                --sec-danger-border: rgba(239, 68, 68, 0.3);
                --sec-warning: #fbbf24;
                --sec-warning-bg: rgba(245, 158, 11, 0.15);
                --sec-warning-border: rgba(245, 158, 11, 0.3);
                --sec-success: #34d399;
                --sec-success-bg: rgba(16, 185, 129, 0.15);
                --sec-success-border: rgba(16, 185, 129, 0.3);
                --sec-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.3);
            }
        }

        /* Override Mode Terang Eksplisit */
        html[data-theme="light"],
        html[data-theme="light"] body.sec-body,
        html[data-theme="light"] .sec-root,
        [data-theme="light"] {
            color-scheme: light !important;
            --sec-bg: #f8fafc !important;
            --sec-card: #ffffff !important;
            --sec-card-hover: #f1f5f9 !important;
            --sec-border: #e2e8f0 !important;
            --sec-border-light: #f1f5f9 !important;
            --sec-text: #0f172a !important;
            --sec-text-muted: #64748b !important;
            --sec-text-subtle: #94a3b8 !important;
            --sec-primary: #2563eb !important;
            --sec-primary-hover: #1d4ed8 !important;
            --sec-primary-fg: #ffffff !important;
            --sec-danger: #ef4444 !important;
            --sec-danger-bg: #fef2f2 !important;
            --sec-danger-border: #fecaca !important;
            --sec-danger-hover: #dc2626 !important;
            --sec-warning: #f59e0b !important;
            --sec-warning-bg: #fffbeb !important;
            --sec-warning-border: #fde68a !important;
            --sec-success: #10b981 !important;
            --sec-success-bg: #ecfdf5 !important;
            --sec-success-border: #a7f3d0 !important;
            --sec-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.07), 0 1px 2px -1px rgba(0, 0, 0, 0.05) !important;
            --sec-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.05) !important;
        }

        /* Override Mode Gelap Eksplisit */
        html[data-theme="dark"],
        html[data-theme="dark"] body.sec-body,
        html[data-theme="dark"] .sec-root,
        html.dark,
        html.dark body.sec-body,
        html.dark .sec-root,
        [data-theme="dark"] {
            color-scheme: dark !important;
            --sec-bg: #090d16 !important;
            --sec-card: #111827 !important;
            --sec-card-hover: #1e293b !important;
            --sec-border: #1e293b !important;
            --sec-border-light: #162032 !important;
            --sec-text: #f9fafb !important;
            --sec-text-muted: #9ca3af !important;
            --sec-text-subtle: #6b7280 !important;
            --sec-primary: #3b82f6 !important;
            --sec-primary-hover: #2563eb !important;
            --sec-danger: #f87171 !important;
            --sec-danger-bg: rgba(239, 68, 68, 0.15) !important;
            --sec-danger-border: rgba(239, 68, 68, 0.3) !important;
            --sec-warning: #fbbf24 !important;
            --sec-warning-bg: rgba(245, 158, 11, 0.15) !important;
            --sec-warning-border: rgba(245, 158, 11, 0.3) !important;
            --sec-success: #34d399 !important;
            --sec-success-bg: rgba(16, 185, 129, 0.15) !important;
            --sec-success-border: rgba(16, 185, 129, 0.3) !important;
            --sec-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.3) !important;
        }

        .sec-root * {
            box-sizing: border-box;
        }

        /* Layout Structure */
        .sec-container {
            display: flex;
            gap: 28px;
            align-items: flex-start;
            width: 100%;
        }

        .sec-sidebar {
            width: 210px;
            flex-shrink: 0;
            position: sticky;
            top: 20px;
        }

        .sec-main {
            flex: 1;
            min-width: 0;
            width: 100%;
        }

        /* Navigation */
        .sec-nav-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sec-nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            font-size: 14px;
            font-weight: 500;
            color: var(--sec-text-muted);
            text-decoration: none;
            border-radius: var(--sec-radius-sm);
            transition: all 0.15s ease-in-out;
        }

        .sec-nav-item:hover {
            color: var(--sec-text);
            background: var(--sec-card-hover);
        }

        .sec-nav-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .sec-nav-item.active {
            color: var(--sec-primary);
            background: rgba(37, 99, 235, 0.08);
            font-weight: 600;
        }

        .sec-nav-badge {
            margin-left: auto;
            background: var(--sec-danger);
            color: #fff;
            font-size: 11px;
            padding: 1px 6px;
            border-radius: 9999px;
            font-weight: 600;
        }

        /* Header (Sticky with Global Refresh Action) */
        .sec-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: var(--sec-bg);
            padding: 12px 0;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid var(--sec-border);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .sec-header-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1;
        }

        .sec-header-logo-link {
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
            text-decoration: none;
            transition: opacity 0.15s ease-in-out;
        }

        .sec-header-logo-link:hover {
            opacity: 0.85;
        }

        .sec-header-logo {
            height: 28px;
            width: auto;
            object-fit: contain;
            display: block;
        }

        .sec-header-logo-dark {
            display: none !important;
        }

        .sec-header-logo-light {
            display: block !important;
        }

        html[data-theme="dark"] .sec-header-logo-light,
        html.dark .sec-header-logo-light {
            display: none !important;
        }

        html[data-theme="dark"] .sec-header-logo-dark,
        html.dark .sec-header-logo-dark {
            display: block !important;
        }

        @media (prefers-color-scheme: dark) {
            :root:not([data-theme="light"]) .sec-header-logo-light,
            html:not([data-theme="light"]) .sec-header-logo-light {
                display: none !important;
            }
            :root:not([data-theme="light"]) .sec-header-logo-dark,
            html:not([data-theme="light"]) .sec-header-logo-dark {
                display: block !important;
            }
        }

        .sec-header-divider {
            width: 1px;
            height: 26px;
            background: var(--sec-border);
            flex-shrink: 0;
        }

        .sec-header-titles {
            min-width: 0;
            flex: 1;
        }

        .sec-page-header {
            margin-bottom: 22px;
        }

        .sec-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 6px 0;
            color: var(--sec-text);
            letter-spacing: -0.02em;
            line-height: 1.25;
        }

        .sec-subtitle {
            font-size: 13.5px;
            color: var(--sec-text-muted);
            margin: 0;
            line-height: 1.5;
        }

        .sec-header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            margin-left: auto;
        }

        .sec-client-ip-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            height: 36px;
            padding: 0 12px;
            border-radius: var(--sec-radius-sm);
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            color: var(--sec-text);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }

        .sec-client-ip-badge:hover {
            background: var(--sec-card-hover);
            border-color: var(--sec-primary);
            color: var(--sec-primary);
        }

        .sec-client-ip-badge:active {
            transform: scale(0.97);
        }

        .sec-ip-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--sec-success);
            box-shadow: 0 0 0 2px var(--sec-success-bg);
            flex-shrink: 0;
        }

        .sec-ip-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--sec-text-subtle);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .sec-ip-address {
            font-family: var(--sec-font-mono);
            font-size: 13px;
            font-weight: 700;
            color: var(--sec-text);
            letter-spacing: -0.01em;
        }

        @media (max-width: 640px) {
            .sec-ip-label {
                display: none;
            }
        }

        .sec-refresh-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 600;
            height: 36px;
            padding: 0 14px;
            border-radius: var(--sec-radius-sm);
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            color: var(--sec-text);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }

        .sec-refresh-btn:hover {
            background: var(--sec-card-hover);
            border-color: var(--sec-primary);
            color: var(--sec-primary);
        }

        .sec-refresh-btn:active {
            transform: scale(0.97);
        }

        .sec-refresh-btn.is-refreshing .sec-refresh-icon {
            animation: sec-spin 0.8s linear infinite;
        }

        .sec-lang-switcher {
            display: inline-flex;
            align-items: center;
            height: 36px;
            padding: 2px 3px;
            border-radius: var(--sec-radius-sm);
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            user-select: none;
            gap: 2px;
        }

        .sec-lang-btn {
            background: transparent;
            border: none;
            color: var(--sec-text-muted);
            font-size: 12px;
            font-weight: 700;
            padding: 5px 8px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            line-height: 1;
        }

        .sec-lang-btn:hover {
            color: var(--sec-text);
            background: var(--sec-card-hover);
        }

        .sec-lang-btn.is-active {
            color: #ffffff;
            background: var(--sec-primary);
        }

        .sec-lang-sep {
            color: var(--sec-border);
            font-size: 11px;
            font-weight: 500;
        }

        .sec-support-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 600;
            height: 36px;
            padding: 0 13px;
            border-radius: var(--sec-radius-sm);
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            color: var(--sec-text-muted);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }

        .sec-support-btn:hover {
            background: var(--sec-card-hover);
            border-color: var(--sec-primary);
            color: var(--sec-primary);
        }

        .sec-support-btn:active {
            transform: scale(0.97);
        }

        .sec-support-modal-box {
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius);
            box-shadow: var(--sec-shadow-md);
            max-width: 780px;
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: sec-modal-in 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sec-support-modal-body {
            padding: 22px 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
            font-size: 13.5px;
            line-height: 1.6;
            color: var(--sec-text);
        }

        .sec-support-section {
            background: var(--sec-bg);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius-sm);
            padding: 14px 16px;
        }

        .sec-support-section-title {
            font-size: 12.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--sec-text-muted);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sec-support-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 10px;
            margin-top: 8px;
        }

        .sec-support-item {
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 13px;
        }

        .sec-support-item-title {
            font-weight: 600;
            color: var(--sec-text);
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sec-support-item-desc {
            font-size: 12px;
            color: var(--sec-text-muted);
            line-height: 1.45;
        }

        .sec-back-app-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 600;
            height: 36px;
            padding: 0 13px;
            border-radius: var(--sec-radius-sm);
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            color: var(--sec-text-muted);
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }

        .sec-back-app-btn:hover {
            background: var(--sec-card-hover);
            border-color: var(--sec-primary);
            color: var(--sec-primary);
        }

        .sec-back-app-btn:active {
            transform: scale(0.97);
        }

        .sec-sidebar-brand {
            margin-bottom: 12px;
            padding: 2px 4px;
            display: flex;
            align-items: center;
        }

        .sec-sidebar-logo-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            transition: opacity 0.15s ease-in-out;
        }

        .sec-sidebar-logo-link:hover {
            opacity: 0.85;
        }

        .sec-sidebar-back-link {
            margin-top: 0;
            margin-bottom: 12px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--sec-text-muted);
            background: var(--sec-card);
            border: 1px dashed var(--sec-border);
            border-radius: var(--sec-radius-sm);
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }

        .sec-sidebar-back-link:hover {
            color: var(--sec-primary);
            background: var(--sec-card-hover);
            border-color: var(--sec-primary);
            border-style: solid;
        }

        @keyframes sec-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Cards */
        .sec-card {
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius);
            padding: 20px;
            box-shadow: var(--sec-shadow);
            margin-bottom: 20px;
        }

        .sec-card-flush {
            padding: 0;
            overflow: hidden;
        }

        .sec-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--sec-border);
            flex-wrap: wrap;
            gap: 12px;
        }

        .sec-card-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            color: var(--sec-text);
        }

        .sec-card-subtitle {
            font-size: 13px;
            color: var(--sec-text-muted);
            margin: 2px 0 0 0;
        }

        .sec-card-body {
            padding: 20px;
        }

        .sec-card-desc {
            font-size: 13px;
            color: var(--sec-text-muted);
            margin: 0 0 14px 0;
            line-height: 1.55;
        }

        .sec-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 13px;
            color: var(--sec-text);
        }

        .sec-content>*:last-child {
            margin-bottom: 0;
        }

        /* Grids */
        .sec-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .sec-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .sec-stat-card {
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius);
            padding: 18px 20px;
            box-shadow: var(--sec-shadow);
        }

        .sec-stat-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--sec-text-muted);
            margin-bottom: 6px;
        }

        .sec-stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--sec-text);
            line-height: 1.1;
            margin-bottom: 6px;
            font-variant-numeric: tabular-nums;
        }

        .sec-stat-value.sec-text-danger {
            color: var(--sec-danger);
        }

        .sec-stat-desc {
            font-size: 12px;
            color: var(--sec-text-subtle);
        }

        /* Toolbar & Controls */
        .sec-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .sec-toolbar-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .sec-input,
        .sec-select {
            height: 38px;
            padding: 0 12px;
            font-size: 14px;
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius-sm);
            color: var(--sec-text);
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .sec-input:focus,
        .sec-select:focus,
        .sec-textarea:focus {
            outline: none;
            border-color: var(--sec-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .sec-textarea {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius-sm);
            color: var(--sec-text);
            font-family: inherit;
        }

        /* Buttons */
        .sec-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 38px;
            padding: 0 16px;
            font-size: 14px;
            font-weight: 500;
            border-radius: var(--sec-radius-sm);
            cursor: pointer;
            border: 1px solid transparent;
            font-family: inherit;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            white-space: nowrap;
        }

        .sec-btn-sm {
            height: 30px;
            padding: 0 10px;
            font-size: 12px;
        }

        .sec-btn-primary {
            background: var(--sec-primary);
            color: #fff;
        }

        .sec-btn-primary:hover {
            background: var(--sec-primary-hover);
        }

        .sec-btn-danger {
            background: var(--sec-danger);
            color: #fff;
        }

        .sec-btn-danger:hover {
            background: var(--sec-danger-hover);
        }

        .sec-btn-secondary {
            background: var(--sec-card);
            border-color: var(--sec-border);
            color: var(--sec-text);
        }

        .sec-btn-secondary:hover {
            background: var(--sec-card-hover);
        }

        .sec-btn-ghost {
            background: transparent;
            color: var(--sec-text-muted);
            border: none;
            padding: 4px 8px;
            height: auto;
        }

        .sec-btn-ghost:hover {
            color: var(--sec-text);
            background: var(--sec-card-hover);
        }

        /* Badges */
        .sec-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .sec-badge-critical,
        .sec-badge-red {
            background: var(--sec-danger-bg);
            color: var(--sec-danger);
            border: 1px solid var(--sec-danger-border);
        }

        .sec-badge-high,
        .sec-badge-orange,
        .sec-badge-amber,
        .sec-badge-warning {
            background: var(--sec-warning-bg);
            color: var(--sec-warning);
            border: 1px solid var(--sec-warning-border);
        }

        .sec-badge-medium {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .sec-badge-low,
        .sec-badge-zinc {
            background: var(--sec-card-hover);
            color: var(--sec-text-muted);
            border: 1px solid var(--sec-border);
        }

        .sec-badge-success,
        .sec-badge-green {
            background: var(--sec-success-bg);
            color: var(--sec-success);
            border: 1px solid var(--sec-success-border);
        }

        /* Table */
        .sec-table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        .sec-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .sec-table th {
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--sec-text-muted);
            background: var(--sec-card-hover);
            border-bottom: 1px solid var(--sec-border);
            white-space: nowrap;
        }

        .sec-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--sec-border-light);
            color: var(--sec-text);
            vertical-align: middle;
        }

        .sec-table tr:last-child td {
            border-bottom: none;
        }

        .sec-table tr:hover td {
            background: var(--sec-card-hover);
        }

        /* Data Table & Pagination */
        .sec-datatable-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid var(--sec-border);
            font-size: 13px;
            color: var(--sec-text-muted);
            flex-wrap: wrap;
            gap: 12px;
        }

        .sec-pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
        }

        .sec-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            font-size: 13px;
            font-weight: 500;
            border-radius: var(--sec-radius-sm);
            border: 1px solid var(--sec-border);
            background: var(--sec-card);
            color: var(--sec-text);
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }

        .sec-page-btn:hover:not(.disabled) {
            background: var(--sec-card-hover);
            border-color: var(--sec-border-light);
            color: var(--sec-text);
        }

        .sec-page-btn.active {
            background: var(--sec-primary);
            border-color: var(--sec-primary);
            color: #fff;
            font-weight: 600;
        }

        .sec-page-btn.disabled {
            opacity: 0.35;
            cursor: not-allowed;
            pointer-events: none;
        }

        .sec-page-ellipsis {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 32px;
            color: var(--sec-text-muted);
            font-size: 13px;
        }

        .sec-font-mono {
            font-family: var(--sec-font-mono);
        }

        /* Floating Toast Notifications */
        .sec-toast-stack {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 100000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 420px;
            width: calc(100vw - 48px);
            pointer-events: none;
        }

        .sec-toast {
            pointer-events: auto;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: var(--sec-radius);
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            color: var(--sec-text);
            font-size: 13.5px;
            line-height: 1.45;
            animation: sec-toast-slide 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .sec-toast.sec-toast-hiding {
            opacity: 0;
            transform: translateX(30px);
        }

        @keyframes sec-toast-slide {
            from {
                opacity: 0;
                transform: translateY(-16px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .sec-toast-icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .sec-toast-success .sec-toast-icon {
            background: var(--sec-success-bg);
            color: var(--sec-success);
            border: 1px solid var(--sec-success-border);
        }

        .sec-toast-error .sec-toast-icon,
        .sec-toast-danger .sec-toast-icon {
            background: var(--sec-danger-bg);
            color: var(--sec-danger);
            border: 1px solid var(--sec-danger-border);
        }

        .sec-toast-warning .sec-toast-icon {
            background: var(--sec-warning-bg);
            color: var(--sec-warning);
            border: 1px solid var(--sec-warning-border);
        }

        .sec-toast-info .sec-toast-icon {
            background: rgba(37, 99, 235, 0.1);
            color: var(--sec-primary);
            border: 1px solid rgba(37, 99, 235, 0.25);
        }

        .sec-toast-content {
            flex: 1;
            min-width: 0;
        }

        .sec-toast-title {
            font-weight: 700;
            font-size: 13.5px;
            margin-bottom: 2px;
            color: var(--sec-text);
        }

        .sec-toast-message {
            font-size: 13px;
            color: var(--sec-text-muted);
            word-break: break-word;
        }

        .sec-toast-close {
            background: transparent;
            border: none;
            color: var(--sec-text-subtle);
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            padding: 2px 4px;
            margin: -2px -4px 0 0;
            border-radius: 4px;
            transition: color 0.15s;
        }

        .sec-toast-close:hover {
            color: var(--sec-text);
        }

        /* Notification / Toast */
        .sec-alert {
            padding: 12px 16px;
            border-radius: var(--sec-radius-sm);
            margin-bottom: 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sec-alert-success {
            background: var(--sec-success-bg);
            border: 1px solid var(--sec-success-border);
            color: var(--sec-success);
        }

        .sec-alert-danger {
            background: var(--sec-danger-bg);
            border: 1px solid var(--sec-danger-border);
            color: var(--sec-danger);
        }

        /* Trend Chart Bars */
        .sec-chart-box {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 150px;
            padding-top: 10px;
            width: 100%;
        }

        .sec-chart-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
            gap: 6px;
            position: relative;
        }

        .sec-chart-bar {
            width: 100%;
            border-radius: 4px 4px 0 0;
            background: rgba(239, 68, 68, 0.7);
            transition: all 0.2s;
            min-height: 2px;
        }

        .sec-chart-bar:hover {
            background: var(--sec-danger);
            transform: scaleY(1.02);
        }

        .sec-chart-label {
            font-size: 10px;
            color: var(--sec-text-subtle);
            white-space: nowrap;
        }

        /* Modal Styles */
        .sec-modal-backdrop[hidden] {
            display: none !important;
        }

        .sec-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 16px;
        }

        .sec-modal-box {
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius);
            box-shadow: var(--sec-shadow-md);
            max-width: 520px;
            width: 100%;
            overflow: hidden;
            animation: sec-pop 0.15s ease-out;
        }

        @keyframes sec-pop {
            from {
                opacity: 0;
                transform: scale(0.96);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .sec-modal-header {
            padding: 18px 20px;
            border-bottom: 1px solid var(--sec-border);
        }

        .sec-modal-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .sec-modal-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--sec-border);
            background: var(--sec-card-hover);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }


        /* Scope Selector (Settings) */
        .sec-scope-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .sec-scope-box {
            cursor: pointer;
            border: 2px solid var(--sec-border);
            border-radius: var(--sec-radius);
            padding: 18px;
            background: var(--sec-card);
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all 0.2s ease-in-out;
        }

        .sec-scope-box:hover {
            border-color: var(--sec-border-light);
            background: var(--sec-card-hover);
        }

        .sec-scope-box.active-primary {
            border-color: var(--sec-primary);
            background: rgba(59, 130, 246, 0.08);
        }

        .sec-scope-box.active-warning {
            border-color: var(--sec-warning);
            background: rgba(245, 158, 11, 0.08);
        }

        .sec-scope-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sec-scope-title-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sec-scope-title {
            font-weight: 700;
            font-size: 15px;
            color: var(--sec-text);
        }

        .sec-scope-desc {
            font-size: 13px;
            color: var(--sec-text-muted);
            line-height: 1.5;
            margin: 0;
        }

        .sec-param-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--sec-border);
        }

        /* Settings — scope pills, level cards, toggle switch */
        .sec-scope-box.active-info {
            border-color: var(--sec-primary);
            background: rgba(59, 130, 246, 0.08);
        }

        .sec-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            height: 22px;
            padding: 0 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .sec-pill-info {
            background: var(--sec-success-bg);
            color: #047857;
            border: 1px solid var(--sec-success-border);
        }

        .sec-switch-wrap {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .sec-switch {
            position: relative;
            width: 46px;
            height: 26px;
            flex: 0 0 46px;
        }

        .sec-switch input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            cursor: pointer;
            z-index: 2;
        }

        .sec-switch-track {
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: var(--sec-border);
            transition: background 0.2s ease-in-out;
        }

        .sec-switch-track::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s ease-in-out;
        }

        .sec-switch input:checked~.sec-switch-track {
            background: var(--sec-success);
        }

        .sec-switch input:checked~.sec-switch-track::after {
            transform: translateX(20px);
        }

        .sec-switch-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--sec-success);
        }

        .sec-switch-label.off {
            color: var(--sec-text-muted);
        }

        .sec-level-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 14px;
        }

        .sec-level-card {
            border: 1px solid var(--sec-border);
            border-left-width: 4px;
            border-left-style: solid;
            border-radius: var(--sec-radius);
            padding: 14px 16px;
            background: var(--sec-card);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sec-level-card.critical {
            border-left-color: var(--sec-danger);
        }

        .sec-level-card.high {
            border-left-color: var(--sec-warning);
        }

        .sec-level-card.medium {
            border-left-color: var(--sec-warning);
        }

        .sec-level-card.low {
            border-left-color: var(--sec-primary);
        }

        .sec-level-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .sec-level-title {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-weight: 700;
            font-size: 14px;
            color: var(--sec-text);
        }

        .sec-level-badge {
            font-size: 10.5px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: var(--sec-radius-sm);
            white-space: nowrap;
        }

        .sec-level-badge.critical {
            background: var(--sec-danger-bg);
            color: #b91c1c;
        }

        .sec-level-badge.high {
            background: var(--sec-warning-bg);
            color: #92400e;
        }

        .sec-level-badge.medium {
            background: var(--sec-warning-bg);
            color: #92400e;
        }

        .sec-level-badge.low {
            background: var(--sec-primary-bg, #eff6ff);
            color: #1d4ed8;
        }

        .sec-level-input {
            width: 100%;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 700;
            font-family: var(--sec-font-mono);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius-sm);
            background: var(--sec-card-hover);
            color: var(--sec-text);
        }

        .sec-level-input:focus {
            outline: none;
            border-color: var(--sec-primary);
        }

        .sec-level-desc {
            font-size: 11.5px;
            line-height: 1.5;
            color: var(--sec-text-muted);
            margin: 0;
        }

        .sec-level-check {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius);
            background: var(--sec-card);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
        }

        .sec-level-check:hover {
            border-color: var(--sec-border-light);
            background: var(--sec-card-hover);
        }

        .sec-level-check.checked {
            border-color: var(--sec-primary);
            background: rgba(59, 130, 246, 0.06);
        }

        .sec-level-check input {
            margin-top: 2px;
            accent-color: var(--sec-primary);
        }

        .sec-select-input {
            width: 100%;
            padding: 9px 12px;
            font-size: 13.5px;
            font-weight: 600;
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius-sm);
            background: var(--sec-card-hover);
            color: var(--sec-text);
        }

        .sec-select-input:focus {
            outline: none;
            border-color: var(--sec-primary);
        }

        .sec-sim-box {
            border: 1px solid var(--sec-warning-border);
            background: var(--sec-warning-bg);
            border-radius: var(--sec-radius);
            padding: 14px 16px;
            color: var(--sec-warning);
        }

        .sec-sim-chip {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: var(--sec-radius-sm);
            font-family: var(--sec-font-mono);
            font-size: 11.5px;
            font-weight: 700;
            color: #ffffff;
        }

        .sec-sim-chip.critical {
            background: #dc2626;
        }

        .sec-sim-chip.high {
            background: #d97706;
        }

        .sec-sim-chip.medium {
            background: #ca8a04;
        }

        .sec-sim-chip.low {
            background: #2563eb;
        }

        .sec-sim-chip.combined {
            background: #7c3aed;
        }

        /* Theme Toggle */
        .sec-theme-toggle {
            margin-top: 16px;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 36px;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 500;
            border-radius: var(--sec-radius-sm);
            cursor: pointer;
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            color: var(--sec-text-muted);
            font-family: inherit;
            transition: all 0.15s ease-in-out;
        }

        .sec-theme-toggle:hover {
            color: var(--sec-text);
            background: var(--sec-card-hover);
        }

        .sec-theme-toggle svg {
            width: 16px;
            height: 16px;
        }

        .sec-sidebar-version {
            margin-top: 10px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--sec-card);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius-sm);
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }

        .sec-sidebar-version:hover {
            background: var(--sec-card-hover);
            border-color: var(--sec-primary);
        }

        .sec-sidebar-version-brand {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--sec-text);
            letter-spacing: -0.01em;
        }

        .sec-sidebar-version-icon {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            flex-shrink: 0;
            display: inline-block;
        }

        .sec-sidebar-version-badge {
            font-family: var(--sec-font-mono);
            font-size: 11px;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 9999px;
            background: rgba(37, 99, 235, 0.08);
            color: var(--sec-primary);
            border: 1px solid rgba(37, 99, 235, 0.2);
            line-height: 1.2;
            transition: all 0.15s ease-in-out;
        }

        .sec-sidebar-version:hover .sec-sidebar-version-badge {
            background: var(--sec-primary);
            color: #ffffff;
        }

        /* Detail Pages */
        .sec-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px 20px;
        }

        .sec-detail-item {
            min-width: 0;
        }

        .sec-detail-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--sec-text-subtle);
            margin-bottom: 4px;
        }

        .sec-detail-value {
            font-size: 14px;
            color: var(--sec-text);
            word-break: break-word;
        }

        .sec-code-block {
            font-family: var(--sec-font-mono);
            font-size: 12.5px;
            line-height: 1.6;
            background: var(--sec-card-hover);
            border: 1px solid var(--sec-border);
            border-radius: var(--sec-radius-sm);
            padding: 14px 16px;
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 320px;
            overflow: auto;
            color: var(--sec-text);
        }

        .sec-back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--sec-text-muted);
            text-decoration: none;
            margin-bottom: 16px;
        }

        .sec-back-link:hover {
            color: var(--sec-primary);
        }

        /* Responsive Breakpoints */
        @media (max-width: 900px) {
            .sec-container {
                flex-direction: column;
                gap: 16px;
            }

            .sec-sidebar {
                width: 100%;
                position: static;
                border-bottom: 1px solid var(--sec-border);
                padding-bottom: 12px;
            }

            .sec-nav-list {
                flex-direction: row;
                overflow-x: auto;
                padding-bottom: 4px;
            }

            .sec-nav-item {
                white-space: nowrap;
            }

            .sec-grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }

            .sec-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .sec-grid-4 {
                grid-template-columns: 1fr;
            }

            .sec-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .sec-toolbar-group {
                flex-direction: column;
                align-items: stretch;
            }

            .sec-input,
            .sec-select {
                width: 100%;
            }
        }
    </style>
</head>

<body class="sec-body" style="margin: 0;">
    @php
        $backUrl = app(\Internal\Pasak\Services\PasakService::class)->getDashboardBackUrl();
    @endphp
    <!-- Floating Toast Notifications -->
    <div class="sec-toast-stack" id="pasakToastStack" aria-live="polite" aria-atomic="true"></div>

    <!-- Alert-Dialog Confirm Modal -->
    <div class="sec-modal-backdrop" id="pasakConfirmModal" hidden>
        <div class="sec-modal-box" role="dialog" aria-modal="true" aria-labelledby="pasakConfirmTitle" style="max-width: 460px;">
            <div class="sec-modal-header" style="display: flex; align-items: center; gap: 12px; padding: 16px 20px;">
                <div id="pasakConfirmIcon" style="width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"></div>
                <div style="min-width: 0; flex: 1;">
                    <h3 class="sec-card-title" id="pasakConfirmTitle" style="font-size: 16px; margin: 0;">{{ __('Konfirmasi Tindakan') }}</h3>
                </div>
            </div>
            <div class="sec-modal-body" style="padding: 18px 20px; font-size: 13.5px; color: var(--sec-text); line-height: 1.55;">
                <p id="pasakConfirmMessage" style="margin: 0;"></p>
            </div>
            <div class="sec-modal-footer" style="padding: 14px 20px; display: flex; justify-content: flex-end; gap: 10px; background: var(--sec-card-hover);">
                <button type="button" class="sec-btn sec-btn-secondary" id="pasakConfirmCancel">{{ __('Batal') }}</button>
                <button type="button" class="sec-btn" id="pasakConfirmOk">{{ __('Lanjutkan') }}</button>
            </div>
        </div>
    </div>
    <div class="sec-root">
        <div class="sec-container" style="max-width: 1600px; margin: 0 auto; padding: 24px 20px;">
            <!-- Sidebar Navigation -->
            <aside class="sec-sidebar" aria-label="Security Navigation">
            <div class="sec-sidebar-brand">
                <a href="{{ route('pasak.dashboard.overview') }}" class="sec-sidebar-logo-link" title="{{ __('Kembali ke Ringkasan Dashboard Pasak') }}">
                            <img class="sec-header-logo sec-header-logo-light" src="data:image/png;base64,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" alt="PASAK" width="94" height="28">
                            <img class="sec-header-logo sec-header-logo-dark" src="data:image/png;base64,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" alt="PASAK" width="94" height="28">
                        </a>
            </div>
            <a href="{{ $backUrl }}" class="sec-sidebar-back-link" title="{{ __('Kembali ke Dashboard Aplikasi Utama (:url)', ['url' => $backUrl]) }}">
                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6"/>
                    <path d="M9 12h12"/>
                </svg>
                <span>{{ __('Dashboard Utama') }}</span>
            </a>
                <nav>
                    <ul class="sec-nav-list">
                        <li>
                            <a href="{{ route('pasak.dashboard.overview') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.overview*') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect width="7" height="7" x="3" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="3" rx="1" />
                                    <rect width="7" height="7" x="14" y="14" rx="1" />
                                    <rect width="7" height="7" x="3" y="14" rx="1" />
                                </svg>
                                <span>{{ __('Overview') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pasak.dashboard.logs') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.logs*') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <path d="M14 2v6h6" />
                                    <path d="M9 13h6" />
                                    <path d="M9 17h6" />
                                </svg>
                                <span>{{ __('Security Logs') }}</span>
                            </a>
                        </li>
                        @if (\Route::has('pasak.dashboard.threat-map'))
                        <li>
                            <a href="{{ route('pasak.dashboard.threat-map') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.threat-map*') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M2 12h20" />
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                </svg>
                                <span>{{ __('Threat Map') }}</span>
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route('pasak.dashboard.blocked-ips') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.blocked-ips*') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="m4.9 4.9 14.2 14.2" />
                                </svg>
                                <span>{{ __('Blocked IPs') }}</span>
                            </a>
                        </li>
                        @if (\Route::has('pasak.dashboard.allowed-ips'))
                        <li>
                            <a href="{{ route('pasak.dashboard.allowed-ips') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.allowed-ips') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                                <span>{{ __('Allowed IPs') }}</span>
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route('pasak.dashboard.server') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.server*') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect width="20" height="8" x="2" y="2" rx="2" />
                                    <rect width="20" height="8" x="2" y="14" rx="2" />
                                    <path d="M6 6h.01" />
                                    <path d="M6 18h.01" />
                                </svg>
                                <span>{{ __('Server Audit') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pasak.dashboard.sessions') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.sessions*') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                <span>{{ __('Sessions') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pasak.dashboard.tickets') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.tickets*') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
                                    <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
                                </svg>
                                <span>{{ __('Appeals') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pasak.dashboard.settings') }}"
                                class="sec-nav-item {{ request()->routeIs('pasak.dashboard.settings*') ? 'active' : '' }}">
                                <svg class="sec-nav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="3" />
                                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
                                </svg>
                                <span>{{ __('Settings') }}</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <button type="button" class="sec-theme-toggle" data-sec-theme-toggle aria-label="{{ __('Toggle dark mode') }}">
                    <svg data-sec-theme-icon="moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
                    </svg>
                    <svg data-sec-theme-icon="sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display: none;">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4" />
                    </svg>
                    <span data-sec-theme-label>{{ __('Dark mode') }}</span>
                </button>
                <a href="https://pasak.is-a.dev/docs/pasak-security-monitor"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="sec-sidebar-version"
                    title="{{ __('Versi Pasak aktif: v:version — Klik untuk melihat catatan rilis', ['version' => \Internal\Pasak\Services\PasakService::VERSION]) }}">
                    <span class="sec-sidebar-version-brand">
                        <svg class="sec-sidebar-version-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" aria-hidden="true">
                            <image href="data:image/png;base64,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" xlink:href="data:image/png;base64,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" width="24" height="24" preserveAspectRatio="xMidYMid meet" />
                        </svg>
                        <span>Pasak</span>
                    </span>
                    <span class="sec-sidebar-version-badge">v{{ \Internal\Pasak\Services\PasakService::VERSION }}</span>
                </a>
            </aside>


            <main class="sec-main">
                @php
                $flashToasts = [];
                if (session()->has('toast')) {
                $t = session('toast');
                if (is_array($t)) {
                $flashToasts[] = [
                'type' => $t['type'] ?? 'success',
                'title' => $t['title'] ?? (($t['type'] ?? 'success') === 'error' ? __('Gagal') : __('Berhasil')),
                'message' => (string) ($t['message'] ?? ''),
                ];
                } else {
                $flashToasts[] = ['type' => 'success', 'title' => __('Berhasil'), 'message' => (string) $t];
                }
                }
                if (session()->has('security_message')) {
                $flashToasts[] = ['type' => 'success', 'title' => __('Berhasil'), 'message' => (string) session('security_message')];
                }
                if (session()->has('success')) {
                $flashToasts[] = ['type' => 'success', 'title' => __('Berhasil'), 'message' => (string) session('success')];
                }
                if (session()->has('error')) {
                $flashToasts[] = ['type' => 'error', 'title' => __('Gagal'), 'message' => (string) session('error')];
                }
                if (session()->has('status') && ! session()->has('toast')) {
                $flashToasts[] = ['type' => 'info', 'title' => __('Status'), 'message' => (string) session('status')];
                }
                if (session()->has('message') && ! session()->has('toast')) {
                $flashToasts[] = ['type' => 'info', 'title' => __('Informasi'), 'message' => (string) session('message')];
                }
                if (isset($errors) && $errors->any()) {
                foreach ($errors->all() as $err) {
                $flashToasts[] = ['type' => 'error', 'title' => __('Gagal Validasi'), 'message' => (string) $err];
                }
                }
                @endphp
                @if (session()->has('security_message'))
                <div class="sec-alert sec-alert-success">
                    <span>{{ session('security_message') }}</span>
                </div>
                @endif
                @if (session()->has('toast'))
                <div class="sec-alert sec-alert-{{ (session('toast.type', 'success') === 'success') ? 'success' : 'danger' }}">
                    <span>{{ session('toast.message') }}</span>
                </div>
                @endif

                <header class="sec-header">
                    <div class="sec-header-brand">
                        @yield('header_left')
                    </div>
                    <div class="sec-header-actions">
                        @yield('header_actions')

                        <button type="button" class="sec-support-btn" onclick="window.pasakOpenPageSupport ? window.pasakOpenPageSupport() : null;" title="{{ __('Panduan & Informasi Halaman') }}" data-sec-header-support>
                            <svg class="sec-support-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                            <span>{{ __('Support') }}</span>
                        </button>
                                                <div class="sec-client-ip-badge" title="{{ __('Waktu Server (:tz)', ['tz' => $pasakTimezone]) }}" style="cursor: default;">
                            <span class="sec-ip-dot" style="background-color: var(--sec-primary);" aria-hidden="true"></span>
                            <span class="sec-ip-label">{{ __('Server:') }}</span>
                            <span class="sec-ip-address">{{ \Internal\Pasak\Support\DashboardView::formatDate(now(), 'H:i') }} <span style="font-size: 10.5px; opacity: 0.75;">{{ \Internal\Pasak\Support\DashboardView::formatDate(now(), 'T') }}</span></span>
                        </div>
                        <div class="sec-client-ip-badge" onclick="window.pasakCopyIp ? window.pasakCopyIp('{{ request()->ip() }}') : null;" title="{{ __('Alamat IP Anda saat ini: :ip (Klik untuk menyalin)', ['ip' => request()->ip()]) }}" role="button" tabindex="0">
                            <span class="sec-ip-dot" aria-hidden="true"></span>
                            <span class="sec-ip-label">{{ __('IP Anda:') }}</span>
                            <span class="sec-ip-address">{{ request()->ip() }}</span>
                        </div>
                        <button type="button" class="sec-refresh-btn" onclick="window.pasakRefreshPage ? window.pasakRefreshPage(this) : window.location.reload();" title="{{ __('Muat ulang data tampilan & bersihkan cache (optimize:clear)') }}" data-sec-header-refresh>
                            <svg class="sec-refresh-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                <path d="M3 3v5h5"/>
                                <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                                <path d="M16 21h5v-5"/>
                            </svg>
                            <span>{{ __('Refresh') }}</span>
                        </button>
                        <div class="sec-lang-switcher" role="group" aria-label="{{ __('Pilih Bahasa / Select Language') }}" title="{{ __('Ganti Bahasa / Switch Language') }}">
                            <button type="button" class="sec-lang-btn {{ $currentLocale === 'en' ? 'is-active' : '' }}" onclick="window.pasakSetLocale('en');" title="English" aria-pressed="{{ $currentLocale === 'en' ? 'true' : 'false' }}">EN</button>
                            <span class="sec-lang-sep" aria-hidden="true">/</span>
                            <button type="button" class="sec-lang-btn {{ $currentLocale === 'id' ? 'is-active' : '' }}" onclick="window.pasakSetLocale('id');" title="Bahasa Indonesia" aria-pressed="{{ $currentLocale === 'id' ? 'true' : 'false' }}">ID</button>
                        </div>
                        @if (\Illuminate\Support\Facades\Route::has('pasak.dashboard.password.update'))
                        <button type="button" class="sec-btn sec-btn-ghost sec-btn-sm" onclick="window.pasakOpenPasswordModal ? window.pasakOpenPasswordModal() : null;" title="{{ __('Ubah Password Dasbor') }}" style="height: 32px; padding: 0 10px; font-size: 12px; gap: 5px; color: {{ session('pasak_dashboard_needs_password_change') ? 'var(--sec-warning)' : 'var(--sec-text-muted)' }}; border: 1px solid {{ session('pasak_dashboard_needs_password_change') ? 'var(--sec-warning)' : 'var(--sec-border)' }}; border-radius: var(--sec-radius-sm); position: relative;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span>{{ __('Password') }}</span>
                            @if (session('pasak_dashboard_needs_password_change'))
                            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: var(--sec-warning);" title="{{ __('Password masih default (Admin)') }}"></span>
                            @endif
                        </button>
                        @endif
                        @if (\Illuminate\Support\Facades\Route::has('pasak.dashboard.logout'))
                        <form method="POST" action="{{ route('pasak.dashboard.logout') }}" style="display: inline-flex; margin: 0;" data-sec-confirm="{{ __('Keluar dari sesi dashboard Pasak?') }}" data-confirm-title="{{ __('Konfirmasi Logout') }}" data-confirm-type="warning" data-confirm-btn="{{ __('Ya, Logout') }}">
                            @csrf
                            <button type="submit" class="sec-btn sec-btn-ghost sec-btn-sm" title="{{ __('Keluar dari sesi Pasak (:user)', ['user' => session('pasak_dashboard_user', 'Admin')]) }}" style="height: 32px; padding: 0 10px; font-size: 12px; gap: 5px; color: var(--sec-text-muted); border: 1px solid var(--sec-border); border-radius: var(--sec-radius-sm);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                <span>{{ __('Logout') }}</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </header>

                @if (! empty($heading) || ! empty($subheading))
                <div class="sec-page-header">
                    @if (! empty($heading))
                    <h1 class="sec-title">{{ $heading }}</h1>
                    @endif
                    @if (! empty($subheading))
                    <p class="sec-subtitle">{{ $subheading }}</p>
                    @endif
                </div>
                @endif

                @if (session('pasak_dashboard_needs_password_change'))
                <div class="sec-alert sec-alert-warning" style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 12px 18px; border-radius: var(--sec-radius); background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.3); color: var(--sec-warning);">
                    <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; line-height: 1.45;">
                        <span style="font-size: 16px; line-height: 1;">{{ session('pasak_dashboard_is_emergency_login') ? '🚨' : '⚠️' }}</span>
                        <span>
                            @if (session('pasak_dashboard_is_emergency_login'))
                                <strong>{{ __('Mode Pemulihan Darurat Aktif:') }}</strong> {{ __('Anda berhasil masuk menggunakan PASAK_DASHBOARD_SECRET. Segera buat password baru untuk mengamankan dasbor.') }}
                            @else
                                <strong>{{ __('Peringatan Keamanan:') }}</strong> {{ __('Anda masih menggunakan password bawaan (Admin). Segera perbarui password dasbor untuk melindungi server.') }}
                            @endif
                        </span>
                    </div>
                    <button type="button" class="sec-btn sec-btn-sm" onclick="window.pasakOpenPasswordModal ? window.pasakOpenPasswordModal() : null;" style="background: var(--sec-warning); color: #000; font-weight: 700; white-space: nowrap; height: 30px; font-size: 12px; padding: 0 12px; border-radius: 6px; border: none; cursor: pointer;">
                        {{ __('Ubah Password') }}
                    </button>
                </div>
                @endif

                @yield('content')
            </main>
        </div>

        <!-- Pengumuman kunjungan pertama (hanya local + debug) -->
        @if (app()->environment('local') && config('app.debug'))
        <div class="sec-modal-backdrop" id="pasakAnnounce" hidden>
            <div class="sec-modal-box" role="dialog" aria-modal="true" aria-labelledby="pasakAnnounceTitle">
                <div class="sec-modal-header">
                    <h3 class="sec-card-title" id="pasakAnnounceTitle">👋 {{ __('Selamat datang di dashboard Pasak') }}</h3>
                </div>
                <div class="sec-modal-body" style="font-size: 14px; color: var(--sec-text-muted); line-height: 1.6;">
                    <div>🛡️ {{ __('Bawaan instalasi adalah monitoring saja: ancaman dideteksi dan dicatat tanpa blokir otomatis.') }}</div>
                    <div>⚙️ {{ __('Siap karantina otomatis? Atur di halaman Settings atau via API.') }}</div>
                    <div>📖 {{ __('Panduan lengkap:') }} <a href="https://github.com/robyajo/pasak" target="_blank" rel="noopener" style="color: var(--sec-primary);">github.com/robyajo/pasak</a></div>
                </div>
                <div class="sec-modal-footer">
                    <button type="button" class="sec-btn sec-btn-primary" data-pasak-announce-close>{{ __('Mengerti') }}</button>
                </div>
            </div>
        </div>
        <script>
            (function() {
                try {
                    var key = 'pasak-announce-{{ str_replace('.', '-', \Internal\Pasak\Services\PasakService::VERSION) }}';
                    if (window.localStorage && localStorage.getItem(key)) return;
                    var el = document.getElementById('pasakAnnounce');
                    if (!el) return;
                    el.hidden = false;
                    var btn = el.querySelector('[data-pasak-announce-close]');
                    if (btn) btn.addEventListener('click', function() {
                        el.hidden = true;
                        try {
                            localStorage.setItem(key, '1');
                        } catch (e) {}
                    });
                } catch (e) {}
            })();
        </script>
        @endif
    </div>
    <script>
        // Toggle tema gelap/terang manual (tersimpan di localStorage, default ikuti sistem).
        (function() {
            var key = 'pasak-theme';
            var labels = {
                dark: {!! json_encode(__('Dark')) !!},
                light: {!! json_encode(__('Light')) !!},
                auto: {!! json_encode(__('Auto')) !!}
            };

            function stored() {
                try {
                    return localStorage.getItem(key) || 'auto';
                } catch (e) {
                    return 'auto';
                }
            }

            function isDark(mode) {
                if (mode === 'dark') {
                    return true;
                }
                if (mode === 'light') {
                    return false;
                }

                return window.matchMedia('(prefers-color-scheme: dark)').matches;
            }

            function paint() {
                var mode = stored();
                var dark = isDark(mode);
                var root = document.documentElement;

                if (mode === 'auto') {
                    root.removeAttribute('data-theme');
                } else {
                    root.setAttribute('data-theme', mode);
                }

                if (dark) {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }

                document.querySelectorAll('[data-sec-theme-toggle]').forEach(function(btn) {
                    var moon = btn.querySelector('[data-sec-theme-icon="moon"]');
                    var sun = btn.querySelector('[data-sec-theme-icon="sun"]');
                    var label = btn.querySelector('[data-sec-theme-label]');

                    if (moon) {
                        moon.style.display = dark ? 'none' : '';
                    }
                    if (sun) {
                        sun.style.display = dark ? '' : 'none';
                    }
                    if (label) {
                        label.textContent = labels[mode] || labels.auto;
                    }
                });
            }

            document.querySelectorAll('[data-sec-theme-toggle]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var order = ['auto', 'light', 'dark'];
                    var next = order[(order.indexOf(stored()) + 1) % order.length];

                    try {
                        localStorage.setItem(key, next);
                    } catch (e) {
                        /* abaikan */ }

                    paint();
                });
            });

            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', paint);
            }

            paint();
        })();
    </script>
    <script>
        window.PasakToasts = @json($flashToasts ?? []);

        (function() {
            // Global Page Refresh Handler (triggers optimize:clear & reloads fresh state)
            window.pasakRefreshPage = function(btn) {
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('is-refreshing');
                }

                var finishRefresh = function() {
                    if (typeof window.pasakCustomRefresh === 'function') {
                        window.pasakCustomRefresh(function() {
                            if (btn) {
                                btn.disabled = false;
                                btn.classList.remove('is-refreshing');
                            }
                        });
                        return;
                    }
                    window.location.reload();
                };

                var clearUrl = @json(\Illuminate\Support\Facades\Route::has('pasak.dashboard.optimize-clear') ? route('pasak.dashboard.optimize-clear') : null);
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                if (!clearUrl) {
                    finishRefresh();
                    return;
                }

                fetch(clearUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    var msg = (data && data.message) || {!! json_encode(__('Cache aplikasi, tampilan, routes, dan konfigurasi berhasil dibersihkan.')) !!};

                    if (typeof window.pasakCustomRefresh === 'function') {
                        if (window.pasakToast) {
                            window.pasakToast(msg, 'success');
                        }
                    }
                    finishRefresh();
                })
                .catch(function() {
                    finishRefresh();
                });
            };

            // Global Locale Switcher
            window.pasakSetLocale = function(lang) {
                if (lang !== 'en' && lang !== 'id') return;
                try {
                    localStorage.setItem('pasak_locale', lang);
                    document.cookie = 'pasak_locale=' + lang + ';path=/;max-age=31536000;SameSite=Lax';
                } catch (e) {}
                var url = new URL(window.location.href);
                url.searchParams.set('lang', lang);
                window.location.href = url.toString();
            };

            // Copy Current Client IP Helper
            window.pasakCopyIp = function(ip) {
                if (!ip) return;
                var notify = function() {
                    if (window.pasakToast) {
                        window.pasakToast({!! json_encode(__('Alamat IP berhasil disalin: ')) !!} + ip, 'info', {!! json_encode(__('IP Disalin')) !!});
                    }
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(ip).then(notify).catch(function() {
                        fallbackCopy(ip, notify);
                    });
                } else {
                    fallbackCopy(ip, notify);
                }

                function fallbackCopy(text, cb) {
                    var ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try {
                        document.execCommand('copy');
                        cb();
                    } catch (e) {}
                    document.body.removeChild(ta);
                }
            };

            // 1. Toast Notification System
            window.pasakToast = function(message, type, title) {
                if (!message) return;
                type = type || 'success';
                var stack = document.getElementById('pasakToastStack');
                if (!stack) return;

                // Cegah penayangan toast ganda dengan pesan identik yang masih aktif
                var existingMsgs = stack.querySelectorAll('.sec-toast-message');
                for (var em = 0; em < existingMsgs.length; em++) {
                    if (existingMsgs[em].textContent.trim() === String(message).trim()) {
                        return;
                    }
                }

                var toast = document.createElement('div');
                toast.className = 'sec-toast sec-toast-' + type;

                var icons = {
                    success: '✓',
                    error: '✕',
                    danger: '✕',
                    warning: '⚠',
                    info: 'ℹ'
                };

                var defaultTitles = {
                    success: {!! json_encode(__('Berhasil')) !!},
                    error: {!! json_encode(__('Gagal')) !!},
                    danger: {!! json_encode(__('Gagal')) !!},
                    warning: {!! json_encode(__('Peringatan')) !!},
                    info: {!! json_encode(__('Informasi')) !!}
                };

                var displayTitle = title || defaultTitles[type] || '';

                toast.innerHTML =
                    '<div class="sec-toast-icon">' + (icons[type] || 'ℹ') + '</div>' +
                    '<div class="sec-toast-content">' +
                    (displayTitle ? '<div class="sec-toast-title">' + displayTitle + '</div>' : '') +
                    '<div class="sec-toast-message">' + message + '</div>' +
                    '</div>' +
                    '<button type="button" class="sec-toast-close" aria-label="' + {!! json_encode(__('Tutup')) !!} + '">&times;</button>';

                stack.appendChild(toast);

                var removeToast = function() {
                    toast.classList.add('sec-toast-hiding');
                    setTimeout(function() {
                        if (toast.parentNode) toast.parentNode.removeChild(toast);
                    }, 200);
                };

                toast.querySelector('.sec-toast-close').addEventListener('click', removeToast);

                var timer = setTimeout(removeToast, 5000);
                toast.addEventListener('mouseenter', function() {
                    clearTimeout(timer);
                });
                toast.addEventListener('mouseleave', function() {
                    timer = setTimeout(removeToast, 3000);
                });
            };

            if (window.PasakToasts && Array.isArray(window.PasakToasts)) {
                window.PasakToasts.forEach(function(t, i) {
                    setTimeout(function() {
                        window.pasakToast(t.message, t.type, t.title);
                    }, i * 150);
                });
            }



            // 2. Alert-Dialog Confirm System
            var modal = document.getElementById('pasakConfirmModal');
            if (!modal) return;

            var titleEl = document.getElementById('pasakConfirmTitle');
            var msgEl = document.getElementById('pasakConfirmMessage');
            var iconEl = document.getElementById('pasakConfirmIcon');
            var okBtn = document.getElementById('pasakConfirmOk');
            var cancelBtn = document.getElementById('pasakConfirmCancel');

            function showConfirm(options, onConfirm) {
                titleEl.textContent = options.title || {!! json_encode(__('Konfirmasi Tindakan')) !!};
                msgEl.textContent = options.message || {!! json_encode(__('Apakah Anda yakin ingin melanjutkan tindakan ini?')) !!};
                okBtn.textContent = options.btnText || {!! json_encode(__('Ya, Lanjutkan')) !!};

                var type = options.type || 'primary';
                okBtn.className = 'sec-btn ' + (type === 'danger' ? 'sec-btn-danger' : (type === 'warning' ? 'sec-btn-danger' : 'sec-btn-primary'));

                if (type === 'danger') {
                    iconEl.textContent = '🚨';
                    iconEl.style.background = 'var(--sec-danger-bg)';
                    iconEl.style.color = 'var(--sec-danger)';
                    iconEl.style.border = '1px solid var(--sec-danger-border)';
                } else if (type === 'warning') {
                    iconEl.textContent = '⚠';
                    iconEl.style.background = 'var(--sec-warning-bg)';
                    iconEl.style.color = 'var(--sec-warning)';
                    iconEl.style.border = '1px solid var(--sec-warning-border)';
                } else {
                    iconEl.textContent = '🛡️';
                    iconEl.style.background = 'rgba(37, 99, 235, 0.1)';
                    iconEl.style.color = 'var(--sec-primary)';
                    iconEl.style.border = '1px solid rgba(37, 99, 235, 0.25)';
                }

                modal.hidden = false;
                okBtn.focus();

                var cleanup = function() {
                    modal.hidden = true;
                    okBtn.onclick = null;
                    cancelBtn.onclick = null;
                    document.removeEventListener('keydown', handleKey);
                };

                var handleKey = function(e) {
                    if (e.key === 'Escape') cleanup();
                };

                cancelBtn.onclick = cleanup;
                modal.onclick = function(e) {
                    if (e.target === modal) cleanup();
                };

                okBtn.onclick = function() {
                    cleanup();
                    if (onConfirm) onConfirm();
                };

                document.addEventListener('keydown', handleKey);
            }

            // Intercept form submissions
            document.addEventListener('submit', function(e) {
                var form = e.target;
                if (!form || (form.method && form.method.toLowerCase() === 'get')) return;
                if (form.dataset.pasakConfirmed === 'true') {
                    form.removeAttribute('data-pasak-confirmed');
                    return;
                }

                var submitBtn = e.submitter;

                var confirmMsg = form.getAttribute('data-sec-confirm');
                if (submitBtn && submitBtn.getAttribute('data-sec-confirm')) {
                    confirmMsg = submitBtn.getAttribute('data-sec-confirm');
                }

                var title = form.getAttribute('data-confirm-title');
                if (submitBtn && submitBtn.getAttribute('data-confirm-title')) {
                    title = submitBtn.getAttribute('data-confirm-title');
                }

                var btnText = form.getAttribute('data-confirm-btn');
                if (submitBtn && submitBtn.getAttribute('data-confirm-btn')) {
                    btnText = submitBtn.getAttribute('data-confirm-btn');
                }

                var type = form.getAttribute('data-confirm-type');
                if (submitBtn && submitBtn.getAttribute('data-confirm-type')) {
                    type = submitBtn.getAttribute('data-confirm-type');
                }

                if (!confirmMsg) {
                    var isDelete = form.querySelector('input[name="_method"][value="DELETE"]') ||
                        (submitBtn && (submitBtn.classList.contains('sec-btn-danger') || (submitBtn.textContent && submitBtn.textContent.indexOf('🗑️') !== -1)));
                    var isPatch = form.querySelector('input[name="_method"][value="PATCH"]');
                    var btnLabel = submitBtn ? submitBtn.textContent.trim().toLowerCase() : '';

                    if (isDelete) {
                        confirmMsg = {!! json_encode(__('Apakah Anda yakin ingin menghapus data ini secara permanen?')) !!};
                        title = title || {!! json_encode(__('Konfirmasi Hapus')) !!};
                        type = type || 'danger';
                        btnText = btnText || {!! json_encode(__('Ya, Hapus')) !!};
                    } else if (isPatch || btnLabel.indexOf('disable') !== -1 || btnLabel.indexOf('enable') !== -1 || btnLabel.indexOf('nonaktifkan') !== -1 || btnLabel.indexOf('aktifkan') !== -1) {
                        confirmMsg = {!! json_encode(__('Apakah Anda yakin ingin mengubah status data ini?')) !!};
                        title = title || {!! json_encode(__('Konfirmasi Pengubahan Status')) !!};
                        type = type || 'warning';
                        btnText = btnText || {!! json_encode(__('Ya, Ubah Status')) !!};
                    } else if (form.id === 'pasak-settings-form') {
                        confirmMsg = {!! json_encode(__('Simpan seluruh perubahan konfigurasi WAF ke database?')) !!};
                        title = title || {!! json_encode(__('Konfirmasi Simpan Pengaturan')) !!};
                        type = type || 'primary';
                        btnText = btnText || {!! json_encode(__('Ya, Simpan')) !!};
                    } else {
                        return;
                    }
                }

                e.preventDefault();

                showConfirm({
                    title: title || {!! json_encode(__('Konfirmasi Tindakan')) !!},
                    message: confirmMsg,
                    type: type || 'primary',
                    btnText: btnText || {!! json_encode(__('Ya, Lanjutkan')) !!}
                }, function() {
                    form.dataset.pasakConfirmed = 'true';
                    if (submitBtn && submitBtn.name) {
                        var hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = submitBtn.name;
                        hidden.value = submitBtn.value;
                        form.appendChild(hidden);
                    }
                    if (form.requestSubmit) {
                        form.requestSubmit(submitBtn);
                    } else {
                        form.submit();
                    }
                });
            });
        })();
    </script>
    @include('pages.pasak.update-password-modal')
    @include('pages.pasak.page-support-modal')
</body>

</html>

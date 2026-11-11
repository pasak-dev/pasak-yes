@php
    $customPageKey = trim($__env->yieldContent('page_support_key'));
    $currentRouteName = request()->route() ? request()->route()->getName() : '';
    $path = trim(request()->path(), '/');

    // Determine current page key
    $pageKey = !empty($customPageKey) ? $customPageKey : 'overview';

    if (empty($customPageKey)) {
        if (str_contains($currentRouteName, 'logs.show') || str_contains($path, 'logs/')) {
            $pageKey = 'log-detail';
        } elseif (str_contains($currentRouteName, 'logs') || str_ends_with($path, 'logs') || str_contains($path, '/logs')) {
            $pageKey = 'logs';
        } elseif (str_contains($currentRouteName, 'threat-map') || str_contains($path, 'threat-map')) {
            $pageKey = 'threat-map';
        } elseif (str_contains($currentRouteName, 'blocked-ips.show') || (str_contains($path, 'blocked-ips/') && !str_contains($path, 'toggle'))) {
            $pageKey = 'blocked-ip-detail';
        } elseif (str_contains($currentRouteName, 'blocked-ips') || str_ends_with($path, 'blocked-ips') || str_contains($path, '/blocked-ips')) {
            $pageKey = 'blocked-ips';
        } elseif (str_contains($currentRouteName, 'allowed-ips') || str_ends_with($path, 'allowed-ips') || str_contains($path, '/allowed-ips')) {
            $pageKey = 'allowed-ips';
        } elseif (str_contains($currentRouteName, 'server') || str_ends_with($path, 'server') || str_contains($path, '/server')) {
            $pageKey = 'server';
        } elseif (str_contains($currentRouteName, 'sessions') || str_ends_with($path, 'sessions') || str_contains($path, '/sessions')) {
            $pageKey = 'sessions';
        } elseif (str_contains($currentRouteName, 'tickets.show') || str_contains($path, 'tickets/')) {
            $pageKey = 'ticket-detail';
        } elseif (str_contains($currentRouteName, 'tickets') || str_ends_with($path, 'tickets') || str_contains($path, '/tickets')) {
            $pageKey = 'tickets';
        } elseif (str_contains($currentRouteName, 'settings') || str_ends_with($path, 'settings') || str_contains($path, '/settings')) {
            $pageKey = 'settings';
        }
    }

    $pageGuides = [
        'overview' => [
            'icon' => '📊',
            'title' => [
                'en' => 'Security Overview Dashboard',
                'id' => 'Ringkasan Keamanan (Security Overview)',
            ],
            'badge' => [
                'en' => 'Executive Telemetry & Threat Trends',
                'id' => 'Telemetri Eksekutif & Tren Ancaman',
            ],
            'purpose' => [
                'en' => 'The executive command center for your application and Linux VPS defense fleet. Delivers real-time visibility into incoming cyber attacks, today\'s critical threat incidents, active WAF quarantines, attack volume trends, and geographic origins of malicious actors.',
                'id' => 'Pusat komando eksekutif pertahanan aplikasi dan server Linux VPS Anda. Menyajikan visibilitas seketika terhadap serangan siber yang masuk, insiden ancaman kritis hari ini, jumlah IP/perangkat yang aktif dikarantina WAF, tren volume serangan, dan asal negara penyerang.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Real-Time KPI Metric Cards', 'desc' => 'Instant count of total attack logs recorded today, critical threat counts, active WAF quarantines, and unique attacker IPs.'],
                    ['title' => 'Attack Trend Timeline', 'desc' => 'Interactive chart tracking attack volume over 7-day, 30-day, and 12-month periods to identify peak attack hours and sudden traffic spikes.'],
                    ['title' => 'Threat Distribution by Severity', 'desc' => 'Visual breakdown of threats categorized into Critical (red), High (orange), Medium (yellow), and Low (blue).'],
                    ['title' => 'Top Attack Origins & Countries', 'desc' => 'Identifies foreign Autonomous System Numbers (ASNs) and countries transmitting the highest volume of malicious requests.'],
                    ['title' => 'Live Incident Feed', 'desc' => 'Stream of the latest 10 security violations with instant client IP, HTTP request path, rule matched, and quick quarantine buttons.'],
                    ['title' => 'Server Host Status Indicator', 'desc' => 'Quick indicators showing active firewall tables, cache status, and host application health.'],
                ],
                'id' => [
                    ['title' => 'Kartu Metrik KPI Real-Time', 'desc' => 'Pantau total log serangan hari ini, jumlah ancaman level kritis, karantina WAF aktif, dan jumlah IP penyerang unik.'],
                    ['title' => 'Grafik Tren Waktu Serangan', 'desc' => 'Grafik interaktif volume serangan dengan rentang 7 hari, 30 hari, dan 12 bulan untuk melihat jam sibuk dan lonjakan serangan.'],
                    ['title' => 'Distribusi Tingkat Keparahan', 'desc' => 'Pengelompokan ancaman berdasarkan kategori Kritis (merah), Tinggi (oranye), Sedang (kuning), dan Rendah (biru).'],
                    ['title' => 'Daftar Negara & ASN Sumber Serangan', 'desc' => 'Mengetahui negara dan operator jaringan (ASN) yang paling banyak mengirimkan request jahat ke server.'],
                    ['title' => 'Stream Insiden Keamanan Terkini', 'desc' => '10 serangan paling mutakhir lengkap dengan IP penyerang, target path URL, rule yang terpicu, dan tombol karantina instan.'],
                    ['title' => 'Indikator Kesehatan Host Server', 'desc' => 'Indikator ringkas kesiapan tabel firewall, status cache framework, dan integritas aplikasi.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Timeframe Filter Selection', 'desc' => 'Switch trend chart between Week (7d), Month (30d), and Year (12m) to spot recurring attack campaigns.'],
                    ['title' => 'Quick Quarantine Action', 'desc' => 'Click "Quarantine" directly on any live incident row to immediately enforce a WAF block on the attacker.'],
                    ['title' => 'Deep Forensic Investigation', 'desc' => 'Click any incident row to jump directly into raw HTTP payload analysis and evidence inspector.'],
                    ['title' => 'Global Cache Clear & Refresh', 'desc' => 'Use the top header Refresh button to flush framework view/route caches and reload live statistics.'],
                ],
                'id' => [
                    ['title' => 'Pilihan Rentang Waktu Tren', 'desc' => 'Beralih antara grafik 7 Hari, 30 Hari, dan 12 Bulan untuk mengidentifikasi pola kampanye serangan berkala.'],
                    ['title' => 'Karantina Cepat', 'desc' => 'Klik tombol "Karantina" pada baris insiden untuk langsung menjatuhkan blokir WAF terhadap penyerang.'],
                    ['title' => 'Buka Detail Forensik', 'desc' => 'Klik baris insiden untuk meneliti bukti muatan dan header HTTP penyerangan secara mendalam.'],
                    ['title' => 'Refresh & Bersihkan Cache', 'desc' => 'Gunakan tombol Refresh di header atas untuk membersihkan cache framework dan memuat data telemetri terbaru.'],
                ],
            ],
            'tips' => [
                'en' => 'Investigate sudden spikes in Critical/High incidents immediately. Check the Threat Map to verify if the attack originates from a coordinated botnet across multiple countries.',
                'id' => 'Segera teliti lonjakan mendadak pada kategori Kritis/Tinggi. Periksa Peta Ancaman (Threat Map) untuk memastikan apakah serangan berasal dari botnet terkoordinasi di banyak negara.',
            ],
        ],
        'logs' => [
            'icon' => '📜',
            'title' => [
                'en' => 'Security Audit Logs',
                'id' => 'Log Audit Keamanan & Forensik',
            ],
            'badge' => [
                'en' => 'Raw Telemetry & Evidence Inspector',
                'id' => 'Inspektor Forensik & Bukti Serangan',
            ],
            'purpose' => [
                'en' => 'Comprehensive forensic audit ledger recording every suspicious HTTP request, path traversal attempt, SQL injection payload, vulnerability scanner probe, or authentication anomaly intercepted by Pasak sensors.',
                'id' => 'Buku besar audit forensik lengkap yang mencatat setiap request HTTP mencurigakan, percobaan path traversal, payload SQL injection, probing scanner otomatis, maupun anomali otentikasi yang diintersepsi sensor Pasak.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Severity Mini-HUD Cards', 'desc' => 'Instant summary counts of attack logs recorded today across Critical, High, Medium, and Low tiers.'],
                    ['title' => 'Multi-Criteria Filter Engine', 'desc' => 'Filter by threat level (Critical, High, Medium, Low), rule name, search keywords, date range, and custom page size.'],
                    ['title' => 'Detailed Audit Table', 'desc' => 'Displays client IP, request method (GET/POST/PUT), target URL path, matched rule signature, threat badge, timestamp, and device fingerprint.'],
                    ['title' => 'Evidence Snippet Box', 'desc' => 'Inline preview of the exact malicious string or pattern detected inside the incoming request payload.'],
                    ['title' => 'Device ID & Local Subnet Detection', 'desc' => 'Extracts client device fingerprint and private LAN IP, enabling WiFi-Safe granular blocking.'],
                ],
                'id' => [
                    ['title' => 'Kartu Mini-HUD Tingkat Keparahan', 'desc' => 'Ringkasan jumlah serangan hari ini pada masing-masing level (Critical, High, Medium, Low).'],
                    ['title' => 'Mesin Pencarian & Filter Bertingkat', 'desc' => 'Saring log berdasarkan tingkat keparahan (Kritis, Tinggi, Sedang, Rendah), nama aturan/rule, kata kunci, tanggal, dan jumlah baris.'],
                    ['title' => 'Tabel Audit Forensik Lengkap', 'desc' => 'Menampilkan IP klien, metode HTTP, path URL target, signature rule, badge keparahan, waktu server, dan sidik jari perangkat.'],
                    ['title' => 'Pratinjau Potongan Bukti (Evidence)', 'desc' => 'Pratinjau potongan string berbahaya atau pola payload yang terdeteksi oleh sensor secara presisi.'],
                    ['title' => 'Deteksi Device ID & IP Lokal Subnet', 'desc' => 'Mendeteksi sidik jari perangkat dan IP lokal penyerang untuk memungkinkan pemblokiran perangkat yang aman pada WiFi bersama.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Inspect Raw Forensic Evidence', 'desc' => 'Click "Detail" on any log row to inspect raw HTTP headers, user-agent, full payload dumps, and related attack chronology.'],
                    ['title' => 'Instant Quarantine Modal', 'desc' => 'Click "Block IP" to quarantine the attacker with custom duration (e.g. 1h, 24h, permanent) and scope (IP vs Device).'],
                    ['title' => 'Date & Keyword Search', 'desc' => 'Search across URLs, IP addresses, rule names, and payloads within specific date windows.'],
                    ['title' => 'Log Retention & Prune', 'desc' => 'Clean up legacy logs manually or configure automated database retention in Settings.'],
                ],
                'id' => [
                    ['title' => 'Periksa Bukti Forensik Mentah', 'desc' => 'Klik tombol "Detail" untuk membuka analisis forensik mendalam mencakup raw header, dump payload, dan riwayat serangan.'],
                    ['title' => 'Modal Karantina Instan', 'desc' => 'Klik "Blokir IP" untuk memblokir penyerang dengan durasi fleksibel (1 jam, 24 jam, permanen) dan pilihan lingkup IP/Perangkat.'],
                    ['title' => 'Pencarian Berdasarkan Tanggal & Kata Kunci', 'desc' => 'Cari berdasarkan URL, IP, nama rule, atau muatan payload dalam rentang tanggal tertentu.'],
                    ['title' => 'Pembersihan & Retensi Log', 'desc' => 'Bersihkan log lama secara manual atau atur masa retensi database otomatis di menu Pengaturan.'],
                ],
            ],
            'tips' => [
                'en' => 'Hover over truncated evidence snippets to reveal the full malicious string. Use the "Device" scope when blocking attackers on public or corporate WiFi networks to avoid disrupting co-workers.',
                'id' => 'Arahkan kursor ke potongan teks bukti untuk melihat muatan payload lengkap. Gunakan lingkup "Perangkat" jika penyerang berada di jaringan WiFi kantor/publik agar pengguna lain tidak terkena blokir.',
            ],
        ],
        'threat-map' => [
            'icon' => '🌐',
            'title' => [
                'en' => 'Live Global Threat Map',
                'id' => 'Peta Ancaman Siber Real-Time',
            ],
            'badge' => [
                'en' => '3D Globe & 2D Tactical Geospatial Monitor',
                'id' => 'Monitor Spasial Globe 3D & Peta Datar 2D',
            ],
            'purpose' => [
                'en' => 'Visualizes live cyber attacks targeting your server on an interactive geospatial canvas. Switch seamlessly between a 3D orthographic globe with missile arc trajectories and a 2D high-density tactical flat map designed for SOC command centers.',
                'id' => 'Memvisualisasikan serangan siber yang menargetkan server Anda ke dalam kanvas geospasial interaktif. Beralih antara Globe 3D ortografis dengan lintasan busur serangan melengkung dan Peta Datar 2D resolusi tinggi yang dirancang untuk wallboard SOC.',
            ],
            'features' => [
                'en' => [
                    ['title' => '3D Globe & 2D Tactical Projections', 'desc' => 'Smooth interactive Canvas rendering with mouse drag rotation, zoom, inertia, and auto-spin controls.'],
                    ['title' => 'Central Protected Target Node', 'desc' => 'Highlights your protected server datacenter location as the central fortress with animated pulse waves.'],
                    ['title' => 'Attacker Geospatial Arcs', 'desc' => 'Curved glowing trajectory arcs connecting attacker coordinates to your server, color-coded by threat severity (Red = Critical, Yellow = Medium).'],
                    ['title' => 'Live Threat HUD Telemetry', 'desc' => 'Floating HUD overlay showing live attack velocity, unique attacking countries, top foreign ASNs, and latest attack feed.'],
                ],
                'id' => [
                    ['title' => 'Proyeksi Globe 3D & Peta Datar 2D', 'desc' => 'Render interaktif Canvas dengan rotasi mouse yang halus, kontrol perbesaran (zoom), dan rotasi otomatis.'],
                    ['title' => 'Node Pusat Pertahanan Server', 'desc' => 'Menandai lokasi datacenter server Anda sebagai benteng pertahanan utama dengan denyut animasi gelombang.'],
                    ['title' => 'Lintasan Busur Geolokasi Penyerang', 'desc' => 'Garis busur bercahaya yang menghubungkan koordinat penyerang ke server, dengan kode warna tingkat keparahan (Merah = Kritis, Kuning = Sedang).'],
                    ['title' => 'Panel HUD Telemetri Langsung', 'desc' => 'Panel transparan yang menampilkan kecepatan serangan, daftar negara penyerang terbanyak, ASN sumber, dan stream log insiden.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Toggle Projection Mode', 'desc' => 'Switch instantly between the 3D Globe and 2D Tactical Flat Map via the projection button.'],
                    ['title' => 'Center & Recalibrate View', 'desc' => 'Click "Center on Server" or "Center on Indonesia" to immediately re-focus the camera.'],
                    ['title' => 'Fullscreen SOC Mode', 'desc' => 'Enter clean fullscreen mode without dashboard navigation bars, ideal for dedicated security wall displays.'],
                    ['title' => 'Toggle Auto-Rotation', 'desc' => 'Pause or resume automatic globe rotation during tactical threat inspections.'],
                ],
                'id' => [
                    ['title' => 'Ganti Mode Proyeksi', 'desc' => 'Beralih seketika antara Globe 3D dan Peta Datar 2D melalui tombol proyeksi di sudut canvas.'],
                    ['title' => 'Pusatkan Pandangan Kamera', 'desc' => 'Klik "Pusatkan ke Server" atau "Pusatkan ke Indonesia" untuk mengembalikan fokus kamera ke target pertahanan.'],
                    ['title' => 'Mode Layar Penuh (SOC Wallboard)', 'desc' => 'Aktifkan tampilan layar penuh tanpa bilah navigasi, sangat cocok untuk layar monitor di Command Center/SOC.'],
                    ['title' => 'Jeda / Lanjutkan Putaran Otomatis', 'desc' => 'Hentikan sementara atau aktifkan kembali putaran globe saat menganalisis ancaman tertentu.'],
                ],
            ],
            'tips' => [
                'en' => 'Ensure server latitude and longitude are correctly configured in Settings so threat trajectories land precisely on your real datacenter coordinates.',
                'id' => 'Pastikan koordinat lintang dan bujur server Anda sudah diatur di menu Pengaturan agar busur serangan mendarat tepat di lokasi fisik datacenter Anda.',
            ],
        ],
        'blocked-ips' => [
            'icon' => '🚫',
            'title' => [
                'en' => 'Quarantined & Blocked IPs',
                'id' => 'Karantina IP & Perangkat Terblokir',
            ],
            'badge' => [
                'en' => 'Active WAF & Linux Kernel Quarantine',
                'id' => 'Karantina Aktif WAF & Kernel Linux',
            ],
            'purpose' => [
                'en' => 'Central enforcement ledger managing all quarantined IP addresses and device fingerprints. Any visitor or automated script matching an active entry is immediately served an HTTP 403 Forbidden response and dropped before consuming CPU, memory, or database resources.',
                'id' => 'Pusat penegakan dan buku besar karantina seluruh alamat IP dan fingerprint perangkat yang terisolasi. Setiap pengunjung atau bot yang cocok dengan entri aktif akan langsung ditolak dengan respon HTTP 403 Forbidden sebelum sempat membebani CPU, memori, atau database aplikasi.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Total Blocks Metric Card', 'desc' => 'Cumulative count of all block records stored in the database, including active, expired, and permanent entries.'],
                    ['title' => 'Active Quarantines Card', 'desc' => 'The count of live blocks currently in effect. Visitors from these addresses are actively denied access with HTTP 403.'],
                    ['title' => 'Permanent Blocks Card', 'desc' => 'Number of entries configured with infinite duration (never expire automatically until manually removed).'],
                    ['title' => 'Total Hits Dropped Card', 'desc' => 'Total accumulated malicious requests intercepted and dropped by Pasak across all blocked entities.'],
                    ['title' => 'Scope (IP vs Device Isolation)', 'desc' => 'Distinguishes between broad IP blocks (blocking all traffic from the public IP) and WiFi-Safe device blocks (blocking only the attacker\'s device fingerprint, sparing legitimate users on shared office WiFi/NAT).'],
                    ['title' => 'Time Auditing (Blocked At & Expires)', 'desc' => 'Displays exact local server timestamp when the block was enforced, relative time elapsed, and precise expiration date or permanent flag.'],
                    ['title' => 'Status Badges (Active / Inactive)', 'desc' => 'Visual badge indicating whether the block is currently Active (enforcing HTTP 403, red) or Inactive (temporarily suspended, gray).'],
                    ['title' => 'Attack Rationale & Rule', 'desc' => 'Summary of the security sensor rule or custom administrative note that prompted the quarantine.'],
                ],
                'id' => [
                    ['title' => 'Kartu Total Pemblokiran', 'desc' => 'Jumlah akumulasi seluruh catatan blokir yang tersimpan di database, mencakup status aktif, kedaluwarsa, maupun permanen.'],
                    ['title' => 'Kartu Karantina Aktif', 'desc' => 'Jumlah pemblokiran yang saat ini sedang berlaku efektif. Setiap request dari IP/perangkat ini langsung ditolak dengan HTTP 403.'],
                    ['title' => 'Kartu Blokir Permanen', 'desc' => 'Jumlah entri blokir yang disetel tanpa batas waktu (seumur hidup / tidak akan pernah kedaluwarsa secara otomatis).'],
                    ['title' => 'Kartu Total Hits Digagalkan', 'desc' => 'Total akumulasi percobaan request jahat yang berhasil ditangkis dan ditolak oleh Pasak setelah pemblokiran diberlakukan.'],
                    ['title' => 'Lingkup Sanksi (IP vs Perangkat)', 'desc' => 'Membedakan sanksi blokir seluruh IP publik router atau isolasi khusus perangkat penyerang (WiFi-Safe), sehingga pengguna sah lain di kantor/WiFi yang sama tidak terkena imbas.'],
                    ['title' => 'Audit Waktu (Diblokir & Kedaluwarsa)', 'desc' => 'Menampilkan waktu persis server saat pemblokiran dijatuhkan, sisa durasi relatif, dan tanggal kedaluwarsa atau label Permanen.'],
                    ['title' => 'Badge Status Aktif / Nonaktif', 'desc' => 'Indikator visual apakah sanksi blokir sedang aktif menolak request (merah) atau sedang ditangguhkan sementara (abu-abu).'],
                    ['title' => 'Alasan & Rule Pemicu', 'desc' => 'Rangkuman alasan administratif atau nama rule sensor keamanan yang memicu penjatuhan sanksi blokir.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Block an IP Manually', 'desc' => 'Enter an IPv4/IPv6 address, set duration in hours (enter 0 for permanent), specify an administrative reason, and click "Block IP" with confirmation dialog.'],
                    ['title' => 'Search & Filter Engine', 'desc' => 'Filter table entries by search query (IP, device ID, reason keyword), status (All, Active, Expired, Permanent), and date ranges (From / To).'],
                    ['title' => 'Adjust Table Density (Show Per Page)', 'desc' => 'Change table pagination density between 10, 25, 50, or 100 rows per page using the toolbar selector.'],
                    ['title' => 'Toggle Active / Inactive Status', 'desc' => 'Temporarily suspend or resume quarantine enforcement for testing or false-positive troubleshooting without deleting historical incident logs.'],
                    ['title' => 'Permanent Record Deletion (Trash 🗑️)', 'desc' => 'Click the trash icon to permanently remove a block record from database and fully restore visitor access.'],
                    ['title' => 'Deep Forensic Intelligence (Detail)', 'desc' => 'Click "Detail" on any entry to open the complete timeline of pre-block attacks, post-block dropped probes, and unblock appeal tickets.'],
                ],
                'id' => [
                    ['title' => 'Tambah Blokir Manual', 'desc' => 'Masukkan alamat IPv4/IPv6, atur durasi dalam jam (isi 0 untuk permanen), tulis alasan administratif, dan klik tombol "Blokir IP" dengan konfirmasi dialog.'],
                    ['title' => 'Pencarian & Filter Fleksibel', 'desc' => 'Saring daftar blokir berdasarkan kata kunci (IP, device ID, alasan), status (Semua, Aktif, Kedaluwarsa, Permanen), dan rentang tanggal (Dari / Sampai).'],
                    ['title' => 'Pengaturan Jumlah Baris (Per Page)', 'desc' => 'Ubah tampilan baris per halaman antara 10, 25, 50, atau 100 baris melalui pemilih jumlah baris toolbar.'],
                    ['title' => 'Ubah Status Aktif / Nonaktif', 'desc' => 'Tangguhkan sementara atau aktifkan kembali pemblokiran tanpa menghapus rekam jejak riwayat insiden, sangat berguna saat pengujian false-positive.'],
                    ['title' => 'Hapus Catatan Permanen (Tong Sampah 🗑️)', 'desc' => 'Klik ikon tong sampah untuk menghapus permanen catatan blokir dari database dan memulihkan akses pengguna sepenuhnya.'],
                    ['title' => 'Buka Intelijen Forensik (Detail)', 'desc' => 'Klik tombol "Detail" pada baris tabel untuk meninjau urutan serangan pemicu blokir, hit lanjutan yang ditolak, dan tiket banding.'],
                ],
            ],
            'tips' => [
                'en' => 'When investigating a suspected false-positive, use "Disable" instead of deleting immediately. If the user is on an office or public WiFi network, quarantine their Device ID instead of the public IP address in Settings.',
                'id' => 'Saat menangani dugaan false-positive, gunakan tombol "Nonaktifkan" terlebih dahulu daripada langsung menghapus. Jika pengguna berada di jaringan WiFi bersama, gunakan lingkup isolasi Perangkat (Device) agar IP publik kantor tidak terblokir.',
            ],
        ],
        'blocked-ip-detail' => [
            'icon' => '🔎',
            'title' => [
                'en' => 'Quarantined IP Intelligence & History',
                'id' => 'Intelijen & Riwayat IP Terblokir',
            ],
            'badge' => [
                'en' => 'Pre-Block vs Post-Block Attack Metrics',
                'id' => 'Metrik Serangan Sebelum & Sesudah Pemblokiran',
            ],
            'purpose' => [
                'en' => 'Forensic investigation dossier for a specific quarantined IP or device. It cross-examines the chronological attack sequence that triggered the block against all post-block probes rejected with HTTP 403, and displays any visitor appeal tickets.',
                'id' => 'Berkas investigasi forensik mendalam untuk alamat IP atau perangkat terblokir. Menghubungkan urutan serangan sebelum pemblokiran dengan seluruh request lanjutan yang digagalkan dengan HTTP 403, serta menampilkan tiket banding yang diajukan pengunjung.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Quarantine Telemetry Card', 'desc' => 'Displays IP address, block scope (IP vs Device), creation origin (manual vs auto-sensor), enforcement timestamps, expiration countdown, and post-block hit volume.'],
                    ['title' => 'Pre-Block Incident Sequence', 'desc' => 'Chronological log of malicious requests (SQLi, path traversal, scanner probes) that crossed the threat threshold and caused the quarantine.'],
                    ['title' => 'Post-Block Dropped Probes (HTTP 403)', 'desc' => 'Real-time log of subsequent requests from this attacker intercepted and rejected with HTTP 403 Forbidden.'],
                    ['title' => 'Unblock Appeal Tickets', 'desc' => 'Any appeal requests submitted by this visitor from the 403 error page, complete with their explanation, name, email, and submission time.'],
                ],
                'id' => [
                    ['title' => 'Kartu Telemetri Karantina', 'desc' => 'Menyajikan alamat IP, lingkup blokir (IP vs Perangkat), asal sanksi (manual vs sensor otomatis), waktu blokir, sisa waktu, dan volume hit yang ditolak.'],
                    ['title' => 'Urutan Insiden Pemicu Blokir', 'desc' => 'Daftar kronologis request berbahaya (SQLi, path traversal, scanner) yang melampaui batas toleransi hingga memicu pemblokiran.'],
                    ['title' => 'Jejak Ditolak Pasca-Blokir (403)', 'desc' => 'Catatan request lanjutan yang terus dikirimkan oleh penyerang setelah diblokir dan langsung digagalkan WAF.'],
                    ['title' => 'Tiket Banding Pengunjung', 'desc' => 'Daftar permohonan pembukaan blokir yang diajukan oleh pengguna dari halaman error 403 beserta nama, email, dan alasan yang ditulis.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Toggle Quarantine Enforcement', 'desc' => 'Temporarily pause or resume the block with a single click.'],
                    ['title' => 'Delete Quarantine Record', 'desc' => 'Permanently delete the block to restore visitor access.'],
                    ['title' => 'Inspect Individual Incidents', 'desc' => 'Click detail links on any incident row to inspect raw HTTP headers and payload parameters.'],
                    ['title' => 'Review & Resolve Appeal', 'desc' => 'Review requester\'s explanation and approve or reject their appeal ticket directly.'],
                ],
                'id' => [
                    ['title' => 'Ubah Status Karantina', 'desc' => 'Tangguhkan sementara atau aktifkan kembali sanksi blokir dengan satu klik.'],
                    ['title' => 'Hapus Catatan Karantina', 'desc' => 'Hapus hukuman blokir secara permanen untuk memulihkan akses klien.'],
                    ['title' => 'Periksa Raw Log Insiden', 'desc' => 'Klik detail pada baris insiden untuk melihat header HTTP lengkap dan muatan parameter request.'],
                    ['title' => 'Proses Tiket Banding', 'desc' => 'Tinjau alasan yang ditulis pemohon dan setujui atau tolak tiket pembukaan blokir secara langsung.'],
                ],
            ],
            'tips' => [
                'en' => 'A high post-block hit count (e.g. dozens of dropped requests within minutes) is strong evidence of an automated scanner script continuing to hammer your server.',
                'id' => 'Jumlah hit pasca-blokir yang tinggi dalam waktu singkat merupakan bukti kuat bahwa IP tersebut dikendalikan bot/scanner otomatis yang terus membombardir server.',
            ],
        ],
        'allowed-ips' => [
            'icon' => '✅',
            'title' => [
                'en' => 'Allowed & Whitelisted IPs',
                'id' => 'Daftar IP Diizinkan (Allowlist / Whitelist)',
            ],
            'badge' => [
                'en' => 'Zero-Inspection Trusted Network Rules',
                'id' => 'Aturan Jaringan Terpercaya Bebas Inspeksi',
            ],
            'purpose' => [
                'en' => 'Defines trusted network addresses, corporate subnets, and wildcard ranges that completely bypass Pasak security inspection. Traffic from allowed addresses is never throttled, blocked by honeypots, or flagged by rate limiters.',
                'id' => 'Menentukan alamat IP, subnet kantor, atau rentang wildcard terpercaya yang sepenuhnya dibebaskan dari inspeksi WAF Pasak. Lalu lintas dari daftar ini tidak akan pernah diperlambat, terkena honeypot, maupun terblokir oleh rate limiter.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Allowlist Metrics Cards', 'desc' => 'Total allowed rules registered and number of currently active rules.'],
                    ['title' => 'Flexible IP Notation', 'desc' => 'Supports single IPv4/IPv6 addresses (203.0.113.10), wildcard masks (192.168.1.*), or standard CIDR subnets (10.0.0.0/8).'],
                    ['title' => 'Descriptive Identifier Labels', 'desc' => 'Custom human-readable tags such as "HQ Office VPN", "Staging Server", or "Payment Gateway Webhook".'],
                    ['title' => 'Time-Bound Expiration Trail', 'desc' => 'Tracks duration in hours or marks entries as permanent, displaying exact allowed timestamp and expiry time.'],
                    ['title' => 'Active / Inactive Status Badges', 'desc' => 'Visual indicators showing which whitelist rules are actively in effect.'],
                ],
                'id' => [
                    ['title' => 'Kartu Metrik Daftar Izin', 'desc' => 'Jumlah total aturan izin yang terdaftar dan jumlah aturan yang saat ini aktif.'],
                    ['title' => 'Notasi Pola IP Fleksibel', 'desc' => 'Mendukung IP tunggal (203.0.113.10), pola wildcard bintang (192.168.1.*), maupun subnet CIDR (10.0.0.0/8).'],
                    ['title' => 'Label Keterangan Pengenal', 'desc' => 'Catatan pengenal administratif seperti "VPN Kantor Pusat", "Server Staging", atau "Webhook Payment Gateway".'],
                    ['title' => 'Audit Kedaluwarsa Berbatas Waktu', 'desc' => 'Mendukung durasi izin dalam satuan jam atau permanen, menampilkan waktu pembuatan dan waktu kedaluwarsa.'],
                    ['title' => 'Badge Status Aktif / Nonaktif', 'desc' => 'Indikator visual aturan mana yang sedang berlaku efektif membebaskan inspeksi.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Add Allowed IP Entry', 'desc' => 'Input an IP pattern, optional descriptive label, and duration (0 for permanent), then submit with confirmation.'],
                    ['title' => 'Toggle Active / Inactive', 'desc' => 'Temporarily suspend whitelist privilege without deleting the rule configuration.'],
                    ['title' => 'Delete Whitelist Rule', 'desc' => 'Permanently remove the entry to restore normal security inspection for that network range.'],
                    ['title' => 'Search & Filter', 'desc' => 'Quickly locate allowlist entries by IP pattern or descriptive label.'],
                ],
                'id' => [
                    ['title' => 'Tambah Entri IP Diizinkan', 'desc' => 'Masukkan pola IP, label keterangan, dan durasi jam (isi 0 untuk permanen), lalu kirim dengan konfirmasi.'],
                    ['title' => 'Ubah Status Aktif / Nonaktif', 'desc' => 'Tangguhkan sementara hak istimewa bebas inspeksi tanpa perlu menghapus konfigurasi aturan.'],
                    ['title' => 'Hapus Aturan Izin', 'desc' => 'Hapus entri secara permanen untuk mengembalikan pengawasan keamanan normal pada rentang IP tersebut.'],
                    ['title' => 'Pencarian Entri Izin', 'desc' => 'Cari aturan izin berdasarkan pola IP atau label keterangan secara instan.'],
                ],
            ],
            'tips' => [
                'en' => 'Never whitelist broad public CIDRs (like 0.0.0.0/0). Limit allowlist entries strictly to static corporate VPNs, known monitoring agents, or trusted payment gateway webhooks.',
                'id' => 'Jangan pernah mengizinkan subnet publik yang terlalu luas (seperti 0.0.0.0/0). Batasi whitelist hanya untuk VPN kantor, IP monitoring resmi, atau webhook payment gateway terpercaya.',
            ],
        ],
        'server' => [
            'icon' => '🛡️',
            'title' => [
                'en' => 'Server Security & Integrity Monitor',
                'id' => 'Keamanan Server & Integritas Berkas',
            ],
            'badge' => [
                'en' => 'FIM Baseline & Suspicious File Scanner',
                'id' => 'Pemantau Integritas File & Anti-Webshell',
            ],
            'purpose' => [
                'en' => 'Deep file integrity monitoring (FIM) and suspicious code analysis. Detects unauthorized PHP files, hidden webshell backdoors, polyglot image payloads, and untracked code tampering across your application directory.',
                'id' => 'Pemantauan integritas berkas (FIM) mendalam dan analisis kode berbahaya. Mendeteksi file PHP liar, webshell terselubung, payload gambar polyglot, dan modifikasi kode ilegal di seluruh direktori aplikasi.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Integrity Baseline Snapshot Card', 'desc' => 'Displays tracked file count, baseline generation timestamp, and cryptographic hash state.'],
                    ['title' => 'Suspicious File Alert Ledger', 'desc' => 'Lists flagged files with file path, size, modification timestamp, match rationale (e.g. eval, base64_decode, system commands), and safety status.'],
                    ['title' => 'Polyglot & Webshell Scanner', 'desc' => 'Automated heuristic scanner detecting hidden PHP code embedded inside JPEG/PNG headers, disguised extensions, and obfuscated stubs.'],
                    ['title' => 'Host Environment Diagnostics', 'desc' => 'Audits Linux environment, storage directory permissions, PHP security settings, and active kernel firewall tables.'],
                ],
                'id' => [
                    ['title' => 'Kartu Snapshot Baseline Integritas', 'desc' => 'Menampilkan jumlah berkas yang terpantau, waktu pembuatan baseline, dan status hash kriptografis.'],
                    ['title' => 'Daftar Berkas Mencurigakan', 'desc' => 'Menyajikan berkas yang terindikasi berbahaya lengkap dengan path, ukuran file, waktu perubahan, alasan deteksi (eval, base64_decode, shell_exec), dan status verifikasi.'],
                    ['title' => 'Pemindai Polyglot & Webshell', 'desc' => 'Deteksi heuristik terhadap kode PHP yang diselipkan ke dalam header gambar JPEG/PNG, ekstensi ganda, atau fungsi terselubung.'],
                    ['title' => 'Diagnostik Lingkungan Server', 'desc' => 'Memeriksa sistem operasi Linux, hak akses direktori storage, konfigurasi keamanan PHP, dan kesiapan firewall kernel.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Rebuild Baseline', 'desc' => 'Generate a fresh cryptographic baseline snapshot immediately after official code deployments or package upgrades.'],
                    ['title' => 'Verify Benign / Safe', 'desc' => 'Mark legitimate application files as verified safe to silence scanner alerts.'],
                    ['title' => 'Delete Malicious Webshell', 'desc' => 'Permanently delete confirmed webshells and injected backdoors directly from the server.'],
                    ['title' => 'Download Suspicious File', 'desc' => 'Download quarantined files securely in a safe archive for offline analysis in an isolated sandbox.'],
                    ['title' => 'Global Framework Optimize Clear', 'desc' => 'Execute optimize:clear to reset view, config, and route caches safely.'],
                ],
                'id' => [
                    ['title' => 'Buat Ulang Baseline Integritas', 'desc' => 'Perbarui snapshot baseline berkas setelah Anda melakukan deployment resmi atau update kode aplikasi.'],
                    ['title' => 'Tandai Berkas Aman (Verify Safe)', 'desc' => 'Tandai berkas yang sah sebagai aman untuk membungkam peringatan scanner di masa mendatang.'],
                    ['title' => 'Hapus Berkas Webshell Berbahaya', 'desc' => 'Hapus permanen berkas webshell atau backdoor sisipan langsung dari server.'],
                    ['title' => 'Unduh Berkas untuk Analisis', 'desc' => 'Unduh berkas mencurigakan secara aman untuk diteliti payload-nya di lingkungan sandbox terisolasi.'],
                    ['title' => 'Bersihkan Cache Framework', 'desc' => 'Jalankan perintah optimize:clear untuk menyegarkan cache view, config, dan route aplikasi.'],
                ],
            ],
            'tips' => [
                'en' => 'Always click "Rebuild Baseline" immediately after completing a git pull, composer update, or production deployment so new files are recognized as authorized.',
                'id' => 'Selalu klik "Buat Ulang Baseline" segera setelah selesai melakukan git pull atau update produksi agar berkas baru resmi dikenali sebagai kode yang sah.',
            ],
        ],
        'sessions' => [
            'icon' => '👥',
            'title' => [
                'en' => 'User Sessions & Device Audit',
                'id' => 'Sesi Pengguna & Riwayat Login',
            ],
            'badge' => [
                'en' => 'Credential Shield & Account Takeover Defense',
                'id' => 'Pelindung Akun & Pertahanan Brute-Force',
            ],
            'purpose' => [
                'en' => 'Monitors active user sessions, tracks authentication history, protects admin accounts against credential stuffing and brute-force attacks, and manages trusted IP immunity.',
                'id' => 'Memantau sesi login pengguna yang sedang aktif, mencatat riwayat otentikasi, melindungi akun admin dari serangan brute-force dan credential stuffing, serta mengelola IP terpercaya dengan hak istimewa.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Online Users (5 min) Card', 'desc' => 'Tracks authenticated accounts currently interacting with the application in real-time.'],
                    ['title' => 'Registered Trusted IPs Card', 'desc' => 'Shows the number of trusted administrative IPs registered to your account.'],
                    ['title' => 'Active Session Fleet Table', 'desc' => 'Lists active session tokens, user ID/email, client browser, operating system, IP address, and last activity timestamp.'],
                    ['title' => 'Authentication Audit Log', 'desc' => 'Chronological ledger of all login attempts (successful and failed), pinpointing brute-force probes and suspicious devices.'],
                    ['title' => 'Adaptive Tiered Lockouts', 'desc' => 'Displays cooldown status when accounts or IPs are throttled by brute-force protection.'],
                ],
                'id' => [
                    ['title' => 'Kartu Pengguna Online (5 Menit)', 'desc' => 'Menghitung jumlah akun terotentikasi yang sedang aktif berinteraksi dengan aplikasi secara real-time.'],
                    ['title' => 'Kartu IP Terpercaya Terdaftar', 'desc' => 'Menampilkan jumlah IP administratif yang terdaftar sebagai IP terpercaya untuk akun Anda.'],
                    ['title' => 'Tabel Sesi Pengguna Aktif', 'desc' => 'Mencatat token sesi aktif, ID/email pengguna, browser klien, sistem operasi, IP, dan waktu aktivitas terakhir.'],
                    ['title' => 'Audit Riwayat Otentikasi', 'desc' => 'Buku besar kronologis seluruh percobaan login (berhasil maupun gagal) untuk melacak percobaan brute-force.'],
                    ['title' => 'Pendinginan Bertingkat Brute-Force', 'desc' => 'Menerapkan masa jeda (cooldown) bertingkat saat terdeteksi upaya tebak kata sandi berulang.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Trust My Current IP', 'desc' => 'Click "Save my IP" to register your current office/home IP as trusted, granting immunity from accidental honeypot lockouts.'],
                    ['title' => 'Force Logout Active Session', 'desc' => 'Immediately terminate a session token to instantly evict a stolen or suspicious device from your application.'],
                    ['title' => 'Filter Login Records', 'desc' => 'Search authentication history by user email, IP address, status (Success/Failed), or date window.'],
                    ['title' => 'Delete Audit Entry', 'desc' => 'Remove individual login records when archiving or purging data.'],
                ],
                'id' => [
                    ['title' => 'Simpan IP Saat Ini sebagai Terpercaya', 'desc' => 'Klik "Simpan IP Saya" untuk mendaftarkan IP Anda agar kebal dari salah blokir sensor/honeypot.'],
                    ['title' => 'Paksa Logout Sesi Aktif', 'desc' => 'Putus token sesi secara instan untuk mendepak perangkat mencurigakan dari aplikasi seketika.'],
                    ['title' => 'Filter Riwayat Login', 'desc' => 'Saring riwayat login berdasarkan email pengguna, IP, status (Berhasil/Gagal), atau rentang tanggal.'],
                    ['title' => 'Hapus Catatan Riwayat', 'desc' => 'Hapus catatan sesi atau login tertentu dari database jika diperlukan pembersihan data.'],
                ],
            ],
            'tips' => [
                'en' => 'Always register your static office or home IP under Trusted IPs. If an administrator accidentally hits a security tripwire, logging in successfully from a recognized device will automatically lift the block.',
                'id' => 'Daftarkan IP kantor atau rumah Anda sebagai IP Terpercaya. Jika admin tidak sengaja memicu jebakan honeypot, login yang berhasil dari browser terverifikasi akan otomatis mencabut blokir IP tersebut.',
            ],
        ],
        'tickets' => [
            'icon' => '🎫',
            'title' => [
                'en' => 'Unblock Appeals & Support Tickets',
                'id' => 'Tiket Banding Pembukaan Blokir',
            ],
            'badge' => [
                'en' => 'Visitor Self-Service Quarantine Appeal Desk',
                'id' => 'Layanan Banding Mandiri Pengguna Terblokir',
            ],
            'purpose' => [
                'en' => 'Visitor self-service unblock appeal management desk. Quarantined visitors encountering the HTTP 403 Forbidden page can submit an unblock request with their explanation, allowing administrators to review and release false-positive victims without touching the database.',
                'id' => 'Pusat penanganan banding pembukaan blokir mandiri bagi pengunjung. Pengguna yang terkena halaman error HTTP 403 dapat mengajukan permohonan pembukaan blokir disertai alasan, sehingga admin dapat meninjau dan membuka blokir false-positive dalam satu klik.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Status Triage Badges', 'desc' => 'Sorts appeal tickets into Pending (awaiting admin review), Approved (quarantine lifted), and Rejected (confirmed malicious).'],
                    ['title' => 'Requester Identification', 'desc' => 'Captures requester name, email address, blocked IP address, device identifier, and submission timestamp.'],
                    ['title' => 'Appeal Justification Narrative', 'desc' => 'The user\'s stated explanation describing what they were attempting to do before being blocked.'],
                    ['title' => 'Link to Quarantine Record', 'desc' => 'Direct link to the active block record and underlying attack logs for instant correlation.'],
                ],
                'id' => [
                    ['title' => 'Badge Status Tiket', 'desc' => 'Mengelompokkan tiket ke status Menunggu (Pending), Disetujui (Approved), dan Ditolak (Rejected).'],
                    ['title' => 'Identitas Lengkap Pemohon', 'desc' => 'Mencatat nama pemohon, alamat email, IP yang terblokir, identitas perangkat, dan waktu pengajuan.'],
                    ['title' => 'Narasi Alasan Pemohon', 'desc' => 'Penjelasan tertulis yang dikirimkan pengguna mengenai aktivitas apa yang sedang dilakukan sebelum terkena blokir.'],
                    ['title' => 'Tautan Rekaman Blokir Terkait', 'desc' => 'Tautan langsung ke data karantina aktif dan log serangan awal untuk memverifikasi kebenaran klaim.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Approve & Lift Quarantine', 'desc' => 'Approve the appeal to immediately remove the block from both WAF and kernel tables in a single click.'],
                    ['title' => 'Reject Appeal', 'desc' => 'Dismiss appeals submitted by confirmed attackers or automated vulnerability scanners with an optional note.'],
                    ['title' => 'Inspect Ticket Detail', 'desc' => 'Open the full ticket dossier to cross-examine the visitor\'s story against the raw attack evidence.'],
                    ['title' => 'Search & Filter Tickets', 'desc' => 'Filter appeal tickets by status, email, IP, or submission date.'],
                ],
                'id' => [
                    ['title' => 'Setujui & Buka Blokir Seketika', 'desc' => 'Klik tombol Setujui untuk langsung mencabut sanksi blokir dari WAF dan kernel Linux secara otomatis.'],
                    ['title' => 'Tolak Permohonan Banding', 'desc' => 'Tolak tiket dari penyerang atau bot scanner dengan catatan evaluasi penolakan.'],
                    ['title' => 'Buka Rincian Tiket', 'desc' => 'Buka halaman detail tiket untuk mencocokkan alasan pemohon dengan bukti teknis pada log serangan.'],
                    ['title' => 'Pencarian & Filter Tiket', 'desc' => 'Saring tiket banding berdasarkan status, email pemohon, IP, atau tanggal pengajuan.'],
                ],
            ],
            'tips' => [
                'en' => 'Always check the triggering attack logs on the Ticket Detail page before approving an appeal. If the request was caused by a benign user form submission, consider adjusting rule thresholds in Settings.',
                'id' => 'Selalu periksa log serangan pemicu pada halaman Detail Tiket sebelum menyetujui. Jika terbukti merupakan form web normal yang salah terdeteksi, sesuaikan sensitivitas rule di Pengaturan.',
            ],
        ],
        'ticket-detail' => [
            'icon' => '📝',
            'title' => [
                'en' => 'Unblock Appeal Ticket Investigation',
                'id' => 'Investigasi Tiket Banding Pembukaan Blokir',
            ],
            'badge' => [
                'en' => 'Claim Verification & Admin Decision',
                'id' => 'Verifikasi Klaim & Keputusan Administrator',
            ],
            'purpose' => [
                'en' => 'Detailed investigation workbench for resolving a specific unblock appeal ticket. Allows administrators to compare the requester\'s claim with the exact forensic attack evidence that triggered the quarantine before issuing an approval or rejection.',
                'id' => 'Meja kerja investigasi menyeluruh untuk menyelesaikan satu tiket banding pembukaan blokir. Memungkinkan administrator membandingkan alasan pemohon dengan bukti forensik serangan asli sebelum memutuskan menyetujui atau menolak permohonan.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Requester Claim & Metadata', 'desc' => 'Requester\'s name, contact email, IP address, device hash, and submission timestamp.'],
                    ['title' => 'Requester Explanation Box', 'desc' => 'Full unaltered text of the explanation provided by the user on the 403 block page.'],
                    ['title' => 'Associated Block Record Card', 'desc' => 'Shows the active quarantine status, scope (IP vs Device), hit count, and expiration date.'],
                    ['title' => 'Triggering Forensic Attack Log', 'desc' => 'Displays the exact HTTP request, URL path, matched rule signature, and payload snippet that caused the initial block.'],
                ],
                'id' => [
                    ['title' => 'Data & Metadata Pemohon', 'desc' => 'Nama pemohon, email kontak, alamat IP, hash perangkat, dan waktu pengiriman tiket.'],
                    ['title' => 'Kotak Narasi Alasan Pemohon', 'desc' => 'Teks lengkap tanpa rekayasa yang ditulis pengguna saat menghadapi halaman pemblokiran 403.'],
                    ['title' => 'Kartu Rekaman Blokir Terkait', 'desc' => 'Menampilkan status blokir aktif saat ini, lingkup penegakan, jumlah hit, dan masa berlaku.'],
                    ['title' => 'Log Serangan Asal Pemicu Blokir', 'desc' => 'Menyajikan request HTTP asli, URL target, signature rule, dan potongan payload yang menyebabkan blokir dijatuhkan.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Approve Appeal', 'desc' => 'Instantly lifts the quarantine and marks the ticket as Approved.'],
                    ['title' => 'Reject Appeal', 'desc' => 'Dismisses the appeal while maintaining the active block, recording your administrative decision.'],
                    ['title' => 'Return to Tickets', 'desc' => 'Return to the master appeal ticket triage list.'],
                ],
                'id' => [
                    ['title' => 'Setujui Permohonan Banding', 'desc' => 'Mencabut sanksi blokir IP/perangkat seketika dan mengubah status tiket menjadi Disetujui (Approved).'],
                    ['title' => 'Tolak Permohonan Banding', 'desc' => 'Menolak permohonan dan membiarkan sanksi blokir tetap aktif untuk melindungi aplikasi.'],
                    ['title' => 'Kembali ke Daftar Tiket', 'desc' => 'Kembali ke daftar utama tiket banding.'],
                ],
            ],
            'tips' => [
                'en' => 'If the triggering log shows a legitimate form input (e.g. an address containing punctuation or SQL-like words), approve the appeal and whitelist the specific parameter in configuration.',
                'id' => 'Jika log pemicu berasal dari input form yang wajar (misalnya alamat yang mengandung tanda kutip), setujui banding dan sesuaikan pengecualian parameter di konfigurasi.',
            ],
        ],
        'settings' => [
            'icon' => '⚙️',
            'title' => [
                'en' => 'WAF & Sensor Security Settings',
                'id' => 'Pengaturan WAF & Sensor Keamanan',
            ],
            'badge' => [
                'en' => 'Enforcement Policies, Thresholds & Channels',
                'id' => 'Kebijakan Penegakan, Ambang Batas & Notifikasi',
            ],
            'purpose' => [
                'en' => 'The central nerve center for configuring Pasak\'s security posture and threat tolerance. Customize automated quarantine enforcement modes, fine-tune threat level hit thresholds, configure notification webhooks, and adjust UI preferences.',
                'id' => 'Pusat kendali konfigurasi postur pertahanan dan toleransi ancaman Pasak. Atur mode penegakan karantina otomatis, sesuaikan batas toleransi hit per level ancaman, konfigurasi webhook notifikasi, dan preferensi tampilan dasbor.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Enforcement Mode Selector', 'desc' => 'Choose between Passive Monitoring-Only (logs attacks without blocking) or Active Quarantine Enforcement (automatically drops attackers with HTTP 403).'],
                    ['title' => 'Threat Level Hit Thresholds', 'desc' => 'Configure tolerance thresholds before auto-blocking across Critical (default: 1), High (default: 3), Medium (default: 10), and Low (default: 25) threats.'],
                    ['title' => 'Quarantine Scope Strategy', 'desc' => 'Select default auto-blocking scope: Entire IP Address or WiFi-Safe Malicious Device Fingerprint.'],
                    ['title' => 'Notification Channels', 'desc' => 'Configure Discord, Telegram, or custom webhook integrations for instant security alert dispatching.'],
                    ['title' => 'Main App Integration URL', 'desc' => 'Set the destination URL for the "Main Dashboard" header button to return administrators to your host application.'],
                    ['title' => 'Theme & Locale Preferences', 'desc' => 'Select Dark, Light, or System visual themes and manage dashboard localization.'],
                ],
                'id' => [
                    ['title' => 'Pemilih Mode Penegakan', 'desc' => 'Pilih antara Mode Pemantauan Pasif (hanya mencatat log tanpa memblokir) atau Mode Penegakan Aktif (otomatis memblokir penyerang dengan HTTP 403).'],
                    ['title' => 'Ambang Batas Toleransi per Level', 'desc' => 'Atur batas toleransi hit sebelum pemblokiran otomatis untuk Kritis (default: 1), Tinggi (default: 3), Sedang (default: 10), dan Rendah (default: 25).'],
                    ['title' => 'Strategi Lingkup Karantina', 'desc' => 'Pilih lingkup sanksi otomatis: Seluruh Alamat IP atau Isolasi Perangkat Berbahaya (WiFi-Safe).'],
                    ['title' => 'Kanal Notifikasi Eksternal', 'desc' => 'Konfigurasi integrasi webhook Discord, Telegram, atau custom HTTP endpoint untuk peringatan insiden seketika.'],
                    ['title' => 'URL Integrasi Aplikasi Induk', 'desc' => 'Tentukan alamat tautan tombol "Dashboard Utama" pada header untuk kembali ke aplikasi utama Anda.'],
                    ['title' => 'Tema Visual & Bahasa', 'desc' => 'Atur tema visual Gelap (Dark), Terang (Light), atau Otomatis (Sistem) dan preferensi bahasa dasbor.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Save All Settings', 'desc' => 'Commit security policy changes to the database and apply them instantly across all incoming requests without restarting services.'],
                    ['title' => 'Test Main Dashboard Link', 'desc' => 'Verify that your configured host app URL works properly.'],
                    ['title' => 'Toggle Theme On The Fly', 'desc' => 'Switch between Dark and Light mode using the header theme switcher.'],
                ],
                'id' => [
                    ['title' => 'Simpan Seluruh Pengaturan', 'desc' => 'Terapkan seluruh perubahan kebijakan ke database seketika tanpa perlu restart web server.'],
                    ['title' => 'Uji Tautan Dashboard Utama', 'desc' => 'Pastikan alamat tautan kembali ke aplikasi induk yang Anda atur terbuka dengan benar.'],
                    ['title' => 'Ubah Tema Visual Instan', 'desc' => 'Ganti tema tampilan Gelap/Terang langsung melalui tombol tema di header dasbor.'],
                ],
            ],
            'tips' => [
                'en' => 'Keep the Critical threat threshold at 1 hit so webshell backdoors, SQL injection, and remote code execution attempts are blocked on their very first probe.',
                'id' => 'Pertahankan ambang batas ancaman Kritis pada angka 1 hit agar percobaan webshell, SQL injection, dan RCE langsung dijatuhkan pada serangan pertama.',
            ],
        ],
        'log-detail' => [
            'icon' => '🔍',
            'title' => [
                'en' => 'Forensic Threat Log Details',
                'id' => 'Detail Forensik Log Ancaman',
            ],
            'badge' => [
                'en' => 'Evidence Dumps & Attack Chronology',
                'id' => 'Dump Bukti Muatan & Kronologi Penyerangan',
            ],
            'purpose' => [
                'en' => 'Deep forensic examination of a single intercepted security violation. Allows security engineers to inspect raw HTTP headers, query string payloads, client fingerprints, and the attack sequence from the same IP.',
                'id' => 'Pemeriksaan forensik mendalam terhadap satu insiden pelanggaran keamanan yang terintersepsi. Memungkinkan analis meneliti raw header HTTP, muatan query string/payload, sidik jari perangkat, dan urutan serangan dari IP yang sama.',
            ],
            'features' => [
                'en' => [
                    ['title' => 'Violation Header & Metadata Card', 'desc' => 'Threat level, matched rule name, HTTP method, URL path, timestamp in server timezone, and status code.'],
                    ['title' => 'Malicious Evidence Inspector', 'desc' => 'Highlighted snippet of the exact hostile pattern (SQLi tokens, webshell commands, directory traversal dots) that triggered the rule.'],
                    ['title' => 'Client Telemetry & Fingerprint', 'desc' => 'Client IP, client user-agent string, resolved country/city geolocation, and device fingerprint hash.'],
                    ['title' => 'Raw HTTP Request Dump', 'desc' => 'Complete raw HTTP headers and request body payload captured at time of interception.'],
                    ['title' => 'Timeline from Same IP', 'desc' => 'Table of preceding and subsequent requests originating from this client around the same timeframe.'],
                ],
                'id' => [
                    ['title' => 'Kartu Metadata & Header Insiden', 'desc' => 'Tingkat keparahan, nama rule yang cocok, metode HTTP, path URL, waktu server, dan kode status.'],
                    ['title' => 'Kotak Inspeksi Bukti Berbahaya', 'desc' => 'Potongan pola berbahaya (token SQLi, perintah webshell, traversal direktori) yang memicu aturan WAF.'],
                    ['title' => 'Telemetri Klien & Sidik Jari', 'desc' => 'IP klien, string user-agent browser, estimasi geolokasi negara/kota, dan hash fingerprint perangkat.'],
                    ['title' => 'Dump Request HTTP Mentah', 'desc' => 'Daftar lengkap raw header HTTP dan muatan body request saat insiden diintersepsi.'],
                    ['title' => 'Kronologi Serangan dari IP Sama', 'desc' => 'Tabel request sebelum dan sesudah insiden ini yang dikirimkan oleh klien bersangkutan.'],
                ],
            ],
            'actions' => [
                'en' => [
                    ['title' => 'Instant Quarantine', 'desc' => 'Click "Block IP" to quarantine the attacker immediately from this forensic view.'],
                    ['title' => 'Back to Audit Logs', 'desc' => 'Return to the master audit log stream with your previous filters and search terms intact.'],
                    ['title' => 'Copy Evidence', 'desc' => 'Copy raw payload strings or IP addresses for external firewall rule creation.'],
                ],
                'id' => [
                    ['title' => 'Karantina Seketika', 'desc' => 'Klik tombol "Blokir IP" untuk mengisolasi penyerang langsung dari halaman forensik ini.'],
                    ['title' => 'Kembali ke Log Audit', 'desc' => 'Kembali ke daftar log keamanan dengan mempertahankan filter dan kata kunci pencarian sebelumnya.'],
                    ['title' => 'Salin Bukti Muatan', 'desc' => 'Salin teks payload atau alamat IP untuk pelaporan insiden atau konfigurasi firewall eksternal.'],
                ],
            ],
            'tips' => [
                'en' => 'Cross-reference the User-Agent and path patterns against known CVE databases to identify automated scanners such as sqlmap, Nikto, or Nuclei.',
                'id' => 'Cocokkan string User-Agent dan pola URL dengan database CVE untuk mengetahui apakah penyerang menggunakan alat scanner otomatis seperti sqlmap, Nikto, atau Nuclei.',
            ],
        ],
    ];

    $guide = $pageGuides[$pageKey] ?? $pageGuides['overview'];
    $currentLang = $currentLocale ?? (in_array(app()->getLocale(), ['en', 'id'], true) ? app()->getLocale() : 'en');
@endphp

<!-- Page Support & Guide Modal Component -->
<div class="sec-modal-backdrop" id="pasakPageSupportModal" hidden role="dialog" aria-modal="true" aria-labelledby="pasakSupportModalTitle">
    <div class="sec-support-modal-box">
        <!-- Modal Header with Page Title & In-Modal Language Switcher -->
        <div class="sec-modal-header" style="display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 18px 24px; border-bottom: 1px solid var(--sec-border); background: var(--sec-card);">
            <div style="display: flex; align-items: center; gap: 12px; min-width: 0;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.2); display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                    {{ $guide['icon'] }}
                </div>
                <div style="min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <h3 class="sec-card-title" id="pasakSupportModalTitle" style="font-size: 16px; margin: 0; font-weight: 700;">
                            <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">{{ $guide['title']['en'] }}</span>
                            <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">{{ $guide['title']['id'] }}</span>
                        </h3>
                        <span class="sec-badge sec-badge-low" style="font-size: 11px; font-weight: 600;">
                            <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">{{ $guide['badge']['en'] }}</span>
                            <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">{{ $guide['badge']['id'] }}</span>
                        </span>
                    </div>
                    <div style="font-size: 12px; color: var(--sec-text-muted); margin-top: 3px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                        <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">Active Page:</span>
                        <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">Halaman Aktif:</span>
                        <code class="sec-font-mono" style="background: var(--sec-bg); padding: 1px 6px; border-radius: 4px; font-size: 11px; color: var(--sec-primary); border: 1px solid var(--sec-border);">/{{ request()->path() }}</code>
                    </div>
                </div>
            </div>

            <!-- Language Switcher & Close Button -->
            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                <div class="sec-lang-switcher" style="height: 30px; padding: 2px 4px;" title="Switch Guide Language / Ganti Bahasa Panduan">
                    <button type="button" class="sec-lang-btn {{ $currentLang === 'en' ? 'is-active' : '' }}" onclick="window.pasakSwitchGuideLang('en');" id="pasakGuideLangEn" style="padding: 2px 6px; font-size: 11px;">EN</button>
                    <span class="sec-lang-sep" style="font-size: 10px;">/</span>
                    <button type="button" class="sec-lang-btn {{ $currentLang === 'id' ? 'is-active' : '' }}" onclick="window.pasakSwitchGuideLang('id');" id="pasakGuideLangId" style="padding: 2px 6px; font-size: 11px;">ID</button>
                </div>
                <button type="button" class="sec-btn sec-btn-ghost sec-btn-sm" onclick="window.pasakClosePageSupport();" title="{{ __('Tutup') }}" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 18px; line-height: 1; border-radius: 6px;">
                    &times;
                </button>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div class="sec-support-modal-body">
            <!-- 1. Purpose / Overview Section -->
            <div class="sec-support-section" style="border-left: 3px solid var(--sec-primary);">
                <div class="sec-support-section-title">
                    <span>🎯</span>
                    <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">Page Purpose & Strategic Role</span>
                    <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">Fungsi & Peran Strategis Halaman</span>
                </div>
                <p style="margin: 0; font-size: 13.5px; line-height: 1.6; color: var(--sec-text);">
                    <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">{{ $guide['purpose']['en'] }}</span>
                    <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">{{ $guide['purpose']['id'] }}</span>
                </p>
            </div>

            <!-- 2. Core Features Breakdown -->
            <div>
                <div class="sec-support-section-title">
                    <span>📊</span>
                    <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">Information & Metrics Displayed</span>
                    <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">Informasi & Data yang Ditampilkan</span>
                </div>
                <div class="sec-support-grid">
                    @foreach ($guide['features']['en'] as $idx => $featEn)
                        @php $featId = $guide['features']['id'][$idx] ?? $featEn; @endphp
                        <div class="sec-support-item">
                            <div class="sec-support-item-title">
                                <span style="color: var(--sec-primary);">•</span>
                                <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">{{ $featEn['title'] }}</span>
                                <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">{{ $featId['title'] }}</span>
                            </div>
                            <div class="sec-support-item-desc">
                                <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">{{ $featEn['desc'] }}</span>
                                <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">{{ $featId['desc'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. Action Guide -->
            <div>
                <div class="sec-support-section-title">
                    <span>🛠️</span>
                    <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">Available Actions & Operator Guide</span>
                    <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">Panduan Tindakan & Aksi yang Dapat Dilakukan</span>
                </div>
                <div class="sec-support-grid">
                    @foreach ($guide['actions']['en'] as $idx => $actEn)
                        @php $actId = $guide['actions']['id'][$idx] ?? $actEn; @endphp
                        <div class="sec-support-item">
                            <div class="sec-support-item-title">
                                <span>▶️</span>
                                <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">{{ $actEn['title'] }}</span>
                                <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">{{ $actId['title'] }}</span>
                            </div>
                            <div class="sec-support-item-desc">
                                <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">{{ $actEn['desc'] }}</span>
                                <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">{{ $actId['desc'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 4. Security Best Practice Tip -->
            <div class="sec-support-section" style="background: rgba(37, 99, 235, 0.05); border: 1px solid rgba(37, 99, 235, 0.2);">
                <div class="sec-support-section-title" style="color: var(--sec-primary);">
                    <span>💡</span>
                    <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">Security Recommendation & Pro-Tip</span>
                    <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">Rekomendasi Keamanan & Tips Ahli</span>
                </div>
                <p style="margin: 0; font-size: 13px; line-height: 1.55; color: var(--sec-text);">
                    <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">{{ $guide['tips']['en'] }}</span>
                    <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">{{ $guide['tips']['id'] }}</span>
                </p>
            </div>

            @yield('page_support_custom')
        </div>

        <!-- Modal Footer -->
        <div class="sec-modal-footer" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 24px; border-top: 1px solid var(--sec-border); background: var(--sec-card-hover);">
            <div style="font-size: 12px; color: var(--sec-text-muted);">
                <span>Pasak Autonomous Linux VPS & Web Security</span>
                <span> &bull; </span>
                <a href="https://github.com/robyajo/pasak" target="_blank" rel="noopener" style="color: var(--sec-primary); text-decoration: none; font-weight: 500;">GitHub Docs &rarr;</a>
            </div>
            <button type="button" class="sec-btn sec-btn-primary" onclick="window.pasakClosePageSupport();">
                <span data-guide-lang="en" style="{{ $currentLang === 'en' ? '' : 'display:none;' }}">Got it, continue</span>
                <span data-guide-lang="id" style="{{ $currentLang === 'id' ? '' : 'display:none;' }}">Mengerti, tutup</span>
            </button>
        </div>
    </div>
</div>

<script>
    (function() {
        // Modal Open / Close Handlers
        window.pasakOpenPageSupport = function() {
            var modal = document.getElementById('pasakPageSupportModal');
            if (modal) {
                modal.removeAttribute('hidden');
                document.body.style.overflow = 'hidden';
            }
        };

        window.pasakClosePageSupport = function() {
            var modal = document.getElementById('pasakPageSupportModal');
            if (modal) {
                modal.setAttribute('hidden', '');
                document.body.style.overflow = '';
            }
        };

        // In-modal language switcher without page reload
        window.pasakSwitchGuideLang = function(lang) {
            var enElements = document.querySelectorAll('[data-guide-lang="en"]');
            var idElements = document.querySelectorAll('[data-guide-lang="id"]');
            var btnEn = document.getElementById('pasakGuideLangEn');
            var btnId = document.getElementById('pasakGuideLangId');

            if (lang === 'id') {
                enElements.forEach(function(el) { el.style.display = 'none'; });
                idElements.forEach(function(el) { el.style.display = ''; });
                if (btnEn) btnEn.classList.remove('is-active');
                if (btnId) btnId.classList.add('is-active');
            } else {
                idElements.forEach(function(el) { el.style.display = 'none'; });
                enElements.forEach(function(el) { el.style.display = ''; });
                if (btnId) btnId.classList.remove('is-active');
                if (btnEn) btnEn.classList.add('is-active');
            }
        };

        // Close on ESC key or backdrop click
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                var modal = document.getElementById('pasakPageSupportModal');
                if (modal && !modal.hasAttribute('hidden')) {
                    window.pasakClosePageSupport();
                }
            }
        });

        var modalEl = document.getElementById('pasakPageSupportModal');
        if (modalEl) {
            modalEl.addEventListener('click', function(e) {
                if (e.target === modalEl) {
                    window.pasakClosePageSupport();
                }
            });
        }
    })();
</script>

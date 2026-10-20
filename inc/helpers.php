<?php
/**
 * Hàm dùng chung: thoát ký tự, cài đặt, đường dẫn, định dạng tiền, biểu tượng.
 */

/* ---------- Escaping ---------- */
function e($s): string { return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function eu($url): string
{
    $url = trim((string) $url);
    if ($url === '' || preg_match('~^\s*(javascript|data|vbscript):~i', $url)) {
        return $url === '' ? '' : '#';
    }
    return e($url);
}

/* ---------- Settings ---------- */
function defaults(): array
{
    return [
        // Liên hệ
        'phone' => '0978271605',
        'zalo' => '0978271605',
        'facebook' => 'https://www.facebook.com/share/1DJ3RGFZgV/',
        'youtube' => '',
        'dealer_hotline' => '093 55 66166 / 19004528',
        'tech_hotline' => '0912 1221 93',
        'email' => 'cskh@hanoiauto.com.vn',
        'address' => 'Tổ 15, Phường Xuân Phương, Thành phố Hà Nội',
        'showroom_name' => 'Đại lý Mitsubishi Cầu Diễn – Hanoi Auto',
        'maps_url' => 'https://www.google.com/maps/place/%C4%90%E1%BA%A1i+L%C3%BD+Mitsubishi+C%E1%BA%A7u+Di%E1%BB%85n+-+Hanoi+Auto/@21.047696,105.744707,17z/data=!4m6!3m5!1s0x3134559a5c4b5c25:0x75290a4e2cb31ec1!8m2!3d21.0476963!4d105.7447074!16s%2Fg%2F11ghnyr1jf',
        'lat' => '21.0476963',
        'lng' => '105.7447074',
        // Tư vấn viên
        'consultant_name' => 'Nguyễn Tiến Thành',
        'consultant_title' => 'Chuyên viên tư vấn bán hàng Mitsubishi',
        'consultant_photo' => 'assets/img/tu-van-vien.webp',
        'consultant_bio' => 'Tư vấn bán hàng tại Công ty Cổ phần Ô tô Hà Nội – đại lý xe Mitsubishi. Với 4 năm gắn bó cùng thương hiệu và 388 xe đã bàn giao đến khách hàng, tôi hiểu rằng mua ô tô là một quyết định lớn của mỗi gia đình – vì vậy tôi luôn đặt sự minh bạch lên đầu.',
        'consultant_bio2' => 'Vì vậy, tôi luôn đặt sự minh bạch lên đầu: báo giá lăn bánh rõ từng khoản, tư vấn đúng nhu cầu và tài chính, hỗ trợ trọn vẹn từ lái thử, hồ sơ trả góp, đăng ký xe đến chăm sóc sau bán hàng.',
        'author_intro' => "Tôi là Nguyễn Tiến Thành, chuyên viên tư vấn bán hàng tại Công ty Cổ phần Ô tô Hà Nội – đại lý xe Mitsubishi. Trong 4 năm làm nghề, tôi đã đồng hành và bàn giao 388 xe tới khách hàng, từ những khách hàng mua chiếc xe đầu tiên đến các gia đình đổi xe rộng rãi hơn cho cả nhà.\nCác bài viết trên trang này đều xuất phát từ những câu hỏi khách hàng thường đặt ra cho tôi khi mua xe: tính giá lăn bánh, chọn phiên bản, vay trả góp, thủ tục đăng ký và chăm sóc xe sau khi nhận. Tôi cố gắng viết rõ ràng, có số liệu và cập nhật khi chính sách hoặc giá thay đổi.",
        'stat_years' => '4 năm',
        'stat_years_label' => 'kinh nghiệm tư vấn xe Mitsubishi',
        'stat_customers' => '388 xe',
        'stat_customers_label' => 'đã bàn giao đến khách hàng',
        'commitments' => "Báo giá lăn bánh chi tiết, minh bạch từng khoản\nHỗ trợ hồ sơ trả góp, duyệt vay nhanh\nLái thử tận nơi, giao xe tận nhà\nĐồng hành chăm sóc sau bán hàng",
        // Công ty
        'company_name' => 'CÔNG TY CỔ PHẦN Ô TÔ HÀ NỘI',
        'company_short' => 'Công ty Cổ phần Ô tô Hà Nội',
        'company_tax' => '0101771055',
        'company_lines' => "Giấy CNĐKKD: 0101771055 – Đăng ký lần đầu ngày 08/09/2005 – Đăng ký thay đổi lần thứ 8, ngày 18/06/2020\nCơ quan cấp: Phòng đăng ký kinh doanh Sở Kế hoạch và Đầu tư TP Hà Nội\nĐịa chỉ: Tổ 15, Phường Xuân Phương, Thành phố Hà Nội\nChịu trách nhiệm nội dung: Nguyễn Tiến Thành",
        'company_intro' => 'Công ty Cổ phần Ô tô Hà Nội (Hanoi Auto) – Đại lý Mitsubishi Cầu Diễn, đăng ký kinh doanh từ năm 2005, có showroom trưng bày và xưởng dịch vụ tại Tổ 15, Phường Xuân Phương, Hà Nội.',
        'updated_date' => '20/10/2026',
        'form_image' => 'assets/img/xforce-form.webp',
        'home_cars_per_page' => '6',
        // Phí lăn bánh
        'fee_rate_hn' => '12',
        'fee_rate_hcm' => '10',
        'fee_rate_other' => '10',
        'fee_pickup_pct' => '60',
        'plate_hn' => '20000000',
        'plate_hcm' => '20000000',
        'plate_other' => '1000000',
        'road_fee_year' => '1560000',
        'tnds_5' => '480700',
        'tnds_7' => '873400',
        'tnds_pickup' => '480700',
        'inspection_fee' => '90000',
        // Trả góp
        'loan_downs' => '20,30,40,50',
        'loan_years' => '3,5,7,8',
        'loan_rates' => '7,8,9,10',
        'maf_text' => 'Gói vay qua công ty tài chính liên kết với hãng. Tỷ lệ trả trước, kỳ hạn và lãi suất thay đổi theo từng thời kỳ – liên hệ để được báo gói đang áp dụng.',
        // Nhận khách
        'lead_email' => '',
        'lead_webhook' => '',
        // SEO
        'site_name' => 'Tư vấn Mitsubishi – Nguyễn Tiến Thành',
        'site_url' => '',
        'home_title' => 'Mitsubishi Hà Nội – Báo giá lăn bánh & ưu đãi 2026 | Tiến Thành',
        'home_desc' => 'Tư vấn mua xe Mitsubishi tại Hà Nội: Xforce, Xpander, Destinator, Triton, Pajero Sport. Báo giá lăn bánh, hỗ trợ trả góp, lái thử tận nơi. Gọi 0978 271 605.',
        'gtm_id' => '',
        'head_code' => '',
    ];
}

function opt(string $key): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (rows('SELECT k, v FROM settings') as $r) {
            $cache[$r['k']] = $r['v'];
        }
    }
    if (isset($cache[$key]) && $cache[$key] !== '') {
        return (string) $cache[$key];
    }
    return (string) (defaults()[$key] ?? '');
}

function opt_save(string $key, string $value): void
{
    q('INSERT INTO settings (k, v) VALUES (?, ?) ON CONFLICT(k) DO UPDATE SET v = excluded.v', [$key, $value]);
}

/* ---------- URLs ---------- */
function url(string $path = ''): string { return BASE . '/' . ltrim($path, '/'); }

function full_url(string $path = ''): string
{
    $site = rtrim(opt('site_url'), '/');
    if ($site === '') {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $site = ($https ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE;
    }
    return $site . '/' . ltrim($path, '/');
}

/** Media path (assets/… or uploads/…) or absolute URL → usable URL (auto-serves .webp if exists). */
function media(?string $path): string
{
    $path = (string) $path;
    if ($path === '' || preg_match('~^https?://~', $path)) {
        return $path;
    }
    if (preg_match('/\.(jpe?g|png)$/i', $path)) {
        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
        if (is_file(ROOT . '/' . ltrim($webp, '/'))) {
            return url($webp);
        }
    }
    return url($path);
}
function media_full(?string $path): string
{
    $path = (string) $path;
    if ($path === '' || preg_match('~^https?://~', $path)) {
        return $path;
    }
    if (preg_match('/\.(jpe?g|png)$/i', $path)) {
        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
        if (is_file(ROOT . '/' . ltrim($webp, '/'))) {
            return full_url($webp);
        }
    }
    return full_url($path);
}

/** Built-in pages: key => [slug, template, menu label]. */
function page_map(): array
{
    return [
        'home'       => ['', 'home'],
        'bang-gia'   => ['bang-gia-xe-mitsubishi/', 'bang-gia'],
        'khuyen-mai' => ['khuyen-mai/', 'khuyen-mai'],
        'du-tinh'    => ['tinh-gia-lan-banh/', 'du-tinh'],
        'tra-gop'    => ['mua-xe-tra-gop/', 'tra-gop'],
        'lai-thu'    => ['dang-ky-lai-thu/', 'lai-thu'],
        'dich-vu'    => ['dich-vu/', 'dich-vu'],
        'tin-tuc'    => ['tin-tuc/', 'tin-tuc'],
        'lien-he'    => ['lien-he/', 'lien-he'],
        'chinh-sach' => ['chinh-sach-bao-mat/', 'chinh-sach'],
        'tac-gia'    => ['tac-gia/nguyen-tien-thanh/', 'tac-gia'],
    ];
}
function page_url(string $key, string $hash = ''): string { return url(page_map()[$key][0] ?? '') . $hash; }
function car_url(array $c): string { return url($c['slug'] . '/'); }
function post_url(array $p): string { return url('tin-tuc/' . $p['slug'] . '/'); }

function page_data(string $key): array
{
    $p = row('SELECT * FROM pages WHERE k = ?', [$key]);
    return $p ?: ['k' => $key, 'title' => '', 'subtitle' => '', 'image' => '', 'content' => '', 'meta_desc' => '', 'updated_at' => ''];
}

/* ---------- Formatting ---------- */
function lines($text): array { return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $text)))); }
function money($n): string { return number_format((float) preg_replace('/[^\d.]/', '', (string) $n), 0, ',', '.'); }
function tel($phone): string { return preg_replace('/[^\d+]/', '', (string) $phone); }
function price_short($n): string
{
    $n = (int) $n;
    if ($n >= 1000000000) {
        return rtrim(rtrim(number_format($n / 1e9, 3, ',', '.'), '0'), ',') . ' tỷ';
    }
    return number_format($n / 1e6, 0, ',', '.') . ' triệu';
}
function vn_date(?string $iso, string $fmt = 'd/m/Y'): string { return $iso ? date($fmt, strtotime($iso)) : ''; }
function updated_iso(): string { $d = DateTime::createFromFormat('d/m/Y', opt('updated_date')); return $d ? $d->format('Y-m-d') : ''; }
function upper(string $s): string { return mb_strtoupper($s, 'UTF-8'); }

function slugify(string $s): string
{
    $s = mb_strtolower(trim($s), 'UTF-8');
    $map = ['à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ' => 'a', 'è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ' => 'e', 'ì|í|ị|ỉ|ĩ' => 'i', 'ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ' => 'o', 'ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ' => 'u', 'ỳ|ý|ỵ|ỷ|ỹ' => 'y', 'đ' => 'd'];
    foreach ($map as $from => $to) {
        $s = preg_replace('/' . $from . '/u', $to, $s);
    }
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

/* ---------- Cars ---------- */
function cars(bool $all = false): array
{
    static $cache = [];
    $k = $all ? 'all' : 'active';
    if (!isset($cache[$k])) {
        $cache[$k] = rows('SELECT * FROM cars ' . ($all ? '' : 'WHERE active = 1 ') . 'ORDER BY ord, name');
    }
    return $cache[$k];
}

function car_versions(array $c): array
{
    $out = [];
    foreach (lines($c['versions'] ?? '') as $line) {
        $p = array_map('trim', explode('|', $line));
        $out[] = ['name' => $p[0], 'price' => isset($p[1]) ? preg_replace('/\D/', '', $p[1]) : ''];
    }
    return $out;
}

function car_price(array $c): int
{
    $prices = array_filter(array_column(car_versions($c), 'price'));
    return $prices ? (int) min($prices) : 0;
}

function car_specs(array $c): array
{
    $s = [];
    $v = array_column(car_versions($c), 'name');
    if ($v) { $s['Phiên bản'] = implode(', ', $v); }
    if ($c['engine']) { $s['Động cơ'] = $c['engine']; }
    if ($c['seats']) { $s['Số chỗ ngồi'] = $c['seats']; }
    $s['Bảo hành'] = $c['warranty'] ?: 'Chính hãng – theo Sổ bảo hành';
    if ($c['origin']) { $s['Xuất xứ'] = $c['origin']; }
    foreach (lines($c['specs']) as $line) {
        $p = array_map('trim', explode('|', $line, 2));
        if (count($p) === 2) { $s[$p[0]] = $p[1]; }
    }
    return $s;
}

function car_image(array $c, string $class = ''): string
{
    if (!empty($c['image'])) {
        return '<img src="' . eu(media($c['image'])) . '" alt="Mitsubishi ' . e($c['name']) . '" loading="lazy" decoding="async" class="' . e($class) . '">';
    }
    return '<span class="car-ph ' . e($class) . '"><span>MITSUBISHI</span><strong>' . e(upper($c['name'])) . '</strong></span>';
}

function cars_js(): array
{
    return array_map(fn($c) => [
        'id' => (int) $c['id'], 'name' => $c['name'], 'price' => car_price($c), 'kind' => $c['fee_kind'] ?: '5', 'url' => car_url($c),
    ], cars());
}

/* ---------- Misc ---------- */
function updated_line(bool $who = true): string
{
    $out = 'Cập nhật lần cuối: <time datetime="' . e(updated_iso()) . '">' . e(opt('updated_date')) . '</time>';
    if ($who) {
        $out .= ' · Người cập nhật: <a href="' . e(page_url('tac-gia')) . '">' . e(opt('consultant_name')) . '</a>';
    }
    return $out;
}

function icon(string $name, int $size = 20, string $color = 'currentColor'): string
{
    $p = [
        'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
        'mobile' => '<rect x="6" y="2" width="12" height="20" rx="2"/><path d="M11 18h2"/>',
        'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
        'menu' => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'down' => '<path d="M6 9l6 6 6-6"/>',
        'left' => '<path d="M15 18l-6-6 6-6"/>',
        'right' => '<path d="M9 6l6 6-6 6"/>',
        'close' => '<path d="M6 6l12 12M18 6L6 18"/>',
        'pin' => '<path d="M12 22s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/>',
    ];
    if (!isset($p[$name])) {
        return '';
    }
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="' . e($color) . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p[$name] . '</svg>';
}

/** Hero band at the top of inner pages. */
function page_hero(string $title, string $desc = '', array $a = []): void
{
    $a += ['bg' => 'assets/img/bg-tintuc.webp', 'align' => 'left', 'pos' => 'center 58%'];
    $dir = $a['align'] === 'right' ? '270deg' : '90deg';
    $grad = $a['align'] === 'center' ? 'linear-gradient(rgba(0,0,0,.66),rgba(0,0,0,.66))' : "linear-gradient($dir,rgba(0,0,0,.78) 0%,rgba(0,0,0,.5) 45%,rgba(0,0,0,.12) 85%)";
    echo '<section class="hero hero--' . e($a['align']) . '" style="background-image:' . $grad . ',url(\'' . eu(media($a['bg'])) . '\');background-position:' . e($a['pos']) . '">';
    echo '<div class="container hero__inner"><h1 class="hero__title">' . e($title) . '</h1>';
    if ($desc !== '') {
        echo '<p class="hero__desc">' . $desc . '</p>';
    }
    echo '</div></section>';
}

/** Hero from a page row: uses its title, subtitle (may contain {phone}) and background. */
function page_hero_from(array $p, string $fallbackBg, array $a = []): void
{
    $sub = e($p['subtitle'] ?? '');
    $sub = str_replace('{phone}', '<a href="tel:' . e(tel(opt('phone'))) . '">' . e(opt('phone')) . '</a>', $sub);
    $sub = str_replace('{tech}', '<a href="tel:' . e(tel(opt('tech_hotline'))) . '">' . e(opt('tech_hotline')) . '</a>', $sub);
    page_hero($p['title'], $sub, $a + ['bg' => $p['image'] ?: $fallbackBg]);
}

function partial(string $name, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    include ROOT . '/views/partials/' . $name . '.php';
}

/** Admin-written HTML: allow normal formatting, strip scripts / event handlers. */
function clean_html(string $html): string
{
    $html = preg_replace('~<(script|style|iframe(?![^>]*(youtube\.com|youtube-nocookie\.com|google\.com/maps)))[^>]*>.*?</\1>~is', '', $html);
    $html = preg_replace('~<(script|style)[^>]*>~i', '', $html);
    $html = preg_replace('~\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)~i', '', $html);
    $html = preg_replace('~(href|src)\s*=\s*(["\'])\s*(javascript|vbscript|data):[^"\']*\2~i', '$1="#"', $html);
    return $html;
}

/* ---------- Session / CSRF ---------- */
function session_start_safe(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('ttadmin');
        session_set_cookie_params(['lifetime' => 0, 'path' => BASE . '/', 'httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
        session_start();
    }
}
function csrf(): string
{
    session_start_safe();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf()) . '">'; }
function csrf_ok(): bool { session_start_safe(); return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']); }

/** Render admin HTML content: clean it and make relative media / root links work in any install folder. */
function render_content(?string $html): string
{
    $html = clean_html((string) $html);
    $html = preg_replace_callback('~(src|href)="(?:\./|/)?((?:assets|uploads)/[^"]+)"~', function ($m) {
        $attr = $m[1];
        $path = $m[2];
        if (preg_match('/\.(jpe?g|png)$/i', $path)) {
            $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
            if (is_file(ROOT . '/' . $webp)) {
                $path = $webp;
            }
        }
        return $attr . '="' . BASE . '/' . $path . '"';
    }, $html);
    if (BASE !== '') {
        $html = preg_replace('~href="/(?!/)~', 'href="' . BASE . '/', $html);
    }
    return $html;
}

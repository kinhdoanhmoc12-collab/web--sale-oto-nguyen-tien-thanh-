<?php
/**
 * Trang quản trị web tư vấn Mitsubishi.
 * Mở tại: tenmien.vn/admin/
 */

require __DIR__ . '/lib.php';

$p = get_('p', 'dashboard');
$isPost = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';

/* =========================================================
 * Đăng nhập / tạo mật khẩu lần đầu
 * ======================================================= */
/* Quên mật khẩu: tạo file data/quen-mat-khau.txt bằng File Manager của hosting rồi mở /admin. */
if (is_file(DATA_DIR . '/quen-mat-khau.txt')) {
    opt_save('admin_hash', '');
    @unlink(DATA_DIR . '/quen-mat-khau.txt');
    header('Location: ' . admin_url());
    exit;
}

if (opt('admin_hash') === '') {
    $err = '';
    if ($isPost) {
        require_post();
        $pw = post('password');
        if (mb_strlen($pw) < 8) {
            $err = 'Mật khẩu cần ít nhất 8 ký tự.';
        } elseif ($pw !== post('password2')) {
            $err = 'Hai lần nhập mật khẩu chưa khớp.';
        } else {
            opt_save('admin_hash', password_hash($pw, PASSWORD_DEFAULT));
            session_regenerate_id(true);
            $_SESSION['admin'] = 1;
            $_SESSION['admin_hash'] = substr(val("SELECT v FROM settings WHERE k = 'admin_hash'"), -20);
            go('dashboard', [], 'Đã tạo mật khẩu. Chào mừng anh đến trang quản trị!');
        }
    }
    layout_start('Tạo mật khẩu');
    ?>
    <form method="post" class="auth">
        <img src="<?= e(url('assets/img/logo.svg')) ?>" alt="" width="64" height="64">
        <h1>Tạo mật khẩu quản trị</h1>
        <p>Lần đầu vào trang quản trị. Anh đặt mật khẩu (ít nhất 8 ký tự) và ghi nhớ lại nhé.</p>
        <?php if ($err) : ?><div class="error"><?= e($err) ?></div><?php endif; ?>
        <?= csrf_field() ?>
        <label>Mật khẩu<input type="password" name="password" required minlength="8" autocomplete="new-password"></label>
        <label>Nhập lại mật khẩu<input type="password" name="password2" required minlength="8" autocomplete="new-password"></label>
        <button class="btn btn--primary btn--block">Tạo mật khẩu &amp; vào quản trị</button>
    </form>
    <?php
    layout_end();
    exit;
}

if ($p === 'login' || !logged_in()) {
    $err = '';
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($isPost && $p !== 'logout') {
        require_post();
        q('DELETE FROM login_attempts WHERE at < ?', [time() - 900]);
        $fails = (int) val('SELECT COUNT(*) FROM login_attempts WHERE ip = ?', [$ip]);
        if ($fails >= 5) {
            $err = 'Sai mật khẩu quá nhiều lần. Vui lòng thử lại sau 15 phút.';
        } elseif (password_verify(post('password'), opt('admin_hash'))) {
            q('DELETE FROM login_attempts WHERE ip = ?', [$ip]);
            session_regenerate_id(true);
            $_SESSION['admin'] = 1;
            $_SESSION['admin_hash'] = substr(opt('admin_hash'), -20);
            go('dashboard');
        } else {
            q('INSERT INTO login_attempts (ip, at) VALUES (?, ?)', [$ip, time()]);
            $err = 'Mật khẩu chưa đúng.';
        }
    }
    layout_start('Đăng nhập');
    ?>
    <form method="post" action="<?= e(admin_url('login')) ?>" class="auth">
        <img src="<?= e(url('assets/img/logo.svg')) ?>" alt="" width="64" height="64">
        <h1>Đăng nhập quản trị</h1>
        <?php if ($err) : ?><div class="error"><?= e($err) ?></div><?php endif; ?>
        <?= csrf_field() ?>
        <label>Mật khẩu<input type="password" name="password" required autofocus autocomplete="current-password"></label>
        <button class="btn btn--primary btn--block">Đăng nhập</button>
        <p class="hint">Quên mật khẩu: vào File Manager của hosting, tạo file trống tên <code>quen-mat-khau.txt</code> trong thư mục <code>data</code>, rồi mở lại trang này để đặt mật khẩu mới.</p>
    </form>
    <?php
    layout_end();
    exit;
}

if ($p === 'logout') {
    require_post();
    $_SESSION = [];
    session_destroy();
    header('Location: ' . admin_url('login'));
    exit;
}

/* =========================================================
 * Tải ảnh từ trình soạn thảo (JSON)
 * ======================================================= */
if ($p === 'upload') {
    header('Content-Type: application/json; charset=utf-8');
    if (!$isPost || !hash_equals($_SESSION['csrf'] ?? '', (string) ($_SERVER['HTTP_X_CSRF'] ?? $_POST['csrf'] ?? ''))) {
        http_response_code(400);
        echo json_encode(['error' => 'Phiên hết hạn, tải lại trang.']);
        exit;
    }
    try {
        $path = save_upload('file', 1600);
        echo json_encode(['location' => url($path)]);
    } catch (Throwable $ex) {
        http_response_code(422);
        echo json_encode(['error' => $ex->getMessage()]);
    }
    exit;
}

/* =========================================================
 * Xử lý lưu dữ liệu (POST)
 * ======================================================= */
try {
    if ($isPost) {
        require_post();
        $act = post('act');

        /* ---- Khách hàng ---- */
        if ($p === 'leads' && $act === 'status') {
            q('UPDATE leads SET status = ? WHERE id = ?', [post('status') === 'called' ? 'called' : 'new', (int) post('id')]);
            go('leads', array_filter(['f' => get_('f'), 'trang' => get_('trang'), 's' => get_('s')]));
        }
        if ($p === 'lead' && $act === 'save') {
            q('UPDATE leads SET status = ?, note = ? WHERE id = ?', [post('status') === 'called' ? 'called' : 'new', post('note'), (int) post('id')]);
            go('leads', [], 'Đã lưu thông tin khách.');
        }
        if ($p === 'lead' && $act === 'delete') {
            q('DELETE FROM leads WHERE id = ?', [(int) post('id')]);
            go('leads', [], 'Đã xóa khách khỏi danh sách.');
        }

        /* ---- Dòng xe ---- */
        if ($p === 'car' && $act === 'save') {
            $id = (int) post('id');
            $name = post('name');
            if ($name === '') {
                throw new RuntimeException('Chưa nhập tên dòng xe.');
            }
            $slug = slugify(post('slug') ?: 'mitsubishi-' . $name);
            if (in_array($slug, ['admin', 'api', 'assets', 'uploads', 'data', 'inc', 'views', 'tin-tuc', 'tac-gia', 'sitemap-xml'], true) || array_filter(page_map(), fn($m) => trim($m[0], '/') === $slug)) {
                $slug .= '-xe';
            }
            if ((int) val('SELECT COUNT(*) FROM cars WHERE slug = ? AND id <> ?', [$slug, $id])) {
                throw new RuntimeException('Đường dẫn "' . $slug . '" đã có xe khác dùng.');
            }
            $data = ['slug' => $slug, 'name' => $name, 'ord' => (int) post('ord'), 'active' => post('active') ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')];
            foreach (['segment', 'versions', 'seats', 'engine', 'origin', 'warranty', 'specs', 'promos', 'feat_exterior', 'feat_interior', 'feat_drive', 'feat_safety', 'excerpt'] as $k) {
                $data[$k] = post($k);
            }
            $data['fee_kind'] = in_array(post('fee_kind'), ['5', '7', 'pickup'], true) ? post('fee_kind') : '5';
            $data['content'] = clean_html(post('content'));
            $data['image'] = image_value('image', 1600);
            $id = db_save('cars', $data, $id ?: null);
            go('car', ['id' => $id], 'Đã lưu xe ' . $name . '.');
        }
        if ($p === 'car' && $act === 'delete') {
            q('DELETE FROM cars WHERE id = ?', [(int) post('id')]);
            go('cars', [], 'Đã xóa dòng xe.');
        }

        /* ---- Bài viết ---- */
        if ($p === 'post' && $act === 'save') {
            $id = (int) post('id');
            $title = post('title');
            if ($title === '') {
                throw new RuntimeException('Chưa nhập tiêu đề bài viết.');
            }
            $slug = slugify(post('slug') ?: $title);
            $base = $slug;
            $i = 2;
            while ((int) val('SELECT COUNT(*) FROM posts WHERE slug = ? AND id <> ?', [$slug, $id])) {
                $slug = $base . '-' . $i++;
            }
            $created = post('created_at') ? date('Y-m-d H:i:s', strtotime(post('created_at'))) : date('Y-m-d H:i:s');
            $data = [
                'slug' => $slug, 'title' => $title, 'excerpt' => post('excerpt'), 'content' => clean_html(post('content')),
                'category' => post('category'), 'status' => post('status') === 'draft' ? 'draft' : 'publish',
                'created_at' => $created, 'updated_at' => date('Y-m-d H:i:s'), 'image' => image_value('image', 1600),
            ];
            $id = db_save('posts', $data, $id ?: null);
            go('post', ['id' => $id], $data['status'] === 'publish' ? 'Đã đăng bài viết.' : 'Đã lưu bản nháp.');
        }
        if ($p === 'post' && $act === 'delete') {
            q('DELETE FROM posts WHERE id = ?', [(int) post('id')]);
            go('posts', [], 'Đã xóa bài viết.');
        }

        /* ---- Banner ---- */
        if ($p === 'banner' && $act === 'save') {
            $id = (int) post('id');
            $img = image_value('image', 1920);
            if ($img === '') {
                throw new RuntimeException('Banner cần có ảnh.');
            }
            $id = db_save('banners', ['image' => $img, 'link' => post('link'), 'alt' => post('alt'), 'ord' => (int) post('ord'), 'active' => post('active') ? 1 : 0], $id ?: null);
            go('banners', [], 'Đã lưu banner.');
        }
        if ($p === 'banner' && $act === 'delete') {
            q('DELETE FROM banners WHERE id = ?', [(int) post('id')]);
            go('banners', [], 'Đã xóa banner.');
        }

        /* ---- Ảnh giao xe ---- */
        if ($p === 'delivery' && $act === 'save') {
            $id = (int) post('id');
            $img = image_value('image', 1200);
            if ($img === '') {
                throw new RuntimeException('Cần chọn ảnh giao xe.');
            }
            $id = db_save('deliveries', [
                'image' => $img, 'car' => post('car'), 'cust_type' => post('cust_type'), 'msg_by' => post('msg_by') === 'customer' ? 'customer' : 'consultant',
                'message' => post('message'), 'cust_name' => post('cust_name'), 'ord' => (int) post('ord'), 'active' => post('active') ? 1 : 0,
            ], $id ?: null);
            go('deliveries', [], 'Đã lưu ảnh giao xe.');
        }
        if ($p === 'delivery' && $act === 'delete') {
            q('DELETE FROM deliveries WHERE id = ?', [(int) post('id')]);
            go('deliveries', [], 'Đã xóa ảnh.');
        }

        /* ---- Trang ---- */
        if ($p === 'page' && $act === 'save') {
            $k = post('k');
            if (!isset(page_map()[$k])) {
                throw new RuntimeException('Trang không tồn tại.');
            }
            q('UPDATE pages SET title = ?, subtitle = ?, image = ?, content = ?, meta_desc = ?, updated_at = ? WHERE k = ?', [
                post('title'), post('subtitle'), image_value('image', 1920), clean_html(post('content')), post('meta_desc'), date('Y-m-d H:i:s'), $k,
            ]);
            go('page', ['k' => $k], 'Đã lưu trang.');
        }

        /* ---- Cài đặt ---- */
        if ($p === 'settings' && $act === 'save') {
            foreach (settings_schema() as $group) {
                foreach ($group['fields'] as $k => $f) {
                    if ($f[1] === 'image') {
                        opt_save($k, image_value($k, $k === 'consultant_photo' ? 900 : 1600));
                    } elseif ($f[1] === 'number') {
                        opt_save($k, preg_replace('/[^\d.]/', '', str_replace(',', '.', post($k))));
                    } else {
                        opt_save($k, post($k));
                    }
                }
            }
            go('settings', ['tab' => get_('tab')], 'Đã lưu cài đặt.');
        }

        /* ---- Mật khẩu ---- */
        if ($p === 'account' && $act === 'password') {
            if (!password_verify(post('old'), opt('admin_hash'))) {
                throw new RuntimeException('Mật khẩu hiện tại chưa đúng.');
            }
            if (mb_strlen(post('new')) < 8 || post('new') !== post('new2')) {
                throw new RuntimeException('Mật khẩu mới cần ít nhất 8 ký tự và hai lần nhập phải khớp.');
            }
            opt_save('admin_hash', password_hash(post('new'), PASSWORD_DEFAULT));
            $_SESSION['admin_hash'] = substr(val("SELECT v FROM settings WHERE k = 'admin_hash'"), -20);
            go('account', [], 'Đã đổi mật khẩu.');
        }
    }
} catch (Throwable $ex) {
    $_SESSION['flash'] = '⚠ ' . $ex->getMessage();
    $back = $_SERVER['HTTP_REFERER'] ?? admin_url($p);
    header('Location: ' . $back);
    exit;
}

/* ---- Tải file (GET, có kiểm tra token) ---- */
if ($p === 'export' && hash_equals(csrf(), get_('t'))) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=khach-hang-' . date('Y-m-d') . '.csv');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Thời gian', 'Họ tên', 'Điện thoại', 'Xe quan tâm', 'Khu vực', 'Thanh toán', 'Form', 'Thông tin thêm', 'Trạng thái', 'Ghi chú', 'Trang']);
    foreach (rows('SELECT * FROM leads ORDER BY created_at DESC') as $l) {
        fputcsv($out, [vn_date($l['created_at'], 'd/m/Y H:i'), $l['name'], '="' . $l['phone'] . '"', $l['car'], $l['region'], $l['pay'], source_label($l['source']), $l['extra'], $l['status'] === 'called' ? 'Đã gọi' : 'Chưa gọi', $l['note'], $l['page']]);
    }
    exit;
}
if ($p === 'backup' && hash_equals(csrf(), get_('t'))) {
    db()->exec('PRAGMA wal_checkpoint(TRUNCATE)');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=sao-luu-web-' . date('Y-m-d') . '.db');
    readfile(DATA_DIR . '/site.db');
    exit;
}

/* =========================================================
 * Các màn hình
 * ======================================================= */
function source_label(string $s): string
{
    return ['trang-chu' => 'Trang chủ', 'trang-xe' => 'Trang xe', 'lai-thu' => 'Lái thử', 'dat-lich-dich-vu' => 'Đặt lịch dịch vụ'][$s] ?? $s;
}

function settings_schema(): array
{
    return [
        'contact' => ['title' => 'Liên hệ', 'fields' => [
            'phone' => ['Số điện thoại tư vấn (hotline, nút gọi)', 'text'],
            'zalo' => ['Số Zalo', 'text'],
            'facebook' => ['Link fanpage Facebook', 'url'],
            'youtube' => ['Link kênh YouTube (để trống nếu chưa có)', 'url'],
            'address' => ['Địa chỉ showroom', 'text'],
            'showroom_name' => ['Tên showroom', 'text'],
            'maps_url' => ['Link Google Maps', 'url'],
            'maps_embed' => ['Link nhúng bản đồ Google Maps (iframe embed)', 'url'],
            'lat' => ['Vĩ độ (bản đồ)', 'text'],
            'lng' => ['Kinh độ (bản đồ)', 'text'],
            'dealer_hotline' => ['Hotline đại lý (chân trang)', 'text'],
            'tech_hotline' => ['Hotline hỗ trợ kỹ thuật', 'text'],
            'email' => ['Email chăm sóc khách hàng', 'email'],
        ]],
        'consultant' => ['title' => 'Tư vấn viên', 'fields' => [
            'consultant_name' => ['Họ tên', 'text'],
            'consultant_title' => ['Chức danh', 'text'],
            'consultant_photo' => ['Ảnh chân dung (vuông, ~800 × 800 px)', 'image'],
            'consultant_bio' => ['Đoạn giới thiệu (trang chủ)', 'textarea'],
            'consultant_bio2' => ['Đoạn giới thiệu thêm (trang Liên hệ)', 'textarea'],
            'stat_years' => ['Ô số liệu 1 – con số', 'text'],
            'stat_years_label' => ['Ô số liệu 1 – chú thích', 'text'],
            'stat_customers' => ['Ô số liệu 2 – con số', 'text'],
            'stat_customers_label' => ['Ô số liệu 2 – chú thích', 'text'],
            'commitments' => ['Cam kết (mỗi dòng một ý)', 'textarea'],
            'author_intro' => ['Giới thiệu ở trang Tác giả (mỗi dòng một đoạn)', 'textarea'],
        ]],
        'company' => ['title' => 'Công ty & chân trang', 'fields' => [
            'company_name' => ['Tên công ty (chữ in hoa ở chân trang)', 'text'],
            'company_short' => ['Tên công ty (viết thường)', 'text'],
            'company_tax' => ['Mã số thuế / ĐKKD', 'text'],
            'company_lines' => ['Thông tin pháp lý ở chân trang (mỗi dòng một ý)', 'textarea'],
            'company_intro' => ['Giới thiệu công ty (trang Liên hệ)', 'textarea'],
            'updated_date' => ['Ngày cập nhật giá hiển thị (dd/mm/yyyy)', 'text'],
            'form_image' => ['Ảnh cạnh form đăng ký ở trang chủ', 'image'],
            'home_cars_per_page' => ['Số xe mỗi trang ở mục "Các dòng xe" trang chủ', 'number'],
        ]],
        'fees' => ['title' => 'Phí lăn bánh', 'note' => 'Dùng cho máy tính "Dự tính chi phí lăn bánh". Đơn vị: % cho lệ phí trước bạ, đồng cho các khoản còn lại.', 'fields' => [
            'fee_rate_hn' => ['Lệ phí trước bạ Hà Nội (%)', 'number'],
            'fee_rate_hcm' => ['Lệ phí trước bạ TP.HCM (%)', 'number'],
            'fee_rate_other' => ['Lệ phí trước bạ tỉnh khác (%)', 'number'],
            'fee_pickup_pct' => ['Xe bán tải tính bằng bao nhiêu % mức xe con', 'number'],
            'plate_hn' => ['Biển số Hà Nội (đ)', 'number'],
            'plate_hcm' => ['Biển số TP.HCM (đ)', 'number'],
            'plate_other' => ['Biển số tỉnh khác (đ)', 'number'],
            'road_fee_year' => ['Phí đường bộ 12 tháng (đ)', 'number'],
            'tnds_5' => ['Bảo hiểm TNDS xe dưới 6 chỗ (đ)', 'number'],
            'tnds_7' => ['Bảo hiểm TNDS xe 6–9 chỗ (đ)', 'number'],
            'tnds_pickup' => ['Bảo hiểm TNDS xe bán tải (đ)', 'number'],
            'inspection_fee' => ['Giấy chứng nhận đăng kiểm (đ)', 'number'],
        ]],
        'loan' => ['title' => 'Trả góp', 'fields' => [
            'loan_downs' => ['Các mức trả trước (%), cách nhau dấu phẩy', 'text'],
            'loan_years' => ['Các thời hạn vay (năm), cách nhau dấu phẩy', 'text'],
            'loan_rates' => ['Các mức lãi suất (%/năm), cách nhau dấu phẩy', 'text'],
            'maf_text' => ['Mô tả chương trình trả góp MAF', 'textarea'],
        ]],
        'leads' => ['title' => 'Nhận khách & SEO', 'note' => 'Khách điền form luôn được lưu ở mục Khách hàng. Bên dưới là nơi nhận thông báo thêm và cài đặt Google.', 'fields' => [
            'lead_email' => ['Email nhận thông báo khi có khách (để trống = email CSKH)', 'email'],
            'lead_webhook' => ['Link Google Apps Script để ghi vào Google Sheets (tùy chọn)', 'url'],
            'site_name' => ['Tên web', 'text'],
            'site_url' => ['Địa chỉ web chính thức, ví dụ https://muaxemitsubishi.vn (để trống = tự nhận)', 'url'],
            'home_title' => ['Tiêu đề trang chủ trên Google', 'text'],
            'home_desc' => ['Mô tả trang chủ trên Google', 'textarea'],
            'gtm_id' => ['Mã Google Tag Manager (GTM-XXXXXXX)', 'text'],
            'head_code' => ['Mã chèn vào &lt;head&gt; (Facebook Pixel, xác minh Google…)', 'textarea'],
        ]],
    ];
}

$editor = false;

switch ($p) {
    /* ------------------------------------------------ Tổng quan */
    default:
    case 'dashboard':
        layout_start('Tổng quan', 'dashboard');
        $new = (int) val("SELECT COUNT(*) FROM leads WHERE status = 'new'");
        $today = (int) val('SELECT COUNT(*) FROM leads WHERE created_at >= ?', [date('Y-m-d 00:00:00')]);
        $week = (int) val('SELECT COUNT(*) FROM leads WHERE created_at >= ?', [date('Y-m-d H:i:s', time() - 7 * 86400)]);
        ?>
        <h1>Xin chào, <?= e(opt('consultant_name')) ?></h1>
        <div class="stats">
            <a class="stat stat--red" href="<?= e(admin_url('leads', ['f' => 'new'])) ?>"><b><?= $new ?></b><span>khách chưa gọi</span></a>
            <div class="stat"><b><?= $today ?></b><span>khách hôm nay</span></div>
            <div class="stat"><b><?= $week ?></b><span>khách 7 ngày qua</span></div>
            <div class="stat"><b><?= (int) val("SELECT COUNT(*) FROM posts WHERE status = 'publish'") ?></b><span>bài viết</span></div>
        </div>
        <div class="grid2">
            <section class="card">
                <h2>Khách mới nhất</h2>
                <?php $ls = rows('SELECT * FROM leads ORDER BY created_at DESC LIMIT 8'); ?>
                <?php if (!$ls) : ?><p class="muted">Chưa có khách nào. Khi khách điền form trên web, tên và số điện thoại sẽ hiện ở đây.</p><?php endif; ?>
                <ul class="leadmini">
                    <?php foreach ($ls as $l) : ?>
                        <li><a href="<?= e(admin_url('lead', ['id' => $l['id']])) ?>"><strong><?= e($l['name']) ?></strong> <?= $l['status'] === 'new' ? '<span class="tag tag--red">Chưa gọi</span>' : '<span class="tag tag--green">Đã gọi</span>' ?><br><small><?= e(vn_date($l['created_at'], 'H:i d/m')) ?> · <?= e($l['car'] ?: source_label($l['source'])) ?></small></a><a class="call" href="tel:<?= e($l['phone']) ?>">📞 <?= e($l['phone']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <a class="btn" href="<?= e(admin_url('leads')) ?>">Xem tất cả khách →</a>
            </section>
            <section class="card">
                <h2>Làm nhanh</h2>
                <div class="quick">
                    <a href="<?= e(admin_url('post', ['id' => 'new'])) ?>">📝 Viết bài mới</a>
                    <a href="<?= e(admin_url('cars')) ?>">🚗 Sửa giá xe</a>
                    <a href="<?= e(admin_url('deliveries')) ?>">🎉 Thêm ảnh giao xe</a>
                    <a href="<?= e(admin_url('banners')) ?>">🖼️ Đổi banner</a>
                    <a href="<?= e(admin_url('settings')) ?>">⚙️ Số điện thoại, phí lăn bánh</a>
                    <a href="<?= e(admin_url('pages')) ?>">📄 Sửa trang Dịch vụ, Chính sách</a>
                </div>
                <h2 style="margin-top:24px">Mẹo</h2>
                <ul class="tips">
                    <li>Mở trang này trên điện thoại để xem khách mới và bấm gọi ngay.</li>
                    <li>Đổi giá xe ở mục <b>Dòng xe & giá</b> – giá tự đổi ở mọi trang và máy tính lăn bánh, trả góp.</li>
                    <li>Mỗi tháng bấm <b>Sao lưu dữ liệu</b> ở mục Mật khẩu & sao lưu.</li>
                </ul>
            </section>
        </div>
        <?php
        break;

    /* ------------------------------------------------ Khách hàng */
    case 'leads':
        layout_start('Khách hàng', 'leads');
        $f = get_('f');
        $s = get_('s');
        $pg = max(1, (int) get_('trang', '1'));
        $per = 30;
        $where = '1=1';
        $args = [];
        if ($f === 'new' || $f === 'called') {
            $where .= ' AND status = :st';
            $args['st'] = $f;
        }
        if ($s !== '') {
            $where .= ' AND (name LIKE :s OR phone LIKE :s OR car LIKE :s OR region LIKE :s)';
            $args['s'] = '%' . $s . '%';
        }
        $total = (int) val("SELECT COUNT(*) FROM leads WHERE $where", $args);
        $list = rows("SELECT * FROM leads WHERE $where ORDER BY created_at DESC LIMIT $per OFFSET " . (($pg - 1) * $per), $args);
        ?>
        <div class="head"><h1>Khách hàng để lại thông tin</h1><a class="btn" href="<?= e(admin_url('export', ['t' => csrf()])) ?>">⬇ Tải Excel (CSV)</a></div>
        <form class="filters" method="get">
            <input type="hidden" name="p" value="leads">
            <div class="seg">
                <?php foreach (['' => 'Tất cả', 'new' => 'Chưa gọi', 'called' => 'Đã gọi'] as $k => $l) : ?>
                    <a class="<?= $f === $k ? 'is-on' : '' ?>" href="<?= e(admin_url('leads', array_filter(['f' => $k, 's' => $s]))) ?>"><?= $l ?></a>
                <?php endforeach; ?>
            </div>
            <input type="search" name="s" value="<?= e($s) ?>" placeholder="Tìm tên, số điện thoại, xe…">
            <?php if ($f) : ?><input type="hidden" name="f" value="<?= e($f) ?>"><?php endif; ?>
            <button class="btn">Tìm</button>
        </form>
        <?php if (!$list) : ?><div class="card"><p class="muted">Chưa có khách nào<?= $s || $f ? ' phù hợp' : '' ?>.</p></div><?php endif; ?>
        <div class="leads">
            <?php foreach ($list as $l) : ?>
                <div class="lead <?= $l['status'] === 'new' ? 'lead--new' : '' ?>">
                    <div class="lead__main">
                        <a class="lead__name" href="<?= e(admin_url('lead', ['id' => $l['id']])) ?>"><?= e($l['name']) ?></a>
                        <div class="lead__meta"><?= e(vn_date($l['created_at'], 'H:i d/m/Y')) ?> · <?= e(source_label($l['source'])) ?><?= $l['car'] ? ' · <b>' . e($l['car']) . '</b>' : '' ?><?= $l['region'] ? ' · ' . e($l['region']) : '' ?><?= $l['pay'] ? ' · ' . e($l['pay']) : '' ?></div>
                        <?php if ($l['extra']) : ?><div class="lead__extra"><?= nl2br(e($l['extra'])) ?></div><?php endif; ?>
                        <?php if ($l['note']) : ?><div class="lead__note">📝 <?= nl2br(e($l['note'])) ?></div><?php endif; ?>
                    </div>
                    <div class="lead__act">
                        <a class="btn btn--primary" href="tel:<?= e($l['phone']) ?>">📞 <?= e($l['phone']) ?></a>
                        <a class="btn btn--zalo" href="https://zalo.me/<?= e($l['phone']) ?>" target="_blank" rel="noopener">Zalo</a>
                        <form method="post" action="<?= e(admin_url('leads', array_filter(['f' => $f, 's' => $s, 'trang' => $pg > 1 ? $pg : null]))) ?>">
                            <?= csrf_field() ?><input type="hidden" name="act" value="status"><input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                            <?php if ($l['status'] === 'new') : ?>
                                <button class="btn btn--ok" name="status" value="called">✓ Đánh dấu đã gọi</button>
                            <?php else : ?>
                                <button class="btn btn--ghost" name="status" value="new">Đã gọi · bỏ đánh dấu</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php $pages = (int) ceil($total / $per); if ($pages > 1) : ?>
            <div class="pager"><?php for ($i = 1; $i <= $pages; $i++) : ?><a class="<?= $i === $pg ? 'is-on' : '' ?>" href="<?= e(admin_url('leads', array_filter(['f' => $f, 's' => $s, 'trang' => $i]))) ?>"><?= $i ?></a><?php endfor; ?></div>
        <?php endif; ?>
        <?php
        break;

    case 'lead':
        $l = row('SELECT * FROM leads WHERE id = ?', [(int) get_('id')]);
        if (!$l) {
            go('leads', [], 'Không tìm thấy khách.');
        }
        layout_start('Khách ' . $l['name'], 'leads');
        ?>
        <a class="back" href="<?= e(admin_url('leads')) ?>">← Danh sách khách</a>
        <h1><?= e($l['name']) ?></h1>
        <div class="grid2">
            <section class="card">
                <table class="kv">
                    <tr><th>Điện thoại</th><td><a href="tel:<?= e($l['phone']) ?>"><b><?= e($l['phone']) ?></b></a> &nbsp; <a class="btn btn--zalo btn--sm" href="https://zalo.me/<?= e($l['phone']) ?>" target="_blank" rel="noopener">Nhắn Zalo</a></td></tr>
                    <tr><th>Thời gian</th><td><?= e(vn_date($l['created_at'], 'H:i d/m/Y')) ?></td></tr>
                    <tr><th>Xe quan tâm</th><td><?= e($l['car']) ?></td></tr>
                    <tr><th>Khu vực lăn bánh</th><td><?= e($l['region']) ?></td></tr>
                    <tr><th>Thanh toán</th><td><?= e($l['pay']) ?></td></tr>
                    <tr><th>Gửi từ</th><td><?= e(source_label($l['source'])) ?></td></tr>
                    <tr><th>Thông tin thêm</th><td><?= nl2br(e($l['extra'])) ?></td></tr>
                    <tr><th>Trang</th><td><a href="<?= eu($l['page']) ?>" target="_blank" rel="noopener"><?= e($l['page']) ?></a></td></tr>
                </table>
            </section>
            <section class="card">
                <form method="post">
                    <?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                    <label class="check big"><input type="checkbox" name="status" value="called" <?= $l['status'] === 'called' ? 'checked' : '' ?>> Đã gọi tư vấn cho khách này</label>
                    <?= field('note', 'Ghi chú của anh', $l['note'], 'textarea', '', ['rows' => 6]) ?>
                    <button class="btn btn--primary">Lưu</button>
                </form>
                <form method="post" class="danger" onsubmit="return confirm('Xóa khách này khỏi danh sách?')">
                    <?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int) $l['id'] ?>">
                    <button class="btn btn--danger btn--sm">Xóa khách</button>
                </form>
            </section>
        </div>
        <?php
        break;

    /* ------------------------------------------------ Dòng xe */
    case 'cars':
        layout_start('Dòng xe', 'cars');
        ?>
        <div class="head"><h1>Dòng xe &amp; giá</h1><a class="btn btn--primary" href="<?= e(admin_url('car', ['id' => 'new'])) ?>">+ Thêm dòng xe</a></div>
        <p class="muted">Bấm vào xe để sửa giá, phiên bản, thông số, ảnh. Số “Thứ tự” nhỏ hiện trước.</p>
        <div class="table-wrap"><table class="tbl">
            <thead><tr><th>Ảnh</th><th>Dòng xe</th><th>Giá từ</th><th>Phiên bản</th><th>Thứ tự</th><th>Hiện</th></tr></thead>
            <tbody>
            <?php foreach (cars(true) as $c) : ?>
                <tr>
                    <td class="thumb"><?= $c['image'] ? '<img src="' . eu(media($c['image'])) . '" alt="">' : '<span class="noimg">Chưa có ảnh</span>' ?></td>
                    <td><a class="strong" href="<?= e(admin_url('car', ['id' => $c['id']])) ?>"><?= e($c['name']) ?></a><br><small class="muted">/<?= e($c['slug']) ?>/</small></td>
                    <td><?= car_price($c) ? e(money(car_price($c))) . ' đ' : '—' ?></td>
                    <td><?= e(implode(', ', array_column(car_versions($c), 'name'))) ?></td>
                    <td><?= (int) $c['ord'] ?></td>
                    <td><?= $c['active'] ? '✓' : '<span class="muted">Ẩn</span>' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php
        break;

    case 'car':
        $id = get_('id');
        $c = $id === 'new' ? ['id' => 0, 'slug' => '', 'name' => '', 'ord' => count(cars(true)) + 1, 'active' => 1, 'segment' => '', 'versions' => '', 'seats' => '', 'engine' => '', 'origin' => '', 'warranty' => '', 'specs' => '', 'fee_kind' => '5', 'promos' => '', 'feat_exterior' => '', 'feat_interior' => '', 'feat_drive' => '', 'feat_safety' => '', 'image' => '', 'content' => '', 'excerpt' => ''] : row('SELECT * FROM cars WHERE id = ?', [(int) $id]);
        if (!$c) {
            go('cars', [], 'Không tìm thấy xe.');
        }
        $editor = true;
        layout_start($c['id'] ? 'Sửa ' . $c['name'] : 'Thêm xe', 'cars');
        ?>
        <a class="back" href="<?= e(admin_url('cars')) ?>">← Danh sách xe</a>
        <div class="head"><h1><?= $c['id'] ? 'Sửa: ' . e($c['name']) : 'Thêm dòng xe' ?></h1><?php if ($c['id']) : ?><a class="btn" href="<?= e(car_url($c)) ?>" target="_blank">Xem trang xe ↗</a><?php endif; ?></div>
        <form method="post" enctype="multipart/form-data" class="form">
            <?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
            <section class="card"><h2>Giá &amp; phiên bản</h2><div class="fields">
                <?= field('name', 'Tên dòng xe', $c['name'], 'text', '', ['required' => true, 'placeholder' => 'Ví dụ: Xforce']) ?>
                <?= field('segment', 'Phân khúc (dòng chữ đỏ nhỏ)', $c['segment'], 'text', 'Ví dụ: SUV ĐÔ THỊ CỠ B · 5 CHỖ') ?>
                <?= field('versions', 'Phiên bản & giá niêm yết', $c['versions'], 'textarea', 'Mỗi dòng một phiên bản: <b>Tên | Giá</b>. Ví dụ: <code>GLX | 599000000</code>. Chưa có giá thì chỉ ghi tên (web hiện “Liên hệ”). Giá thấp nhất tự thành “Giá từ”.', ['rows' => 5]) ?>
            </div></section>
            <section class="card"><h2>Thông số</h2><div class="fields">
                <?= field('seats', 'Số chỗ ngồi', $c['seats'], 'text', 'Ví dụ: 05') ?>
                <?= field('engine', 'Động cơ', $c['engine']) ?>
                <?= field('origin', 'Xuất xứ', $c['origin'], 'text', 'Ví dụ: Nhập khẩu Indonesia') ?>
                <?= field('warranty', 'Bảo hành', $c['warranty'], 'text', 'Để trống = “Chính hãng – theo Sổ bảo hành”') ?>
                <?= field('fee_kind', 'Loại xe để tính lệ phí lăn bánh', $c['fee_kind'], 'select', '', ['options' => ['5' => 'Xe con dưới 6 chỗ', '7' => 'Xe con 6–9 chỗ', 'pickup' => 'Xe bán tải']]) ?>
                <?= field('specs', 'Thông số bổ sung', $c['specs'], 'textarea', 'Mỗi dòng: <b>Tên | Giá trị</b>. Ví dụ: <code>Hộp số | CVT</code>', ['rows' => 4]) ?>
            </div></section>
            <section class="card"><h2>Ảnh &amp; giới thiệu</h2><div class="fields">
                <?= image_field('image', 'Ảnh xe (trang chủ, trang xe, khuyến mãi)', $c['image'], 'Nên dùng ảnh nền trắng, ngang khoảng 1200 × 800 px.') ?>
                <?= field('excerpt', 'Mô tả ngắn trên Google (1–2 câu)', $c['excerpt'], 'textarea', 'Để trống sẽ tự tạo từ giá.', ['rows' => 2]) ?>
                <?= field('content', 'Bài giới thiệu chi tiết (tùy chọn)', $c['content'], 'editor') ?>
            </div></section>
            <section class="card"><h2>Ưu đãi &amp; điểm nổi bật</h2><div class="fields">
                <?= field('promos', 'Ưu đãi đang áp dụng (trang Khuyến mãi)', $c['promos'], 'textarea', 'Mỗi dòng một ưu đãi. Để trống sẽ hiện ưu đãi chung.', ['rows' => 4]) ?>
                <?= field('feat_exterior', 'Ngoại thất', $c['feat_exterior'], 'textarea', '', ['rows' => 3]) ?>
                <?= field('feat_interior', 'Nội thất', $c['feat_interior'], 'textarea', '', ['rows' => 3]) ?>
                <?= field('feat_drive', 'Vận hành', $c['feat_drive'], 'textarea', '', ['rows' => 3]) ?>
                <?= field('feat_safety', 'An toàn', $c['feat_safety'], 'textarea', '', ['rows' => 3]) ?>
            </div></section>
            <section class="card"><h2>Hiển thị</h2><div class="fields">
                <?= field('ord', 'Thứ tự (số nhỏ hiện trước)', (string) $c['ord'], 'number') ?>
                <?= field('slug', 'Đường dẫn', $c['slug'], 'text', 'Để trống sẽ tự tạo, ví dụ <code>mitsubishi-xforce</code>. Đổi đường dẫn sẽ làm mất thứ hạng Google của trang cũ.') ?>
                <div class="field"><label class="check"><input type="checkbox" name="active" value="1" <?= $c['active'] ? 'checked' : '' ?>> Hiện xe này trên web</label></div>
            </div></section>
            <div class="savebar"><button class="btn btn--primary btn--lg">💾 Lưu xe</button></div>
        </form>
        <?php if ($c['id']) : ?>
            <form method="post" class="danger" onsubmit="return confirm('Xóa hẳn dòng xe <?= e($c['name']) ?>?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int) $c['id'] ?>"><button class="btn btn--danger btn--sm">Xóa dòng xe</button></form>
        <?php endif; ?>
        <?php
        break;

    /* ------------------------------------------------ Bài viết */
    case 'posts':
        layout_start('Bài viết', 'posts');
        ?>
        <div class="head"><h1>Bài viết</h1><a class="btn btn--primary" href="<?= e(admin_url('post', ['id' => 'new'])) ?>">+ Viết bài mới</a></div>
        <div class="table-wrap"><table class="tbl">
            <thead><tr><th>Ảnh</th><th>Tiêu đề</th><th>Chuyên mục</th><th>Ngày</th><th>Trạng thái</th></tr></thead>
            <tbody>
            <?php foreach (rows('SELECT * FROM posts ORDER BY created_at DESC') as $po) : ?>
                <tr>
                    <td class="thumb"><?= $po['image'] ? '<img src="' . eu(media($po['image'])) . '" alt="">' : '' ?></td>
                    <td><a class="strong" href="<?= e(admin_url('post', ['id' => $po['id']])) ?>"><?= e($po['title']) ?></a></td>
                    <td><?= e($po['category']) ?></td>
                    <td><?= e(vn_date($po['created_at'])) ?></td>
                    <td><?= $po['status'] === 'publish' ? '<span class="tag tag--green">Đã đăng</span>' : '<span class="tag">Nháp</span>' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <?php
        break;

    case 'post':
        $id = get_('id');
        $po = $id === 'new' ? ['id' => 0, 'slug' => '', 'title' => '', 'excerpt' => '', 'content' => '', 'image' => '', 'category' => 'Kinh nghiệm mua xe', 'status' => 'publish', 'created_at' => date('Y-m-d H:i:s')] : row('SELECT * FROM posts WHERE id = ?', [(int) $id]);
        if (!$po) {
            go('posts', [], 'Không tìm thấy bài.');
        }
        $editor = true;
        $catList = array_unique(array_merge(['Kinh nghiệm mua xe', 'Tin khuyến mãi', 'Sự kiện nổi bật', 'Tin tổng hợp', 'Công nghệ & Kỹ thuật', 'Trải nghiệm khách hàng', 'Tin tuyển dụng'], array_column(rows("SELECT DISTINCT category FROM posts WHERE category <> ''"), 'category')));
        layout_start($po['id'] ? 'Sửa bài' : 'Viết bài mới', 'posts');
        ?>
        <a class="back" href="<?= e(admin_url('posts')) ?>">← Danh sách bài</a>
        <div class="head"><h1><?= $po['id'] ? 'Sửa bài viết' : 'Viết bài mới' ?></h1><?php if ($po['id'] && $po['status'] === 'publish') : ?><a class="btn" href="<?= e(post_url($po)) ?>" target="_blank">Xem bài ↗</a><?php endif; ?></div>
        <form method="post" enctype="multipart/form-data" class="form">
            <?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int) $po['id'] ?>">
            <div class="postgrid">
                <div>
                    <section class="card"><div class="fields">
                        <?= field('title', 'Tiêu đề', $po['title'], 'text', '', ['required' => true, 'placeholder' => 'Ví dụ: Giá lăn bánh Xforce tháng 11/2026']) ?>
                        <?= field('content', 'Nội dung', $po['content'], 'editor', 'Bấm biểu tượng ảnh trên thanh công cụ để chèn ảnh từ máy. Dùng “Heading 2” cho các mục lớn – web tự tạo mục lục.') ?>
                    </div></section>
                </div>
                <div>
                    <section class="card"><div class="fields">
                        <?= field('status', 'Trạng thái', $po['status'], 'select', '', ['options' => ['publish' => 'Đăng ngay', 'draft' => 'Lưu nháp (chưa hiện trên web)']]) ?>
                        <?= field('category', 'Chuyên mục', $po['category'], 'text', 'Chọn trong danh sách hoặc gõ chuyên mục mới.', ['list' => 'cats']) ?>
                        <datalist id="cats"><?php foreach ($catList as $cl) : ?><option value="<?= e($cl) ?>"><?php endforeach; ?></datalist>
                        <?= image_field('image', 'Ảnh đại diện', $po['image'], 'Ngang 1200 × 630 px – hiện ở danh sách tin và khi chia sẻ Facebook.') ?>
                        <?= field('excerpt', 'Tóm tắt (hiện trên Google)', $po['excerpt'], 'textarea', '1–2 câu, khoảng 150 ký tự.', ['rows' => 3]) ?>
                        <?= field('created_at', 'Ngày đăng', date('Y-m-d\TH:i', strtotime($po['created_at'])), 'datetime-local') ?>
                        <?= field('slug', 'Đường dẫn', $po['slug'], 'text', 'Để trống sẽ tự tạo từ tiêu đề.') ?>
                    </div>
                    <button class="btn btn--primary btn--lg btn--block">💾 <?= $po['id'] ? 'Lưu bài' : 'Đăng bài' ?></button>
                    </section>
                </div>
            </div>
        </form>
        <?php if ($po['id']) : ?>
            <form method="post" class="danger" onsubmit="return confirm('Xóa bài viết này?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int) $po['id'] ?>"><button class="btn btn--danger btn--sm">Xóa bài</button></form>
        <?php endif; ?>
        <?php
        break;

    /* ------------------------------------------------ Banner */
    case 'banners':
        layout_start('Banner', 'banners');
        ?>
        <div class="head"><h1>Banner trang chủ</h1><a class="btn btn--primary" href="<?= e(admin_url('banner', ['id' => 'new'])) ?>">+ Thêm banner</a></div>
        <p class="muted">Kích thước chuẩn <b>1920 × 817 px</b> (tỉ lệ 2,35 : 1), nên dưới 400 KB. Banner chạy lần lượt theo “Thứ tự”.</p>
        <div class="cards">
            <?php foreach (rows('SELECT * FROM banners ORDER BY ord, id') as $b) : ?>
                <a class="tile" href="<?= e(admin_url('banner', ['id' => $b['id']])) ?>">
                    <img src="<?= eu(media($b['image'])) ?>" alt="">
                    <span><b>#<?= (int) $b['ord'] ?></b> <?= e($b['alt']) ?> <?= $b['active'] ? '' : '<em class="tag">Ẩn</em>' ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
        break;

    case 'banner':
        $id = get_('id');
        $b = $id === 'new' ? ['id' => 0, 'image' => '', 'link' => '', 'alt' => '', 'ord' => (int) val('SELECT COALESCE(MAX(ord),0)+1 FROM banners'), 'active' => 1] : row('SELECT * FROM banners WHERE id = ?', [(int) $id]);
        if (!$b) {
            go('banners');
        }
        layout_start('Banner', 'banners');
        $carOpts = ['' => '— Không bấm được —'];
        foreach (cars(true) as $c) {
            $carOpts[$c['slug'] . '/'] = 'Trang xe ' . $c['name'];
        }
        foreach (['bang-gia' => 'Bảng giá', 'khuyen-mai' => 'Khuyến mãi', 'lai-thu' => 'Đăng ký lái thử', 'tra-gop' => 'Tính trả góp', 'du-tinh' => 'Dự tính lăn bánh'] as $k => $l) {
            $carOpts[page_map()[$k][0]] = 'Trang ' . $l;
        }
        if ($b['link'] && !isset($carOpts[$b['link']])) {
            $carOpts[$b['link']] = $b['link'];
        }
        ?>
        <a class="back" href="<?= e(admin_url('banners')) ?>">← Danh sách banner</a>
        <h1><?= $b['id'] ? 'Sửa banner' : 'Thêm banner' ?></h1>
        <form method="post" enctype="multipart/form-data" class="form card">
            <?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
            <div class="fields">
                <?= image_field('image', 'Ảnh banner', $b['image'], '1920 × 817 px (tỉ lệ 2,35 : 1).') ?>
                <?= field('link', 'Khi khách bấm vào banner thì mở', $b['link'], 'select', '', ['options' => $carOpts]) ?>
                <?= field('alt', 'Mô tả ảnh (cho Google)', $b['alt'], 'text', 'Ví dụ: Mitsubishi Xpander Cross – Tự tin vững lái') ?>
                <?= field('ord', 'Thứ tự', (string) $b['ord'], 'number') ?>
                <div class="field"><label class="check"><input type="checkbox" name="active" value="1" <?= $b['active'] ? 'checked' : '' ?>> Hiện banner này</label></div>
            </div>
            <button class="btn btn--primary btn--lg">💾 Lưu banner</button>
        </form>
        <?php if ($b['id']) : ?><form method="post" class="danger" onsubmit="return confirm('Xóa banner này?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int) $b['id'] ?>"><button class="btn btn--danger btn--sm">Xóa banner</button></form><?php endif; ?>
        <?php
        break;

    /* ------------------------------------------------ Ảnh giao xe */
    case 'deliveries':
        layout_start('Ảnh giao xe', 'deliveries');
        ?>
        <div class="head"><h1>Ảnh giao xe</h1><a class="btn btn--primary" href="<?= e(admin_url('delivery', ['id' => 'new'])) ?>">+ Thêm ảnh giao xe</a></div>
        <p class="muted">Ảnh dọc <b>1080 × 1440 px</b> (3 : 4). Hai ảnh đầu tiên hiện ở trang chủ, tất cả hiện ở trang Liên hệ.</p>
        <div class="cards cards--portrait">
            <?php foreach (rows('SELECT * FROM deliveries ORDER BY ord, id') as $d) : ?>
                <a class="tile" href="<?= e(admin_url('delivery', ['id' => $d['id']])) ?>">
                    <img src="<?= eu(media($d['image'])) ?>" alt="">
                    <span><b>#<?= (int) $d['ord'] ?></b> <?= e($d['car']) ?> · <?= e($d['cust_type']) ?> <?= $d['active'] ? '' : '<em class="tag">Ẩn</em>' ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
        break;

    case 'delivery':
        $id = get_('id');
        $d = $id === 'new' ? ['id' => 0, 'image' => '', 'car' => '', 'cust_type' => 'Khách hàng cá nhân', 'msg_by' => 'consultant', 'message' => '', 'cust_name' => '', 'ord' => (int) val('SELECT COALESCE(MAX(ord),0)+1 FROM deliveries'), 'active' => 1] : row('SELECT * FROM deliveries WHERE id = ?', [(int) $id]);
        if (!$d) {
            go('deliveries');
        }
        layout_start('Ảnh giao xe', 'deliveries');
        $carNames = array_column(cars(true), 'name', 'name');
        ?>
        <a class="back" href="<?= e(admin_url('deliveries')) ?>">← Danh sách ảnh</a>
        <h1><?= $d['id'] ? 'Sửa ảnh giao xe' : 'Thêm ảnh giao xe' ?></h1>
        <form method="post" enctype="multipart/form-data" class="form card">
            <?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="id" value="<?= (int) $d['id'] ?>">
            <div class="fields">
                <?= image_field('image', 'Ảnh bàn giao', $d['image'], 'Ảnh dọc 1080 × 1440 px.') ?>
                <?= field('car', 'Dòng xe', $d['car'], 'select', '', ['options' => ($d['car'] && !isset($carNames[$d['car']]) ? [$d['car'] => $d['car']] : []) + $carNames]) ?>
                <?= field('cust_type', 'Loại khách', $d['cust_type'], 'select', '', ['options' => ['Khách hàng cá nhân' => 'Khách hàng cá nhân', 'Khách hàng gia đình' => 'Khách hàng gia đình']]) ?>
                <?= field('msg_by', 'Nội dung dưới ảnh là', $d['msg_by'], 'select', 'Chỉ chọn “Nhận xét thật của khách” khi khách đã đồng ý cho đăng.', ['options' => ['consultant' => 'Lời cảm ơn của tư vấn viên', 'customer' => 'Nhận xét thật của khách']]) ?>
                <?= field('message', 'Nội dung', $d['message'], 'textarea', '', ['rows' => 3]) ?>
                <?= field('cust_name', 'Tên khách (chỉ khi là nhận xét của khách)', $d['cust_name'], 'text', 'Ví dụ: Anh Minh – Hà Đông') ?>
                <?= field('ord', 'Thứ tự', (string) $d['ord'], 'number') ?>
                <div class="field"><label class="check"><input type="checkbox" name="active" value="1" <?= $d['active'] ? 'checked' : '' ?>> Hiện ảnh này</label></div>
            </div>
            <button class="btn btn--primary btn--lg">💾 Lưu ảnh</button>
        </form>
        <?php if ($d['id']) : ?><form method="post" class="danger" onsubmit="return confirm('Xóa ảnh này?')"><?= csrf_field() ?><input type="hidden" name="act" value="delete"><input type="hidden" name="id" value="<?= (int) $d['id'] ?>"><button class="btn btn--danger btn--sm">Xóa ảnh</button></form><?php endif; ?>
        <?php
        break;

    /* ------------------------------------------------ Trang */
    case 'pages':
        layout_start('Các trang', 'pages');
        $names = ['home' => 'Trang chủ', 'bang-gia' => 'Bảng giá', 'khuyen-mai' => 'Khuyến mãi', 'du-tinh' => 'Dự tính lăn bánh', 'tra-gop' => 'Tính trả góp', 'lai-thu' => 'Đăng ký lái thử', 'dich-vu' => 'Dịch vụ', 'tin-tuc' => 'Tin tức', 'lien-he' => 'Giới thiệu & liên hệ', 'chinh-sach' => 'Chính sách & điều khoản', 'tac-gia' => 'Tác giả'];
        ?>
        <h1>Các trang</h1>
        <p class="muted">Sửa tiêu đề, dòng mô tả, ảnh nền đầu trang và nội dung thêm của từng trang.</p>
        <div class="table-wrap"><table class="tbl">
            <thead><tr><th>Ảnh nền</th><th>Trang</th><th>Tiêu đề hiển thị</th><th>Sửa lần cuối</th></tr></thead>
            <tbody>
            <?php foreach (rows('SELECT * FROM pages') as $pg) : if ($pg['k'] === 'home') { continue; } ?>
                <tr>
                    <td class="thumb"><?= $pg['image'] ? '<img src="' . eu(media($pg['image'])) . '" alt="">' : '' ?></td>
                    <td><a class="strong" href="<?= e(admin_url('page', ['k' => $pg['k']])) ?>"><?= e($names[$pg['k']] ?? $pg['k']) ?></a><br><small class="muted"><?= e(page_url($pg['k'])) ?></small></td>
                    <td><?= e($pg['title']) ?></td>
                    <td><?= e(vn_date($pg['updated_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
        <p class="muted">Tiêu đề &amp; mô tả trang chủ trên Google: sửa ở <a href="<?= e(admin_url('settings', ['tab' => 'leads'])) ?>">Cài đặt chung → Nhận khách &amp; SEO</a>.</p>
        <?php
        break;

    case 'page':
        $k = get_('k');
        $pg = row('SELECT * FROM pages WHERE k = ?', [$k]);
        if (!$pg) {
            go('pages');
        }
        $editor = true;
        layout_start('Sửa trang', 'pages');
        $contentHint = [
            'dich-vu' => 'Nội dung chính của trang Dịch vụ. Mỗi mục lớn dùng “Heading 2” – web tự tạo thanh mục ở đầu trang. Form đặt lịch tự thêm ở cuối.',
            'chinh-sach' => 'Nội dung chính của trang Chính sách.',
        ][$k] ?? 'Nội dung thêm (tùy chọn) – hiện bên dưới phần có sẵn của trang.';
        ?>
        <a class="back" href="<?= e(admin_url('pages')) ?>">← Các trang</a>
        <div class="head"><h1>Sửa trang: <?= e($pg['title']) ?></h1><a class="btn" href="<?= e(page_url($k)) ?>" target="_blank">Xem trang ↗</a></div>
        <form method="post" enctype="multipart/form-data" class="form">
            <?= csrf_field() ?><input type="hidden" name="act" value="save"><input type="hidden" name="k" value="<?= e($k) ?>">
            <section class="card"><div class="fields">
                <?= field('title', 'Tiêu đề lớn', $pg['title']) ?>
                <?= field('subtitle', 'Dòng mô tả dưới tiêu đề', $pg['subtitle'], 'textarea', 'Gõ <code>{phone}</code> để chèn số điện thoại tư vấn, <code>{tech}</code> để chèn hotline kỹ thuật.', ['rows' => 2]) ?>
                <?php if ($k !== 'tac-gia') : ?><?= image_field('image', 'Ảnh nền đầu trang', $pg['image'], 'Ngang 1920 × 600 px trở lên.') ?><?php endif; ?>
                <?= field('meta_desc', 'Mô tả trên Google', $pg['meta_desc'], 'textarea', 'Khoảng 150 ký tự.', ['rows' => 2]) ?>
                <?= field('content', 'Nội dung', $pg['content'], 'editor', $contentHint) ?>
            </div></section>
            <div class="savebar"><button class="btn btn--primary btn--lg">💾 Lưu trang</button></div>
        </form>
        <?php
        break;

    /* ------------------------------------------------ Cài đặt */
    case 'settings':
        layout_start('Cài đặt chung', 'settings');
        $schema = settings_schema();
        $tab = isset($schema[get_('tab')]) ? get_('tab') : 'contact';
        ?>
        <h1>Cài đặt chung</h1>
        <div class="tabs"><?php foreach ($schema as $k => $g) : ?><a class="<?= $k === $tab ? 'is-on' : '' ?>" href="<?= e(admin_url('settings', ['tab' => $k])) ?>"><?= e($g['title']) ?></a><?php endforeach; ?></div>
        <form method="post" enctype="multipart/form-data" class="form" action="<?= e(admin_url('settings', ['tab' => $tab])) ?>">
            <?= csrf_field() ?><input type="hidden" name="act" value="save">
            <?php foreach ($schema as $gk => $g) : ?>
                <section class="card<?= $gk === $tab ? '' : ' is-hidden' ?>">
                    <h2><?= e($g['title']) ?></h2>
                    <?php if (!empty($g['note'])) : ?><p class="muted"><?= e($g['note']) ?></p><?php endif; ?>
                    <div class="fields">
                        <?php foreach ($g['fields'] as $fk => $f) : ?>
                            <?php
                            $raw = row('SELECT v FROM settings WHERE k = ?', [$fk]);
                            $v = $raw ? (string) $raw['v'] : (string) (defaults()[$fk] ?? '');
                            if ($f[1] === 'image') {
                                echo image_field($fk, $f[0], $v);
                            } else {
                                echo field($fk, html_entity_decode($f[0]), $v, $f[1] === 'number' ? 'text' : $f[1], '', ['rows' => 3]);
                            }
                            ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
            <div class="savebar"><button class="btn btn--primary btn--lg">💾 Lưu cài đặt</button></div>
        </form>
        <?php if ($tab === 'leads') : ?>
            <section class="card">
                <h2>Ghi khách vào Google Sheets</h2>
                <ol class="tips">
                    <li>Tạo Google Sheet mới → menu <b>Tiện ích mở rộng → Apps Script</b>.</li>
                    <li>Xóa hết, dán đoạn mã bên dưới, bấm <b>Lưu</b>.</li>
                    <li><b>Triển khai → Triển khai mới → Ứng dụng web</b>, “Ai có quyền truy cập”: <b>Bất kỳ ai</b> → Triển khai → cấp quyền.</li>
                    <li>Copy “URL ứng dụng web”, dán vào ô <b>Link Google Apps Script</b> ở trên, bấm Lưu.</li>
                </ol>
                <pre class="code">function doPost(e) {
  var d = JSON.parse(e.postData.contents);
  var sh = SpreadsheetApp.getActiveSheet();
  if (sh.getLastRow() === 0) sh.appendRow(['Thời gian','Họ tên','Điện thoại','Xe','Khu vực','Thanh toán','Form','Thông tin thêm','Trang']);
  sh.appendRow([d.time, d.name, "'" + d.phone, d.car, d.region, d.pay, d.source, d.extra, d.page]);
  return ContentService.createTextOutput('ok');
}</pre>
            </section>
        <?php endif; ?>
        <?php
        break;

    /* ------------------------------------------------ Tài khoản */
    case 'account':
        layout_start('Mật khẩu & sao lưu', 'account');
        ?>
        <h1>Mật khẩu &amp; sao lưu</h1>
        <div class="grid2">
            <form method="post" class="card form">
                <h2>Đổi mật khẩu</h2>
                <?= csrf_field() ?><input type="hidden" name="act" value="password">
                <div class="fields">
                    <?= field('old', 'Mật khẩu hiện tại', '', 'password') ?>
                    <?= field('new', 'Mật khẩu mới (ít nhất 8 ký tự)', '', 'password') ?>
                    <?= field('new2', 'Nhập lại mật khẩu mới', '', 'password') ?>
                </div>
                <button class="btn btn--primary">Đổi mật khẩu</button>
            </form>
            <section class="card">
                <h2>Sao lưu dữ liệu</h2>
                <p>Toàn bộ nội dung web (xe, giá, bài viết, khách hàng, cài đặt) nằm trong một file. Nên tải về máy mỗi tháng một lần.</p>
                <a class="btn btn--primary" href="<?= e(admin_url('backup', ['t' => csrf()])) ?>">⬇ Tải file sao lưu</a>
                <p class="hint">Ảnh tải lên nằm trong thư mục <code>uploads/</code> trên hosting – sao lưu thư mục này bằng File Manager của hosting.</p>
                <h2 style="margin-top:24px">Thông tin</h2>
                <p class="muted">Phiên bản <?= e(APP_VERSION) ?> · PHP <?= e(PHP_VERSION) ?> · <?= (int) val('SELECT COUNT(*) FROM leads') ?> khách đã lưu.</p>
            </section>
        </div>
        <?php
        break;
}

layout_end($editor);

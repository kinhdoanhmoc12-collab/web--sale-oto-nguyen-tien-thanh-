<?php
/**
 * Trang quản trị – hàm dùng chung: đăng nhập, tải ảnh, giao diện khung.
 */

require dirname(__DIR__) . '/inc/bootstrap.php';
session_start_safe();
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

function admin_url(string $p = '', array $args = []): string
{
    $q = $p !== '' ? ['p' => $p] + $args : $args;
    return url('admin/') . ($q ? '?' . http_build_query($q) : '');
}
function go(string $p = '', array $args = [], string $flash = ''): void
{
    if ($flash !== '') {
        $_SESSION['flash'] = $flash;
    }
    header('Location: ' . admin_url($p, $args));
    exit;
}
function logged_in(): bool { return !empty($_SESSION['admin']) && ($_SESSION['admin_hash'] ?? '') === substr(opt('admin_hash'), -20); }
function post(string $k, string $d = ''): string { return isset($_POST[$k]) ? trim((string) $_POST[$k]) : $d; }
function get_(string $k, string $d = ''): string { return isset($_GET[$k]) ? trim((string) $_GET[$k]) : $d; }
function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !csrf_ok()) {
        http_response_code(400);
        exit('Phiên làm việc hết hạn. Vui lòng tải lại trang và thử lại.');
    }
}

/**
 * Lưu ảnh tải lên: kiểm tra đúng là ảnh, thu nhỏ nếu quá lớn, trả về đường dẫn "uploads/…".
 * Trả về '' nếu không có file; ném lỗi nếu file không hợp lệ.
 */
function save_upload(string $field, int $maxW = 1920, ?array $file = null): string
{
    $f = $file ?? ($_FILES[$field] ?? null);
    if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if ($f['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Tải ảnh lỗi (mã ' . $f['error'] . '). Ảnh có thể quá lớn so với giới hạn của hosting.');
    }
    if ($f['size'] > 15 * 1024 * 1024) {
        throw new RuntimeException('Ảnh quá lớn (tối đa 15MB).');
    }
    $info = @getimagesize($f['tmp_name']);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if (!$info || !isset($types[$info[2]])) {
        throw new RuntimeException('File không phải ảnh JPG, PNG, WEBP hoặc GIF.');
    }
    $ext = $types[$info[2]];
    $dir = date('Y/m');
    if (!is_dir(UPLOAD_DIR . '/' . $dir)) {
        mkdir(UPLOAD_DIR . '/' . $dir, 0755, true);
    }
    $base = slugify(pathinfo($f['name'], PATHINFO_FILENAME)) ?: 'anh';
    $name = substr($base, 0, 50) . '-' . substr(bin2hex(random_bytes(4)), 0, 6);
    $dest = UPLOAD_DIR . "/$dir/$name.$ext";

    $resized = false;
    if ($info[0] > $maxW && $ext !== 'gif' && function_exists('imagecreatetruecolor')) {
        $src = $ext === 'jpg' ? @imagecreatefromjpeg($f['tmp_name']) : ($ext === 'png' ? @imagecreatefrompng($f['tmp_name']) : (function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($f['tmp_name']) : false));
        if ($src) {
            $h = (int) round($info[1] * $maxW / $info[0]);
            $dst = imagecreatetruecolor($maxW, $h);
            if ($ext !== 'jpg') {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            }
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxW, $h, $info[0], $info[1]);
            $ok = $ext === 'jpg' ? imagejpeg($dst, $dest, 85) : ($ext === 'png' ? imagepng($dst, $dest, 7) : imagewebp($dst, $dest, 82));
            imagedestroy($src);
            imagedestroy($dst);
            $resized = (bool) $ok;
        }
    }
    if (!$resized && !move_uploaded_file($f['tmp_name'], $dest)) {
        if (!copy($f['tmp_name'], $dest)) {
            throw new RuntimeException('Không lưu được ảnh. Kiểm tra quyền ghi thư mục uploads.');
        }
    }
    return "uploads/$dir/$name.$ext";
}

/** Image field: preview + file input + "remove" checkbox. Current value posted back in hidden input. */
function image_field(string $name, string $label, string $value, string $hint = ''): string
{
    $h = '<div class="field"><label>' . e($label) . '</label><div class="imgfield">';
    $h .= '<div class="imgfield__preview">' . ($value ? '<img src="' . eu(media($value)) . '" alt="">' : '<span>Chưa có ảnh</span>') . '</div>';
    $h .= '<div class="imgfield__ctrl"><input type="hidden" name="' . e($name) . '" value="' . e($value) . '">';
    $h .= '<input type="file" name="' . e($name) . '_file" accept="image/jpeg,image/png,image/webp,image/gif">';
    if ($hint) {
        $h .= '<p class="hint">' . e($hint) . '</p>';
    }
    if ($value) {
        $h .= '<label class="check"><input type="checkbox" name="' . e($name) . '_remove" value="1"> Bỏ ảnh này</label>';
    }
    $h .= '</div></div></div>';
    return $h;
}
/** Resolve posted image field (new upload > remove > keep). */
function image_value(string $name, int $maxW = 1920): string
{
    $up = save_upload($name . '_file', $maxW);
    if ($up !== '') {
        return $up;
    }
    if (!empty($_POST[$name . '_remove'])) {
        return '';
    }
    return post($name);
}

function field(string $name, string $label, $value, string $type = 'text', string $hint = '', array $opts = []): string
{
    $id = 'f_' . preg_replace('/\W/', '_', $name);
    $h = '<div class="field' . ($type === 'textarea' || $type === 'editor' ? ' field--wide' : '') . '"><label for="' . $id . '">' . e($label) . '</label>';
    if ($type === 'textarea') {
        $h .= '<textarea id="' . $id . '" name="' . e($name) . '" rows="' . (int) ($opts['rows'] ?? 4) . '">' . e($value) . '</textarea>';
    } elseif ($type === 'editor') {
        $h .= '<textarea id="' . $id . '" class="js-editor" name="' . e($name) . '" rows="18">' . e($value) . '</textarea>';
    } elseif ($type === 'select') {
        $h .= '<select id="' . $id . '" name="' . e($name) . '">';
        foreach ($opts['options'] as $v => $l) {
            $h .= '<option value="' . e($v) . '"' . ((string) $v === (string) $value ? ' selected' : '') . '>' . e($l) . '</option>';
        }
        $h .= '</select>';
    } else {
        $extra = isset($opts['list']) ? ' list="' . e($opts['list']) . '"' : '';
        $extra .= isset($opts['placeholder']) ? ' placeholder="' . e($opts['placeholder']) . '"' : '';
        $extra .= !empty($opts['required']) ? ' required' : '';
        $h .= '<input id="' . $id . '" type="' . e($type) . '" name="' . e($name) . '" value="' . e($value) . '"' . $extra . '>';
    }
    if ($hint !== '') {
        $h .= '<p class="hint">' . $hint . '</p>';
    }
    return $h . '</div>';
}

function admin_menu(): array
{
    $new = (int) val("SELECT COUNT(*) FROM leads WHERE status = 'new'");
    return [
        'dashboard' => ['Tổng quan', '🏠'],
        'leads' => ['Khách hàng' . ($new ? ' <b class="badge">' . $new . '</b>' : ''), '👥'],
        'cars' => ['Dòng xe & giá', '🚗'],
        'posts' => ['Bài viết', '📝'],
        'banners' => ['Banner trang chủ', '🖼️'],
        'deliveries' => ['Ảnh giao xe', '🎉'],
        'pages' => ['Các trang', '📄'],
        'settings' => ['Cài đặt chung', '⚙️'],
        'account' => ['Mật khẩu & sao lưu', '🔒'],
    ];
}

function layout_start(string $title, string $active = ''): void
{
    $flash = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    ?><!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> – Quản trị web</title>
<link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>">
<link rel="stylesheet" href="<?= e(url('admin/admin.css')) ?>?v=<?= @filemtime(__DIR__ . '/admin.css') ?>">
<script>window.ADM = {upload: <?= json_encode(admin_url('upload')) ?>, csrf: <?= json_encode(csrf()) ?>, base: <?= json_encode(BASE . '/') ?>};</script>
</head>
<body class="adm">
<?php if (logged_in()) : ?>
<header class="adm-top">
	<button type="button" class="adm-burger" aria-label="Mở menu" onclick="document.body.classList.toggle('menu-open')">☰</button>
	<a class="adm-brand" href="<?= e(admin_url()) ?>"><img src="<?= e(url('assets/img/logo.svg')) ?>" alt="" width="30" height="30"> Quản trị web</a>
	<a class="adm-view" href="<?= e(url()) ?>" target="_blank">Xem web ↗</a>
</header>
<nav class="adm-side" aria-label="Menu quản trị">
	<?php foreach (admin_menu() as $k => $m) : ?>
		<a href="<?= e(admin_url($k)) ?>" class="<?= $active === $k ? 'is-on' : '' ?>"><span class="ico"><?= $m[1] ?></span><?= $m[0] ?></a>
	<?php endforeach; ?>
	<form method="post" action="<?= e(admin_url('logout')) ?>"><?= csrf_field() ?><button type="submit">↩ Đăng xuất</button></form>
</nav>
<?php endif; ?>
<main class="adm-main<?= logged_in() ? '' : ' adm-main--auth' ?>">
<?php if ($flash) : ?><div class="flash" role="status"><?= e($flash) ?></div><?php endif; ?>
<?php
}

function layout_end(bool $editor = false): void
{
    ?>
</main>
<?php if ($editor) : ?>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<?php endif; ?>
<script>window.ADM = {upload: <?= json_encode(admin_url('upload')) ?>, csrf: <?= json_encode(csrf()) ?>, base: <?= json_encode(BASE . '/') ?>};</script>
<script src="<?= e(url('admin/admin.js')) ?>?v=<?= @filemtime(__DIR__ . '/admin.js') ?>"></script>
</body>
</html>
<?php
}

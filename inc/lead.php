<?php
/**
 * Nhận thông tin khách từ mọi form: lưu database, gửi email, (tùy chọn) ghi Google Sheets.
 */

header('Content-Type: application/json; charset=utf-8');

function lead_out(bool $ok, string $msg = 'ok', int $code = 200): void
{
    http_response_code($code);
    echo json_encode(['success' => $ok, 'data' => ['message' => $msg]], JSON_UNESCAPED_UNICODE);
    exit;
}

function clean_phone(string $raw, bool $loose = false): string
{
    $d = preg_replace('/\D/', '', $raw);
    if (strpos($d, '84') === 0 && strlen($d) === 11) {
        $d = '0' . substr($d, 2);
    }
    if ($loose && preg_match('/0\d{9}/', $d, $m)) {
        return $m[0];
    }
    return preg_match('/^0\d{9}$/', $d) ? $d : '';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    lead_out(false, 'Sai phương thức.', 405);
}
$p = $_POST;
$txt = fn($k, $max = 200) => mb_substr(trim(strip_tags((string) ($p[$k] ?? ''))), 0, $max);

// Bẫy spam: ô ẩn phải trống, form mở tối thiểu 3 giây.
if (!empty($p['website'])) {
    lead_out(true);
}
$started = (int) ($p['t'] ?? 0);
if ($started && (microtime(true) * 1000 - $started) < 3000) {
    lead_out(false, 'Vui lòng thử lại sau vài giây.', 429);
}
// Giới hạn 6 lần / 10 phút / IP.
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if ((int) val("SELECT COUNT(*) FROM leads WHERE ip = ? AND created_at > ?", [$ip, date('Y-m-d H:i:s', time() - 600)]) >= 6) {
    lead_out(false, 'Anh/chị đã gửi nhiều lần. Vui lòng gọi trực tiếp ' . opt('phone') . '.', 429);
}

$name = $txt('name', 80);
$phone = clean_phone((string) ($p['phone'] ?? ''));
if ($name === '') {
    lead_out(false, 'Vui lòng nhập họ tên.', 422);
}
if ($phone === '') {
    lead_out(false, 'Vui lòng nhập số điện thoại hợp lệ (10 số, bắt đầu bằng 0).', 422);
}
if (empty($p['agree'])) {
    lead_out(false, 'Vui lòng tích ô đồng ý để chúng tôi liên hệ tư vấn.', 422);
}

$extra = [];
foreach (['version' => 'Phiên bản', 'date' => 'Ngày mong muốn', 'place' => 'Địa điểm', 'plate' => 'Biển số', 'service' => 'Dịch vụ', 'note' => 'Ghi chú'] as $k => $label) {
    $v = $txt($k, 500);
    if ($v !== '') {
        if ($k === 'date' && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $v, $dm)) {
            $v = "$dm[3]/$dm[2]/$dm[1]";
        }
        $extra[] = "$label: $v";
    }
}
$lead = [
    'created_at' => date('Y-m-d H:i:s'),
    'name' => $name,
    'phone' => $phone,
    'car' => $txt('car', 80),
    'region' => $txt('region', 120),
    'pay' => $txt('pay', 40),
    'source' => $txt('source', 40),
    'extra' => implode("\n", $extra),
    'page' => $txt('page', 300),
    'ip' => $ip,
    'status' => 'new',
    'note' => '',
];
$id = db_save('leads', $lead);

$labels = ['name' => 'Họ tên', 'phone' => 'Điện thoại', 'car' => 'Xe quan tâm', 'region' => 'Khu vực', 'pay' => 'Thanh toán', 'source' => 'Form', 'extra' => 'Thông tin thêm', 'page' => 'Trang'];

// Email.
$to = opt('lead_email') ?: opt('email');
if ($to) {
    $body = "Có khách vừa để lại thông tin trên web:\n\n";
    foreach ($labels as $k => $l) {
        if ($lead[$k] !== '') {
            $body .= "$l: {$lead[$k]}\n";
        }
    }
    $body .= "\nXem trong trang quản trị: " . full_url('admin/?p=leads');
    $subject = "[Web] Khách mới: $name – $phone";

    $sent = false;
    if (opt('smtp_enabled') === '1' && opt('smtp_user') && opt('smtp_pass')) {
        $sent = send_smtp_mail($to, $subject, $body);
    }
    if (!$sent && function_exists('mail')) {
        $host = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'mitsubishimydinh.com');
        $headers = "From: Web tu van <no-reply@$host>\r\nReply-To: no-reply@$host\r\nContent-Type: text/plain; charset=UTF-8";
        @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
    }
}

// Google Sheets (Apps Script web app).
$hook = opt('lead_webhook');
if ($hook && preg_match('~^https://~', $hook)) {
    $payload = json_encode(['time' => date('H:i d/m/Y')] + array_intersect_key($lead, $labels), JSON_UNESCAPED_UNICODE);
    if (function_exists('curl_init')) {
        $ch = curl_init($hook);
        curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 4, CURLOPT_FOLLOWLOCATION => true]);
        @curl_exec($ch);
        curl_close($ch);
    }
}

lead_out(true);

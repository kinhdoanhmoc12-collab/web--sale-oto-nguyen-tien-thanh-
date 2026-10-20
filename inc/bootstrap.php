<?php
/**
 * Khởi động: cấu hình, database, phiên đăng nhập, hàm dùng chung.
 */

declare(strict_types=1);

define('ROOT', dirname(__DIR__));
define('DATA_DIR', ROOT . '/data');
define('UPLOAD_DIR', ROOT . '/uploads');
define('APP_VERSION', '1.0.0');

date_default_timezone_set('Asia/Ho_Chi_Minh');
mb_internal_encoding('UTF-8');
ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', DATA_DIR . '/error.log');

/* Thư mục cài đặt (hỗ trợ đặt web trong thư mục con). */
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if (substr($base, -6) === '/admin') {
    $base = substr($base, 0, -6);
}
define('BASE', $base);

require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
require __DIR__ . '/data.php';

db_init();

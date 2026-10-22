<?php
/**
 * Web tư vấn Mitsubishi – bộ điều hướng trang.
 * Mọi đường dẫn đều đi qua file này (xem .htaccess).
 */

// PHP built-in server: serve real files directly.
if (PHP_SAPI === 'cli-server') {
    $f = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (preg_match('~/(data|inc|views)/~', $f)) {
        http_response_code(403);
        exit;
    }
    if (is_file($f) && substr($f, -4) !== '.php') {
        return false;
    }
    if (is_dir($f) && is_file(rtrim($f, '/') . '/index.php') && rtrim($f, '/') !== __DIR__) {
        require rtrim($f, '/') . '/index.php';
        return true;
    }
}

require __DIR__ . '/inc/bootstrap.php';
require __DIR__ . '/inc/seo.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (BASE !== '' && strpos($path, BASE) === 0) {
    $path = substr($path, strlen(BASE));
}
$path = trim(rawurldecode($path), '/');

/* ---------- Special routes ---------- */
if ($path === 'api/lead') {
    require ROOT . '/inc/lead.php';
    exit;
}
if ($path === 'mua-xe' || $path === 'xe-moi') {
    header('Location: ' . url('#mua-xe'), true, 301);
    exit;
}
if ($path === 'admin') {
    header('Location: ' . url('admin/'), true, 301);
    exit;
}
if ($path === 'sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $add = function ($loc, $mod = null) {
        echo '<url><loc>' . e($loc) . '</loc>' . ($mod ? '<lastmod>' . date('Y-m-d', strtotime($mod)) . '</lastmod>' : '') . "</url>\n";
    };
    foreach (page_map() as $k => $p) {
        $add(full_url($p[0]), page_data($k)['updated_at']);
    }
    foreach (cars() as $c) {
        $add(full_url($c['slug'] . '/'), $c['updated_at']);
    }
    foreach (rows("SELECT slug, updated_at FROM posts WHERE status = 'publish' ORDER BY created_at DESC") as $p) {
        $add(full_url('tin-tuc/' . $p['slug'] . '/'), $p['updated_at']);
    }
    echo '</urlset>';
    exit;
}
if ($path === 'robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\nDisallow: /admin/\nDisallow: /api/\nDisallow: /*?utm_\nDisallow: /*?fbclid=\n\nSitemap: " . full_url('sitemap.xml') . "\n";
    exit;
}

/* ---------- Resolve page ---------- */
$view = null;
$ctx = [];
foreach (page_map() as $k => $p) {
    if ($path === trim($p[0], '/')) {
        $view = $p[1];
        $ctx['page'] = page_data($k);
        $ctx['key'] = $k;
        break;
    }
}
if (!$view && preg_match('~^tin-tuc/([a-z0-9-]+)$~', $path, $m)) {
    $post = row("SELECT * FROM posts WHERE slug = ? AND status = 'publish'", [$m[1]]);
    if ($post) {
        $view = 'post';
        $ctx['post'] = $post;
    }
}
if (!$view && preg_match('~^([a-z0-9-]+)$~', $path, $m)) {
    $car = row('SELECT * FROM cars WHERE slug = ? AND active = 1', [$m[1]]);
    if ($car) {
        $view = 'car';
        $ctx['car'] = $car;
    }
}
/* Redirect missing trailing slash. */
if ($view && $path !== '' && substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), -1) !== '/') {
    header('Location: ' . url($path . '/') . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']), true, 301);
    exit;
}
if (!$view) {
    http_response_code(404);
    $view = '404';
}

$ACTIVE = $ctx['key'] ?? ($view === 'car' ? 'car' : ($view === 'post' ? 'tin-tuc' : ''));
extract($ctx);
include ROOT . '/views/' . $view . '.php';

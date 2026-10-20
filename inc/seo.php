<?php
/**
 * Thẻ SEO, Open Graph, dữ liệu có cấu trúc (JSON-LD).
 */

function plus84(string $p): string { $t = tel($p); return strpos($t, '0') === 0 ? '+84' . substr($t, 1) : $t; }

function schema_dealer(): array
{
    $home = full_url();
    return [
        '@type' => 'AutoDealer', '@id' => $home . '#dealer',
        'name' => opt('showroom_name') . ' (' . opt('company_short') . ')',
        'url' => $home, 'telephone' => plus84(explode('/', opt('dealer_hotline'))[0]), 'email' => opt('email'),
        'image' => full_url('assets/img/og-image.jpg'), 'brand' => ['@type' => 'Brand', 'name' => 'Mitsubishi'],
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => opt('address'), 'addressRegion' => 'Hà Nội', 'addressCountry' => 'VN'],
        'geo' => ['@type' => 'GeoCoordinates', 'latitude' => (float) opt('lat'), 'longitude' => (float) opt('lng')],
        'hasMap' => opt('maps_url'), 'taxID' => opt('company_tax'),
    ];
}

function schema_person(): array
{
    $home = full_url();
    return [
        '@type' => 'Person', '@id' => $home . '#tu-van-vien', 'name' => opt('consultant_name'), 'jobTitle' => opt('consultant_title'),
        'telephone' => plus84(opt('phone')), 'image' => media_full(opt('consultant_photo')), 'url' => full_url(page_map()['tac-gia'][0]),
        'worksFor' => ['@id' => $home . '#dealer'], 'sameAs' => array_values(array_filter([opt('facebook'), opt('youtube')])),
    ];
}

function breadcrumb(array $items): array
{
    $list = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => full_url()]];
    foreach ($items as $i => $it) {
        $list[] = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $it[0], 'item' => $it[1]];
    }
    return ['@type' => 'BreadcrumbList', 'itemListElement' => $list];
}

function seo_head(array $m): void
{
    $m += ['title' => opt('site_name'), 'desc' => '', 'canonical' => '', 'image' => full_url('assets/img/og-image.jpg'), 'type' => 'website', 'schema' => [], 'noindex' => false];
    echo '<title>' . e($m['title']) . "</title>\n";
    if ($m['desc'] !== '') {
        echo '<meta name="description" content="' . e($m['desc']) . "\">\n";
    }
    echo '<meta name="robots" content="' . ($m['noindex'] ? 'noindex, follow' : 'index, follow, max-image-preview:large') . "\">\n";
    if ($m['canonical']) {
        echo '<link rel="canonical" href="' . e($m['canonical']) . "\">\n";
    }
    echo '<meta property="og:locale" content="vi_VN">' . "\n";
    echo '<meta property="og:type" content="' . e($m['type']) . "\">\n";
    echo '<meta property="og:site_name" content="' . e(opt('site_name')) . "\">\n";
    echo '<meta property="og:title" content="' . e($m['title']) . "\">\n";
    if ($m['desc'] !== '') {
        echo '<meta property="og:description" content="' . e($m['desc']) . "\">\n";
    }
    if ($m['canonical']) {
        echo '<meta property="og:url" content="' . e($m['canonical']) . "\">\n";
    }
    echo '<meta property="og:image" content="' . e($m['image']) . "\">\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    if ($m['schema']) {
        echo '<script type="application/ld+json">' . json_encode(['@context' => 'https://schema.org', '@graph' => $m['schema']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
    }
}

/** Standard META for a built-in page. */
function page_meta(array $p, string $key, array $schema = []): array
{
    $u = full_url(page_map()[$key][0]);
    $title = ($p['title'] ?: opt('site_name'));
    return [
        'title' => $title . ' | Mitsubishi ' . opt('consultant_name'),
        'desc' => $p['meta_desc'] ?: strip_tags(str_replace(['{phone}', '{tech}'], [opt('phone'), opt('tech_hotline')], (string) $p['subtitle'])),
        'canonical' => $u,
        'image' => $p['image'] ? media_full($p['image']) : full_url('assets/img/og-image.jpg'),
        'schema' => array_merge($schema, [breadcrumb([[$title, $u]])]),
    ];
}

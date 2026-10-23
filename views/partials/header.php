<?php
/** @var array $META */
$phone = opt('phone');
$navCars = cars();
$fees = [
    'regions' => [
        ['id' => 'hn', 'label' => 'Hà Nội', 'rate' => (float) opt('fee_rate_hn'), 'plate' => (int) opt('plate_hn')],
        ['id' => 'hcm', 'label' => 'TP. Hồ Chí Minh', 'rate' => (float) opt('fee_rate_hcm'), 'plate' => (int) opt('plate_hcm')],
        ['id' => 'other', 'label' => 'Tỉnh/thành khác', 'rate' => (float) opt('fee_rate_other'), 'plate' => (int) opt('plate_other')],
    ],
    'pickupPct' => (float) opt('fee_pickup_pct'),
    'road' => (int) opt('road_fee_year'),
    'tnds' => ['5' => (int) opt('tnds_5'), '7' => (int) opt('tnds_7'), 'pickup' => (int) opt('tnds_pickup')],
    'inspection' => (int) opt('inspection_fee'),
];
$TT = ['ajax' => url('api/lead'), 'cars' => cars_js(), 'phone' => $phone, 'fees' => $fees];
$navLink = function (string $key, string $label) use (&$ACTIVE) {
    $on = ($ACTIVE ?? '') === $key;
    return '<li><a href="' . e(page_url($key)) . '"' . ($on ? ' class="is-active" aria-current="page"' : '') . '>' . e($label) . '</a></li>';
};
?><!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php seo_head($META ?? []); ?>
<meta name="theme-color" content="#CC0000">
<link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>" type="image/svg+xml">
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<?php $cssMtime = @filemtime(ROOT . '/assets/css/main.css'); $cssVer = APP_VERSION . '-' . $cssMtime; ?>
<link rel="stylesheet" href="<?= e(url('assets/css/main.css')) ?>?v=<?= e($cssVer) ?>">
<link rel="stylesheet" href="<?= e(url('assets/css/flatpickr.min.css')) ?>?v=<?= e(APP_VERSION) ?>">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap">
<?php if (!empty($PRELOAD)) : ?><link rel="preload" as="image" href="<?= eu($PRELOAD) ?>" fetchpriority="high"><?php endif; ?>
<?php $gtm = trim(opt('gtm_id')); if (preg_match('/^GTM-[A-Z0-9]+$/', $gtm)) : ?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?= e($gtm) ?>');</script>
<?php endif; ?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-L1896LSPRZ"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-L1896LSPRZ');
</script>
<?= opt('head_code') /* mã do quản trị viên dán (Facebook Pixel, xác minh Google…) */ ?>
<script>window.TT=<?= json_encode($TT, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;</script>
<script src="<?= e(url('assets/js/flatpickr.min.js')) ?>?v=<?= e(APP_VERSION) ?>" defer></script>
<script src="<?= e(url('assets/js/flatpickr-vn.js')) ?>?v=<?= e(APP_VERSION) ?>" defer></script>
<script src="<?= e(url('assets/js/main.js')) ?>?v=<?= e(APP_VERSION . '-' . @filemtime(ROOT . '/assets/js/main.js')) ?>" defer></script>
<?= $EXTRA_HEAD ?? '' ?>
</head>
<body class="view-<?= e($view ?? '') ?>">
<a class="skip-link" href="#main">Bỏ qua menu</a>

<header class="site-header">
	<div class="container site-header__inner">
		<a class="site-header__logo" href="<?= e(url()) ?>" aria-label="Về trang chủ"><img class="site-header__logo-img" src="<?= e(url('assets/img/logo.svg')) ?>" alt="Mitsubishi Motors" width="84" height="82" decoding="async"></a>
		<div class="site-header__brand">
			<a class="site-header__title" href="<?= e(url()) ?>">Tư vấn Mitsubishi – <?= e(opt('consultant_name')) ?></a>
			<div class="site-header__address">Địa chỉ: <?= e(opt('address')) ?></div>
		</div>
		<div class="site-header__actions">
			<button type="button" class="icon-btn" data-toggle="mega" aria-controls="tt-mega" aria-expanded="false" aria-label="Mở menu tất cả trang"><?= icon('menu', 26) ?></button>
			<a class="site-header__hotline" href="tel:<?= e(tel($phone)) ?>">HOTLINE: <span><?= e($phone) ?></span></a>
		</div>
	</div>
</header>

<nav class="main-nav" aria-label="Menu chính">
	<div class="container main-nav__inner">
		<a href="<?= e(url()) ?>" class="main-nav__link<?= ($ACTIVE ?? '') === 'home' ? ' is-active' : '' ?>">Trang chủ</a>
		<button type="button" class="main-nav__link<?= ($ACTIVE ?? '') === 'car' ? ' is-active' : '' ?>" data-toggle="cars" aria-controls="tt-cars" aria-expanded="false">Mua xe <?= icon('down', 12) ?></button>
		<ul class="main-nav__list">
			<?= $navLink('bang-gia', 'Bảng giá') ?>
			<?= $navLink('khuyen-mai', 'Khuyến mãi') ?>
			<?= $navLink('dich-vu', 'Dịch vụ') ?>
			<?= $navLink('tin-tuc', 'Tin tức') ?>
			<?= $navLink('lien-he', 'Liên hệ') ?>
		</ul>
		<a class="main-nav__cta" href="<?= e(page_url('lai-thu')) ?>">Đăng ký lái thử</a>
	</div>
</nav>

<div class="dropdown" id="tt-cars" hidden>
	<div class="container dropdown__cars">
		<?php foreach ($navCars as $c) : ?>
			<a href="<?= e(car_url($c)) ?>"><?= e($c['name']) ?> <span aria-hidden="true">›</span></a>
		<?php endforeach; ?>
		<a class="dropdown__all" href="<?= e(page_url('bang-gia')) ?>">Xem bảng giá tất cả xe →</a>
	</div>
</div>

<div class="dropdown" id="tt-mega" hidden>
	<div class="container dropdown__mega">
		<div>
			<div class="dropdown__head">DÒNG XE</div>
			<?php foreach ($navCars as $c) : ?><a href="<?= e(car_url($c)) ?>"><?= e($c['name']) ?></a><?php endforeach; ?>
		</div>
		<div>
			<div class="dropdown__head">MUA XE</div>
			<a href="<?= e(page_url('bang-gia')) ?>">Bảng giá</a>
			<a href="<?= e(page_url('khuyen-mai')) ?>">Khuyến mãi</a>
			<a href="<?= e(page_url('du-tinh')) ?>">Dự tính chi phí lăn bánh</a>
			<a href="<?= e(page_url('tra-gop')) ?>">Tính trả góp</a>
			<a href="<?= e(page_url('lai-thu')) ?>">Đăng ký lái thử</a>
		</div>
		<div>
			<div class="dropdown__head">DỊCH VỤ &amp; TIN TỨC</div>
			<a href="<?= e(page_url('dich-vu')) ?>">Bảo hành, bảo dưỡng, phụ kiện</a>
			<a href="<?= e(page_url('tin-tuc')) ?>">Tin tức &amp; sự kiện</a>
			<a href="<?= e(page_url('lien-he')) ?>">Giới thiệu &amp; liên hệ</a>
			<a href="<?= e(page_url('chinh-sach')) ?>">Chính sách &amp; điều khoản</a>
		</div>
	</div>
</div>

<main id="main" class="site-main">

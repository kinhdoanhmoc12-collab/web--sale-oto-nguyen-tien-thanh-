<?php
$banners = rows('SELECT * FROM banners WHERE active = 1 ORDER BY ord, id');
$allCars = cars();
$META = [
    'title' => opt('home_title'),
    'desc' => opt('home_desc'),
    'canonical' => full_url(),
    'schema' => [
        ['@type' => 'WebSite', '@id' => full_url() . '#website', 'url' => full_url(), 'name' => opt('site_name'), 'inLanguage' => 'vi-VN', 'publisher' => ['@id' => full_url() . '#tu-van-vien']],
        schema_dealer(), schema_person(),
    ],
];
$PRELOAD = $banners ? media($banners[0]['image']) : '';
include __DIR__ . '/partials/header.php';
?>
<section class="slider" aria-roledescription="carousel" aria-label="Banner xe Mitsubishi">
	<h1 class="sr-only">Mitsubishi Hà Nội – Tư vấn mua xe, báo giá lăn bánh và trả góp | <?= e(opt('consultant_name')) ?></h1>
	<?php if ($banners) : ?>
		<div class="slider__track">
			<?php foreach ($banners as $i => $b) : $img = '<img class="slider__img" src="' . eu(media($b['image'])) . '" alt="' . e($b['alt']) . '" width="1920" height="817"' . ($i ? ' loading="lazy" decoding="async"' : ' fetchpriority="high" decoding="async"') . '>'; ?>
				<div class="slider__slide<?= $i ? '' : ' is-active' ?>" aria-hidden="<?= $i ? 'true' : 'false' ?>">
					<?php if ($b['link']) : ?><a href="<?= eu(preg_match('~^(https?:)?//|^#~', $b['link']) ? $b['link'] : url($b['link'])) ?>" tabindex="<?= $i ? '-1' : '0' ?>"><?= $img ?></a><?php else : ?><?= $img ?><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if (count($banners) > 1) : ?>
			<button type="button" class="slider__arrow slider__arrow--prev" aria-label="Banner trước"><?= icon('left', 22, '#fff') ?></button>
			<button type="button" class="slider__arrow slider__arrow--next" aria-label="Banner tiếp theo"><?= icon('right', 22, '#fff') ?></button>
			<div class="slider__dots">
				<?php foreach ($banners as $i => $b) : ?><button type="button" aria-label="Xem banner <?= $i + 1 ?>" aria-pressed="<?= $i ? 'false' : 'true' ?>"><span></span></button><?php endforeach; ?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</section>

<?php partial('consultant'); ?>

<section id="mua-xe" class="section container">
	<div class="title-line"><h2>CÁC DÒNG XE Ô TÔ MITSUBISHI</h2></div>
	<div class="car-grid" data-per="<?= max(1, (int) opt('home_cars_per_page')) ?>">
		<?php foreach ($allCars as $c) { partial('car-card', ['c' => $c]); } ?>
	</div>
	<nav class="car-pager" aria-label="Trang danh sách xe" hidden></nav>
</section>

<section id="tin-tuc" class="section section--grey">
	<div class="container">
		<div class="title-line"><h2>TIN TỨC &amp; ƯU ĐÃI</h2></div>
		<div class="news3">
			<div class="news3__col">
				<a class="news3__img news3__img--sm" href="<?= e(page_url('khuyen-mai')) ?>"><img loading="lazy" decoding="async" src="<?= eu(media(page_data('khuyen-mai')['image'] ?: 'assets/img/bg-khuyenmai.webp')) ?>" alt="Ưu đãi xe Mitsubishi <?= date('Y') ?>" width="380" height="214"></a>
				<a class="news3__link" href="<?= e(page_url('khuyen-mai')) ?>">CHƯƠNG TRÌNH KHUYẾN MÃI THÁNG NÀY →</a>
				<a class="news3__link" href="<?= e(page_url('bang-gia')) ?>">BẢNG GIÁ XE MITSUBISHI MỚI NHẤT →</a>
				<a class="news3__link" href="<?= e(page_url('tra-gop')) ?>">TÍNH TRẢ GÓP: TRẢ TRƯỚC BAO NHIÊU, MỖI THÁNG BAO NHIÊU? →</a>
				<a class="pill pill--outline" href="<?= e(page_url('tin-tuc')) ?>">TIN TỨC &amp; SỰ KIỆN</a>
			</div>
			<div class="news3__col">
				<?php $posts = rows("SELECT * FROM posts WHERE status = 'publish' ORDER BY created_at DESC LIMIT 2"); ?>
				<?php foreach ($posts as $p) : ?>
					<a class="news3__img news3__post" href="<?= e(post_url($p)) ?>"><img loading="lazy" decoding="async" src="<?= eu(media($p['image'] ?: 'assets/img/bg-tintuc.webp')) ?>" alt="" width="380" height="214"><span><?= e($p['title']) ?></span></a>
				<?php endforeach; ?>
				<?php if (count($posts) < 2) : ?>
					<a class="news3__img" href="<?= e(page_url('bang-gia')) ?>"><img loading="lazy" decoding="async" src="<?= e(url('assets/img/bg-banggia.webp')) ?>" alt="Bảng giá xe Mitsubishi" width="380" height="214"></a>
				<?php endif; ?>
				<a class="pill pill--red pill--end" href="<?= e(page_url('bang-gia')) ?>">XEM CÁC DÒNG XE</a>
			</div>
			<div class="news3__col" id="khach-hang">
				<?php foreach (rows('SELECT * FROM deliveries WHERE active = 1 ORDER BY ord, id LIMIT 2') as $d) : ?>
					<a class="news3__img" href="<?= e(page_url('lien-he', '#khach-hang')) ?>"><img class="pos-60" loading="lazy" decoding="async" src="<?= eu(media($d['image'])) ?>" alt="Bàn giao xe Mitsubishi <?= e($d['car']) ?> cho khách hàng tại showroom" width="380" height="507"></a>
				<?php endforeach; ?>
				<a class="pill pill--red" href="<?= e(page_url('lien-he', '#khach-hang')) ?>">TRẢI NGHIỆM KHÁCH HÀNG</a>
			</div>
		</div>
	</div>
</section>

<section id="dang-ky" class="quote">
	<a class="quote__img" href="<?= e($allCars ? car_url($allCars[0]) : page_url('bang-gia')) ?>"><img loading="lazy" decoding="async" src="<?= eu(media(opt('form_image'))) ?>" alt="Mitsubishi – Nhận báo giá và đăng ký lái thử" width="560" height="420"></a>
	<div class="quote__form">
		<?php partial('lead-form', ['source' => 'trang-chu', 'title' => 'NHẬN BÁO GIÁ & ĐĂNG KÝ LÁI THỬ', 'skin' => 'onred', 'fields' => ['pay', 'car', 'region'], 'button' => 'GỬI YÊU CẦU NGAY']); ?>
	</div>
</section>
<?php include __DIR__ . '/partials/footer.php';

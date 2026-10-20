<?php
$META = page_meta($page, 'khuyen-mai');
include __DIR__ . '/partials/header.php';
page_hero_from($page, 'assets/img/bg-khuyenmai.webp', ['align' => 'right']);
$ph = opt('phone');
$generic = ['Ưu đãi lệ phí trước bạ hoặc tiền mặt theo chương trình từng tháng', 'Quà tặng phụ kiện chính hãng (tùy phiên bản)', 'Hỗ trợ vay ngân hàng lãi suất ưu đãi'];
?>
<section class="container section">
	<div class="updated updated--top"><?= updated_line() ?></div>
	<?php if (trim(strip_tags((string) $page['content'], '<img>')) !== '') : ?><div class="entry section-bottom"><?= render_content($page['content']) ?></div><?php endif; ?>
	<div class="promo-list">
		<?php foreach (cars() as $c) : $own = lines($c['promos']); ?>
			<article class="promo">
				<a class="promo__img" href="<?= e(car_url($c)) ?>"><?= car_image($c) ?></a>
				<div class="promo__body">
					<h2>Mitsubishi <?= e($c['name']) ?></h2>
					<ul class="checks"><?php foreach ($own ?: $generic as $l) : ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
					<?php if (!$own) : ?><div class="promo__note">Mức ưu đãi cụ thể thay đổi theo tháng – gọi để được báo chính xác</div><?php endif; ?>
					<div class="btn-row">
						<a class="btn btn--red btn--sm" href="tel:<?= e(tel($ph)) ?>">NHẬN ƯU ĐÃI</a>
						<a class="btn btn--outline btn--sm" href="<?= e(car_url($c)) ?>">XEM XE</a>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
<?php include __DIR__ . '/partials/footer.php';

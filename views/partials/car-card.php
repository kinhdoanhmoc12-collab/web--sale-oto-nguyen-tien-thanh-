<?php
$u = car_url($c);
$price = car_price($c);
$specs = car_specs($c);
?>
<article class="car-card">
	<a class="car-card__img" href="<?= e($u) ?>"><?= car_image($c) ?></a>
	<h3 class="car-card__name"><a href="<?= e($u) ?>">MITSUBISHI <?= e(upper($c['name'])) ?></a></h3>
	<div class="car-card__price">GIÁ TỪ: <?= $price ? e(money($price)) . ' VNĐ' : 'Liên hệ' ?></div>
	<div class="car-card__btns">
		<a class="btn btn--red btn--sm" href="#dang-ky" data-car="<?= e($c['name']) ?>">ĐĂNG KÝ TƯ VẤN</a>
		<a class="btn btn--outline btn--sm" href="<?= e($u) ?>">CHI TIẾT</a>
	</div>
	<dl class="spec-list">
		<?php foreach (['Phiên bản', 'Bảo hành', 'Động cơ', 'Số chỗ ngồi'] as $k) : ?>
			<?php if (!empty($specs[$k])) : $o = in_array($k, ['Bảo hành', 'Động cơ'], true) ? ' class="opt"' : ''; ?><dt<?= $o ?>><?= e($k) ?></dt><dd<?= $o ?>><?= e($specs[$k]) ?></dd><?php endif; ?>
		<?php endforeach; ?>
		<?php if ($c['origin']) : ?><dt class="opt">Sản xuất</dt><dd class="opt"><?= e(preg_replace('/^Nhập khẩu\s+/u', '', $c['origin'])) ?></dd><?php endif; ?>
	</dl>
</article>

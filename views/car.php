<?php
/**
 * Trang chi tiết xe – bố cục giống trang sản phẩm của hãng:
 * chọn phiên bản → chi tiết phiên bản → màu sắc → thông số → điểm nổi bật → phụ kiện → đăng ký lái thử.
 * Nội dung sửa ở Quản trị → Dòng xe → (tên xe) → "Trang chi tiết xe".
 *
 * @var array $car
 */
$name = $car['name'];
$up = upper($name);
$ph = opt('phone');
$priceFrom = car_price($car);
$prices = [];
foreach (car_versions($car) as $v) {
    $prices[mb_strtolower(trim($v['name']))] = $v['price'];
}

/* ---------- Dữ liệu trang chi tiết (JSON trong cột detail) ---------- */
$D = json_decode((string) ($car['detail'] ?? ''), true);
$D = is_array($D) ? $D : [];
$carImg = $car['image'] ?? '';

$vers = [];
foreach (($D['versions'] ?? []) as $v) {
    if (trim((string) ($v['name'] ?? '')) === '') {
        continue;
    }
    $vers[] = $v;
}
if (!$vers) { // chưa nhập chi tiết: lấy từ ô "Phiên bản & giá"
    foreach (car_versions($car) as $v) {
        $vers[] = ['name' => $v['name'], 'desc' => '', 'image' => '', 'equipment' => '', 'colors' => []];
    }
}
foreach ($vers as &$v) {
    $v['price'] = $prices[mb_strtolower(trim($v['name']))] ?? '';
    $v['img'] = ($v['image'] ?? '') ?: $carImg;
    $v['eq'] = lines($v['equipment'] ?? '');
    $v['colors'] = array_values(array_filter($v['colors'] ?? [], fn($c) => trim((string) ($c['name'] ?? '')) !== '' || trim((string) ($c['hex'] ?? '')) !== ''));
}
unset($v);
$hasColors = (bool) array_filter($vers, fn($v) => $v['colors']);

$specGroups = [];
foreach (($D['specs'] ?? []) as $g) {
    $rowsS = [];
    foreach (lines($g['rows'] ?? '') as $line) {
        $parts = array_map('trim', explode('|', $line));
        $rowsS[] = ['label' => array_shift($parts), 'values' => $parts];
    }
    if ($rowsS) {
        $specGroups[] = ['title' => $g['title'] ?? '', 'rows' => $rowsS];
    }
}
if (!$specGroups) { // chưa nhập: dùng thông số cơ bản của xe
    $rowsS = [];
    foreach (car_specs($car) as $k => $val) {
        $rowsS[] = ['label' => $k, 'values' => [$val]];
    }
    $specGroups[] = ['title' => 'Thông số chính', 'rows' => $rowsS];
}

$hl = $D['highlight'] ?? [];
if (empty($hl['title'])) {
    $hl = [
        'image' => '',
        'kicker' => 'ĐẶC ĐIỂM NỔI BẬT',
        'title' => 'MITSUBISHI ' . $up,
        'desc' => $car['excerpt'] ?: ($car['segment'] ? "Khám phá phong cách và tiện nghi vượt trội của dòng xe $name." : "SUV hiện đại, đẳng cấp dành cho gia đình."),
    ];
}
$feats = array_values(array_filter($D['features'] ?? [], fn($f) => trim((string) ($f['title'] ?? '')) !== ''));
if (!$feats) { // dùng 4 ô mô tả cũ nếu có
    foreach (['feat_exterior' => 'NGOẠI THẤT', 'feat_interior' => 'NỘI THẤT', 'feat_drive' => 'VẬN HÀNH', 'feat_safety' => 'AN TOÀN'] as $k => $t) {
        if (trim((string) ($car[$k] ?? '')) !== '') {
            $feats[] = ['image' => '', 'kicker' => $up, 'title' => $t, 'desc' => $car[$k]];
        }
    }
}
$acc = $D['accessories'] ?? [];
$accImg = trim((string) ($acc['image'] ?? ''));
$accItems = array_values(array_filter($acc['items'] ?? [], fn($i) => !empty($i['image'])));
$hasAcc = $accImg !== '' || !empty($accItems);
$partner = $D['partner'] ?? [];

/* ---------- SEO ---------- */
$offers = [];
foreach ($vers as $v) {
    if ($v['price']) {
        $offers[] = ['@type' => 'Offer', 'name' => "Mitsubishi $name {$v['name']}", 'price' => (int) $v['price'], 'priceCurrency' => 'VND', 'availability' => 'https://schema.org/InStock', 'itemCondition' => 'https://schema.org/NewCondition', 'url' => full_url($car['slug'] . '/'), 'seller' => ['@id' => full_url() . '#dealer']];
    }
}
$product = ['@type' => ['Product', 'Car'], '@id' => full_url($car['slug'] . '/') . '#xe', 'name' => "Mitsubishi $name", 'brand' => ['@type' => 'Brand', 'name' => 'Mitsubishi'], 'model' => $name, 'vehicleSeatingCapacity' => (int) $car['seats'], 'vehicleEngine' => ['@type' => 'EngineSpecification', 'name' => $car['engine']], 'itemCondition' => 'https://schema.org/NewCondition', 'url' => full_url($car['slug'] . '/')];
if ($carImg) { $product['image'] = media_full($carImg); }
if (count($offers) > 1) {
    $product['offers'] = ['@type' => 'AggregateOffer', 'lowPrice' => min(array_column($offers, 'price')), 'highPrice' => max(array_column($offers, 'price')), 'priceCurrency' => 'VND', 'offerCount' => count($offers), 'offers' => $offers];
} elseif ($offers) {
    $product['offers'] = $offers[0];
}
$desc = $car['excerpt'] ?: "Mitsubishi $name giá niêm yết từ " . ($priceFrom ? price_short($priceFrom) : 'liên hệ') . '. Xem phiên bản, màu sắc, thông số, giá lăn bánh và ưu đãi mới nhất. Tư vấn: ' . $ph . '.';
$product['description'] = $desc;
$META = [
    'title' => "Giá xe Mitsubishi $name " . date('Y') . ' – Lăn bánh, thông số, ưu đãi',
    'desc' => $desc, 'canonical' => full_url($car['slug'] . '/'), 'type' => 'product',
    'image' => $carImg ? media_full($carImg) : full_url('assets/img/og-image.jpg'),
    'schema' => [$product, breadcrumb([["Mitsubishi $name", full_url($car['slug'] . '/')]])],
];
$v_cd = @filemtime(ROOT . '/assets/css/car-detail.css');
$EXTRA_HEAD = '<link rel="stylesheet" href="' . e(url('assets/css/car-detail.css')) . '?v=' . $v_cd . '">' . "\n"
    . '<script src="' . e(url('assets/js/car-detail.js')) . '?v=' . @filemtime(ROOT . '/assets/js/car-detail.js') . '" defer></script>';
include __DIR__ . '/partials/header.php';

$img = function (string $src, string $alt, string $class = '', bool $lazy = true) use ($name): string {
    if ($src === '') {
        return '<span class="cd-ph ' . e($class) . '"><span>MITSUBISHI</span><strong>' . e(upper($name)) . '</strong></span>';
    }
    return '<img class="' . e($class) . '" src="' . eu(media($src)) . '" alt="' . e($alt) . '"' . ($lazy ? ' loading="lazy" decoding="async"' : ' fetchpriority="high"') . '>';
};
$money = fn($p) => $p ? money($p) . ' VNĐ' : 'Liên hệ';
$learn = function (string $label = 'TÌM HIỂU THÊM') use ($name): string {
    return '<a class="cd-btn" href="#lai-thu" data-popup data-car="' . e($name) . '">' . e($label) . '</a>';
};
?>
<div class="cd" data-car="<?= e($name) ?>">

<!-- 1. Chọn phiên bản -->
<section class="cd-picker">
	<div class="cd-wrap">
		<div class="cd-picker__head">
			<?php if ($car['segment']) : ?><div class="cd-kicker"><?= e($car['segment']) ?></div><?php endif; ?>
			<h1 class="cd-picker__title">MITSUBISHI <?= e($up) ?></h1>
			<div class="cd-picker__from">Giá niêm yết từ <strong><?= e($money($priceFrom)) ?></strong></div>
		</div>
		<div class="cd-picker__list cd-picker__list--<?= max(min(count($vers), 4), 1) ?>" role="tablist" aria-label="Chọn phiên bản">
			<?php foreach ($vers as $i => $v) : ?>
				<button type="button" class="cd-pick<?= $i ? '' : ' is-on' ?>" data-v="<?= $i ?>" role="tab" aria-selected="<?= $i ? 'false' : 'true' ?>">
					<span class="cd-pick__img"><?= $img($v['img'], "Mitsubishi $name {$v['name']}", '', $i > 0) ?></span>
					<span class="cd-pick__name"><?= e(upper($v['name'])) ?></span>
					<span class="cd-pick__price">Giá: <?= e($money($v['price'])) ?></span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- 2. Chi tiết từng phiên bản -->
<section class="cd-version" id="phien-ban">
	<div class="cd-version__text">
		<div class="cd-tabs" role="tablist">
			<?php foreach ($vers as $i => $v) : ?>
				<button type="button" class="cd-tab<?= $i ? '' : ' is-on' ?>" data-v="<?= $i ?>"><?= e(upper($v['name'])) ?></button>
			<?php endforeach; ?>
		</div>
		<?php foreach ($vers as $i => $v) : ?>
			<div class="cd-pane" data-v="<?= $i ?>"<?= $i ? ' hidden' : '' ?>>
				<h2 class="cd-pane__name"><?= e($v['name']) ?></h2>
				<?php if (!empty($v['desc'])) : ?><p class="cd-pane__desc"><?= e($v['desc']) ?></p><?php endif; ?>
				<div class="cd-pane__price">Giá: <?= e($money($v['price'])) ?></div>
				<div class="cd-pane__btns">
					<a class="btn btn--red" href="#lai-thu" data-popup data-car="<?= e($name) ?>">NHẬN BÁO GIÁ LĂN BÁNH</a>
					<a class="btn btn--outline" href="#lai-thu">ĐĂNG KÝ LÁI THỬ</a>
				</div>
				<?php $eq = $v['eq'] ?: array_map(fn($k, $val) => "$k: $val", array_keys(car_specs($car)), car_specs($car)); ?>
				<h3 class="cd-pane__sub"><?= $v['eq'] ? 'Trang thiết bị' : 'Thông số chính' ?></h3>
				<ul class="cd-eq<?= count($eq) > 8 ? ' is-clamped' : '' ?>">
					<?php foreach ($eq as $line) : ?><li><?= e($line) ?></li><?php endforeach; ?>
				</ul>
				<?php if (count($eq) > 8) : ?><button type="button" class="cd-more" data-more>Xem tất cả <?= count($eq) ?> trang bị ▾</button><?php endif; ?>
				<div class="cd-links">
					<a href="<?= e(page_url('du-tinh')) ?>?xe=<?= (int) $car['id'] ?>">Dự tính chi phí lăn bánh →</a>
					<a href="<?= e(page_url('tra-gop')) ?>?xe=<?= (int) $car['id'] ?>">Tính trả góp →</a>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="cd-version__img">
		<?php foreach ($vers as $i => $v) : ?>
			<div class="cd-version__pic" data-v="<?= $i ?>"<?= $i ? ' hidden' : '' ?>><?= $img($v['img'], "Mitsubishi $name {$v['name']}") ?></div>
		<?php endforeach; ?>
	</div>
</section>

<?php if ($hasColors) : ?>
<!-- 3. Màu sắc -->
<section class="cd-colors">
	<div class="cd-wrap">
		<h2 class="cd-h2">Màu sắc</h2>
		<div class="cd-colors__grid cd-colors__grid--1">
			<?php foreach ($vers as $i => $v) : $c0 = $v['colors'][0] ?? []; ?>
				<div class="cd-color" data-v="<?= $i ?>"<?= $i ? ' hidden' : '' ?>>
					<div class="cd-color__img"><?= $img(($c0['image'] ?? '') ?: $v['img'], "Mitsubishi $name {$v['name']} màu " . ($c0['name'] ?? '')) ?></div>
					<div class="cd-color__name"><?= e($v['name']) ?></div>
					<div class="cd-color__price">Giá: <?= e($money($v['price'])) ?></div>
					<div class="cd-swatches" role="radiogroup" aria-label="Màu xe <?= e($v['name']) ?>">
						<?php foreach ($v['colors'] as $k => $c) : ?>
							<button type="button" class="cd-swatch<?= $k ? '' : ' is-on' ?>" style="--c:<?= e(preg_match('/^#[0-9a-f]{3,8}$/i', (string) ($c['hex'] ?? '')) ? $c['hex'] : '#cccccc') ?>" data-img="<?= eu(media(($c['image'] ?? '') ?: $v['img'])) ?>" data-name="<?= e($c['name'] ?? '') ?>" aria-label="<?= e($c['name'] ?? '') ?>" aria-checked="<?= $k ? 'false' : 'true' ?>" role="radio"></button>
						<?php endforeach; ?>
					</div>
					<div class="cd-color__label"><?= e($c0['name'] ?? 'Liên hệ để biết màu có sẵn') ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- 4. Thông số kỹ thuật -->
<section class="cd-specs" id="thong-so">
	<div class="cd-wrap">
		<h2 class="cd-h2">Thông số kỹ thuật</h2>
		<div class="cd-specs__head" style="--n:<?= max(count($vers), 1) ?>">
			<span></span>
			<?php foreach ($vers as $v) : ?><div><strong><?= e($v['name']) ?></strong><small>Giá: <?= e($money($v['price'])) ?></small></div><?php endforeach; ?>
		</div>
		<?php foreach ($specGroups as $gi => $g) : ?>
			<details class="cd-acc"<?= $gi === 0 ? ' open' : '' ?>>
				<summary><?= e(upper($g['title'])) ?></summary>
				<div class="cd-acc__body">
					<?php foreach ($g['rows'] as $r) : ?>
						<div class="cd-row" style="--n:<?= max(count($vers), 1) ?>">
							<div class="cd-row__label"><?= e($r['label']) ?></div>
							<?php if (count($r['values']) <= 1) : ?>
								<div class="cd-row__val cd-row__val--all"><?= e($r['values'][0] ?? '') ?></div>
							<?php else : ?>
								<?php foreach ($vers as $vi => $v) : ?><div class="cd-row__val" data-label="<?= e($v['name']) ?>"><?= e($r['values'][$vi] ?? '—') ?></div><?php endforeach; ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</details>
		<?php endforeach; ?>
		<button type="button" class="cd-collapse" data-collapse>THU GỌN</button>
	</div>
</section>

<?php if (!empty($hl['title'])) : ?>
<!-- 5. Đặc điểm nổi bật -->
<section class="cd-hl<?= empty($hl['image']) ? ' cd-hl--noimg' : '' ?>">
	<div class="cd-hl__img"><?= $img(($hl['image'] ?? '') ?: $carImg, "Mitsubishi $name") ?></div>
	<div class="cd-hl__box">
		<?php if (!empty($hl['kicker'])) : ?><div class="cd-kicker cd-kicker--light"><?= e($hl['kicker']) ?></div><?php endif; ?>
		<h2><?= e($hl['title']) ?></h2>
		<?php if (!empty($hl['desc'])) : ?><p><?= e($hl['desc']) ?></p><?php endif; ?>
		<a class="cd-btn cd-btn--light" href="#lai-thu" data-popup data-car="<?= e($name) ?>">KHÁM PHÁ NGAY</a>
	</div>
</section>
<?php endif; ?>

<?php if ($feats) : ?>
<!-- 6. Các điểm nổi bật (xen kẽ ảnh – chữ) -->
<section class="cd-feats">
	<?php foreach ($feats as $i => $f) : ?>
		<div class="cd-feat<?= $i % 2 ? ' cd-feat--rev' : '' ?>">
			<div class="cd-feat__img"><?= $img(($f['image'] ?? '') ?: $carImg, $f['title'], empty($f['image']) ? 'is-fallback' : '') ?></div>
			<div class="cd-feat__text">
				<?php if (!empty($f['kicker'])) : ?><div class="cd-kicker"><?= e($f['kicker']) ?></div><?php endif; ?>
				<h2><?= e($f['title']) ?></h2>
				<?php if (!empty($f['desc'])) : ?><p><?= nl2br(e($f['desc'])) ?></p><?php endif; ?>
				<?= $learn() ?>
			</div>
		</div>
	<?php endforeach; ?>
</section>
<?php endif; ?>

<?php if ($hasAcc) : ?>
<!-- 7. Phụ kiện chính hãng -->
<section class="cd-accs">
	<div class="cd-wrap cd-accs__inner">
		<div class="cd-accs__box">
			<h2><?= e(($acc['title'] ?? '') ?: 'PHỤ KIỆN CHÍNH HÃNG') ?></h2>
			<?php if (!empty($acc['desc'])) : ?><p><?= e($acc['desc']) ?></p><?php endif; ?>
			<a class="cd-btn cd-btn--light" href="#lai-thu" data-popup data-car="<?= e($name) ?>">HỎI GIÁ PHỤ KIỆN</a>
		</div>
		<?php if (!empty($accItems)) : ?>
			<div class="cd-accs__grid">
				<?php foreach ($accItems as $it) : ?>
					<figure class="cd-acc-item">
						<div class="cd-acc-item__img"><?= $img($it['image'], $it['name'] ?? 'Phụ kiện chính hãng') ?></div>
						<?php if (!empty($it['name'])) : ?><figcaption><?= e(upper($it['name'])) ?></figcaption><?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php elseif (!empty($accImg)) : ?>
			<div class="cd-accs__visual">
				<img src="<?= eu(media($accImg)) ?>" alt="<?= e(($acc['title'] ?? 'Phụ kiện chính hãng') . " Mitsubishi $name") ?>" loading="lazy" decoding="async">
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<?php if (!empty($partner['title'])) : ?>
<!-- 8. Phụ kiện bên thứ 3 -->
<section class="cd-feats">
	<div class="cd-feat cd-feat--rev">
		<div class="cd-feat__img"><?= $img(($partner['image'] ?? '') ?: $carImg, $partner['title'], empty($partner['image']) ? 'is-fallback' : '') ?></div>
		<div class="cd-feat__text">
			<?php if (!empty($partner['kicker'])) : ?><div class="cd-kicker"><?= e($partner['kicker']) ?></div><?php endif; ?>
			<h2><?= e($partner['title']) ?></h2>
			<?php if (!empty($partner['desc'])) : ?><p><?= e($partner['desc']) ?></p><?php endif; ?>
			<?= $learn() ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if (trim(strip_tags((string) $car['content'], '<img>')) !== '') : ?>
<section class="cd-wrap cd-intro"><div class="entry"><?= render_content($car['content']) ?></div></section>
<?php endif; ?>

<!-- 9. Đăng ký lái thử -->
<section class="cd-form" id="lai-thu">
	<div class="cd-wrap cd-form__inner">
		<h2 class="cd-h2">Đăng ký lái thử</h2>
		<p class="cd-form__sub">Để lại thông tin, chuyên viên <?= e(opt('consultant_name')) ?> sẽ gọi lại sắp xếp lịch lái thử và gửi báo giá lăn bánh <?= e($name) ?>.</p>
		<?php partial('lead-form', ['source' => 'trang-xe', 'skin' => 'light', 'fields' => $vers ? ['version', 'date', 'region'] : ['car', 'date', 'region'], 'button' => 'ĐĂNG KÝ LÁI THỬ', 'purpose' => 'sắp xếp lái thử và báo giá', 'car' => $name, 'versions' => array_map(fn($v) => "$up {$v['name']}", $vers)]); ?>
	</div>
	<span id="bao-gia"></span>
</section>

</div>
<?php include __DIR__ . '/partials/footer.php';

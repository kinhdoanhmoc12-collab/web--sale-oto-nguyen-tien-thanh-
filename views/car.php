<?php
/** @var array $car */
$name = $car['name'];
$up = upper($name);
$price = car_price($car);
$versions = car_versions($car);
$specs = car_specs($car);
$ph = opt('phone');
$offers = [];
foreach ($versions as $v) {
    if ($v['price']) {
        $offers[] = ['@type' => 'Offer', 'name' => "Mitsubishi $name {$v['name']}", 'price' => (int) $v['price'], 'priceCurrency' => 'VND', 'availability' => 'https://schema.org/InStock', 'itemCondition' => 'https://schema.org/NewCondition', 'url' => full_url($car['slug'] . '/'), 'seller' => ['@id' => full_url() . '#dealer']];
    }
}
$product = ['@type' => ['Product', 'Car'], '@id' => full_url($car['slug'] . '/') . '#xe', 'name' => "Mitsubishi $name", 'brand' => ['@type' => 'Brand', 'name' => 'Mitsubishi'], 'model' => $name, 'vehicleSeatingCapacity' => (int) $car['seats'], 'vehicleEngine' => ['@type' => 'EngineSpecification', 'name' => $car['engine']], 'itemCondition' => 'https://schema.org/NewCondition', 'url' => full_url($car['slug'] . '/')];
if ($car['image']) { $product['image'] = media_full($car['image']); }
if (count($offers) > 1) {
    $product['offers'] = ['@type' => 'AggregateOffer', 'lowPrice' => min(array_column($offers, 'price')), 'highPrice' => max(array_column($offers, 'price')), 'priceCurrency' => 'VND', 'offerCount' => count($offers), 'offers' => $offers];
} elseif ($offers) {
    $product['offers'] = $offers[0];
}
$desc = $car['excerpt'] ?: "Mitsubishi $name giá niêm yết từ " . ($price ? price_short($price) : 'liên hệ') . '. Xem phiên bản, thông số, giá lăn bánh Hà Nội và ưu đãi mới nhất. Tư vấn: ' . $ph . '.';
$product['description'] = $desc;
$META = [
    'title' => "Giá xe Mitsubishi $name " . date('Y') . ' – Lăn bánh, thông số, ưu đãi',
    'desc' => $desc, 'canonical' => full_url($car['slug'] . '/'), 'type' => 'product',
    'image' => $car['image'] ? media_full($car['image']) : full_url('assets/img/og-image.jpg'),
    'schema' => [$product, breadcrumb([["Mitsubishi $name", full_url($car['slug'] . '/')]])],
];
include __DIR__ . '/partials/header.php';
?>
<section class="container car-hero">
	<div class="car-hero__img"><?= car_image($car) ?></div>
	<div class="car-hero__body">
		<?php if ($car['segment']) : ?><div class="kicker"><?= e($car['segment']) ?></div><?php endif; ?>
		<h1 class="car-hero__title">MITSUBISHI <?= e($up) ?></h1>
		<div class="car-hero__price">Giá niêm yết từ <strong><?= $price ? e(money($price)) . ' VNĐ' : 'Liên hệ' ?></strong></div>
		<div class="car-hero__note">Giá có thể thay đổi theo từng thời điểm – liên hệ để nhận giá lăn bánh và ưu đãi mới nhất.<br>Cập nhật: <time datetime="<?= e(substr((string) $car['updated_at'], 0, 10)) ?>"><?= e(vn_date($car['updated_at'])) ?></time> · Tư vấn: <a href="<?= e(page_url('tac-gia')) ?>"><?= e(opt('consultant_name')) ?></a></div>
		<div class="btn-row">
			<a class="btn btn--red btn--lg" href="#bao-gia">NHẬN BÁO GIÁ LĂN BÁNH</a>
			<a class="btn btn--outline btn--lg" href="<?= e(page_url('lai-thu')) ?>?xe=<?= e(rawurlencode($name)) ?>">ĐĂNG KÝ LÁI THỬ</a>
		</div>
		<div class="link-row">
			<a href="<?= e(page_url('du-tinh')) ?>?xe=<?= (int) $car['id'] ?>">Dự tính chi phí lăn bánh →</a>
			<a href="<?= e(page_url('tra-gop')) ?>?xe=<?= (int) $car['id'] ?>">Tính trả góp →</a>
		</div>
	</div>
</section>

<?php if ($versions) : ?>
<section class="section section--light">
	<div class="container">
		<h2 class="h-red">PHIÊN BẢN &amp; GIÁ NIÊM YẾT</h2>
		<div class="table-wrap">
			<table class="table">
				<thead><tr><th>Phiên bản</th><th>Giá niêm yết (VNĐ)</th><th><span class="sr-only">Hành động</span></th></tr></thead>
				<tbody>
				<?php foreach ($versions as $v) : ?>
					<tr><td><strong><?= e("$up {$v['name']}") ?></strong></td><td><?= $v['price'] ? e(money($v['price'])) : 'Liên hệ' ?></td><td class="ta-r"><a href="#bao-gia" data-version="<?= e("$up {$v['name']}") ?>">Nhận giá lăn bánh</a></td></tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="container section two-col">
	<div>
		<h2 class="h-red">THÔNG SỐ CHÍNH</h2>
		<dl class="spec-list spec-list--wide"><?php foreach ($specs as $k => $v) : ?><dt><?= e($k) ?></dt><dd><?= e($v) ?></dd><?php endforeach; ?></dl>
	</div>
	<div>
		<h2 class="h-red">MÀU SẮC</h2>
		<p class="muted">Liên hệ để xem bảng màu thực tế của <?= e($name) ?> và màu xe đang có sẵn tại showroom.</p>
		<a class="btn btn--red" href="tel:<?= e(tel($ph)) ?>">HỎI MÀU XE CÓ SẴN: <?= e($ph) ?></a>
	</div>
</section>

<?php if (trim(strip_tags((string) $car['content'], '<img>')) !== '') : ?>
<section class="container section"><div class="entry"><?= render_content($car['content']) ?></div></section>
<?php endif; ?>

<section class="features">
	<div class="container features__grid">
		<?php foreach (['feat_exterior' => ['NGOẠI THẤT', 'ngoại thất'], 'feat_interior' => ['NỘI THẤT', 'nội thất'], 'feat_drive' => ['VẬN HÀNH', 'khả năng vận hành'], 'feat_safety' => ['AN TOÀN', 'trang bị an toàn']] as $k => $f) : ?>
			<div class="feature"><h3><?= $f[0] ?></h3><p><?= $car[$k] ? nl2br(e($car[$k])) : e("Liên hệ để được giới thiệu chi tiết {$f[1]} của $name và trải nghiệm thực tế tại showroom.") ?></p></div>
		<?php endforeach; ?>
	</div>
</section>

<section id="bao-gia" class="quote-band">
	<div class="container container--narrow">
		<?php partial('lead-form', ['source' => 'trang-xe', 'title' => "NHẬN BÁO GIÁ LĂN BÁNH $up", 'skin' => 'red', 'fields' => $versions ? ['version', 'region'] : ['car', 'region'], 'car' => $name, 'versions' => array_map(fn($v) => "$up {$v['name']}", $versions)]); ?>
	</div>
</section>
<?php include __DIR__ . '/partials/footer.php';

<?php
$items = [];
foreach (cars() as $i => $c) { $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'url' => full_url($c['slug'] . '/'), 'name' => 'Mitsubishi ' . $c['name']]; }
$META = page_meta($page, 'bang-gia', [['@type' => 'ItemList', 'name' => $page['title'], 'itemListElement' => $items]]);
include __DIR__ . '/partials/header.php';
page_hero_from($page, 'assets/img/bg-banggia.webp');
$ph = opt('phone');
?>
<section class="container section">
	<div class="table-wrap">
		<table class="table table--price">
			<thead><tr><th>Dòng xe</th><th>Phiên bản</th><th>Số chỗ</th><th>Giá niêm yết từ (VNĐ)</th><th><span class="sr-only">Liên kết</span></th></tr></thead>
			<tbody>
			<?php foreach (cars() as $c) : $p = car_price($c); ?>
				<tr>
					<td><a class="strong" href="<?= e(car_url($c)) ?>">Mitsubishi <?= e($c['name']) ?></a></td>
					<td data-label="Phiên bản"><?= e(implode(', ', array_column(car_versions($c), 'name'))) ?></td>
					<td data-label="Số chỗ"><?= e(ltrim((string) $c['seats'], '0')) ?></td>
					<td class="strong red" data-label="Giá từ (VNĐ)"><?= $p ? e(money($p)) : 'Liên hệ' ?></td>
					<td class="table__links"><a href="<?= e(car_url($c)) ?>">Chi tiết</a><a href="<?= e(page_url('du-tinh')) ?>?xe=<?= (int) $c['id'] ?>">Giá lăn bánh</a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div class="updated"><?= updated_line() ?></div>
	<div class="cta-box">
		<div>Muốn biết <strong>giá lăn bánh</strong> và <strong>ưu đãi tháng này</strong> cho đúng phiên bản anh/chị chọn?</div>
		<div class="btn-row">
			<a class="btn btn--red" href="tel:<?= e(tel($ph)) ?>">GỌI <?= e($ph) ?></a>
			<a class="btn btn--outline" href="<?= e(page_url('du-tinh')) ?>">TỰ DỰ TÍNH CHI PHÍ</a>
		</div>
	</div>
	<?php if (trim(strip_tags((string) $page['content'])) !== '') : ?><div class="entry section-top"><?= render_content($page['content']) ?></div><?php endif; ?>
</section>
<?php include __DIR__ . '/partials/footer.php';

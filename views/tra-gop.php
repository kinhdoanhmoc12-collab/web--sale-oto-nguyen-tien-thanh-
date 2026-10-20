<?php
$META = page_meta($page, 'tra-gop');
include __DIR__ . '/partials/header.php';
page_hero_from($page, 'assets/img/banner-xpandercross.webp', ['align' => 'center']);
$ph = opt('phone');
$sel = (int) ($_GET['xe'] ?? 0);
$chips = function (string $name, string $csv, string $suffix, $default) {
    $vals = array_values(array_filter(array_map('trim', explode(',', $csv)), 'strlen'));
    $hasDef = in_array((string) $default, $vals, true);
    echo '<div class="chips" data-chips="' . e($name) . '">';
    foreach ($vals as $i => $v) {
        $on = $hasDef ? ((string) $v === (string) $default) : $i === 0;
        echo '<button type="button" class="chip" data-value="' . e($v) . '" aria-pressed="' . ($on ? 'true' : 'false') . '">' . e($v . $suffix) . '</button>';
    }
    echo '</div>';
};
?>
<section class="container section calc" data-calc="loan" data-selected="<?= $sel ?>">
	<div class="calc__inputs">
		<div class="calc__group"><h2>1. Chọn dòng xe</h2><div class="chips" data-chips="car"><?php foreach (cars() as $c) : ?><button type="button" class="chip" data-value="<?= (int) $c['id'] ?>" aria-pressed="false"><?= e($c['name']) ?></button><?php endforeach; ?></div></div>
		<div class="calc__group"><h2>2. Trả trước</h2><?php $chips('down', opt('loan_downs'), '%', '30'); ?></div>
		<div class="calc__group"><h2>3. Thời hạn vay</h2><?php $chips('years', opt('loan_years'), ' năm', '5'); ?></div>
		<div class="calc__group"><h2>4. Lãi suất (%/năm)</h2><?php $chips('rate', opt('loan_rates'), '%', '8'); ?><p class="small muted">Lãi suất để tính thử. Mức thực tế tùy ngân hàng, gói vay MAF và hồ sơ của anh/chị – liên hệ để được báo chính xác.</p></div>
	</div>
	<div class="result">
		<div class="result__kicker" data-out="title">KẾT QUẢ</div>
		<div class="result__label">Trả hằng tháng (tháng đầu)</div>
		<div class="result__big" data-out="first">—</div>
		<div class="result__label" data-out="last"></div>
		<div class="result__rows" data-out="rows"></div>
		<div class="result__small">Trả trước tính trên giá niêm yết, chưa gồm chi phí lăn bánh. Cách tính dư nợ giảm dần. Cập nhật: <time datetime="<?= e(updated_iso()) ?>"><?= e(opt('updated_date')) ?></time>.</div>
		<a class="btn btn--red btn--block" href="tel:<?= e(tel($ph)) ?>">TƯ VẤN HỒ SƠ VAY: <?= e($ph) ?></a>
		<a class="result__link" data-out="feelink" href="<?= e(page_url('du-tinh')) ?>">Xem chi phí lăn bánh →</a>
	</div>
</section>
<section class="section section--light">
	<div class="container info-cards">
		<div class="info-card"><h3>Hồ sơ cá nhân</h3><p>CCCD, giấy xác nhận tình trạng hôn nhân, chứng minh thu nhập (sao kê lương, hợp đồng lao động…). Danh sách chính xác theo yêu cầu của ngân hàng.</p></div>
		<div class="info-card"><h3>Chương trình trả góp MAF</h3><p><?= e(opt('maf_text')) ?> Liên hệ <a href="tel:<?= e(tel($ph)) ?>"><?= e($ph) ?></a>.</p></div>
	</div>
	<?php if (trim(strip_tags((string) $page['content'])) !== '') : ?><div class="container section-top"><div class="entry"><?= render_content($page['content']) ?></div></div><?php endif; ?>
</section>
<?php include __DIR__ . '/partials/footer.php';

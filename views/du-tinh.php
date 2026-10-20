<?php
$META = page_meta($page, 'du-tinh');
include __DIR__ . '/partials/header.php';
page_hero_from($page, 'assets/img/bg-dichvu.webp', ['align' => 'center']);
$ph = opt('phone');
$sel = (int) ($_GET['xe'] ?? 0);
?>
<section class="container section calc" data-calc="fees" data-selected="<?= $sel ?>">
	<div class="calc__inputs">
		<div class="calc__group">
			<h2>1. Chọn dòng xe</h2>
			<div class="chips" data-chips="car"><?php foreach (cars() as $c) : ?><button type="button" class="chip" data-value="<?= (int) $c['id'] ?>" aria-pressed="false"><?= e($c['name']) ?></button><?php endforeach; ?></div>
		</div>
		<div class="calc__group">
			<h2>2. Nơi đăng ký xe</h2>
			<div class="chips" data-chips="region">
				<button type="button" class="chip" data-value="hn" aria-pressed="true">Hà Nội</button>
				<button type="button" class="chip" data-value="hcm" aria-pressed="false">TP. Hồ Chí Minh</button>
				<button type="button" class="chip" data-value="other" aria-pressed="false">Tỉnh/thành khác</button>
			</div>
		</div>
		<div class="note-box">
			<strong>Căn cứ tính:</strong> lệ phí trước bạ Hà Nội <?= e(opt('fee_rate_hn')) ?>%, TP.HCM <?= e(opt('fee_rate_hcm')) ?>%, tỉnh khác <?= e(opt('fee_rate_other')) ?>%; xe bán tải tính <?= e(opt('fee_pickup_pct')) ?>% mức của xe con. Biển số Hà Nội <?= e(money(opt('plate_hn'))) ?>đ, TP.HCM <?= e(money(opt('plate_hcm'))) ?>đ, tỉnh khác tạm tính <?= e(money(opt('plate_other'))) ?>đ. Phí đường bộ <?= e(money(opt('road_fee_year'))) ?>đ/12 tháng. Bảo hiểm TNDS bắt buộc: dưới 6 chỗ <?= e(money(opt('tnds_5'))) ?>đ, 6–9 chỗ <?= e(money(opt('tnds_7'))) ?>đ, bán tải <?= e(money(opt('tnds_pickup'))) ?>đ. Lệ phí trước bạ thực tế tính trên bảng giá của Bộ Tài chính, có thể khác giá niêm yết. Cập nhật: <time datetime="<?= e(updated_iso()) ?>"><?= e(opt('updated_date')) ?></time>.
		</div>
	</div>
	<div class="result">
		<div class="result__kicker">ƯỚC TÍNH CHO</div>
		<div class="result__title" data-out="title">—</div>
		<div class="result__rows" data-out="rows"></div>
		<div class="result__total"><span>TỔNG LĂN BÁNH (ƯỚC TÍNH)</span><span data-out="total">—</span></div>
		<div class="result__small">Chưa gồm bảo hiểm vật chất, phí dịch vụ đăng ký và ưu đãi trong tháng.</div>
		<a class="btn btn--red btn--block" href="tel:<?= e(tel($ph)) ?>">NHẬN BÁO GIÁ CHÍNH XÁC: <?= e($ph) ?></a>
		<a class="result__link" data-out="loanlink" href="<?= e(page_url('tra-gop')) ?>">Tính trả góp cho xe này →</a>
	</div>
</section>
<?php if (trim(strip_tags((string) $page['content'])) !== '') : ?><section class="container section-bottom"><div class="entry"><?= render_content($page['content']) ?></div></section><?php endif; ?>
<?php include __DIR__ . '/partials/footer.php';

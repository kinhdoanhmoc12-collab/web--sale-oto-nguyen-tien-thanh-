<?php
$META = page_meta($page, 'dich-vu');
include __DIR__ . '/partials/header.php';
page_hero_from($page, 'assets/img/bg-dichvu.webp');
$content = render_content($page['content']);
preg_match_all('~<h2[^>]*id="([^"]+)"[^>]*>(.*?)</h2>~s', $content, $m, PREG_SET_ORDER);
?>
<?php if ($m) : ?>
<nav class="subnav" aria-label="Mục dịch vụ">
	<div class="container subnav__inner">
		<?php foreach ($m as $h) : ?><a href="#<?= e($h[1]) ?>"><?= e(html_entity_decode(strip_tags($h[2]))) ?></a><?php endforeach; ?>
		<a href="#dat-lich">Đặt lịch dịch vụ</a>
	</div>
</nav>
<?php endif; ?>
<section class="container section"><div class="entry entry--service"><?= $content ?></div></section>
<section id="dat-lich" class="section section--light">
	<div class="container container--form">
		<?php partial('lead-form', ['source' => 'dat-lich-dich-vu', 'title' => 'Đặt lịch dịch vụ', 'skin' => 'light', 'fields' => ['plate', 'date', 'service', 'note'], 'button' => 'GỬI ĐẶT LỊCH', 'purpose' => 'liên hệ', 'success' => 'Xưởng dịch vụ sẽ gọi xác nhận lịch hẹn. Hỗ trợ kỹ thuật: <a href="tel:' . e(tel(opt('tech_hotline'))) . '">' . e(opt('tech_hotline')) . '</a>.']); ?>
	</div>
</section>
<?php include __DIR__ . '/partials/footer.php';

<?php
$META = page_meta($page, 'lai-thu');
include __DIR__ . '/partials/header.php';
$ph = opt('phone');
?>
<section class="split-hero">
	<img class="split-hero__img" src="<?= eu(media($page['image'] ?: 'assets/img/banner-triton.webp')) ?>" alt="<?= e($page['title']) ?>">
	<div class="split-hero__body">
		<h1><?= e($page['title']) ?></h1>
		<?php if ($page['subtitle']) : ?><p><?= e($page['subtitle']) ?></p><?php endif; ?>
	</div>
</section>
<section class="container container--form section">
	<?php partial('lead-form', ['source' => 'lai-thu', 'skin' => 'light', 'fields' => ['car', 'date', 'place', 'note'], 'button' => 'ĐĂNG KÝ LÁI THỬ', 'purpose' => 'sắp xếp lái thử', 'car' => (string) ($_GET['xe'] ?? ''), 'success' => 'Chuyên viên ' . e(opt('consultant_name')) . ' sẽ gọi xác nhận lịch hẹn. Cần gấp, gọi ngay <a href="tel:' . e(tel($ph)) . '">' . e($ph) . '</a>.']); ?>
	<?php if (trim(strip_tags((string) $page['content'])) !== '') : ?><div class="entry section-top"><?= render_content($page['content']) ?></div><?php endif; ?>
</section>
<?php include __DIR__ . '/partials/footer.php';

<?php
$META = page_meta($page, 'chinh-sach');
include __DIR__ . '/partials/header.php';
page_hero_from($page, 'assets/img/banner-xpander.webp', ['align' => 'center']);
?>
<article class="container container--text section">
	<div class="updated updated--top">Cập nhật lần cuối: <time datetime="<?= e(substr((string) $page['updated_at'], 0, 10)) ?>"><?= e(vn_date($page['updated_at'])) ?></time>.</div>
	<div class="entry"><?= render_content($page['content']) ?></div>
</article>
<?php include __DIR__ . '/partials/footer.php';

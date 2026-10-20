<?php
$cat = trim((string) ($_GET['cm'] ?? ''));
$per = 9;
$pg = max(1, (int) ($_GET['trang'] ?? 1));
$where = "status = 'publish'" . ($cat !== '' ? ' AND category = :c' : '');
$params = $cat !== '' ? ['c' => $cat] : [];
$total = (int) val("SELECT COUNT(*) FROM posts WHERE $where", $params);
$posts = rows("SELECT * FROM posts WHERE $where ORDER BY created_at DESC LIMIT $per OFFSET " . (($pg - 1) * $per), $params);
$cats = array_column(rows("SELECT category, COUNT(*) n FROM posts WHERE status = 'publish' AND category <> '' GROUP BY category ORDER BY n DESC"), 'category');
$META = page_meta($page, 'tin-tuc');
if ($cat !== '' || $pg > 1) { $META['canonical'] .= '?' . http_build_query(array_filter(['cm' => $cat, 'trang' => $pg > 1 ? $pg : null])); }
include __DIR__ . '/partials/header.php';
page_hero_from($page, 'assets/img/bg-tintuc.webp');
?>
<section class="container section">
	<?php if (count($cats) > 1) : ?>
		<div class="chips chips--links" role="group" aria-label="Lọc theo chuyên mục">
			<a class="chip" aria-current="<?= $cat === '' ? 'page' : 'false' ?>" href="<?= e(page_url('tin-tuc')) ?>">Tất cả</a>
			<?php foreach ($cats as $c) : ?><a class="chip" aria-current="<?= $cat === $c ? 'page' : 'false' ?>" href="<?= e(page_url('tin-tuc')) ?>?cm=<?= e(rawurlencode($c)) ?>"><?= e($c) ?></a><?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if ($posts) : ?>
		<div class="post-grid"><?php foreach ($posts as $p) { partial('post-card', ['p' => $p]); } ?></div>
		<?php $pages = (int) ceil($total / $per); if ($pages > 1) : ?>
			<div class="pagination">
				<?php for ($i = 1; $i <= $pages; $i++) : ?>
					<?php if ($i === $pg) : ?><span class="page-numbers current"><?= $i ?></span><?php else : ?><a class="page-numbers" href="<?= e(page_url('tin-tuc')) ?>?<?= e(http_build_query(array_filter(['cm' => $cat, 'trang' => $i]))) ?>"><?= $i ?></a><?php endif; ?>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<p class="empty">Chưa có bài viết trong mục này.</p>
	<?php endif; ?>
</section>
<?php include __DIR__ . '/partials/footer.php';

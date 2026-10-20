<article class="post-card">
	<a class="post-card__img" href="<?= e(post_url($p)) ?>"><img loading="lazy" src="<?= eu(media($p['image'] ?: 'assets/img/bg-tintuc.webp')) ?>" alt="<?= e($p['title']) ?>"></a>
	<div class="post-card__cat"><?= e(upper($p['category'] ?: 'Tin tức')) ?> · <?= e(vn_date($p['created_at'])) ?></div>
	<h2 class="post-card__title"><a href="<?= e(post_url($p)) ?>"><?= e($p['title']) ?></a></h2>
	<?php if ($p['excerpt']) : ?><p class="post-card__desc"><?= e($p['excerpt']) ?></p><?php endif; ?>
</article>

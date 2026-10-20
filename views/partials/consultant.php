<?php
$authorLink = $authorLink ?? false;
$hideCommit = $hideCommit ?? false;
$ph = opt('phone');
?>
<section class="consultant" aria-label="Người tư vấn">
	<div class="container consultant__inner">
		<img class="consultant__photo" decoding="async" src="<?= eu(media(opt('consultant_photo'))) ?>" alt="Chuyên viên tư vấn <?= e(opt('consultant_name')) ?> tại showroom Mitsubishi" width="300" height="300">
		<div class="consultant__body">
			<div class="kicker">CHUYÊN VIÊN TƯ VẤN</div>
			<h2 class="consultant__name"><?= e(opt('consultant_name')) ?></h2>
			<p><?= e(opt('consultant_bio')) ?></p>
			<?php if ($hideCommit && opt('consultant_bio2')) : ?><p><?= e(opt('consultant_bio2')) ?></p><?php endif; ?>
			<div class="stats">
				<div class="stat"><div class="stat__num"><?= e(opt('stat_years')) ?></div><div class="stat__label"><?= e(opt('stat_years_label')) ?></div></div>
				<div class="stat"><div class="stat__num"><?= e(opt('stat_customers')) ?></div><div class="stat__label"><?= e(opt('stat_customers_label')) ?></div></div>
			</div>
			<?php if (!$hideCommit) : ?>
				<ul class="checks"><?php foreach (lines(opt('commitments')) as $l) : ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
			<?php endif; ?>
			<div class="btn-row">
				<a class="btn btn--red" href="tel:<?= e(tel($ph)) ?>">GỌI NGAY: <?= e($ph) ?></a>
				<a class="btn btn--zalo" href="https://zalo.me/<?= e(tel(opt('zalo'))) ?>" target="_blank" rel="noopener">CHAT ZALO</a>
			</div>
			<?php if ($authorLink) : ?><a class="link-arrow" href="<?= e(page_url('tac-gia')) ?>">Xem trang tác giả &amp; bài viết chia sẻ kinh nghiệm →</a><?php endif; ?>
		</div>
	</div>
</section>

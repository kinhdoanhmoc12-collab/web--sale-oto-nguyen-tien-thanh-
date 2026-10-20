<?php
$u = full_url(page_map()['tac-gia'][0]);
$META = page_meta($page, 'tac-gia', [['@type' => 'ProfilePage', 'url' => $u, 'mainEntity' => ['@id' => full_url() . '#tu-van-vien']], schema_person(), schema_dealer()]);
$META['title'] = opt('consultant_name') . ' – ' . opt('consultant_title');
$META['type'] = 'profile';
include __DIR__ . '/partials/header.php';
$ph = opt('phone');
$n = count(cars());
$posts = rows("SELECT * FROM posts WHERE status = 'publish' ORDER BY created_at DESC");
?>
<section class="profile">
	<div class="container profile__inner">
		<img class="profile__photo" src="<?= eu(media(opt('consultant_photo'))) ?>" alt="<?= e(opt('consultant_name')) ?> – <?= e(opt('consultant_title')) ?>" width="220" height="220" decoding="async">
		<div>
			<div class="kicker">TÁC GIẢ &amp; CHUYÊN VIÊN TƯ VẤN</div>
			<h1 class="profile__name"><?= e(opt('consultant_name')) ?></h1>
			<div class="profile__role"><?= e(opt('consultant_title')) ?> tại <?= e(opt('company_short')) ?> – showroom <?= e(opt('address')) ?></div>
			<div class="stats">
				<div class="stat"><div class="stat__num"><?= e(opt('stat_years')) ?></div><div class="stat__label"><?= e(opt('stat_years_label')) ?></div></div>
				<div class="stat"><div class="stat__num"><?= e(opt('stat_customers')) ?></div><div class="stat__label"><?= e(opt('stat_customers_label')) ?></div></div>
				<div class="stat"><div class="stat__num"><?= $n ?></div><div class="stat__label">dòng xe đang tư vấn</div></div>
			</div>
		</div>
	</div>
</section>
<section class="container section two-col two-col--aside">
	<div class="entry">
		<h2>Giới thiệu</h2>
		<?php foreach (lines(opt('author_intro')) as $para) : ?><p><?= e($para) ?></p><?php endforeach; ?>
		<h2>Tôi có thể hỗ trợ</h2>
		<ul>
			<li>Tư vấn chọn xe phù hợp nhu cầu và tài chính trong <?= $n ?> dòng xe Mitsubishi đang bán.</li>
			<li>Báo giá lăn bánh chi tiết theo nơi đăng ký xe.</li>
			<li>Hỗ trợ hồ sơ vay trả góp cho cá nhân và hộ gia đình.</li>
			<li>Sắp xếp lái thử tại showroom hoặc tận nhà.</li>
			<li>Hỗ trợ đăng ký xe, bàn giao và kết nối dịch vụ bảo hành, bảo dưỡng.</li>
		</ul>
		<?php if (trim(strip_tags((string) $page['content'])) !== '') : ?><?= render_content($page['content']) ?><?php endif; ?>
	</div>
	<aside class="contact-card">
		<h2>Liên hệ trực tiếp</h2>
		<a class="btn btn--red btn--block" href="tel:<?= e(tel($ph)) ?>">GỌI <?= e($ph) ?></a>
		<a class="btn btn--zalo btn--block" href="https://zalo.me/<?= e(tel(opt('zalo'))) ?>" target="_blank" rel="noopener">CHAT ZALO</a>
		<?php if (opt('facebook')) : ?><a class="btn btn--outline btn--block" href="<?= eu(opt('facebook')) ?>" target="_blank" rel="noopener">FANPAGE FACEBOOK</a><?php endif; ?>
		<a class="link-arrow" href="<?= e(page_url('lien-he', '#khach-hang')) ?>">Xem ảnh khách hàng nhận xe →</a>
	</aside>
</section>
<section class="section section--light">
	<div class="container">
		<h2 class="h-red">Bài viết của <?= e(opt('consultant_name')) ?></h2>
		<?php if ($posts) : ?><div class="post-grid"><?php foreach ($posts as $p) { partial('post-card', ['p' => $p]); } ?></div><?php else : ?><p class="empty">Chưa có bài viết.</p><?php endif; ?>
	</div>
</section>
<?php include __DIR__ . '/partials/footer.php';

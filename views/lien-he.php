<?php
$META = page_meta($page, 'lien-he', [schema_dealer(), schema_person()]);
include __DIR__ . '/partials/header.php';
page_hero_from($page, 'assets/img/bg-lienhe.webp');
$ph = opt('phone');
$photos = rows('SELECT * FROM deliveries WHERE active = 1 ORDER BY ord, id');
partial('consultant', ['authorLink' => true, 'hideCommit' => true]);
?>
<?php if ($photos) : ?>
<section id="khach-hang" class="customers" aria-label="Khách hàng đã nhận xe" style="--kh-bg:url('<?= eu(media($page['image'] ?: 'assets/img/bg-lienhe.webp')) ?>')">
	<div class="container">
		<div class="customers__head">
			<div class="kicker kicker--light">HÌNH ẢNH THẬT TẠI SHOWROOM</div>
			<h2>KHÁCH HÀNG ĐÃ TIN TƯỞNG</h2>
			<div class="customers__sub">Hình ảnh bàn giao thật và lời cảm ơn gửi tới từng khách hàng</div>
			<p>Mỗi chiếc xe được bàn giao là một lời cảm ơn gửi tới quý khách đã tin tưởng đồng hành cùng chuyên viên <?= e(opt('consultant_name')) ?> – từ những bạn trẻ mua chiếc xe đầu tiên đến các gia đình cần thêm chỗ cho cả nhà.</p>
		</div>
		<div class="customers__grid">
			<?php foreach ($photos as $d) : $alt = 'Bàn giao xe Mitsubishi ' . $d['car'] . ' cho khách hàng tại showroom'; ?>
				<figure class="customer">
					<button type="button" class="customer__zoom" data-full="<?= eu(media($d['image'])) ?>" data-alt="<?= e($alt) ?>" aria-label="Xem lớn ảnh bàn giao <?= e($d['car']) ?>"><img loading="lazy" src="<?= eu(media($d['image'])) ?>" alt="<?= e($alt) ?>"></button>
					<figcaption>
						<div class="customer__meta"><strong>Bàn giao <?= e($d['car']) ?></strong><?= $d['cust_type'] ? ' · ' . e($d['cust_type']) : '' ?></div>
						<?php if ($d['message']) : ?>
							<blockquote class="customer__quote"><?= e($d['message']) ?></blockquote>
							<?php if ($d['msg_by'] === 'customer' && $d['cust_name']) : ?><div class="customer__who">— <?= e($d['cust_name']) ?></div><?php endif; ?>
						<?php endif; ?>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
		<div class="customers__cta">
			<div>Anh/chị muốn là khách hàng tiếp theo nhận xe?</div>
			<a class="btn btn--red" href="tel:<?= e(tel($ph)) ?>">GỌI <?= e($ph) ?></a>
			<a class="btn btn--ghost" href="<?= e(page_url('lai-thu')) ?>">ĐĂNG KÝ LÁI THỬ</a>
		</div>
	</div>
</section>
<?php endif; ?>
<section class="section">
	<div class="container two-col">
		<div>
			<h2 class="h-red"><?= e(opt('company_short')) ?></h2>
			<p><?= e(opt('company_intro')) ?></p>
			<ul class="info-list">
				<?php if (opt('company_tax')) : ?><li>Giấy CNĐKKD: <?= e(opt('company_tax')) ?></li><?php endif; ?>
				<li>Địa chỉ: <?= e(opt('address')) ?></li>
				<?php if (opt('dealer_hotline')) : ?><li>Hotline: <?= implode(' / ', array_map(fn($x) => '<a href="tel:' . e(tel($x)) . '">' . e(trim($x)) . '</a>', explode('/', opt('dealer_hotline')))) ?></li><?php endif; ?>
				<?php if (opt('tech_hotline')) : ?><li>Hỗ trợ kỹ thuật: <a href="tel:<?= e(tel(opt('tech_hotline'))) ?>"><?= e(opt('tech_hotline')) ?></a></li><?php endif; ?>
				<?php if (opt('email')) : ?><li>Email: <a href="mailto:<?= e(opt('email')) ?>"><?= e(opt('email')) ?></a></li><?php endif; ?>
			</ul>
		</div>
		<div class="map-card">
			<div class="kicker">VỊ TRÍ SHOWROOM</div>
			<div class="map-card__name"><?= e(opt('showroom_name')) ?></div>
			<div class="map-card__addr"><?= icon('pin', 18) ?> <?= e(opt('address')) ?></div>
			<div class="btn-row">
				<a class="btn btn--red" href="<?= e('https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode(opt('lat') . ',' . opt('lng'))) ?>" target="_blank" rel="noopener">CHỈ ĐƯỜNG ĐẾN SHOWROOM</a>
				<a class="btn btn--outline" href="<?= eu(opt('maps_url')) ?>" target="_blank" rel="noopener">XEM TRÊN GOOGLE MAPS</a>
			</div>
		</div>
	</div>
	<?php if (trim(strip_tags((string) $page['content'], '<img><iframe>')) !== '') : ?><div class="container section-top"><div class="entry"><?= render_content($page['content']) ?></div></div><?php endif; ?>
</section>
<?php include __DIR__ . '/partials/footer.php';

<?php
$phone = opt('phone');
$hotline = opt('dealer_hotline');
$fb = opt('facebook');
$yt = opt('youtube');
$L = fn($label, $href) => ['label' => $label, 'href' => $href];
$cols = [
    'Sản phẩm' => array_merge([$L('Tất cả dòng xe', page_url('bang-gia'))], array_map(fn($c) => $L($c['name'], car_url($c)), cars())),
    'Mua xe' => [$L('Bảng giá', page_url('bang-gia')), $L('Khuyến mãi', page_url('khuyen-mai')), $L('Dự tính chi phí', page_url('du-tinh')), $L('Chương trình trả góp MAF', page_url('tra-gop')), $L('Đăng ký lái thử', page_url('lai-thu')), $L('Phụ kiện chính hãng', page_url('dich-vu', '#phu-tung'))],
    'Dịch vụ' => [$L('Chính sách bảo hành', page_url('dich-vu', '#bao-hanh')), $L('Bảo dưỡng định kỳ', page_url('dich-vu', '#bao-duong')), $L('Phụ tùng & phụ kiện', page_url('dich-vu', '#phu-tung')), $L('Đặt lịch dịch vụ', page_url('dich-vu', '#dat-lich')), $L('Ứng dụng Mitsubishi Connect+', page_url('dich-vu', '#connect')), $L('Tài liệu hướng dẫn sử dụng', page_url('dich-vu', '#connect'))],
    'Tin tức' => [$L('Kinh nghiệm mua xe', page_url('tin-tuc') . '?cm=' . rawurlencode('Kinh nghiệm mua xe')), $L('Giá xe Mitsubishi', page_url('bang-gia')), $L('Tin khuyến mãi', page_url('khuyen-mai')), $L('Tất cả tin tức', page_url('tin-tuc'))],
    'Về chúng tôi' => [$L('Giới thiệu', page_url('lien-he')), $L('Tác giả – ' . opt('consultant_name'), page_url('tac-gia')), $L('Khách hàng đã nhận xe', page_url('lien-he', '#khach-hang')), $L('Chính sách bảo mật', page_url('chinh-sach', '#bao-mat')), $L('Điều khoản giao dịch', page_url('chinh-sach', '#dieu-khoan')), $L('Quy định truy cập website', page_url('chinh-sach', '#truy-cap'))],
];
?>
</main>

<footer class="site-footer" id="lien-he">
	<div class="container site-footer__top">
		<div class="site-footer__cols">
			<?php foreach ($cols as $title => $links) : ?>
				<div class="site-footer__col">
					<div class="site-footer__title"><?= e($title) ?></div>
					<?php foreach ($links as $l) : ?><a href="<?= e($l['href']) ?>"><?= e($l['label']) ?></a><?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="site-footer__info">
			<?php if ($hotline) : ?><a class="site-footer__big" href="tel:<?= e(tel(explode('/', $hotline)[0])) ?>"><?= icon('phone', 22) ?><?= e($hotline) ?> (Hotline)</a><?php endif; ?>
			<?php if (opt('tech_hotline')) : ?><a class="site-footer__big" href="tel:<?= e(tel(opt('tech_hotline'))) ?>"><?= icon('mobile', 22) ?><?= e(opt('tech_hotline')) ?> (Hỗ trợ kỹ thuật)</a><?php endif; ?>
			<?php if (opt('email')) : ?><a class="site-footer__big" href="mailto:<?= e(opt('email')) ?>"><?= icon('mail', 22) ?><?= e(opt('email')) ?></a><?php endif; ?>
			<div class="site-footer__company">Website này được ủy quyền bởi <?= e(opt('company_name')) ?></div>
			<ul class="site-footer__legal">
				<?php foreach (lines(opt('company_lines')) as $line) : ?><li><?= e($line) ?></li><?php endforeach; ?>
			</ul>
			<div class="site-footer__social">
				<?php if ($yt) : ?><a href="<?= eu($yt) ?>" target="_blank" rel="noopener" aria-label="Kênh YouTube"><svg width="28" height="20" viewBox="0 0 28 20" aria-hidden="true"><rect width="28" height="20" rx="5" fill="#fff"/><path d="M11 5l8 5-8 5z" fill="#000"/></svg></a><?php endif; ?>
			</div>
		</div>
	</div>
	<div class="container site-footer__bottom">
		<div>Tư vấn bán hàng – <?= e(opt('company_short')) ?></div>
		<div>© <?= date('Y') ?> <?= e(opt('company_short')) ?>. Mọi quyền được bảo lưu.</div>
	</div>
</footer>

<div class="float-contact" aria-label="Liên hệ nhanh">
	<a class="float-contact__zalo" href="https://zalo.me/<?= e(tel(opt('zalo'))) ?>" target="_blank" rel="noopener" aria-label="Chat Zalo" data-tip="Chat Zalo ngay">Zalo</a>
	<a class="float-contact__call" href="tel:<?= e(tel($phone)) ?>" data-tip="Gọi tư vấn miễn phí"><span class="call-icon"><?= icon('phone', 18) ?></span><?= e($phone) ?></a>
</div>

<?php $qHref = ($view ?? '') === 'car' ? '#bao-gia' : (($view ?? '') === 'home' ? '#dang-ky' : url('#dang-ky')); ?>
<nav class="mbar" aria-label="Liên hệ nhanh">
	<a class="mbar__call" href="tel:<?= e(tel($phone)) ?>"><?= icon('phone', 18) ?><span>Gọi ngay</span></a>
	<a class="mbar__zalo" href="https://zalo.me/<?= e(tel(opt('zalo'))) ?>" target="_blank" rel="noopener"><b>Zalo</b><span>Nhắn tin</span></a>
	<a class="mbar__quote" href="<?= e($qHref) ?>"><?= icon('mail', 18) ?><span>Nhận báo giá</span></a>
</nav>

<div class="lightbox" id="tt-lightbox" hidden role="dialog" aria-modal="true" aria-label="Xem ảnh lớn">
	<img src="" alt="" decoding="async">
	<button type="button" class="lightbox__close" aria-label="Đóng ảnh"><?= icon('close', 26, '#fff') ?></button>
</div>
</body>
</html>

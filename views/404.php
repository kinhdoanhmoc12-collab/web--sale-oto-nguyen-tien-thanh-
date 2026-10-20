<?php
$META = ['title' => 'Không tìm thấy trang | Mitsubishi ' . opt('consultant_name'), 'noindex' => true];
include __DIR__ . '/partials/header.php';
?>
<section class="container section notfound">
	<h1>Không tìm thấy trang</h1>
	<p>Trang anh/chị tìm có thể đã đổi địa chỉ. Xem bảng giá các dòng xe hoặc gọi trực tiếp để được tư vấn.</p>
	<div class="btn-row">
		<a class="btn btn--red" href="<?= e(page_url('bang-gia')) ?>">XEM BẢNG GIÁ</a>
		<a class="btn btn--outline" href="tel:<?= e(tel(opt('phone'))) ?>">GỌI <?= e(opt('phone')) ?></a>
		<a class="btn btn--outline" href="<?= e(url()) ?>">VỀ TRANG CHỦ</a>
	</div>
</section>
<?php include __DIR__ . '/partials/footer.php';

<?php
/**
 * Form thu thông tin khách. Biến: $source, $title, $skin (onred|red|light), $fields[], $button, $success, $purpose, $car, $versions[].
 */
$source = $source ?? 'form';
$title = $title ?? '';
$skin = $skin ?? 'light';
$fields = array_flip($fields ?? ['car', 'region']);
$button = $button ?? 'GỬI YÊU CẦU';
$purpose = $purpose ?? 'liên hệ tư vấn và báo giá';
$car = $car ?? '';
$versions = $versions ?? [];
$ph = opt('phone');
$success = $success ?? ('Chuyên viên ' . e(opt('consultant_name')) . ' sẽ gọi lại trong ít phút. Cần gấp, anh/chị gọi ngay <a href="tel:' . e(tel($ph)) . '">' . e($ph) . '</a>.');
?>
<form class="lead-form lead-form--<?= e($skin) ?>" data-source="<?= e($source) ?>" novalidate>
	<?php if ($title) : ?><h2 class="lead-form__title"><?= e($title) ?></h2><?php endif; ?>
	<div class="lead-form__body">
		<?php if (isset($fields['pay'])) : 
			$id_gop = 'pay_gop_' . substr(md5($source . '_' . $skin), 0, 6);
			$id_thang = 'pay_thang_' . substr(md5($source . '_' . $skin), 0, 6);
		?>
			<div class="lead-form__radios" role="radiogroup" aria-label="Hình thức thanh toán">
				<label for="<?= $id_gop ?>"><input type="radio" id="<?= $id_gop ?>" name="pay" value="Trả góp" checked> Trả góp</label>
				<label for="<?= $id_thang ?>"><input type="radio" id="<?= $id_thang ?>" name="pay" value="Trả thẳng"> Trả thẳng</label>
			</div>
		<?php endif; ?>
		<div class="lead-form__grid">
			<label>Họ tên (*)<input type="text" name="name" placeholder="Nhập họ tên" autocomplete="name" required maxlength="80"></label>
			<label>Số điện thoại (*)<input type="tel" name="phone" placeholder="Nhập số điện thoại" autocomplete="tel" inputmode="tel" required maxlength="20"></label>
			<?php if (isset($fields['plate'])) : ?><label>Biển số xe<input type="text" name="plate" placeholder="30A-xxx.xx"></label><?php endif; ?>
			<?php if (isset($fields['car'])) : ?>
				<label><?= isset($fields['date']) ? 'Xe muốn lái thử' : 'Xe quan tâm' ?>
					<select name="car">
						<?php if (!isset($fields['date'])) : ?><option value="">-- Chọn xe --</option><?php endif; ?>
						<?php foreach (cars() as $c) : ?><option<?= $car === $c['name'] ? ' selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
					</select>
				</label>
			<?php endif; ?>
			<?php if (isset($fields['version']) && $versions) : ?>
				<label>Phiên bản quan tâm
					<select name="version"><?php foreach ($versions as $v) : ?><option><?= e($v) ?></option><?php endforeach; ?></select>
				</label>
				<input type="hidden" name="car" value="<?= e($car) ?>">
			<?php endif; ?>
			<?php if (isset($fields['date'])) : ?><label><?= isset($fields['service']) ? 'Ngày hẹn' : 'Ngày mong muốn' ?><input type="text" name="date" class="lead-form__date" placeholder="dd/mm/yyyy" autocomplete="off" inputmode="numeric" maxlength="10"></label><?php endif; ?>
		</div>
		<?php if (isset($fields['region'])) : ?><label>Khu vực lăn bánh (nơi đăng ký xe)<input type="text" name="region" placeholder="VD: Hà Nội, Bắc Ninh, Hưng Yên…" maxlength="120"></label><?php endif; ?>
		<?php if (isset($fields['service'])) : ?>
			<label>Dịch vụ cần làm
				<select name="service"><option>Bảo dưỡng định kỳ</option><option>Bảo dưỡng nhanh</option><option>Sửa chữa</option><option>Bảo hành</option><option>Lắp phụ kiện</option></select>
			</label>
		<?php endif; ?>
		<?php if (isset($fields['place'])) : ?>
			<fieldset class="lead-form__fieldset">
				<legend>Địa điểm lái thử</legend>
				<div class="lead-form__radios lead-form__radios--left">
					<label><input type="radio" name="place" value="Tại showroom" checked> Tại showroom</label>
					<label><input type="radio" name="place" value="Tận nhà / nơi làm việc"> Tận nhà / nơi làm việc</label>
				</div>
			</fieldset>
		<?php endif; ?>
		<?php if (isset($fields['note'])) : ?><label>Ghi chú<textarea name="note" rows="3" maxlength="500" placeholder="Khung giờ thuận tiện, địa chỉ nếu muốn lái thử tận nơi…"></textarea></label><?php endif; ?>
		<div class="hp" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
		<label class="lead-form__agree"><input type="checkbox" name="agree" value="1"> <span>Tôi đồng ý cho <?= e(opt('company_short')) ?> xử lý thông tin cá nhân của tôi để <?= e($purpose) ?>, theo <a href="<?= e(page_url('chinh-sach', '#bao-mat')) ?>">Chính sách bảo vệ dữ liệu cá nhân</a>.</span></label>
		<div class="lead-form__error" role="alert" hidden></div>
		<button type="submit" class="lead-form__submit"><?= e($button) ?></button>
	</div>
	<div class="lead-form__done" hidden tabindex="-1">
		<div class="lead-form__done-title">Cảm ơn anh/chị đã đăng ký!</div>
		<div><?= $success ?></div>
	</div>
</form>

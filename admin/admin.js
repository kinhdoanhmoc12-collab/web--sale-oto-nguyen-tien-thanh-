/* Trang quản trị – trình soạn thảo, xem trước ảnh. */
(function () {
	'use strict';
	var ADM = window.ADM || {};

	// Xem trước ảnh ngay khi chọn file.
	document.querySelectorAll('.imgfield input[type=file]').forEach(function (inp) {
		inp.addEventListener('change', function () {
			var f = inp.files && inp.files[0];
			if (!f) return;
			var box = inp.closest('.imgfield').querySelector('.imgfield__preview');
			var url = URL.createObjectURL(f);
			box.innerHTML = '<img src="' + url + '" alt="">';
			if (f.size > 8 * 1024 * 1024) alert('Ảnh hơi nặng (' + Math.round(f.size / 1048576) + 'MB). Nên nén dưới 1MB để web tải nhanh.');
		});
	});

	// Cảnh báo khi rời trang mà chưa lưu.
	var dirty = false;
	document.querySelectorAll('form.form').forEach(function (form) {
		form.addEventListener('input', function () { dirty = true; });
		form.addEventListener('change', function () { dirty = true; });
		form.addEventListener('submit', function () {
			dirty = false;
			var b = form.querySelector('button.btn--primary');
			if (b) { b.disabled = true; b.textContent = 'Đang lưu…'; }
			if (window.tinymce) window.tinymce.triggerSave();
		});
	});
	window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

	// Trình soạn thảo bài viết (TinyMCE). Nếu không tải được, ô nhập vẫn dùng được dạng HTML.
	if (window.tinymce && document.querySelector('.js-editor')) {
		window.tinymce.init({
			selector: 'textarea.js-editor',
			height: 560,
			menubar: false,
			branding: false,
			promotion: false,
			plugins: 'link image lists table media autoresize code wordcount',
			toolbar: 'blocks | bold italic | bullist numlist | link image media table | alignleft aligncenter | removeformat code',
			block_formats: 'Đoạn văn=p; Tiêu đề mục lớn (H2)=h2; Tiêu đề mục nhỏ (H3)=h3',
			content_css: ADM.base + 'assets/css/main.css',
			body_class: 'entry',
			content_style: 'body{padding:16px;max-width:800px;margin:0 auto}',
			relative_urls: false,
			remove_script_host: true,
			convert_urls: true,
			image_caption: true,
			image_dimensions: false,
			automatic_uploads: true,
			images_file_types: 'jpg,jpeg,png,webp,gif',
			images_upload_handler: function (blobInfo) {
				return new Promise(function (resolve, reject) {
					var fd = new FormData();
					fd.append('file', blobInfo.blob(), blobInfo.filename());
					fd.append('csrf', ADM.csrf);
					fetch(ADM.upload, { method: 'POST', body: fd, headers: { 'X-CSRF': ADM.csrf }, credentials: 'same-origin' })
						.then(function (r) { return r.json(); })
						.then(function (j) { j.location ? resolve(j.location) : reject({ message: j.error || 'Không tải được ảnh', remove: true }); })
						.catch(function () { reject({ message: 'Mất kết nối khi tải ảnh', remove: true }); });
				});
			},
			setup: function (ed) { ed.on('change input', function () { dirty = true; }); }
		});
	}
})();

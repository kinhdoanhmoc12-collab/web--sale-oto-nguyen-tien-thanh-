/* Trang quản trị – trình soạn thảo, xem trước ảnh. */
(function () {
	'use strict';
	var ADM = window.ADM || {};

	function formatBytes(bytes) {
		if (bytes < 1024) return bytes + ' B';
		if (bytes < 1048576) return Math.round(bytes / 1024) + ' KB';
		return (bytes / 1048576).toFixed(1) + ' MB';
	}

	function compressImageFile(file, maxDimension, quality) {
		return new Promise(function (resolve) {
			if (!file || !file.type || !file.type.startsWith('image/') || file.type === 'image/gif') {
				return resolve(file);
			}
			var maxD = maxDimension || 1600;
			var q = typeof quality === 'number' ? quality : 0.85;

			var reader = new FileReader();
			reader.onload = function (e) {
				var img = new Image();
				img.onload = function () {
					var w = img.naturalWidth || img.width;
					var h = img.naturalHeight || img.height;
					var needResize = w > maxD || h > maxD;
					var needCompress = file.size > 800 * 1024;

					if (!needResize && !needCompress) {
						return resolve(file);
					}

					var targetW = w;
					var targetH = h;
					if (needResize) {
						if (w > h) {
							targetH = Math.round(h * (maxD / w));
							targetW = maxD;
						} else {
							targetW = Math.round(w * (maxD / h));
							targetH = maxD;
						}
					}

					var canvas = document.createElement('canvas');
					canvas.width = targetW;
					canvas.height = targetH;
					var ctx = canvas.getContext('2d');
					if (!ctx) return resolve(file);

					var isJpeg = file.type === 'image/jpeg';
					if (isJpeg) {
						ctx.fillStyle = '#FFFFFF';
						ctx.fillRect(0, 0, targetW, targetH);
					}
					ctx.drawImage(img, 0, 0, targetW, targetH);

					var finish = function (outType) {
						canvas.toBlob(function (blob) {
							if (!blob) return resolve(file);
							if (blob.size >= file.size && !needResize) return resolve(file);
							var ext = outType === 'image/jpeg' ? '.jpg' : (outType === 'image/webp' ? '.webp' : '.png');
							var baseName = (file.name || 'image').replace(/\.[^.]+$/, '');
							try {
								var newFile = new File([blob], baseName + ext, { type: outType, lastModified: Date.now() });
								resolve(newFile);
							} catch (e2) {
								resolve(blob);
							}
						}, outType, q);
					};

					if (isJpeg) {
						finish('image/jpeg');
					} else {
						try {
							var test = canvas.toDataURL('image/webp');
							if (test && test.indexOf('data:image/webp') === 0) {
								finish('image/webp');
							} else {
								finish('image/jpeg');
							}
						} catch (ex) {
							finish('image/jpeg');
						}
					}
				};
				img.onerror = function () { resolve(file); };
				img.src = e.target.result;
			};
			reader.onerror = function () { resolve(file); };
			reader.readAsDataURL(file);
		});
	}

	// Xem trước ảnh ngay khi chọn file + tự động tối ưu nếu ảnh nặng quá giới hạn hosting.
	document.querySelectorAll('.imgfield input[type=file]').forEach(function (inp) {
		inp.addEventListener('change', function () {
			var f = inp.files && inp.files[0];
			if (!f) return;
			var ctrl = inp.closest('.imgfield__ctrl') || inp.parentElement;
			var box = inp.closest('.imgfield').querySelector('.imgfield__preview');
			var url = URL.createObjectURL(f);
			box.innerHTML = '<img src="' + url + '" alt="">';

			var status = ctrl.querySelector('.img-status');
			if (!status) {
				status = document.createElement('p');
				status.className = 'hint img-status';
				ctrl.appendChild(status);
			}

			var oldSize = f.size;
			if (oldSize > 800 * 1024) {
				status.textContent = '⏳ Đang tự động tối ưu dung lượng ảnh...';
				status.style.color = '#b26b00';
				status.style.fontWeight = '600';

				compressImageFile(f, 1600, 0.85).then(function (optimized) {
					if (optimized && optimized.size < oldSize && window.DataTransfer) {
						try {
							var dt = new DataTransfer();
							dt.items.add(optimized);
							inp.files = dt.files;
							var newUrl = URL.createObjectURL(optimized);
							box.innerHTML = '<img src="' + newUrl + '" alt="">';
							status.innerHTML = '✓ Đã tối ưu nhẹ: <b>' + formatBytes(oldSize) + '</b> ➔ <b>' + formatBytes(optimized.size) + '</b> (Đã sẵn sàng lưu).';
							status.style.color = '#1a7f37';
						} catch (e3) {
							status.textContent = '';
						}
					} else {
						status.textContent = '';
					}
				});
			} else {
				status.textContent = '';
			}
		});
	});

	// Cảnh báo khi rời trang mà chưa lưu & tự động nén trước khi submit.
	var dirty = false;
	document.querySelectorAll('form.form').forEach(function (form) {
		form.addEventListener('input', function () { dirty = true; });
		form.addEventListener('change', function () { dirty = true; });
		form.addEventListener('submit', function (e) {
			if (form._isSubmitting) return;

			var fileInputs = form.querySelectorAll('input[type=file]');
			var hasLarge = false;
			fileInputs.forEach(function (inp) {
				if (inp.files && inp.files[0] && inp.files[0].size > 800 * 1024) {
					hasLarge = true;
				}
			});

			if (hasLarge && window.DataTransfer) {
				e.preventDefault();
				var b = form.querySelector('button.btn--primary');
				if (b) { b.disabled = true; b.textContent = 'Đang tối ưu ảnh…'; }

				var promises = [];
				fileInputs.forEach(function (inp) {
					var f = inp.files && inp.files[0];
					if (f && f.size > 800 * 1024) {
						promises.push(
							compressImageFile(f, 1600, 0.85).then(function (optFile) {
								if (optFile) {
									var dt = new DataTransfer();
									dt.items.add(optFile);
									inp.files = dt.files;
								}
							})
						);
					}
				});

				Promise.all(promises).then(function () {
					form._isSubmitting = true;
					dirty = false;
					if (b) { b.textContent = 'Đang lưu…'; }
					if (window.tinymce) window.tinymce.triggerSave();
					form.submit();
				}).catch(function () {
					form._isSubmitting = true;
					form.submit();
				});
				return;
			}

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
					var rawFile = blobInfo.blob();
					Promise.resolve(rawFile.size > 800 * 1024 ? compressImageFile(rawFile, 1600, 0.85) : rawFile).then(function (finalFile) {
						var fd = new FormData();
						fd.append('file', finalFile, blobInfo.filename());
						fd.append('csrf', ADM.csrf);
						fetch(ADM.upload, { method: 'POST', body: fd, headers: { 'X-CSRF': ADM.csrf }, credentials: 'same-origin' })
							.then(function (r) { return r.json(); })
							.then(function (j) { j.location ? resolve(j.location) : reject({ message: j.error || 'Không tải được ảnh', remove: true }); })
							.catch(function () { reject({ message: 'Mất kết nối khi tải ảnh', remove: true }); });
					});
				});
			},
			setup: function (ed) { ed.on('change input', function () { dirty = true; }); }
		});
	}
})();

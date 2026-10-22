/* Trang chi tiết xe: chọn phiên bản, đổi màu, xem thêm trang bị, thu gọn thông số. */
(function () {
	'use strict';
	var root = document.querySelector('.cd');
	if (!root) return;
	var $$ = function (s) { return Array.prototype.slice.call(root.querySelectorAll(s)); };

	function pick(i, scroll) {
		$$('.cd-pick').forEach(function (b) {
			var on = b.getAttribute('data-v') === String(i);
			b.classList.toggle('is-on', on);
			b.setAttribute('aria-selected', on ? 'true' : 'false');
		});
		$$('.cd-tab').forEach(function (b) { b.classList.toggle('is-on', b.getAttribute('data-v') === String(i)); });
		$$('.cd-pane, .cd-version__pic').forEach(function (p) { p.hidden = p.getAttribute('data-v') !== String(i); });
		var sel = document.querySelector('#lai-thu select[name="version"]');
		if (sel && sel.options[i]) sel.selectedIndex = i;
		if (scroll) {
			var t = document.getElementById('phien-ban');
			if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
		}
	}
	$$('.cd-pick').forEach(function (b) { b.addEventListener('click', function () { pick(b.getAttribute('data-v'), true); }); });
	$$('.cd-tab').forEach(function (b) { b.addEventListener('click', function () { pick(b.getAttribute('data-v'), false); }); });

	// Đổi màu xe
	$$('.cd-color').forEach(function (card) {
		var img = card.querySelector('.cd-color__img img');
		var label = card.querySelector('.cd-color__label');
		card.querySelectorAll('.cd-swatch').forEach(function (s) {
			s.addEventListener('click', function () {
				card.querySelectorAll('.cd-swatch').forEach(function (x) {
					x.classList.toggle('is-on', x === s);
					x.setAttribute('aria-checked', x === s ? 'true' : 'false');
				});
				if (img && s.getAttribute('data-img')) img.src = s.getAttribute('data-img');
				if (label) label.textContent = s.getAttribute('data-name') || '';
			});
		});
	});

	// Xem thêm trang bị
	$$('[data-more]').forEach(function (b) {
		b.addEventListener('click', function () {
			var list = b.previousElementSibling;
			var open = list.classList.toggle('is-open');
			b.textContent = open ? 'Thu gọn ▴' : 'Xem tất cả ' + list.children.length + ' trang bị ▾';
		});
	});

	// Thu gọn thông số
	$$('[data-collapse]').forEach(function (b) {
		b.addEventListener('click', function () {
			$$('.cd-acc').forEach(function (d) { d.open = false; });
			var t = document.getElementById('thong-so');
			if (t) t.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});
	});
})();

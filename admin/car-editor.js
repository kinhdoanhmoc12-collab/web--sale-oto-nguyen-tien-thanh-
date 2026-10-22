/* Trình sửa "Trang chi tiết xe": phiên bản, màu, thông số, điểm nổi bật, phụ kiện. Lưu dạng JSON vào ô ẩn #cd-detail. */
(function () {
	'use strict';

	function init() {
		var box = document.getElementById('cd-editor');
		var out = document.getElementById('cd-detail');
		if (!box || !out) return;

		function adm() { return window.ADM || {}; }

		var SCHEMA = [
			{ key: 'versions', label: 'Phiên bản', type: 'list', add: '+ Thêm phiên bản', title: 'name', hint: 'Mỗi phiên bản: ảnh xe nhìn ngang (nền trắng/trong suốt, ~1600 × 800 px), mô tả ngắn, danh sách trang bị và các màu.', fields: [
				{ key: 'name', label: 'Tên phiên bản (giống ô Phiên bản & giá)', type: 'text' },
				{ key: 'image', label: 'Ảnh xe của phiên bản', type: 'image' },
				{ key: 'desc', label: 'Mô tả ngắn', type: 'textarea', rows: 2 },
				{ key: 'equipment', label: 'Trang thiết bị (mỗi dòng một ý)', type: 'textarea', rows: 8 },
				{ key: 'colors', label: 'Màu xe', type: 'list', add: '+ Thêm màu', title: 'name', compact: true, fields: [
					{ key: 'name', label: 'Tên màu', type: 'text' },
					{ key: 'hex', label: 'Màu chấm', type: 'color' },
					{ key: 'image', label: 'Ảnh xe màu này', type: 'image' }
				] }
			] },
			{ key: 'specs', label: 'Thông số kỹ thuật (bảng so sánh)', type: 'list', add: '+ Thêm nhóm thông số', title: 'title', hint: 'Mỗi dòng: Tên thông số | giá trị phiên bản 1 | giá trị phiên bản 2 … (theo đúng thứ tự phiên bản ở trên). Nếu các bản giống nhau chỉ cần ghi 1 giá trị.', fields: [
				{ key: 'title', label: 'Tên nhóm (ví dụ: Động cơ và vận hành)', type: 'text' },
				{ key: 'rows', label: 'Các dòng thông số', type: 'textarea', rows: 6, placeholder: 'Công suất cực đại | 163 PS\nTúi khí | 6 | 4' }
			] },
			{ key: 'highlight', label: 'Banner “Đặc điểm nổi bật”', type: 'group', hint: 'Ảnh ngang lớn ~2000 × 860 px, khung đen chữ trắng nằm bên phải.', fields: [
				{ key: 'image', label: 'Ảnh banner', type: 'image' },
				{ key: 'kicker', label: 'Chữ nhỏ phía trên', type: 'text' },
				{ key: 'title', label: 'Tiêu đề', type: 'text' },
				{ key: 'desc', label: 'Mô tả', type: 'textarea', rows: 2 }
			] },
			{ key: 'features', label: 'Các điểm nổi bật (ảnh và chữ xen kẽ trái – phải)', type: 'list', add: '+ Thêm điểm nổi bật', title: 'title', hint: 'Ảnh ngang ~1600 × 900 px. Nút “Tìm hiểu thêm” mở form tư vấn.', fields: [
				{ key: 'image', label: 'Ảnh', type: 'image' },
				{ key: 'kicker', label: 'Chữ nhỏ phía trên (ví dụ: AN TOÀN)', type: 'text' },
				{ key: 'title', label: 'Tiêu đề', type: 'text' },
				{ key: 'desc', label: 'Mô tả', type: 'textarea', rows: 3 }
			] },
			{ key: 'accessories', label: 'Phụ kiện chính hãng', type: 'group', fields: [
				{ key: 'title', label: 'Tiêu đề', type: 'text' },
				{ key: 'desc', label: 'Mô tả', type: 'textarea', rows: 2 },
				{ key: 'image', label: 'Ảnh phụ kiện chính (~1200 × 700 px)', type: 'image' },
				{ key: 'items', label: 'Danh sách ảnh phụ kiện khác (nếu có)', type: 'list', add: '+ Thêm ảnh phụ kiện', title: 'name', compact: true, fields: [
					{ key: 'image', label: 'Ảnh phụ kiện', type: 'image' },
					{ key: 'name', label: 'Tên phụ kiện / Chú thích (tùy chọn)', type: 'text' }
				] }
			] },
			{ key: 'partner', label: 'Phụ kiện hợp tác bên thứ 3', type: 'group', fields: [
				{ key: 'image', label: 'Ảnh', type: 'image' },
				{ key: 'kicker', label: 'Chữ nhỏ phía trên', type: 'text' },
				{ key: 'title', label: 'Tiêu đề', type: 'text' },
				{ key: 'desc', label: 'Mô tả', type: 'textarea', rows: 2 }
			] }
		];

		var data;
		try { data = JSON.parse(out.value || '{}') || {}; } catch (e) { data = {}; }
		if (Array.isArray(data)) data = {};

		// Phiên bản chưa có: lấy theo ô "Phiên bản & giá".
		var vt = document.querySelector('textarea[name="versions"]');
		if ((!data.versions || !data.versions.length) && vt) {
			data.versions = vt.value.split(/\r?\n/).map(function (l) { return l.split('|')[0].trim(); }).filter(Boolean)
				.map(function (n) { return { name: n, image: '', desc: '', equipment: '', colors: [] }; });
		}

		function save() { out.value = JSON.stringify(data); }
		function el(tag, cls, html) { var x = document.createElement(tag); if (cls) x.className = cls; if (html != null) x.innerHTML = html; return x; }
		function esc(s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
		function src(v) { return !v ? '' : /^(https?:)?\//.test(v) ? v : (adm().base || '/') + v; }

		function imageField(obj, f) {
			var w = el('div', 'cde-img');
			var prev = el('div', 'cde-img__prev');
			var paint = function () { prev.innerHTML = obj[f.key] ? '<img src="' + esc(src(obj[f.key])) + '" alt="">' : '<span>Chưa có ảnh</span>'; rm.hidden = !obj[f.key]; };
			var file = el('input'); file.type = 'file'; file.accept = 'image/jpeg,image/png,image/webp,image/gif';
			var status = el('span', 'cde-img__status');
			var rm = el('button', 'cde-link', 'Bỏ ảnh'); rm.type = 'button';
			rm.onclick = function () { obj[f.key] = ''; save(); paint(); };
			file.onchange = function () {
				var fl = file.files && file.files[0];
				if (!fl) return;
				var oldSize = fl.size, note = '';
				var fmt = window.ttFormatBytes || function (b) { return Math.round(b / 1024) + ' KB'; };
				status.textContent = 'Đang nén ảnh…';
				// Tự nén ảnh: tối đa 1920 px, ảnh > 300 KB được nén lại; ảnh nền trong suốt (PNG) giữ trong suốt.
				Promise.resolve(window.ttCompressImage ? window.ttCompressImage(fl, 1920, 0.85) : fl)
					.catch(function () { return fl; })
					.then(function (out) {
						out = out || fl;
						if (out.size < oldSize) note = ' (' + fmt(oldSize) + ' → ' + fmt(out.size) + ')';
						status.textContent = 'Đang tải ảnh…';
						var fd = new FormData(); fd.append('file', out, out.name || fl.name); fd.append('csrf', adm().csrf || '');
						return fetch(adm().upload || '', { method: 'POST', body: fd, headers: { 'X-CSRF': adm().csrf || '' }, credentials: 'same-origin' });
					})
					.then(function (r) { return r.json(); })
					.then(function (j) {
						if (j.location) { obj[f.key] = j.location; save(); paint(); status.textContent = 'Đã tải ảnh ✓' + note; }
						else status.textContent = j.error || 'Không tải được ảnh';
					})
					.catch(function () { status.textContent = 'Mất kết nối khi tải ảnh'; })
					.then(function () { file.value = ''; });
			};
			var ctrl = el('div', 'cde-img__ctrl');
			ctrl.appendChild(file); ctrl.appendChild(status); ctrl.appendChild(rm);
			w.appendChild(prev); w.appendChild(ctrl);
			paint();
			return w;
		}

		function renderFields(container, fields, obj) {
			fields.forEach(function (f) {
				if (f.type === 'list' || f.type === 'group') {
					container.appendChild(renderBlock(f, obj));
					return;
				}
				var row = el('label', 'cde-field' + (f.type === 'textarea' || f.type === 'image' ? ' cde-field--wide' : ''));
				row.appendChild(el('span', 'cde-label', esc(f.label)));
				var input;
				if (f.type === 'image') {
					row = el('div', 'cde-field cde-field--wide');
					row.appendChild(el('span', 'cde-label', esc(f.label)));
					row.appendChild(imageField(obj, f));
					container.appendChild(row);
					return;
				}
				if (f.type === 'textarea') { input = el('textarea'); input.rows = f.rows || 3; }
				else { input = el('input'); input.type = f.type === 'color' ? 'color' : 'text'; }
				if (f.placeholder) input.placeholder = f.placeholder;
				input.value = obj[f.key] != null ? obj[f.key] : (f.type === 'color' ? '#ffffff' : '');
				if (f.type === 'color' && obj[f.key] == null) obj[f.key] = '#ffffff';
				input.addEventListener('input', function () { obj[f.key] = input.value; save(); var t = input.closest('.cde-item'); if (t && t._title) t._title(); });
				row.appendChild(input);
				container.appendChild(row);
			});
		}

		function renderBlock(f, parent) {
			var wrap = el('div', 'cde-block' + (f.compact ? ' cde-block--sub' : ''));
			var head = el('div', 'cde-block__head');
			head.appendChild(el('strong', '', esc(f.label)));
			wrap.appendChild(head);
			if (f.hint) wrap.appendChild(el('p', 'hint', esc(f.hint)));
			if (f.type === 'group') {
				if (!parent[f.key] || typeof parent[f.key] !== 'object' || Array.isArray(parent[f.key])) parent[f.key] = {};
				var g = el('div', 'cde-fields');
				renderFields(g, f.fields, parent[f.key]);
				wrap.appendChild(g);
				return wrap;
			}
			if (!Array.isArray(parent[f.key])) parent[f.key] = [];
			var list = parent[f.key];
			var items = el('div', 'cde-items');
			wrap.appendChild(items);
			var draw = function () {
				items.innerHTML = '';
				list.forEach(function (obj, i) {
					var it = el('div', 'cde-item');
					var bar = el('div', 'cde-item__bar');
					var title = el('span', 'cde-item__title');
					it._title = function () { title.textContent = (i + 1) + '. ' + ((obj[f.title] || '').trim() || '(chưa đặt tên)'); };
					it._title();
					var tools = el('span', 'cde-item__tools');
					var mk = function (label, fn, dis) { var b = el('button', 'cde-link', label); b.type = 'button'; b.disabled = !!dis; b.onclick = fn; tools.appendChild(b); };
					mk('↑', function () { list.splice(i - 1, 0, list.splice(i, 1)[0]); save(); draw(); }, i === 0);
					mk('↓', function () { list.splice(i + 1, 0, list.splice(i, 1)[0]); save(); draw(); }, i === list.length - 1);
					var toggle = el('button', 'cde-link', 'Thu gọn'); toggle.type = 'button';
					var del = el('button', 'cde-link cde-link--del', 'Xóa'); del.type = 'button';
					tools.appendChild(toggle); tools.appendChild(del);
					bar.appendChild(title); bar.appendChild(tools);
					var body = el('div', 'cde-fields');
					renderFields(body, f.fields, obj);
					toggle.onclick = function () { body.hidden = !body.hidden; toggle.textContent = body.hidden ? 'Mở' : 'Thu gọn'; };
					del.onclick = function () {
						if (del.dataset.sure) { list.splice(i, 1); save(); draw(); return; }
						del.dataset.sure = '1'; del.textContent = 'Bấm lần nữa để xóa';
						setTimeout(function () { delete del.dataset.sure; del.textContent = 'Xóa'; }, 3000);
					};
					if (list.length > 3 && !f.compact) { body.hidden = true; toggle.textContent = 'Mở'; }
					it.appendChild(bar); it.appendChild(body);
					items.appendChild(it);
				});
			};
			draw();
			var add = el('button', 'btn btn--sm cde-add', esc(f.add || '+ Thêm')); add.type = 'button';
			add.onclick = function () {
				var o = {};
				f.fields.forEach(function (x) { o[x.key] = x.type === 'list' ? [] : x.type === 'group' ? {} : x.type === 'color' ? '#ffffff' : ''; });
				list.push(o); save(); draw();
			};
			wrap.appendChild(add);
			return wrap;
		}

		if (!document.getElementById('cde-style')) {
			var css = el('style', '', [
				'.cde{display:flex;flex-direction:column;gap:18px;margin-top:12px}',
				'.cde-block{border:1px solid #e3e3e3;border-radius:10px;padding:14px 16px;background:#fcfcfc}',
				'.cde-block--sub{background:#fff;margin-top:6px}',
				'.cde-block__head{font-size:15px;margin-bottom:6px}',
				'.cde-items{display:flex;flex-direction:column;gap:10px;margin:8px 0}',
				'.cde-item{border:1px solid #e3e3e3;border-radius:8px;background:#fff}',
				'.cde-item__bar{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:8px 12px;background:#f6f6f6;border-radius:8px 8px 0 0}',
				'.cde-item__title{font-weight:700}',
				'.cde-item__tools{display:flex;gap:4px;flex-wrap:wrap}',
				'.cde-link{background:none;border:1px solid #ddd;border-radius:6px;padding:3px 9px;cursor:pointer;font:inherit;font-size:13px}',
				'.cde-link[disabled]{opacity:.35;cursor:default}',
				'.cde-link--del{color:#c00;border-color:#f3b5b5}',
				'.cde-fields{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(300px,100%),1fr));gap:12px 16px;padding:12px}',
				'.cde-block > .cde-fields{padding:6px 0 0}',
				'.cde-field{display:flex;flex-direction:column;gap:5px}',
				'.cde-field--wide,.cde-fields > .cde-block{grid-column:1/-1}',
				'.cde-label{font-weight:600;font-size:13px}',
				'.cde-field input[type=text],.cde-field textarea{width:100%;padding:8px 10px;border:1px solid #c9c9c9;border-radius:8px;font:inherit}',
				'.cde-field input[type=color]{width:60px;height:36px;padding:2px;border:1px solid #c9c9c9;border-radius:6px}',
				'.cde-img{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-start}',
				'.cde-img__prev{width:180px;min-height:90px;border:1px dashed #ccc;border-radius:8px;display:flex;align-items:center;justify-content:center;background:#fafafa;color:#999;font-size:12px;overflow:hidden}',
				'.cde-img__prev img{width:100%;height:auto;display:block}',
				'.cde-img__ctrl{display:flex;flex-direction:column;gap:6px;flex:1 1 200px}',
				'.cde-img__status{font-size:13px;color:#1a7f37}',
				'.cde-add{margin-top:4px}',
				'.cde-img__ctrl .cde-link{align-self:flex-start}'
			].join(''));
			css.id = 'cde-style';
			document.head.appendChild(css);
		}

		box.innerHTML = '';
		SCHEMA.forEach(function (f) { box.appendChild(renderBlock(f, data)); });
		save();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();

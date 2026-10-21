/* Tư vấn Mitsubishi – front-end behaviour (no dependencies). */
(function () {
	'use strict';
	var D = document;
	var CFG = window.TT || {};
	var $ = function (s, r) { return (r || D).querySelector(s); };
	var $$ = function (s, r) { return Array.prototype.slice.call((r || D).querySelectorAll(s)); };
	var fmt = function (n) { return Math.round(n).toLocaleString('vi-VN'); };

	/* ---------- Dropdowns: "Xe mới" and mega menu ---------- */
	var toggles = $$('[data-toggle]');
	function closeAll(except) {
		toggles.forEach(function (b) {
			if (b === except) return;
			b.setAttribute('aria-expanded', 'false');
			var p = D.getElementById(b.getAttribute('aria-controls'));
			if (p) p.hidden = true;
		});
	}
	toggles.forEach(function (btn) {
		btn.addEventListener('click', function (e) {
			e.stopPropagation();
			var panel = D.getElementById(btn.getAttribute('aria-controls'));
			var open = btn.getAttribute('aria-expanded') !== 'true';
			closeAll(btn);
			btn.setAttribute('aria-expanded', open ? 'true' : 'false');
			if (panel) panel.hidden = !open;
		});
	});
	D.addEventListener('click', function (e) { if (!e.target.closest('.dropdown')) closeAll(); });
	D.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeAll(); closeLightbox(); } });

	/* ---------- Banner slider ---------- */
	var slider = $('.slider');
	if (slider) {
		var slides = $$('.slider__slide', slider);
		var dots = $$('.slider__dots button', slider);
		var cur = 0, timer = null;
		var go = function (i) {
			cur = (i + slides.length) % slides.length;
			slides.forEach(function (s, k) {
				var on = k === cur;
				s.classList.toggle('is-active', on);
				s.setAttribute('aria-hidden', on ? 'false' : 'true');
				var a = s.querySelector('a'); if (a) a.tabIndex = on ? 0 : -1;
				var img = s.querySelector('img'); if (img && on) img.loading = 'eager';
			});
			dots.forEach(function (d, k) { d.setAttribute('aria-pressed', k === cur ? 'true' : 'false'); });
		};
		var stop = function () { clearInterval(timer); timer = null; };
		var start = function () { if (slides.length > 1 && !timer) timer = setInterval(function () { go(cur + 1); }, 6000); };
		var prev = $('.slider__arrow--prev', slider), next = $('.slider__arrow--next', slider);
		if (prev) prev.addEventListener('click', function () { stop(); go(cur - 1); });
		if (next) next.addEventListener('click', function () { stop(); go(cur + 1); });
		dots.forEach(function (d, k) { d.addEventListener('click', function () { stop(); go(k); }); });
		var x0 = null;
		slider.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
		slider.addEventListener('touchend', function (e) {
			if (x0 === null) return;
			var dx = e.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 40) { stop(); go(cur + (dx < 0 ? 1 : -1)); }
			x0 = null;
		});
		start();
	}

	/* ---------- Homepage car grid: 6 cars per page ---------- */
	$$('.car-grid[data-per]').forEach(function (grid) {
		var per = parseInt(grid.getAttribute('data-per'), 10) || 6;
		var cards = $$('.car-card', grid);
		var pager = grid.nextElementSibling;
		if (!pager || !pager.classList.contains('car-pager') || cards.length <= per) return;
		var pages = Math.ceil(cards.length / per), cur = 1;
		var show = function (n, scroll) {
			cur = n;
			cards.forEach(function (c, i) { c.hidden = Math.floor(i / per) + 1 !== n; });
			$$('button', pager).forEach(function (b) {
				var v = b.getAttribute('data-page');
				if (v === 'prev') b.disabled = n === 1;
				else if (v === 'next') b.disabled = n === pages;
				else b.setAttribute('aria-current', +v === n ? 'page' : 'false');
			});
			if (scroll) {
				var top = grid.closest('section') || grid;
				top.scrollIntoView({ behavior: 'smooth', block: 'start' });
			}
		};
		var html = '<button type="button" data-page="prev" aria-label="Trang trước">‹</button>';
		for (var i = 1; i <= pages; i++) html += '<button type="button" data-page="' + i + '">' + i + '</button>';
		html += '<button type="button" data-page="next" aria-label="Trang sau">›</button>';
		pager.innerHTML = html;
		pager.hidden = false;
		pager.addEventListener('click', function (e) {
			var b = e.target.closest('button');
			if (!b || b.disabled) return;
			var v = b.getAttribute('data-page');
			show(v === 'prev' ? cur - 1 : v === 'next' ? cur + 1 : +v, true);
		});
		show(1, false);
	});

	/* ---------- Lightbox for delivery photos ---------- */
	var lb = D.getElementById('tt-lightbox'), lastFocus = null;
	function closeLightbox() {
		if (!lb || lb.hidden) return;
		lb.hidden = true;
		D.body.style.overflow = '';
		if (lastFocus) lastFocus.focus();
	}
	if (lb) {
		$$('[data-full]').forEach(function (b) {
			b.addEventListener('click', function () {
				lastFocus = b;
				var img = $('img', lb);
				img.src = b.getAttribute('data-full');
				img.alt = b.getAttribute('data-alt') || '';
				lb.hidden = false;
				D.body.style.overflow = 'hidden';
				$('.lightbox__close', lb).focus();
			});
		});
		lb.addEventListener('click', function (e) { if (e.target === lb || e.target.closest('.lightbox__close')) closeLightbox(); });
	}

	/* ---------- Popup đăng ký ---------- */
	var store = {
		get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
		set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) { /* ignore */ } }
	};
	var popup = D.getElementById('tt-popup');
	var popupLast = null;
	function openPopup(car) {
		if (!popup) return false;
		closeAll();
		if (car) {
			var s = $('select[name="car"]', popup);
			if (s) s.value = car;
		}
		popupLast = D.activeElement;
		popup.hidden = false;
		D.body.classList.add('popup-open');
		var first = $('input[name="name"]', popup) || $('.popup__close', popup);
		if (first && !popup.querySelector('.lead-form__done:not([hidden])')) setTimeout(function () { first.focus(); }, 60);
		return true;
	}
	function closePopup() {
		if (!popup || popup.hidden) return;
		popup.hidden = true;
		D.body.classList.remove('popup-open');
		store.set('tt_popup_closed', String(Date.now()));
		if (popupLast && popupLast.focus) popupLast.focus();
	}
	if (popup) {
		popup.addEventListener('click', function (e) {
			if (e.target === popup || e.target.closest('[data-popup-close]')) closePopup();
		});
		D.addEventListener('keydown', function (e) {
			if (popup.hidden) return;
			if (e.key === 'Escape') closePopup();
			if (e.key === 'Tab') { // keep focus inside the popup
				var f = $$('a[href],button:not([disabled]),input:not([type=hidden]):not([tabindex="-1"]),select,textarea', popup).filter(function (el) { return el.offsetParent !== null; });
				if (!f.length) return;
				if (e.shiftKey && D.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
				else if (!e.shiftKey && D.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
			}
		});
		// Nút "Đăng ký tư vấn", "Nhận báo giá" mở popup.
		$$('[data-popup]').forEach(function (a) {
			a.addEventListener('click', function (e) {
				if (openPopup(a.getAttribute('data-car'))) e.preventDefault();
			});
		});
		// Tự hiện sau vài giây (một lần), trừ khi khách đã gửi thông tin hoặc vừa đóng popup.
		var auto = popup.getAttribute('data-auto') === '1';
		var delay = (parseFloat(popup.getAttribute('data-delay')) || 0) * 1000;
		var againMs = (parseFloat(popup.getAttribute('data-again')) || 0) * 3600 * 1000;
		var closedAt = parseInt(store.get('tt_popup_closed') || '0', 10);
		var allowed = auto && !store.get('tt_lead_done') && (!closedAt || Date.now() - closedAt > againMs);
		var shown = false;
		var tryAuto = function () {
			if (shown || !allowed || !popup.hidden) return;
			var a = D.activeElement;
			if (a && a.closest && a.closest('.lead-form')) return; // khách đang điền form khác
			shown = true;
			openPopup();
		};
		if (allowed) {
			setTimeout(tryAuto, delay);
			D.addEventListener('mouseout', function (e) { // máy tính: khi chuột rời khỏi trang
				if (!e.relatedTarget && e.clientY <= 0 && Date.now() - start > 5000) tryAuto();
			});
		}
	}
	var start = Date.now();

	/* ---------- Pre-select car / version from buttons ---------- */
	$$('[data-car]').forEach(function (a) {
		a.addEventListener('click', function () {
			var sel = $('#dang-ky select[name="car"]');
			if (sel) sel.value = a.getAttribute('data-car');
		});
	});
	$$('[data-version]').forEach(function (a) {
		a.addEventListener('click', function () {
			var sel = $('#bao-gia select[name="version"]');
			if (sel) sel.value = a.getAttribute('data-version');
		});
	});

	/* ---------- Lead forms ---------- */
	function cleanPhone(raw, loose) {
		var d = String(raw || '').replace(/\D/g, '');
		if (d.indexOf('84') === 0 && d.length === 11) d = '0' + d.slice(2);
		if (loose) { var m = d.match(/0\d{9}/); return m ? m[0] : ''; }
		return /^0\d{9}$/.test(d) ? d : '';
	}
	$$('.lead-form').forEach(function (form) {
		var opened = Date.now();
		var err = $('.lead-form__error', form);
		var btn = $('.lead-form__submit', form);
		var showErr = function (msg) { err.textContent = msg; err.hidden = !msg; };
		form.addEventListener('input', function () { showErr(''); });
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var fd = new FormData(form);
			if (!String(fd.get('name') || '').trim()) { showErr('Vui lòng nhập họ tên.'); form.elements.name.focus(); return; }
			if (!cleanPhone(fd.get('phone'))) { showErr('Vui lòng nhập số điện thoại hợp lệ (10 số, bắt đầu bằng 0).'); form.elements.phone.focus(); return; }
			if (!fd.get('agree')) { showErr('Vui lòng tích ô đồng ý để chúng tôi liên hệ tư vấn.'); return; }
			fd.append('action', 'tt_lead');
			fd.append('source', form.getAttribute('data-source') || 'form');
			fd.append('page', location.href);
			fd.append('t', String(opened));
			btn.disabled = true;
			var label = btn.textContent;
			btn.textContent = 'ĐANG GỬI…';
			fetch(CFG.ajax, { method: 'POST', body: fd, credentials: 'same-origin' })
				.then(function (r) { return r.json().catch(function () { return { success: false }; }); })
				.then(function (res) {
					if (res && res.success) {
						$('.lead-form__body', form).hidden = true;
						var t = $('.lead-form__title', form); if (t) t.hidden = true;
						var done = $('.lead-form__done', form);
						done.hidden = false;
						store.set('tt_lead_done', '1');
						done.focus();
						if (window.dataLayer) window.dataLayer.push({ event: 'generate_lead', form_source: form.getAttribute('data-source') });
						if (typeof window.gtag === 'function') window.gtag('event', 'generate_lead', { form_source: form.getAttribute('data-source') || 'lead_form' });
						if (typeof window.fbq === 'function') window.fbq('track', 'Lead');
					} else {
						showErr((res && res.data && res.data.message) || ('Chưa gửi được. Vui lòng gọi ' + (CFG.phone || '') + '.'));
					}
				})
				.catch(function () { showErr('Mất kết nối. Vui lòng thử lại hoặc gọi ' + (CFG.phone || '') + '.'); })
				.then(function () { btn.disabled = false; btn.textContent = label; });
		});
	});

	/* ---------- Calculators ---------- */
	function chipGroup(root, name, onChange) {
		var wrap = $('[data-chips="' + name + '"]', root);
		if (!wrap) return { get: function () { return null; } };
		var chips = $$('.chip', wrap);
		var value = null;
		var set = function (v) {
			value = v;
			chips.forEach(function (c) { c.setAttribute('aria-pressed', c.getAttribute('data-value') === String(v) ? 'true' : 'false'); });
		};
		var pre = chips.filter(function (c) { return c.getAttribute('aria-pressed') === 'true'; })[0] || chips[0];
		if (pre) set(pre.getAttribute('data-value'));
		chips.forEach(function (c) { c.addEventListener('click', function () { set(c.getAttribute('data-value')); onChange(); }); });
		return { get: function () { return value; }, set: set };
	}
	function carById(id) {
		var cars = CFG.cars || [];
		for (var i = 0; i < cars.length; i++) if (String(cars[i].id) === String(id)) return cars[i];
		return cars[0];
	}
	function rowsHtml(rows) {
		return rows.map(function (r) { return '<div class="result__row"><span>' + r[0] + '</span><span>' + r[1] + '</span></div>'; }).join('');
	}
	function linkWithCar(a, id) {
		if (!a) return;
		try { var u = new URL(a.href, location.href); u.searchParams.set('xe', id); a.href = u.toString(); } catch (e) { /* ignore */ }
	}

	/* ---------- Scroll Reveal (IntersectionObserver) ---------- */
	(function () {
		var selectors = [
			'.car-card', '.post-card', '.news3__col',
			'.feature', '.promo', '.customer', '.info-card',
			'.title-line', '.cta-box', '.result', '.calc__group'
		];
		var els = $$(selectors.join(','));
		if (!('IntersectionObserver' in window)) {
			els.forEach(function (el) { el.classList.add('tt-visible'); });
			return;
		}
		var vh = window.innerHeight || D.documentElement.clientHeight;
		els.forEach(function (el) {
			var rect = el.getBoundingClientRect();
			if (rect.top < vh + 80) {
				el.classList.add('tt-visible');
			} else {
				if (!el.classList.contains('tt-reveal') && !el.classList.contains('tt-reveal-left') && !el.classList.contains('tt-reveal-right') && !el.classList.contains('tt-reveal-scale')) {
					el.classList.add('tt-reveal');
				}
			}
		});
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('tt-visible');
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.1, rootMargin: '0px 0px 50px 0px' });
		els.forEach(function (el) {
			if (!el.classList.contains('tt-visible')) {
				io.observe(el);
			}
		});
	})();

	/* ---------- Ripple effect on buttons ---------- */
	D.addEventListener('click', function (e) {
		var btn = e.target.closest('.btn, .mbar a, .chip');
		if (!btn) return;
		var r = D.createElement('span');
		var rect = btn.getBoundingClientRect();
		var size = Math.max(rect.width, rect.height) * 2;
		r.style.cssText = 'position:absolute;border-radius:50%;background:rgba(255,255,255,.35);width:' + size + 'px;height:' + size + 'px;left:' + (e.clientX - rect.left - size / 2) + 'px;top:' + (e.clientY - rect.top - size / 2) + 'px;transform:scale(0);animation:tt-ripple .55s ease-out forwards;pointer-events:none;z-index:10';
		var style = D.getElementById('tt-ripple-style');
		if (!style) {
			style = D.createElement('style');
			style.id = 'tt-ripple-style';
			style.textContent = '@keyframes tt-ripple{to{transform:scale(1);opacity:0}}';
			D.head.appendChild(style);
		}
		btn.appendChild(r);
		setTimeout(function () { if (r.parentNode) r.parentNode.removeChild(r); }, 600);
	});

	/* ---------- Stat number count-up ---------- */
	(function () {
		var stats = $$('.stat__num');
		if (!stats.length || !('IntersectionObserver' in window)) return;
		var io2 = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				io2.unobserve(entry.target);
				var el = entry.target;
				var text = el.textContent.trim();
				var num = parseFloat(text.replace(/[^\d.]/g, ''));
				if (isNaN(num) || num === 0) return;
				var prefix = text.match(/^[^\d]*/)[0];
				var suffix = text.match(/[^\d.]*$/)[0];
				var start = 0, dur = 1200, startTime = null;
				var step = function (ts) {
					if (!startTime) startTime = ts;
					var prog = Math.min((ts - startTime) / dur, 1);
					var ease = 1 - Math.pow(1 - prog, 3);
					var cur = Math.round(start + (num - start) * ease);
					el.textContent = prefix + cur.toLocaleString('vi-VN') + suffix;
					if (prog < 1) requestAnimationFrame(step);
				};
				requestAnimationFrame(step);
			});
		}, { threshold: 0.5 });
		stats.forEach(function (el) { io2.observe(el); });
	})();

	$$('[data-calc]').forEach(function (root) {
		var type = root.getAttribute('data-calc');
		var out = function (k) { return $('[data-out="' + k + '"]', root); };
		var groups = {};
		var render;
		var names = type === 'fees' ? ['car', 'region'] : ['car', 'down', 'years', 'rate'];
		names.forEach(function (n) { groups[n] = chipGroup(root, n, function () { render(); }); });
		var pre = root.getAttribute('data-selected');
		if (pre && pre !== '0' && groups.car.set) groups.car.set(pre);

		if (type === 'fees') {
			render = function () {
				var car = carById(groups.car.get());
				if (!car) return;
				var F = CFG.fees || {};
				var reg = (F.regions || []).filter(function (r) { return r.id === groups.region.get(); })[0] || (F.regions || [])[0];
				var rate = reg.rate / 100 * (car.kind === 'pickup' ? (F.pickupPct || 60) / 100 : 1);
				var tb = car.price * rate;
				var tnds = (F.tnds || {})[car.kind] || 0;
				var rows = [
					['Giá niêm yết', fmt(car.price) + ' đ'],
					['Lệ phí trước bạ (' + (Math.round(rate * 1000) / 10).toString().replace('.', ',') + '%)', fmt(tb) + ' đ'],
					['Lệ phí biển số', fmt(reg.plate) + ' đ'],
					['Phí đường bộ (12 tháng)', fmt(F.road) + ' đ'],
					['Bảo hiểm TNDS bắt buộc (1 năm)', fmt(tnds) + ' đ'],
					['Giấy chứng nhận đăng kiểm', fmt(F.inspection) + ' đ']
				];
				out('title').textContent = car.name + ' · ' + reg.label;
				out('rows').innerHTML = rowsHtml(rows);
				out('total').textContent = fmt(car.price + tb + reg.plate + F.road + tnds + F.inspection) + ' đ';
				linkWithCar(out('loanlink'), car.id);
			};
		} else {
			render = function () {
				var car = carById(groups.car.get());
				if (!car) return;
				var down = parseFloat(groups.down.get()) || 0;
				var years = parseFloat(groups.years.get()) || 1;
				var rateY = parseFloat(groups.rate.get()) || 0;
				var loan = car.price * (1 - down / 100);
				var n = Math.round(years * 12);
				var r = rateY / 100 / 12;
				var principal = loan / n;
				var first = principal + loan * r;
				var last = principal + principal * r;
				var interest = loan * r * (n + 1) / 2;
				out('title').textContent = 'KẾT QUẢ – ' + car.name.toUpperCase();
				out('first').textContent = fmt(first) + ' đ';
				out('last').textContent = 'Giảm dần, tháng cuối còn khoảng ' + fmt(last) + ' đ';
				out('rows').innerHTML = rowsHtml([
					['Giá niêm yết', fmt(car.price) + ' đ'],
					['Trả trước (' + down + '%)', fmt(car.price - loan) + ' đ'],
					['Số tiền vay', fmt(loan) + ' đ'],
					['Thời hạn', n + ' tháng'],
					['Tổng lãi dự kiến', fmt(interest) + ' đ']
				]);
				linkWithCar(out('feelink'), car.id);
			};
		}
		render();
	});

	/* ---------- Click Tracking (Hotline, Zalo) ---------- */
	document.addEventListener('click', function (e) {
		var a = e.target && e.target.closest('a');
		if (!a) return;
		var href = a.getAttribute('href') || '';
		if (href.indexOf('tel:') === 0) {
			if (typeof window.gtag === 'function') window.gtag('event', 'click_hotline', { phone: href.replace('tel:', '') });
			if (window.dataLayer) window.dataLayer.push({ event: 'click_hotline' });
		} else if (href.indexOf('zalo.me') !== -1) {
			if (typeof window.gtag === 'function') window.gtag('event', 'click_zalo', { url: href });
			if (window.dataLayer) window.dataLayer.push({ event: 'click_zalo' });
		}
	});
})();

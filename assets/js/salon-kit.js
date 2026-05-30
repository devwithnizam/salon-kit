(function () {
  'use strict';

  const $ = (s, c) => (c || document).querySelector(s);
  const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));

  const MONTHS = ['January','February','March','April','May','June',
                  'July','August','September','October','November','December'];
  const DAYS   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

  function initInstance(wrap) {
    let debug = false;
    try { debug = localStorage.getItem('sk_debug') === '1' || /[?&]sk_debug=1/.test(location.search); } catch(e) {}
    function log(...args) { if (debug) console.log('[SK]', ...args); }

    const st = {
      step: 1,
      services: [],
      service: null,
      date: '',
      dateLabel: '',
      time: '',
      calYear: 0,
      calMonth: 0,
      isSubmitting: false,
    };

    function text(key) {
      const attr = 'data-' + key.replace(/_/g, '-');
      return wrap.getAttribute(attr) || '';
    }

    function vis(key) {
      const attr = 'data-' + key.replace(/_/g, '-');
      return wrap.getAttribute(attr) !== 'no';
    }

    function applyVisibility() {
      wrap.querySelectorAll('[data-sk-vis]').forEach(el => {
        const key = el.dataset.skVis.replace(/-/g, '_');
        el.style.display = vis(key) ? '' : 'none';
      });
    }

    function applyTexts() {
      wrap.querySelectorAll('[data-sk-text]').forEach(el => {
        const key = el.dataset.skText.replace(/-/g, '_');
        const val = text(key);
        if (val) {
          if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
            el.placeholder = val;
          } else {
            el.textContent = val;
          }
        }
      });
    }

    function getPanel(n) {
      return wrap.querySelector('.sb-panel[data-panel="' + n + '"]');
    }

    function goTo(step) {
      wrap.querySelectorAll('.sb-panel').forEach(p => p.classList.remove('active'));
      const panel = getPanel(step);
      if (panel && panel.style.display !== 'none') panel.classList.add('active');

      wrap.querySelectorAll('.sb-step').forEach(s => {
        const n = parseInt(s.dataset.step, 10);
        s.classList.remove('active', 'done');
        if (n === step) s.classList.add('active');
        if (n < step) s.classList.add('done');
      });

      st.step = step;
      updateSummary();

      const summary = wrap.querySelector('.sb-summary');
      if (summary && panel) {
        const btnRow = panel.querySelector('.sb-btn-row');
        if (btnRow) {
          panel.insertBefore(summary, btnRow);
        }
      }

      const focusable = panel ? panel.querySelector('button, input, [tabindex]:not([tabindex="-1"])') : null;
      if (focusable) focusable.focus();
    }

    function updateSummary() {
      const svc = wrap.querySelector('.sb-summary-value[data-sk-summary="service"]');
      const dt  = wrap.querySelector('.sb-summary-value[data-sk-summary="date"]');
      const tm  = wrap.querySelector('.sb-summary-value[data-sk-summary="time"]');

      if (svc) {
        if (st.service) {
          const s = st.service;
          let html = '';
          if (s.thumb_url && vis('show_service_images')) {
            html += '<img src="' + s.thumb_url + '" alt="" class="sk-summary-thumb">';
          }
          html += s.name;
          html += ' &mdash; ' + formatPrice(s.price);
          if (s.duration) html += ' &middot; ' + s.duration + 'min';
          if (s.break_time) html += ' &middot; ' + s.break_time + 'min break';
          svc.innerHTML = html;
        } else {
          svc.textContent = text('summary_service') || 'No service selected';
        }
      }
      if (dt) dt.textContent = st.dateLabel || text('summary_date') || 'No date selected';
      if (tm) tm.textContent = st.time || text('summary_time') || 'No time selected';
    }

    function renderServices() {
      const grid = wrap.querySelector('.sb-services-grid');
      if (!grid) { log('renderServices: grid not found'); return; }
      log('renderServices: rendering', st.services.length, 'cards');
      grid.innerHTML = '';

      if (!st.services.length) {
        grid.innerHTML = '<p class="sb-empty-msg">' + (text('msg_empty_services') || 'No services available.') + '</p>';
        log('renderServices: no services, showing empty msg');
        return;
      }

      st.services.forEach(svc => {
        let thumb = '';
        if (vis('show_service_images')) {
          thumb = svc.thumb_url
            ? '<img src="' + svc.thumb_url + '" alt="' + svc.name + '" class="sb-svc-thumb">'
            : '<div class="sb-svc-thumb" style="background:var(--sk-primary-lite)"></div>';
        }

        let desc = '';
        if (vis('show_service_desc') && svc.description) {
          desc = '<span class="sb-svc-desc">' + svc.description + '</span>';
        }

        let priceHtml = '';
        if (vis('show_service_price') && svc.price !== undefined && svc.price !== null && svc.price !== '') {
          priceHtml += '<span class="sb-svc-price">' + formatPrice(svc.price) + '</span>';
        }

        const card = document.createElement('div');
        card.className = 'sb-service-card';
        card.dataset.id = svc.id;
        card.tabIndex = 0;
        card.innerHTML =
          thumb +
          '<div class="sb-svc-info">' +
            '<span class="sb-svc-name">' + svc.name + '</span>' +
            desc +
          '</div>' +
          '<div class="sb-svc-meta">' + priceHtml + '</div>';

        card.addEventListener('click', () => selectService(svc, card));
        card.addEventListener('keydown', e => {
          if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectService(svc, card); }
        });
        grid.appendChild(card);
      });
    }

    function selectService(svc, card) {
      wrap.querySelectorAll('.sb-service-card').forEach(c => c.classList.remove('active'));
      if (card) card.classList.add('active');
      st.service = svc;
      st.date = '';
      st.dateLabel = '';
      st.time = '';
      const panel = getPanel(1);
      const btn = panel ? panel.querySelector('.sb-btn-next') : null;
      if (btn) btn.disabled = false;
      updateSummary();
    }

    function selectServiceById(id) {
      const svc = st.services.find(s => String(s.id) === String(id));
      if (!svc) {
        log('selectServiceById: no service found for id', id);
        return false;
      }
      const card = wrap.querySelector('.sb-service-card[data-id="' + id + '"]');
      selectService(svc, card);
      log('selectServiceById: selected', svc.name);
      return true;
    }

    function getServiceIdFromUrl() {
      const params = new URLSearchParams(location.search);
      if (params.has('sk_service')) return params.get('sk_service');
      const hash = location.hash;
      if (hash.startsWith('#booking')) {
        const qs = hash.indexOf('?') !== -1 ? hash.split('?')[1] : '';
        if (qs) {
          const hp = new URLSearchParams(qs);
          if (hp.has('sk_service')) return hp.get('sk_service');
        }
      }
      return null;
    }

    function tryAutoSelectService() {
      const id = getServiceIdFromUrl();
      if (id) {
        log('tryAutoSelectService: found service id in URL:', id);
        selectServiceById(id);
      }
    }

    function initCalendar() {
      const now = new Date();
      st.calYear = now.getFullYear();
      st.calMonth = now.getMonth();
      renderCalendar();
    }

    function renderCalendar() {
      const label = wrap.querySelector('.sb-cal-month-label');
      const grid  = wrap.querySelector('.sb-cal-grid');
      if (!label || !grid) return;

      label.textContent = MONTHS[st.calMonth] + ' ' + st.calYear;
      grid.innerHTML = '';

      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const firstDay = new Date(st.calYear, st.calMonth, 1).getDay();
      const daysInMonth = new Date(st.calYear, st.calMonth + 1, 0).getDate();

      for (let i = 0; i < firstDay; i++) {
        const empty = document.createElement('div');
        empty.className = 'sb-cal-day empty';
        grid.appendChild(empty);
      }

      for (let d = 1; d <= daysInMonth; d++) {
        const cellDate = new Date(st.calYear, st.calMonth, d);
        const cell = document.createElement('div');
        cell.className = 'sb-cal-day';
        cell.textContent = d;

        const m = String(st.calMonth + 1).padStart(2, '0');
        const dd = String(d).padStart(2, '0');
        const isoStr = st.calYear + '-' + m + '-' + dd;

        if (cellDate < today || cellDate.getDay() === 0) {
          cell.classList.add('disabled');
        } else {
          if (isoStr === st.date) cell.classList.add('selected');
          if (cellDate.toDateString() === today.toDateString()) cell.classList.add('today');
          cell.tabIndex = 0;

          cell.addEventListener('click', () => {
            st.date = isoStr;
            st.dateLabel = DAYS[cellDate.getDay()] + ', ' + MONTHS[st.calMonth].slice(0, 3) + ' ' + cellDate.getDate() + ', ' + st.calYear;
            renderCalendar();
            const panel = getPanel(2);
            const btn = panel ? panel.querySelector('.sb-btn-next') : null;
            if (btn) btn.disabled = false;
            updateSummary();
          });
          cell.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
              e.preventDefault();
              cell.click();
            }
          });
        }

        grid.appendChild(cell);
      }
    }

    function loadSlots(serviceId, date) {
      const grid = wrap.querySelector('.sb-time-grid');
      if (!grid) return;
      grid.innerHTML = '<div class="sk-skeleton sk-skeleton-slot"></div><div class="sk-skeleton sk-skeleton-slot"></div><div class="sk-skeleton sk-skeleton-slot"></div>';
      const panel = getPanel(3);
      const btn = panel ? panel.querySelector('.sb-btn-next') : null;
      if (btn) btn.disabled = true;

      if (!SalonKit.ajax_url) {
        grid.innerHTML = '<p class="sb-error-msg">Configuration error.</p>';
        return;
      }
      fetch(SalonKit.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'salon_get_slots',
          service_id: serviceId,
          date: date,
        }),
      })
      .then(r => r.json())
      .then(res => {
        if (res.success && res.data.length) {
          renderTimeSlots(res.data);
        } else {
          grid.innerHTML = '<p class="sb-empty-msg">' + (text('msg_empty_slots') || 'No slots available.') + '</p>';
        }
      })
      .catch(() => {
        grid.innerHTML = '<p class="sb-error-msg">' + (text('msg_error_network') || 'Failed to load.') + '</p>';
      });
    }

    function renderTimeSlots(slots) {
      const grid = wrap.querySelector('.sb-time-grid');
      if (!grid) return;
      grid.innerHTML = '';

      slots.forEach(slot => {
        const div = document.createElement('div');
        div.className = 'sb-time-slot';
        if (!slot.available) div.classList.add('disabled');
        if (st.time === slot.time) div.classList.add('active');

        const formatted = formatTime(slot.time);
        let remainingHtml = '';
        if (vis('show_remaining_slots')) {
          if (slot.remaining > 0) {
            remainingHtml = '<span class="sb-slot-remaining">' + slot.remaining + ' ' + (text('slot_remaining') || 'left') + '</span>';
          } else {
            remainingHtml = '<span class="sb-slot-remaining sb-slot-full">' + (text('slot_full') || 'Full') + '</span>';
          }
        }

        div.innerHTML = '<strong>' + formatted + '</strong>' + remainingHtml;

        if (slot.available) {
          div.tabIndex = 0;
          div.addEventListener('click', () => {
            wrap.querySelectorAll('.sb-time-slot').forEach(s => s.classList.remove('active'));
            div.classList.add('active');
            st.time = slot.time;
            const panel = getPanel(3);
            const btn = panel ? panel.querySelector('.sb-btn-next') : null;
            if (btn) btn.disabled = false;
            updateSummary();
          });
          div.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); div.click(); }
          });
        }

        grid.appendChild(div);
      });
    }

    function formatPrice(price) {
      if (price === '' || price === null || price === undefined) return '';
      const n = parseFloat(price);
      if (n === 0) return text('free_label') || 'Free';
      return '$' + price;
    }

    function formatTime(time) {
      const parts = time.split(':');
      let h = parseInt(parts[0], 10);
      const m = parts[1];
      const ampm = h >= 12 ? 'PM' : 'AM';
      h = h % 12 || 12;
      return h + ':' + m + ' ' + ampm;
    }

    function handleSubmit() {
      if (st.isSubmitting) return;

      const nameInput = wrap.querySelector('[data-sk-input="client-name"]');
      const emailInput = wrap.querySelector('[data-sk-input="client-email"]');
      const phoneInput = wrap.querySelector('[data-sk-input="client-phone"]');
      const notesInput = wrap.querySelector('[data-sk-input="client-notes"]');
      const errEl = wrap.querySelector('.sb-error-msg');

      const name  = (nameInput || {}).value || '';
      const email = (emailInput || {}).value || '';
      const phone = (phoneInput || {}).value || '';
      const notes = (notesInput || {}).value || '';

      if (vis('require_name') && !name) {
        errEl.textContent = text('msg_error_name') || 'Please enter your full name.';
        return;
      }
      if (vis('require_email') && (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))) {
        errEl.textContent = text('msg_error_email') || 'Please enter a valid email.';
        return;
      }

      errEl.textContent = '';

      const btn = wrap.querySelector('.sb-btn-submit');
      btn.classList.add('loading');
      btn.textContent = text('msg_submitting') || 'Submitting...';
      btn.disabled = true;
      st.isSubmitting = true;

      if (!SalonKit.ajax_url) {
        errEl.textContent = 'Configuration error.';
        resetBtn(btn);
        return;
      }
      fetch(SalonKit.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
          action: 'salon_save_booking',
          nonce: SalonKit.nonce,
          service_id: st.service.id,
          booking_date: st.date,
          booking_time: st.time,
          client_name: name,
          client_email: email,
          client_phone: phone || '',
          notes: notes || '',
        }),
      })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          showSuccess(name, email, res.data);
        } else {
          errEl.textContent = res.data.message || (text('msg_error_slot_taken') || 'Something went wrong.');
          resetBtn(btn);
        }
      })
      .catch(() => {
        errEl.textContent = text('msg_error_network') || 'Network error. Check your connection.';
        resetBtn(btn);
      });
    }

    function resetBtn(btn) {
      btn.classList.remove('loading');
      btn.textContent = text('submit_btn') || 'Confirm Booking';
      btn.disabled = false;
      st.isSubmitting = false;
    }

    function showSuccess(name, email, data) {
      wrap.querySelectorAll('.sb-panel').forEach(p => p.classList.remove('active'));
      const success = wrap.querySelector('.sb-success');
      if (success && success.style.display !== 'none') success.classList.add('active');

      const emailEl = wrap.querySelector('[data-sk-summary="success-email"]');
      if (emailEl) emailEl.textContent = email;

      const bookingIdEl = wrap.querySelector('[data-sk-summary="booking-id"]');
      if (bookingIdEl && data && data.booking_id_display) {
        bookingIdEl.textContent = data.booking_id_display;
      }

      const details = wrap.querySelector('.sb-success-details');
      if (details) {
        details.innerHTML =
          '<p><strong>' + (text('bsb_service') || 'Service') + ':</strong> ' + (st.service ? st.service.name : '--') + '</p>' +
          '<p><strong>' + (text('bsb_date') || 'Date') + ':</strong> ' + (st.dateLabel || st.date) + '</p>' +
          '<p><strong>' + (text('bsb_time') || 'Time') + ':</strong> ' + (st.time ? formatTime(st.time) : '--') + '</p>' +
          '<p><strong>' + (text('bsb_price') || 'Price') + ':</strong> ' + (st.service ? formatPrice(st.service.price) : '--') + '</p>';
      }

      wrap.querySelectorAll('.sb-step').forEach(s => {
        s.classList.remove('active');
        s.classList.add('done');
      });
    }

    function resetForm() {
      st.service = null;
      st.date = '';
      st.dateLabel = '';
      st.time = '';
      st.isSubmitting = false;

      wrap.querySelectorAll('.sb-service-card').forEach(c => c.classList.remove('active'));
      wrap.querySelectorAll('.sb-time-slot').forEach(s => s.classList.remove('active'));

      const nameEl = wrap.querySelector('[data-sk-input="client-name"]');
      const emailEl = wrap.querySelector('[data-sk-input="client-email"]');
      const phoneEl = wrap.querySelector('[data-sk-input="client-phone"]');
      const notesEl = wrap.querySelector('[data-sk-input="client-notes"]');
      if (nameEl) nameEl.value = '';
      if (emailEl) emailEl.value = '';
      if (phoneEl) phoneEl.value = '';
      if (notesEl) notesEl.value = '';

      const panel1 = getPanel(1);
      const btn = panel1 ? panel1.querySelector('.sb-btn-next') : null;
      if (btn) btn.disabled = true;
      const errEl = wrap.querySelector('.sb-error-msg');
      if (errEl) errEl.textContent = '';

      goTo(1);
    }

    // ── Init ──
    applyTexts();
    applyVisibility();

    log('SalonKit global:', typeof SalonKit !== 'undefined' ? 'found' : 'missing');
    log('SalonKit.services count:', SalonKit.services ? SalonKit.services.length : 0);

    st.services = SalonKit.services || [];
    try {
      const json = wrap.getAttribute('data-services');
      log('data-services attr:', json ? (json.length + ' chars') : 'missing');
      if (json) {
        const parsed = JSON.parse(json);
        if (Array.isArray(parsed) && parsed.length) {
          st.services = parsed;
          log('Using services from DOM attribute:', parsed.length);
        }
      }
    } catch(e) { log('data-services parse error:', e); }

    log('Total services after load:', st.services.length);

    const orderby = wrap.getAttribute('data-services-orderby') || 'menu_order';
    const order   = wrap.getAttribute('data-services-order') || 'asc';
    const dir     = order === 'desc' ? -1 : 1;

    st.services.sort(function (a, b) {
      var va, vb;
      switch (orderby) {
        case 'title':    va = (a.name || '').toLowerCase(); vb = (b.name || '').toLowerCase(); break;
        case 'date':     va = a.id || 0; vb = b.id || 0; break;
        case 'price':    va = parseFloat(a.price) || 0; vb = parseFloat(b.price) || 0; break;
        case 'duration': va = a.duration || 0; vb = b.duration || 0; break;
        default:         va = a.menu_order || 0; vb = b.menu_order || 0; break;
      }
      if (va < vb) return -1 * dir;
      if (va > vb) return 1 * dir;
      return 0;
    });

    renderServices();

    tryAutoSelectService();

    // ── Event binding ──
    function bind(panelNum, selector, event, fn) {
      const panel = getPanel(panelNum);
      if (!panel) return;
      const el = panel.querySelector(selector);
      if (el) el.addEventListener(event, fn);
    }

    // Step 1 → 2
    bind(1, '.sb-btn-next', 'click', () => {
      if (!st.service) return;
      st.time = '';
      initCalendar();
      goTo(2);
    });

    // Step 2
    bind(2, '.sb-btn-back', 'click', () => goTo(1));
    bind(2, '.sb-btn-next', 'click', () => {
      if (!st.date || !st.service) return;
      st.time = '';
      loadSlots(st.service.id, st.date);
      goTo(3);
    });

    // Step 3
    bind(3, '.sb-btn-back', 'click', () => goTo(2));
    bind(3, '.sb-btn-next', 'click', () => {
      if (st.time) {
        const bsbSvc = wrap.querySelector('[data-sk-summary="bsb-service"]');
        const bsbDt  = wrap.querySelector('[data-sk-summary="bsb-date"]');
        const bsbTm  = wrap.querySelector('[data-sk-summary="bsb-time"]');
        const bsbPr  = wrap.querySelector('[data-sk-summary="bsb-price"]');
        if (bsbSvc) {
          let sText = st.service ? st.service.name + ' -- ' + formatPrice(st.service.price) : '--';
          if (st.service && st.service.break_time) sText += ' -- ' + st.service.break_time + 'min break';
          bsbSvc.textContent = sText;
        }
        if (bsbDt) bsbDt.textContent = st.dateLabel || '--';
        if (bsbTm) bsbTm.textContent = formatTime(st.time);
        if (bsbPr) bsbPr.textContent = st.service ? formatPrice(st.service.price) : '--';
        goTo(4);
      }
    });

    // Step 4
    bind(4, '.sb-btn-back', 'click', () => goTo(3));

    // Calendar nav
    const calPrev = wrap.querySelector('.sb-cal-nav-prev');
    const calNext = wrap.querySelector('.sb-cal-nav-next');
    if (calPrev) {
      calPrev.addEventListener('click', () => {
        const now = new Date();
        if (st.calYear === now.getFullYear() && st.calMonth <= now.getMonth()) return;
        st.calMonth--;
        if (st.calMonth < 0) { st.calMonth = 11; st.calYear--; }
        renderCalendar();
      });
    }
    if (calNext) {
      calNext.addEventListener('click', () => {
        st.calMonth++;
        if (st.calMonth > 11) { st.calMonth = 0; st.calYear++; }
        renderCalendar();
      });
    }

    // Submit
    const submitBtn = wrap.querySelector('.sb-btn-submit');
    if (submitBtn) submitBtn.addEventListener('click', handleSubmit);

    const bookAgain = wrap.querySelector('[data-sk-action="book-again"]');
    if (bookAgain) bookAgain.addEventListener('click', resetForm);

    // Click done steps to jump back
    wrap.addEventListener('click', e => {
      const step = e.target.closest('.sb-step.done');
      if (step) {
        const n = parseInt(step.dataset.step, 10);
        if (n < st.step) goTo(n);
      }
    });

    // Enter on input → trigger next button
    const inner = wrap.querySelector('.sb-inner');
    if (inner) {
      inner.addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
          const panel = getPanel(st.step);
          if (panel) {
            const nextBtn = panel.querySelector('.sb-btn-next, .sb-btn-submit');
            if (nextBtn && !nextBtn.disabled) nextBtn.click();
          }
        }
      });
    }

    // Keyboard: Escape to go back
    wrap.addEventListener('keydown', e => {
      if (e.key === 'Escape' && st.step > 1) {
        e.preventDefault();
        const panel = getPanel(st.step);
        if (panel) {
          const backBtn = panel.querySelector('.sb-btn-back');
          if (backBtn) backBtn.click();
        }
      }
    });

    // ── URL-based auto-selection on hash change ──
    window.addEventListener('hashchange', () => {
      const id = getServiceIdFromUrl();
      if (id) {
        log('hashchange: service id found:', id);
        selectServiceById(id);
      }
    });
  }

  // ── Global click handler for same-page "Book Now" buttons ──
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.sk-book-btn');
    if (!btn) return;
    var href = btn.getAttribute('href');
    if (href && href.charAt(0) === '#') {
      e.preventDefault();
      var form = document.querySelector('.sb-wrap');
      if (form) form.scrollIntoView({ behavior: 'smooth' });
    }
  });

  function init() {
    try {
      if (typeof SalonKit === 'undefined') {
        window.SalonKit = { services: [], ajax_url: '', nonce: '' };
      }
      const wraps = $$('.sb-wrap');
      wraps.forEach(wrap => initInstance(wrap));
    } catch(e) {}
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();

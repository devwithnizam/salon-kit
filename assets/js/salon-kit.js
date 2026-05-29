(function () {
  'use strict';

  const $ = (s, c) => (c || document).querySelector(s);
  const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));

  const MONTHS = ['January','February','March','April','May','June',
                  'July','August','September','October','November','December'];
  const DAYS   = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

  let state = {
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

  function getWrap() {
    return $('#salonBookingWrap') || document.querySelector('.sb-wrap');
  }

  function text(key) {
    const el = getWrap();
    if (!el) return '';
    const attr = 'data-' + key.replace(/_/g, '-');
    return el.getAttribute(attr) || '';
  }

  function vis(key) {
    const el = getWrap();
    if (!el) return true;
    const attr = 'data-' + key.replace(/_/g, '-');
    return el.getAttribute(attr) !== 'no';
  }

  function applyVisibility() {
    $$('[data-sk-vis]').forEach(el => {
      const key = el.dataset.skVis.replace(/-/g, '_');
      const show = vis(key);
      el.style.display = show ? '' : 'none';
    });
  }

  function applyTexts() {
    $$('[data-sk-text]').forEach(el => {
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

  function goTo(step) {
    $$('.sb-panel').forEach(p => p.classList.remove('active'));
    const panel = $('#sbPanel' + step);
    if (panel && panel.style.display !== 'none') panel.classList.add('active');

    $$('.sb-step').forEach(s => {
      const n = parseInt(s.dataset.step, 10);
      s.classList.remove('active', 'done');
      if (n === step) s.classList.add('active');
      if (n < step) s.classList.add('done');
    });

    state.step = step;
    updateSummary();

    // Relocate summary bar into active panel (before button row)
    const summary = $('#sbSummaryBar');
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
    const svc = $('#sumService');
    const dt  = $('#sumDate');
    const tm  = $('#sumTime');

    if (svc) {
      if (state.service) {
        const s = state.service;
        let html = '';
        if (s.thumb_url && vis('show_service_images')) {
          html += '<img src="' + s.thumb_url + '" alt="" class="sk-summary-thumb">';
        }
        html += s.name;
        if (s.price) html += ' &mdash; $' + s.price;
        if (s.duration) html += ' &middot; ' + s.duration + 'min';
        svc.innerHTML = html;
      } else {
        svc.textContent = text('summary_service') || 'No service selected';
      }
    }
    if (dt) dt.textContent = state.dateLabel || text('summary_date') || 'No date selected';
    if (tm) tm.textContent = state.time || text('summary_time') || 'No time selected';
  }

  function renderServices() {
    const grid = $('#sbServicesGrid');
    if (!grid) return;
    grid.innerHTML = '';

    if (!state.services.length) {
      grid.innerHTML = '<p class="sb-empty-msg">' + (text('msg_empty_services') || 'No services available.') + '</p>';
      return;
    }

    state.services.forEach(svc => {
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
      if (vis('show_service_price') && svc.price) {
        priceHtml += '<span class="sb-svc-price">$' + svc.price + '</span>';
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
    $$('.sb-service-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');
    state.service = svc;
    state.date = '';
    state.dateLabel = '';
    state.time = '';
    const btn = $('#step1Next');
    if (btn) btn.disabled = false;
    updateSummary();
  }

  function initCalendar() {
    const now = new Date();
    state.calYear = now.getFullYear();
    state.calMonth = now.getMonth();
    renderCalendar();
  }

  function renderCalendar() {
    const label = $('#calMonthLabel');
    const grid  = $('#calGrid');
    if (!label || !grid) return;

    label.textContent = MONTHS[state.calMonth] + ' ' + state.calYear;
    grid.innerHTML = '';

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const firstDay = new Date(state.calYear, state.calMonth, 1).getDay();
    const daysInMonth = new Date(state.calYear, state.calMonth + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
      const empty = document.createElement('div');
      empty.className = 'sb-cal-day empty';
      grid.appendChild(empty);
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const cellDate = new Date(state.calYear, state.calMonth, d);
      const cell = document.createElement('div');
      cell.className = 'sb-cal-day';
      cell.textContent = d;

      const m = String(state.calMonth + 1).padStart(2, '0');
      const dd = String(d).padStart(2, '0');
      const isoStr = state.calYear + '-' + m + '-' + dd;

      if (cellDate < today || cellDate.getDay() === 0) {
        cell.classList.add('disabled');
      } else {
        if (isoStr === state.date) cell.classList.add('selected');
        if (cellDate.toDateString() === today.toDateString()) cell.classList.add('today');
        cell.tabIndex = 0;

        cell.addEventListener('click', () => {
          state.date = isoStr;
          state.dateLabel = DAYS[cellDate.getDay()] + ', ' + MONTHS[state.calMonth].slice(0, 3) + ' ' + cellDate.getDate() + ', ' + state.calYear;
          renderCalendar();
          const btn = $('#step2Next');
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
    const grid = $('#sbTimeGrid');
    if (!grid) return;
    grid.innerHTML = '<div class="sk-skeleton sk-skeleton-slot"></div><div class="sk-skeleton sk-skeleton-slot"></div><div class="sk-skeleton sk-skeleton-slot"></div>';
    const btn = $('#step3Next');
    if (btn) btn.disabled = true;

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
    const grid = $('#sbTimeGrid');
    if (!grid) return;
    grid.innerHTML = '';

    slots.forEach(slot => {
      const div = document.createElement('div');
      div.className = 'sb-time-slot';
      if (!slot.available) div.classList.add('disabled');
      if (state.time === slot.time) div.classList.add('active');

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
          $$('.sb-time-slot').forEach(s => s.classList.remove('active'));
          div.classList.add('active');
          state.time = slot.time;
          const btn = $('#step3Next');
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

  function formatTime(time) {
    const parts = time.split(':');
    let h = parseInt(parts[0], 10);
    const m = parts[1];
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return h + ':' + m + ' ' + ampm;
  }

  function handleSubmit() {
    if (state.isSubmitting) return;

    const name  = ($('#sbClientName')  || {}).value || '';
    const email = ($('#sbClientEmail') || {}).value || '';
    const phone = ($('#sbClientPhone') || {}).value || '';
    const notes = ($('#sbClientNotes') || {}).value || '';
    const errEl = $('#sbErrorMsg');

    if (vis('require_name') && !name) {
      errEl.textContent = text('msg_error_name') || 'Please enter your full name.';
      return;
    }
    if (vis('require_email') && (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))) {
      errEl.textContent = text('msg_error_email') || 'Please enter a valid email.';
      return;
    }

    errEl.textContent = '';

    const btn = $('#sbSubmitBtn');
    btn.classList.add('loading');
    btn.textContent = text('msg_submitting') || 'Submitting...';
    btn.disabled = true;
    state.isSubmitting = true;

    fetch(SalonKit.ajax_url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'salon_save_booking',
        nonce: SalonKit.nonce,
        service_id: state.service.id,
        booking_date: state.date,
        booking_time: state.time,
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
    state.isSubmitting = false;
  }

  function showSuccess(name, email, data) {
    $$('.sb-panel').forEach(p => p.classList.remove('active'));
    const success = $('#sbSuccess');
    if (success && success.style.display !== 'none') success.classList.add('active');

    const emailEl = $('#successEmail');
    if (emailEl) emailEl.textContent = email;

    const bookingIdEl = $('#successBookingId');
    if (bookingIdEl && data && data.booking_id_display) {
      bookingIdEl.textContent = data.booking_id_display;
    }

    const details = $('#successDetails');
    if (details) {
      details.innerHTML =
        '<p><strong>' + (text('bsb_service') || 'Service') + ':</strong> ' + (state.service ? state.service.name : '--') + '</p>' +
        '<p><strong>' + (text('bsb_date') || 'Date') + ':</strong> ' + (state.dateLabel || state.date) + '</p>' +
        '<p><strong>' + (text('bsb_time') || 'Time') + ':</strong> ' + (state.time ? formatTime(state.time) : '--') + '</p>' +
        '<p><strong>' + (text('bsb_price') || 'Price') + ':</strong> ' + (state.service ? '$' + state.service.price : '--') + '</p>';
    }

    $$('.sb-step').forEach(s => {
      s.classList.remove('active');
      s.classList.add('done');
    });
  }

  function resetForm() {
    state.service = null;
    state.date = '';
    state.dateLabel = '';
    state.time = '';
    state.isSubmitting = false;

    $$('.sb-service-card').forEach(c => c.classList.remove('active'));
    $$('.sb-time-slot').forEach(s => s.classList.remove('active'));

    const nameEl = $('#sbClientName');
    const emailEl = $('#sbClientEmail');
    const phoneEl = $('#sbClientPhone');
    const notesEl = $('#sbClientNotes');
    if (nameEl) nameEl.value = '';
    if (emailEl) emailEl.value = '';
    if (phoneEl) phoneEl.value = '';
    if (notesEl) notesEl.value = '';

    const btn = $('#step1Next');
    if (btn) btn.disabled = true;
    const errEl = $('#sbErrorMsg');
    if (errEl) errEl.textContent = '';

    goTo(1);
  }

  function handleKeydown(e) {
    if (e.key === 'Escape' && state.step > 1) {
      e.preventDefault();
      const backBtn = $('#step' + state.step + 'Back');
      if (backBtn) backBtn.click();
    }
  }

  function init() {
    const wrap = getWrap();
    if (!wrap) return;

    applyTexts();
    applyVisibility();

    state.services = SalonKit.services || [];

    // Sort services by widget-defined order
    const orderby = wrap.getAttribute('data-services-orderby') || 'menu_order';
    const order   = wrap.getAttribute('data-services-order') || 'asc';
    const dir     = order === 'desc' ? -1 : 1;

    state.services.sort(function (a, b) {
      var va, vb;
      switch (orderby) {
        case 'title':    va = (a.name || '').toLowerCase(); vb = (b.name || '').toLowerCase(); break;
        case 'date':     va = a.id || 0; vb = b.id || 0; break;
        case 'price':    va = parseFloat(a.price) || 0; vb = parseFloat(b.price) || 0; break;
        case 'duration': va = a.duration || 0; vb = b.duration || 0; break;
        default:         va = a.menu_order || 0; vb = b.menu_order || 0; break; // menu_order
      }
      if (va < vb) return -1 * dir;
      if (va > vb) return 1 * dir;
      return 0;
    });

    renderServices();

    // Relocate summary bar into initial active panel
    const initialPanel = $('#sbPanel1');
    const summary = $('#sbSummaryBar');
    if (summary && initialPanel) {
      const btnRow = initialPanel.querySelector('.sb-btn-row');
      if (btnRow) initialPanel.insertBefore(summary, btnRow);
    }

    const bind = (id, event, fn) => {
      const el = $(id);
      if (el) el.addEventListener(event, fn);
    };

    // Step 1 → 2 (Service → Date)
    bind('#step1Next', 'click', () => {
      if (!state.service) return;
      state.time = '';
      initCalendar();
      goTo(2);
    });

    // Step 2 navigation
    bind('#step2Back', 'click', () => goTo(1));
    bind('#step2Next', 'click', () => {
      if (!state.date || !state.service) return;
      state.time = '';
      loadSlots(state.service.id, state.date);
      goTo(3);
    });

    // Step 3 navigation
    bind('#step3Back', 'click', () => goTo(2));
    bind('#step3Next', 'click', () => {
      if (state.time) {
        const bsbSvc = $('#bsbService');
        const bsbDt  = $('#bsbDate');
        const bsbTm  = $('#bsbTime');
        const bsbPr  = $('#bsbPrice');
        if (bsbSvc) bsbSvc.textContent = state.service ? state.service.name + ' -- $' + state.service.price : '--';
        if (bsbDt) bsbDt.textContent = state.dateLabel || '--';
        if (bsbTm) bsbTm.textContent = formatTime(state.time);
        if (bsbPr) bsbPr.textContent = state.service ? '$' + state.service.price : '--';
        goTo(4);
      }
    });

    // Step 4
    bind('#step4Back', 'click', () => goTo(3));

    // Calendar
    bind('#calPrev', 'click', () => {
      const now = new Date();
      if (state.calYear === now.getFullYear() && state.calMonth <= now.getMonth()) return;
      state.calMonth--;
      if (state.calMonth < 0) { state.calMonth = 11; state.calYear--; }
      renderCalendar();
    });

    bind('#calNext', 'click', () => {
      state.calMonth++;
      if (state.calMonth > 11) { state.calMonth = 0; state.calYear++; }
      renderCalendar();
    });

    // Submit
    bind('#sbSubmitBtn', 'click', handleSubmit);
    bind('#sbBookAgain', 'click', resetForm);

    // Click done steps to jump back
    document.addEventListener('click', e => {
      const step = e.target.closest('.sb-step.done');
      if (step) {
        const n = parseInt(step.dataset.step, 10);
        if (n < state.step) goTo(n);
      }
    });

    document.addEventListener('keydown', handleKeydown);

    // Enter on input → trigger next button in current panel
    const inner = wrap.querySelector('.sb-inner');
    if (inner) {
      inner.addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
          const panel = $('#sbPanel' + state.step);
          if (panel) {
            const nextBtn = panel.querySelector('.sb-btn-next, .sb-btn-submit');
            if (nextBtn && !nextBtn.disabled) nextBtn.click();
          }
        }
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();

/**
 * SalonKit — Admin Schedule Interactions
 * Vanilla JS for multi-segment schedule, copy, exceptions, timeline
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    // ── Global click delegation ────────────────
    document.addEventListener('click', function (e) {

      // Add segment
      var addBtn = e.target.closest('.sk-sched-add-seg');
      if (addBtn) {
        var day = addBtn.closest('[data-sk-day]');
        var container = day.querySelector('[data-sk-segments]');
        var idx = container.querySelectorAll('[data-sk-seg]').length;
        var proto = container.querySelector('[data-sk-seg]');
        var clone = proto.cloneNode(true);
        // Reset indices
        clone.querySelectorAll('input').forEach(function (inp) {
          var n = inp.getAttribute('name');
          if (n) inp.setAttribute('name', n.replace(/segments\[\d+]/, 'segments[' + idx + ']'));
        });
        clone.setAttribute('data-sk-seg', idx);
        var removeBtn = clone.querySelector('.sk-sched-remove-seg');
        if (removeBtn) removeBtn.style.display = '';
        container.appendChild(clone);
        container.querySelectorAll('.sk-sched-remove-seg').forEach(function (b) { b.style.display = ''; });
        updateRange(day);
        return;
      }

      // Remove segment
      var rmBtn = e.target.closest('.sk-sched-remove-seg');
      if (rmBtn) {
        var day = rmBtn.closest('[data-sk-day]');
        var seg = rmBtn.closest('[data-sk-seg]');
        if (seg) {
          seg.remove();
          var remaining = day.querySelectorAll('[data-sk-seg]');
          if (remaining.length <= 1) {
            day.querySelectorAll('.sk-sched-remove-seg').forEach(function (b) { b.style.display = 'none'; });
          }
          updateRange(day);
        }
        return;
      }

      // Copy single day
      var copyBtn = e.target.closest('[data-sk-copy]');
      if (copyBtn && !copyBtn.hasAttribute('data-sk-copy-all')) {
        showCopyDialog(copyBtn.getAttribute('data-sk-copy'));
        return;
      }

      // Copy all weekdays
      if (e.target.closest('[data-sk-copy-all]')) {
        copyAllWeekdays();
        return;
      }

      // Add exception
      if (e.target.closest('[data-sk-add-exc]')) {
        addExceptionRow();
        return;
      }

      // Remove exception
      var rmExc = e.target.closest('.sk-sched-remove-exc');
      if (rmExc) {
        rmExc.closest('[data-sk-exc]').remove();
        return;
      }
    });

    // ── Toggle day body ──────────────────────────
    document.querySelectorAll('[data-sk-toggle]').forEach(function (cb) {
      cb.addEventListener('change', function () {
        var day = this.closest('[data-sk-day]');
        if (day) day.classList.toggle('sk-sched-day--on', this.checked);
        updateRange(day);
      });
    });

    // ── Update range text ────────────────────────
    function updateRange(day) {
      var range = day && day.querySelector('.sk-sched-range');
      if (!range) return;
      var segs = day.querySelectorAll('[data-sk-seg]');
      if (!segs.length || !day.querySelector('[data-sk-toggle]').checked) {
        range.innerHTML = '\u2014';
        return;
      }
      var first = segs[0].querySelector('input[type="time"]:first-child');
      var last  = segs[segs.length - 1].querySelectorAll('input[type="time"]');
      var fVal = first ? first.value : '--';
      var lVal = last.length > 1 ? last[1].value : '--';
      var extra = segs.length > 1 ? ' <span class="sk-plus">+' + (segs.length - 1) + '</span>' : '';
      range.innerHTML = fVal + ' \u2013 ' + lVal + extra;
      updateTimeline(day);
    }

    // ── Auto-update range on time change ──────────
    document.querySelectorAll('[data-sk-sched]').forEach(function (sched) {
      sched.addEventListener('change', function (e) {
        var inp = e.target.closest('input[type="time"], input[type="number"]');
        if (inp) {
          var day = inp.closest('[data-sk-day]');
          if (day) updateRange(day);
        }
      });
    });

    // ── Copy dialog ───────────────────────────────
    function showCopyDialog(fromDay) {
      var days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
      var labels = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
      var fromLabel = labels[days.indexOf(fromDay)] || fromDay;

      var overlay = document.createElement('div');
      overlay.className = 'sk-modal-overlay';
      overlay.innerHTML =
        '<div class="sk-modal">' +
          '<h3>Copy schedule from <strong>' + fromLabel + '</strong></h3>' +
          '<p class="sk-modal-desc">Select days to apply this schedule to:</p>' +
          '<div class="sk-modal-days">' +
            labels.map(function (l, i) {
              var d = days[i];
              var checked = d !== fromDay ? ' checked' : '';
              var disabled = d === fromDay ? ' disabled' : '';
              return '<label class="sk-modal-day' + (d === fromDay ? ' sk-modal-day--src' : '') + '">' +
                '<input type="checkbox" value="' + d + '"' + checked + disabled + '> ' + l +
              '</label>';
            }).join('') +
          '</div>' +
          '<div class="sk-modal-actions">' +
            '<button class="button button-primary sk-modal-apply">Apply</button> ' +
            '<button class="button sk-modal-cancel">Cancel</button>' +
          '</div>' +
        '</div>';
      document.body.appendChild(overlay);

      overlay.querySelector('.sk-modal-cancel').addEventListener('click', function () { overlay.remove(); });
      overlay.querySelector('.sk-modal-apply').addEventListener('click', function () {
        var checked = overlay.querySelectorAll('.sk-modal-days input:checked');
        checked.forEach(function (cb) {
          copyDaySchedule(fromDay, cb.value);
        });
        overlay.remove();
      });
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) overlay.remove();
      });
    }

    // ── Copy day schedule to another day ──────────
    function copyDaySchedule(from, to) {
      var fromDay = document.querySelector('[data-sk-day="' + from + '"]');
      var toDay   = document.querySelector('[data-sk-day="' + to + '"]');
      if (!fromDay || !toDay) return;

      // Copy toggle state
      var fromToggle = fromDay.querySelector('[data-sk-toggle]');
      var toToggle   = toDay.querySelector('[data-sk-toggle]');
      if (fromToggle && toToggle) {
        toToggle.checked = fromToggle.checked;
        toDay.classList.toggle('sk-sched-day--on', fromToggle.checked);
      }

      // Copy buffer & max_daily
      fromDay.querySelectorAll('.sk-sched-opt input').forEach(function (inp) {
        var name = inp.getAttribute('name');
        if (!name) return;
        var toInp = toDay.querySelector('[name="' + name.replace('[' + from + ']', '[' + to + ']') + '"]');
        if (toInp) toInp.value = inp.value;
      });

      // Copy segments
      var toSegs = toDay.querySelector('[data-sk-segments]');
      if (!toSegs) return;
      var fromSegs = fromDay.querySelectorAll('[data-sk-seg]');
      toSegs.innerHTML = '';
      fromSegs.forEach(function (seg, idx) {
        var clone = seg.cloneNode(true);
        var inputs = clone.querySelectorAll('input');
        inputs.forEach(function (inp) {
          var n = inp.getAttribute('name');
          if (n) inp.setAttribute('name', n.replace('[' + from + ']', '[' + to + ']').replace(/segments\[\d+]/, 'segments[' + idx + ']'));
        });
        clone.setAttribute('data-sk-seg', idx);
        toSegs.appendChild(clone);
      });

      // Hide remove if only 1 segment
      var segCount = toSegs.querySelectorAll('[data-sk-seg]').length;
      toSegs.querySelectorAll('.sk-sched-remove-seg').forEach(function (b) {
        b.style.display = segCount <= 1 ? 'none' : '';
      });

      updateRange(toDay);
    }

    // ── Apply Mon–Fri to all weekdays ────────────
    function copyAllWeekdays() {
      var monday = document.querySelector('[data-sk-day="monday"]');
      if (!monday) return;
      ['tuesday','wednesday','thursday','friday'].forEach(function (d) {
        copyDaySchedule('monday', d);
      });
    }

    // ── Add exception row ─────────────────────────
    function addExceptionRow() {
      var list = document.querySelector('[data-sk-exceptions]');
      if (!list) return;
      var idx = list.querySelectorAll('[data-sk-exc]').length;
      var div = document.createElement('div');
      div.className = 'sk-exc-row';
      div.setAttribute('data-sk-exc', idx);
      div.innerHTML =
        '<input type="date" name="sb_exceptions[' + idx + '][date]" value="">' +
        '<input type="text" name="sb_exceptions[' + idx + '][reason]" value="" placeholder="Reason (optional)" class="sk-exc-reason">' +
        '<button type="button" class="sk-sched-remove-exc button-link" title="Remove exception"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
      list.appendChild(div);
    }

    // ── Visual timeline preview ───────────────────
    function updateTimeline(day) {
      var tl = day.querySelector('.sk-sched-timeline');
      if (!tl) {
        tl = document.createElement('div');
        tl.className = 'sk-sched-timeline';
        var body = day.querySelector('.sk-sched-body');
        if (body) body.appendChild(tl);
      }
      var segs = day.querySelectorAll('[data-sk-seg]');
      if (!segs.length) { tl.innerHTML = ''; return; }

      var min = 6 * 60; // 6:00
      var max = 22 * 60; // 22:00
      var total = max - min;

      var html = '<div class="sk-tl-label">Slot coverage</div><div class="sk-tl-track">';
      segs.forEach(function (seg) {
        var startInp = seg.querySelector('input[type="time"]:first-child');
        var endInp   = seg.querySelectorAll('input[type="time"]')[1];
        if (!startInp || !endInp) return;
        var sVal = startInp.value;
        var eVal = endInp.value;
        if (!sVal || !eVal) return;
        var sMin = timeToMin(sVal);
        var eMin = timeToMin(eVal);
        if (sMin >= eMin) return;
        var left = ((sMin - min) / total) * 100;
        var width = ((eMin - sMin) / total) * 100;
        html += '<div class="sk-tl-bar" style="left:' + left + '%;width:' + width + '%"></div>';
      });
      html += '</div>';
      html += '<div class="sk-tl-labels"><span>6am</span><span>10am</span><span>2pm</span><span>6pm</span><span>10pm</span></div>';
      tl.innerHTML = html;
    }

    function timeToMin(t) {
      var parts = t.split(':');
      return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
    }

    // ── Initial timeline render ───────────────────
    document.querySelectorAll('[data-sk-day].sk-sched-day--on').forEach(function (day) {
      updateRange(day);
    });

  });
})();

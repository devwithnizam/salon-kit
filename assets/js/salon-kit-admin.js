(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

      var addBtn = e.target.closest('.sk-sched-add-seg');
      if (addBtn) {
        var day = addBtn.closest('[data-sk-day]');
        var container = day.querySelector('[data-sk-segments]');
        var idx = container.querySelectorAll('[data-sk-seg]').length;

        var proto = container.querySelector('[data-sk-seg]');
        var clone = proto.cloneNode(true);
        clone.querySelectorAll('input').forEach(function (inp) {
          var n = inp.getAttribute('name');
          if (n) inp.setAttribute('name', n.replace(/segments\[\d+]/, 'segments[' + idx + ']'));
        });
        clone.setAttribute('data-sk-seg', idx);
        var segLabel = clone.querySelector('.sk-sched-seg-label');
        if (segLabel) segLabel.textContent = 'Time segment ' + (idx + 1);
        var removeBtn = clone.querySelector('.sk-sched-remove-seg');
        if (removeBtn) removeBtn.style.display = '';
        clone.style.display = '';
        container.appendChild(clone);
        container.querySelectorAll('.sk-sched-remove-seg').forEach(function (b) { b.style.display = ''; });
        updateRange(day);
        return;
      }

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

      var copyBtn = e.target.closest('[data-sk-copy]');
      if (copyBtn && !copyBtn.hasAttribute('data-sk-copy-all') && !copyBtn.hasAttribute('data-sk-copy-mwf')) {
        showCopyDialog(copyBtn.getAttribute('data-sk-copy'));
        return;
      }

      if (e.target.closest('[data-sk-copy-all]')) {
        copyAllWeekdays();
        return;
      }

      if (e.target.closest('[data-sk-copy-mwf]')) {
        copyMWF();
        return;
      }

      if (e.target.closest('[data-sk-add-exc]')) {
        addExceptionRow();
        return;
      }

      var rmExc = e.target.closest('.sk-btn-remove');
      if (rmExc) {
        rmExc.closest('[data-sk-exc]').remove();
        return;
      }
    });

    document.querySelectorAll('[data-sk-toggle]').forEach(function (cb) {
      cb.addEventListener('change', function () {
        var day = this.closest('[data-sk-day]');
        if (day) day.classList.toggle('sk-sched-day--on', this.checked);
        updateRange(day);
      });
    });

    function updateRange(day) {
      var range = day && day.querySelector('.sk-sched-range');
      if (!range) return;
      var segs = day.querySelectorAll('[data-sk-seg]');
      if (!segs.length || !day.querySelector('[data-sk-toggle]').checked) {
        range.innerHTML = 'Closed';
        var tl = day.querySelector('[data-sk-timeline]');
        if (tl) tl.innerHTML = '';
        return;
      }
      var visible = [];
      segs.forEach(function (s) { if (s.style.display !== 'none') visible.push(s); });
      if (!visible.length) {
        range.innerHTML = 'Closed';
        var tl = day.querySelector('[data-sk-timeline]');
        if (tl) tl.innerHTML = '';
        return;
      }
      var first = visible[0].querySelector('input[type="time"]:first-child');
      var last  = visible[visible.length - 1].querySelectorAll('input[type="time"]');
      var fVal = first ? first.value : '--';
      var lVal = last.length > 1 ? last[1].value : '--';
      var extra = visible.length > 1 ? ' <span class="sk-plus">+' + (visible.length - 1) + ' more</span>' : '';
      range.innerHTML = (fVal && lVal) ? fVal + ' \u2013 ' + lVal + extra : 'Closed';
      updateTimeline(day, visible);
    }

    document.querySelectorAll('[data-sk-sched]').forEach(function (sched) {
      sched.addEventListener('change', function (e) {
        var inp = e.target.closest('input[type="time"], input[type="number"]');
        if (inp) {
          var day = inp.closest('[data-sk-day]');
          if (day) updateRange(day);
        }
      });
    });

    function showCopyDialog(fromDay) {
      var days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
      var labels = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
      var fromLabel = labels[days.indexOf(fromDay)] || fromDay;

      var overlay = document.createElement('div');
      overlay.className = 'sk-modal-overlay';
      overlay.innerHTML =
        '<div class="sk-modal">' +
          '<h3>Copy from <strong>' + fromLabel + '</strong></h3>' +
          '<p class="sk-modal-desc">Apply this schedule to:</p>' +
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

    function copyDaySchedule(from, to) {
      var fromDay = document.querySelector('[data-sk-day="' + from + '"]');
      var toDay   = document.querySelector('[data-sk-day="' + to + '"]');
      if (!fromDay || !toDay) return;

      var fromToggle = fromDay.querySelector('[data-sk-toggle]');
      var toToggle   = toDay.querySelector('[data-sk-toggle]');
      if (fromToggle && toToggle) {
        toToggle.checked = fromToggle.checked;
        toDay.classList.toggle('sk-sched-day--on', fromToggle.checked);
      }

      fromDay.querySelectorAll('.sk-sched-opt input').forEach(function (inp) {
        var name = inp.getAttribute('name');
        if (!name) return;
        var toInp = toDay.querySelector('[name="' + name.replace('[' + from + ']', '[' + to + ']') + '"]');
        if (toInp) toInp.value = inp.value;
      });

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
        var segLabel = clone.querySelector('.sk-sched-seg-label');
        if (segLabel) segLabel.textContent = 'Time segment ' + (idx + 1);
        clone.style.display = '';
        toSegs.appendChild(clone);
      });

      var segCount = toSegs.querySelectorAll('[data-sk-seg]').length;
      toSegs.querySelectorAll('.sk-sched-remove-seg').forEach(function (b) {
        b.style.display = segCount <= 1 ? 'none' : '';
      });

      updateRange(toDay);
    }

    function copyAllWeekdays() {
      var monday = document.querySelector('[data-sk-day="monday"]');
      if (!monday) return;
      ['tuesday','wednesday','thursday','friday'].forEach(function (d) {
        copyDaySchedule('monday', d);
      });
    }

    function copyMWF() {
      var monday = document.querySelector('[data-sk-day="monday"]');
      if (!monday) return;
      ['wednesday','friday'].forEach(function (d) {
        copyDaySchedule('monday', d);
      });
    }

    function addExceptionRow() {
      var list = document.querySelector('[data-sk-exceptions]');
      if (!list) return;
      var idx = list.querySelectorAll('[data-sk-exc]').length;
      var div = document.createElement('div');
      div.className = 'sk-exc-row';
      div.setAttribute('data-sk-exc', idx);
      div.innerHTML =
        '<input type="date" name="sb_exceptions[' + idx + '][date]" value="">' +
        '<input type="text" name="sb_exceptions[' + idx + '][reason]" value="" placeholder="e.g. Christmas, maintenance" class="sk-exc-reason">' +
        '<button type="button" class="sk-btn-remove button-link" title="Remove exception"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
      list.appendChild(div);
    }

    function updateTimeline(day, visible) {
      var tl = day.querySelector('[data-sk-timeline]');
      if (!tl) return;

      if (!visible || !visible.length) { tl.innerHTML = ''; return; }

      var min = 6 * 60;
      var max = 22 * 60;
      var total = max - min;

      var html = '<div class="sk-tl-label">Hours overview</div><div class="sk-tl-track">';
      visible.forEach(function (seg) {
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

    document.querySelectorAll('[data-sk-day].sk-sched-day--on').forEach(function (day) {
      updateRange(day);
    });

  });
})();

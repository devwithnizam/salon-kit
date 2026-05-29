(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    document.addEventListener('click', function (e) {

      var copyBtn = e.target.closest('[data-sk-copy]');
      if (copyBtn) {
        showCopyDialog(copyBtn.getAttribute('data-sk-copy'));
        return;
      }

      if (e.target.closest('[data-sk-copy-all]')) {
        copyToDays('all');
        return;
      }

      if (e.target.closest('[data-sk-copy-mwf]')) {
        copyToDays('mwf');
        return;
      }

      if (e.target.closest('[data-sk-add-exc]')) {
        addExceptionRow();
        return;
      }

      var rmExc = e.target.closest('.sk-btn-remove');
      if (rmExc) {
        var exc = rmExc.closest('[data-sk-exc]');
        if (exc) exc.remove();
        return;
      }
    });

    document.querySelectorAll('[data-sk-toggle]').forEach(function (cb) {
      cb.addEventListener('change', function () {
        var row = this.closest('.sk-sched-tbl-row');
        if (row) row.classList.toggle('sk-sched-tbl-row--on', this.checked);
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
          copyDay(fromDay, cb.value);
        });
        overlay.remove();
      });
      overlay.addEventListener('click', function (e) {
        if (e.target === overlay) overlay.remove();
      });
    }

    function copyDay(from, to) {
      var fromRow = document.querySelector('[data-sk-day="' + from + '"]');
      var toRow   = document.querySelector('[data-sk-day="' + to + '"]');
      if (!fromRow || !toRow) return;

      // Get from-row values (the row is inside .sk-sched-tbl-row)
      var fromInputs = fromRow.querySelectorAll('input');
      var toInputs   = toRow.querySelectorAll('input');

      // Copy checkbox state
      if (fromInputs[0] && toInputs[0]) {
        toInputs[0].checked = fromInputs[0].checked;
        toRow.classList.toggle('sk-sched-tbl-row--on', fromInputs[0].checked);
      }

      // Copy start time
      if (fromInputs[1] && toInputs[1]) {
        toInputs[1].value = fromInputs[1].value;
      }

      // Copy end time
      if (fromInputs[2] && toInputs[2]) {
        toInputs[2].value = fromInputs[2].value;
      }
    }

    function copyToDays(mode) {
      var monday = document.querySelector('[data-sk-day="monday"]');
      if (!monday) return;

      var targets = [];
      if (mode === 'all') {
        targets = ['tuesday','wednesday','thursday','friday','saturday','sunday'];
      } else if (mode === 'mwf') {
        targets = ['tuesday','wednesday','thursday','friday'];
      }

      targets.forEach(function (d) {
        copyDay('monday', d);
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
        '<input type="text" name="sb_exceptions[' + idx + '][reason]" value="" placeholder="e.g. Christmas" class="sk-exc-reason">' +
        '<button type="button" class="sk-btn-remove" title="Remove"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
      list.appendChild(div);
    }

  });
})();

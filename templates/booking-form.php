<?php
defined( 'ABSPATH' ) || exit;
?><div class="sb-inner">

  <div class="sb-steps" data-sk-vis="show-step-indicator">
    <div class="sb-step active" data-step="1">
      <span class="sb-step-num">1</span>
      <span class="sb-step-label" data-sk-text="step_label_1">Service</span>
    </div>
    <div class="sb-step-line"></div>
    <div class="sb-step" data-step="2">
      <span class="sb-step-num">2</span>
      <span class="sb-step-label" data-sk-text="step_label_2">Date</span>
    </div>
    <div class="sb-step-line"></div>
    <div class="sb-step" data-step="3">
      <span class="sb-step-num">3</span>
      <span class="sb-step-label" data-sk-text="step_label_3">Time</span>
    </div>
    <div class="sb-step-line"></div>
    <div class="sb-step" data-step="4">
      <span class="sb-step-num">4</span>
      <span class="sb-step-label" data-sk-text="step_label_4">Details</span>
    </div>
  </div>

  <div class="sb-summary" id="sbSummaryBar" data-sk-vis="show-summary-bar">
    <div class="sb-summary-item" id="summaryService">
      <div class="sb-summary-icon">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16.862 4.487l1.687-1.688a2.25 2.25 0 113.182 3.182L10.582 17.13a4.5 4.5 0 01-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487z"/></svg>
      </div>
      <div class="sb-summary-body">
        <span class="sb-summary-label" data-sk-text="step_label_1">Service</span>
        <span class="sb-summary-text" data-sk-text="summary_service" id="sumService">No service selected</span>
      </div>
    </div>
    <div class="sb-summary-item" id="summaryDate">
      <div class="sb-summary-icon">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
      </div>
      <div class="sb-summary-body">
        <span class="sb-summary-label" data-sk-text="step_label_2">Date</span>
        <span class="sb-summary-text" data-sk-text="summary_date" id="sumDate">No date selected</span>
      </div>
    </div>
    <div class="sb-summary-item" id="summaryTime">
      <div class="sb-summary-icon">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      </div>
      <div class="sb-summary-body">
        <span class="sb-summary-label" data-sk-text="step_label_3">Time</span>
        <span class="sb-summary-text" data-sk-text="summary_time" id="sumTime">No time selected</span>
      </div>
    </div>
  </div>

  <div class="sb-panel active" id="sbPanel1" data-sk-vis="show-step1">
    <h2 class="sb-panel-title" data-sk-text="step1_title">Choose a Service</h2>
    <div class="sb-services-grid" id="sbServicesGrid"></div>
    <div class="sb-btn-row">
      <button class="sb-btn sb-btn-next" id="step1Next" disabled>
        <span class="sb-btn-text" data-sk-text="step1_btn">Choose Date →</span>
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>

  <div class="sb-panel" id="sbPanel2" data-sk-vis="show-step2">
    <h2 class="sb-panel-title" data-sk-text="step2_title">Choose a Date</h2>
    <div class="sb-calendar-wrap">
      <div class="sb-cal-header">
        <button class="sb-cal-nav" id="calPrev">
          <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <span class="sb-cal-month-label" id="calMonthLabel"></span>
        <button class="sb-cal-nav" id="calNext">
          <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
      <div class="sb-cal-weekdays">
        <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
      </div>
      <div class="sb-cal-grid" id="calGrid"></div>
    </div>
    <div class="sb-btn-row">
      <button class="sb-btn sb-btn-back" id="step2Back">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span class="sb-btn-text" data-sk-text="back_btn">← Back</span>
      </button>
      <button class="sb-btn sb-btn-next" id="step2Next" disabled>
        <span class="sb-btn-text" data-sk-text="step2_btn">Choose Time →</span>
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>

  <div class="sb-panel" id="sbPanel3" data-sk-vis="show-step3">
    <h2 class="sb-panel-title" data-sk-text="step3_title">Choose a Time</h2>
    <div class="sb-time-grid" id="sbTimeGrid"></div>
    <div class="sb-btn-row">
      <button class="sb-btn sb-btn-back" id="step3Back">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span class="sb-btn-text" data-sk-text="back_btn">← Back</span>
      </button>
      <button class="sb-btn sb-btn-next" id="step3Next" disabled>
        <span class="sb-btn-text" data-sk-text="step3_btn">Your Details →</span>
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>

  <div class="sb-panel" id="sbPanel4" data-sk-vis="show-step4">
    <h2 class="sb-panel-title" data-sk-text="step4_title">Your Details</h2>
    <div class="sb-booking-summary-box" data-sk-vis="show-booking-summary">
      <div class="sb-bsb-row"><span data-sk-text="bsb_service">Service</span><strong id="bsbService">--</strong></div>
      <div class="sb-bsb-row"><span data-sk-text="bsb_date">Date</span><strong id="bsbDate">--</strong></div>
      <div class="sb-bsb-row"><span data-sk-text="bsb_time">Time</span><strong id="bsbTime">--</strong></div>
      <div class="sb-bsb-row sb-bsb-total"><span data-sk-text="bsb_price">Price</span><strong id="bsbPrice">--</strong></div>
    </div>
    <div class="sb-fields">
      <div class="sb-field-group" data-sk-vis="show-field-name">
        <label for="sbClientName">
          <span data-sk-text="field_name_label">Full Name</span>
          <span class="sk-required" data-sk-vis="require_name" data-sk-text="field_required_mark">*</span>
        </label>
        <input type="text" id="sbClientName" data-sk-text="field_name_placeholder" placeholder="Jane Smith" autocomplete="name">
      </div>
      <div class="sb-field-group" data-sk-vis="show-field-email">
        <label for="sbClientEmail">
          <span data-sk-text="field_email_label">Email Address</span>
          <span class="sk-required" data-sk-vis="require_email" data-sk-text="field_required_mark">*</span>
        </label>
        <input type="email" id="sbClientEmail" data-sk-text="field_email_placeholder" placeholder="jane@example.com" autocomplete="email">
      </div>
      <div class="sb-field-group" data-sk-vis="show-field-phone">
        <label for="sbClientPhone">
          <span data-sk-text="field_phone_label">Phone Number</span>
        </label>
        <input type="tel" id="sbClientPhone" data-sk-text="field_phone_placeholder" placeholder="+1 (555) 000-0000" autocomplete="tel">
      </div>
      <div class="sb-field-group sb-field-full" data-sk-vis="show-field-notes">
        <label for="sbClientNotes">
          <span data-sk-text="field_notes_label">Special Requests / Notes</span>
        </label>
        <textarea id="sbClientNotes" data-sk-text="field_notes_placeholder" placeholder="Any allergies, preferences or special requests..." rows="3"></textarea>
      </div>
    </div>
    <p class="sb-error-msg" id="sbErrorMsg"></p>
    <div class="sb-btn-row">
      <button class="sb-btn sb-btn-back" id="step4Back">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span class="sb-btn-text" data-sk-text="back_btn">← Back</span>
      </button>
      <button class="sb-btn sb-btn-submit" id="sbSubmitBtn">
        <span class="sb-btn-text" data-sk-text="submit_btn">Confirm Booking</span>
      </button>
    </div>
  </div>

  <div class="sb-panel sb-success" id="sbSuccess" data-sk-vis="show-success">
    <div class="sb-success-icon">
      <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
    </div>
    <h2 data-sk-text="success_title">You're all booked!</h2>
    <p class="sb-booking-id-line">Booking ID: <strong id="successBookingId"></strong></p>
    <p><span data-sk-text="success_text">A confirmation email has been sent to</span> <strong id="successEmail"></strong>.</p>
    <div class="sb-success-details" id="successDetails"></div>
    <button class="sb-btn sb-btn-next" id="sbBookAgain">
      <span class="sb-btn-text" data-sk-text="book_again">Book Another Appointment</span>
    </button>
  </div>

</div>

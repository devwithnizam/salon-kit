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

  <div class="sb-panel active" data-panel="1" data-sk-vis="show-step1">
    <h2 class="sb-panel-title" data-sk-text="step1_title">Choose a Service</h2>
    <div class="sb-services-grid"><?php if (isset($svc_cards_html)) echo $svc_cards_html; ?></div>
    <div class="sb-summary" data-sk-vis="show-summary-bar">
      <div class="sb-summary-col">
        <span class="sb-summary-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M16.862 4.487l1.687-1.688a2.25 2.25 0 113.182 3.182L10.582 17.13a4.5 4.5 0 01-1.897 1.13L6 19l.74-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487z"/></svg>
        </span>
        <span class="sb-summary-content">
          <span class="sb-summary-label" data-sk-text="step_label_1">Service</span>
          <span class="sb-summary-value" data-sk-summary="service" data-sk-text="summary_service">Not selected</span>
        </span>
      </div>
      <div class="sb-summary-sep"></div>
      <div class="sb-summary-col">
        <span class="sb-summary-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </span>
        <span class="sb-summary-content">
          <span class="sb-summary-label" data-sk-text="step_label_2">Date</span>
          <span class="sb-summary-value" data-sk-summary="date" data-sk-text="summary_date">Not selected</span>
        </span>
      </div>
      <div class="sb-summary-sep"></div>
      <div class="sb-summary-col">
        <span class="sb-summary-icon">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </span>
        <span class="sb-summary-content">
          <span class="sb-summary-label" data-sk-text="step_label_3">Time</span>
          <span class="sb-summary-value" data-sk-summary="time" data-sk-text="summary_time">Not selected</span>
        </span>
      </div>
    </div>
    <div class="sb-btn-row">
      <button class="sb-btn sb-btn-next" disabled>
        <span class="sb-btn-text" data-sk-text="step1_btn">Choose Date →</span>
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>

  <div class="sb-panel" data-panel="2" data-sk-vis="show-step2">
    <h2 class="sb-panel-title" data-sk-text="step2_title">Choose a Date</h2>
    <div class="sb-calendar-wrap">
      <div class="sb-cal-header">
        <button class="sb-cal-nav sb-cal-nav-prev">
          <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <span class="sb-cal-month-label"></span>
        <button class="sb-cal-nav sb-cal-nav-next">
          <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
      <div class="sb-cal-weekdays">
        <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
      </div>
      <div class="sb-cal-grid"></div>
    </div>
    <div class="sb-btn-row">
      <button class="sb-btn sb-btn-back">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span class="sb-btn-text" data-sk-text="back_btn">← Back</span>
      </button>
      <button class="sb-btn sb-btn-next" disabled>
        <span class="sb-btn-text" data-sk-text="step2_btn">Choose Time →</span>
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>

  <div class="sb-panel" data-panel="3" data-sk-vis="show-step3">
    <h2 class="sb-panel-title" data-sk-text="step3_title">Choose a Time</h2>
    <div class="sb-time-grid"></div>
    <div class="sb-btn-row">
      <button class="sb-btn sb-btn-back">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span class="sb-btn-text" data-sk-text="back_btn">← Back</span>
      </button>
      <button class="sb-btn sb-btn-next" disabled>
        <span class="sb-btn-text" data-sk-text="step3_btn">Your Details →</span>
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </div>
  </div>

  <div class="sb-panel" data-panel="4" data-sk-vis="show-step4">
    <h2 class="sb-panel-title" data-sk-text="step4_title">Your Details</h2>
    <div class="sb-booking-summary-box" data-sk-vis="show-booking-summary">
      <div class="sb-bsb-row"><span data-sk-text="bsb_service">Service</span><strong data-sk-summary="bsb-service">--</strong></div>
      <div class="sb-bsb-row"><span data-sk-text="bsb_date">Date</span><strong data-sk-summary="bsb-date">--</strong></div>
      <div class="sb-bsb-row"><span data-sk-text="bsb_time">Time</span><strong data-sk-summary="bsb-time">--</strong></div>
      <div class="sb-bsb-row sb-bsb-total"><span data-sk-text="bsb_price">Price</span><strong data-sk-summary="bsb-price">--</strong></div>
    </div>
    <div class="sb-fields">
      <div class="sb-field-group" data-sk-vis="show-field-name">
        <label>
          <span class="sb-field-label-text">
            <span data-sk-text="field_name_label">Full Name</span>
            <span class="sk-required" data-sk-vis="require_name" data-sk-text="field_required_mark">*</span>
          </span>
          <input type="text" data-sk-input="client-name" data-sk-text="field_name_placeholder" placeholder="Jane Smith" autocomplete="name">
        </label>
      </div>
      <div class="sb-field-group" data-sk-vis="show-field-email">
        <label>
          <span class="sb-field-label-text">
            <span data-sk-text="field_email_label">Email Address</span>
            <span class="sk-required" data-sk-vis="require_email" data-sk-text="field_required_mark">*</span>
          </span>
          <input type="email" data-sk-input="client-email" data-sk-text="field_email_placeholder" placeholder="jane@example.com" autocomplete="email">
        </label>
      </div>
      <div class="sb-field-group" data-sk-vis="show-field-phone">
        <label>
          <span class="sb-field-label-text">
            <span data-sk-text="field_phone_label">Phone Number</span>
          </span>
          <input type="tel" data-sk-input="client-phone" data-sk-text="field_phone_placeholder" placeholder="+1 (555) 000-0000" autocomplete="tel">
        </label>
      </div>
      <div class="sb-field-group sb-field-full" data-sk-vis="show-field-notes">
        <label>
          <span class="sb-field-label-text">
            <span data-sk-text="field_notes_label">Special Requests / Notes</span>
          </span>
          <textarea data-sk-input="client-notes" data-sk-text="field_notes_placeholder" placeholder="Any allergies, preferences or special requests..." rows="3"></textarea>
        </label>
      </div>
    </div>
    <p class="sb-error-msg"></p>
    <div class="sb-btn-row">
      <button class="sb-btn sb-btn-back">
        <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        <span class="sb-btn-text" data-sk-text="back_btn">← Back</span>
      </button>
      <button class="sb-btn sb-btn-submit">
        <span class="sb-btn-text" data-sk-text="submit_btn">Confirm Booking</span>
      </button>
    </div>
  </div>

  <div class="sb-panel sb-success" data-panel="5" data-sk-vis="show-success">
    <div class="sb-success-icon">
      <svg class="sk-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
    </div>
    <h2 data-sk-text="success_title">You're all booked!</h2>
    <p class="sb-booking-id-line">Booking ID: <strong data-sk-summary="booking-id"></strong></p>
    <p><span data-sk-text="success_text">A confirmation email has been sent to</span> <strong data-sk-summary="success-email"></strong>.</p>
    <div class="sb-success-details"></div>
    <button class="sb-btn sb-btn-next" data-sk-action="book-again">
      <span class="sb-btn-text" data-sk-text="book_again">Book Another Appointment</span>
    </button>
  </div>

</div>

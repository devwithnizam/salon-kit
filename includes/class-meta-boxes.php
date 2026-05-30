<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Meta_Boxes {

    const DAYS   = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ];
    const LABELS = [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ];

    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_service_boxes' ] );
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_booking_boxes' ] );
        add_action( 'save_post_salon_service', [ __CLASS__, 'save_service_meta' ], 10, 2 );
        add_action( 'save_post_salon_booking',  [ __CLASS__, 'save_booking_meta' ], 10, 2 );
    }

    // ── HELPERS ───────────────────────────────────────────

    private static function field( $label, $input, $note = '' ) {
        echo '<div class="sk-fld">';
        echo '<label class="sk-fld-label">' . esc_html( $label ) . '</label>';
        echo '<div class="sk-fld-body">' . $input . '</div>';
        if ( $note ) echo '<p class="sk-fld-note">' . esc_html( $note ) . '</p>';
        echo '</div>';
    }

    private static function input_group( $html, $prefix = '', $suffix = '' ) {
        $out = '<div class="sk-ig">';
        if ( $prefix ) $out .= '<span class="sk-ig-prefix">' . esc_html( $prefix ) . '</span>';
        $out .= $html;
        if ( $suffix ) $out .= '<span class="sk-ig-suffix">' . esc_html( $suffix ) . '</span>';
        $out .= '</div>';
        return $out;
    }

    // ── SERVICE META BOXES ────────────────────────────────

    public static function register_service_boxes() {
        add_meta_box( 'sb_service_details',
            '<span class="sk-mb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M16.86 4.49l1.69-1.69a2.25 2.25 0 113.18 3.18L10.58 17.13a4.5 4.5 0 01-1.9 1.13L6 19l.74-2.69a4.5 4.5 0 011.13-1.9L16.86 4.49z"/></svg>Service Details</span>',
            [ __CLASS__, 'render_service_details' ], 'salon_service', 'normal', 'high' );
        add_meta_box( 'sb_service_schedule',
            '<span class="sk-mb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Availability Schedule</span>',
            [ __CLASS__, 'render_service_schedule' ], 'salon_service', 'normal', 'high' );
    }

    public static function render_service_details( $post ) {
        wp_nonce_field( 'sb_save_service', 'sb_service_nonce' );
        $price    = get_post_meta( $post->ID, '_sb_price', true );
        $duration = get_post_meta( $post->ID, '_sb_duration', true );
        $slot_qty = get_post_meta( $post->ID, '_sb_slot_qty', true ) ?: 1;
        $slot_interval = get_post_meta( $post->ID, '_sb_slot_interval', true ) ?: '';
        $sort_order = $post->menu_order;
        $break_meta = get_post_meta( $post->ID, '_sb_buffer', true );
        $break_time = $break_meta !== '' ? (int) $break_meta : 10;

        $currency = apply_filters( 'sk_currency_symbol', '$' );
        ?>
        <div class="sk-mb">
            <div class="sk-mb-grid">
                <div class="sk-mb-card">
                    <?php self::field(
                        'Price',
                        self::input_group(
                            '<input type="number" step="0.01" min="0" id="sb_price" name="sb_price" value="' . esc_attr( $price ) . '" placeholder="35.00" required>',
                            '',
                            $currency
                        ),
                        'How much does this service cost?'
                    ); ?>
                    <?php self::field(
                        'Duration',
                        self::input_group(
                            '<input type="number" min="5" step="5" id="sb_duration" name="sb_duration" value="' . esc_attr( $duration ) . '" placeholder="45" required>',
                            '',
                            'min'
                        ),
                        'How long does one appointment take?'
                    ); ?>
                </div>
                <div class="sk-mb-card">
                    <?php self::field(
                        'Sort Order',
                        self::input_group(
                            '<input type="number" min="0" step="1" id="sb_sort_order" name="sb_sort_order" value="' . esc_attr( $sort_order ) . '" placeholder="0">',
                            '',
                            ''
                        ),
                        'Lower numbers appear first in the booking form. Set widget sort to "Menu Order" to use this.'
                    ); ?>
                    <?php self::field(
                        'Parallel Slots',
                        self::input_group(
                            '<input type="number" min="1" id="sb_slot_qty" name="sb_slot_qty" value="' . esc_attr( $slot_qty ) . '" required>',
                            '',
                            'seats'
                        ),
                        'Max clients that can book the same time slot.'
                    ); ?>
                    <?php self::field(
                        'Slot Interval',
                        self::input_group(
                            '<input type="number" min="5" step="5" id="sb_slot_interval" name="sb_slot_interval" value="' . esc_attr( $slot_interval ) . '" placeholder="' . esc_attr( $duration ?: 45 ) . '">',
                            '',
                            'min'
                        ),
                        'Time between slot starts. Leave empty to match duration.'
                    ); ?>
                    <?php self::field(
                        'Break Time',
                        self::input_group(
                            '<input type="number" min="0" max="60" step="5" id="sb_break_time" name="sb_buffer" value="' . esc_attr( $break_time ) . '">',
                            '',
                            'min'
                        ),
                        'Gap between consecutive slots (clean-up / break). Shows in the booking form.'
                    ); ?>
                </div>
            </div>
        </div>
        <?php
    }

    public static function render_service_schedule( $post ) {
        $schedule   = (array) get_post_meta( $post->ID, '_sb_schedule', true );
        $exceptions = (array) get_post_meta( $post->ID, '_sb_exceptions', true );

        $global_max_daily = (int) get_post_meta( $post->ID, '_sb_max_daily', true );
        ?>
        <p style="margin:0 0 12px;color:var(--sk-muted);font-size:13px;line-height:1.5;">
            Set the hours your service is available each day. Enable a day and pick your open &amp; close times.
        </p>

        <div class="sk-sched-globals">
            <label class="sk-sched-global">
                <span class="sk-sched-global-label">Max per day</span>
                <input type="number" name="sb_max_daily" value="<?php echo esc_attr( $global_max_daily ); ?>" min="0" max="200" class="sk-sched-global-num">
                <span class="sk-sched-global-hint">0 = no limit</span>
            </label>
        </div>

        <div class="sk-sched-tbl">
            <div class="sk-sched-tbl-head">
                <span class="sk-sched-tbl-cell sk-sched-tbl-cell--day">Day</span>
                <span class="sk-sched-tbl-cell sk-sched-tbl-cell--status">On</span>
                <span class="sk-sched-tbl-cell sk-sched-tbl-cell--time">Open</span>
                <span class="sk-sched-tbl-cell sk-sched-tbl-cell--time">Close</span>
                <span class="sk-sched-tbl-cell sk-sched-tbl-cell--copy"></span>
            </div>
            <?php foreach ( self::DAYS as $i => $day ) :
                $day_data = $schedule[ $day ] ?? [];
                $segments = [];
                $active   = false;
                if ( ! empty( $day_data ) ) {
                    $segments = isset( $day_data['segments'] ) ? $day_data['segments'] : $day_data;
                    $active   = ! empty( $segments );
                }
                $start = ! empty( $segments[0]['start'] ) ? $segments[0]['start'] : '';
                $end   = ! empty( $segments[0]['end'] )   ? $segments[0]['end']   : '';
            ?>
            <div class="sk-sched-tbl-row<?php echo $active ? ' sk-sched-tbl-row--on' : ''; ?>" data-sk-day="<?php echo esc_attr( $day ); ?>">
                <span class="sk-sched-tbl-cell sk-sched-tbl-cell--day"><?php echo esc_html( self::LABELS[ $i ] ); ?></span>
                <label class="sk-sched-tbl-cell sk-sched-tbl-cell--status">
                    <span class="sk-toggle">
                        <input type="checkbox" name="sb_schedule[<?php echo esc_attr( $day ); ?>][active]" value="1" <?php checked( $active ); ?> data-sk-toggle>
                        <span class="sk-toggle-track"><span class="sk-toggle-knob"></span></span>
                    </span>
                </label>
                <input type="time" class="sk-sched-tbl-cell sk-sched-tbl-cell--time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][start]" value="<?php echo esc_attr( $start ); ?>" placeholder="09:00">
                <input type="time" class="sk-sched-tbl-cell sk-sched-tbl-cell--time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][end]" value="<?php echo esc_attr( $end ); ?>" placeholder="17:00">
                <span class="sk-sched-tbl-cell sk-sched-tbl-cell--copy">
                    <button type="button" class="sk-sched-copy-btn" title="Copy to other days" data-sk-copy="<?php echo esc_attr( $day ); ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    </button>
                </span>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="sk-sched-toolbar">
            <button type="button" class="button button-small" data-sk-copy-mwf>Apply Mon–Fri</button>
            <button type="button" class="button button-small" data-sk-copy-all>Apply to all days</button>
            <span class="sk-sched-toolbar-hint">Use <strong>Copy</strong> on any day to apply to specific days.</span>
        </div>

        <div class="sk-exceptions">
            <div class="sk-exc-heading">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Date Exceptions
                <span class="sk-exc-heading-desc">Block specific dates (holidays, closures)</span>
            </div>
            <div class="sk-exceptions-list" data-sk-exceptions>
                <?php foreach ( $exceptions as $ei => $exc ) : ?>
                <div class="sk-exc-row" data-sk-exc="<?php echo esc_attr( $ei ); ?>">
                    <input type="date" name="sb_exceptions[<?php echo esc_attr( $ei ); ?>][date]" value="<?php echo esc_attr( $exc['date'] ?? '' ); ?>">
                    <input type="text" name="sb_exceptions[<?php echo esc_attr( $ei ); ?>][reason]" value="<?php echo esc_attr( $exc['reason'] ?? '' ); ?>" placeholder="e.g. Christmas" class="sk-exc-reason">
                    <button type="button" class="sk-btn-remove" title="Remove">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-small" data-sk-add-exc>+ Add exception</button>
        </div>

        <?php
        // Blocked hours
        $blocked_hours = (array) get_post_meta( $post->ID, '_sb_blocked_hours', true );
        ?>
        <div class="sk-exceptions" style="margin-top:12px;">
            <div class="sk-exc-heading">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/></svg>
                Blocked Hours
                <span class="sk-exc-heading-desc">Recurring time ranges to block on specific days</span>
            </div>
            <div class="sk-exceptions-list" data-sk-blocked>
                <?php foreach ( $blocked_hours as $bi => $bh ) : ?>
                <div class="sk-exc-row" data-sk-block="<?php echo esc_attr( $bi ); ?>">
                    <select name="sb_blocked[<?php echo esc_attr( $bi ); ?>][day]" class="sk-blocked-day">
                        <?php foreach ( self::DAYS as $k => $d ) : ?>
                        <option value="<?php echo esc_attr( $d ); ?>" <?php selected( $bh['day'] ?? '', $d ); ?>><?php echo esc_html( self::LABELS[ $k ] ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="time" name="sb_blocked[<?php echo esc_attr( $bi ); ?>][start]" value="<?php echo esc_attr( $bh['start'] ?? '' ); ?>" class="sk-blocked-time">
                    <span class="sk-sched-to">→</span>
                    <input type="time" name="sb_blocked[<?php echo esc_attr( $bi ); ?>][end]" value="<?php echo esc_attr( $bh['end'] ?? '' ); ?>" class="sk-blocked-time">
                    <input type="text" name="sb_blocked[<?php echo esc_attr( $bi ); ?>][label]" value="<?php echo esc_attr( $bh['label'] ?? '' ); ?>" placeholder="e.g. Lunch" class="sk-exc-reason">
                    <button type="button" class="sk-btn-remove" title="Remove">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-small" data-sk-add-blocked>+ Add blocked hours</button>
        </div>
        <?php
    }

    public static function save_service_meta( $post_id, $post ) {
        if ( ! isset( $_POST['sb_service_nonce'] ) || ! wp_verify_nonce( $_POST['sb_service_nonce'], 'sb_save_service' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        if ( $post->post_type !== 'salon_service' ) return;

        // Sort order (menu_order)
        $new_order = absint( $_POST['sb_sort_order'] ?? 0 );
        if ( $new_order !== $post->menu_order ) {
            remove_action( 'save_post_salon_service', [ __CLASS__, 'save_service_meta' ], 10 );
            wp_update_post( [ 'ID' => $post_id, 'menu_order' => $new_order ] );
            add_action( 'save_post_salon_service', [ __CLASS__, 'save_service_meta' ], 10, 2 );
        }

        update_post_meta( $post_id, '_sb_price',    sprintf( '%.2f', floatval( $_POST['sb_price'] ?? 0 ) ) );
        update_post_meta( $post_id, '_sb_duration', max( 5, absint( $_POST['sb_duration'] ?? 0 ) ) );
        update_post_meta( $post_id, '_sb_slot_qty', max( 1, absint( $_POST['sb_slot_qty'] ?? 1 ) ) );

        $interval = absint( $_POST['sb_slot_interval'] ?? 0 );
        update_post_meta( $post_id, '_sb_slot_interval', $interval ?: '' );

        // Global buffer & max_daily (top-level only — not duplicated in schedule)
        $global_buffer    = min( 60, max( 0, absint( $_POST['sb_buffer'] ?? 10 ) ) );
        $global_max_daily = max( 0, absint( $_POST['sb_max_daily'] ?? 0 ) );
        update_post_meta( $post_id, '_sb_buffer',    $global_buffer );
        update_post_meta( $post_id, '_sb_max_daily', $global_max_daily );

        // Save schedule (buffer/max_daily no longer embedded per-day)
        $schedule = [];
        $raw = isset( $_POST['sb_schedule'] ) ? $_POST['sb_schedule'] : [];
        foreach ( self::DAYS as $day ) {
            $start  = sanitize_text_field( $raw[ $day ]['start'] ?? '' );
            $end    = sanitize_text_field( $raw[ $day ]['end'] ?? '' );
            $active = ! empty( $raw[ $day ]['active'] );

            if ( $active && $start && $end ) {
                $schedule[ $day ] = [
                    'segments' => [ [ 'start' => $start, 'end' => $end ] ],
                ];
            }
        }
        update_post_meta( $post_id, '_sb_schedule', $schedule );

        // Save exceptions
        $exceptions = [];
        $raw_exc = isset( $_POST['sb_exceptions'] ) ? $_POST['sb_exceptions'] : [];
        foreach ( $raw_exc as $e ) {
            $date   = sanitize_text_field( $e['date'] ?? '' );
            $reason = sanitize_text_field( $e['reason'] ?? '' );
            if ( $date ) $exceptions[] = [ 'date' => $date, 'reason' => $reason ];
        }
        update_post_meta( $post_id, '_sb_exceptions', $exceptions );

        // Save blocked hours
        $blocked = [];
        $raw_blk = isset( $_POST['sb_blocked'] ) ? $_POST['sb_blocked'] : [];
        foreach ( $raw_blk as $b ) {
            $day   = sanitize_text_field( $b['day'] ?? '' );
            $start = sanitize_text_field( $b['start'] ?? '' );
            $end   = sanitize_text_field( $b['end'] ?? '' );
            $label = sanitize_text_field( $b['label'] ?? '' );
            if ( $day && $start && $end ) $blocked[] = [ 'day' => $day, 'start' => $start, 'end' => $end, 'label' => $label ];
        }
        update_post_meta( $post_id, '_sb_blocked_hours', $blocked );
    }

    // ── BOOKING META BOXES ────────────────────────────────

    public static function register_booking_boxes() {
        add_meta_box( 'sb_booking_details',
            '<span class="sk-mb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Booking Details</span>',
            [ __CLASS__, 'render_booking_details' ], 'salon_booking', 'normal', 'high' );
        add_meta_box( 'sb_booking_actions',
            '<span class="sk-mb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>Booking Actions</span>',
            [ __CLASS__, 'render_booking_actions' ], 'salon_booking', 'side', 'high' );
    }

    public static function render_booking_details( $post ) {
        wp_nonce_field( 'sb_save_booking', 'sb_booking_nonce' );

        $currency = apply_filters( 'sk_currency_symbol', '$' );

        $cid    = get_post_meta( $post->ID, '_client_name', true );
        $cemail = get_post_meta( $post->ID, '_client_email', true );
        $cphone = get_post_meta( $post->ID, '_client_phone', true );
        $svc    = get_post_meta( $post->ID, '_service', true );
        $date   = get_post_meta( $post->ID, '_booking_date', true );
        $time   = get_post_meta( $post->ID, '_booking_time', true );
        $price  = get_post_meta( $post->ID, '_booking_price', true );
        $notes  = get_post_meta( $post->ID, '_booking_notes', true );
        $db_id  = get_post_meta( $post->ID, '_booking_db_id', true );
        ?>

        <div class="sk-mb">
            <div class="sk-mb-grid">
                <div class="sk-mb-card">
                    <div class="sk-mb-card-head">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Client Information
                    </div>
                    <?php self::field( 'Name',
                        '<input type="text" name="sb_client_name" value="' . esc_attr( $cid ) . '" placeholder="Client name" class="sk-input">'
                    ); ?>
                    <?php self::field( 'Email',
                        '<input type="email" name="sb_client_email" value="' . esc_attr( $cemail ) . '" placeholder="Email address" class="sk-input">'
                    ); ?>
                    <?php self::field( 'Phone',
                        '<input type="text" name="sb_client_phone" value="' . esc_attr( $cphone ) . '" placeholder="Phone number" class="sk-input">'
                    ); ?>
                </div>

                <div class="sk-mb-card">
                    <div class="sk-mb-card-head">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Appointment Details
                    </div>
                    <table class="sk-booking-info-table">
                        <tr><td class="sk-bit-label">Booking ID</td><td class="sk-bit-value"><span class="sk-booking-id-badge"><?php echo $db_id ? esc_html( '#BK-' . str_pad( $db_id, 4, '0', STR_PAD_LEFT ) ) : esc_html( '#' . $post->ID ); ?></span></td></tr>
                        <tr><td class="sk-bit-label">Service</td><td class="sk-bit-value"><?php echo esc_html( $svc ?: '—' ); ?></td></tr>
                        <tr><td class="sk-bit-label">Date</td><td class="sk-bit-value"><?php echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '—'; ?></td></tr>
                        <tr><td class="sk-bit-label">Time</td><td class="sk-bit-value"><?php echo $time ? esc_html( date_i18n( get_option( 'time_format' ), strtotime( $time ) ) ) : '—'; ?></td></tr>
                        <tr><td class="sk-bit-label">Price</td><td class="sk-bit-value"><?php echo $price ? '<span class="sk-badge sk-badge-price">' . esc_html( $currency ) . esc_html( $price ) . '</span>' : '—'; ?></td></tr>
                    </table>
                </div>
            </div>

            <div class="sk-mb-card" style="margin-top:12px;">
                <div class="sk-mb-card-head">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Notes
                </div>
                <textarea name="sb_booking_notes" rows="3" class="sk-input" style="width:100%;"><?php echo esc_textarea( $notes ); ?></textarea>
            </div>

            <input type="hidden" name="sb_service_name" value="<?php echo esc_attr( $svc ); ?>">
            <input type="hidden" name="sb_booking_date" value="<?php echo esc_attr( $date ); ?>">
            <input type="hidden" name="sb_booking_time" value="<?php echo esc_attr( $time ); ?>">
            <input type="hidden" name="sb_booking_price" value="<?php echo esc_attr( $price ); ?>">
            <input type="hidden" name="sb_db_id" value="<?php echo esc_attr( $db_id ); ?>">
        </div>
        <?php
    }

    public static function render_booking_actions( $post ) {
        $status    = get_post_meta( $post->ID, '_status', true ) ?: 'confirmed';
        $submitted = get_post_meta( $post->ID, '_submitted_at', true );
        ?>
        <div class="sk-mb">
            <div class="sk-mb-card" style="margin-bottom:0;">
                <div class="sk-fld">
                    <label class="sk-fld-label">Status</label>
                    <select name="sb_status" class="sk-input">
                        <option value="confirmed" <?php selected( $status, 'confirmed' ); ?>>Confirmed</option>
                        <option value="cancelled" <?php selected( $status, 'cancelled' ); ?>>Cancelled</option>
                        <option value="pending" <?php selected( $status, 'pending' ); ?>>Pending</option>
                    </select>
                </div>
            </div>
            <div style="padding:12px 0 4px;">
                <div class="sk-booking-meta-row">
                    <span class="sk-booking-meta-label">Submitted</span>
                    <span class="sk-booking-meta-value"><?php echo $submitted ? esc_html( wp_date( 'M j, Y g:i A', strtotime( $submitted ) ) ) : '—'; ?></span>
                </div>
                <div class="sk-booking-meta-row">
                    <span class="sk-booking-meta-label">Post ID</span>
                    <span class="sk-booking-meta-value">#<?php echo esc_html( $post->ID ); ?></span>
                </div>
            </div>
        </div>
        <?php
    }

    public static function save_booking_meta( $post_id, $post ) {
        if ( ! isset( $_POST['sb_booking_nonce'] ) || ! wp_verify_nonce( $_POST['sb_booking_nonce'], 'sb_save_booking' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        if ( $post->post_type !== 'salon_booking' ) return;

        $client_name  = sanitize_text_field( $_POST['sb_client_name'] ?? '' );
        $client_email = sanitize_email( $_POST['sb_client_email'] ?? '' );
        $client_phone = sanitize_text_field( $_POST['sb_client_phone'] ?? '' );
        $notes        = sanitize_textarea_field( $_POST['sb_booking_notes'] ?? '' );
        $status       = in_array( $_POST['sb_status'] ?? '', [ 'confirmed', 'cancelled', 'pending' ] ) ? $_POST['sb_status'] : 'confirmed';

        update_post_meta( $post_id, '_client_name',   $client_name );
        update_post_meta( $post_id, '_client_email',  $client_email );
        update_post_meta( $post_id, '_client_phone',  $client_phone );
        update_post_meta( $post_id, '_booking_notes', $notes );
        update_post_meta( $post_id, '_status',        $status );

        $fields = [
            '_service'        => 'sb_service_name',
            '_booking_date'   => 'sb_booking_date',
            '_booking_time'   => 'sb_booking_time',
            '_booking_price'  => 'sb_booking_price',
        ];
        foreach ( $fields as $meta_key => $post_key ) {
            if ( isset( $_POST[ $post_key ] ) ) {
                update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
            }
        }

        $db_id = absint( $_POST['sb_db_id'] ?? 0 );
        if ( $db_id ) {
            Bookings_DB::update_status( $db_id, $status );
        }
    }
}

<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Meta_Boxes {

    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_service_boxes' ] );
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_professional_boxes' ] );
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_booking_boxes' ] );
        add_action( 'save_post_salon_service',      [ __CLASS__, 'save_service_meta' ], 10, 2 );
        add_action( 'save_post_salon_professional', [ __CLASS__, 'save_professional_meta' ], 10, 2 );
        add_action( 'save_post_salon_booking',      [ __CLASS__, 'save_booking_meta' ], 10, 2 );
        add_action( 'admin_footer', [ __CLASS__, 'assign_search_script' ] );
    }

    public static function assign_search_script() {
        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->post_type, [ 'salon_service', 'salon_professional' ] ) ) return;
        ?>
        <script>
        document.querySelectorAll('.sk-assign-search').forEach(function(input) {
            input.addEventListener('input', function() {
                var q = this.value.toLowerCase();
                var items = this.closest('.sk-assign').querySelectorAll('.sk-assign-item');
                var count = 0;
                items.forEach(function(item) {
                    var match = item.textContent.toLowerCase().indexOf(q) !== -1;
                    item.style.display = match ? '' : 'none';
                    if (match) count++;
                });
                var label = this.closest('.sk-assign').querySelector('.sk-assign-count');
                if (label) label.textContent = count + ' of ' + items.length + ' selected';
            });
        });
        </script>
        <?php
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

    private static function assign_list( $name, $items, $selected_ids, $empty_msg, $empty_url, $image_cb = null ) {
        if ( empty( $items ) ) {
            printf( '<p class="sk-fld-note">%s <a href="%s">%s</a></p>',
                esc_html( $empty_msg ),
                esc_url( $empty_url ),
                esc_html__( 'Create one', 'salon-kit' )
            );
            return;
        }

        $total = count( $items );
        $sel   = count( $selected_ids );
        echo '<div class="sk-assign">';

        echo '<div class="sk-assign-bar">';
        echo '<input type="text" class="sk-assign-search" placeholder="Filter by name…">';
        echo '<span class="sk-assign-count">' . esc_html( $sel ) . ' of ' . esc_html( $total ) . '</span>';
        echo '</div>';

        echo '<div class="sk-assign-list">';
        foreach ( $items as $item ) {
            $id      = $item->ID;
            $checked = in_array( $id, $selected_ids, false );
            $img     = $image_cb ? $image_cb( $id ) : '';

            printf(
                '<label class="sk-assign-item%s">',
                $checked ? ' sk-assign-item--on' : ''
            );
            printf(
                '<input type="checkbox" name="%s[]" value="%s" %s>',
                esc_attr( $name ),
                esc_attr( $id ),
                checked( $checked, true, false )
            );
            echo '<span class="sk-assign-box"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>';
            if ( $img ) echo $img;
            echo '<span class="sk-assign-name">' . esc_html( $item->post_title ) . '</span>';
            echo '</label>';
        }
        echo '</div></div>';
    }

    // ── SERVICE META BOXES ────────────────────────────────

    public static function register_service_boxes() {
        add_meta_box( 'sb_service_details',
            '<span class="sk-mb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M16.86 4.49l1.69-1.69a2.25 2.25 0 113.18 3.18L10.58 17.13a4.5 4.5 0 01-1.9 1.13L6 19l.74-2.69a4.5 4.5 0 011.13-1.9L16.86 4.49z"/></svg>Service Details</span>',
            [ __CLASS__, 'render_service_details' ], 'salon_service', 'normal', 'high' );
        add_meta_box( 'sb_service_pros',
            '<span class="sk-mb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Assign Team</span>',
            [ __CLASS__, 'render_service_pros' ], 'salon_service', 'side' );
    }

    public static function render_service_details( $post ) {
        wp_nonce_field( 'sb_save_service', 'sb_service_nonce' );
        $price    = get_post_meta( $post->ID, '_sb_price', true );
        $duration = get_post_meta( $post->ID, '_sb_duration', true );
        $slot_qty = get_post_meta( $post->ID, '_sb_slot_qty', true ) ?: 1;
        ?>
        <div class="sk-mb">
            <div class="sk-mb-grid">
                <div class="sk-mb-card">
                    <?php self::field(
                        'Price',
                        self::input_group(
                            '<input type="number" step="0.01" min="0" id="sb_price" name="sb_price" value="' . esc_attr( $price ) . '" placeholder="35.00" required>',
                            '',
                            'USD'
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
                        'Parallel Slots',
                        self::input_group(
                            '<input type="number" min="1" id="sb_slot_qty" name="sb_slot_qty" value="' . esc_attr( $slot_qty ) . '" required>',
                            '',
                            'seats'
                        ),
                        'Max clients that can book the same time slot.'
                    ); ?>
                </div>
            </div>
        </div>
        <?php
    }

    public static function render_service_pros( $post ) {
        $assigned = (array) get_post_meta( $post->ID, '_sb_professionals', true );
        $pros     = get_posts( [ 'post_type' => 'salon_professional', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );
        self::assign_list(
            'sb_professionals', $pros, $assigned,
            'No professionals yet.', admin_url( 'post-new.php?post_type=salon_professional' ),
            function ( $id ) {
                $img = get_the_post_thumbnail( $id, [ 24, 24 ], [ 'class' => 'sk-assign-avatar' ] );
                return $img ?: '<span class="sk-assign-avatar sk-assign-avatar--empty">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>';
            }
        );
    }

    public static function save_service_meta( $post_id, $post ) {
        if ( ! isset( $_POST['sb_service_nonce'] ) || ! wp_verify_nonce( $_POST['sb_service_nonce'], 'sb_save_service' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        update_post_meta( $post_id, '_sb_price',    sanitize_text_field( $_POST['sb_price'] ?? '' ) );
        update_post_meta( $post_id, '_sb_duration', absint( $_POST['sb_duration'] ?? 0 ) );
        update_post_meta( $post_id, '_sb_slot_qty', max( 1, absint( $_POST['sb_slot_qty'] ?? 1 ) ) );

        $pro_ids = isset( $_POST['sb_professionals'] ) ? array_map( 'absint', $_POST['sb_professionals'] ) : [];
        update_post_meta( $post_id, '_sb_professionals', $pro_ids );
        self::sync_pros_for_service( $post_id, $pro_ids );
    }

    private static function sync_pros_for_service( $service_id, $assigned_pro_ids ) {
        $all_pros = get_posts( [ 'post_type' => 'salon_professional', 'posts_per_page' => -1, 'fields' => 'ids' ] );
        foreach ( $all_pros as $pro_id ) {
            $services = (array) get_post_meta( $pro_id, '_sb_assigned_services', true );
            if ( in_array( $pro_id, $assigned_pro_ids ) ) {
                if ( ! in_array( $service_id, $services ) ) $services[] = $service_id;
            } else {
                $services = array_values( array_diff( $services, [ $service_id ] ) );
            }
            update_post_meta( $pro_id, '_sb_assigned_services', $services );
        }
    }

    // ── PROFESSIONAL META BOXES ───────────────────────────

    public static function register_professional_boxes() {
        add_meta_box( 'sb_pro_services',
            '<span class="sk-mb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 6h12M6 12h12M6 18h8"/></svg>Assigned Services</span>',
            [ __CLASS__, 'render_pro_services' ], 'salon_professional', 'side' );
        add_meta_box( 'sb_pro_schedule',
            '<span class="sk-mb-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Weekly Schedule</span>',
            [ __CLASS__, 'render_pro_schedule' ], 'salon_professional', 'normal', 'high' );
    }

    public static function render_pro_services( $post ) {
        wp_nonce_field( 'sb_save_professional', 'sb_pro_nonce' );
        $assigned = (array) get_post_meta( $post->ID, '_sb_assigned_services', true );
        $services = get_posts( [ 'post_type' => 'salon_service', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );
        self::assign_list( 'sb_services', $services, $assigned,
            'No services yet.', admin_url( 'post-new.php?post_type=salon_service' ) );
    }

    public static function render_pro_schedule( $post ) {
        $schedule = (array) get_post_meta( $post->ID, '_sb_schedule', true );
        $days     = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ];
        $labels   = [ 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' ];
        ?>
        <div class="sk-sched">
            <?php foreach ( $days as $i => $day ) :
                $segments = $schedule[ $day ] ?? [];
                $active   = ! empty( $segments );
                $start    = $segments[0]['start'] ?? '09:00';
                $end      = $segments[0]['end']   ?? '17:00';
                $lunch_s  = $segments[1]['start'] ?? '';
                $lunch_e  = $segments[1]['end']   ?? '';
            ?>
            <div class="sk-sched-day<?php echo $active ? ' sk-sched-day--on' : ''; ?>">
                <div class="sk-sched-head">
                    <label class="sk-sched-toggle">
                        <input type="checkbox" name="sb_schedule[<?php echo esc_attr( $day ); ?>][active]" value="1" <?php checked( $active ); ?>>
                        <span class="sk-sched-track"><span class="sk-sched-knob"></span></span>
                        <span class="sk-sched-abbr"><?php echo esc_html( $labels[ $i ] ); ?></span>
                    </label>
                    <div class="sk-sched-hours">
                        <span class="sk-sched-range"><?php echo $active ? esc_html( $start ) . ' – ' . esc_html( $end ) : '—'; ?></span>
                    </div>
                </div>
                <div class="sk-sched-body">
                    <div class="sk-sched-row">
                        <label class="sk-sched-fld">
                            <span>Open</span>
                            <input type="time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][start]" value="<?php echo esc_attr( $start ); ?>">
                        </label>
                        <label class="sk-sched-fld">
                            <span>Close</span>
                            <input type="time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][end]" value="<?php echo esc_attr( $end ); ?>">
                        </label>
                    </div>
                    <div class="sk-sched-row">
                        <label class="sk-sched-fld sk-sched-fld--lunch">
                            <span>Lunch start</span>
                            <input type="time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][lunch_start]" value="<?php echo esc_attr( $lunch_s ); ?>">
                        </label>
                        <label class="sk-sched-fld sk-sched-fld--lunch">
                            <span>Lunch end</span>
                            <input type="time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][lunch_end]" value="<?php echo esc_attr( $lunch_e ); ?>">
                        </label>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <p class="sk-fld-note">Toggle a day on to set hours. Leave lunch blank for no break.</p>
        </div>
        <?php
    }

    public static function save_professional_meta( $post_id, $post ) {
        if ( ! isset( $_POST['sb_pro_nonce'] ) || ! wp_verify_nonce( $_POST['sb_pro_nonce'], 'sb_save_professional' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        $service_ids = isset( $_POST['sb_services'] ) ? array_map( 'absint', $_POST['sb_services'] ) : [];
        update_post_meta( $post_id, '_sb_assigned_services', $service_ids );
        self::sync_services_for_pro( $post_id, $service_ids );

        $schedule = [];
        $raw = isset( $_POST['sb_schedule'] ) ? $_POST['sb_schedule'] : [];
        foreach ( [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ] as $day ) {
            if ( ! empty( $raw[ $day ]['active'] ) ) {
                $start    = sanitize_text_field( $raw[ $day ]['start'] ?? '09:00' );
                $end      = sanitize_text_field( $raw[ $day ]['end']   ?? '17:00' );
                $segments = [ [ 'start' => $start, 'end' => $end ] ];
                $lunch_s  = sanitize_text_field( $raw[ $day ]['lunch_start'] ?? '' );
                $lunch_e  = sanitize_text_field( $raw[ $day ]['lunch_end']   ?? '' );
                if ( $lunch_s && $lunch_e ) $segments[] = [ 'start' => $lunch_s, 'end' => $lunch_e ];
                $schedule[ $day ] = $segments;
            }
        }
        update_post_meta( $post_id, '_sb_schedule', $schedule );
    }

    private static function sync_services_for_pro( $pro_id, $assigned_service_ids ) {
        $all = get_posts( [ 'post_type' => 'salon_service', 'posts_per_page' => -1, 'fields' => 'ids' ] );
        foreach ( $all as $svc_id ) {
            $pros = (array) get_post_meta( $svc_id, '_sb_professionals', true );
            if ( in_array( $svc_id, $assigned_service_ids ) ) {
                if ( ! in_array( $pro_id, $pros ) ) $pros[] = $pro_id;
            } else {
                $pros = array_values( array_diff( $pros, [ $pro_id ] ) );
            }
            update_post_meta( $svc_id, '_sb_professionals', $pros );
        }
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

        $cid    = get_post_meta( $post->ID, '_client_name', true );
        $cemail = get_post_meta( $post->ID, '_client_email', true );
        $cphone = get_post_meta( $post->ID, '_client_phone', true );
        $svc    = get_post_meta( $post->ID, '_service', true );
        $pro    = get_post_meta( $post->ID, '_professional', true );
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
                        <tr><td class="sk-bit-label">Professional</td><td class="sk-bit-value"><?php echo esc_html( $pro ?: '—' ); ?></td></tr>
                        <tr><td class="sk-bit-label">Date</td><td class="sk-bit-value"><?php echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '—'; ?></td></tr>
                        <tr><td class="sk-bit-label">Time</td><td class="sk-bit-value"><?php echo $time ? esc_html( date_i18n( get_option( 'time_format' ), strtotime( $time ) ) ) : '—'; ?></td></tr>
                        <tr><td class="sk-bit-label">Price</td><td class="sk-bit-value"><?php echo $price ? '<span class="sk-badge sk-badge-price">$' . esc_html( $price ) . '</span>' : '—'; ?></td></tr>
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
            <input type="hidden" name="sb_professional_name" value="<?php echo esc_attr( $pro ); ?>">
            <input type="hidden" name="sb_booking_date" value="<?php echo esc_attr( $date ); ?>">
            <input type="hidden" name="sb_booking_time" value="<?php echo esc_attr( $time ); ?>">
            <input type="hidden" name="sb_booking_price" value="<?php echo esc_attr( $price ); ?>">
            <input type="hidden" name="sb_db_id" value="<?php echo esc_attr( $db_id ); ?>">
        </div>
        <?php
    }

    public static function render_booking_actions( $post ) {
        $status  = get_post_meta( $post->ID, '_status', true ) ?: 'confirmed';
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

        // Editable fields
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

        // Read-only fields that might be updated via form
        $fields = [
            '_service'        => 'sb_service_name',
            '_professional'   => 'sb_professional_name',
            '_booking_date'   => 'sb_booking_date',
            '_booking_time'   => 'sb_booking_time',
            '_booking_price'  => 'sb_booking_price',
        ];
        foreach ( $fields as $meta_key => $post_key ) {
            if ( isset( $_POST[ $post_key ] ) ) {
                update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
            }
        }

        // Sync status to bookings DB table
        $db_id = absint( $_POST['sb_db_id'] ?? 0 );
        if ( $db_id ) {
            Bookings_DB::update_status( $db_id, $status );
        }
    }
}

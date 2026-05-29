<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Meta_Boxes {

    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_service_boxes' ] );
        add_action( 'add_meta_boxes', [ __CLASS__, 'register_professional_boxes' ] );
        add_action( 'save_post_salon_service',      [ __CLASS__, 'save_service_meta' ], 10, 2 );
        add_action( 'save_post_salon_professional', [ __CLASS__, 'save_professional_meta' ], 10, 2 );
    }

    // ── HELPERS ───────────────────────────────────────────

    private static function field_wrap( $label, $input, $note = '' ) {
        printf(
            '<div class="sk-mb-field"><label class="sk-mb-label">%s</label><div class="sk-mb-input">%s%s</div></div>',
            esc_html( $label ),
            $input,
            $note ? '<p class="sk-mb-note">' . esc_html( $note ) . '</p>' : ''
        );
    }

    private static function checkbox_list( $name, $items, $selected_ids, $empty_msg, $empty_url, $image_cb = null ) {
        if ( empty( $items ) ) {
            printf(
                '<p class="sk-mb-empty">%s <a href="%s">%s</a></p>',
                esc_html( $empty_msg ),
                esc_url( $empty_url ),
                esc_html__( 'Create one', 'salon-kit' )
            );
            return;
        }

        echo '<div class="sk-mb-checkbox-list" data-max-height="260">';
        foreach ( $items as $item ) {
            $id = $item->ID;
            $checked = in_array( $id, $selected_ids, false );
            $img = $image_cb ? $image_cb( $id ) : '';
            printf(
                '<label class="sk-mb-cb-label%s"><input type="checkbox" name="%s[]" value="%s" %s>%s<span>%s</span></label>',
                $checked ? ' sk-mb-cb-label--on' : '',
                esc_attr( $name ),
                esc_attr( $id ),
                checked( $checked, true, false ),
                $img,
                esc_html( $item->post_title )
            );
        }
        echo '</div>';
    }

    // ── SERVICE META BOXES ────────────────────────────────

    public static function register_service_boxes() {
        add_meta_box(
            'sb_service_details',
            '<span class="sk-mb-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16.86 4.49l1.69-1.69a2.25 2.25 0 113.18 3.18L10.58 17.13a4.5 4.5 0 01-1.9 1.13L6 19l.74-2.69a4.5 4.5 0 011.13-1.9L16.86 4.49z"/></svg>Service Details</span>',
            [ __CLASS__, 'render_service_details' ],
            'salon_service',
            'normal',
            'high'
        );
        add_meta_box(
            'sb_service_pros',
            '<span class="sk-mb-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Assign Team</span>',
            [ __CLASS__, 'render_service_pros' ],
            'salon_service',
            'side'
        );
    }

    public static function render_service_details( $post ) {
        wp_nonce_field( 'sb_save_service', 'sb_service_nonce' );
        $price    = get_post_meta( $post->ID, '_sb_price', true );
        $duration = get_post_meta( $post->ID, '_sb_duration', true );
        $slot_qty = get_post_meta( $post->ID, '_sb_slot_qty', true ) ?: 1;
        ?>
        <div class="sk-mb sk-mb-fields">
            <?php self::field_wrap(
                'Price ($)',
                '<input type="number" step="0.01" min="0" id="sb_price" name="sb_price" value="' . esc_attr( $price ) . '" class="sk-mb-input--sm" placeholder="35.00" required>',
                'Set the service price in USD.'
            ); ?>
            <?php self::field_wrap(
                'Duration (minutes)',
                '<input type="number" min="5" step="5" id="sb_duration" name="sb_duration" value="' . esc_attr( $duration ) . '" class="sk-mb-input--sm" placeholder="45" required>',
                'How long does this service take?'
            ); ?>
            <?php self::field_wrap(
                'Slot Quantity',
                '<input type="number" min="1" id="sb_slot_qty" name="sb_slot_qty" value="' . esc_attr( $slot_qty ) . '" class="sk-mb-input--xs" required>',
                'Max clients per time slot.'
            ); ?>
        </div>
        <?php
    }

    public static function render_service_pros( $post ) {
        $assigned = (array) get_post_meta( $post->ID, '_sb_professionals', true );
        $pros     = get_posts( [
            'post_type'      => 'salon_professional',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        self::checkbox_list(
            'sb_professionals',
            $pros,
            $assigned,
            'No professionals yet.',
            admin_url( 'post-new.php?post_type=salon_professional' ),
            function ( $id ) {
                $img = get_the_post_thumbnail( $id, [ 20, 20 ], [ 'class' => 'sk-mb-avatar' ] );
                return $img ?: '<span class="sk-mb-avatar sk-mb-avatar--empty">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
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
        $all_pros = get_posts( [
            'post_type'      => 'salon_professional',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ] );
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
        add_meta_box(
            'sb_pro_services',
            '<span class="sk-mb-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6 6h12M6 12h12M6 18h8"/></svg>Assigned Services</span>',
            [ __CLASS__, 'render_pro_services' ],
            'salon_professional',
            'side'
        );
        add_meta_box(
            'sb_pro_schedule',
            '<span class="sk-mb-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Weekly Schedule</span>',
            [ __CLASS__, 'render_pro_schedule' ],
            'salon_professional',
            'normal',
            'high'
        );
    }

    public static function render_pro_services( $post ) {
        wp_nonce_field( 'sb_save_professional', 'sb_pro_nonce' );
        $assigned = (array) get_post_meta( $post->ID, '_sb_assigned_services', true );
        $services = get_posts( [
            'post_type'      => 'salon_service',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        self::checkbox_list(
            'sb_services',
            $services,
            $assigned,
            'No services yet.',
            admin_url( 'post-new.php?post_type=salon_service' )
        );
    }

    public static function render_pro_schedule( $post ) {
        $schedule = (array) get_post_meta( $post->ID, '_sb_schedule', true );
        $days     = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ];
        $labels   = [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ];
        ?>
        <div class="sk-mb sk-mb-schedule">
            <?php foreach ( $days as $i => $day ) :
                $segments = $schedule[ $day ] ?? [];
                $active   = ! empty( $segments );
                $start    = $segments[0]['start'] ?? '09:00';
                $end      = $segments[0]['end']   ?? '17:00';
                $lunch_s  = $segments[1]['start'] ?? '';
                $lunch_e  = $segments[1]['end']   ?? '';
                $abbr     = substr( $day, 0, 3 );
            ?>
                <div class="sk-mb-sched-row<?php echo $active ? ' sk-mb-sched-row--on' : ''; ?>">
                    <div class="sk-mb-sched-day">
                        <label class="sk-toggle" title="Toggle <?php echo esc_attr( $labels[ $i ] ); ?>">
                            <input type="checkbox" name="sb_schedule[<?php echo esc_attr( $day ); ?>][active]" value="1" <?php checked( $active ); ?>>
                            <span class="sk-toggle-track"><span class="sk-toggle-knob"></span></span>
                            <span class="sk-toggle-label"><?php echo esc_html( $abbr ); ?></span>
                        </label>
                    </div>
                    <div class="sk-mb-sched-times">
                        <label class="sk-mb-sched-time">
                            <span>Start</span>
                            <input type="time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][start]" value="<?php echo esc_attr( $start ); ?>">
                        </label>
                        <label class="sk-mb-sched-time">
                            <span>End</span>
                            <input type="time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][end]" value="<?php echo esc_attr( $end ); ?>">
                        </label>
                        <label class="sk-mb-sched-time sk-mb-sched-time--lunch">
                            <span>Lunch start</span>
                            <input type="time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][lunch_start]" value="<?php echo esc_attr( $lunch_s ); ?>">
                        </label>
                        <label class="sk-mb-sched-time sk-mb-sched-time--lunch">
                            <span>Lunch end</span>
                            <input type="time" name="sb_schedule[<?php echo esc_attr( $day ); ?>][lunch_end]" value="<?php echo esc_attr( $lunch_e ); ?>">
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
            <p class="sk-mb-note">Toggle a day on to set working hours. Leave lunch blank for no break.</p>
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
                $start   = sanitize_text_field( $raw[ $day ]['start'] ?? '09:00' );
                $end     = sanitize_text_field( $raw[ $day ]['end']   ?? '17:00' );
                $segments = [ [ 'start' => $start, 'end' => $end ] ];
                $lunch_s = sanitize_text_field( $raw[ $day ]['lunch_start'] ?? '' );
                $lunch_e = sanitize_text_field( $raw[ $day ]['lunch_end']   ?? '' );
                if ( $lunch_s && $lunch_e ) $segments[] = [ 'start' => $lunch_s, 'end' => $lunch_e ];
                $schedule[ $day ] = $segments;
            }
        }
        update_post_meta( $post_id, '_sb_schedule', $schedule );
    }

    private static function sync_services_for_pro( $pro_id, $assigned_service_ids ) {
        $all = get_posts( [
            'post_type'      => 'salon_service',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ] );
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
}

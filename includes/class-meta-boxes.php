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

    // ── SERVICE ───────────────────────────────────────────

    public static function register_service_boxes() {
        add_meta_box( 'sb_service_details', 'Service Details',
            [ __CLASS__, 'render_service_details' ], 'salon_service', 'normal', 'high' );
        add_meta_box( 'sb_service_pros', 'Assign Professionals',
            [ __CLASS__, 'render_service_pros' ], 'salon_service', 'side' );
    }

    public static function render_service_details( $post ) {
        wp_nonce_field( 'sb_save_service', 'sb_service_nonce' );
        $price    = get_post_meta( $post->ID, '_sb_price', true );
        $duration = get_post_meta( $post->ID, '_sb_duration', true );
        $slot_qty = get_post_meta( $post->ID, '_sb_slot_qty', true ) ?: 1;
        ?>
        <table class="form-table">
            <tr>
                <th><label for="sb_price">Price ($)</label></th>
                <td><input type="number" step="0.01" min="0" id="sb_price" name="sb_price"
                    value="<?php echo esc_attr( $price ); ?>" class="regular-text" placeholder="35.00" required></td>
            </tr>
            <tr>
                <th><label for="sb_duration">Duration (minutes)</label></th>
                <td><input type="number" min="5" step="5" id="sb_duration" name="sb_duration"
                    value="<?php echo esc_attr( $duration ); ?>" class="regular-text" placeholder="45" required></td>
            </tr>
            <tr>
                <th><label for="sb_slot_qty">Slot Quantity</label></th>
                <td>
                    <input type="number" min="1" id="sb_slot_qty" name="sb_slot_qty"
                        value="<?php echo esc_attr( $slot_qty ); ?>" class="small-text" required>
                    <p class="description">Max clients per time slot for this service.</p>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function render_service_pros( $post ) {
        $assigned_pros = (array) get_post_meta( $post->ID, '_sb_professionals', true );
        $pros = get_posts( [ 'post_type' => 'salon_professional', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );

        if ( empty( $pros ) ) {
            echo '<p>No professionals yet. <a href="' . admin_url( 'post-new.php?post_type=salon_professional' ) . '">Create one</a>.</p>';
            return;
        }

        echo '<ul style="margin:0;max-height:260px;overflow-y:auto;">';
        foreach ( $pros as $pro ) {
            $img = get_the_post_thumbnail( $pro->ID, [ 24, 24 ], [ 'style' => 'border-radius:50%;vertical-align:middle;margin-right:6px;' ] ) ?: '';
            echo '<li style="padding:4px 0;"><label>';
            echo '<input type="checkbox" name="sb_professionals[]" value="' . esc_attr( $pro->ID ) . '" '
                . ( in_array( $pro->ID, $assigned_pros ) ? 'checked' : '' ) . '> '
                . $img . esc_html( $pro->post_title );
            echo '</label></li>';
        }
        echo '</ul>';
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

    // ── PROFESSIONAL ──────────────────────────────────────

    public static function register_professional_boxes() {
        add_meta_box( 'sb_pro_services', 'Assigned Services',
            [ __CLASS__, 'render_pro_services' ], 'salon_professional', 'side' );
        add_meta_box( 'sb_pro_schedule', 'Weekly Schedule',
            [ __CLASS__, 'render_pro_schedule' ], 'salon_professional', 'normal', 'high' );
    }

    public static function render_pro_services( $post ) {
        wp_nonce_field( 'sb_save_professional', 'sb_pro_nonce' );
        $assigned = (array) get_post_meta( $post->ID, '_sb_assigned_services', true );
        $services = get_posts( [ 'post_type' => 'salon_service', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ] );

        if ( empty( $services ) ) {
            echo '<p>No services yet. <a href="' . admin_url( 'post-new.php?post_type=salon_service' ) . '">Create one</a>.</p>';
            return;
        }

        echo '<ul style="margin:0;max-height:260px;overflow-y:auto;">';
        foreach ( $services as $svc ) {
            echo '<li style="padding:4px 0;"><label>';
            echo '<input type="checkbox" name="sb_services[]" value="' . esc_attr( $svc->ID ) . '" '
                . ( in_array( $svc->ID, $assigned ) ? 'checked' : '' ) . '> '
                . esc_html( $svc->post_title );
            echo '</label></li>';
        }
        echo '</ul>';
    }

    public static function render_pro_schedule( $post ) {
        $schedule = (array) get_post_meta( $post->ID, '_sb_schedule', true );
        $days = [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ];
        $labels = [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' ];
        ?>
        <table class="form-table sb-schedule-table">
            <thead><tr>
                <th style="width:100px;">Day</th><th style="width:60px;">Works?</th>
                <th>Start</th><th>End</th><th>Lunch Start</th><th>Lunch End</th>
            </tr></thead>
            <tbody>
            <?php foreach ( $days as $i => $day ) :
                $segments = $schedule[ $day ] ?? [];
                $active   = ! empty( $segments );
                $start    = $segments[0]['start'] ?? '09:00';
                $end      = $segments[0]['end']   ?? '17:00';
                $lunch_s  = $segments[1]['start'] ?? '';
                $lunch_e  = $segments[1]['end']   ?? '';
                ?>
                <tr>
                    <td><strong><?php echo $labels[ $i ]; ?></strong></td>
                    <td><input type="checkbox" name="sb_schedule[<?php echo $day; ?>][active]" value="1" <?php checked( $active ); ?>></td>
                    <td><input type="time" name="sb_schedule[<?php echo $day; ?>][start]" value="<?php echo esc_attr( $start ); ?>"></td>
                    <td><input type="time" name="sb_schedule[<?php echo $day; ?>][end]" value="<?php echo esc_attr( $end ); ?>"></td>
                    <td><input type="time" name="sb_schedule[<?php echo $day; ?>][lunch_start]" value="<?php echo esc_attr( $lunch_s ); ?>"></td>
                    <td><input type="time" name="sb_schedule[<?php echo $day; ?>][lunch_end]" value="<?php echo esc_attr( $lunch_e ); ?>"></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p class="description">Check a day to mark as working. Leave lunch blank for no break.</p>
        <style>.sb-schedule-table input[type="time"]{width:110px}.sb-schedule-table td{vertical-align:middle;padding:6px 4px}</style>
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
}

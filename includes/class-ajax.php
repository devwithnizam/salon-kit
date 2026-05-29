<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Ajax {

    public static function init() {
        $actions = [ 'salon_get_services', 'salon_get_slots', 'salon_save_booking' ];
        foreach ( $actions as $a ) {
            add_action( "wp_ajax_{$a}",        [ __CLASS__, $a ] );
            add_action( "wp_ajax_nopriv_{$a}", [ __CLASS__, $a ] );
        }
    }

    public static function salon_get_services() {
        $posts = get_posts( [ 'post_type' => 'salon_service', 'posts_per_page' => -1, 'orderby' => [ 'menu_order' => 'ASC', 'title' => 'ASC' ] ] );
        $data  = [];
        foreach ( $posts as $p ) {
            $thumb_id  = get_post_thumbnail_id( $p->ID );
            $data[] = [
                'id'          => $p->ID,
                'name'        => $p->post_title,
                'description' => get_the_excerpt( $p ),
                'price'       => get_post_meta( $p->ID, '_sb_price', true ),
                'duration'    => (int) get_post_meta( $p->ID, '_sb_duration', true ),
                'slot_qty'    => max( 1, (int) get_post_meta( $p->ID, '_sb_slot_qty', true ) ?: 1 ),
                'thumb_url'   => $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '',
            ];
        }
        wp_send_json_success( $data );
    }

    public static function salon_get_slots() {
        $service_id = isset( $_POST['service_id'] ) ? absint( $_POST['service_id'] ) : 0;
        $date       = isset( $_POST['date'] ) ? sanitize_text_field( $_POST['date'] ) : '';

        if ( ! $service_id || ! $date ) {
            wp_send_json_error( [ 'message' => 'Missing parameters.' ] );
        }
        if ( strtotime( $date ) < strtotime( date( 'Y-m-d' ) ) ) {
            wp_send_json_error( [ 'message' => 'Past date.' ] );
        }

        $slots = Slot_Engine::get_available_slots( $service_id, $date );
        wp_send_json_success( $slots );
    }

    public static function salon_save_booking() {
        check_ajax_referer( 'salon_booking_nonce', 'nonce' );

        $service_id = isset( $_POST['service_id'] ) ? absint( $_POST['service_id'] ) : 0;
        $date       = sanitize_text_field( $_POST['booking_date'] ?? '' );
        $time       = sanitize_text_field( $_POST['booking_time'] ?? '' );
        $name       = sanitize_text_field( $_POST['client_name']  ?? '' );
        $email      = sanitize_email( $_POST['client_email'] ?? '' );
        $phone      = sanitize_text_field( $_POST['client_phone'] ?? '' );
        $notes      = sanitize_textarea_field( $_POST['notes'] ?? '' );

        if ( ! $service_id || ! $date || ! $time || ! $name || ! $email ) {
            wp_send_json_error( [ 'message' => 'All required fields must be filled.' ] );
        }
        if ( ! is_email( $email ) ) {
            wp_send_json_error( [ 'message' => 'Invalid email address.' ] );
        }

        global $wpdb;
        $table = $wpdb->prefix . Bookings_DB::TABLE;

        $wpdb->query( 'START TRANSACTION' );

        $slot_qty = max( 1, (int) get_post_meta( $service_id, '_sb_slot_qty', true ) ?: 1 );
        $booked = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table
             WHERE service_id = %d AND booking_date = %s AND booking_time = %s AND status = 'confirmed'
             FOR UPDATE",
            $service_id, $date, $time
        ) );

        if ( $booked >= $slot_qty ) {
            $wpdb->query( 'ROLLBACK' );
            wp_send_json_error( [ 'message' => 'This slot was just taken. Please choose another.' ] );
        }

        $wpdb->insert( $table, [
            'service_id'   => $service_id,
            'booking_date' => $date,
            'booking_time' => $time,
            'client_name'  => $name,
            'client_email' => $email,
            'client_phone' => $phone,
            'notes'        => $notes,
            'status'       => 'confirmed',
        ], [ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ] );

        $booking_id = $wpdb->insert_id;
        $wpdb->query( 'COMMIT' );

        // Invalidate slot cache
        delete_transient( "sk_slots_{$service_id}_{$date}" );

        // Create admin record
        $svc_name = get_the_title( $service_id );
        $booking_id_display = '#BK-' . str_pad( $booking_id, 4, '0', STR_PAD_LEFT );
        $post_id  = wp_insert_post( [
            'post_title'  => "{$booking_id_display} – {$name} – {$date}",
            'post_type'   => 'salon_booking',
            'post_status' => 'publish',
        ] );
        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_client_name',    $name );
            update_post_meta( $post_id, '_client_email',   $email );
            update_post_meta( $post_id, '_client_phone',   $phone );
            update_post_meta( $post_id, '_service',        $svc_name );
            update_post_meta( $post_id, '_service_id',     $service_id );
            update_post_meta( $post_id, '_booking_date',   $date );
            update_post_meta( $post_id, '_booking_time',   $time );
            update_post_meta( $post_id, '_booking_price',  get_post_meta( $service_id, '_sb_price', true ) );
            update_post_meta( $post_id, '_booking_notes',  $notes );
            update_post_meta( $post_id, '_status',         'confirmed' );
            update_post_meta( $post_id, '_submitted_at',   current_time( 'mysql' ) );
            update_post_meta( $post_id, '_booking_db_id',  $booking_id );
        }

        // Send email notifications
        Email::send_booking_confirmation( $booking_id );

        wp_send_json_success( [ 'message' => 'Booking confirmed!', 'booking_id' => $booking_id, 'booking_id_display' => $booking_id_display ] );
    }
}

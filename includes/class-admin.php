<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Admin {

    public static function init() {
        add_filter( 'manage_salon_service_posts_columns',       [ __CLASS__, 'service_columns' ] );
        add_action( 'manage_salon_service_posts_custom_column', [ __CLASS__, 'service_values' ], 10, 2 );

        add_filter( 'manage_salon_professional_posts_columns',       [ __CLASS__, 'professional_columns' ] );
        add_action( 'manage_salon_professional_posts_custom_column', [ __CLASS__, 'professional_values' ], 10, 2 );

        add_filter( 'manage_salon_booking_posts_columns',       [ __CLASS__, 'booking_columns' ] );
        add_action( 'manage_salon_booking_posts_custom_column', [ __CLASS__, 'booking_values' ], 10, 2 );

        add_action( 'wp_dashboard_setup', [ __CLASS__, 'dashboard_widget' ] );
        add_action( 'admin_head', [ __CLASS__, 'admin_styles' ] );
    }

    public static function service_columns( $cols ) {
        return [
            'cb'       => '<input type="checkbox">',
            'title'    => 'Service',
            'sb_thumb' => 'Image',
            'sb_price' => 'Price',
            'sb_dur'   => 'Duration',
            'sb_slots' => 'Slot Qty',
            'sb_pros'  => 'Professionals',
            'date'     => 'Date',
        ];
    }

    public static function service_values( $col, $post_id ) {
        switch ( $col ) {
            case 'sb_thumb':
                echo get_the_post_thumbnail( $post_id, [ 40, 40 ], [ 'style' => 'border-radius:4px;object-fit:cover;' ] ) ?: '<span style="color:#999">—</span>';
                break;
            case 'sb_price':
                $p = get_post_meta( $post_id, '_sb_price', true );
                echo $p ? '$' . esc_html( $p ) : '—';
                break;
            case 'sb_dur':
                $d = (int) get_post_meta( $post_id, '_sb_duration', true );
                echo $d ? esc_html( $d ) . ' min' : '—';
                break;
            case 'sb_slots':
                echo (int) get_post_meta( $post_id, '_sb_slot_qty', true ) ?: 1;
                break;
            case 'sb_pros':
                $pros = (array) get_post_meta( $post_id, '_sb_professionals', true );
                if ( ! empty( $pros ) ) {
                    $names = array_map( function( $pid ) { return get_the_title( $pid ) ?: "(ID $pid)"; }, $pros );
                    echo esc_html( implode( ', ', $names ) );
                } else {
                    echo '<span style="color:#999">None</span>';
                }
                break;
        }
    }

    public static function professional_columns( $cols ) {
        return [
            'cb'          => '<input type="checkbox">',
            'title'       => 'Name',
            'sb_photo'    => 'Photo',
            'sb_services' => 'Services',
            'sb_sched'    => 'Schedule',
            'date'        => 'Date',
        ];
    }

    public static function professional_values( $col, $post_id ) {
        switch ( $col ) {
            case 'sb_photo':
                echo get_the_post_thumbnail( $post_id, [ 40, 40 ], [ 'style' => 'border-radius:50%;object-fit:cover;' ] ) ?: '<span style="color:#999">—</span>';
                break;
            case 'sb_services':
                $svcs = (array) get_post_meta( $post_id, '_sb_assigned_services', true );
                if ( ! empty( $svcs ) ) {
                    $names = array_map( function( $sid ) { return get_the_title( $sid ) ?: "(ID $sid)"; }, $svcs );
                    echo esc_html( implode( ', ', $names ) );
                } else {
                    echo '<span style="color:#999">None</span>';
                }
                break;
            case 'sb_sched':
                $sched = (array) get_post_meta( $post_id, '_sb_schedule', true );
                $days  = array_keys( $sched );
                if ( ! empty( $days ) ) {
                    $abbr = array_map( function( $d ) { return strtoupper( substr( $d, 0, 2 ) ); }, $days );
                    echo esc_html( implode( ' ', $abbr ) );
                } else {
                    echo '<span style="color:#999">Not set</span>';
                }
                break;
        }
    }

    public static function booking_columns( $cols ) {
        return [
            'cb'            => $cols['cb'],
            'title'         => 'Booking #',
            'client_name'   => 'Client',
            'client_email'  => 'Email',
            'service'       => 'Service',
            'professional'  => 'Professional',
            'booking_date'  => 'Date',
            'booking_time'  => 'Time',
            'booking_price' => 'Price',
            'status'        => 'Status',
        ];
    }

    public static function booking_values( $col, $post_id ) {
        $map = [
            'client_name'   => 'client_name',
            'client_email'  => 'client_email',
            'service'       => 'service',
            'professional'  => 'professional',
            'booking_date'  => 'booking_date',
            'booking_time'  => 'booking_time',
            'booking_price' => 'booking_price',
            'status'        => 'status',
        ];
        if ( isset( $map[ $col ] ) ) {
            echo esc_html( get_post_meta( $post_id, '_' . $map[ $col ], true ) );
        }
    }

    public static function dashboard_widget() {
        wp_add_dashboard_widget(
            'sk_dashboard_today',
            'Today\'s Bookings — SalonKit',
            [ __CLASS__, 'render_dashboard' ]
        );
    }

    public static function render_dashboard() {
        global $wpdb;
        $table = $wpdb->prefix . Bookings_DB::TABLE;
        $today = date( 'Y-m-d' );

        $bookings = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table WHERE booking_date = %s AND status = 'confirmed' ORDER BY booking_time ASC", $today
        ) );

        if ( empty( $bookings ) ) {
            echo '<p>No bookings today.</p>';
            return;
        }

        echo '<table style="width:100%;border-collapse:collapse;">';
        echo '<thead><tr style="text-align:left;border-bottom:1px solid #eee;">';
        echo '<th style="padding:4px 6px;">Time</th><th style="padding:4px 6px;">Client</th><th style="padding:4px 6px;">Service</th><th style="padding:4px 6px;">Pro</th>';
        echo '</tr></thead><tbody>';
        foreach ( $bookings as $b ) {
            $svc_name = get_the_title( $b->service_id ) ?: "Service #{$b->service_id}";
            $pro_name = get_the_title( $b->professional_id ) ?: "Pro #{$b->professional_id}";
            echo '<tr style="border-bottom:1px solid #f5f5f5;">';
            echo '<td style="padding:5px 6px;">' . date( 'g:i A', strtotime( $b->booking_time ) ) . '</td>';
            echo '<td style="padding:5px 6px;">' . esc_html( $b->client_name ) . '</td>';
            echo '<td style="padding:5px 6px;">' . esc_html( $svc_name ) . '</td>';
            echo '<td style="padding:5px 6px;">' . esc_html( $pro_name ) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '<p style="margin-top:8px;"><a href="' . admin_url( 'edit.php?post_type=salon_booking' ) . '">View all bookings →</a></p>';
    }

    public static function admin_styles() {
        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->post_type, [ 'salon_service', 'salon_professional', 'salon_booking' ] ) ) return;
        echo '<style>.fixed .column-sb_thumb,.fixed .column-sb_photo{width:50px}.fixed .column-sb_price,.fixed .column-sb_dur,.fixed .column-sb_slots{width:80px}</style>';
    }
}

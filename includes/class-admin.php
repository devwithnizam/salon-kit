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
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_assets' ] );
        add_action( 'admin_head', [ __CLASS__, 'admin_inline_styles' ] );
    }

    // ── ASSETS ────────────────────────────────────────────

    public static function admin_assets( $hook ) {
        $screen = get_current_screen();
        if ( ! $screen ) return;
        $types = [ 'salon_service', 'salon_professional', 'salon_booking' ];
        if ( ! in_array( $screen->post_type, $types ) && $hook !== 'index.php' ) return;

        wp_enqueue_style(
            'salon-kit-admin',
            SK_URL . 'assets/css/salon-kit-admin.css',
            [],
            SK_VERSION
        );
    }

    public static function admin_inline_styles() {
        $screen = get_current_screen();
        if ( ! $screen ) return;

        // Column widths for list tables
        if ( in_array( $screen->post_type, [ 'salon_service', 'salon_professional', 'salon_booking' ] ) ) : ?>
        <style>
            .fixed .column-sb_thumb,
            .fixed .column-sb_photo { width: 44px; }
            .fixed .column-sb_price,
            .fixed .column-sb_dur,
            .fixed .column-sb_slots { width: 70px; }
            .fixed .column-sb_sched { width: 100px; }
            .fixed .column-booking_date,
            .fixed .column-booking_time,
            .fixed .column-booking_price { width: 90px; }
            .fixed .column-status { width: 80px; }
        </style>
        <?php endif;

        // Postbox polish for meta box screens
        if ( in_array( $screen->post_type, [ 'salon_service', 'salon_professional' ] ) ) : ?>
        <style>
            #sb_service_details .inside,
            #sb_pro_schedule .inside { padding: 12px 14px; }
            #sb_service_pros .inside,
            #sb_pro_services .inside { padding: 10px 12px; }
            #sb_service_details .postbox-header,
            #sb_service_pros .postbox-header,
            #sb_pro_services .postbox-header,
            #sb_pro_schedule .postbox-header { border-bottom: 1px solid #e5e7eb; }
            .post-type-salon_service .handle-actions,
            .post-type-salon_professional .handle-actions { display: flex; align-items: center; }
        </style>
        <?php endif;
    }

    // ── SERVICES TABLE ────────────────────────────────────

    public static function service_columns( $cols ) {
        return [
            'cb'       => '<input type="checkbox">',
            'title'    => 'Service',
            'sb_thumb' => '',
            'sb_price' => 'Price',
            'sb_dur'   => 'Duration',
            'sb_slots' => 'Capacity',
            'sb_pros'  => 'Team',
            'date'     => 'Date',
        ];
    }

    public static function service_values( $col, $post_id ) {
        switch ( $col ) {
            case 'sb_thumb':
                $img = get_the_post_thumbnail( $post_id, [ 36, 36 ], [ 'class' => 'sk-admin-thumb' ] );
                echo $img ?: '<span class="sk-admin-thumb sk-admin-thumb--empty">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M16.86 4.49l1.69-1.69a2.25 2.25 0 113.18 3.18L10.58 17.13a4.5 4.5 0 01-1.9 1.13L6 19l.74-2.69a4.5 4.5 0 011.13-1.9L16.86 4.49z"/></svg>
                </span>';
                break;

            case 'sb_price':
                $p = get_post_meta( $post_id, '_sb_price', true );
                echo $p ? '<span class="sk-badge sk-badge-price">$' . esc_html( $p ) . '</span>' : '<span class="sk-na">—</span>';
                break;

            case 'sb_dur':
                $d = (int) get_post_meta( $post_id, '_sb_duration', true );
                echo $d ? '<span class="sk-badge sk-badge-dur">' . esc_html( $d ) . ' <span class="sk-unit">min</span></span>' : '<span class="sk-na">—</span>';
                break;

            case 'sb_slots':
                $qty = (int) get_post_meta( $post_id, '_sb_slot_qty', true ) ?: 1;
                echo '<span class="sk-badge sk-badge-slot">' . esc_html( $qty ) . ' <span class="sk-unit">slot' . ( $qty > 1 ? 's' : '' ) . '</span></span>';
                break;

            case 'sb_pros':
                $pros = (array) get_post_meta( $post_id, '_sb_professionals', true );
                if ( ! empty( $pros ) ) {
                    $count = count( $pros );
                    $first = get_the_title( $pros[0] ) ?: "(ID {$pros[0]})";
                    echo '<span class="sk-team-count">' . esc_html( $count ) . '</span>';
                    echo '<span class="sk-team-list">' . esc_html( $first );
                    if ( $count > 1 ) echo ' +' . ( $count - 1 );
                    echo '</span>';
                } else {
                    echo '<span class="sk-na">None</span>';
                }
                break;
        }
    }

    // ── PROFESSIONALS TABLE ───────────────────────────────

    public static function professional_columns( $cols ) {
        return [
            'cb'          => '<input type="checkbox">',
            'title'       => 'Name',
            'sb_photo'    => '',
            'sb_services' => 'Services',
            'sb_sched'    => 'Schedule',
            'date'        => 'Date',
        ];
    }

    public static function professional_values( $col, $post_id ) {
        switch ( $col ) {
            case 'sb_photo':
                $img = get_the_post_thumbnail( $post_id, [ 36, 36 ], [ 'class' => 'sk-admin-avatar' ] );
                echo $img ?: '<span class="sk-admin-avatar sk-admin-avatar--empty">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>';
                break;

            case 'sb_services':
                $svcs = (array) get_post_meta( $post_id, '_sb_assigned_services', true );
                if ( ! empty( $svcs ) ) {
                    $count = count( $svcs );
                    $names = array_map( function( $sid ) { return get_the_title( $sid ) ?: "(ID $sid)"; }, $svcs );
                    echo '<span class="sk-badge sk-badge-svc">' . esc_html( $count ) . '</span>';
                    echo '<span class="sk-team-list">' . esc_html( implode( ', ', array_slice( $names, 0, 2 ) ) );
                    if ( $count > 2 ) echo ' +' . ( $count - 2 );
                    echo '</span>';
                } else {
                    echo '<span class="sk-na">None</span>';
                }
                break;

            case 'sb_sched':
                $sched = (array) get_post_meta( $post_id, '_sb_schedule', true );
                $days  = array_keys( $sched );
                if ( ! empty( $days ) ) {
                    $all = [ 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun' ];
                    echo '<span class="sk-day-dots">';
                    foreach ( $all as $abbr ) {
                        $full = [ 'mon' => 'monday', 'tue' => 'tuesday', 'wed' => 'wednesday', 'thu' => 'thursday', 'fri' => 'friday', 'sat' => 'saturday', 'sun' => 'sunday' ];
                        $active = in_array( $full[ $abbr ], $days );
                        echo '<span class="sk-day-dot' . ( $active ? ' sk-day-dot--on' : '' ) . '" title="' . ucfirst( $abbr ) . '">' . substr( $abbr, 0, 1 ) . '</span>';
                    }
                    echo '</span>';
                } else {
                    echo '<span class="sk-na">Not set</span>';
                }
                break;
        }
    }

    // ── BOOKINGS TABLE ────────────────────────────────────

    public static function booking_columns( $cols ) {
        return [
            'cb'            => $cols['cb'],
            'title'         => 'Booking',
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
            'client_name'   => '_client_name',
            'client_email'  => '_client_email',
            'service'       => '_service',
            'professional'  => '_professional',
            'booking_date'  => '_booking_date',
            'booking_time'  => '_booking_time',
            'booking_price' => '_booking_price',
            'status'        => '_status',
        ];

        if ( $col === 'title' ) {
            echo '<span class="sk-booking-id">#' . esc_html( $post_id ) . '</span>';
            return;
        }

        if ( isset( $map[ $col ] ) ) {
            $val = get_post_meta( $post_id, $map[ $col ], true );
            if ( $col === 'status' ) {
                $is_confirmed = $val === 'confirmed';
                echo '<span class="sk-status-dot ' . ( $is_confirmed ? 'sk-status-dot--confirmed' : 'sk-status-dot--cancelled' ) . '"></span>';
                echo '<span class="sk-status-text">' . esc_html( $val ?: 'pending' ) . '</span>';
            } elseif ( $col === 'booking_date' ) {
                echo $val ? '<span class="sk-date">' . esc_html( date_i18n( 'M j, Y', strtotime( $val ) ) ) . '</span>' : '—';
            } elseif ( $col === 'booking_time' ) {
                echo $val ? '<span class="sk-time">' . esc_html( date( 'g:i A', strtotime( $val ) ) ) . '</span>' : '—';
            } elseif ( $col === 'booking_price' ) {
                echo $val ? '<span class="sk-badge sk-badge-price">$' . esc_html( $val ) . '</span>' : '—';
            } elseif ( $col === 'client_email' ) {
                echo $val ? '<a href="mailto:' . esc_attr( $val ) . '" class="sk-email-link">' . esc_html( $val ) . '</a>' : '—';
            } else {
                echo esc_html( $val ?: '—' );
            }
        }
    }

    // ── DASHBOARD WIDGET ──────────────────────────────────

    public static function dashboard_widget() {
        wp_add_dashboard_widget(
            'sk_dashboard_today',
            '<span class="sk-dash-header">'
                . CPT::brand_icon_svg( 18, '#6366f1' )
                . '<span class="sk-dash-header-title">Today\'s Bookings</span>
            </span>',
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

        $total = count( $bookings );

        echo '<div class="sk-dash">';

        // Summary stat
        echo '<div class="sk-dash-stat">';
        echo '<span class="sk-dash-stat-num">' . esc_html( $total ) . '</span>';
        echo '<span class="sk-dash-stat-label">appointment' . ( $total !== 1 ? 's' : '' ) . ' today</span>';
        echo '</div>';

        if ( empty( $bookings ) ) {
            echo '<div class="sk-dash-empty">
                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/></svg>
                <p>No bookings today — time to promote!</p>
            </div>';
        } else {
            echo '<div class="sk-dash-list">';
            foreach ( $bookings as $b ) {
                $svc_name = get_the_title( $b->service_id ) ?: "Service #{$b->service_id}";
                $pro_name = get_the_title( $b->professional_id ) ?: "Pro #{$b->professional_id}";
                echo '<div class="sk-dash-row">';
                echo '<div class="sk-dash-time">' . esc_html( date( 'g:i A', strtotime( $b->booking_time ) ) ) . '</div>';
                echo '<div class="sk-dash-info">';
                echo '<strong class="sk-dash-client">' . esc_html( $b->client_name ) . '</strong>';
                echo '<span class="sk-dash-meta">' . esc_html( $svc_name ) . ' &middot; ' . esc_html( $pro_name ) . '</span>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }

        echo '<div class="sk-dash-footer"><a href="' . admin_url( 'edit.php?post_type=salon_booking' ) . '">View all bookings &rarr;</a></div>';
        echo '</div>';
    }
}

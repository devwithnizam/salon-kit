<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Bookings_DB {

    const TABLE = 'salon_bookings';

    public static function init() {
        add_action( 'admin_init', [ __CLASS__, 'maybe_create_table' ] );
    }

    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE;
        $charset    = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            service_id      BIGINT UNSIGNED NOT NULL,
            professional_id BIGINT UNSIGNED NOT NULL,
            booking_date    DATE NOT NULL,
            booking_time    TIME NOT NULL,
            client_name     VARCHAR(100) NOT NULL,
            client_email    VARCHAR(100) NOT NULL,
            client_phone    VARCHAR(30) DEFAULT '',
            notes           TEXT DEFAULT '',
            status          VARCHAR(20) DEFAULT 'confirmed',
            created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_slot (professional_id, booking_date, booking_time, status),
            INDEX idx_date (booking_date),
            INDEX idx_pro (professional_id),
            INDEX idx_service (service_id)
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    public static function maybe_create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) !== $table_name ) {
            self::create_table();
        }
    }

    public static function insert( $data ) {
        global $wpdb;
        $defaults = [
            'service_id'      => 0,
            'professional_id' => 0,
            'booking_date'    => '',
            'booking_time'    => '',
            'client_name'     => '',
            'client_email'    => '',
            'client_phone'    => '',
            'notes'           => '',
            'status'          => 'confirmed',
        ];
        $data = wp_parse_args( $data, $defaults );

        $result = $wpdb->insert(
            $wpdb->prefix . self::TABLE,
            [
                'service_id'      => absint( $data['service_id'] ),
                'professional_id' => absint( $data['professional_id'] ),
                'booking_date'    => sanitize_text_field( $data['booking_date'] ),
                'booking_time'    => sanitize_text_field( $data['booking_time'] ),
                'client_name'     => sanitize_text_field( $data['client_name'] ),
                'client_email'    => sanitize_email( $data['client_email'] ),
                'client_phone'    => sanitize_text_field( $data['client_phone'] ),
                'notes'           => sanitize_textarea_field( $data['notes'] ),
                'status'          => in_array( $data['status'], [ 'confirmed', 'cancelled', 'pending' ] ) ? $data['status'] : 'confirmed',
            ],
            [ '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
        );

        return $result ? $wpdb->insert_id : false;
    }

    public static function count_for_slot( $professional_id, $date, $time ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;
        return (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table
             WHERE professional_id = %d AND booking_date = %s AND booking_time = %s AND status = 'confirmed'",
            $professional_id, $date, $time
        ) );
    }

    public static function get_counts_for_date( $professional_id, $date ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT booking_time, COUNT(*) AS booked
             FROM $table
             WHERE professional_id = %d AND booking_date = %s AND status = 'confirmed'
             GROUP BY booking_time",
            $professional_id, $date
        ) );

        $counts = [];
        foreach ( $results as $row ) {
            $counts[ $row->booking_time ] = (int) $row->booked;
        }
        return $counts;
    }

    public static function get_bookings( $args = [] ) {
        global $wpdb;
        $table = $wpdb->prefix . self::TABLE;

        $defaults = [
            'professional_id' => 0,
            'service_id'      => 0,
            'date_from'       => '',
            'date_to'         => '',
            'status'          => '',
            'orderby'         => 'booking_date',
            'order'           => 'DESC',
            'limit'           => 50,
            'offset'          => 0,
        ];
        $args = wp_parse_args( $args, $defaults );

        $where  = [];
        $params = [];

        if ( $args['professional_id'] ) { $where[] = 'professional_id = %d'; $params[] = $args['professional_id']; }
        if ( $args['service_id'] )      { $where[] = 'service_id = %d';      $params[] = $args['service_id']; }
        if ( $args['status'] )          { $where[] = 'status = %s';          $params[] = $args['status']; }
        if ( $args['date_from'] )       { $where[] = 'booking_date >= %s';   $params[] = $args['date_from']; }
        if ( $args['date_to'] )         { $where[] = 'booking_date <= %s';   $params[] = $args['date_to']; }

        $where_clause = $where ? 'WHERE ' . implode( ' AND ', $where ) : '';
        $order = in_array( strtoupper( $args['order'] ), [ 'ASC', 'DESC' ] ) ? strtoupper( $args['order'] ) : 'DESC';
        $allowed_orderby = [ 'booking_date', 'booking_time', 'created_at', 'client_name' ];
        $orderby = in_array( $args['orderby'], $allowed_orderby ) ? $args['orderby'] : 'booking_date';

        $sql = "SELECT * FROM $table $where_clause ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $params[] = absint( $args['limit'] );
        $params[] = absint( $args['offset'] );

        return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
    }

    public static function update_status( $id, $status ) {
        global $wpdb;
        $status = in_array( $status, [ 'confirmed', 'cancelled', 'pending' ] ) ? $status : 'confirmed';
        return $wpdb->update(
            $wpdb->prefix . self::TABLE,
            [ 'status' => $status ],
            [ 'id' => absint( $id ) ],
            [ '%s' ],
            [ '%d' ]
        );
    }
}

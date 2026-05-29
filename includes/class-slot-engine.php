<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Slot_Engine {

    public static function init() {}

    public static function get_available_slots( $professional_id, $service_id, $date ) {
        $duration = (int) get_post_meta( $service_id, '_sb_duration', true );
        $slot_qty = max( 1, (int) get_post_meta( $service_id, '_sb_slot_qty', true ) ?: 1 );

        if ( ! $duration ) return [];

        $day_of_week = strtolower( date( 'l', strtotime( $date ) ) );
        $schedule    = (array) get_post_meta( $professional_id, '_sb_schedule', true );

        if ( empty( $schedule[ $day_of_week ] ) ) return [];

        $raw_slots = [];
        foreach ( $schedule[ $day_of_week ] as $seg ) {
            $raw_slots = array_merge( $raw_slots, self::generate( $seg['start'], $seg['end'], $duration ) );
        }

        $booked_counts = Bookings_DB::get_counts_for_date( $professional_id, $date );
        $cached_key    = "sk_slots_{$professional_id}_{$date}";
        $cached        = get_transient( $cached_key );

        $result = [];
        foreach ( $raw_slots as $t ) {
            $booked   = isset( $booked_counts[ $t ] ) ? $booked_counts[ $t ] : 0;
            $remaining = max( 0, $slot_qty - $booked );
            $result[] = [
                'time'      => $t,
                'remaining' => $remaining,
                'available' => $remaining > 0,
            ];
        }

        // Cache for 1 hour
        set_transient( $cached_key, $result, HOUR_IN_SECONDS );

        return $result;
    }

    public static function is_slot_available( $professional_id, $service_id, $date, $time ) {
        $slot_qty = max( 1, (int) get_post_meta( $service_id, '_sb_slot_qty', true ) ?: 1 );
        $booked   = Bookings_DB::count_for_slot( $professional_id, $date, $time );
        return $booked < $slot_qty;
    }

    private static function generate( $start, $end, $duration_minutes ) {
        $slots   = [];
        $current = strtotime( $start );
        $end_ts  = strtotime( $end );

        while ( $current + ( $duration_minutes * 60 ) <= $end_ts ) {
            $slots[] = date( 'H:i', $current );
            $current = $current + ( $duration_minutes * 60 );
        }
        return $slots;
    }

    public static function format_time( $time ) {
        return date( 'g:i A', strtotime( $time ) );
    }
}

<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Slot_Engine {

    public static function init() {}

    public static function get_available_slots( $service_id, $date ) {
        $duration = (int) get_post_meta( $service_id, '_sb_duration', true );
        $slot_qty = max( 1, (int) get_post_meta( $service_id, '_sb_slot_qty', true ) ?: 1 );

        if ( ! $duration ) return [];

        $day_of_week = strtolower( date( 'l', strtotime( $date ) ) );
        $schedule    = (array) get_post_meta( $service_id, '_sb_schedule', true );

        if ( empty( $schedule[ $day_of_week ] ) ) return [];

        $day_data = $schedule[ $day_of_week ];

        $segments  = [];
        $buffer    = 10;
        $max_daily = 0;

        if ( isset( $day_data['segments'] ) ) {
            $segments  = $day_data['segments'];
            $buffer    = (int) ( $day_data['buffer'] ?? 10 );
            $max_daily = (int) ( $day_data['max_daily'] ?? 0 );
        } else {
            $segments = $day_data;
        }

        if ( empty( $segments ) ) return [];

        // Check date exceptions
        $exceptions = (array) get_post_meta( $service_id, '_sb_exceptions', true );
        foreach ( $exceptions as $exc ) {
            if ( ( $exc['date'] ?? '' ) === $date ) return [];
        }

        $raw_slots = [];
        foreach ( $segments as $seg ) {
            $raw_slots = array_merge( $raw_slots, self::generate( $seg['start'], $seg['end'], $duration, $buffer ) );
        }

        $booked_counts = Bookings_DB::get_counts_for_date( $service_id, $date );
        $cached_key    = "sk_slots_{$service_id}_{$date}";

        $result = [];

        if ( $max_daily > 0 ) {
            $total_booked_today = array_sum( $booked_counts );
            $remaining_daily    = max( 0, $max_daily - $total_booked_today );
            if ( $remaining_daily <= 0 ) return [];
            $max_slots_to_offer = ceil( $remaining_daily / $slot_qty );
            $raw_slots          = array_slice( $raw_slots, 0, $max_slots_to_offer * 2 );
        }

        foreach ( $raw_slots as $t ) {
            if ( isset( $result[ $t ] ) ) continue;

            $booked   = isset( $booked_counts[ $t ] ) ? $booked_counts[ $t ] : 0;
            $remaining = max( 0, $slot_qty - $booked );
            $result[ $t ] = [
                'time'      => $t,
                'remaining' => $remaining,
                'available' => $remaining > 0,
            ];
        }

        $result = array_values( $result );

        set_transient( $cached_key, $result, HOUR_IN_SECONDS );

        return $result;
    }

    public static function is_slot_available( $service_id, $date, $time ) {
        $slot_qty = max( 1, (int) get_post_meta( $service_id, '_sb_slot_qty', true ) ?: 1 );
        $booked   = Bookings_DB::count_for_slot( $service_id, $date, $time );
        return $booked < $slot_qty;
    }

    private static function generate( $start, $end, $duration_minutes, $buffer = 0 ) {
        $slots   = [];
        $current = strtotime( $start );
        $end_ts  = strtotime( $end );

        while ( $current + ( $duration_minutes * 60 ) <= $end_ts ) {
            $slots[] = date( 'H:i', $current );
            $current = $current + ( $duration_minutes * 60 ) + ( $buffer * 60 );
        }
        return $slots;
    }

    public static function estimate_slots_for_day( $schedule_day, $duration = 45, $buffer = 10 ) {
        $total = 0;
        if ( ! $schedule_day || empty( $schedule_day['segments'] ) ) return 0;

        foreach ( $schedule_day['segments'] as $seg ) {
            $start_ts = strtotime( $seg['start'] );
            $end_ts   = strtotime( $seg['end'] );
            $step     = $duration + $buffer;
            while ( $start_ts + ( $duration * 60 ) <= $end_ts ) {
                $total++;
                $start_ts += $step * 60;
            }
        }
        return $total;
    }

    public static function format_time( $time ) {
        return date( 'g:i A', strtotime( $time ) );
    }
}

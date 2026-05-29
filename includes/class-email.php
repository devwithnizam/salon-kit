<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Email {

    public static function init() {}

    public static function send_booking_confirmation( $booking_id ) {
        global $wpdb;
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}salon_bookings WHERE id = %d",
            $booking_id
        ) );

        if ( ! $row ) return false;

        $settings = Settings::get();

        $service_name       = get_the_title( $row->service_id );
        $professional_name  = get_the_title( $row->professional_id );
        $booking_id_display = '#BK-' . str_pad( $row->id, 4, '0', STR_PAD_LEFT );
        $booking_date       = date_i18n( get_option( 'date_format' ), strtotime( $row->booking_date ) );
        $booking_time       = date_i18n( get_option( 'time_format' ), strtotime( $row->booking_time ) );

        $tags = [
            '{client_name}'       => $row->client_name,
            '{service_name}'      => $service_name,
            '{professional_name}' => $professional_name,
            '{booking_date}'      => $booking_date,
            '{booking_time}'      => $booking_time,
            '{booking_id}'        => $booking_id_display,
        ];

        $from_name  = $settings['from_name'];
        $from_email = $settings['from_email'];

        $headers = [
            "From: {$from_name} <{$from_email}>",
            'Content-Type: text/html; charset=UTF-8',
        ];

        $result = true;

        if ( $settings['customer_enabled'] === 'yes' && is_email( $row->client_email ) ) {
            $subject = str_replace( array_keys( $tags ), array_values( $tags ), $settings['customer_subject'] );
            $body = self::build_body( $row, $settings, $tags, 'customer' );
            $sent = wp_mail( $row->client_email, $subject, $body, $headers );
            if ( ! $sent ) $result = false;
        }

        if ( $settings['admin_enabled'] === 'yes' ) {
            $admin_emails = array_map( 'trim', explode( ',', $settings['admin_emails'] ) );
            $admin_emails = array_filter( $admin_emails, 'is_email' );

            if ( ! empty( $admin_emails ) ) {
                $subject = str_replace( array_keys( $tags ), array_values( $tags ), $settings['admin_subject'] );
                $body = self::build_body( $row, $settings, $tags, 'admin' );
                $sent = wp_mail( $admin_emails, $subject, $body, $headers );
                if ( ! $sent ) $result = false;
            }
        }

        return $result;
    }

    private static function build_body( $row, $settings, $tags, $type ) {
        $service_name       = $tags['{service_name}'];
        $professional_name  = $tags['{professional_name}'];
        $booking_date       = $tags['{booking_date}'];
        $booking_time       = $tags['{booking_time}'];
        $booking_id_display = $tags['{booking_id}'];
        $client_name        = $row->client_name;
        $notes              = $row->notes;

        $from_name = $settings['from_name'];

        if ( $type === 'admin' ) {
            $heading = 'New Booking Received';
            $greeting = 'A new booking has been made.';
        } else {
            $heading = 'Your booking is confirmed!';
            $greeting = "Hi {$client_name}, thank you for booking with us.";
        }

        ob_start();
        include SK_PATH . 'templates/email-booking-confirmation.php';
        return ob_get_clean();
    }
}

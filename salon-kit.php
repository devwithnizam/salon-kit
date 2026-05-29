<?php
/**
 * Plugin Name:       SalonKit — Booking System
 * Plugin URI:        https://github.com/devwithnizam/salon-kit
 * Description:       Complete salon appointment booking system with services, professionals, dynamic slots, Elementor widgets, and overbooking prevention. Fully customizable via Elementor.
 * Version:           2.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            MD Nizam Uddin
 * Author URI:        https://www.linkedin.com/in/devwithnizam/
 * License:           GPL v2 or later
 * Text Domain:       salon-kit
 * Domain Path:       /languages
 * Elementor tested up to: 3.24
 */

defined( 'ABSPATH' ) || exit;

define( 'SK_VERSION', '2.1.0' );
define( 'SK_FILE',    __FILE__ );
define( 'SK_PATH',    plugin_dir_path( __FILE__ ) );
define( 'SK_URL',     plugin_dir_url( __FILE__ ) );

// ── Autoload ─────────────────────────────────────────────
spl_autoload_register( function ( $class ) {
    $prefix = 'SalonKit\\';
    if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) return;

    $relative = substr( $class, strlen( $prefix ) );
    $parts    = explode( '\\', $relative );
    $name     = array_pop( $parts );
    $subdir   = strtolower( implode( '/', $parts ) );

    $filename = 'class-' . str_replace( '_', '-', strtolower( $name ) ) . '.php';

    if ( $subdir ) {
        $file = SK_PATH . "$subdir/$filename";
        if ( file_exists( $file ) ) { require_once $file; return; }
    }

    $file = SK_PATH . "includes/$filename";
    if ( file_exists( $file ) ) { require_once $file; return; }

    $file = SK_PATH . $filename;
    if ( file_exists( $file ) ) { require_once $file; }
} );

// ── Bootstrap ────────────────────────────────────────────
register_activation_hook( __FILE__, function () {
    require_once SK_PATH . 'includes/class-cpt.php';
    require_once SK_PATH . 'includes/class-bookings-db.php';
    \SalonKit\CPT::register_all();
    \SalonKit\Bookings_DB::create_table();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

add_action( 'plugins_loaded', function () {
    load_plugin_textdomain( 'salon-kit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    \SalonKit\CPT::init();
    \SalonKit\Bookings_DB::init();
    \SalonKit\Meta_Boxes::init();
    \SalonKit\Slot_Engine::init();
    \SalonKit\Ajax::init();
    \SalonKit\Admin::init();
    \SalonKit\Icons::init();
    \SalonKit\Frontend_Assets::init();
    \SalonKit\Settings::init();
    \SalonKit\Email::init();

    add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
        require_once SK_PATH . 'widgets/class-booking-widget.php';
        require_once SK_PATH . 'widgets/class-services-widget.php';
        $widgets_manager->register( new \SalonKit\Booking_Widget() );
        $widgets_manager->register( new \SalonKit\Services_Widget() );
    } );
} );

add_shortcode( 'salon_booking', function ( $atts ) {
    wp_enqueue_style( 'salon-kit-css' );
    wp_enqueue_script( 'salon-kit-js' );

    $defaults = [
        'data-step1-title' => 'Choose a Service',
        'data-step2-title' => 'Choose Your Professional',
        'data-step3-title' => 'Choose a Date',
        'data-step4-title' => 'Choose a Time',
        'data-step5-title' => 'Your Details',
        'data-step1-btn' => 'Choose Professional →',
        'data-step2-btn' => 'Choose Date →',
        'data-step3-btn' => 'Choose Time →',
        'data-step4-btn' => 'Your Details →',
        'data-submit-btn' => 'Confirm Booking',
        'data-back-btn' => '← Back',
        'data-book-again' => 'Book Another Appointment',
        'data-success-title' => "You're all booked!",
        'data-success-text' => 'A confirmation email has been sent to',
        'data-step-label-1' => 'Service',
        'data-step-label-2' => 'Professional',
        'data-step-label-3' => 'Date',
        'data-step-label-4' => 'Time',
        'data-step-label-5' => 'Details',
        'data-summary-service' => 'No service selected',
        'data-summary-pro' => 'No professional selected',
        'data-summary-date' => 'No date selected',
        'data-summary-time' => 'No time selected',
        'data-field-name-label' => 'Full Name',
        'data-field-email-label' => 'Email Address',
        'data-field-phone-label' => 'Phone Number',
        'data-field-notes-label' => 'Special Requests / Notes',
        'data-field-name-placeholder' => 'Jane Smith',
        'data-field-email-placeholder' => 'jane@example.com',
        'data-field-phone-placeholder' => '+1 (555) 000-0000',
        'data-field-notes-placeholder' => 'Any allergies, preferences or special requests...',
        'data-field-required-mark' => '*',
        'data-bsb-service' => 'Service',
        'data-bsb-professional' => 'Professional',
        'data-bsb-date' => 'Date',
        'data-bsb-time' => 'Time',
        'data-bsb-price' => 'Price',
        'data-msg-loading-services' => 'Loading services...',
        'data-msg-loading-pros' => 'Loading professionals...',
        'data-msg-loading-slots' => 'Loading available times...',
        'data-msg-empty-services' => 'No services available. Please check back later.',
        'data-msg-empty-pros' => 'No professionals available for this service.',
        'data-msg-empty-slots' => 'No available slots for this date. Choose another.',
        'data-msg-error-name' => 'Please enter your full name.',
        'data-msg-error-email' => 'Please enter a valid email.',
        'data-msg-error-network' => 'Network error. Check your connection.',
        'data-msg-error-slot-taken' => 'This slot was just taken. Please choose another.',
        'data-msg-submitting' => 'Submitting...',
        'data-slot-remaining' => 'left',
        'data-slot-full' => 'Full',
        'data-show-step-indicator' => 'yes',
        'data-show-summary-bar' => 'yes',
        'data-show-step1' => 'yes',
        'data-show-step2' => 'yes',
        'data-show-step3' => 'yes',
        'data-show-step4' => 'yes',
        'data-show-step5' => 'yes',
        'data-show-success' => 'yes',
        'data-show-field-name' => 'yes',
        'data-show-field-email' => 'yes',
        'data-show-field-phone' => 'yes',
        'data-show-field-notes' => 'yes',
        'data-show-service-price' => 'yes',
        'data-show-service-duration' => 'no',
        'data-show-service-desc' => 'yes',
        'data-show-service-images' => 'yes',
        'data-show-pro-photos' => 'yes',
        'data-show-remaining-slots' => 'yes',
        'data-show-booking-summary' => 'yes',
        'data-require-name' => 'yes',
        'data-require-email' => 'yes',
    ];

    $data_attrs = '';
    foreach ( $defaults as $key => $val ) {
        $data_attrs .= ' ' . $key . '="' . esc_attr( $val ) . '"';
    }

    ob_start();
    echo '<div id="salonBookingWrap" class="sb-wrap"' . $data_attrs . '>';
    include SK_PATH . 'templates/booking-form.php';
    echo '</div>';
    return ob_get_clean();
} );

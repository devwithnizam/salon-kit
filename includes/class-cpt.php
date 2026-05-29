<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class CPT {

    public static function init() {
        add_action( 'init', [ __CLASS__, 'register_all' ] );
    }

    public static function register_all() {
        self::register_service();
        self::register_professional();
        self::register_booking();
    }

    private static function register_service() {
        register_post_type( 'salon_service', [
            'labels' => [
                'name'               => 'Services',
                'singular_name'      => 'Service',
                'menu_name'          => 'SalonKit',
                'all_items'          => 'Services',
                'add_new'            => 'Add Service',
                'add_new_item'       => 'Add New Service',
                'edit_item'          => 'Edit Service',
                'view_item'          => 'View Service',
                'search_items'       => 'Search Services',
                'not_found'          => 'No services found.',
            ],
            'public'        => true,
            'show_ui'       => true,
            'show_in_menu'  => true,
            'menu_position' => 25,
            'menu_icon'     => self::menu_icon_data_url(),
            'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
            'has_archive'   => false,
            'rewrite'       => [ 'slug' => 'service' ],
            'show_in_rest'  => true,
        ] );
    }

    private static function register_professional() {
        register_post_type( 'salon_professional', [
            'labels' => [
                'name'               => 'Professionals',
                'singular_name'      => 'Professional',
                'menu_name'          => 'Professionals',
                'add_new'            => 'Add Professional',
                'add_new_item'       => 'Add New Professional',
                'edit_item'          => 'Edit Professional',
                'view_item'          => 'View Professional',
                'search_items'       => 'Search Professionals',
                'not_found'          => 'No professionals found.',
            ],
            'public'        => true,
            'show_ui'       => true,
            'show_in_menu'  => 'edit.php?post_type=salon_service',
            'supports'      => [ 'title', 'editor', 'thumbnail' ],
            'has_archive'   => false,
            'rewrite'       => [ 'slug' => 'professional' ],
            'show_in_rest'  => true,
        ] );
    }

    public static function menu_icon_svg() {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#7f8388" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 3q4 5 7 8-2 4-4 7"/>
            <path d="M19 3q-4 5-7 8 2 4 4 7"/>
            <circle cx="8" cy="20" r="2.5"/>
            <circle cx="16" cy="20" r="2.5"/>
            <polygon points="12,9.5 13.5,11 12,12.5 10.5,11" fill="#7f8388"/>
        </svg>';
    }

    public static function menu_icon_data_url() {
        return 'data:image/svg+xml;base64,' . base64_encode( self::menu_icon_svg() );
    }

    public static function brand_icon_svg( $size = 20, $color = 'currentColor' ) {
        return '<svg width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="' . esc_attr( $color ) . '" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle">
            <path d="M5 3q4 5 7 8-2 4-4 7"/>
            <path d="M19 3q-4 5-7 8 2 4 4 7"/>
            <circle cx="8" cy="20" r="2.5"/>
            <circle cx="16" cy="20" r="2.5"/>
            <polygon points="12,9.5 13.5,11 12,12.5 10.5,11" fill="' . esc_attr( $color ) . '"/>
        </svg>';
    }

    private static function register_booking() {
        register_post_type( 'salon_booking', [
            'labels' => [
                'name'               => 'Bookings',
                'singular_name'      => 'Booking',
                'menu_name'          => 'Bookings',
                'add_new_item'       => 'Add New Booking',
                'edit_item'          => 'Edit Booking',
                'view_item'          => 'View Booking',
                'all_items'          => 'All Bookings',
                'search_items'       => 'Search Bookings',
                'not_found'          => 'No bookings found.',
            ],
            'public'        => false,
            'show_ui'       => true,
            'show_in_menu'  => 'edit.php?post_type=salon_service',
            'supports'      => [ 'title' ],
            'capability_type' => 'post',
        ] );
    }
}

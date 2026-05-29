<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Frontend_Assets {

    public static function init() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_assets' ] );
    }

    public static function register_assets() {
        // CSS
        wp_register_style(
            'salon-kit-css',
            SK_URL . 'assets/css/salon-kit.css',
            [],
            SK_VERSION
        );

        // JS — no jQuery dependency
        wp_register_script(
            'salon-kit-js',
            SK_URL . 'assets/js/salon-kit.js',
            [],
            SK_VERSION,
            true
        );

        // Localize data
        $services = [];
        $all = get_posts( [
            'post_type'      => 'salon_service',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );
        foreach ( $all as $svc ) {
            $thumb_id  = get_post_thumbnail_id( $svc->ID );
            $services[] = [
                'id'          => $svc->ID,
                'name'        => $svc->post_title,
                'description' => get_the_excerpt( $svc ),
                'price'       => get_post_meta( $svc->ID, '_sb_price', true ),
                'duration'    => (int) get_post_meta( $svc->ID, '_sb_duration', true ),
                'slot_qty'    => (int) get_post_meta( $svc->ID, '_sb_slot_qty', true ) ?: 1,
                'thumb_url'   => $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '',
                'menu_order'  => $svc->menu_order,
            ];
        }

        wp_localize_script( 'salon-kit-js', 'SalonKit', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'salon_booking_nonce' ),
            'services' => $services,
        ] );
    }
}

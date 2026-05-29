<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Icons {

    public static function init() {
        add_filter( 'elementor/icons_manager/native', [ __CLASS__, 'register_icon_set' ] );
    }

    public static function register_icon_set( $icons ) {
        $icons['salonkit'] = [
            'name'          => 'salonkit',
            'label'         => 'SalonKit',
            'url'           => '',
            'enqueue'       => '',
            'prefix'        => 'salonkit-',
            'displayPrefix' => '',
            'labelIcon'     => 'salonkit-logo',
            'ver'           => SK_VERSION,
            'fetchJson'     => SK_URL . 'assets/icons/salonkit-icons.json',
            'native'        => true,
        ];
        return $icons;
    }

    public static function get_default_colors() {
        return [
            'primary'         => '#6366f1',
            'primary-hover'   => '#4f46e5',
            'primary-lite'    => '#eef2ff',
            'accent'          => '#f59e0b',
            'accent-soft'     => '#fef3c7',
            'bg'              => '#ffffff',
            'text'            => '#0f172a',
            'text-muted'      => '#64748b',
            'border'          => '#e2e8f0',
            'card-bg'         => '#ffffff',
            'card-border'     => '#e2e8f0',
            'card-active-bg'  => '#6366f1',
            'card-active-text'=> '#ffffff',
            'input-bg'        => '#ffffff',
            'input-border'    => '#e2e8f0',
            'input-focus'     => '#6366f1',
            'btn-primary-bg'  => '#6366f1',
            'btn-primary-text'=> '#ffffff',
            'btn-primary-hover'=>'#4f46e5',
            'btn-back-bg'     => '#f1f5f9',
            'btn-back-text'   => '#475569',
            'btn-back-hover'  => '#e2e8f0',
            'success-icon'    => '#10b981',
            'error'           => '#ef4444',
            'summary-bg'      => '#f8fafc',
            'label'           => '#0f172a',
            'shadow-color'    => 'rgba(99,102,241,0.12)',
            'step-done'       => '#6366f1',
            'today-border'    => '#f59e0b',
            'slot-full'       => '#ef4444',
            'slot-full-bg'    => '#fef2f2',
        ];
    }
}

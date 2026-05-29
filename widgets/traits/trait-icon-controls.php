<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

trait Icon_Controls {

    protected function register_icon_controls() {
        $this->start_controls_section( 'section_icons', [
            'label' => 'Icons',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'icons_note', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => 'Choose icons for each section. Leave default for SalonKit custom icons. Supports any FontAwesome / Elementor icon.',
        ] );

        $icons = [
            'icon_service_card'  => [ 'Service Card Icon', 'salonkit-scissors' ],
            'icon_pro_card'      => [ 'Professional Card Icon', 'salonkit-professional' ],
            'icon_date'          => [ 'Date Icon', 'salonkit-calendar' ],
            'icon_time'          => [ 'Time Icon', 'salonkit-clock' ],
            'icon_summary_svc'   => [ 'Summary Service Icon', 'salonkit-scissors' ],
            'icon_summary_pro'   => [ 'Summary Professional Icon', 'salonkit-professional' ],
            'icon_summary_date'  => [ 'Summary Date Icon', 'salonkit-calendar' ],
            'icon_summary_time'  => [ 'Summary Time Icon', 'salonkit-clock' ],
            'icon_success'       => [ 'Success Checkmark', 'salonkit-confirmed' ],
            'icon_back'          => [ 'Back Arrow', 'salonkit-arrow-left' ],
            'icon_next'          => [ 'Next Arrow', 'salonkit-arrow-right' ],
            'icon_cal_prev'      => [ 'Calendar Prev', 'salonkit-chevron-left' ],
            'icon_cal_next'      => [ 'Calendar Next', 'salonkit-chevron-right' ],
        ];

        foreach ( $icons as $key => $data ) {
            $this->add_control( $key, [
                'label' => $data[0],
                'type'  => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value'   => $data[1],
                    'library' => 'salonkit',
                ],
            ] );
        }

        $this->end_controls_section();
    }
}

<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

trait Visibility_Controls {

    protected function register_visibility_controls() {
        $this->start_controls_section( 'section_visibility', [
            'label' => 'Visibility (Show / Hide)',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'visibility_note', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => 'Toggle visibility of each element in the form. Disabled elements are removed from DOM.',
        ] );

        $switches = [
            'show_step_indicator' => [ 'Step Indicator', 'yes' ],
            'show_summary_bar'    => [ 'Summary Bar', 'yes' ],
            'show_step1'          => [ 'Step 1 — Service Selection', 'yes' ],
            'show_step2'          => [ 'Step 2 — Professional Selection', 'yes' ],
            'show_step3'          => [ 'Step 3 — Date Selection', 'yes' ],
            'show_step4'          => [ 'Step 4 — Time Selection', 'yes' ],
            'show_step5'          => [ 'Step 5 — Details Form', 'yes' ],
            'show_success'        => [ 'Success Screen', 'yes' ],
            'show_field_name'     => [ 'Name Field', 'yes' ],
            'show_field_email'    => [ 'Email Field', 'yes' ],
            'show_field_phone'    => [ 'Phone Field', 'yes' ],
            'show_field_notes'    => [ 'Notes Field', 'yes' ],
            'show_service_price'  => [ 'Price on Service Cards', 'yes' ],
            'show_service_duration' => [ 'Duration on Service Cards', 'yes' ],
            'show_service_desc'   => [ 'Description on Service Cards', 'yes' ],
            'show_service_images' => [ 'Images on Service Cards', 'yes' ],
            'show_pro_photos'     => [ 'Photos on Professional Cards', 'yes' ],
            'show_remaining_slots'=> [ 'Remaining Slot Count', 'yes' ],
            'show_booking_summary'=> [ 'Booking Summary Box (Step 5)', 'yes' ],
        ];

        foreach ( $switches as $key => $data ) {
            $this->add_control( $key, [
                'label'        => $data[0],
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => 'Show',
                'label_off'    => 'Hide',
                'return_value' => 'yes',
                'default'      => $data[1],
            ] );
        }

        $this->add_control( 'required_heading', [
            'label'     => 'Required Fields',
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_control( 'require_name', [
            'label'        => 'Name Required',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Required',
            'label_off'    => 'Optional',
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'require_email', [
            'label'        => 'Email Required',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Required',
            'label_off'    => 'Optional',
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->end_controls_section();
    }
}

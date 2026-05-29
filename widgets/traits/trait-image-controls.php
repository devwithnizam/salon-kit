<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

trait Image_Controls {

    protected function register_image_controls() {
        $this->start_controls_section( 'section_images', [
            'label' => 'Image Design',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'image_note', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => 'Control how service & professional images look on cards.',
        ] );

        $this->add_control( 'svc_image_heading', [
            'label' => 'Service Images',
            'type'  => \Elementor\Controls_Manager::HEADING,
        ] );

        $this->add_responsive_control( 'svc_image_size', [
            'label'      => 'Width / Height',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 24, 'max' => 120 ] ],
            'default'    => [ 'size' => 52, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-svc-thumb' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'svc_image_radius', [
            'label'      => 'Border Radius',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'size' => 8, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-svc-thumb' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'svc_image_border_type', [
            'label'   => 'Border Type',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'none'   => 'None',
                'solid'  => 'Solid',
                'dashed' => 'Dashed',
                'dotted' => 'Dotted',
            ],
            'default' => 'none',
            'selectors' => [
                '{{WRAPPER}} .sb-svc-thumb' => 'border-style: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'svc_image_border_width', [
            'label'      => 'Border Width',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 1, 'max' => 8 ] ],
            'default'    => [ 'size' => 2, 'unit' => 'px' ],
            'condition'  => [ 'svc_image_border_type!' => 'none' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-svc-thumb' => 'border-width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'svc_image_border_color', [
            'label'     => 'Border Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#e2e8f0',
            'condition' => [ 'svc_image_border_type!' => 'none' ],
            'selectors' => [
                '{{WRAPPER}} .sb-svc-thumb' => 'border-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'pro_image_heading', [
            'label'     => 'Professional Photos',
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'pro_image_size', [
            'label'      => 'Width / Height',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 24, 'max' => 120 ] ],
            'default'    => [ 'size' => 48, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-pro-photo' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'pro_image_radius', [
            'label'      => 'Border Radius',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'size' => 50, 'unit' => '%' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-pro-photo' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'pro_image_border_type', [
            'label'   => 'Border Type',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'none'   => 'None',
                'solid'  => 'Solid',
                'dashed' => 'Dashed',
                'dotted' => 'Dotted',
            ],
            'default' => 'none',
            'selectors' => [
                '{{WRAPPER}} .sb-pro-photo' => 'border-style: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'pro_image_border_width', [
            'label'      => 'Border Width',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 1, 'max' => 8 ] ],
            'condition'  => [ 'pro_image_border_type!' => 'none' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-pro-photo' => 'border-width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'pro_image_border_color', [
            'label'     => 'Border Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'condition' => [ 'pro_image_border_type!' => 'none' ],
            'selectors' => [
                '{{WRAPPER}} .sb-pro-photo' => 'border-color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();
    }
}

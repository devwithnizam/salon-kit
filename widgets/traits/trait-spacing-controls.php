<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

trait Spacing_Controls {

    protected function register_spacing_controls() {
        $this->start_controls_section( 'section_spacing', [
            'label' => 'Layout & Spacing',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'form_width', [
            'label'      => 'Form Max Width',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%', 'vw' ],
            'range'      => [ 'px' => [ 'min' => 320, 'max' => 1200 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 740 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'form_padding', [
            'label'      => 'Form Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [ 'top' => 32, 'right' => 28, 'bottom' => 36, 'left' => 28, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'form_border_radius', [
            'label'      => 'Form Border Radius',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 12 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'form_border_width', [
            'label'      => 'Form Border Width',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 10 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 1 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-wrap' => 'border-width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'form_shadow_heading', [
            'label' => 'Form Shadow',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_control( 'form_shadow', [
            'label'     => 'Box Shadow',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => [
                'none'   => 'None',
                'sm'     => 'Small',
                'md'     => 'Medium',
                'lg'     => 'Large',
                'xl'     => 'Extra Large',
            ],
            'default'   => 'md',
            'selectors_dictionary' => [
                'none' => 'none',
                'sm'   => '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                'md'   => '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                'lg'   => '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
                'xl'   => '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)',
            ],
            'selectors' => [
                '{{WRAPPER}} .sb-wrap' => 'box-shadow: {{VALUE}};',
            ],
        ] );

        // Card spacing
        $this->add_control( 'card_heading', [
            'label' => 'Cards',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'card_gap', [
            'label'      => 'Card Grid Gap',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 12 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-services-grid, {{WRAPPER}} .sb-pro-grid' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'card_padding', [
            'label'      => 'Card Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'default'    => [ 'top' => 12, 'right' => 12, 'bottom' => 12, 'left' => 12, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-service-card, {{WRAPPER}} .sb-pro-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'card_radius', [
            'label'      => 'Card Border Radius',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 10 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-service-card, {{WRAPPER}} .sb-pro-card, {{WRAPPER}} .sb-calendar-wrap' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'card_border_width', [
            'label'      => 'Card Border Width',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 5 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 1 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-service-card, {{WRAPPER}} .sb-pro-card' => 'border-width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'card_shadow', [
            'label'     => 'Card Hover Shadow',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => [
                'none' => 'None',
                'sm'   => 'Small',
                'md'   => 'Medium',
                'lg'   => 'Large',
            ],
            'default'   => 'md',
            'selectors_dictionary' => [
                'none' => 'none',
                'sm'   => '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)',
                'md'   => '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                'lg'   => '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
            ],
            'selectors' => [
                '{{WRAPPER}} .sb-service-card:hover, {{WRAPPER}} .sb-pro-card:hover' => 'box-shadow: {{VALUE}};',
            ],
        ] );

        // Buttons
        $this->add_control( 'btn_heading', [
            'label' => 'Buttons',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'button_height', [
            'label'      => 'Button Height',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 30, 'max' => 70 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 50 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-btn' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'button_radius', [
            'label'      => 'Button Border Radius',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 8 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'button_padding', [
            'label'      => 'Button Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'default'    => [ 'top' => 0, 'right' => 28, 'bottom' => 0, 'left' => 28, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        // Inputs
        $this->add_control( 'input_heading', [
            'label' => 'Input Fields',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'input_height', [
            'label'      => 'Input Height',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 30, 'max' => 60 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 44 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-field-group input' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_control( 'input_radius', [
            'label'      => 'Input Border Radius',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 6 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-field-group input, {{WRAPPER}} .sb-field-group textarea' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ] );

        // Summary bar
        $this->add_control( 'summary_heading_spacing', [
            'label' => 'Summary Bar',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_responsive_control( 'summary_padding', [
            'label'      => 'Summary Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'default'    => [ 'top' => 14, 'right' => 18, 'bottom' => 14, 'left' => 18, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sb-summary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        // Step indicator
        $this->add_control( 'step_heading_spacing', [
            'label' => 'Step Indicator',
            'type'  => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ] );

        $this->add_control( 'step_size', [
            'label'      => 'Step Circle Size',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 24, 'max' => 50 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 34 ],
            'selectors'  => [
                '{{WRAPPER}} .sb-step-num' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }
}

<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

trait Typography_Controls {

    protected function register_typography_controls() {
        $this->start_controls_section( 'section_typography', [
            'label' => 'Typography',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $typography_items = [
            'panel_title_typo' => [
                'label' => 'Panel Title',
                'selector' => '{{WRAPPER}} .sb-panel-title',
            ],
            'step_label_typo' => [
                'label' => 'Step Label',
                'selector' => '{{WRAPPER}} .sb-step-label',
            ],
            'step_number_typo' => [
                'label' => 'Step Number',
                'selector' => '{{WRAPPER}} .sb-step-num',
            ],
            'service_name_typo' => [
                'label' => 'Service Name',
                'selector' => '{{WRAPPER}} .sb-svc-name',
            ],
            'service_price_typo' => [
                'label' => 'Service Price',
                'selector' => '{{WRAPPER}} .sb-svc-price',
            ],
            'service_desc_typo' => [
                'label' => 'Service Description',
                'selector' => '{{WRAPPER}} .sb-svc-desc',
            ],
            'pro_name_typo' => [
                'label' => 'Professional Name',
                'selector' => '{{WRAPPER}} .sb-pro-name',
            ],
            'pro_bio_typo' => [
                'label' => 'Professional Bio',
                'selector' => '{{WRAPPER}} .sb-pro-bio',
            ],
            'summary_text_typo' => [
                'label' => 'Summary Text',
                'selector' => '{{WRAPPER}} .sb-summary-text',
            ],
            'field_label_typo' => [
                'label' => 'Field Label',
                'selector' => '{{WRAPPER}} .sb-field-group label',
            ],
            'field_input_typo' => [
                'label' => 'Field Input',
                'selector' => '{{WRAPPER}} .sb-field-group input, {{WRAPPER}} .sb-field-group textarea',
            ],
            'button_text_typo' => [
                'label' => 'Button Text',
                'selector' => '{{WRAPPER}} .sb-btn',
            ],
            'success_title_typo' => [
                'label' => 'Success Title',
                'selector' => '{{WRAPPER}} .sb-success h2',
            ],
            'calendar_label_typo' => [
                'label' => 'Calendar Month Label',
                'selector' => '{{WRAPPER}} .sb-cal-month-label',
            ],
            'time_slot_typo' => [
                'label' => 'Time Slot',
                'selector' => '{{WRAPPER}} .sb-time-slot',
            ],
            'booking_summary_typo' => [
                'label' => 'Booking Summary Box',
                'selector' => '{{WRAPPER}} .sb-bsb-row',
            ],
        ];

        foreach ( $typography_items as $key => $item ) {
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name'     => $key,
                    'label'    => $item['label'],
                    'selector' => $item['selector'],
                ]
            );
        }

        $this->end_controls_section();
    }
}

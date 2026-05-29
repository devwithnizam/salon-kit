<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

trait Color_Controls {

    protected function register_color_controls() {
        $this->start_controls_section( 'section_colors', [
            'label' => 'Colors',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $colors = [
            'primary_color'    => [ 'Primary', '#6366f1', '--sk-primary' ],
            'primary_hover'    => [ 'Primary Hover', '#4f46e5', '--sk-primary-hover' ],
            'primary_light'    => [ 'Primary Light', '#eef2ff', '--sk-primary-lite' ],
            'accent_color'     => [ 'Accent', '#f59e0b', '--sk-accent' ],
            'accent_soft'      => [ 'Accent Soft', '#fef3c7', '--sk-accent-soft' ],
            'body_bg'          => [ 'Form Background', '#ffffff', '--sk-bg' ],
            'text_color'       => [ 'Text', '#0f172a', '--sk-text' ],
            'text_muted'       => [ 'Text Muted', '#64748b', '--sk-text-muted' ],
            'border_color'     => [ 'Border', '#e2e8f0', '--sk-border' ],
            'card_bg'          => [ 'Card Background', '#ffffff', '--sk-card-bg' ],
            'card_border'      => [ 'Card Border', '#e2e8f0', '--sk-card-border' ],
            'card_active_bg'   => [ 'Card Active Background', '#6366f1', '--sk-card-active-bg' ],
            'card_active_text' => [ 'Card Active Text', '#ffffff', '--sk-card-active-text' ],
            'input_bg'         => [ 'Input Background', '#ffffff', '--sk-input-bg' ],
            'input_border'     => [ 'Input Border', '#e2e8f0', '--sk-input-border' ],
            'input_focus'      => [ 'Input Focus Border', '#6366f1', '--sk-input-focus' ],
            'btn_primary_bg'   => [ 'Button Primary Background', '#6366f1', '--sk-btn-primary-bg' ],
            'btn_primary_text' => [ 'Button Primary Text', '#ffffff', '--sk-btn-primary-text' ],
            'btn_primary_hover'=> [ 'Button Primary Hover', '#4f46e5', '--sk-btn-primary-hover' ],
            'btn_back_bg'      => [ 'Button Back Background', '#f1f5f9', '--sk-btn-back-bg' ],
            'btn_back_text'    => [ 'Button Back Text', '#475569', '--sk-btn-back-text' ],
            'btn_back_hover'   => [ 'Button Back Hover', '#e2e8f0', '--sk-btn-back-hover' ],
            'success_icon_bg'  => [ 'Success Icon Background', '#10b981', '--sk-success-icon' ],
            'error_color'      => [ 'Error Text', '#ef4444', '--sk-error' ],
            'summary_bg'       => [ 'Summary Background', '#f8fafc', '--sk-summary-bg' ],
            'field_label'      => [ 'Field Label', '#0f172a', '--sk-label' ],
            'shadow_color'     => [ 'Shadow Color', 'rgba(99,102,241,0.12)', '--sk-shadow-color' ],
            'step_done_bg'     => [ 'Step Done Background', '#6366f1', '--sk-step-done' ],
            'calendar_today'   => [ 'Calendar Today Border', '#f59e0b', '--sk-today-border' ],
            'slider_full_icon' => [ 'Slot Full Color', '#ef4444', '--sk-slot-full' ],
            'slider_full_bg'   => [ 'Slot Full Background', '#fef2f2', '--sk-slot-full-bg' ],
        ];

        foreach ( $colors as $key => $data ) {
            $this->add_control( $key, [
                'label'     => $data[0],
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => $data[1],
                'selectors' => [
                    '{{WRAPPER}} .sb-wrap' => "{$data[2]}: {{VALUE}};",
                ],
            ] );
        }

        $this->end_controls_section();
    }
}

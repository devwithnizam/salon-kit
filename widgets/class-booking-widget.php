<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

require_once SK_PATH . 'widgets/traits/trait-text-controls.php';
require_once SK_PATH . 'widgets/traits/trait-visibility-controls.php';
require_once SK_PATH . 'widgets/traits/trait-color-controls.php';
require_once SK_PATH . 'widgets/traits/trait-typography-controls.php';
require_once SK_PATH . 'widgets/traits/trait-spacing-controls.php';
require_once SK_PATH . 'widgets/traits/trait-icon-controls.php';

class Booking_Widget extends \Elementor\Widget_Base {

    use Text_Controls;
    use Visibility_Controls;
    use Color_Controls;
    use Typography_Controls;
    use Spacing_Controls;
    use Icon_Controls;

    public function get_name() {
        return 'salon_booking_form';
    }

    public function get_title() {
        return 'Salon Booking Form';
    }

    public function get_icon() {
        return 'salonkit-logo';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_keywords() {
        return [ 'salon', 'booking', 'appointment', 'salonkit', 'form' ];
    }

    protected function register_controls() {
        $this->register_text_controls();
        $this->register_visibility_controls();
        $this->register_icon_controls();
        $this->register_color_controls();
        $this->register_typography_controls();
        $this->register_spacing_controls();

        $this->start_controls_section( 'section_advanced', [
            'label' => 'Advanced',
            'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
        ] );

        $this->add_control( 'css_classes', [
            'label'       => 'Additional CSS Classes',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'description' => 'Space-separated class names to add to the form wrapper.',
        ] );

        $this->add_control( 'custom_attributes', [
            'label'       => 'Custom Data Attributes',
            'type'        => \Elementor\Controls_Manager::TEXTAREA,
            'default'     => '',
            'description' => 'One per line: key=value (will become data-key="value")',
        ] );

        $this->end_controls_section();
    }

    private function get_text_settings() {
        $s = $this->get_settings_for_display();
        $keys = [
            'step1_title', 'step2_title', 'step3_title', 'step4_title', 'step5_title',
            'step1_btn', 'step2_btn', 'step3_btn', 'step4_btn', 'submit_btn', 'back_btn',
            'book_again', 'success_title', 'success_text',
            'step_label_1', 'step_label_2', 'step_label_3', 'step_label_4', 'step_label_5',
            'summary_service', 'summary_pro', 'summary_date', 'summary_time',
            'field_name_label', 'field_email_label', 'field_phone_label', 'field_notes_label',
            'field_name_placeholder', 'field_email_placeholder', 'field_phone_placeholder', 'field_notes_placeholder',
            'field_required_mark',
            'bsb_service', 'bsb_professional', 'bsb_date', 'bsb_time', 'bsb_price',
            'msg_loading_services', 'msg_loading_pros', 'msg_loading_slots',
            'msg_empty_services', 'msg_empty_pros', 'msg_empty_slots',
            'msg_error_name', 'msg_error_email', 'msg_error_network', 'msg_error_slot_taken',
            'msg_submitting', 'slot_remaining', 'slot_full',
        ];
        $data = [];
        foreach ( $keys as $k ) {
            $data[ $k ] = $s[ $k ] ?? '';
        }
        return $data;
    }

    private function get_visibility_settings() {
        $s = $this->get_settings_for_display();
        $keys = [
            'show_step_indicator', 'show_summary_bar',
            'show_step1', 'show_step2', 'show_step3', 'show_step4', 'show_step5', 'show_success',
            'show_field_name', 'show_field_email', 'show_field_phone', 'show_field_notes',
            'show_service_price', 'show_service_duration', 'show_service_desc', 'show_service_images',
            'show_pro_photos', 'show_remaining_slots', 'show_booking_summary',
            'require_name', 'require_email',
        ];
        $data = [];
        foreach ( $keys as $k ) {
            $data[ $k ] = $s[ $k ] ?? 'yes';
        }
        return $data;
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        if ( ! wp_style_is( 'salon-kit-css', 'enqueued' ) ) {
            wp_enqueue_style( 'salon-kit-css' );
        }
        if ( ! wp_script_is( 'salon-kit-js', 'enqueued' ) ) {
            wp_enqueue_script( 'salon-kit-js' );
        }

        $texts     = $this->get_text_settings();
        $visiblity = $this->get_visibility_settings();

        $wrapper_classes = [ 'sb-wrap' ];
        if ( ! empty( $settings['css_classes'] ) ) {
            $wrapper_classes[] = esc_attr( $settings['css_classes'] );
        }

        $custom_attrs = '';
        if ( ! empty( $settings['custom_attributes'] ) ) {
            $lines = explode( "\n", $settings['custom_attributes'] );
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( strpos( $line, '=' ) !== false ) {
                    [ $key, $val ] = explode( '=', $line, 2 );
                    $custom_attrs .= ' data-' . esc_attr( trim( $key ) ) . '="' . esc_attr( trim( $val ) ) . '"';
                }
            }
        }

        $data_attrs = '';
        foreach ( $texts as $key => $val ) {
            $data_attrs .= ' data-' . str_replace( '_', '-', $key ) . '="' . esc_attr( $val ) . '"';
        }
        foreach ( $visiblity as $key => $val ) {
            $data_attrs .= ' data-' . str_replace( '_', '-', $key ) . '="' . esc_attr( $val ) . '"';
        }

        echo '<div class="' . esc_attr( implode( ' ', $wrapper_classes ) ) . '"' . $data_attrs . $custom_attrs . '>';
        include SK_PATH . 'templates/booking-form.php';
        echo '</div>';
    }

    protected function content_template() {
        ?>
        <div class="sb-wrap">
            <div style="padding:60px 20px;text-align:center;border:2px dashed var(--sk-border, #e2e8f0);border-radius:12px;font-family:system-ui,sans-serif;">
                <span style="font-size:32px;display:block;margin-bottom:12px;">📅</span>
                <strong style="font-size:18px;color:var(--sk-primary, #6366f1);">Salon Booking Form</strong>
                <p style="margin:6px 0 0;font-size:13px;color:var(--sk-text-muted, #64748b);">
                    Configure all text, colors, typography &amp; visibility in the Elementor panel.<br>
                    Preview works on the frontend.
                </p>
            </div>
        </div>
        <?php
    }
}

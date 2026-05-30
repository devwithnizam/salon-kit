<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

require_once SK_PATH . 'widgets/traits/trait-text-controls.php';
require_once SK_PATH . 'widgets/traits/trait-visibility-controls.php';
require_once SK_PATH . 'widgets/traits/trait-color-controls.php';
require_once SK_PATH . 'widgets/traits/trait-typography-controls.php';
require_once SK_PATH . 'widgets/traits/trait-spacing-controls.php';
require_once SK_PATH . 'widgets/traits/trait-image-controls.php';

class Booking_Widget extends \Elementor\Widget_Base {

    use Text_Controls;
    use Visibility_Controls;
    use Color_Controls;
    use Typography_Controls;
    use Spacing_Controls;
    use Image_Controls;

    public function get_name() {
        return 'salon_booking_form';
    }

    public function get_title() {
        return 'Salon Booking Form';
    }

    public function get_icon() {
        return 'dashicons-calendar-alt';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_keywords() {
        return [ 'salon', 'booking', 'appointment', 'salonkit', 'form' ];
    }

    public function get_script_depends() {
        return [ 'salon-kit-js' ];
    }

    public function get_style_depends() {
        return [ 'salon-kit-css' ];
    }

    protected function register_controls() {
        $this->register_text_controls();
        $this->register_visibility_controls();
        $this->register_image_controls();
        $this->register_color_controls();
        $this->register_typography_controls();
        $this->register_spacing_controls();

        $this->start_controls_section( 'section_service_order', [
            'label' => 'Service Order',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'services_orderby', [
            'label'   => 'Order By',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'menu_order' => 'Custom Order (menu order)',
                'title'      => 'Name (alphabetical)',
                'date'       => 'Date Created',
                'price'      => 'Price',
                'duration'   => 'Duration',
            ],
            'default' => 'menu_order',
        ] );

        $this->add_control( 'services_order', [
            'label'   => 'Order Direction',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'asc'  => 'Ascending',
                'desc' => 'Descending',
            ],
            'default' => 'asc',
        ] );

        $this->add_control( 'services_order_note', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => '<small>Set the custom order from <strong>Services → Edit Service → Sort Order</strong> field in the Service Details metabox.</small>',
        ] );

        $this->end_controls_section();

        $this->start_controls_section( 'section_summary', [
            'label' => 'Summary Bar',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'summary_bg', [
            'label'     => 'Background',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .sb-wrap' => '--sk-summary-bg: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'summary_border', [
            'label'     => 'Border',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#e2e8f0',
            'selectors' => [
                '{{WRAPPER}} .sb-wrap' => '--sk-summary-border: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'summary_icon_bg', [
            'label'     => 'Icon Background',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#eef2ff',
            'selectors' => [
                '{{WRAPPER}} .sb-wrap' => '--sk-summary-icon-bg: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'summary_label', [
            'label'     => 'Label Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#64748b',
            'selectors' => [
                '{{WRAPPER}} .sb-wrap' => '--sk-summary-label: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'summary_value', [
            'label'     => 'Value Color (inactive)',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#0f172a',
            'selectors' => [
                '{{WRAPPER}} .sb-wrap' => '--sk-summary-value: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'summary_value_active', [
            'label'     => 'Value Color (selected)',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#6366f1',
            'selectors' => [
                '{{WRAPPER}} .sb-wrap' => '--sk-summary-active: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();

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
            'step1_title', 'step2_title', 'step3_title', 'step4_title',
            'step1_btn', 'step2_btn', 'step3_btn', 'submit_btn', 'back_btn',
            'book_again', 'success_title', 'success_text',
            'step_label_1', 'step_label_2', 'step_label_3', 'step_label_4',
            'summary_service', 'summary_date', 'summary_time',
            'field_name_label', 'field_email_label', 'field_phone_label', 'field_notes_label',
            'field_name_placeholder', 'field_email_placeholder', 'field_phone_placeholder', 'field_notes_placeholder',
            'field_required_mark',
            'bsb_service', 'bsb_date', 'bsb_time', 'bsb_price',
            'msg_loading_services', 'msg_loading_slots',
            'msg_empty_services', 'msg_empty_slots',
            'msg_error_name', 'msg_error_email', 'msg_error_network', 'msg_error_slot_taken',
            'msg_submitting', 'slot_remaining', 'slot_full', 'free_label',
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
            'show_step1', 'show_step2', 'show_step3', 'show_step4', 'show_success',
            'show_field_name', 'show_field_email', 'show_field_phone', 'show_field_notes',
            'show_service_price', 'show_service_duration', 'show_service_desc', 'show_service_images',
            'show_remaining_slots', 'show_booking_summary',
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
            $wrapper_classes[] = $settings['css_classes'];
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
        $data_attrs .= ' data-services-orderby="' . esc_attr( $settings['services_orderby'] ?? 'menu_order' ) . '"';
        $data_attrs .= ' data-services-order="' . esc_attr( $settings['services_order'] ?? 'asc' ) . '"';

        $query_orderby = $settings['services_orderby'] ?? 'menu_order';
        $query_order   = strtoupper( $settings['services_order'] ?? 'asc' );

        $args = [
            'post_type'      => 'salon_service',
            'posts_per_page' => -1,
        ];

        if ( in_array( $query_orderby, [ 'menu_order', 'title', 'date' ], true ) ) {
            $args['orderby'] = $query_orderby;
            $args['order']   = $query_order;
        } elseif ( in_array( $query_orderby, [ 'price', 'duration' ], true ) ) {
            $meta_key = $query_orderby === 'price' ? '_sb_price' : '_sb_duration';
            $args['meta_key'] = $meta_key;
            $args['orderby']  = 'meta_value_num';
            $args['order']    = $query_order;
        }

        $all_services = get_posts( $args );
        $svc_data = [];
        foreach ( $all_services as $svc ) {
            $thumb_id = get_post_thumbnail_id( $svc->ID );
            $svc_data[] = [
                'id'          => $svc->ID,
                'name'        => $svc->post_title,
                'description' => get_the_excerpt( $svc ),
                'price'       => get_post_meta( $svc->ID, '_sb_price', true ),
                'duration'    => (int) get_post_meta( $svc->ID, '_sb_duration', true ),
                'slot_qty'    => (int) get_post_meta( $svc->ID, '_sb_slot_qty', true ) ?: 1,
                'break_time'  => (int) get_post_meta( $svc->ID, '_sb_buffer', true ) ?: 10,
                'thumb_url'   => $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'thumbnail' ) : '',
                'menu_order'  => $svc->menu_order,
            ];
        }
        $data_attrs .= " data-services='" . esc_attr( wp_json_encode( $svc_data ) ) . "'";

        $currency = apply_filters( 'sk_currency_symbol', '$' );
        $svc_cards_html = '';
        foreach ( $svc_data as $svc ) {
            $thumb = $svc['thumb_url']
                ? '<img src="' . esc_url( $svc['thumb_url'] ) . '" alt="' . esc_attr( $svc['name'] ) . '" class="sb-svc-thumb">'
                : '<div class="sb-svc-thumb" style="background:var(--sk-primary-lite)"></div>';
            $desc  = $svc['description'] ? '<span class="sb-svc-desc">' . esc_html( $svc['description'] ) . '</span>' : '';
            $price_raw = $svc['price'];
            $price = ( $price_raw !== '' && $price_raw !== null )
                ? '<span class="sb-svc-price">' . esc_html( $currency . $price_raw ) . '</span>' : '';
            $duration = $svc['duration'] ? '<span class="sb-svc-duration">' . esc_html( $svc['duration'] ) . ' min</span>' : '';
            $svc_cards_html .= '<div class="sb-service-card" data-id="' . esc_attr( $svc['id'] ) . '">';
            $svc_cards_html .= $thumb;
            $svc_cards_html .= '<div class="sb-svc-info"><span class="sb-svc-name">' . esc_html( $svc['name'] ) . '</span>' . $desc . '</div>';
            $svc_cards_html .= '<div class="sb-svc-meta">' . $price . $duration . '</div>';
            $svc_cards_html .= '</div>';
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

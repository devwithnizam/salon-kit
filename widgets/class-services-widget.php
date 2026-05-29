<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Services_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'salon_services_grid';
    }

    public function get_title() {
        return 'Salon Services Grid';
    }

    public function get_icon() {
        return 'dashicons-screenoptions';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    public function get_keywords() {
        return [ 'salon', 'services', 'grid', 'cards', 'salonkit' ];
    }

    protected function register_controls() {
        $this->content_section();
        $this->style_card_section();
        $this->style_typography_section();
        $this->style_spacing_section();
        $this->advanced_section();
    }

    private function content_section() {
        $this->start_controls_section( 'content_section', [
            'label' => 'Content',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'columns', [
            'label'   => 'Columns',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '1' => '1 Column',
                '2' => '2 Columns',
                '3' => '3 Columns',
                '4' => '4 Columns',
            ],
            'default' => '3',
        ] );

        $this->add_responsive_control( 'columns_mobile', [
            'label'   => 'Mobile Columns',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '1' => '1 Column',
                '2' => '2 Columns',
            ],
            'default' => '1',
            'condition' => [ 'columns!' => '1' ],
        ] );

        $this->add_control( 'show_price', [
            'label'        => 'Show Price',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Show',
            'label_off'    => 'Hide',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_duration', [
            'label'        => 'Show Duration',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_description', [
            'label'        => 'Show Description',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_image', [
            'label'        => 'Show Image',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'default'      => 'yes',
        ] );

        $this->add_control( 'max_items', [
            'label'   => 'Max Services',
            'type'    => \Elementor\Controls_Manager::NUMBER,
            'min'     => -1,
            'max'     => 50,
            'default' => 6,
            'description' => 'Set -1 to show all.',
        ] );

        $this->add_control( 'orderby', [
            'label'   => 'Order By',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'title'  => 'Title',
                'date'   => 'Date',
                'rand'   => 'Random',
                'menu_order' => 'Menu Order',
            ],
            'default' => 'title',
        ] );

        $this->add_control( 'order', [
            'label'   => 'Order Direction',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'ASC'  => 'Ascending',
                'DESC' => 'Descending',
            ],
            'default' => 'ASC',
        ] );

        $this->end_controls_section();
    }

    private function style_card_section() {
        $this->start_controls_section( 'style_card', [
            'label' => 'Card Style',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'card_bg', [
            'label'     => 'Card Background',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .sk-service-card' => 'background: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'card_border_color', [
            'label'     => 'Card Border Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#e2e8f0',
            'selectors' => [
                '{{WRAPPER}} .sk-service-card' => 'border-color: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'card_border_width', [
            'label'      => 'Card Border Width',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 5 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 1 ],
            'selectors'  => [
                '{{WRAPPER}} .sk-service-card' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
            ],
        ] );

        $this->add_control( 'card_radius', [
            'label'      => 'Card Border Radius',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 12 ],
            'selectors'  => [
                '{{WRAPPER}} .sk-service-card' => 'border-radius: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .sk-service-card img' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;',
            ],
        ] );

        $this->add_control( 'card_shadow', [
            'label'     => 'Card Shadow',
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
                'sm'   => '0 1px 2px 0 rgb(0 0 0 / 0.05)',
                'md'   => '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                'lg'   => '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
            ],
            'selectors' => [
                '{{WRAPPER}} .sk-service-card' => 'box-shadow: {{VALUE}};',
            ],
        ] );

        $this->add_control( 'card_hover_effect', [
            'label'     => 'Hover Effect',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => [
                'none'       => 'None',
                'lift'       => 'Lift Up',
                'shadow'     => 'Shadow Increase',
                'lift-shadow'=> 'Lift + Shadow',
            ],
            'default'   => 'lift-shadow',
        ] );

        $this->add_control( 'image_height', [
            'label'      => 'Image Height',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 400 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 180 ],
            'selectors'  => [
                '{{WRAPPER}} .sk-service-card img' => 'height: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [ 'show_image' => 'yes' ],
        ] );

        $this->add_control( 'image_fit', [
            'label'   => 'Image Fit',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'cover'   => 'Cover',
                'contain' => 'Contain',
                'fill'    => 'Fill',
            ],
            'default'   => 'cover',
            'selectors' => [
                '{{WRAPPER}} .sk-service-card img' => 'object-fit: {{VALUE}};',
            ],
            'condition' => [ 'show_image' => 'yes' ],
        ] );

        $this->end_controls_section();
    }

    private function style_typography_section() {
        $this->start_controls_section( 'style_typo', [
            'label' => 'Typography',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'name_typography',
                'label'    => 'Service Name',
                'selector' => '{{WRAPPER}} .sk-service-body h3',
            ]
        );

        $this->add_control( 'name_color', [
            'label'     => 'Name Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#6366f1',
            'selectors' => [
                '{{WRAPPER}} .sk-service-body h3' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'desc_typography',
                'label'    => 'Description',
                'selector' => '{{WRAPPER}} .sk-service-body p',
            ]
        );

        $this->add_control( 'desc_color', [
            'label'     => 'Description Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#64748b',
            'selectors' => [
                '{{WRAPPER}} .sk-service-body p' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'price_typography',
                'label'    => 'Price',
                'selector' => '{{WRAPPER}} .sk-service-footer .price',
            ]
        );

        $this->add_control( 'price_color', [
            'label'     => 'Price Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#6366f1',
            'selectors' => [
                '{{WRAPPER}} .sk-service-footer .price' => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name'     => 'duration_typography',
                'label'    => 'Duration',
                'selector' => '{{WRAPPER}} .sk-service-footer .duration',
            ]
        );

        $this->add_control( 'duration_color', [
            'label'     => 'Duration Color',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#64748b',
            'selectors' => [
                '{{WRAPPER}} .sk-service-footer .duration' => 'color: {{VALUE}};',
            ],
        ] );

        $this->end_controls_section();
    }

    private function style_spacing_section() {
        $this->start_controls_section( 'style_spacing', [
            'label' => 'Layout & Spacing',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ] );

        $this->add_responsive_control( 'grid_gap', [
            'label'      => 'Grid Gap',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 20 ],
            'selectors'  => [
                '{{WRAPPER}} .sk-services-grid' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'card_body_padding', [
            'label'      => 'Card Body Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'default'    => [ 'top' => 16, 'right' => 16, 'bottom' => 16, 'left' => 16, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sk-service-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'card_footer_padding', [
            'label'      => 'Card Footer Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px' ],
            'default'    => [ 'top' => 12, 'right' => 16, 'bottom' => 12, 'left' => 16, 'unit' => 'px' ],
            'selectors'  => [
                '{{WRAPPER}} .sk-service-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }

    private function advanced_section() {
        $this->start_controls_section( 'section_advanced', [
            'label' => 'Advanced',
            'tab'   => \Elementor\Controls_Manager::TAB_ADVANCED,
        ] );

        $this->add_control( 'css_classes', [
            'label'       => 'Additional CSS Classes',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
        ] );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $services = get_posts( [
            'post_type'      => 'salon_service',
            'posts_per_page' => (int) $settings['max_items'] ?: 6,
            'orderby'        => $settings['orderby'] ?? 'title',
            'order'          => $settings['order'] ?? 'ASC',
        ] );

        if ( empty( $services ) ) {
            echo '<p>No services found.</p>';
            return;
        }

        $cols = (int) $settings['columns'] ?: 3;

        $this->add_render_attribute( 'grid', 'class', 'sk-services-grid' );

        $classes = [ 'sk-services-grid' ];
        if ( ! empty( $settings['css_classes'] ) ) {
            $classes[] = esc_attr( $settings['css_classes'] );
        }

        // Hover effect via class
        $hover = $settings['card_hover_effect'] ?? 'lift-shadow';
        if ( $hover !== 'none' ) {
            $classes[] = 'sk-hover-' . $hover;
        }

        // Mobile columns
        $mobile_cols = $settings['columns_mobile'] ?? '1';
        if ( $mobile_cols !== '1' ) {
            $classes[] = 'sk-mobile-cols-' . $mobile_cols;
        }

        if ( ! wp_style_is( 'salon-kit-css', 'enqueued' ) ) {
            wp_enqueue_style( 'salon-kit-css' );
        }

        $style = "grid-template-columns: repeat($cols, 1fr);";
        echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '" style="' . $style . '">';

        foreach ( $services as $svc ) :
            $thumb_id  = get_post_thumbnail_id( $svc->ID );
            $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';
            $price     = get_post_meta( $svc->ID, '_sb_price', true );
            $duration  = (int) get_post_meta( $svc->ID, '_sb_duration', true );
            $desc      = get_the_excerpt( $svc );
            ?>
            <div class="sk-service-card">
                <?php if ( $settings['show_image'] !== 'no' && $thumb_url ) : ?>
                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $svc->post_title ); ?>">
                <?php endif; ?>
                <div class="sk-service-body">
                    <h3><?php echo esc_html( $svc->post_title ); ?></h3>
                    <?php if ( $settings['show_description'] !== 'no' && $desc ) : ?>
                        <p><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="sk-service-footer">
                    <?php if ( $settings['show_price'] !== 'no' && $price ) : ?>
                        <span class="price">$<?php echo esc_html( $price ); ?></span>
                    <?php else : ?>
                        <span></span>
                    <?php endif; ?>
                    <?php if ( $settings['show_duration'] !== 'no' && $duration ) : ?>
                        <span class="duration"><?php echo esc_html( $duration ); ?> min</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach;

        echo '</div>';
    }

    protected function content_template() {
        ?>
        <#
        var cols = settings.columns || 3;
        var style = 'grid-template-columns: repeat(' + cols + ', 1fr);';
        #>
        <div class="sk-services-grid" style="{{ style }}">
            <# for (var i = 0; i < 3; i++) { #>
            <div class="sk-service-card">
                <# if (settings.show_image !== 'no') { #>
                <div style="height:180px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:13px;">Image</div>
                <# } #>
                <div class="sk-service-body">
                    <h3>Service Name</h3>
                    <# if (settings.show_description !== 'no') { #><p>Short description of this service appears here.</p><# } #>
                </div>
                <div class="sk-service-footer">
                    <# if (settings.show_price !== 'no') { #><span class="price">$35</span><# } #>
                    <# if (settings.show_duration !== 'no') { #><span class="duration">45 min</span><# } #>
                </div>
            </div>
            <# } #>
        </div>
        <?php
    }
}

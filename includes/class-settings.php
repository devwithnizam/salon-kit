<?php
namespace SalonKit;

defined( 'ABSPATH' ) || exit;

class Settings {

    const OPTION = 'sk_email_settings';

    public static function init() {
        add_action( 'admin_menu', [ __CLASS__, 'add_submenu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
    }

    public static function defaults() {
        return [
            'customer_enabled'   => 'yes',
            'customer_subject'   => 'Booking Confirmed – {service_name}',
            'admin_enabled'      => 'yes',
            'admin_emails'       => get_option( 'admin_email' ),
            'admin_subject'      => 'New Booking: {service_name} – {client_name}',
            'from_name'          => get_bloginfo( 'name' ),
            'from_email'         => get_option( 'admin_email' ),
        ];
    }

    public static function get() {
        $saved = get_option( self::OPTION, [] );
        return wp_parse_args( $saved, self::defaults() );
    }

    public static function add_submenu() {
        add_submenu_page(
            'edit.php?post_type=salon_service',
            'Email Settings',
            'Email Settings',
            'manage_options',
            'sk-email-settings',
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function register_settings() {
        register_setting( 'sk_email_settings_group', self::OPTION, [ __CLASS__, 'sanitize' ] );
    }

    public static function sanitize( $input ) {
        $defaults = self::defaults();
        $output   = [];
        $output['customer_enabled'] = isset( $input['customer_enabled'] ) ? 'yes' : 'no';
        $output['customer_subject'] = sanitize_text_field( $input['customer_subject'] ?? $defaults['customer_subject'] );
        $output['admin_enabled']    = isset( $input['admin_enabled'] ) ? 'yes' : 'no';
        $output['admin_emails']     = sanitize_text_field( $input['admin_emails'] ?? $defaults['admin_emails'] );
        $output['admin_subject']    = sanitize_text_field( $input['admin_subject'] ?? $defaults['admin_subject'] );
        $output['from_name']        = sanitize_text_field( $input['from_name'] ?? $defaults['from_name'] );
        $output['from_email']       = sanitize_email( $input['from_email'] ?? $defaults['from_email'] );
        return $output;
    }

    public static function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        $settings = self::get();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'SalonKit — Booking System', 'salon-kit' ); ?></h1>

            <div class="notice notice-info" style="padding:12px 20px;margin:10px 0 20px;">
                <h2 style="margin:0 0 10px;font-size:1.3em;">How to Use SalonKit</h2>

                <h3 style="margin:16px 0 4px;">1. Add Services</h3>
                <p>Go to <strong>SalonKit → Services</strong> → Add New. Set the name, price, duration, slot capacity, and availability schedule for each service. You can also upload a thumbnail image.</p>

                <h3 style="margin:16px 0 4px;">2. Display the Booking Form</h3>
                <p><strong>Shortcode:</strong> Use <code>[salon_booking]</code> on any page or post.</p>
                <p><strong>Elementor:</strong> Drag the "Salon Booking Form" widget into any page built with Elementor. All text, colors, visibility, and typography are customizable from the Elementor panel.</p>

                <h3 style="margin:16px 0 4px;">3. Display Services (Standalone Grid)</h3>
                <p>Use the <strong>"Salon Services Grid"</strong> Elementor widget to show service cards anywhere on your site. Each card can have a "Book Now" button that links to the booking form with the service pre-selected.</p>

                <h3 style="margin:16px 0 4px;">4. URL Auto-Selection (Book Now Buttons)</h3>
                <p>When a "Book Now" button is clicked, the service is automatically selected in the booking form. Two modes:</p>
                <ul style="margin:4px 0 4px 20px;list-style:disc;">
                    <li><strong>Same page:</strong> Leave the "Booking Page URL" empty in the Services Grid widget. Clicking "Book Now" scrolls to the booking form on the same page and selects the service.</li>
                    <li><strong>Cross page:</strong> Set a "Booking Page URL" in the Services Grid widget. Clicking navigates to that page with <code>?sk_service=SERVICE_ID</code> in the URL and the service is auto-selected.</li>
                </ul>
                <p>You can also link manually: <code>/your-booking-page/?sk_service=42</code> or use the hash <code>#booking?sk_service=42</code>.</p>

                <h3 style="margin:16px 0 4px;">5. Customization</h3>
                <p>Use the <code>sk_currency_symbol</code> filter to change the currency symbol. Example:</p>
                <p><code>add_filter( 'sk_currency_symbol', function() { return '€'; } );</code></p>

                <p style="margin-top:14px;">Manage email notifications using the settings below.</p>
            </div>

            <h2 style="margin-top:24px;"><?php esc_html_e( 'Email Settings', 'salon-kit' ); ?></h2>
            <form method="post" action="options.php">
                <?php settings_fields( 'sk_email_settings_group' ); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'From Name', 'salon-kit' ); ?></th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( self::OPTION ); ?>[from_name]"
                                value="<?php echo esc_attr( $settings['from_name'] ); ?>" class="regular-text">
                            <p class="description">Name shown as the email sender.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'From Email', 'salon-kit' ); ?></th>
                        <td>
                            <input type="email" name="<?php echo esc_attr( self::OPTION ); ?>[from_email]"
                                value="<?php echo esc_attr( $settings['from_email'] ); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>

                <h2 class="title"><?php esc_html_e( 'Customer Confirmation Email', 'salon-kit' ); ?></h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable', 'salon-kit' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr( self::OPTION ); ?>[customer_enabled]" value="yes"
                                    <?php checked( $settings['customer_enabled'], 'yes' ); ?>>
                                Send confirmation email to customers
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Subject', 'salon-kit' ); ?></th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( self::OPTION ); ?>[customer_subject]"
                                value="<?php echo esc_attr( $settings['customer_subject'] ); ?>" class="regular-text">
                            <p class="description">
                                Available tags: <code>{client_name}</code> <code>{service_name}</code>
                                <code>{professional_name}</code> <code>{booking_date}</code> <code>{booking_time}</code>
                                <code>{booking_id}</code>
                            </p>
                        </td>
                    </tr>
                </table>

                <h2 class="title"><?php esc_html_e( 'Admin Notification Email', 'salon-kit' ); ?></h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable', 'salon-kit' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr( self::OPTION ); ?>[admin_enabled]" value="yes"
                                    <?php checked( $settings['admin_enabled'], 'yes' ); ?>>
                                Send notification to admin(s) on new booking
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Recipients', 'salon-kit' ); ?></th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( self::OPTION ); ?>[admin_emails]"
                                value="<?php echo esc_attr( $settings['admin_emails'] ); ?>" class="regular-text">
                            <p class="description">Comma-separated email addresses. Default: <code><?php echo esc_html( get_option( 'admin_email' ) ); ?></code></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Subject', 'salon-kit' ); ?></th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( self::OPTION ); ?>[admin_subject]"
                                value="<?php echo esc_attr( $settings['admin_subject'] ); ?>" class="regular-text">
                            <p class="description">Same tags as customer subject.</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <style>
                .wrap .title { margin: 24px 0 12px; padding: 9px 0 4px; border-top: 1px solid #c3c4c7; }
                .wrap .form-table th { width: 160px; }
            </style>
        </div>
        <?php
    }
}

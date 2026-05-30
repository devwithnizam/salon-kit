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
            'How to Use',
            'How to Use',
            'manage_options',
            'sk-how-to-use',
            [ __CLASS__, 'render_help_page' ]
        );

        add_submenu_page(
            'edit.php?post_type=salon_service',
            'Email Settings',
            'Email Settings',
            'manage_options',
            'sk-email-settings',
            [ __CLASS__, 'render_page' ]
        );
    }

    public static function render_help_page() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'How to Use SalonKit', 'salon-kit' ); ?></h1>

            <style>
                .sk-help { max-width: 800px; }
                .sk-help h2 { font-size: 1.4em; margin: 28px 0 8px; padding-bottom: 6px; border-bottom: 1px solid #c3c4c7; }
                .sk-help h3 { font-size: 1.1em; margin: 18px 0 4px; }
                .sk-help p, .sk-help li { font-size: 13px; line-height: 1.6; }
                .sk-help ul { margin: 4px 0 12px 20px; list-style: disc; }
                .sk-help code { background: #f0f0f1; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
                .sk-help .sk-help-box { background: #f0f6fc; border: 1px solid #c5d9ed; border-radius: 4px; padding: 14px 18px; margin: 12px 0; }
                .sk-help .sk-help-box code { background: #e2edf7; }
            </style>

            <div class="sk-help">

                <h2>1. Adding Services</h2>
                <p>Go to <strong>SalonKit → Services</strong> → <strong>Add New</strong>. Each service can have:</p>
                <ul>
                    <li><strong>Title &amp; Description</strong> — Name and short excerpt</li>
                    <li><strong>Price</strong> — e.g. <code>45</code> (currency symbol appears automatically)</li>
                    <li><strong>Duration</strong> — e.g. <code>60</code> for 60 minutes</li>
                    <li><strong>Slot Capacity</strong> — how many clients can book the same time slot (default: 1)</li>
                    <li><strong>Slot Interval</strong> — minutes between each available time slot (default: 30)</li>
                    <li><strong>Break Time</strong> — padding between bookings (default: 10 min)</li>
                    <li><strong>Availability Schedule</strong> — which days/hours the service is offered</li>
                    <li><strong>Featured Image</strong> — optional thumbnail</li>
                </ul>

                <h2>2. Display the Booking Form</h2>
                <h3>Option A: Shortcode</h3>
                <p>Place <code>[salon_booking]</code> on any page or post. The form renders as a 4-step wizard (Service → Date → Time → Details).</p>

                <h3>Option B: Elementor Widget</h3>
                <p>Drag the <strong>"Salon Booking Form"</strong> widget into any Elementor page. Customize all text, colors, typography, visibility, and spacing directly from the Elementor panel — no coding needed.</p>

                <div class="sk-help-box">
                    <strong>Customization tip:</strong> You can hide any step or field using the <strong>Visibility</strong> section in the Elementor widget settings. For example, disable the phone field or hide the summary bar.
                </div>

                <h2>3. Display Services as a Grid</h2>
                <p>Use the <strong>"Salon Services Grid"</strong> Elementor widget to showcase your services anywhere on the site. It displays service cards with image, name, description, price, and duration.</p>
                <p>Each card can have a <strong>"Book Now"</strong> button (enabled by default). Configure it in the widget's Content panel:</p>
                <ul>
                    <li><strong>Show Book Now Button</strong> — toggle on/off</li>
                    <li><strong>Button Text</strong> — customize the label (default: "Book Now")</li>
                    <li><strong>Booking Page URL</strong> — controls how the button behaves (see next section)</li>
                </ul>

                <h2>4. URL Auto-Selection (Book Now Feature)</h2>
                <p>When a "Book Now" button is clicked, the service is automatically pre-selected in the booking form. Two modes available:</p>

                <h3>Same-Page Mode (no URL entered)</h3>
                <p>Leave the <strong>"Booking Page URL"</strong> empty. Clicking "Book Now" will:</p>
                <ol style="margin:4px 0 12px 20px;list-style:decimal;">
                    <li>Set the URL hash to <code>#booking?sk_service=SERVICE_ID</code></li>
                    <li>Smooth-scroll to the booking form on the same page</li>
                    <li>Auto-select the service (user stays on Step 1, "Next" button becomes active)</li>
                </ol>

                <h3>Cross-Page Mode (URL entered)</h3>
                <p>Enter your booking page URL in the <strong>"Booking Page URL"</strong> field. Clicking "Book Now" navigates to that page with <code>?sk_service=SERVICE_ID</code> appended, and the service is auto-selected on load.</p>

                <h3>Manual Linking</h3>
                <p>You can link to the booking form from anywhere using these formats:</p>
                <ul>
                    <li>Query parameter: <code>/booking-page/?sk_service=42</code></li>
                    <li>URL hash (same-page): <code>#booking?sk_service=42</code></li>
                </ul>

                <div class="sk-help-box">
                    <strong>Note:</strong> The service ID is the WordPress post ID. You can find it in the URL when editing a service (e.g., <code>post=42</code>).
                </div>

                <h2>5. Managing Bookings</h2>
                <p>All bookings appear under <strong>SalonKit → Bookings</strong>. Each row shows the client name, email, service, date, time, price, and status. The status can be <strong>confirmed</strong>, <strong>pending</strong>, or <strong>cancelled</strong>.</p>
                <p>A <strong>Today's Bookings</strong> dashboard widget shows upcoming appointments for the current day.</p>

                <h2>6. Email Notifications</h2>
                <p>Configure email settings under <strong>SalonKit → Email Settings</strong>. You can:</p>
                <ul>
                    <li>Set the sender name and email address</li>
                    <li>Enable/disable customer confirmation emails</li>
                    <li>Enable/disable admin notification emails</li>
                    <li>Customize subject lines using available tags: <code>{client_name}</code>, <code>{service_name}</code>, <code>{booking_date}</code>, <code>{booking_time}</code>, <code>{booking_id}</code></li>
                </ul>

                <h2>7. Developer Hooks</h2>
                <table class="wp-list-table widefat striped" style="margin-top:6px;">
                    <thead><tr><th>Hook</th><th>Type</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td><code>sk_currency_symbol</code></td><td>filter</td><td>Change the currency symbol. Default: <code>$</code></td></tr>
                    </tbody>
                </table>
                <p style="margin-top:6px;">Example: <code>add_filter( 'sk_currency_symbol', function() { return '€'; } );</code></p>

                <h2>8. Troubleshooting</h2>
                <ul>
                    <li><strong>No services show in the form?</strong> — Make sure you've published at least one service with a price and schedule.</li>
                    <li><strong>No time slots available?</strong> — Check the service's availability schedule and ensure today isn't blocked by a date exception.</li>
                    <li><strong>Book Now button does nothing?</strong> — Ensure the booking form widget or <code>[salon_booking]</code> shortcode exists on the page (for same-page mode) or that the booking page URL is correct (for cross-page mode).</li>
                    <li><strong>Icon controls missing in Elementor?</strong> — The custom icon library was removed in v2.2. The form uses clean inline SVGs instead.</li>
                    <li><strong>Auto-selection not working?</strong> — Verify the service ID in the URL exists and is published.</li>
                </ul>

            </div>
        </div>
        <?php
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

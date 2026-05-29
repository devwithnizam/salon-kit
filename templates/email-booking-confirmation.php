<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body, table, td, p { margin: 0; padding: 0; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background: #f4f6f9;
    color: #1e293b;
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
  }
  .email-wrapper { padding: 32px 16px; }
  .email-container {
    max-width: 560px;
    margin: 0 auto;
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
  }
  .email-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    padding: 36px 32px 28px;
    text-align: center;
  }
  .email-header h1 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.3px;
  }
  .email-header p {
    margin: 6px 0 0;
    color: rgba(255,255,255,0.85);
    font-size: 14px;
  }
  .email-body { padding: 32px; }
  .email-body h2 {
    font-size: 18px;
    margin: 0 0 4px;
    color: #1e293b;
  }
  .email-body .greeting {
    font-size: 15px;
    color: #475569;
    margin-bottom: 24px;
  }
  .booking-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
  }
  .booking-card table { width: 100%; border-collapse: collapse; }
  .booking-card td {
    padding: 8px 0;
    font-size: 14px;
    border-bottom: 1px solid #eef2f6;
  }
  .booking-card tr:last-child td { border-bottom: none; }
  .booking-card .label {
    color: #64748b;
    font-weight: 500;
    width: 100px;
    vertical-align: top;
  }
  .booking-card .value {
    color: #1e293b;
    font-weight: 600;
  }
  .booking-id-badge {
    display: inline-block;
    background: #eef2ff;
    color: #4f46e5;
    font-weight: 700;
    font-size: 16px;
    padding: 4px 14px;
    border-radius: 6px;
    letter-spacing: 0.5px;
  }
  .email-footer {
    padding: 24px 32px;
    text-align: center;
    border-top: 1px solid #e2e8f0;
    font-size: 12px;
    color: #94a3b8;
  }
  .email-footer a { color: #4f46e5; text-decoration: none; }
  @media (max-width: 480px) {
    .email-body { padding: 20px; }
    .booking-card { padding: 16px; }
    .booking-card .label { display: block; width: auto; }
  }
</style>
</head>
<body>
<div class="email-wrapper">
  <div class="email-container">
    <div class="email-header">
      <h1><?php echo esc_html( $from_name ); ?></h1>
      <p><?php esc_html_e( 'Booking Confirmation', 'salon-kit' ); ?></p>
    </div>
    <div class="email-body">
      <h2><?php echo esc_html( $heading ); ?></h2>
      <p class="greeting"><?php echo esc_html( $greeting ); ?></p>

      <div class="booking-card">
        <table cellpadding="0" cellspacing="0">
          <tr>
            <td class="label"><?php esc_html_e( 'Booking ID', 'salon-kit' ); ?></td>
            <td class="value"><span class="booking-id-badge"><?php echo esc_html( $booking_id_display ); ?></span></td>
          </tr>
          <tr>
            <td class="label"><?php esc_html_e( 'Service', 'salon-kit' ); ?></td>
            <td class="value"><?php echo esc_html( $service_name ); ?></td>
          </tr>
          <tr>
            <td class="label"><?php esc_html_e( 'Date', 'salon-kit' ); ?></td>
            <td class="value"><?php echo esc_html( $booking_date ); ?></td>
          </tr>
          <tr>
            <td class="label"><?php esc_html_e( 'Time', 'salon-kit' ); ?></td>
            <td class="value"><?php echo esc_html( $booking_time ); ?></td>
          </tr>
          <?php if ( ! empty( $client_name ) ) : ?>
          <tr>
            <td class="label"><?php esc_html_e( 'Client', 'salon-kit' ); ?></td>
            <td class="value"><?php echo esc_html( $client_name ); ?></td>
          </tr>
          <?php endif; ?>
          <?php if ( ! empty( $notes ) ) : ?>
          <tr>
            <td class="label"><?php esc_html_e( 'Notes', 'salon-kit' ); ?></td>
            <td class="value"><?php echo nl2br( esc_html( $notes ) ); ?></td>
          </tr>
          <?php endif; ?>
        </table>
      </div>

      <p style="font-size:13px;color:#64748b;text-align:center;">
        <?php esc_html_e( 'If you have any questions, please contact us.', 'salon-kit' ); ?>
      </p>
    </div>
    <div class="email-footer">
      &copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( $from_name ); ?><br>
    </div>
  </div>
</div>
</body>
</html>

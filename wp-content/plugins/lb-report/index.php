<?php
/**
 * Plugin Name: Report emails
 * Description: Käsitööturg custom extension that sends report e-mails to merchants
 * Version: 1.0
 */


class lbReport{

	function __construct(){

        register_activation_hook( __FILE__, [$this, 'schedule_cron'] );
        add_action( 'lb_send_report_emails', [$this, 'send_report_emails'] );
        register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
        
    }

    function deactivate() {
        wp_clear_scheduled_hook( 'lb_send_report_emails' );
    }

    function schedule_cron(){

        $timestamp = wp_next_scheduled( 'lb_send_report_emails' );

        if( $timestamp == false ){
            //Schedule the event for right now, then to repeat daily
            wp_schedule_event( 1449285028, 'daily', 'lb_send_report_emails' );
        }
    }

    // TODO: properly test this plugin on a dev mail() server with shop data
    function send_report_emails(){

        // Check if it's the beginning of the month
        if( date('j') == 1 ){

            // Get all shop owners
            $args = array(
                'role' => 'seller'
            ); 
            $users = get_users( $args );

            if( $users ){

                foreach($users as $user){
                    $this->mail($user->ID);
                }

            }

        }        
        
    }

    private function mail($user_id){
        
        require_once( DOKAN_INC_DIR . '/pro/includes/reports.php' );

        $email_heading = __('Käsitööturg monthly sales report', 'ktt');

        $admin_email = get_option('admin_email');

        $headers = "From: Käsitööturg\r\n";
        $headers .= "Reply-To: ".$admin_email."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        ob_start();

        // TODO: proper HTML e-mail design
        include('email/email-header.php');
        include('email/content.php');
        include('email/email-footer.php');

        $message = ob_get_clean();

        return mail($to, $email_heading, $message, $headers);

    }

    static function modified_dokan_report_sales_overview( $start_date, $end_date ) {
        global $woocommerce, $wpdb, $wp_locale, $current_user;

        $total_sales = $total_orders = $order_items = $discount_total = $shipping_total = 0;

        $order_totals = dokan_get_order_report_data( array(
            'data' => array(
                '_order_total' => array(
                    'type'     => 'meta',
                    'function' => 'SUM',
                    'name'     => 'total_sales'
                ),
                '_order_shipping' => array(
                    'type'     => 'meta',
                    'function' => 'SUM',
                    'name'     => 'total_shipping'
                ),
                'ID' => array(
                    'type'     => 'post_data',
                    'function' => 'COUNT',
                    'name'     => 'total_orders'
                )
            ),
            'filter_range' => true,
            // 'debug' => true
        ), $start_date, $end_date );

        $total_sales    = $order_totals->total_sales;
        $total_shipping = $order_totals->total_shipping;
        $total_orders   = absint( $order_totals->total_orders );
        $total_items    = absint( dokan_get_order_report_data( array(
            'data' => array(
                '_qty' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => 'SUM',
                    'name'            => 'order_item_qty'
                )
            ),
            'query_type' => 'get_var',
            'filter_range' => true
        ), $start_date, $end_date ) );

        // Get discount amounts in range
        $total_coupons = dokan_get_order_report_data( array(
            'data' => array(
                'discount_amount' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'coupon',
                    'function'        => 'SUM',
                    'name'            => 'discount_amount'
                )
            ),
            'where' => array(
                array(
                    'key'      => 'order_item_type',
                    'value'    => 'coupon',
                    'operator' => '='
                )
            ),
            'query_type' => 'get_var',
            'filter_range' => true
        ), $start_date, $end_date );

        $average_sales = $total_sales / ( 30 + 1 );

        $legend = apply_filters( 'dokan-seller-dashboard-reports-left-sidebar', array(
            'sales_in_this_period' => array(
                'title' => sprintf( __( '%s sales in this period', 'dokan' ), '<strong>' . wc_price( $total_sales ) . '</strong>' ),
            ),

            'average_daily_sales' => array(
                'title' => sprintf( __( '%s average daily sales', 'dokan' ), '<strong>' . wc_price( $average_sales ) . '</strong>' ),
            ),

            'orders_placed' => array(
                'title' => sprintf( __( '%s orders placed', 'dokan' ), '<strong>' . $total_orders . '</strong>' ),
            ),

            'items_purchased' => array(
                'title' => sprintf( __( '%s items purchased', 'dokan' ), '<strong>' . $total_items . '</strong>' ),
            ),

            'charged_for_shipping' => array(
                'title' => sprintf( __( '%s charged for shipping', 'dokan' ), '<strong>' . wc_price( $total_shipping ) . '</strong>' ),
            ),

            'worth_of_coupons_used' => array(
                'title' => sprintf( __( '%s worth of coupons used', 'dokan' ), '<strong>' . wc_price( $total_coupons ) . '</strong>' ),
            ),
        ) );
        ?>

        <ul class="chart-legend">
            <?php foreach ($legend as $item) {
                printf( '<li>%s</li>', $item['title'] );
            } ?>
        </ul>
            
        <?php
    }

}

new lbReport();
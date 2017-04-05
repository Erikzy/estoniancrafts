<?php
/**
 * Plugin Name: Homepage advertise products
 * Description: Käsitööturg custom extension for displaying products on the front page
 * Version: 1.0
 */


class lbAdvert{

    private $advertsPerDay = 5; // TODO: adjust this

	function __construct(){

        add_action( 'wp_enqueue_scripts', [$this, 'register_scripts'], 15 );
        add_action( 'dokan_product_edit_after_options', [$this, 'display_date_selector'] );

        add_filter( 'woocommerce_add_cart_item_data', [$this, 'add_cart_item'], 10, 2 );
        add_action( 'woocommerce_add_to_cart_validation', [$this, 'validate_add_to_cart'], 1, 5 );
        
        add_filter( 'woocommerce_get_item_data', [$this, 'render_meta_on_cart_and_checkout'], 10, 2 );
        add_action( 'woocommerce_add_order_item_meta', [$this, 'add_order_meta'], 10, 3 );

        add_action( 'woocommerce_payment_complete', [$this, 'order_complete'] );
        add_action( 'woocommerce_order_status_completed', [$this, 'order_complete'] );

        add_action( 'admin_init', [$this, 'settings_api_init'] );

    }

    function register_scripts(){
        global $wp_scripts;

        if (!is_admin()) {
            wp_enqueue_style(  'lb-advert', plugin_dir_url( __FILE__ ) . 'css/pikaday.css', false,'1.0','all');
            wp_enqueue_script( 'lb-moment', plugin_dir_url( __FILE__ ) . 'js/moment.js', false,'1.0','all');
            wp_enqueue_script( 'lb-pikaday', plugin_dir_url( __FILE__ ) . 'js/pikaday.js', false,'1.0','all');
            wp_enqueue_script( 'lb-advert', plugin_dir_url( __FILE__ ) . 'js/lbadvert.js', false,'1.0','all');
        }

    }
    
    function display_date_selector(){
        global $post, $wpdb;

        ?>

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Frontpage', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
                <div class="dokan-form-group">

                    <?php 

                    // Fetch days that are already full and disable them from                    
                    $query = "SELECT COUNT(*) as date_count, display_date FROM ".$wpdb->prefix."lb_advert GROUP BY display_date";
                    $items = $wpdb->get_results($query, ARRAY_A);

                    $full_dates = [];

                    if($items){

                        foreach ($items as $item) {

                            if($item['date_count'] >= $this->advertsPerDay){
                                $full_dates[] = $item['display_date'];
                            }

                        }

                    }

                    $query = "SELECT display_date FROM ".$wpdb->prefix."lb_advert WHERE product_id = ".(int)$post->ID;
                    $items = $wpdb->get_results($query, ARRAY_A);
                    if($items){

                        foreach ($items as $item) {

                            $full_dates[] = $item['display_date'];
                            
                        }

                    }

                    $full_dates = array_values($full_dates);

                    ?>

                    <script>
                        var lb_advert_disabled = <?php echo json_encode($full_dates) ?>;
                    </script>
                    
                    <label class="form-label" for="lb-datepicker"><?php _e( 'Frontpage date', 'ktt' ); ?></label>

                    <input type="text" id="lb-datepicker" placeholder="<?php _e('Date when to show this product on the front page', 'ktt'); ?>">

                    <a href="<?= site_url() ?>?add-to-cart=<?= get_option( 'lb_ad_product_id' ) ?>&lb-advert-date=01.10.2017&lb-product=<?= $post->ID ?>" target="_blank" id="lb-advert-buy" data-baseurl="<?= site_url() ?>?add-to-cart=<?= get_option( 'lb_ad_product_id' ) ?>&lb-product=<?= $post->ID ?>" style="display: none;margin-top:1em;"><?= __( 'Add to front page', 'ktt' ) ?> - 10€</a>
                </div>

            </div>
        </div><!-- .lb-dokan-options -->

        <?php

    }


    // Validate adding to cart
    // Same product once per day
    // Respect max number of ads per day
    function validate_add_to_cart( $passed, $product_id, $quantity, $variation_id = 0, $variations = [] ) {
        global $woocommerce, $wpdb;

        if( $product_id == get_option( 'lb_ad_product_id' ) ){

            $date = date( 'Y-m-d', strtotime($_GET['lb-advert-date']) );

            $q = $wpdb->prepare( "SELECT COUNT(*) FROM ".$wpdb->prefix."lb_advert WHERE product_id = %d AND display_date = %s", 
                [ 
                    $_GET['lb-product'], 
                    $date
                ]
            );
        
            if($wpdb->get_var($q)){

                wc_add_notice(  __( "The product is already featured on this date. Choose another.", "ktt" ) ,'error' );
                return false;

            }

            $q = $wpdb->prepare( "SELECT COUNT(*) FROM ".$wpdb->prefix."lb_advert WHERE display_date = %s", 
                [
                    $date
                ]
            );

            if($wpdb->get_var($q) >= $this->advertsPerDay){

                wc_add_notice(  __( "The date is already full. Choose another.", "ktt" ) ,'error' );
                return false;

            }            

        }

        return true;

    }

    // Add custom metadata to lbAdvert cart item
    function add_cart_item( $cart_item_meta, $product_id ) {
        global $woocommerce;

        // If the custom name field is checked
        if( isset($_GET['lb-advert-date']) ){

            $cart_item_meta['lb-advert-date'] = sanitize_text_field($_GET['lb-advert-date']);
            $cart_item_meta['lb-product'] = (int)$_GET['lb-product'];
            // var_dump($cart_item_meta);die();
            return $cart_item_meta; 

        }

    }

    function render_meta_on_cart_and_checkout( $cart_data, $cart_item ) {
        $custom_items = array();
        // Woo 2.4.2 updates
        if( !empty( $cart_data ) ) {
            $custom_items = $cart_data;
        }
        if( isset($cart_item['lb-advert-date']) ){
            $custom_items[] = array( "name" => __( 'Display date', 'ktt' ), "value" => $cart_item['lb-advert-date'] );
            $custom_items[] = array( "name" => __( 'Product id', 'ktt' ), "value" => $cart_item['lb-product'] );
        }
        return $custom_items;
    }

    function add_order_meta ( $itemId, $values, $key ) {
        if ( isset( $values['lb-advert-date'] ) && isset( $values['lb-product'] ) ) {
            wc_add_order_item_meta( $itemId, 'lb_advert_date', $values['lb-advert-date'] );
            wc_add_order_item_meta( $itemId, 'lb_advert_product', $values['lb-product'] );
        }
    }

    // Add to advertisement table
    function order_complete( $order_id ) {

        $order = new WC_Order( $order_id );
        $order_item = $order->get_items();

        foreach( $order_item as $product ) {

            if( $product['product_id'] == get_option( 'lb_ad_product_id' ) ){
                $this->add_to_advert_queue($product['item_meta']['lb_advert_product'][0], $order_id, $product['item_meta']['lb_advert_date'][0] );
            }
            
        }

        error_log( "Payment has been received for order $order_id", 0 );
    }

    function add_to_advert_queue( $product_id, $order_id, $date ){
        global $wpdb;

        $display_date = date('Y-m-d', strtotime($date));

        $wpdb->insert($wpdb->prefix . 'lb_advert', array(
                        'product_id' => (int)$product_id,
                        'order_id' => (int)$order_id,
                        'display_date' => $display_date,
                        'date_added' => date('Y-m-d H:i:s', time())
                    ));

    }

    static function display(){
        global $wpdb;

        $date = date( 'Y-m-d', time() );

        $q = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."lb_advert WHERE display_date = %s", 
            [
                $date
            ]
        );

        $results = $wpdb->get_results($q, ARRAY_A);
        
        if($results){

            $ids = [];

            foreach( $results as $result ){
                $ids[] = $result['product_id'];
            }
            
            echo do_shortcode('[products ids="'.implode(', ', $ids).'"]');
        }

    }


    /**
     * Settings page data inputs
     */
    function settings_api_init() {
        // Add the section to reading settings so we can add our
        // fields to it
        add_settings_section(
            'lb_setting_section',
            'Paid products for shopowners',
            '',
            'reading'
        );

        // Add the field with the names and function to use for our new
        // settings, put it in our new section
        add_settings_field(
            'lb_ad_product_id',
            'Frontpage ad product ID',
            [$this, 'ad_id_callback_function'],
            'reading',
            'lb_setting_section'
        );

        add_settings_field(
            'lb_bcard_product_id',
            'Businesscard product ID',
            [$this, 'bcard_id_callback_function'],
            'reading',
            'lb_setting_section'
        );

        // Register our setting so that $_POST handling is done for us and
        // our callback function just has to echo the <input>
        register_setting( 'reading', 'lb_ad_product_id' );
        register_setting( 'reading', 'lb_bcard_product_id' );
    }

    function setting_section_callback_function() {
        echo '<p>Paid products for shopowners</p>';
    }

    function ad_id_callback_function() {
        echo '<input name="lb_ad_product_id" id="lb_ad_product_id" type="number" value="'.get_option( 'lb_ad_product_id' ).'" />';
    }

    function bcard_id_callback_function() {
        echo '<input name="lb_bcard_product_id" id="lb_bcard_product_id" type="number" value="'.get_option( 'lb_bcard_product_id' ).'" />';
    }

}

new lbAdvert();
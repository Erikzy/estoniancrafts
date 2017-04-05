<?php
/**
 * Plugin Name: Homepage on sale products
 * Description: Käsitööturg custom extension for displaying on sale products on the front page
 * Version: 1.0
 */


class lbSale{

    public static $scheduled_ids = [];

	function __construct(){

        register_activation_hook( __FILE__, [$this, 'schedule_cron'] );
        add_action( 'lb_sale_recalculate', [$this, 'recalculate_sale_status'] );

        add_action( 'dokan_process_product_meta', [$this, 'save_product'], 99 );
        add_action( 'publish_post', [$this, 'save_global'], 99 ); 
        add_action( 'woocommerce_scheduled_sales', [$this, 'before_wc_scheduled_sales'], 1 );
        add_action( 'woocommerce_scheduled_sales', [$this, 'after_wc_scheduled_sales'], 99 );
        register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
        
    }

    function deactivate() {
        wp_clear_scheduled_hook( 'lb_sale_recalculate' );
    }

    function schedule_cron(){

        $timestamp = wp_next_scheduled( 'lb_sale_recalculate' );

        if( $timestamp == false ){
            //Schedule the event for right now, then to repeat daily
            wp_schedule_event( 1449285028, 'daily', 'lb_sale_recalculate' );
        }
    }

    function save_global($product_id){

        if( is_admin() && ! ( wp_is_post_revision( $product_id) || wp_is_post_autosave( $product_id ) ) ) {
            $this->save_product($product_id);
        }

    }

    function save_product($product_id){
        
        if( ! ( wp_is_post_revision( $product_id) || wp_is_post_autosave( $product_id ) ) ) {

            // Check if regular price is different from last price change
            $last_change = $this->get_last_price_change($product_id);
            $current_price = get_post_meta($product_id, '_regular_price', true);

            if( $last_change['regular_price'] != $current_price ){
                                
                // Add new pricechange
                add_post_meta( $product_id, '_lb_price_change', ['regular_price' => $current_price, 'date' => date('Y-m-d H:i:s') ]);

            }

            $is_on_sale = $this->is_valid_for_sale($product_id);
            update_post_meta( $product_id, '_lb_sale', (int)$is_on_sale);

        }

    }

    function get_last_price_change($product_id){

        // _lb_price_change = ['regular_price' => 44, 'date' => Y-m-d H:i:s]
        $last_change = ['regular_price' => -999, 'date' => '1970-01-01 00:00:00'];

        $price_changes = get_post_meta($product_id, '_lb_price_change');

        if( is_array($price_changes) ){
            $last_change = array_pop($price_changes);
        }

        return $last_change;

    }

    function is_valid_for_sale($product_id){

        // Check if _price is more than -30% of _regular_price
        $current_price = get_post_meta($product_id, '_price', true);
        $regular_price = get_post_meta($product_id, '_regular_price', true);
        if( $current_price <= $regular_price * 0.70 ){

            // Is 30 days from last regular_price change?
            $last_change = $this->get_last_price_change($product_id);

            // if( time() - strtotime($last_change['date']) >= 60*60*24*30 ){
            if( time() - strtotime($last_change['date']) >= 30 ){ // TODO: change it back to 30 days!

                return true;

            }

        }

        return false;

    }

    // Called to recalculate _lb_sale status on products with scheduled sales
    function before_wc_scheduled_sales() {
        global $wpdb;

        // Sales which are due to start
        $product_ids = $wpdb->get_col( $wpdb->prepare( "
            SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
            LEFT JOIN {$wpdb->postmeta} as postmeta_2 ON postmeta.post_id = postmeta_2.post_id
            LEFT JOIN {$wpdb->postmeta} as postmeta_3 ON postmeta.post_id = postmeta_3.post_id
            WHERE postmeta.meta_key = '_sale_price_dates_from'
            AND postmeta_2.meta_key = '_price'
            AND postmeta_3.meta_key = '_sale_price'
            AND postmeta.meta_value > 0
            AND postmeta.meta_value < %s
            AND postmeta_2.meta_value != postmeta_3.meta_value
        ", current_time( 'timestamp' ) ) );

        if ( $product_ids ) {
            self::$scheduled_ids = array_merge(self::$scheduled_ids, $product_ids);
        }

        // Sales which are due to end
        $product_ids = $wpdb->get_col( $wpdb->prepare( "
            SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
            LEFT JOIN {$wpdb->postmeta} as postmeta_2 ON postmeta.post_id = postmeta_2.post_id
            LEFT JOIN {$wpdb->postmeta} as postmeta_3 ON postmeta.post_id = postmeta_3.post_id
            WHERE postmeta.meta_key = '_sale_price_dates_to'
            AND postmeta_2.meta_key = '_price'
            AND postmeta_3.meta_key = '_regular_price'
            AND postmeta.meta_value > 0
            AND postmeta.meta_value < %s
            AND postmeta_2.meta_value != postmeta_3.meta_value
        ", current_time( 'timestamp' ) ) );

        if ( $product_ids ) {
            self::$scheduled_ids = array_merge(self::$scheduled_ids, $product_ids);
        }

        self::$scheduled_ids = array_unique(self::$scheduled_ids);

    }

    function after_wc_scheduled_sales() {
        global $wpdb;

        if ( count(self::$scheduled_ids) ) {

            foreach (self::$scheduled_ids as $product_id) {

                $is_on_sale = $this->is_valid_for_sale($product_id);
                update_post_meta( $product_id, '_lb_sale', (int)$is_on_sale);

            }
            
        }

    }

    static function display(){

        $args = [
            'post_type' => 'product',
            'meta_query'  => [
                [
                    'key' => '_lb_sale',
                    'value' => 1
                ]
            ]
        ];

        $products = get_posts($args);

        $ids = [];

        if( $products ){

            foreach ($products as $p) {
                $ids[] = $p->ID;
            }

            echo do_shortcode('[products ids="'.implode(', ', $ids).'"]');

        }

    }
    
    // Recalculate sale status for every product once per day 
    // That makes sure that the 30 day criteria gets re-calculated.. 
    // Even if the price is already -30% and the product hasn't been updated/saved in a while
    function recalculate_sale_status(){
        global $wpdb;

        // Sales which are due to end
        $product_ids = $wpdb->get_col("
            SELECT posts.ID FROM {$wpdb->posts} as posts
            WHERE posts.post_status = 'publish'
            AND (posts.post_type = 'product'
            OR posts.post_type = 'product_variation')
        ");

        if ( $product_ids ) {

            foreach ( $product_ids as $product_id ) {
                $is_on_sale = $this->is_valid_for_sale($product_id);
                update_post_meta( $product_id, '_lb_sale', (int)$is_on_sale);
            }
            
        }

    }

}

new lbSale();
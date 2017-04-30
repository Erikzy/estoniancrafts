<?php
/**
 * Plugin Name: Dokan follow user/shop
 * Description: Käsitööturg custom extension for Dokan plugin follow function
 * Version: 1.0
 */


class lbFollow{

    private static $instance = null;

    public static function get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
 
        return self::$instance;
 
    }

	function __construct(){

        add_action( 'wp_enqueue_scripts', [$this, 'register_scripts'], 15 );
        add_action( 'lb_store_after_rating', [$this, 'store_follow_button'], 10, 1 );

        add_action( 'wp_ajax_lb_follow', [$this, 'follow'] );
        add_action( 'wp_ajax_nopriv_lb_follow', [$this, 'follow'] );

        add_action( 'transition_post_status', [$this, 'transition_status'], 10, 3 );

    }

    function register_scripts(){
        global $wp_scripts;

        if (!is_admin()) {
            wp_enqueue_style(  'lb-follow', plugin_dir_url( __FILE__ ) . 'follow.css', false,'1.0','all');
            wp_enqueue_script( 'lb-follow', plugin_dir_url( __FILE__ ) . 'follow.js', false,'1.0','all');
        }

    }

    function store_follow_button($shop_id){
        
        if( !$this->is_following($shop_id) ){
            ?>
            <a href="#follow" class="lb-follow-btn" data-store-id="<?= $shop_id ?>" data-follow="true"><i class="fa fa-heart-o"></i> <span><?php _e('start following', 'ktt') ?></span></a>
            <?php
        }else{
            ?>
            <a href="#follow" class="lb-follow-btn" data-store-id="<?= $shop_id ?>" data-follow="false"><i class="fa fa-heart"></i> <span><?php _e('stop following', 'ktt') ?></span></a>
            <?php
        }

    }

    function is_following($shop_id, $user_id = false){
        global $wpdb;

        if($user_id === false){
            $user_id = get_current_user_id();
        }

        $user_count = $wpdb->get_var( $wpdb->prepare('SELECT COUNT(*) FROM '.$wpdb->prefix.'lb_follow WHERE store_id = %d AND user_id = %d', $shop_id, $user_id) );

        return (bool)$user_count;

    }


    function follow(){
        global $wpdb;

        if( isset($_POST['follow']) && isset($_POST['store_id']) ){

            if($_POST['follow'] == 'true'){

                if( !$this->is_following($_POST['store_id']) ){

                    $wpdb->insert( 
                        $wpdb->prefix.'lb_follow', 
                        array( 
                            'user_id' => get_current_user_id(), 
                            'store_id' => $_POST['store_id'],
                            'date_added' => date('Y-m-d H:i:s')
                        ), 
                        array( 
                            '%d', 
                            '%d',
                            '%s',
                        ) 
                    );

                }

            }else{

                $wpdb->delete( $wpdb->prefix.'lb_follow', 
                    array( 
                        'user_id' => get_current_user_id(), 
                        'store_id' => $_POST['store_id']
                    ), 
                    array( 
                        '%d', 
                        '%d'
                    )
                );

                $this->json( ['status' => 'OK', 'following' => false, 'text' => __('start following', 'ktt')] );

            }

        }

        $this->json( ['status' => 'OK', 'following' => true, 'text' => __('stop following', 'ktt')] );

    }

    private function json($data){

        header('Content-Type: application/json');
        echo json_encode( $data );
        exit;

    }

    function transition_status( $new_status, $old_status, $post ){

        if( $old_status == 'pending' && $new_status == 'publish' && $post->post_type == 'product' ){

            // error_log('save messages to the notifications table');
            $this->schedule_notifications_for_product($post);
        }

    }

    function schedule_notifications_for_product($post){
        global $wpdb;

        // Fetch all users that are following the author of the post ( the store )
        $results = $wpdb->get_results( $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'lb_follow WHERE store_id = %d', $post->post_author) );

        if( $results ){

            foreach( $results as $follower ){

                $wpdb->insert( 
                    $wpdb->prefix.'lb_follow_notice', 
                    array( 
                        'shop_id' => $follower->store_id,
                        'user_id' => $follower->user_id, 
                        'type' => 'product', 
                        'post_id' => $post->ID
                    ), 
                    array( 
                        '%d', 
                        '%d',
                        '%s',
                        '%d'
                    ) 
                );

            }

        }

    }

    // TODO: proper testing in live & hook it to wp_cron
    function send_daily_messages(){
        global $wpdb;

        $results = $wpdb->get_results( $wpdb->prepare('
            SELECT fn.id AS notify_id, fn.shop_id, fn.user_id, fn.post_id, u.display_name, p.post_title, p.ID AS post_id, us.user_email AS follower_email
            FROM '.$wpdb->prefix.'lb_follow_notice fn 
            LEFT JOIN '.$wpdb->users.' u ON (fn.shop_id = u.ID)
            LEFT JOIN '.$wpdb->users.' us ON (fn.user_id = us.ID)
            LEFT JOIN '.$wpdb->posts.' p ON (fn.post_id = p.ID)
            WHERE fn.date_sent IS NULL AND fn.type = %s ORDER BY fn.user_id, fn.shop_id', 'product') );

        // print_r($results);die();

        if( $results ){

            $user_id = 0;
            $shop_id = 0;
            $updates = [];

            foreach($results as $follower){

                if( $follower->user_id != $user_id ){
                    $user_id = $follower->user_id;
                    $updates[$user_id] = ['user_id' => $follower->user_id, 'email' => $follower->follower_email, 'shops' =>[], 'notify_id' =>[] ];
                }

                if( $follower->shop_id != $shop_id ){
                    $shop_id = $follower->shop_id;
                    $updates[$user_id]['shops'][$shop_id] = ['shop_id' => $shop_id, 'name' => $follower->display_name, 'products' => []];
                }

                $updates[$user_id]['shops'][$shop_id]['products'][] = ['post_id' => $follower->post_id, 'post_title' => $follower->post_title];

                $updates[$user_id]['notify_id'][] = $follower->notify_id;

            }

            foreach ($updates as $update) {
            
                $sent = $this->email_follower($update['email'], $update['shops']);

                $ids = implode(',', $update['notify_id']);
                $wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."lb_follow_notice SET date_sent = %s WHERE id IN (".$ids.")",  date('Y-m-d H:i:s') ));


            }

        }

    }

    function email_follower($follower_email, $updates){

        $email_heading = __('Käsitööturg updates to stores that you are following', 'ktt');

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

        echo $message;

        return mail($follower_email, $email_heading, $message, $headers);

    }

}

lbFollow::get_instance();
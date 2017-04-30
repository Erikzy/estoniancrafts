<?php
/**
 * Plugin Name: Student addon
 * Description: Käsitööturg custom extension for student private posts
 * Version: 1.0
 */


class lbStudent{

    public static $errors = [];

	function __construct(){

        add_action('init', [$this, 'register_post_type']);
        add_action('template_redirect', [$this, 'template_redirect']);
        add_filter( 'woocommerce_account_menu_items', [$this, 'my_account_menu'] );
        add_action( 'init', [$this, 'custom_endpoints'] );
        add_filter( 'query_vars', [$this, 'custom_query_vars'], 0 );
        add_action( 'woocommerce_account_student_endpoint', [$this, 'endpoint_content'] );

    }

    function register_post_type(){
    
        register_post_type('student_product',
            [
               'labels'      => [
                   'name'          => __('Student products'),
                   'singular_name' => __('Student product'),
               ],
               'public'      => true,
               'has_archive' => false,
            ]
        );

    }

    public static function can_edit_post($post_id){

        $current_user = wp_get_current_user();
        $student_post = get_post( (int)$post_id );

        if ( $student_post && is_user_logged_in() && $current_user->ID == $student_post->post_author && $student_post->post_type == 'student_product' )  {
            return $student_post;
        }
            
        return false;

    }

    function template_redirect(){
        
        $student_post = new stdClass;
        $student_post->ID = 0;
        $student_post->post_title = '';
        $student_post->post_content = '';

        if( isset($_POST['lb_student_save']) ){

            if( !check_admin_referer( 'edit_student_post_'.(int)$_POST['lb_student_id'] ) ){
                self::$errors[] = __( "Wp_nonce is not valid.", 'ktt' );
            }

            if( isset($_POST['lb_student_id']) && $_POST['lb_student_id'] != 0 ){

                $student_p = self::can_edit_post($_POST['lb_student_id']);
                if ( $student_p )  {
                    $student_post->ID = $student_p->ID;
                }else{
                    self::$errors[] = __( "Can't modify the post with that ID", 'ktt' );
                }

            }

            $student_post->post_title   = sanitize_text_field($_POST['lb_student_title']);
            $student_post->post_content = wp_kses_post($_POST['lb_student_content']);

            if(strlen($student_post->post_title) < 3){
                self::$errors[] = __( 'Please enter product title', 'ktt' );
            }
            if(strlen($student_post->post_content) < 3){
                self::$errors[] = __( 'Please enter product content', 'ktt' );
            }

            if( count(self::$errors) == 0 ){
                $post_array = [
                    'ID' => $student_post->ID, 
                    'post_content' => $student_post->post_content,
                    'post_title' => $student_post->post_title,
                    'post_status' => 'publish',
                    'post_type' => 'student_product',
                    'comment_status' => 'open',
                    'ping_status' => 'closed'
                ];

                $insert_id = wp_insert_post( $post_array );

                if( !$insert_id){
                    self::$errors[] = __( 'Something went wrong. Try again', 'ktt' );
                }else{

                    self::share_post($insert_id, $_POST['_shared_email']);

                    $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
                    $redirect = site_url();
                    if ( $myaccount_page_id ) {
                        $redirect = get_permalink( $myaccount_page_id );
                    }

                    wp_redirect($redirect . "/student/?message=success");
                    
                }

            }

        }

    }


    public static function share_post($post_id, $post_emails){

        $saved_emails = [];
        
        if( is_array($post_emails) && count($post_emails) ){
            $saved_emails = get_post_meta($post_id, '_shared_emails', true);
        }

        $new_emails = [];

        foreach( $post_emails as $email ){

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {

                if( !is_array($saved_emails) || !in_array($email, $saved_emails) ){
                    self::notify($email, $post_id);
                }

                $new_emails[] = $email;
            }

        }
        
        update_post_meta( $post_id, '_shared_emails', wc_clean($new_emails) );

    }

    // Send notification e-mail
    public static function notify($email, $post_id){
        
        self::mail($email, $post_id);
        return 1;

    }

    private static function mail($to, $post_id){

        $email_heading = __('Käsitööturg e-mail header', 'ktt');

        $admin_email = get_option('admin_email');

        $headers = "From: Käsitööturg\r\n";
        $headers .= "Reply-To: ".$admin_email."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        ob_start();

        include('email/email-header.php');
        include('email/content.php');
        include('email/email-footer.php');

        $message = ob_get_clean();

        return mail($to, $email_heading, $message, $headers);

    }

    function my_account_menu( $items ) {
        $items = array(
            'dashboard'         => __( 'Dashboard', 'woocommerce' ),
            'orders'            => __( 'Orders', 'woocommerce' ),
            //'downloads'       => __( 'Downloads', 'woocommerce' ),
            //'edit-address'    => __( 'Addresses', 'woocommerce' ),
            //'payment-methods' => __( 'Payment Methods', 'woocommerce' ),
            'edit-account'      => __( 'Edit Account', 'woocommerce' ),
            'student'           => __( 'Student pages', 'ktt' ),
            'customer-logout'   => __( 'Logout', 'woocommerce' ),
        );

        return $items;
    }

    function custom_endpoints() {
        add_rewrite_endpoint( 'student', EP_ROOT | EP_PAGES );
    }

    function custom_query_vars( $vars ) {
        $vars[] = 'student';

        return $vars;
    }

    function endpoint_content() {
        include get_template_directory().'/woocommerce/myaccount/student.php'; 
    }

}

new lbStudent();
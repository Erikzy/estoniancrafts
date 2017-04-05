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


    public static function share_post($post_id, $post_emails){

        $saved_emails = [];
        
        if( is_array($post_emails) && count($post_emails) ){
            $saved_emails = get_post_meta($post_id, '_shared_emails', true);
        }

        $new_emails = [];

        foreach( $post_emails as $email ){

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {

                if( !in_array($email, $saved_emails) ){
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

        error_log("Kõmm! - email - ".$to." - id: ".$post_id);

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


}

new lbStudent();
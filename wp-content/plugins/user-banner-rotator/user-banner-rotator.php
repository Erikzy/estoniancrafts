<?php
/*
  Plugin Name: User Banner Rotator
  Plugin URI: -
  Description: Allows creation and upload of images for a user rotator and display of the banner on the frontend
  Version: 1.0
  Author: Erik Kesa
  Text Domain: user banner rotator
 */



if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('User_banner_rotator')) :

    class User_banner_rotator {

     
     	public $assetsEnqueued = false;
        public function __construct() {
            register_activation_hook(__FILE__, array($this, 'install'));
        }
        

		public function add_rotator_post_type() {
    			$args = array(
      				'public' => true,
      				'label'  => 'user banner rotator'
    			);
    			register_post_type( 'user_banner_rotator', $args );
		 }

		public function get_or_create_user_rotator($user_id,$banner_name){
			global $wpdb;
			$rotator = $this->get_rotator_post_id( $user_id,$banner_name);
			if(!isset($rotator[0]) || $rotator[0] == null){
				$rotator = $this->add_user_rotator($banner_name, $user_id);
			}
			return $rotator[0];
		}

		public function get_rotator_post_id($user_id, $banner_name){
		 	 global $wpdb;
			 $query = $wpdb->prepare( "SELECT post_id FROM ".$wpdb->prefix."user_banner WHERE user_id = %d AND banner_name = %s", $user_id, $banner_name );
			 return $wpdb->get_col($query);
		}

		public function get_rotator_slides($user_id, $post_id){
		 	 global $wpdb;
			 $query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."user_banner_slide WHERE user_id = %d AND banner_post_id = %d", $user_id, $post_id );
	   	     $res =  $wpdb->get_results($query);
			 return $res;
		}
		
		public function confirm_ownership($user_id, $attachment_id){
		 	global $wpdb;
			$query = $wpdb->prepare( "SELECT banner_post_id FROM ".$wpdb->prefix."user_banner_slide WHERE user_id = %d AND wp_attachment_id = %d", $user_id, $attachment_id );
			$res = $wpdb->get_col($query);
			if(sizeof($res) > 0){
				return true;
			}else{
				echo 'ownership failed';
				return false;
			}
		}
		public function confirm_post_ownership($user_id, $post_id){
		 	global $wpdb;
			$query = $wpdb->prepare( "SELECT post_id FROM ".$wpdb->prefix."user_banner WHERE user_id = %d AND post_id = %d", $user_id, $post_id );
			$res = $wpdb->get_col($query);
			if(sizeof($res) > 0){
				return true;
			}else{
				return false;
			}
		}
		
		public function add_user_rotator($banner_name, $user_id){
		  global $wpdb;
		  $banner_header_post = array(
  			'post_title'    =>  wp_strip_all_tags( $banner_name ),
  			'post_content'  =>  $banner_name,
  			'post_status'   => 'publish',
  			'post_author'   =>  $user_id ,
  			'post_type'     => 'user_banner_rotator' 
  		  );
 
         // Insert the post into the database
		   $post = wp_insert_post( $banner_header_post );
		
		   $query = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."user_banner 
			 							(user_id,banner_name,post_id)
			 						   VALUES 
			 						 	(%d, %s,%d)", $user_id, $banner_name, $post);
	   		$wpdb->query($query);
			return array($post);	 						 		
		
		}
		
		public function add_attachment_id($user_id, $post_id, $attachment_id){
		     global $wpdb;
		    if($this->confirm_post_ownership($user_id, $post_id)){
		      $query = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."user_banner_slide 
			 							(user_id,banner_post_id,wp_attachment_id)
			 						   VALUES 
			 						 	(%d, %d,%d)", $user_id, $post_id, $attachment_id );
			  $wpdb->query($query);		  
			  return true;
			}
			return false;	 						 		
		}
		
		public function remove_attachment_id($user_id, $post_id, $attachment_id){
		    global $wpdb;
		   if($this->confirm_ownership($user_id, $attachment_id)){
		  	 $query = $wpdb->prepare( "DELETE FROM ".$wpdb->prefix."user_banner_slide 
										WHERE banner_post_id = %d 
									    AND wp_attachment_id = %d
			 						    AND user_id = %d 
			 						", $post_id, $attachment_id,$user_id );
		   	 wp_delete_attachment($attachment_id);
  		     $wpdb->query($query);
			 return true;
		   }
		   return false;	
		}
			
        public function install() {
            global $wpdb;

 			add_action( 'init', array($this, 'add_rotator_post_type') );

            if ($wpdb->has_cap('collation')) {
                if (!empty($wpdb->charset)) {
                    $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
                }
                if (!empty($wpdb->collate)) {
                    $collate .= " COLLATE $wpdb->collate";
                }
            }
            $user_banner_table = $wpdb->prefix . 'user_banner';
            $user_banner_slide_table = $wpdb->prefix . 'user_banner_slide';
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta("

    	  CREATE TABLE {$user_banner_table} (
       		 ID bigint(20) unsigned NOT NULL auto_increment,
       		 user_id bigint(20) unsigned,
        	 banner_name varchar(20),
   		  	 post_id varchar(20),
   		  
   		  PRIMARY KEY  (ID)
    	  ) $collate;

    		CREATE TABLE {$user_banner_slide_table} (
    		 ID bigint(20) unsigned NOT NULL auto_increment,
       		 user_id bigint(20) unsigned,
       		 banner_post_id bigint(20), 
       		 wp_attachment_id bigint(20) unsigned,
       		 PRIMARY KEY (ID)

   			 ) $collate;");
        }

    }

    endif;

function user_banner_add_slide_ajax(){
  ob_start();
  global $user_banner_rotator;
  if ( 
    ! isset( $_POST['user_banner_upload_form'] ) 
    || ! wp_verify_nonce( $_POST['user_banner_upload_form'], 'user_banner_upload' ) 
  ) {

   print 'Sorry, your nonce did not verify.';
   exit;

  } else {
	
	 if(isset($_POST['user_attachment_id']) && isset($_POST['post_id'])){
	 	$user_id = get_current_user_id();
	 	$post_id = $_POST['post_id'];
	 	$attachment_id = $_POST['user_attachment_id'];
	 	$res = $user_banner_rotator->add_attachment_id($user_id, $post_id, $attachment_id);
        //echo json_serialize(array("res"=>false,"msg"=>"Sorry, your nonce did not verify."));
	 			echo $res;
	 }
  }
  wp_die();
  return ob_get_clean();
}

function user_banner_remove_slide_ajax(){
  ob_start();
  global $user_banner_rotator;
  if ( 
    ! isset( $_POST['user_banner_upload_form'] ) 
    || ! wp_verify_nonce( $_POST['user_banner_upload_form'], 'user_banner_upload' ) 
  ) {

   echo json_serialize(array("res"=>false,"msg"=>"Sorry, your nonce did not verify."));
   exit;

  } else {
	 if(isset($_POST['user_attachment_id']) && isset($_POST['post_id'])){
	 	$user_id = get_current_user_id();
	 	$post_id = $_POST['post_id'];
	 	$attachment_id = $_POST['user_attachment_id'];
	 	$res = $user_banner_rotator->remove_attachment_id($user_id, $post_id, $attachment_id);
	 	//echo json_serialize(array("res"=>$res));
		echo $res;
	 }
  }
   wp_die();
  return ob_get_clean();
}

function user_banner_upload_form($atts = []){
	add_action('wp_enqueue_scripts', 'enqueue_assets');
    ob_start();
    global $wpdb, $user_banner_rotator;
   	$user_id = $atts['user_id'];
 	$banner_name = $atts['banner_name'];
 	$banner_instance_width = $atts['width'];
  	$banner_instance_height = $atts['height'];
    $banner_instance_id = $user_banner_rotator->get_or_create_user_rotator($user_id, $banner_name);
    $slides = $user_banner_rotator->get_rotator_slides($user_id, $banner_instance_id);
    include ( 'banner-upload-html.php');
    return ob_get_clean();

}
function user_banner_html($atts = []){
	add_action('wp_enqueue_scripts', 'enqueue_assets');
    ob_start();
    global $wpdb, $user_banner_rotator;
    $atts = array_change_key_case((array)$atts, CASE_LOWER);
 	$user_id = intval($atts['user_id']);
 	$banner_name = $atts['banner_name'];
 	$banner_instance_width = $atts['width'];
  	$banner_instance_height = $atts['height'];

 	$banner_instance = $user_banner_rotator->get_rotator_post_id($user_id, $banner_name);
    if(!isset($banner_instance[0]) || $banner_instance[0] == null){
    	include ( 'placeholder-html.php'); 
    }else{
     	$banner_instance_id = $banner_instance[0];
 		$slides = $user_banner_rotator->get_rotator_slides($user_id, $banner_instance_id);
   	 	include ('banner-html.php');
    }
    return ob_get_clean();

}

add_shortcode("display_upload_form", "user_banner_upload_form");
add_shortcode("display_user_banner", "user_banner_html");
add_action('wp_ajax_user-banner-remove-slide', 'user_banner_remove_slide_ajax');
add_action('wp_ajax_user-banner-add-slide', 'user_banner_add_slide_ajax');

function enqueue_assets(){
	global $user_banner_rotator;
	
	if(!$user_banner_rotator->assetsEnqueued){
	wp_register_style( 'bxSlider', 'https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css' );
	wp_enqueue_style('bxSlider');
	wp_register_script( 'jQuery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', null, null, true );
	wp_enqueue_script('jQuery');

	wp_register_script( 'jQueryBxSlider', 'https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js', null, null, true );
	wp_enqueue_script('jQueryBxSlider');
	$user_banner_rotator->assetsEnqueued = true;
	}
}
global $user_banner_rotator;
if (class_exists('User_banner_rotator') && !$user_banner_rotator) {
    $user_banner_rotator = new User_banner_rotator();
}
 
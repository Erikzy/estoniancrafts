<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
ob_clean();
ob_start();
   /**
 * return $images_id array()
 */
 function get_gallery_attachments_to_id($users_id, $filed_name ){
	$ids[] ='';
	$gallery_content = get_post_meta($users_id, $filed_name, true);
    if(!empty($gallery_content)):
	preg_match('/\[gallery.*ids=.(.*).\]/', $gallery_content, $ids);
	$images_id = explode(",", $ids[1]);
	return $images_id;
    endif;
}



function add_edit_portfolio()
{
    if(is_user_logged_in())
    {
    $current_user = wp_get_current_user();
   
    $gallery_id = isset($_GET['id']) ? esc_attr($_GET['id']) : null ;
        if($gallery_id!==null)
        {
           $post = get_post($gallery_id);
        }
        else
        {
          $post = new WP_Post((object)[]);   
        }
    if(isset($_POST['gallery_submit'])  && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ))
    {
       
      
        
        $gallery_images = [];
        $post_content = [];
        $postTitle = isset($_POST['post_title']) ? esc_attr($_POST['post_title']) : null ;
        $portfolio_gallery = isset($_POST['portfolio_gallery']) ? esc_attr($_POST['portfolio_gallery']) : null ;
        $gallery_images = isset($_POST['post_picture']) ? esc_attr($_POST['post_picture']) : null ;
        for($k=0; $k < sizeof($_POST['post_picture']); $k++ )
        {
           array_push($post_content, "picture_url", $_POST['post_picture'][$k]);
        }
        echo sizeof($_POST['post_picture']);
        echo '<pre>';
        print_r($post_content);
            
        exit;
        $post->post_title = $postTitle;
        $post->post_type = 'portfolio_gallery';
        $post->post_content = $portfolio_gallery;
        
           $errors = [];
        
        
        // validate
        	if (!$postTitle) {
        		$errors['post_title'] = __('Empty blog post title', 'ktt');
        	}
        // save if no errors
        	if (!count($errors)) {
       if ($post->ID) {
        			wp_update_post($post);
       }
        else
        {
           $post->ID = (wp_insert_post($post)); 
        }
        wp_redirect(home_url('/my-account/portfolio/edit?id='.$post->ID));
            }
       
    }
    include(locate_template('templates/myaccount/gallery_form.php'));
    }
        else
    {
       wp_redirect( home_url('/my-account'));
    }
}
add_shortcode('add_edit_portfolio', 'add_edit_portfolio');

/*page design*/
function my_enqueue_media_lib_uploader() {
        //Core media script
        wp_enqueue_media();
}
add_action('wp_enqueue_scripts',   'my_enqueue_media_lib_uploader');



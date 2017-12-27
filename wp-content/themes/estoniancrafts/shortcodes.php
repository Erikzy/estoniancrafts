<?php

class EC_Shortcodes
{
	public static function init()
	{
		add_action( 'wp_loaded', array(__CLASS__, 'wp_loaded_action') );
	}

	public static function wp_loaded_action()
	{
		add_shortcode('ec_institutions', array(__CLASS__, 'ec_institutions'));
		add_shortcode( 'non_vc_frontpage', array(__CLASS__, 'frontpage_sliders'));
		add_shortcode('user_has_idCard_extended', array(__CLASS__,'user_has_idcard_extended'));
		

	}

	public static function ec_institutions($atts)
	{
		$query = new WP_Query(array(
			'post_type' => 'institution',
			'orderby' => 'title',
			'order' => 'ASC'
		));

		$html = '<div class="ec-institutions-list">';
		if($query->have_posts())
		{
			foreach($query->get_posts() as $post)
			{
				$html .= '<div class="ec-institution">';
				$html .= '	<div class="ec-institution-logo">'.get_the_post_thumbnail( $post ).'</div>';
				$html .= '	<div class="ec-institution-content">';
				$html .= '		<h2>'.$post->post_title.'</h2>';
				$html .= '		<div class="ec-institution-description">'.$post->post_content.'</div>';
				$html .= '	</div>';
				$html .= '</div>';
			}
/* With the link to institution page
			foreach($query->get_posts() as $post)
			{
				$postUrl = get_the_permalink( $post );
				$html .= '<div class="ec-institution">';
				$html .= '	<div class="ec-institution-logo"><a href="'.$postUrl.'">'.get_the_post_thumbnail( $post ).'</a></div>';
				$html .= '	<div class="ec-institution-content">';
				$html .= '		<h2><a href="'.$postUrl.'">'.$post->post_title.'</a></h2>';
				$html .= '		<div class="ec-institution-description">'.$post->post_content.'</div>';
				$html .= '	</div>';
				$html .= '</div>';
			}
*/
		}
		$html .= '</div>';
		return $html;
	}
	
	public static function user_has_idcard_extended(){
		 global $wpdb;
         $current_user = wp_get_current_user();
         $user = $wpdb->get_row(
         $wpdb->prepare(
                "select * from $wpdb->prefix" . "idcard_users WHERE userid=%s", $current_user->ID
               )
         );
         if (!$user) {
            echo '<h4>Confirm with ID-card</h4>';
            $data = do_shortcode('[smart_id]');
            echo $data;
         }	else	{
        	echo '<h4>Create Shop</h4>';
        	?>
        	<script type="text/javascript">
        		function addSellerProfile(){
        			alert("Add seller profile");
        		}
        	</script>	
        	<?php 
         	echo '<button class="call-to-action-button" onclick=addSellerProfile()>Create shop</button>';
         }
	}
	
	public static function frontpage_sliders( $atts ) {
	
		 include(locate_template('partials/frontpage_sliders.php'));
	
   		 return null;
	}
	
	
	
}
EC_Shortcodes::init();
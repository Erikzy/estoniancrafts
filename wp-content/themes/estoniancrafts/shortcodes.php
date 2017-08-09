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
}
EC_Shortcodes::init();
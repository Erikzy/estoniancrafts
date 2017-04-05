<?php
/**
 * The template for displaying user profile
 *
 */

get_header(); 
global $wp_query;
?>


<?php 
	
	// Get content width and sidebar position
	$content_class = basel_get_content_class();

?>

<div class="site-content <?php echo esc_attr( $content_class ); ?>" role="main">

	<pre>
		<?php 

		$user = get_user_by('login', $wp_query->query_vars['user']);
		$dokan_profile = get_user_meta( $user->data->ID, 'dokan_profile_settings', true );
		$ext_profile = get_user_meta( $user->data->ID, 'ktt_extended_profile', true );
		$ext_shop = get_user_meta( $user->data->ID, 'ktt_extended_settings', true );
		
		print_r($ext_profile);
		print_r($ext_shop);

		?>
	</pre>

</div><!-- .site-content -->


<?php get_sidebar(); ?>

<?php get_footer(); ?>
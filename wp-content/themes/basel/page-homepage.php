<?php
/**
 * Template Name: Homepage
 */

get_header(); 

?>
	
	<?php 
		lbAdvert::display();
		lbSale::display();
	?>

<?php get_footer(); ?>

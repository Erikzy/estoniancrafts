<?php
 /*Template Name: My Account Page*/
?>
<?php  
	get_header();
?>
<div style='width:300px'>
<?php 
	get_sidebar();
?>
</div>
<div style="float:left ; width:calc(100% - 300px)">
			<?php while ( have_posts() ) : the_post(); ?>


					<div class="entry-content">
						<?php the_content(); ?>
						<?php wp_link_pages(); ?>
					</div>




		<?php endwhile; ?>
</div>

<?php
	get_footer();
?>
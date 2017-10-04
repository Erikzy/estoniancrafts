<?php
/**
 * The template for displaying the footer
 *
 */
?>
<?php if (basel_needs_footer()): ?>
	<?php basel_page_bottom_part(); ?>

	<?php if ( basel_get_opt( 'prefooter_area' ) != '' ): ?>
		<div class="basel-prefooter">
			<div class="container">
				<?php echo do_shortcode( basel_get_opt( 'prefooter_area' ) ); ?>
			</div>
		</div>
	<?php endif ?>

	<!-- FOOTER -->
	<footer class="footer-container color-scheme-<?php echo esc_attr( basel_get_opt( 'footer-style' ) ); ?>">
	
		<div class="kt-footer-top">
			<?php echo do_shortcode('[mc4wp_form id="322"]'); ?>
		</div>
		
		<?php get_sidebar( 'footer' ); ?>
		
		<div class="copyrights-wrapper">
			<div class="container">
				<div class="min-footer">
						<?php if ( basel_get_opt( 'copyrights' ) != ''): ?>
							<?php echo do_shortcode( basel_get_opt( 'copyrights' ) ); ?>
						<?php else: ?>
							<p>&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url('/') ); ?>"><?php bloginfo( 'name' ); ?></a>. <?php _e( 'All rights reserved', 'basel' ) ?></p>
						<?php endif ?>
					
						<?php if ( basel_get_opt( 'copyrights2' ) != ''): ?>
							<?php echo do_shortcode( basel_get_opt( 'copyrights2' ) ); ?>
						<?php endif ?>
				
					
				</div>
			</div>
		</div>
	</footer>
	<div class="basel-close-side"></div>
<?php endif ?>
</div> <!-- end wrapper -->

<?php wp_footer(); ?>

<?php if (basel_needs_footer()) do_action( 'basel_after_footer' ); ?>

</body>
</html>
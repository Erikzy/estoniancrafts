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


<div id='rndd'></div>
<?php if(!user_has_confirmed()):?>

<script type="text/javascript">
 jQuery(document).ready(function(){
 	jQuery(".new-footer-newsletter input").focus(function(){
		if(jQuery("#mc_accept").length){
		
		}else{
			jQuery(".new-footer-newsletter").css("cssText", "height:104px !important;line-height:52px;color:#cdcdcd;");
			jQuery(".newsletter-submit").css("cssText","background-color: #cdcdcd !important;height:104px !important;line-height:104px !important;position: absolute;right: 0px;top: 0px;");		
			jQuery(".newsletter-submit").prop('disabled', true);
			jQuery(".new-footer-newsletter input").after("<?php
				echo '<br>&nbsp;&nbsp;&nbsp;<input id=\'mc_accept\'  type=checkbox style=\'color:#343434;\' />'.__('I agree with the privacy policy', 'ec-privacy');	
			?>");
			jQuery('#mc_accept').change(function() {
        		if(this.checked) {
        			jQuery(".newsletter-submit").css("cssText","background-color: #EF7F27 !important;height:104px !important;line-height:104px !important;position: absolute;right: 0px;top: 0px;");
            		jQuery(".newsletter-submit").prop('disabled', false);
		    	}else{
		    		jQuery(".newsletter-submit").css("cssText","background-color: #cdcdcd !important;height:104px !important;line-height:104px !important;position: absolute;right: 0px;top: 0px;");
            		jQuery(".newsletter-submit").prop('disabled', true);
		    	}
       		});
			
		}
 	});
 })


</script>
<?php endif; ?>

</body>
</html>
<?php 
	global $product, $woocommerce_loop;

	$timer = basel_get_opt( 'shop_countdown' );
	// Sale countdown
	if ( ! empty( $woocommerce_loop['timer'] ) )
		$timer = true;
?>
<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

<!--
<div class="product-element-top">

	<a href="<?php echo esc_url( get_permalink() ); ?>">
		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked basel_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
		?>
	</a>
	<?php basel_hover_image(); ?>
	<div class="basel-buttons">
		<?php if( class_exists('YITH_WCWL_Shortcode')) basel_wishlist_btn(); ?>
		<?php basel_compare_btn(); ?>
		<?php basel_quick_view_btn( get_the_ID(), $woocommerce_loop['quick_view_loop'] - 1, 'main-loop' ); ?>
	</div>

	<div class="swatches-wrapper">
		<?php 
			basel_swatches_list();
		?>
	</div>
</div>



<div class="product-element-bottom">
	<?php
		/**
		 * woocommerce_shop_loop_item_title hook
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );

		// Price and categories
		do_action( 'woocommerce_after_shop_loop_item_title' );
	?>

	<div class="product-excerpt">
		<?php the_excerpt(); ?>
	</div>
</div>
<div class="rating-wrapper">
    <?php
    /**
     * woocommerce_after_shop_loop_item_title hook
     *
     * @hooked woocommerce_template_loop_rating - 5
     * @hooked woocommerce_template_loop_price - 10
     */
    ?>
</div>
<div class="btn-add">
	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
</div>

<?php if ( $timer ): ?>
	<?php basel_product_sale_countdown(); ?>
<?php endif ?>



-->
<div class="ec-new-product-grid row">
<div class="ec-new-product-grid-left col-md-3 " style="padding:0px;">
	<a href="<?php echo esc_url( get_permalink() ); ?>">
		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked basel_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
		?>
	</a>
	<?php basel_hover_image(); ?>
	<div class="basel-buttons">
		<?php if( class_exists('YITH_WCWL_Shortcode')) basel_wishlist_btn(); ?>
		<?php basel_compare_btn(); ?>
		<?php basel_quick_view_btn( get_the_ID(), $woocommerce_loop['quick_view_loop'] - 1, 'main-loop' ); ?>
	</div>

	<div class="swatches-wrapper">
		<?php 
			basel_swatches_list();
		?>
	</div>

</div>

<div class="ec-new-product-grid-right col-md-9 rr">
  
  <div class="ec-new-product-grid-right-top">
  	<?php
		/**
		 * woocommerce_shop_loop_item_title hook
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );
		
		
		woocommerce_template_loop_price();
		// Price and categories
		//do_action( 'woocommerce_after_shop_loop_item_title' );
	?>
	<div class="sold-list">
  		   Units sold:
  		    <?php
				$units_sold = get_post_meta( $product->id, 'total_sales', true );
				echo $units_sold;
			?>
		</div>
			
  		  
  		    <?php
				$_size_chart = get_post_meta( $product->id, 'size_chart' );
				var_dump($_size_chart);
				if($_size_chart == 'on'){
					echo '<div class="sold-list">';
					echo '<a href="'.get_site_url(null, 'size-chart').'">Size chart</a>';
					echo '</div>';
				}	
			?>

  </div>
  
  <div class="ec-new-product-grid-right-mid adj-mid-grid">
  	<?php 
  	 $ex =	get_the_excerpt(); 
  	 $max = 270;
  	 if(strlen($ex) > $max){
  	 	echo substr($ex,0,$max).'...';
     }else{
     	echo $ex;
     }
  	?>
  </div>
  
  <div class="ec-new-product-grid-right-bottom adj-grid">
  
  	<div class="col-md-4 d-table" >
  	<!-- <div class="col-md-4 d-table" style="padding:0px;"> -->
	
	  <div class="btn-add archive-button-add"><span class='add-to-cart-store'>
	    <?php  do_action( 'woocommerce_after_shop_loop_item' ); ?></span>
      </div>
  	</div>
  	<div class="col-md-4 wishlist-p d-table" >
  	<!-- <div class="col-md-4 wishlist-p d-table" style="padding:0px;"> -->
	 	<div class='stars-wrapper'>
		 	<?php if( class_exists('YITH_WCWL_Shortcode'))  echo YITH_WCWL_Shortcode::add_to_wishlist(array(get_the_ID()));; ?>
		 	<?php
		 	  
		 	
		 	  woocommerce_template_loop_rating();
		 	?>
	  	</div>
  	</div>

   	<!-- <div class="col-md-4 autor-p d-table" style="padding:0px;text-align:left;"> -->
   	<div class="col-md-4 autor-p d-table" >
		
			<div class="ec-new-author-heading ">
				<i class="fa fa-user-o" style="font-size: 20px;"></i> 
				<div class="ec-new-author-name"><?php
	   			$author     = get_user_by( 'id', $product->post->post_author );
	   			if(is_object($author)){
					echo '<a href="/'.bp_core_get_username( $product->post->post_author).'">'.$author->display_name.'</a>';
	   			}
	   			?>
	   		 </div>
			</div>
	

   	</div>  
  	
    <?php if ( $timer ): ?>
	  <?php basel_product_sale_countdown(); ?>
    <?php endif ?> 
  </div>

</div>

</div>


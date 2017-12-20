<?php
/**
 * Product attributes
 *
 * Used by list_attributes() in the products class.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-attributes.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$has_row    = false;
$alt        = 1;
$attributes = $product->get_attributes();
$post_id = $product->id;
//var_dump($product);
ob_start();

?>
<table class="shop_attributes">

	<?php if ( $product->enable_dimensions_display() ) : ?>

		<?php if ( $product->has_weight() ) : $has_row = true; ?>
			<tr class="<?php if ( ( $alt = $alt * -1 ) === 1 ) echo 'alt'; ?>">
				<th><?php _e( 'Weight', 'woocommerce' ) ?></th>
				<td class="product_weight"><?php echo wc_format_localized_decimal( $product->get_weight() ) . ' ' . esc_attr( get_option( 'woocommerce_weight_unit' ) ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $product->has_dimensions() ) : $has_row = true; ?>
			<tr class="<?php if ( ( $alt = $alt * -1 ) === 1 ) echo 'alt'; ?>">
				<th><?php _e( 'Dimensions', 'woocommerce' ) ?></th>
				<td class="product_dimensions"><?php echo $product->get_dimensions(); ?></td>
			</tr>
		<?php endif; ?>
		<?php 
			$materials = get_post_meta($post_id, '_materials', true);
			
			if(sizeof($materials) > 0 && $materials[0]["name"] !== "" ) :
			?>
			<tr >
				<th><?php _e( 'Used materials', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins">
					<?php 
						
						foreach($materials as $material){

                        ?>

                           
                            	<div class="material-container">
                            		<p>
                            			<strong>Name: </strong> <?php echo $material['name']?><br> 
                            			<strong>Contents: </strong> <?php echo $material['contents']?><br> 
                            			<strong>Description: </strong> <?php echo $material['desc']?><br> 
                            			<?php
                            				 $countries_obj   = new WC_Countries();
    										$countries   = $countries_obj->__get('countries');

                            			 ?> 
                            			
                            			<strong>Country: </strong> <?php echo $countries[$material['country']]?><br> <br> 


                            		</p>
                            	</div>

                        <?php 
                        }
                        ?>
				 	</div>



				 </td>
			</tr>
		
			<?php
			endif; //end materials
			?>
			<?php 
			$manufacturing_method= get_post_meta($post_id, '_manufacturing_method', true) ;
			//var_dump($mis);
			
			if(trim($manufacturing_method) !== "") :
			?>
			<tr >
				<th><?php _e( 'Manufacturing information', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins lh-prod">
							<strong>Manufacturing Method: </strong><br> <?php echo $manufacturing_method; ?><br>
							<?php
								$md = get_post_meta($post_id, '_manufacturing_desc', true);
								//var_dump($md);
								if(trim($md) !==""):
									?>
										<strong>Manufacturing Description: </strong> <br><?php echo $md; ?><br>
									<?php
								endif;
							?> 
							<?php
								$mt = get_post_meta($post_id, '_manufacturing_time', true);
								if(trim($mt) !== ""):
									?>
										<strong>Manufacturing Time: </strong> <br><?php echo $mt.' '.get_post_meta($post_id, '_manufacturing_time_unit', true) ; ?>(s)<br>
									<?php
								endif;
							?> 
							<?php
								$mq = get_post_meta($post_id, '_manufacturing_qty', true);
								if(trim($mq) !== ""):
									?>
										<strong>Manufacturing Quantity: </strong><br> <?php echo $mq.' '.get_post_meta($post_id, '_manufacturing_qty_unit', true) ; ?>(s)<br>
									<?php
								endif;
							?> 
                             
				 	</div>



				 </td>
			</tr>
		
			<?php
			endif; // end manufacturing
			?>
			<?php 
			$maint= get_post_meta($post_id, '_maintenance_info', true) ;
			
			
			if(trim($maint) !== "") :
			?>
			<tr >
				<th><?php _e( 'Maintenance information', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins lh-prod">
							 <?php echo $maint; ?>

                             
				 	</div>



				 </td>
			</tr>
		
			<?php
			endif; //end maintenance
			?>
			<?php 
			$media_links = get_post_meta($post_id, '_media_links', true);
			
			
			if(trim($media_links[0]) !== "") :
			?>
			<tr >
				<th><?php _e( 'External media links', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins lh-prod">
					<?php		 
                    foreach($media_links as $link){

                        ?>

                        <div class="lb-elastic-element lb-input-margins">
                            
                                <a href="<?php echo $link ?>" target="_blank" ><?php echo $link ?></a><br>
                            
                        </div>
                        
                        <?php

                    }?>     
				 	</div>
				 </td>
			</tr>
		
			<?php
			endif; //end media links
			?>

			<?php 
			$media_links = get_post_meta($post_id, '_media_links', true);
			
			
			if(trim($media_links[0]) !== "") :
			?>
<!-- 			<tr >
				<th><?php _e( 'Patent / Certificate', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins lh-prod">
					<?php		 
                    foreach($media_links as $link){

                        ?>

                        <div class="lb-elastic-element lb-input-margins">
                            
                                <a href="<?php echo $link ?>" target="_blank" ><?php echo $link ?></a><br>
                            
                        </div>
                        
                        <?php

                    }?>     
				 	</div>
				 </td>
			</tr> -->
		
			<?php
			endif; //end media links
			?>


	<?php endif; ?>

	<?php foreach ( $attributes as $attribute ) :
		if ( empty( $attribute['is_visible'] ) || ( $attribute['is_taxonomy'] && ! taxonomy_exists( $attribute['name'] ) ) ) {
			continue;
		} else {
			$has_row = true;
		}
		?>
		<tr class="<?php if ( ( $alt = $alt * -1 ) == 1 ) echo 'alt'; ?>">
			<th><?php echo wc_attribute_label( $attribute['name'] ); ?></th>
			<td><?php
				if ( $attribute['is_taxonomy'] ) {

					$values = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

				} else {

					// Convert pipes to commas and display values
					$values = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
					echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

				}
			?></td>
		</tr>
	<?php endforeach; ?>

</table>
<?php
if ( $has_row ) {
	echo ob_get_clean();
} else {
	ob_end_clean();
}

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

//ob_start();

?>
<table class="shop_attributes">

	<?php //if ( $product->enable_dimensions_display() ) : ?>
	<?php if ( checkAttributes($product->id)  || $product->enable_dimensions_display() ) : ?>

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
			$manufacturing_method= get_post_meta($post_id, '_manufacturing_method', true) ;
			//var_dump($manufacturing_method);
			//if(trim($manufacturing_method) !== "") :
			if( !empty($manufacturing_method) ) :
			?>
			
			<tr><th><?php _e( 'Manufacturing method', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins lh-prod">

							<p><?php echo $manufacturing_method; ?></p>
                             
				 	</div>
			</tr>
					<?php
								$md = get_post_meta($post_id, '_manufacturing_desc', true);
								
								if( !empty($md) ): ?>
			<tr><th><?php _e( 'Manufacturing description', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins lh-prod">
							<p><?php echo $md; ?></p>
                             
				 	</div>
			</tr> <?php endif; ?>
					<?php
								$mt = get_post_meta($post_id, '_manufacturing_time', true);
								if( !empty($mt) ):
									?>
			<tr><th><?php _e( 'Manufacturing time', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins lh-prod">
							<p><?php echo $mt.' '.get_post_meta($post_id, '_manufacturing_time_unit', true) ; ?>(s)</p>
                             
				 	</div>
			</tr> <?php endif; ?>
					<?php
								$mq = get_post_meta($post_id, '_manufacturing_qty', true);
								if( !empty($mq) ):
									?>
			<tr><th><?php _e( 'Manufacturing quantity', 'woocommerce' ) ?></th>
				<td class="product_dimensions">
					 <div class="lb-elastic-element lb-input-margins lh-prod">
							<p><?php echo $mq.' '.get_post_meta($post_id, '_manufacturing_qty_unit', true) ; ?>(s)</p>
                             
				 	</div>
			</tr> <?php endif; ?>
		
			<?php
			endif; // end manufacturing
			?>
		


	<?php endif; ?>



</table>


<?php 
			$materials = get_post_meta($post_id, '_materials', true);
			
			if(!empty($materials) && $materials[0]["name"] !== "" ) :
			?>
			<div class="tab-sep"> </div>
			<h1 class="shop_attributes tab-header">Used Materials</h1>
			<!-- USED MATERIALS -->

			<table class="shop_attributes">

					
						<tbody>
					<?php 
						
						foreach($materials as $material){

                        ?>
			<tr >
				<th> 		
	                            			<strong>Material Name: </strong><br> 
											<?php echo $material['name']?><br>

	                            		</th>
				<td class="product_dimensions product_materials_m">
					 <div class="lb-elastic-element lb-input-margins">
			

                           
                            	<div class="material-container">
                        
	                           
	                            		<p> 
	                            			 <?php echo $material['contents']?><br> <br>
	                            			<!-- <strong>Description: </strong>  -->
	                            			<?php echo $material['desc']?><br> 
	     
	                	                    <?php
	                            				 $countries_obj   = new WC_Countries();
	    										$countries   = $countries_obj->__get('countries');

	                            			 ?> 
	                            			<?php if($material["country"] !== "" ): ?> 

	                            				<!-- <strong>Country: </strong> --> <?php echo $countries[$material['country']]?><br> <br> 
	                            			<?php endif; ?>
	       

	                            		 </p>
	                          
                            	</div>

                   
				 	</div>



				 </td>
			</tr>
		     <?php 
                        }
                        ?>
         </tbody></table>
			<?php


			endif; //end materials?>
			



				<?php 
						$maint= get_post_meta($post_id, '_maintenance_info', true) ;
						
						
						if(trim($maint) !== "") :
						?>
						<div class="tab-sep"> </div>
						<h1 class="shop_attributes tab-header">Maintenance</h1>
						<!-- MAINTENANCE -->

						<table class="shop_attributes">

								
									<tbody>
						<tr >
							
							<td class="product_dimensions">
								 <div class="lb-elastic-element lb-input-margins lh-prod">
										 <p class="text-left" ><?php echo $maint; ?></p>

			                             
							 	</div>



							 </td>
						</tr>
	</tbody></table>
				
						<?php
						endif; //end maintenance
						?>
			





			<?php 
			$certificates = get_post_meta($post_id, '_certificates', true);
			$media_links = get_post_meta($post_id, '_media_links', true);

			if(!empty($certificates[0]["file"]) || !empty($media_links[0])  ):
					?>
			<div class="tab-sep"> </div>
			<h1 class="shop_attributes tab-header">Links</h1>
			<!--  LINKS -->

			<table class="shop_attributes">

					
						<tbody>



					<?php
			
				
								
								if(!empty($certificates[0]["file"])) :
								?>

										<?php		 
					                    foreach($certificates as $certificate){

					                        ?>
								<tr >
								
									<td class="product_dimensions">
										 <div class="lb-elastic-element lb-input-margins lh-prod">

					                        <div class="lb-elastic-element lb-input-margins">
					                            
					                             <p class="text-left"> <i class="fa fa-file-o"></i><a href="<?php echo wp_get_attachment_url( $certificate['file']); ?>"  class="view_cert_link links-attributes"  target="_blank" > View <?php echo $certificate["type"]; ?> 
					                             </a> <br></p>
					                            
					                        </div>
					                     </div>
									 </td>
								</tr>
					                        <?php

					                    }?>     

							 
								<?php
								endif; //end mpatents
								?>








					<?php 
								
								
								
								if(!empty($media_links[0]) ) :
								?>
								
										<?php		 
					                    foreach($media_links as $link){

					                        ?>
									<tr >
									
										<td class="product_dimensions">
											 <div class="lb-elastic-element lb-input-margins lh-prod">
						                        <div class="lb-elastic-element lb-input-margins">
						                            
						                                <p class="text-left"> <i class="fa  fa-external-link aa  " ></i> <a href="<?php echo $link ?>" target="_blank" class="links-attributes" >External article<?php //echo $link ?></a><br></p>
						                            
						                        </div>
						              				 	</div>
										 </td>
									</tr>          
					                        <?php

					                    }?>     

							
								<?php
								endif; //end media links
								?>

						</tbody></table>

								<?php
					endif;
								?>



<?php
if ( $has_row ) {
	//echo ob_get_clean();
} else {
	//ob_end_clean();
}

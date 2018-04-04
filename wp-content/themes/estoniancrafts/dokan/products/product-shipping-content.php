<?php
/**
 * Dokan Dashboard Product shipping Content
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<?php do_action( 'dokan_product_options_shipping_before', $post_id ); ?>

<?php if ( 'yes' == get_option( 'woocommerce_calc_shipping' ) || 'yes' == get_option( 'woocommerce_calc_taxes' ) ): ?>
<div class="dokan-product-shipping-tax dokan-edit-row dokan-clearfix <?php echo ( 'no' == get_option('woocommerce_calc_shipping') ) ? 'woocommerce-no-shipping' : '' ?> <?php echo ( 'no' == get_option('woocommerce_calc_taxes') ) ? 'woocommerce-no-tax' : '' ?>">
    <div class="dokan-side-left">
        <h2><?php _e( 'Shipping & Tax', 'dokan' ); ?></h2>

        <p>
            <?php _e( 'Manage shipping and tax for this product', 'dokan' ); ?>
        </p>
    </div>

    <div class="dokan-side-right">
        <?php
            $dokan_shipping_option  = get_option( 'woocommerce_dokan_product_shipping_settings' );
            $dokan_shipping_enabled = ( isset( $dokan_shipping_option['enabled'] ) ) ? $dokan_shipping_option['enabled'] : 'yes';
            $store_shipping         = get_user_meta( get_current_user_id(), '_dps_shipping_enable', true );
        ?>
        <?php if( 'yes' == get_option('woocommerce_calc_shipping') ): ?>
            <div class="dokan-clearfix hide_if_downloadable dokan-shipping-container">
                <input type="hidden" name="product_shipping_class" value="0">
                <div class="dokan-form-group">
                    <label class="dokan-checkbox-inline form-label " for="_disable_shipping">
                        <input type="checkbox" id="_disable_shipping" name="_disable_shipping" <?php checked( $_disable_shipping, 'no' ); ?>>
                        <?php _e( 'This product requires shipping', 'dokan' ); ?>
                    </label>
                </div>
                <div class="show_if_needs_shipping dokan-form-group ec-extras">
                    <?php $is_fragile = ( get_post_meta($post_id, '_fragile_cargo', true) == 'yes' ) ? 'yes' : 'no'; ?>
                    <?php dokan_post_input_box( $post_id, '_fragile_cargo', array("class"=>"form-label", 'value' => $is_fragile, 'label' => __( 'Fragile cargo', 'ktt' ) ), 'checkbox' ); ?>
                    <?php $is_food = ( get_post_meta($post_id, '_food_cargo', true) == 'yes' ) ? 'yes' : 'no'; ?>
                    <?php dokan_post_input_box( $post_id, '_food_cargo', array("class"=> "form-label" , 'value' => $is_food, 'label' => __( 'Is food', 'ktt' ) ), 'checkbox' ); ?>
				</div>
                <div class="show_if_needs_shipping row">
					<div class="col-md-3 cont-labels">
                        
	                    <label class="form-label"><?php _e( 'length', 'dokan' ) ?></label>
	                    <?php dokan_post_input_box( $post_id, '_length', array( 'class' => 'form-control' ), 'number' ); ?>
					</div>
					<div class="col-md-3">
	                    <label class="form-label"><?php _e( 'width', 'dokan' ) ?></label>
	                    <?php dokan_post_input_box( $post_id, '_width', array( 'class' => 'form-control' ), 'number' ); ?>
					</div>
					<div class="col-md-3">
	                    <label class="form-label"><?php _e( 'height', 'dokan' ) ?></label>
	                    <?php dokan_post_input_box( $post_id, '_height', array( 'class' => 'form-control' ), 'number' ); ?>
					</div>
					<div class="col-md-3">
	                    <label class="form-label "><?php _e( 'Dimension unit', 'dokan' ) ?></label>
						<select name="lb-dimension-unit" class="select-label selects">
							<option <?= get_option( 'woocommerce_dimension_unit' ) == 'mm'? 'selected':'' ?>>mm</option>
							<option <?= get_option( 'woocommerce_dimension_unit' ) == 'cm'? 'selected':'' ?>>cm</option>
							<option <?= get_option( 'woocommerce_dimension_unit' ) == 'm'? 'selected':'' ?>>m</option>
						</select>
						<input type="hidden" name="lb-dimension-woocom-unit" value="<?= get_option( 'woocommerce_dimension_unit' ) ?>">
					</div>
                </div>
<?php /* Sven: Redesigned above
                <div class="show_if_needs_shipping dokan-shipping-dimention-options">
                    <?php // dokan_post_input_box( $post_id, '_weight', array( 'class' => 'form-control', 'placeholder' => __( 'weight (' . esc_html( get_option( 'woocommerce_weight_unit' ) ) . ')', 'dokan' ) ), 'number' ); ?>
                    <?php dokan_post_input_box( $post_id, '_length', array( 'class' => 'form-control', 'placeholder' => __( 'length', 'dokan' ) ), 'number' ); ?>
                    <?php dokan_post_input_box( $post_id, '_width', array( 'class' => 'form-control', 'placeholder' => __( 'width', 'dokan' ) ), 'number' ); ?>
                    <?php dokan_post_input_box( $post_id, '_height', array( 'class' => 'form-control', 'placeholder' => __( 'height', 'dokan' ) ), 'number' ); ?>
                    <div class="dokan-clearfix"></div>
                </div>
                <div class="show_if_needs_shipping dokan-shipping-dimention-options">
                    <label class="control-label"><?php _e( 'Dimension measurements unit', 'dokan' ); ?></label>
                    <select name="lb-dimension-unit">
                        <option <?= get_option( 'woocommerce_dimension_unit' ) == 'mm'? 'selected':'' ?>>mm</option>
                        <option <?= get_option( 'woocommerce_dimension_unit' ) == 'cm'? 'selected':'' ?>>cm</option>
                        <option <?= get_option( 'woocommerce_dimension_unit' ) == 'm'? 'selected':'' ?>>m</option>
                    </select>
                    <input type="hidden" name="lb-dimension-woocom-unit" value="<?= get_option( 'woocommerce_dimension_unit' ) ?>">
                </div>
*/ ?>

                <?php if ( $post_id ): ?>
                    <?php do_action( 'dokan_product_options_shipping' ); ?>
                <?php endif; ?>
                <div class="show_if_needs_shipping dokan-form-group row">
					<div class="col-md-3">
	                    <label class="form-label"><?php _e( 'weight (' . esc_html( get_option( 'woocommerce_weight_unit' ) ) . ')', 'dokan' ); ?></label>
	                    <?php dokan_post_input_box( $post_id, '_weight', array( 'class' => 'form-control' ), 'number' ); ?>
					</div>
					<div class="col-md-9">
						<label class="form-label" for="product_shipping_class"><?php _e( 'Shipping Class', 'dokan' ); ?></label>
						<div class="dokan-text-left">
							<?php
							// Shipping Class
							$classes = get_the_terms( $post->ID, 'product_shipping_class' );
							if ( $classes && ! is_wp_error( $classes ) ) {
								$current_shipping_class = current($classes)->term_id;
							} else {
								$current_shipping_class = '';
							}

							$args = array(
								'taxonomy'          => 'product_shipping_class',
								'hide_empty'        => 0,
								'show_option_none'  => __( 'No shipping class', 'dokan' ),
								'name'              => 'product_shipping_class',
								'id'                => 'product_shipping_class',
								'selected'          => $current_shipping_class,
								'class'             => 'dokan-form-control'
							);
							?>

							<?php wp_dropdown_categories( $args ); ?>
							<p class="ec-form-field-description"><?php _e( 'Shipping classes are used by certain shipping methods to group similar products.', 'dokan' ); ?></p>
						</div>
					</div>
                </div>
                <?php if( $dokan_shipping_enabled == 'yes' && $store_shipping == 'yes' ) : ?>
                    <div class="show_if_needs_shipping dokan-shipping-product-options">

                        <div class="dokan-form-group">
                            <?php dokan_post_input_box( $post_id, '_overwrite_shipping', array( 'label' => __( 'Override default shipping cost for this product', 'dokan' ) ), 'checkbox' ); ?>
                        </div>

                        <div class="dokan-form-group show_if_override">
                            <label class="dokan-control-label" for="_additional_product_price"><?php _e( 'Additional cost', 'dokan' ); ?></label>
                            <input id="_additional_product_price" value="<?php echo $_additional_price; ?>" name="_additional_price" placeholder="9.99" class="dokan-form-control" type="number" step="any">
                        </div>

                        <div class="dokan-form-group show_if_override">
                            <label class="dokan-control-label" for="dps_additional_qty"><?php _e( 'Per Qty Additional Price', 'dokan' ); ?></label>
                            <input id="additional_qty" value="<?php echo ( $_additional_qty ) ? $_additional_qty : $dps_additional_qty; ?>" name="_additional_qty" placeholder="1.99" class="dokan-form-control" type="number" step="any">
                        </div>

                        <div class="dokan-form-group show_if_override">
                            <label class="dokan-control-label" for="dps_additional_qty"><?php _e( 'Processing Time', 'dokan' ); ?></label>
                            <select name="_dps_processing_time" id="_dps_processing_time" class="dokan-form-control selects">
                                <?php foreach ( $processing_time as $processing_key => $processing_value ): ?>
                                      <option value="<?php echo $processing_key; ?>" <?php selected( $porduct_shipping_pt, $processing_key ); ?>><?php echo $processing_value; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="show_if_needs_shipping">
                    <div class="dokan-form-group">
                        <label class="dokan-control-label form-label "><?php _e('Ready to ship time for products in stock', 'ktt'); ?></label>
                        <input id="_expected_delivery_in_warehouse" type="hidden" name="_expected_delivery_in_warehouse" value="<?php echo get_post_meta( $post_id, '_expected_delivery_in_warehouse', true);?>" />
                        <select name="expectedDeliveryPicker" onchange="updateExpectedDelivery()" id="expectedDeliveryPickerProd" >
                        <?php
						$options  = ec_get_shipping_durations_array();                        
                        
                        
                        foreach($options as $k=>$v){
                        	$meta = get_post_meta( $post_id, '_expected_delivery_in_warehouse', true);
                        	if($k == $meta){
                        		echo '<option value='.$k.' selected>'.$v.'</option>';
                        	} else {
                        		echo '<option value='.$k.'>'.$v.'</option>';
                        	}
                        }
                        
                        /*
                         dokan_post_input_box(
                            $post_id, 
                            '_expected_delivery_in_warehouse', 
                            array( 
                                'placeholder' => __( 'Ready to ship time for products in stock', 'ktt' ),
                                'value' => get_post_meta( $post_id, '_expected_delivery_in_warehouse', true)
                            ),
                            'text' 
                        ); 
                        */
                        
                        
                        
                        ?>
                        </select>
                        <script type="text/javascript">
                        	function updateExpectedDelivery(){
								var exDel = jQuery("#expectedDeliveryPickerProd").val();
								   if(exDel == 0){
								   jQuery("#_expected_delivery_in_warehouse").val("");                        	
                        			
								   }else{
									jQuery("#_expected_delivery_in_warehouse").val(exDel);                        	
                        			}
                        	}
                        </script>
                    </div>
                    <div class="dokan-form-group">
                        <label class="dokan-control-label form-label "><?php _e('Ready to ship time for products not in stock', 'ktt'); ?></label>
                        <?php dokan_post_input_box(
                            $post_id, 
                            '_expected_delivery_no_warehouse', 
                            array( 
                                'placeholder' => __( 'Ready to ship time for products not in stock', 'ktt' ),
                                'value' => get_post_meta( $post_id, '_expected_delivery_no_warehouse', true)
                            ),
                            'text' 
                        ); ?>
                        
                        
                         <p class="ec-form-field-description">Please fill the blank in a simple and understandable fashion. For example: 1 week; up to 3 days; minimum 2 weeks.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( 'yes' == get_option('woocommerce_calc_shipping') && 'yes' == get_option( 'woocommerce_calc_taxes' ) ): ?>
        <!--    <div class="dokan-divider-top hide_if_downloadable"></div> !-->
        <?php endif ?>
 		<input type="hidden" name="_required_tax" value="yes">
        <?php if ( 'yes' == get_option( 'woocommerce_calc_taxes' ) ) { ?>
      
        <!--  <div class="dokan-clearfix dokan-tax-container">
          <div class="dokan-form-group">
                <label for="_required_tax" class="form-label ">
                <input type="hidden" name="_required_tax" value="yes">
                <?php _e( 'The product requires Tax', 'dokan' ); ?>
                </label>
            </div>
          
            <div class="show_if_needs_tax dokan-tax-product-options">
                <div class="dokan-form-group dokan-w">
                    <label class="form-label" for="_tax_status"><?php _e( 'Tax Status', 'dokan' ); ?></label>
                    <div class="dokan-text-left">
                        <?php dokan_post_input_box( $post_id, '_tax_status', array( 'options' => array(
                            'taxable'   => __( 'Taxable', 'dokan' ),
                            'shipping'  => __( 'Shipping only', 'dokan' ),
                            'none'      => _x( 'None', 'Tax status', 'dokan' )
                            ) ), 'select'
                        ); ?>
                    </div>
                </div>

                <div class="dokan-form-group dokan-w">
                    <label class="form-label" for="_tax_class"><?php _e( 'Tax Class', 'dokan' ); ?></label>
                    <div class="dokan-text-left">
                        <?php dokan_post_input_box( $post_id, '_tax_class', array( 'options' => $classes_options ), 'select' ); ?>
                    </div>
                </div>
            </div>
        </div>
          --!>
        <?php } ?>
    </div><!-- .dokan-side-right -->
</div><!-- .dokan-product-inventory -->
<?php endif; ?>

<?php do_action( 'dokan_product_edit_after_shipping', $post_id ); ?>

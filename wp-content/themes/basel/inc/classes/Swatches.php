<?php 
/**
 * Color and Images swatches for WooCommerce products attributes
 */


class BASEL_Swatches {

	public function __construct() {

		// Init swatches after CMB plugin loaded
		// on init hook after 199

		add_action( 'cmb2_init', array( $this, 'init' ), 299 );

	}

	public function init() {
		if( ! class_exists( 'Taxonomy_MetaData_CMB2' ) ) return;

		$this->add_new_fields();
	}

	private function add_new_fields() {
		if( ! function_exists('wc_get_attribute_taxonomies') ) return;
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		foreach ($attribute_taxonomies as $key => $value) {

			$fileds = array(
				array(
                    'name' => 'Enable swatch',
                    'desc' => 'Attribute dropdown will be replaces with squared buttons',
                    'id' => 'not_dropdown',
                    'type' => 'checkbox'
            	),
				array(
                    'name' => 'Image preview for this value',
                    'desc' => 'Upload an image',
                    'id' => 'image',
		            'type' => 'file',
		            'allow' => array( 'url', 'attachment' ) // limit to just attachments with array( 'attachment' )
            	),
				array(
                    'name' => 'Color preview for this value',
                    'desc' => 'Select color',
                    'id' => 'color',
                    'type' => 'colorpicker'
            	),
			);

	        $attribute_field = array(
	            'id'         => 'pa_fields_'.$value->attribute_name,
	            // 'key' and 'value' should be exactly as follows
	            'show_names' => true, // Show field names on the left
	            'fields'     => $fileds
	        );

	        /**
	         * Instantiate our taxonomy meta class
	         */
	        new Taxonomy_MetaData_CMB2( 'pa_'.$value->attribute_name, $attribute_field );
		}


	}
}

if( ! function_exists( 'basel_has_swatches' ) ) {
	function basel_has_swatches( $id, $attr_name, $options, $available_variations, $swatches_use_variation_images = false ) {
		$swatches = array();

		foreach ($options as $key => $value) {
			$swatch = basel_has_swatch($id, $attr_name, $value);

			if( ! empty( $swatch ) ) {

				if( $swatches_use_variation_images && basel_get_opt( 'grid_swatches_attribute' ) == $attr_name ) {

					$variation = basel_get_option_variations( $attr_name, $available_variations, $value );

					$swatch = array_merge( $swatch, $variation);
				}

				$swatches[$key] = $swatch;
			}
		}

		return $swatches;
	}
}

if( ! function_exists( 'basel_has_swatch' ) ) {
	function basel_has_swatch($id, $attr_name, $value) {
		$swatches = array();

		$color = $image = '';
		$term = get_term_by( 'slug', $value, $attr_name );
		$color = basel_tax_data( $attr_name, $term->term_id, 'color' );
		$image = basel_tax_data( $attr_name, $term->term_id, 'image' );
		$not_dropdown = basel_tax_data( $attr_name, $term->term_id, 'not_dropdown' );

		if( $color != '' ) {
			$swatches['color'] = $color;
		}

		if( $image != '' ) {
			$swatches['image'] = $image;
		}

		if( $not_dropdown != '' ) {
			$swatches['not_dropdown'] = $not_dropdown;
		}

		return $swatches;
	}
}

if( ! function_exists( 'basel_get_option_variations' ) ) {
	function basel_get_option_variations( $attribute_name, $available_variations, $option = false, $product_id = false ) {
		$swatches_to_show = array();
		foreach ($available_variations as $key => $variation) {
			$option_variation = array();
			$attr_key = 'attribute_' . $attribute_name;
			if( ! isset( $variation['attributes'][$attr_key] )) return;

			$val = $variation['attributes'][$attr_key]; // red green black ..
			if( ! empty( $variation['image_src'] ) ) {
				$option_variation = array(
					'variation_id' => $variation['variation_id'],
					'image_src' => $variation['image_src'],
					'image_srcset' => $variation['image_srcset'],
					'image_sizes' => $variation['image_sizes'],
					'is_in_stock' => $variation['is_in_stock'],
				);
			}

			// Get only one variation by attribute option value 
			if( $option ) {
				if( $val != $option ) {
					continue;
				} else {
					return $option_variation;
				}
			} else {
				// Or get all variations with swatches to show by attribute name
				
				$swatch = basel_has_swatch($product_id, $attribute_name, $val);
				$swatches_to_show[$val] = array_merge( $swatch, $option_variation);

			}

		}

		return $swatches_to_show;

	}
}

?>

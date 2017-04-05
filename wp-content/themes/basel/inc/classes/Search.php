<?php 
/**
 * Ajax search
 */


class BASEL_Search {

	public function __construct() {
		add_action( 'wp_ajax_basel_ajax_search', array( $this, 'ajax_suggestions') );
		add_action( 'wp_ajax_nopriv_basel_ajax_search', array( $this, 'ajax_suggestions') );
	}

	public function ajax_suggestions() {

		$query_args = array(
			'posts_per_page' => 3,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'no_found_rows'  => 1,
			//'order'          => $order,
			'meta_query'     => array()
		);

		if( ! empty( $_REQUEST['query'] ) ) {
			$query_args['s'] = sanitize_text_field( $_REQUEST['query'] );
		}

		if( ! empty( $_REQUEST['number'] ) ) {
			$query_args['posts_per_page'] = (int) $_REQUEST['number'];
		}

		if( ! empty( $_REQUEST['product_cat'] ) ) {
			$query_args['product_cat'] = strip_tags($_REQUEST['product_cat']);
		}

		$products = new WP_Query( apply_filters( 'basel_ajax_get_products', $query_args ) );

		$suggestions = array();

		if( $products->have_posts() ) {

			$factory = new WC_Product_Factory();

			$wptexturize = remove_filter( 'the_title', 'wptexturize' );
				
			while( $products->have_posts() ) {
				$products->the_post();

				$product = $factory->get_product( get_the_ID() );

				$title       = get_the_title();

				$suggestions[] = array(
					'value' => $title,
					'permalink' => get_the_permalink(),
					'price' => $product->get_price_html(),
					'thumbnail' => $product->get_image(),
				);
			}

			if ( $wptexturize )
			    add_filter( 'the_title', 'wptexturize' );

			wp_reset_postdata();
		} else {
			$suggestions[] = array(
				'value' => __( 'No products found', 'basel' ),
				'permalink' => ''
			);
		}


		echo json_encode( array(
			'suggestions' => $suggestions
		) );

		die();
	}

}

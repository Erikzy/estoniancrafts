<?php
include_once(get_stylesheet_directory().'/Blocks/EC_Block.php');

class EC_Merchant_Products extends EC_Block
{
	public $post;
	public $actions;				// array of EC_Block
	public $products;				// array of EC_Block
	public $products_query;			// WP_Query
	public $products_pagination;	// EC_Block

	/**
	 * @param WP_Post $post
	 * @return \EC_Merchant_Products
	 */
	public function load($post)
	{
		$this->post = $post;
		$this->loadActions();
		$this->loadProducts();
		$this->loadProductsPagination();

		return $this;
	}

	public function loadActions()
	{
		$this->actions = array();

		// Add new product
		$action = new EC_Block();
		$action->id = 'new_product';
		$action->url = dokan_get_navigation_url( 'new-product' );
		$action->title = translate( 'Add new product', 'dokan' );
		$this->actions[] = $action;
	}

	public function loadProducts()
	{
		global $post;

		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

		$post_statuses = array('publish', 'draft', 'pending');
		$args = array(
			'post_type'      => 'product',
			'post_status'    => $post_statuses,
			'posts_per_page' => 10,
			'author'         => get_current_user_id(),
			'orderby'        => 'post_date',
			'order'          => 'DESC',
			'paged'          => $pagenum,
			'tax_query'      => array(
									array(
										'taxonomy' => 'product_type',
										'field'    => 'slug',
										'terms'    => apply_filters( 'dokan_product_listing_exclude_type', array() ),
										'operator' => 'NOT IN',
									),
								),
		);

		if ( isset( $_GET['post_status']) && in_array( $_GET['post_status'], $post_statuses ) ) {
			$args['post_status'] = $_GET['post_status'];
		}

		if( isset( $_GET['date'] ) && $_GET['date'] != 0 ) {
			$args['m'] = $_GET['date'];
		}

		if( isset( $_GET['product_cat'] ) && $_GET['product_cat'] != -1 ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => (int)  $_GET['product_cat'],
				'include_children' => false,
			);
		}

		if ( isset( $_GET['product_search_name']) && !empty( $_GET['product_search_name'] ) ) {
			$args['s'] = $_GET['product_search_name'];
		}

		$this->products = array();
		$this->product_query = new WP_Query( apply_filters( 'dokan_product_listing_query', $args ) );

		if($this->product_query->have_posts())
		{
			while ($this->product_query->have_posts())
			{
				$this->product_query->the_post();
				$product = get_product( $post->ID );

				$productCls = new EC_Block();
				$productCls->post = $post;
				$productCls->post_id = $post->ID;
				$productCls->product = $product;
				$productCls->id = $product->ID;
				$productCls->type = $product->get_price_html();
				$productCls->title = $product->get_title();
				$productCls->sku = $product->get_sku();
				$productCls->image_html = $product->get_image();
				$productCls->status = $post->post_status;
				$productCls->price_html = $product->get_price_html();
				$productCls->managing_stock = $product->managing_stock();
				$productCls->is_in_stock = $product->is_in_stock();
				$productCls->total_stock = $product->get_total_stock();
				$productCls->tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';
				$productCls->nr_of_views = get_post_meta( $post->ID, 'pageview', true );
				$productCls->date = $post->post_date;

				$this->products[] = $productCls;
			}
		}
	}

	public function loadProductsPagination()
	{
		if(is_null($this->products_query)) {
			return false;
		}
		if($this->product_query->max_num_pages <= 1) {
			return true;
		}

		wp_reset_postdata();

		$base_url = dokan_get_navigation_url('products');
		$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

		$pagination = new EC_Block();
		$pagination->current_page_num = $pagenum;
		$pagination->total_pages = $this->product_query->max_num_pages;
		$pagination->links = paginate_links( array(
			'current'   => $pagination->current_page_num,
			'total'     => $pagination->total_pages,
			'base'      => $base_url. '%_%',
			'format'    => '?pagenum=%#%',
			'add_args'  => false,
			'type'      => 'array',
			'prev_text' => __( '&laquo; Previous', 'dokan' ),
			'next_text' => __( 'Next &raquo;', 'dokan' )
		) );

		$this->products_pagination = $pagination;
	}

	/**
	 * @param string $id
	 * @return \EC_Block
	 */
	public function getAction($id)
	{
		if(empty($this->actions)) {
			return null;
		}

		foreach($this->actions as $action)
		{
			if($action->id == $id)
			{
				return $action;
			}
		}

		return null;
	}
}
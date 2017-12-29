<?php global $post; ?>

<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-product-listing">

        <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_before' );
            do_action( 'dokan_before_listing_product' );
        ?>

		<?php $page = apply_filters('ec_get_page_merchant_products', null) ?>
        <article class="dokan-product-listing-area">

            <div class="product-listing-top dokan-clearfix">

				<?php // Search filters ?>
                <?php dokan_product_listing_status_filter(); ?>

				<?php // Actions ?>
				<?php if(!empty($page->actions)): ?>
                <span class="dokan-add-product-link">

					<?php // New product ?>
					<?php if(($action = $page->getAction('new_product'))): ?>
						<a href="<?= $action->url ?>" class="dokan-btn dokan-btn-theme dokan-right"><i class="fa fa-briefcase">&nbsp;</i> <?= $action->title ?></a>
					<?php endif; ?>

                </span>
				<?php endif; ?>
            </div>

            <?php dokan_product_dashboard_errors(); ?>

            <div class="dokan-w12">
                <?php dokan_product_listing_filter(); ?>
            </div>

			<?php // Products table ?>
			<h1><?= $page->title ?></h1>
            <table class="product-listing-table ">
                <thead>
                    <tr>
                        <th><?php _e( 'Image', 'dokan' ); ?></th>
                        <th><?php _e( 'Name', 'dokan' ); ?></th>
                        <th><?php _e( 'Status', 'dokan' ); ?></th>
                        <th><?php _e( 'Statistics', 'dokan' ); ?></th>
                        <th><?php _e( 'Stock', 'dokan' ); ?></th>
                        <th><?php _e( 'Price', 'dokan' ); ?></th>
                        <th><?php _e( 'Date', 'dokan' ); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
					<?php if(!empty($page->products)): ?>
						<?php foreach($page->products as $product): ?>

                            <tr<?php echo $product->tr_class; ?>>
                                <td data-title="<?php _e( 'Image', 'dokan' ); ?>">
                                    <a href="<?php echo dokan_edit_product_url( $product->post_id ); ?>"><?php echo $product->image_html ?></a>
                                </td>
                                <td data-title="<?php _e( 'Name', 'dokan' ); ?>">
                                    <p>
										<a href="<?php echo dokan_edit_product_url( $product->post_id ); ?>"><?php echo $product->title ?></a>
										<br />
										<?php
										if ( $product->sku ) {
											echo sprintf('%s: %s',
												translate( 'SKU', 'dokan' ),
												$product->sku
											);
										} else {
											echo '<span class="na">&ndash;</span>';
										}
										?>
									</p>
                                </td>
                                <td class="post-status" data-title="<?php _e( 'Status', 'dokan' ); ?>">
                                    <label class="dokan-label <?php echo $product->status; ?>"><?php echo dokan_get_post_status( $product->status ); ?></label>
                                </td>
                                <td>
                                    <a href="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php?action=get_product_statistics&product_id=' . $product->post_id), 'ec_get_product_statistics' ) ?>" class="get-product-statistics" style="font-size: 22px;"><i class="fa fa-bar-chart">&nbsp;</i></a>
                                </td>
                                <td data-title="<?php _e( 'Stock', 'dokan' ); ?>">
                                    <?php
                                    if ( $product->is_in_stock ) {
                                        echo '<mark class="instock">' . __( 'In stock', 'woocommerce' ) . '</mark>';
                                    } else {
                                        echo '<mark class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</mark>';
                                    }

                                    if ( $product->managing_stock ) :
                                        echo ' &times; ' . $product->total_stock;
                                    endif;
                                    ?>
                                </td>
                                <td data-title="<?php _e( 'Price', 'dokan' ); ?>">
                                    <?php
                                    if ( $product->price_html ) {
                                        echo $product->price_html;
                                    } else {
                                        echo '<span class="na">&ndash;</span>';
                                    }
                                    ?>
                                </td>
                                <td class="post-date" data-title="<?php _e( 'Date', 'dokan' ); ?>">
                                    <?php
                                    if ( '0000-00-00 00:00:00' == $product->date ) {
                                        $t_time = $h_time = __( 'Unpublished', 'dokan' );
                                        $time_diff = 0;
                                    } else {
                                        $t_time = get_the_time( __( 'Y/m/d g:i:s A', 'dokan' ) );
                                        $m_time = $product->date;
                                        $time = get_post_time( 'G', true, $post );

                                        $time_diff = time() - $time;

                                        if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 ) {
                                            $h_time = sprintf( __( '%s ago', 'dokan' ), human_time_diff( $time ) );
                                        } else {
                                            $h_time = mysql2date( __( 'Y/m/d', 'dokan' ), $m_time );
                                        }
                                    }

                                    echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $product->post, 'date', 'all' ) . '</abbr>';
                                    echo '<div class="status">';
                                    if ( 'publish' == $product->status ) {
                                        _e( 'Published', 'dokan' );
                                    } elseif ( 'future' == $product->status ) {
                                        if ( $time_diff > 0 ) {
                                            echo '<strong class="attention">' . __( 'Missed schedule', 'dokan' ) . '</strong>';
                                        } else {
                                            _e( 'Scheduled', 'dokan' );
                                        }
                                    } else {
                                        _e( 'Last Modified', 'dokan' );
                                    }
                                    ?>
                                    </div>
                                </td>
                                <td>
									<div class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
											<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
										</a>
										<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
											<li role="presentation" class="edit"><a href="<?php echo dokan_edit_product_url( $product->post_id ); ?>"><?php _e( 'Edit', 'dokan' ); ?></a></li>
											<li role="presentation" class="view"><a href="<?php echo get_permalink( $product->id ); ?>" rel="permalink"><?php _e( 'View', 'dokan' ); ?></a></li>
											<li role="separator" class="divider"></li>
											<?php /* 
                                            :temporarily disabled based on requirement 2.2 "2.2 PDF hinnasildid - funktsioon hetkel kinni panna" - <marek@dolmit.com>
                                            ?>
                                            <li role="presentation"><a href="<?= site_url().'/lbpdf/pricetag-a4/?id='.$product->post_id ?>" target="_blank" role="menuitem" tabindex="-1">Print PDF A4</a></li>
											<li role="presentation"><a href="<?= site_url().'/lbpdf/pricetag-a5/?id='.$product->post_id ?>" target="_blank" role="menuitem" tabindex="-1">Print PDF A5</a></li>
											<li role="presentation"><a href="<?= site_url().'/lbpdf/pricetag-bcard/?id='.$product->post_id ?>" target="_blank" role="menuitem" tabindex="-1">Print PDF bcard</a></li>
											<li role="separator" class="divider"></li>
                                            <?php */ ?>
											<li role="presentation" class="delete"><a onclick="return confirm('<?php _e('Are you sure?', 'ktt') ?>');" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'dokan-delete-product', 'product_id' => $product->post_id ), dokan_get_navigation_url('products') ), 'dokan-delete-product' ); ?>"><?php _e( 'Delete Permanently', 'dokan' ); ?></a></li>
										</ul>
									</div>
                                </td>
                                <!-- <td class="diviader"></td> -->
                            </tr>

						<?php endforeach; ?>
					<?php else: ?>

                        <tr>
                            <td colspan="7"><?php _e( 'No product found', 'dokan' ); ?></td>
                        </tr>

					<?php endif; ?>

                </tbody>
            </table>

			<?php // Pagination   ?>
			<?php if($page->products_pagination): ?>

				<!-- <div class="pagination-wrap"> -->
                <nav class="woocommerce-pagination">
					<ul class="page-numbers">
						<li>
							<?php echo join("</li>\n\t<li>", $page->products_pagination->links) ?>
						</li>
					</ul>
				<!-- </div> -->
            </nav>

			<?php endif; ?>

        </article>

        <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_after' );
            do_action( 'dokan_after_listing_product' );
        ?>

    </div><!-- #primary .content-area -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->



<?php
if ( $sellers['users'] ) {
    ?>
    <ul class="dokan-seller-wrap">
        <?php
        foreach ( $sellers['users'] as $seller ) {
            $store_info = dokan_get_store_info( $seller->ID );
            $banner_id  = isset( $store_info['banner'] ) ? $store_info['banner'] : 0;
            $store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'dokan' );
            $store_url  = dokan_get_store_url( $seller->ID );
            ?>

            <li class="dokan-single-seller">
                <div class="dokan-store-thumbnail">

                    <div class="dokan-store-banner-wrap">
                        <a href="<?php echo $store_url; ?>">
                            <?php if ( $banner_id ) {
                                $banner_url = wp_get_attachment_image_src( $banner_id, $image_size );
                                
                                ?>
                                <img class="dokan-store-img" src="<?php echo esc_url( $banner_url[0] ); ?>" alt="<?php echo esc_attr( $store_name ); ?>">

                            <?php } else { ?>
                                <img class="dokan-store-img" src="<?php echo dokan_get_no_seller_image(); ?>" alt="<?php _e( 'No Image', 'dokan' ); ?>">
                            <?php } ?>
                        </a>
                    </div>

                    <div class="dokan-store-caption">
                        <h3><a href="<?php echo $store_url; ?>"><?php echo $store_name; ?></a></h3>

                        <address>

                            <?php if ( isset( $store_info['address'] ) && !empty( $store_info['address'] ) ) {
                                echo dokan_get_seller_address( $seller->ID );
                            } ?>



                        </address>

                        <p><a class="button  call-to-action-button " href="<?php echo $store_url; ?>"><?php _e( 'Visit Store', 'dokan' ); ?></a></p>

                    </div> <!-- .caption -->
                </div> <!-- .thumbnail -->
            </li> <!-- .single-seller -->
        <?php } ?>
    </ul> <!-- .dokan-seller-wrap -->

    <?php
    $user_count   = $sellers['count'];
    $num_of_pages = ceil( $user_count / $limit );

    if ( $num_of_pages > 1 ) {
        echo '<div class="pagination-container clearfix ">';

        $pagination_args = array(
            'current'   => $paged,
            'total'     => $num_of_pages,
            'base'      => $pagination_base,
            'type'      => 'array',
            'prev_text' => __( '&larr; Previous', 'dokan' ),
            'next_text' => __( 'Next &rarr;', 'dokan' ),
        );

        if ( ! empty( $search_query ) ) {
            $pagination_args['add_args'] = array(
                'dokan_seller_search' => $search_query,
            );
        }

        $page_links = paginate_links( $pagination_args );

        if ( $page_links ) {
            $pagination_links  = '<div class="woocommerce-pagination">';
            $pagination_links .= '<ul class="page-numbers"><li class="page-numbers">';
            $pagination_links .= join( "</li>\n\t<li>", $page_links );
            $pagination_links .= "</li>\n</ul>\n";
            $pagination_links .= '</div>';

            echo $pagination_links;
        }

        echo '</div>';
    }
    ?>

<?php } else { ?>
    <p class="dokan-error"><?php _e( 'No seller found!', 'dokan' ); ?></p>
<?php } ?>
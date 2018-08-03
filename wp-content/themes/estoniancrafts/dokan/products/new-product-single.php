<?php

global $post;

$from_shortcode = false;



if( isset( $post->ID ) && $post->ID && $post->post_type == 'product' ) {

    if ( $post->post_author != get_current_user_id() ) {
        wp_die( __( 'Access Denied', 'dokan' ) );
    }

    $post_id = $post->ID;
    $post_title = $post->post_title;
    $post_content = $post->post_content;
    $post_excerpt = $post->post_excerpt;
    $post_status = $post->post_status;
} else {
    $post_id = NULL;
    $post_title = '';
    $post_content = '';
    $post_excerpt = '';
    $post_status = 'pending';
    $from_shortcode = true;

}

if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
    $post_title     = $post->post_title;
    $post_content   = $post->post_content;
    $post_excerpt   = $post->post_excerpt;
    $post_status    = $post->post_status;
    $product        = get_product( $post_id );
    $from_shortcode = true;
}

$_regular_price         = get_post_meta( $post_id, '_regular_price', true );
$_sale_price            = get_post_meta( $post_id, '_sale_price', true );
$is_discount            = !empty( $_sale_price ) ? true : false;
$_sale_price_dates_from = get_post_meta( $post_id, '_sale_price_dates_from', true );
$_sale_price_dates_to   = get_post_meta( $post_id, '_sale_price_dates_to', true );

$_sale_price_dates_from = !empty( $_sale_price_dates_from ) ? date_i18n( 'Y-m-d', $_sale_price_dates_from ) : '';
$_sale_price_dates_to   = !empty( $_sale_price_dates_to ) ? date_i18n( 'Y-m-d', $_sale_price_dates_to ) : '';
$show_schedule          = false;

if ( !empty( $_sale_price_dates_from ) && !empty( $_sale_price_dates_to ) ) {
    $show_schedule = true;
}


$_featured              = get_post_meta( $post_id, '_featured', true );
$_downloadable          = get_post_meta( $post_id, '_downloadable', true );
$_is_lot_discount       = get_post_meta( $post_id, '_is_lot_discount', true );
$_lot_discount_quantity = get_post_meta( $post_id, '_lot_discount_quantity', true );
$_lot_discount_amount   = get_post_meta( $post_id, '_lot_discount_amount', true );
$is_enable_op_discount  = dokan_get_option( 'discount_edit', 'dokan_selling' );
$is_enable_op_discount  = $is_enable_op_discount ? $is_enable_op_discount : array();
$_stock                 = get_post_meta( $post_id, '_stock', true );
$_stock_status          = get_post_meta( $post_id, '_stock_status', true );

$_size_chart            = get_post_meta( $post_id, 'size_chart');
$_visibility            = get_post_meta( $post_id, '_visibility', true );
$_enable_reviews        = $post->comment_status;


if ( ! $from_shortcode ) {
    get_header();
}



?>

<?php

    /**
     *  dokan_dashboard_wrap_before hook
     *
     *  @since 2.4
     */
    do_action( 'dokan_dashboard_wrap_before', $post, $post_id );
?>

<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_before_product_content_area hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_before_product_content_area' );
    ?>

    <div class="dokan-dashboard-content dokan-product-edit">

        <?php

            /**
             *  dokan_product_content_inside_area_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_product_content_inside_area_before' );

        ?>

        <header class="dokan-dashboard-header dokan-clearfix">
            <?php 
            if(isset($_POST["dokan_product_id"])){
               
              /*      $post_title = isset($_POST["post_title"]) ? $_POST["post_title"] : ""; 
                    $product_cat = isset($_POST["product_cat"]) ? $_POST["product_cat"] : "";
                    $regular_price = isset($_POST["_regular_price"]) ? $_POST["_regular_price"]:"0.00";*/
                 }

             ?>
            <h1 class="entry-title">
                <?php if ( !$post_id ): ?>
                    <?php _e( 'Add New Product', 'dokan' ); ?>
                <?php else: ?>
                    <?php _e( 'Edit Product', 'dokan' ); ?>
                    <span class="dokan-label <?php echo dokan_get_post_status_label_class( $post->post_status ); ?> dokan-product-status-label">
                        <?php echo dokan_get_post_status( $post->post_status ); ?>
                    </span>

					<?php // View product button ?>
                       <span style="margin-left:15px;" class="dokan-right">
                            <a class="view-product dokan-btn dokan-btn-sm smaller-gray-button" href="<?php echo get_clone_link( $post->ID ); ?>" target="_blank"><?php _e( 'Make a copy', 'dokan' ); ?></a>
                        </span>
                    <?php if ( $post->post_status == 'publish' ) { ?>
                    
                     
                    
                        <span class="dokan-right">
                            <a class="view-product dokan-btn dokan-btn-sm smaller-gray-button" href="<?php echo get_permalink( $post->ID ); ?>" target="_blank"><?php _e( 'View Product', 'dokan' ); ?></a>
                        </span>
                    <?php } ?>

					<?php // Hidden indicator ?>
                    <?php if ( $_visibility == 'hidden' ) { ?>
                        <span class="dokan-right dokan-label dokan-label-default dokan-product-hidden-label"><i class="fa fa-eye-slash"></i> <?php _e( 'Hidden', 'dokan' ); ?></span>
                    <?php } ?>

                <?php endif ?>
            </h1>
        </header><!-- .entry-header -->
		<div class="dokan-alert dokan-alert-info"><p>Please enter product information in English! Support for other languages will be added soon.</p></div>
        <?php lbDokan::display_product_completeness($post_id); ?>

        <div class="product-edit-new-container">
            <?php if ( Dokan_Template_Products::$errors  &&  isset($_POST["dokan_add_product"]))  { ?>
                <div class="dokan-alert dokan-alert-danger">
                    <a class="dokan-close" data-dismiss="alert">&times;</a>

                    <?php foreach ( Dokan_Template_Products::$errors as $error) { ?>
                        <strong><?php _e( 'Error!', 'dokan' ); ?></strong> <?php echo $error ?>.<br>
                    <?php } ?>
                </div>
            <?php }
            elseif( Dokan_Template_Products::$draft_errors  &&  ( isset( $_POST["dokan_save_draft_product"] ) && $_POST["dokan_save_draft_product"]  == "true"  )  )  { ?>
                <div class="dokan-alert dokan-alert-danger">
                    <a class="dokan-close" data-dismiss="alert">&times;</a>

                    <?php foreach ( Dokan_Template_Products::$draft_errors as $error) { ?>
                        <strong><?php _e( 'Error!', 'dokan' ); ?></strong> <?php echo $error ?>.<br>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if ( isset( $_GET['message'] ) && $_GET['message'] == 'success') { ?>
                <div class="dokan-message">
                    <button type="button" class="dokan-close" data-dismiss="alert">&times;</button>
                    <strong><?php _e( 'Success!', 'dokan' ); ?></strong> <?php _e( 'The product has been saved successfully.', 'dokan' ); ?>

                    <?php if ( $post->post_status == 'publish' ) { ?>
                        <a href="<?php echo get_permalink( $post_id ); ?>" target="_blank"><?php _e( 'View Product &rarr;', 'dokan' ); ?></a>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php
            $can_sell = apply_filters( 'dokan_can_post', true );

            if ( $can_sell ) {

                if ( dokan_is_seller_enabled( get_current_user_id() ) ) { ?>
                    <script type="text/javascript">var ec_product_limits = <?= json_encode([
                        'maxLength' => (double)get_option('_product_max_length'),
                        'maxWidth' => (double)get_option('_product_max_width'),
                        'maxHeight' => (double)get_option('_product_max_height'),
                        'descriptionLimit' => (int)get_option('_product_description_limit'),
                        'shortDescriptionLimit' => (int)get_option('_product_short_description_limit')
                    ])?>;</script>
                    <form class="dokan-product-edit-form" role="form" method="post">
                        <input type="hidden" id="discription_limit" value="<?php echo get_option('discription_limit'); ?>">
                        
                        <?php if ( $post_id ): ?>
                            <?php do_action( 'dokan_product_data_panel_tabs' ); ?>
                        <?php endif; ?>
                        <?php 
                        

                        	do_action( 'dokan_product_edit_before_main' ); ?>

                        <div class="dokan-form-top-area">

                            <div class="content-half-part">

								<?php // Product title ?>
                                <div class="dokan-form-group">
                                    <input type="hidden" name="dokan_product_id" value="<?php echo $post_id; ?>"/>

                                    <label for="post_title" class="form-label desc-pro"><?php _e( 'Title ', 'dokan' ); ?><span class="required-m">*</span></label>
									<span class="ec-form-field-description"><?php _e( 'Product title description', 'ktt' ); ?></span>
                                    <div class="errfield">
                    
                                    </div>
                                    <?php dokan_post_input_box( $post_id, 'post_title', array( 'placeholder' => __( 'Product name..', 'dokan' ), 'value' => $post_title , "class"=> "dokan-w3 dokan-control-label") ); ?>
                                </div>



           						 <div class="dokan-form-group" style="margin-top:20px;">
                                    <input type="hidden" name="dokan_product_id" value="<?php echo $post_id; ?>"/>

                                    <label for="post_title" class="form-label desc-pro"><?php _e( 'Meta keywords', 'dokan' ); ?><span class="required-m">*</span></label>
									<span class="ec-form-field-description"><?php _e( 'Releated search tags', 'ktt' ); ?></span>
                                    <div class="errfield">
                    
                                    </div>
                                    <textarea rows="3" id="product-tags" name="product-tags" >
                                    
                                    </textarea>
                                    
                      	<!--  jquery tag  --->
                             		<script type="text/javascript">
                             			jQuery('#product-tags').tagEditor({
                             				maxLength:255,
                             				placeholder:"Enter tags related to your product to improve search appearance.",
                             				initialTags: [<?php 
                             					 $tags = get_post_meta( $post_id, 'product-tags', true );
                             					 $tagArray =  explode(",",$tags);
                             					 $glued = array();
                             					 foreach($tagArray as $tag){
                             					 	$glued[] = "'".$tag."'";
                             					 }
                             					 echo implode(",",$glued);
                             				 ?>]
                             			});
                             		</script>
                             		<style>
                             			.tag-editor  input[type=text]:focus,.tag-editor  input[type=email]:focus,.tag-editor  input[type=password]:focus,.tag-editor  input[type=number]:focus,.tag-editor  input[type=tel]:focus, .tag-editor select:focus,.tag-editor  textarea:focus {
                             				border:0px;
                             				padding:inherit;
                             			}
                             			.tag-editor:focus{
                             			border: 1px solid rgba(29,29,29,.5);
                             			}
                             			.tag-editor li{
                             				margin:5px !important;
                             				margin-left:0px !important;
                             			}
                             			.tag-editor{
                             				padding:5px;
                             				padding-left:0px;
                             				border: 1px solid rgba(129,129,129,.25);
                             			}
                             			.tag-editor .placeholder{
                             				color:rgb(141,141,141);
                             			}
                             		</style>
                                </div>
                                
                                
                                <div class="hide_if_variation dokan-clearfix">

                                    <div class="dokan-form-group dokan-clearfix dokan-price-container">

										<?php // Regular price ?>
                                        <div class="content-half-part regular-price mrp  f-top">

                                            <label for="_regular_price" class="form-label desc-pro"><?php _e( 'Price', 'dokan' ); ?><span class="required-m">*</span></label>

                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <?php dokan_post_input_box( $post_id, '_regular_price', array( 'placeholder' => __( '0.00', 'dokan' ), "class"=> "dokan-w3 dokan-control-label", "id"=> "_product_price"  ), 'number' ); ?>
                                            </div>
                                        </div>

<?php /* Sven: discounts are disabled
                                        <div class="content-half-part sale-price">
                                            <label for="_sale_price" class="form-label"><?php _e( 'Discounted Price', 'dokan' ); ?></label>

                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <?php dokan_post_input_box( $post_id, '_sale_price', array( 'placeholder' => __( '0.00', 'dokan' ) ), 'number' ); ?>
                                            </div>
                                        </div>
*/ ?>
                                    </div>

<?php /* Sven: discounts are disabled
                                    <div class="discount-price dokan-form-group">
                                        <label>
                                            <input type="checkbox" <?php checked( $is_discount, true ); ?> class="sale-schedule"> <?php _e( 'Schedule Discounted Price', 'dokan' ); ?>
                                        </label>
                                    </div>

                                    <div class="sale-schedule-container dokan-clearfix dokan-form-group">
                                        <div class="content-half-part from">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php _e( 'From', 'dokan' ); ?></span>
                                                <input type="text" name="_sale_price_dates_from" class="dokan-form-control datepicker" value="<?php echo esc_attr( $_sale_price_dates_from ); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>

                                        <div class="content-half-part to">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php _e( 'To', 'dokan' ); ?></span>
                                                <input type="text" name="_sale_price_dates_to" class="dokan-form-control datepicker" value="<?php echo esc_attr( $_sale_price_dates_to ); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="YYYY-MM-DD">
                                            </div>
                                        </div>
                                    </div><!-- .sale-schedule-container -->
*/ ?>
                                </div>

								<?php // Category ?>
                                <?php if ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ): ?>
                                    <div class="dokan-form-group">

                                        <label for="product_cat" class="form-label desc-pro"><?php _e( 'Category ', 'dokan' ); ?>:&nbsp;<span id="selected_cat_string" class="required-m">*</span></label>
									   
                                     
                                        <div class="dokan-product-cat-alert dokan-hide dokan-alert dokan-alert-danger">
                                            <?php _e('Please choose a category !!!', 'dokan'); ?>
                                        </div>
                                       
                                        <?php
                                        $product_cat = -1;
                                        $term = array();
                                        $term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

                                        if ( $term ) {
                                            $product_cat = reset( $term );
                                        }
                                        
                                        ?>
                                         	<input name="product_cat" id="product_cat_to_submit" type="hidden" value="<?php print $product_cat; ?>"/>
                                      <?php
  /*
                                       
                                        $category_args =  array(
                                            'show_option_none' => __( '- Select a category -', 'dokan' ),
                                            'hierarchical'     => 1,
                                            'hide_empty'       => 0,
                                            'name'             => 'product_cat',
                                            'id'               => 'product_cat',
                                            'taxonomy'         => 'product_cat',
                                            'title_li'         => '',
                                            'class'            => 'product_cat dokan-form-control chosen selects',
                                            'exclude'          => '',
                                            'selected'         => $product_cat,
                                        );

                                        wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
                                   		*/
                                   		?>
                                   		<span class="dokan-btn dokan-btn-sm dokan-btn-success smaller-gray-button" onclick="showCategoryPicker()">Select category</span>
                                   		
                                	<div class="ec-modal-category-backdrop" id="ec-category-bg" style="display:none">
                                	    <div class="ec-modal-category" id="ec-category-modal-box">
                                      	<h5 class="widget-title">Select category:</h5>
                                      	<ul class="dokan-checkbox-cat">
                                            <?php
                                            $term = array();
                                            $term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );
											
										
											
                                            include_once DOKAN_LIB_DIR.'/class.category-walker.php';
                                            wp_list_categories(array(
                                                'walker'       => new DokanCategoryWalker(),
                                                'title_li'     => '',
                                                'id'           => 'product_cat',
                                                'hide_empty'   => 0,
                                                'taxonomy'     => 'product_cat',
                                                'hierarchical' => 1,
                                                'selected'     => $term
                                            ));
                                            ?>
                                        </ul>
                                        </div>
                                   	</div>

                                    </div>
                                <?php elseif ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'multiple' ): ?>
                                    
                                    
                                    <div class="dokan-form-group dokan-list-category-box">
                                        <h5><?php _e( 'Choose a category', 'dokan' );  ?></h5>
                                        <ul class="dokan-checkbox-cat">
                                            <?php
                                            $term = array();
                                            $term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

                                            include_once DOKAN_LIB_DIR.'/class.category-walker.php';
                                            wp_list_categories(array(
                                                'walker'       => new DokanCategoryWalker(),
                                                'title_li'     => '',
                                                'id'           => 'product_cat',
                                                'hide_empty'   => 0,
                                                'taxonomy'     => 'product_cat',
                                                'hierarchical' => 1,
                                                'selected'     => $term
                                            ));
                                            ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                
<?php /* Sven: disabled until works properly
                                <div class="dokan-form-group">
                                    <label for="product_tag" class="form-label"><?php _e( 'Tags', 'dokan' ); ?></label>
									<span class="ec-form-field-description"><?php _e( 'Product tags description', 'ktt' ); ?></span>
                                    <select id="lb-tags" style="width:100%;" name="lb-tags[]" multiple="multiple" data-select2-max-sel-len="3">
                                        <?php 

                                            $term = wp_get_post_terms( $post_id, 'product_tag', array( 'fields' => 'all') );
                                            $selected = ( $term ) ? $term : array();

                                            foreach ($selected as $option) {
                                                ?>
                                                <option value="<?= $option->term_id ?>" selected="selected"><?= $option->name ?></option>
                                                <?php
                                            }
                                        ?>
                                    </select>
                                </div>
*/ ?>
                            </div><!-- .content-half-part -->

							<?php // Featured image ?>
                            <div class="content-half-part featured-image">

                                <div class="dokan-feat-image-upload">
                                    <?php
                                    $wrap_class        = ' dokan-hide';
                                    $instruction_class = '';
                                    $feat_image_id     = 0;

                                    if ( has_post_thumbnail( $post_id ) || ( isset($_POST["feat_image_id"]) && isset($_POST["feat_image_url"]) &&  ( $_POST["feat_image_url"] !="" && $_POST["feat_image_id"] != "")   ) ) {
                                        $wrap_class        = '';
                                        $instruction_class = ' dokan-hide';
                                        $feat_image_id     = get_post_thumbnail_id( $post_id );
                                    }
                                    ?>

                                    <div class="instruction-inside<?php echo $instruction_class; ?>">
                                        <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="<?php echo $feat_image_id; ?>">

                                        <i class="fa fa-cloud-upload"></i>
                                        <a href="#" class="dokan-feat-image-btn btn btn-sm smaller-orange-button"  onmousedown="removeImage();" ><?php _e( 'Upload a product cover image', 'dokan' ); ?></a>
                                        <p style="margin-top:5px"> The minimum image size is  800 x 600 px </p>
                                    </div>

                                    <div class="image-wrap<?php echo $wrap_class; ?>" id="fet-im-a">
                                        <a class="close dokan-remove-feat-image" " >&times;</a>
                                        <?php if ( $feat_image_id ) { ?>
                                            <?php echo get_the_post_thumbnail( $post_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array( 'height' => '', 'width' => '')  ); ?>
                                        <?php } else { ?>
                                            <img height="" width="" src="" alt="">
                                        <?php } ?>
                                        <input type="hidden" name="feat_image_url" value="" id="feat_image_url" >
                                    </div>
                                </div><!-- .dokan-feat-image-upload -->

                                <div class="dokan-product-gallery">
                                    <div class="dokan-side-body" id="dokan-product-images">
                                        <div id="product_images_container">
                                            <ul class="product_images dokan-clearfix">
                                                <?php
                                                $product_images = get_post_meta( $post_id, '_product_image_gallery', true );
                                                $gallery = explode( ',', $product_images );

                                                if ( $gallery ) {
                                                    foreach ($gallery as $image_id) {
                                                        if ( empty( $image_id ) ) {
                                                            continue;
                                                        }

                                                        $attachment_image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                                                        ?>
                                                        <li class="image" data-attachment_id="<?php echo $image_id; ?>">
                                                            <img src="<?php echo $attachment_image[0]; ?>" alt="">
                                                            <a href="#" class="action-delete" title="<?php esc_attr_e( 'Delete image', 'dokan' ); ?>">&times;</a>
                                                        </li>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </ul>

                                            <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_images ); ?>">
                                        </div>

                                        <a href="#" class="add-product-images dokan-btn dokan-btn-sm dokan-btn-success smaller-gray-button addm-images"><?php _e( '+ Add more images', 'dokan' ); ?></a>
                                    </div>
                                </div> <!-- .product-gallery -->
                            </div><!-- .content-half-part -->
                        </div><!-- .dokan-form-top-area -->

						<?php // Short description ?>
                        <div class="dokan-product-description">
                            <label for="post_excerpt" class="form-label desc-pro"><?php _e( 'Short Description', 'dokan' ); ?></label>
							<span class="ec-form-field-description"><?php _e( 'Product short description', 'ktt' );
												      _e(' up to: ','ktt');
echo get_option('_product_short_description_limit');
_e(' characters','ktt')			
												 ?></span>
                            <?php 
/*                            wp_editor( $post_excerpt , 'post_excerpt', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_excerpt') ); */
                            ?>

                                <textarea id="post_excerpt" name="post_excerpt" class="valid"> <?php echo $post_excerpt ; ?></textarea>
                         
                        </div>

						<?php // Description ?>
                        <div class="dokan-product-description">
                            <label for="post_content" class="form-label desc-pro"><?php _e( 'Description', 'dokan' ); ?></label>
							<span class="ec-form-field-description"><?php _e( 'Product description', 'ktt' ); ?></span>
                            <?php 

/*                            wp_editor( $post_content , 'post_content', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_content') );*/

                             ?>
                             <textarea id="post_content" name="post_content" class="valid"> <?php echo $post_content ; ?></textarea>
                        </div>

						     <div class="dokan-form-group">
                                 
                                 <!--size_chart-->
                                 <label class="dokan-checkbox-inline dokan-form-label form-label " for="size_chart">
                                 <?php if($_size_chart == 'on'){
                                 	$checked=true;
                                 
                                 
                                 }else{
                                 	$checked=false;
                                 }; ?>
                                        <input type="checkbox" id="size_chart" name="size_chart" <?php if($checked){echo 'checked';}?>  >
                                        <?php _e( 'Size Chart', 'dokan' ); ?>
										<span class="ec-form-field-description"><?php _e( 'This product displays a size chart', 'ktt' ); ?></span>
                                    </label>

                                               </div>

                        <?php do_action( 'dokan_new_product_form' ); ?>

                        <?php //if ( $post_id ): ?>
                            <?php do_action( 'dokan_product_edit_after_main' ); ?>
                        
                            
                        <?php //endif; ?>
                        
                        	<?php // Inventory & Variants ?>
                        <div class="dokan-product-inventory dokan-edit-row dokan-clearfix">
                            <div class="dokan-side-left">
                                <h2>
									<?php _e( 'Inventory & Variants', 'dokan' ); ?> 
									<i class="fa fa-caret-square-o-down ec-section-toggle-btn" aria-hidden="true"></i>
								</h2>
                                <p>
                                    <?php _e( 'Manage inventory, and configure the options for selling this product.', 'dokan' ); ?>
                                </p>
                            </div>

                            <div class="dokan-side-right">
                                <div class="dokan-form-group hide_if_variation" style="width: 50%;">
                                    <label for="_sku" class="form-label"><?php _e( 'SKU', 'dokan' ); ?> <span class="ec-form-field-description" ><?php _e( '(Stock Keeping Unit)', 'dokan' ); ?></span><span class="dokan-loading check-sku dokan-hide"></span></label>
									<span class="ec-form-field-description"><?php _e( 'Product SKU description', 'ktt' ); ?></span>
                                    <?php dokan_post_input_box( $post_id, '_sku' ); ?>
                                </div>

                                <!--  <div class="dokan-form-group">
                     
                                    <label for="post_title" class="form-label"><?php _e( 'Title', 'dokan' ); ?></label>
                                    <span class="ec-form-field-description"><?php _e( 'Product title description', 'ktt' ); ?></span>
                                    <div class="dokan-product-title-alert dokan-hide dokan-alert dokan-alert-danger">
                                            <?php _e('Please choose a Name !!!', 'dokan'); ?>
                                    </div>
                                    <?php dokan_post_input_box( $post_id, 'post_title', array( 'placeholder' => __( 'Product name..', 'dokan' ), 'value' => $post_title ) ); ?>
                                </div> -->

                                <div class="dokan-form-group hide_if_variation">
                                    <?php dokan_post_input_box( $post_id, '_manage_stock', array( "class"=>"form-label", 'label' => __( 'Enable product stock management', 'dokan' ) ), 'checkbox' ); ?>
                                </div>

                                <div class="show_if_stock dokan-stock-management-wrapper dokan-form-group dokan-clearfix mywrapper-stock">

                                    <div class="dokan-w2 hide_if_variation">
                                        <label for="_stock" class=" form-label"><?php _e( 'Quantity', 'dokan' ); ?></label>
										<span class="ec-form-field-description"><?php _e( 'Product quantity <br> description', 'ktt' ); ?></span>
                                        <input type="number" name="_stock" placeholder="<?php __( '1', 'dokan' ); ?>" value="<?php echo wc_stock_amount( $_stock ); ?>" min="0" step="1">
                                    </div>

                                    <div class="dokan-w2 hide_if_variation">
                                        <label for="_stock_status" class=" form-label"><?php _e( 'Stock Status', 'dokan' ); ?></label>
										<span class="ec-form-field-description"><?php _e( 'Product stock status description', 'ktt' ); ?></span>

                                        <?php dokan_post_input_box( $post_id, '_stock_status', array( 'options' => array(
                                            'instock'     => __( 'In Stock', 'dokan' ),
                                            'outofstock' => __( 'Out of Stock', 'dokan' ),
                                        ) ), 'select' ); ?>
                                    </div>

                                    <div class="dokan-w2 hide_if_variation">
                                        <label for="_backorders" class=" form-label"><?php _e( 'Allow Backorders', 'dokan' ); ?></label>
										<span class="ec-form-field-description"><?php _e( 'Product allow backorders description', 'ktt' ); ?></span>

                                        <?php dokan_post_input_box( $post_id, '_backorders', array( 'options' => array(
                                            'no'     => __( 'Do not allow', 'dokan' ),
                                            'notify' => __( 'Allow but notify customer', 'dokan' ),
                                            'yes'    => __( 'Allow', 'dokan' )
                                        ) ), 'select' ); ?>
                                    </div>

                                    <div class="dokan-w2 hide_if_variation">
                                        <label for="_backorder_time" class=" form-label"><?php _e( 'Backorder time', 'ktt' ); ?></label>
										<span class="ec-form-field-description"><?php _e( 'Product backorder time description', 'ktt' ); ?></span>

                                        <?php dokan_post_input_box( $post_id, '_backorder_time', array( 'options' => array(
                                            ''     => __( '- select time -', 'ktt' ),
                                            '1'    => __( '1 day', 'ktt' ),
                                            '2'    => __( '2 days', 'ktt' ),
                                            '3'    => __( '3 days', 'ktt' ),
                                            '4'    => __( '4 days', 'ktt' ),
                                            '5'    => __( '5 days', 'ktt' ),
                                            '6'    => __( '6 days', 'ktt' ),
                                            '7'    => __( '7 days', 'ktt' ),
                                        ) ), 'select' ); ?>
                                    </div>
                                </div><!-- .show_if_stock -->

                                <div class="dokan-form-group">
                                    <?php dokan_post_input_box( $post_id, '_sold_individually', array('label' => __( 'Allow only one quantity of this product to be bought in a single order', 'dokan' ), "class" => "form-label" ), 'checkbox' ); ?>
                                </div>

                                <?php if ( $post_id ): ?>
                                    <?php do_action( 'dokan_product_edit_after_inventory' ); ?>
                                <?php endif; ?>

                                <div class="dokan-divider-top dokan-clearfix downloadable downloadable_files hide_if_variation">
                                    <label class="dokan-checkbox-inline dokan-form-label form-label " for="_downloadable">
                                        <input type="checkbox" id="_downloadable" name="_downloadable" value="yes" <?php checked( $_downloadable, 'yes' ); ?>>
                                        <?php _e( 'This is a downloadable product', 'dokan' ); ?>
										<span class="ec-form-field-description"><?php _e( 'Product downloadable products description', 'ktt' ); ?></span>
                                    </label>


                                    <?php if ( $post_id ): ?>
                                        <?php do_action( 'dokan_product_edit_before_sidebar' ); ?>
                                    <?php endif; ?>
                                    <div class="dokan-side-body dokan-download-wrapper<?php echo ( $_downloadable == 'yes' ) ? '' : ' dokan-hide'; ?>">

                                        <table class="dokan-table dokan-table-condensed mytab-prod">
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3">
                                                        <a href="#" class="insert-file-row dokan-btn dokan-btn-sm dokan-btn-success smaller-orange-button" data-row="<?php
                                                            $file = array(
                                                                'file' => '',
                                                                'name' => ''
                                                            );
                                                            ob_start();
                                                            include DOKAN_INC_DIR . '/woo-views/html-product-download.php';
                                                            echo esc_attr( ob_get_clean() );
                                                        ?>"><?php _e( 'Add File', 'dokan' ); ?></a>
                                                    </th>
                                                </tr>
                                            </tfoot>
                                            <thead>
                                                <tr>
                                                    <th><?php _e( 'Name', 'dokan' ); ?> <span class="tips" title="<?php _e( 'This is the name of the download shown to the customer.', 'dokan' ); ?>">[?]</span></th>
                                                    <th><?php _e( 'File URL', 'dokan' ); ?> <span class="tips" title="<?php _e( 'This is the URL or absolute path to the file which customers will get access to.', 'dokan' ); ?>">[?]</span></th>
                                                    <th><?php _e( 'Action', 'dokan' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $downloadable_files = get_post_meta( $post_id, '_downloadable_files', true );

                                                if ( $downloadable_files ) {
                                                    foreach ( $downloadable_files as $key => $file ) {
                                                        include DOKAN_INC_DIR . '/woo-views/html-product-download.php';
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>

                                        <div class="dokan-clearfix">
                                            <div class="content-half-part">
                                                <label for="_download_limit" class="form-label"><?php _e( 'Download Limit', 'dokan' ); ?></label>
												<span class="ec-form-field-description"><?php _e( 'Product download limit description', 'ktt' ); ?></span>
                                                <?php dokan_post_input_box( $post_id, '_download_limit', array( 'placeholder' => __( 'e.g. 4', 'dokan' ) ) ); ?>
                                            </div><!-- .content-half-part -->

                                            <div class="content-half-part">
                                                <label for="_download_expiry" class="form-label"><?php _e( 'Download Expiry', 'dokan' ); ?></label>
												<span class="ec-form-field-description"><?php _e( 'Product download expiry description', 'ktt' ); ?></span>
                                                <?php dokan_post_input_box( $post_id, '_download_expiry', array( 'placeholder' => __( 'Number of days', 'dokan' ) ) ); ?>
                                            </div><!-- .content-half-part -->
                                        </div>

                                    </div> <!-- .dokan-side-body -->
                                </div> <!-- .downloadable -->

                                <?php do_action( 'dokan_product_edit_after_downloadable', $post, $post_id ); ?>
                                <?php do_action( 'dokan_product_edit_after_sidebar', $post, $post_id ); ?>
                                <?php do_action( 'dokan_single_product_edit_after_sidebar', $post, $post_id ); ?>

                            </div><!-- .dokan-side-right -->
                        </div><!-- .dokan-product-inventory -->

                        <?php do_action( 'dokan_product_edit_after_inventory_variants', $post, $post_id ); ?>
                        
                        
                        
                        
                        
                        
                    

					

<?php /* Sven: discounts are disabled
                        <?php if ( ! is_int( key( $is_enable_op_discount ) ) && array_key_exists("product-discount", $is_enable_op_discount ) == "product-discount" ) : ?>
                            <div class="dokan-discount-options dokan-edit-row dokan-clearfix">
                                <div class="dokan-side-left">
                                    <h2><?php _e( 'Discount Options', 'dokan' ); ?></h2>
                                </div>

                                <div class="dokan-side-right">
                                    <?php //if ( $post_id ) : ?>
                                        <label class="dokan-checkbox-inline dokan-form-label" for="_is_lot_discount">
                                            <input type="checkbox" id="_is_lot_discount" name="_is_lot_discount" value="yes" <?php checked( $_is_lot_discount, 'yes' ); ?>>
                                            <?php _e( 'Enable bulk discount', 'dokan' ); ?>
                                        </label>
                                        <div class="show_if_needs_lot_discount <?php echo ($_is_lot_discount=='yes') ? '' : 'hide_if_lot_discount' ;?>">
                                            <label class="form-label dokan-form-label" for="_lot_discount_quantity"><?php _e('Minimum quantity', 'dokan');?></label>
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <?php dokan_post_input_box( $post_id, '_lot_discount_quantity', array( 'placeholder' => __( '0', 'dokan' ), 'min' => 0, 'value' => '' ), 'number' ); ?>
                                            </div>
                                            <label class="form-label dokan-form-label" for="_lot_discount_quantity"><?php _e('Discount %', 'dokan');?></label>
                                            <div class="dokan-input-group">
                                                <?php dokan_post_input_box( $post_id, '_lot_discount_amount', array( 'placeholder' => __( '0 %', 'dokan' ), 'min' => 0, 'value' => '' ), 'number' ); ?>
                                                <span class="dokan-input-group-addon"><?php echo '%'; ?></span>
                                            </div>
                                        </div>
                                    <?php //endif;?>
                                </div>
                            </div>
                        <?php endif;?>
*/ ?>

                        <?php //if ( $post_id ): ?>
                            <?php do_action( 'dokan_product_edit_after_options' ); ?>
                        
                         <?php //endif; ?>

                        				<?php // Other options ?>
                        <div class="dokan-other-options dokan-edit-row dokan-clearfix">
                            <div class="dokan-side-left">
                                <h2>
									<?php _e( 'Other Options', 'dokan' ); ?>
									<i class="fa fa-caret-square-o-down ec-section-toggle-btn" aria-hidden="true"></i>
								</h2>
								<p class="ec-form-field-description" >
									<?php _e( 'Product Other Options description', 'dokan' ); ?>
								</p>
                            </div>

                            <div class="dokan-side-right">

                                <?php if ( $post_id ): ?>
									<?php // Post status ?>
									<input id="post_status" name="post_status" value="<?php echo $post_status ?>" type="hidden" />
<?php /* Sven: handled by save buttons
                                    <div class="dokan-form-group">
                                        <label for="post_status" class="form-label"><?php _e( 'Product Status', 'dokan' ); ?></label>
										<span class="ec-form-field-description"><?php _e( 'Product status description', 'ktt' ); ?></span>
                                        <?php if ( $post_status != 'pending' ) { ?>
                                            <?php $post_statuses = apply_filters( 'dokan_post_status', array(
                                                'publish' => __( 'Online', 'dokan' ),
                                                'draft'   => __( 'Draft', 'dokan' )
                                            ), $post ); ?>

                                            <select id="post_status" class="dokan-form-control" name="post_status">
                                                <?php foreach ( $post_statuses as $status => $label ) { ?>
                                                    <option value="<?php echo $status; ?>"<?php selected( $post_status, $status ); ?>><?php echo $label; ?></option>
                                                <?php } ?>
                                            </select>
                                        <?php } else { ?>
                                            <?php $pending_class = $post_status == 'pending' ? '  dokan-label dokan-label-warning': ''; ?>
                                            <span class="dokan-toggle-selected-display<?php echo $pending_class; ?>"><?php echo dokan_get_post_status( $post_status ); ?></span>
                                        <?php } ?>
                                    </div>
*/ ?>
                                <?php endif ?>

								<input type="hidden" id="_visibility" name="_visibility" value="visible" />
<?php /* Sven: no such option. This is default value
                                <div class="dokan-form-group">
                                    <label for="_visibility" class="form-label"><?php _e( 'Visibility', 'dokan' ); ?></label>
									<span class="ec-form-field-description"><?php _e( 'Product visibility description', 'ktt' ); ?></span>
                                    <?php dokan_post_input_box( $post_id, '_visibility', array( 'options' => array(
                                        'visible' => __( 'Catalog or Search', 'dokan' ),
                                        'catalog' => __( 'Catalog', 'dokan' ),
                                        'search'  => __( 'Search', 'dokan' ),
                                        'hidden'  => __( 'Hidden', 'dokan ')
                                    ) ), 'select' ); ?>
                                </div>
*/ ?>

                                <div class="dokan-form-group">
                                    <label for="_purchase_note" class="form-label"><?php _e( 'Purchase Note', 'dokan' ); ?></label>
									<span class="ec-form-field-description"><?php _e( 'Customer will get this info in their order email', 'dokan' ); ?></span>
                                    <?php dokan_post_input_box( $post_id, '_purchase_note', array(), 'textarea' ); ?>
                                </div>

								<input name="_enable_reviews" value="yes" id="_enable_reviews" type="hidden">
<?php /* Sven: disabled by default
                                <div class="dokan-form-group">
                                    <?php $_enable_reviews = ( $post->comment_status == 'open' ) ? 'yes' : 'no'; ?>
                                    <?php dokan_post_input_box( $post_id, '_enable_reviews', array('value' => $_enable_reviews, 'label' => __( 'Enable product reviews', 'dokan' ) ), 'checkbox' ); ?>
                                </div>
*/ ?>

								<?php if ( $post_id ): ?>
									<?php //do_action( 'ec_merchant_product_edit_other_options' ); ?>
								<?php endif; ?>

                            </div>
                        </div><!-- .dokan-other-options -->
                        
                        
                        
                       
		

                        <?php wp_nonce_field( 'dokan_add_new_product', 'dokan_add_new_product_nonce' ); ?>

                        <!--hidden input for Firefox issue-->
                        <input type="hidden" name="dokan_add_product" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
<?php /*
                        <button name="dokan_add_product" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block ec-product-publish-btn" data-balloon-length="medium" data-balloon="<?php _e( 'Make sure you didn\'t make any spelling mistakes. This data will be sent to our translators shortly.', 'ktt' ); ?>" data-balloon-pos="up" ><?php esc_attr_e( 'Save Product', 'dokan' ); ?></button>
*/ ?>

						<?php // Publish product ?>

                        <button name="dokan_add_product" class="dokan-btn dokan-btn-theme dokan-btn-lg ec-product-publish-btn  call-to-action-button m10-r" data-balloon-length="medium" onclick='checkImage(event)'  data-balloon="<?php _e( 'Make sure you didn\'t make any spelling mistakes. This data will be sent to our translators shortly.', 'ktt' ); ?>" data-balloon-pos="up"><?php esc_attr_e( 'Publish', 'ktt' ); ?></button>
 
						<?php // Save as draft. Only available if product not published yet. ?>
                        <?php if($post_status != 'publish'): ?>
          
	                        <button name="dokan_save_draft_product" class="dokan-btn dokan-btn-lg ec-merchant-product-save-btn call-to-action-button" onclick='document.getElementById("bdraft").value = "true"; ' ><?php esc_attr_e( 'Save as draft', 'ktt' );  ?></button>
                            <input type="hidden"  name="dokan_save_draft_product" id="bdraft" value="<?php esc_attr_e( 'Save Draft Product', 'dokan' ); ?>"/>
						<?php endif; ?>

                    </form>
                <?php } else { ?>
                    <div class="dokan-alert dokan-alert">
                        <?php echo dokan_seller_not_enabled_notice(); ?>
                    </div>
                <?php } ?>

            <?php } else { ?>

                <?php do_action( 'dokan_can_post_notice' ); ?>

            <?php } ?>
        </div> <!-- #primary .content-area -->

        <?php

            /**
             *  dokan_product_content_inside_area_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_product_content_inside_area_after' );
        ?>
    </div>

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_after_product_content_area hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_after_product_content_area' );
    ?>

</div><!-- .dokan-dashboard-wrap -->
<div class="dokan-clearfix poxc"></div>

<?php

    /**
     *  dokan_dashboard_content_before hook
     *
     *  @since 2.4
     */
    do_action( 'dokan_dashboard_wrap_after', $post, $post_id );

    wp_reset_postdata();

    if ( ! $from_shortcode ) {
        get_footer();
    }
?>

 <script>


    checkImage = (ev)=>{

             storeInfo();       
             if(jQuery('.dokan-feat-image-upload .instruction-inside').hasClass('dokan-hide') === false){
                 ev.preventDefault();
                  alert('Remember to upload a cover image!');
            }
           let arr = jQuery("input[name^='variable_regular_price'") ;
            let el = [jQuery("#post_title"),jQuery("#product_cat"), jQuery("#_regular_price"), arr];
            
            errDisp(el);
                          
    }
    errDisp = (elements) =>{
       
            let message = "";
            let errors = [];
            for(e in elements){
                if(elements[e].is("select")){
                   if(elements[e].val()==="-1"){
                         errors.push(elements[e]);
                    }
                }
                if(elements[e].is("input")){
                    if(jQuery.trim(elements[e].val())==="" || ( elements[e].val() =="0" &&  jQuery('input#_create_variation').is(':checked') == false ) ){
                           errors.push(elements[e]);
                    }
                }
            }

            if(errors.length > 0 )
            {     
                for(e in errors){
                  errors[e].addClass("input-red-error");
                  jQuery("label[for='"+errors[e].attr("name")+"']").addClass("label-red-error");
                  message = '<div class="dokan-product-title-alert dokan-alert dokan-alert-danger"> Please fill out this field. </div>';
                  //jQuery('.errfield').html(message);
                  //jQuery(message).insertBefore(errors[e]);
                 }
               jQuery( ".input-red-error" ).change(function() {
                let id = this.id;
               // console.log(jQuery("#"+id).attr("name"));
                   jQuery("#"+id).removeClass("input-red-error");
                   jQuery("label[for='"+jQuery("#"+id).attr("name")+"']").removeClass("label-red-error");
                });
               
                return false;
            }
          

        }    


</script>
<script type="text/javascript">
  
   jQuery(document).ready(function(){
         var err= false;
         var materials_size = "<?php echo isset($_POST['_material_name'])? sizeof($_POST['_material_name']) : '0'; ?>";
        
         var id_v = "<?php echo isset($_POST["dokan_product_id"]); ?>";

        if( id_v == "1"){  
            build();
        }
        else{
          localStorage.setItem("form", "");
        }
/*        for(let a = 0 ; a < materials_size ; a++  ){
            jQuery(". a.lb-elastic-add").trigger("click");
        }*/
           
jQuery('.subcats').each(function(){
		if(jQuery(this).children().length == 0){
			 var id = jQuery(this).attr("subcat-id");
			 jQuery("#"+id+"-handle").html("&nbsp;&nbsp;&nbsp;");

		}else{
			 var id = jQuery(this).attr("subcat-id");
			 jQuery("#"+id+"-handle").html("+");
		}
		
	})



    });
    showButton = () =>{ 
    if(jQuery(".dokan-feat-image-id").val() == 0)
        jQuery(".add-product-images").addClass("addm-images");
    else
         jQuery(".add-product-images").removeClass("addm-images");
    }
    showButton();
    jQuery(window).on("mousemove", ()=>{ 
        showButton();
        jQuery("#feat_image_url").val(jQuery("#fet-im-a > img").attr("src"));
       //  updatePatentBox();
      // validateVariation();

    } );

    removeImage = ()=>{
                    jQuery("#fet-im-a > img").attr({"src": "", "srcset":"" });
      
    }

    function convertValue(id)
    {
        var t = id.replace(/[[]/g,'\\\\[');
        return "#" + t.replace(/]/g,'\\\\]'); 
    }
     build = () =>{
        if( jQuery.trim(localStorage.getItem("form")) !="" && jQuery.trim(localStorage.getItem("variants")) !="" && jQuery.trim(localStorage.getItem("fet-image")) !="" ){
                    let j =  JSON.parse(localStorage.getItem("form"));
                    let v = JSON.parse(localStorage.getItem("variants"));
                    let featImage = JSON.parse(localStorage.getItem("fet-image"));
                    jQuery("#fet-im-a > img").attr("src",featImage.url);
                    jQuery(".dokan-feat-image-id").val(featImage.id);
                   // let form = jQuery(".dokan-product-edit-form").html();
                    for( var o in j){
                        jQuery(convertValue(j[o].name)).val(j[o].value);
                    }
                    

        }


    }  
    storeInfo = ()=>{
        let p = jQuery(".dokan-product-edit-form").serializeArray();
        let variants =  {"labels": jQuery('.dokan-attribute-option-name-label').val() , "options": jQuery('.attributes-prod > input').val() } ;
        localStorage.setItem("form", JSON.stringify(p) );
        //console.log(jQuery("#fet-im-a > img").attr("src"));
        localStorage.setItem("fet-image",JSON.stringify({"url":jQuery("#fet-im-a > img").attr("src"), "id": jQuery(".dokan-feat-image-id").val() }));
        localStorage.setItem("variants", JSON.stringify(variants) );


    }

    validateVariation = () =>{
        
        if(jQuery(".inventory-table").val() == undefined) {
            //console.log(jQuery(".dokan-attribute-option-name") .val());
            if(  jQuery(".dokan-attribute-option-name").val() == undefined    ){
                 jQuery("#_create_variation").attr("disabled","true");
                 jQuery("#variation-message").html("Please, add an option to create a variation.");
            }
            else{
                       jQuery("#_create_variation").removeAttr("disabled");
                jQuery("#variation-message").html('Create variation using those attribute options'); 
            }
        }

    }


function toggleEcSubcatsEdit(cat_id){
	var target = ".subcats-"+cat_id;
	jQuery(target).toggle("fast");
	return false;
	
}
function showCategoryPicker(){
	jQuery("#ec-category-bg").toggle();
}
function setCategory(cat_id){
   jQuery("#product_cat_to_submit").val(cat_id);
   
   jQuery(".checkbox-category").each(function(){
   		var sel_id = jQuery(this).attr("cat-term-id");
   		
   		if(sel_id == cat_id){
   			jQuery(this).prop("checked",true);
   		} else { 
   			jQuery(this).prop("checked",false);
   		}
   }) 
	
	var breadcrumbString =  " > " + jQuery("#category-"+cat_id).attr("cat-term-name")+ " (Unsaved) ";
	ec_category_breadcrumbs(jQuery("#category-"+cat_id), breadcrumbString);
	jQuery("#ec-category-bg").toggle();
}


jQuery('#ec-category-bg').on('click', function(e){
		if(!jQuery("#ec-category-modal-box").is(":hover")){
    		jQuery("#ec-category-bg").toggle();
  		}
		
});


function ec_category_breadcrumbs(catElm, breadcrumbstring){
 	parentCatElm = catElm.parent().closest('div.subcats');
 	parentCatElmCheck = catElm.parent().closest('input.checkbox-category');
 	
	if(parentCatElm.length) {
		var breadcrumbstring = " > "+parentCatElm.attr("parentcat-name") + breadcrumbstring
		ec_category_breadcrumbs(parentCatElm, breadcrumbstring);
	} else {
		jQuery("#selected_cat_string").html(breadcrumbstring.substring(3));
	}

}


function ec_category_open(catElm, breadcrumbstring)
{
	catElm.css('margin-left', '15px');
    catElm.css('display', 'block');
 	parentCatElm = catElm.parent().closest('div.subcats');
 	parentCatElmCheck = catElm.parent().closest('input.checkbox-category');
 	
	if(parentCatElm.length) {
		breadcrumbstring = " > " + parentCatElm.attr("parentcat-name") + breadcrumbstring
		ec_category_open(parentCatElm, breadcrumbstring);
	} else {
	
		
		jQuery("#selected_cat_string").html(breadcrumbstring.substring(3));
	}
}
jQuery(document).ready(function($)
{
	
	var breadcrumbstring = "";
	
	catElm = $('.dokan-checkbox-cat input.checkbox-category[checked=checked]');
	parentCatElm = catElm.parent().closest('div.subcats');
	if(parentCatElm.length){
		parentstring = " > " + parentCatElm.attr("parentcat-name");
	}else{
		parentstring = "";
	}

	
	
	if(catElm.length) {
		breadcrumbstring = parentstring + " > " +catElm.attr("cat-term-name") + breadcrumbstring
		ec_category_open( catElm.closest('div.subcats'), breadcrumbstring  );
	}
	

	
});


</script>
<style>
 .dokan-checkbox-cat label{
 	display:inline-block;
 }
 
 .subcat-togglehandle{
	cursor: pointer;
    padding-left: 20px;
    padding-right: 20px;
    width: 20px;
    display: inline-block;
}
#ec-category-bg{
	position:fixed;
	top:0px;
	left:0px;
	width:100%;
	height:100%;
	overflow-y:scroll;
	box-sizing:border-box;
	padding-bottom:10%;
	background-color:rgba(200,200,200,0.5);
	z-index:99999999999999;
}
.ec-modal-category{
	margin-left:auto;
	margin-right:auto;
	margin-top:10%;
	width:400px;
	padding:20px;
	border:2px solid #cdcdcd;
	background:white;
	
}
 }
</style>
                                   

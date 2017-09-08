<?php 

$productQuery = new WP_Query($args);
if($productQuery->have_posts()): 
?> 
<div class="row marginTop">
    <div class="col-md-12">
        <div class="owl-carousel">
        <?php  while($productQuery->have_posts()):$productQuery->the_post(); ?>
           <?php $product = wc_get_product( $productQuery->post->ID);?>
            <div class="item" style="width:259px;">
            	<a href="<?php echo the_permalink() ?>">
              
                 <div class='owl-carousel-thumb'>
                   <?php the_post_thumbnail(apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array( 'height' => '285', 'width' => '257' ) );?>
                 </div>
                </a>
                <div class='product-title'><?php 
                
                    $title = the_title('','',false);
                    if(strlen($title) < 50){
                      echo $title;
                    }else{
                      echo substr($title,0,50).'..';
                    } ?></div>
                <div class="product-price">
                	<?php if ( $product->get_sale_price()) {
                            echo '<span style="text-decoration: line-through;">'.$product->get_price().'€</span> /';
                            echo '<span style="color:#fc693d">'.$product->get_sale_price().'€</span>';
                          } else {
            	            echo '<span >'.$product->get_regular_price().'€</span>';
    	 	              }
                    ?>
                </div>
            </div>
        <?php endwhile; ?>	
        </div>
    </div>
</div>

<?php  endif; wp_reset_query(); wp_reset_postdata(); ?>	
<script>
 
  
 

</script>



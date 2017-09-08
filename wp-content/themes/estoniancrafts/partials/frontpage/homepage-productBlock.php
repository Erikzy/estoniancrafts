<?php 
$args = array(
    'post_type'=>'products',
    'posts_per_page' => -1,
);
$caroucelCatTwoQuery = new WP_Query($args);
$ids = array();
if($caroucelCatTwoQuery->have_posts()): 
?> 
<div class="row marginTop">
    <div class="col-md-12">
        <h2><?php echo $caroucelCatTwoDetails->name; ?></h2>
        <div class="owl-carousel" id="frontpage-product-owl">
        <?php  while($caroucelCatTwoQuery->have_posts()): $caroucelCatTwoQuery->the_post(); ?>
           
            <div class="item">
            	<?php
              		$field = get_field('product_ext_link');
                     if(strlen($field) < 1){
                       $field="#";
                     }
                ?>
                <a href="<?php echo $field; ?>">
              
                 <div class='owl-carousel-thumb'>
                   <?php the_post_thumbnail('size-120x104');?>
                 </div>
                </a>
                <h3><?php 
                
                $title = the_title('','',false);
                    if(strlen($title) < 50){
                      echo $title;
                    }else{
                      echo substr($title,0,50).'..';
                    } ?></h3>
                <p><?php echo excerpt(5);?></p>
                
                <?php if($field != "#"){ ?>
                  <a href="<?php the_permalink(); ?>" class="cta-button">Vaata</a>
                  <a href="<?php  echo $field; ?>" target="_blank" class="cta-button">Osta</a>
                <?php }else{ ?>
                  <a href="<?php the_permalink(); ?>" class="cta-button">Vaata</a>
                <?php }?>
            </div>
        <?php endwhile; ?>	
        </div>
    </div>
</div>
<div class="row">
		<a href="/product-cat/tooted/" class="cta-button allProdButton">Vaata kõiki tooteid</a>
	
</div>
<?php  endif; wp_reset_query(); wp_reset_postdata(); ?>	


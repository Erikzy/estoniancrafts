<?php 

$eventQuery = new WP_Query($args);
if($eventQuery->have_posts()): 
?> 
<div class="row marginTop">
    <div class="col-md-12">
        <div class="owl-carousel event-owl-carousel">
        <?php  while($eventQuery->have_posts()):$eventQuery->the_post(); ?>
            <div class="item">
            	<a href="<?php echo the_permalink() ?>">
                 <div class='owl-carousel-thumb'>
                   <?php 
                   
                      $attachment_id =  get_post_thumbnail_id();
					  $image_info = wp_get_attachment_image_src($attachment_id, 'product-slider-img');
                      echo '<img src="'. $image_info[0] .'"  />';
                   ?>
                 </div>
                </a>
                <div class='product-title'><?php 
                
                    $title = the_title('','',false);
                    if(strlen($title) < 50){
                      echo $title;
                    }else{
                      echo substr($title,0,50).'..';
                    } ?></div>
            </div>
        <?php endwhile; ?>	
        </div>
    </div>
</div>

<?php  endif; wp_reset_query(); wp_reset_postdata(); ?>	
<script>
 
  
 

</script>



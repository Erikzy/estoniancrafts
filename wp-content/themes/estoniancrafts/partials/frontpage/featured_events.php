<?php 

$eventQuery = new WP_Query($args);

         var_dump($eventQuery ) ;
         echo "<br><br>";
        //  die();
if($eventQuery->have_posts()): 

?> 

<div class="row marginTop"  >
    <div class="col-md-12">
        <div class="event-owl-carousel owl-carousel" owl-init="0" >
        <?php  while($eventQuery->have_posts()):

        $eventQuery->the_post();  ?>
            <div class="item" style="width:380px;">
            	<a href="<?php echo the_permalink() ?>">
                 <div class='owl-carousel-event-thumb'>
                   <?php 
                   
                      $attachment_id =  get_post_thumbnail_id();
					  $image_info = wp_get_attachment_image_src($attachment_id, 'product-slider-img');
                      echo '<img src="'. $image_info[0] .'"  />';
                   ?>
                   
                 <div class="eventDate"><?php
                  if(tribe_get_start_date(null, false, "j.M") == tribe_get_end_date(null, false, "j.M")){
                  	echo tribe_get_start_date(null, false, "j.M");
                  }else{
                  	echo tribe_get_start_date(null, false, "j.M")."&nbsp;-<br> ".tribe_get_end_date(null, false, "j.M");
                  }
                  
                  ?>
                 </div>
                  <div class="eventVenue"><?php
                  
                     echo tribe_get_venue();
                  ?></div>  
                 </div>
            
                <div class='event-title'>
                	
                <?php 
                	
                    $title = the_title('','',false);
                    if(strlen($title) < 50){
                      echo $title;
                    }else{
                      echo substr($title,0,50).'..';
                    } ?>
                    
                </div>
                <div class='event-excerpt'>
                 <?php
                	the_excerpt(5);
              
               	?>
                </div> 
              </a>
            </div>
        <?php endwhile; ?>	
        </div>
    </div>
</div>

<?php  endif; wp_reset_query(); wp_reset_postdata(); ?>	
<script>
 
  
 

</script>



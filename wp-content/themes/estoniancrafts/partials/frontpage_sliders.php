
<div class="container frontpageSliders">

<!--rotator-->	

<!--featured events-->	

<!--top products-->	
	<h1><?php echo __('Top tooted','ec');?></h1>
    <?php
    
    $args = array(
    	'post_type'=>'product',
    	'post_status' => 'published',
    	'posts_per_page' => -1,
    	'meta_key'              => 'total_sales',
        'orderby'               => 'meta_value_num',
	);
   include(locate_template('partials/frontpage/product_scroller.php')); 
   ?>



<!--most likes-->	
     
 	 <h1><?php echo __('Enim like kogunud tooted','ec');?></h1>
	
	 <?php
    
    	$args = array(
    		'post_type'=>'product',
    		'post_status' => 'published',
    		'posts_per_page' => -1,
		);
  	 include(locate_template('partials/frontpage/product_scroller.php')); 
    
    
    
     ?>
	
<!--featured products-->


<!--most viewed-->	
<!--events-->	

  
	 <h1><?php echo __('Tulevased sündmused','ec');?></h1>
 	
	 <?php
    
    $args = array(
    	'post_type'=>'tribe_events',
    	'post_status' => 'published',
    	'posts_per_page' => -1,
	);
  	include(locate_template('partials/frontpage/featured_events.php')); 
    
    
    
     ?>
	
<!--posts-->		
	
</div><!--container ends-->


<div class="container frontpageSliders">

<!--rotator-->	

<!--featured events-->	

<!--top products-->	
    <h2 class="frontpageSubheading"><?php echo __('Made With Heart','ec');?></h2>
	<h1><?php echo __('Top tooted','ec');?></h1>
	<div class="heartImgWrapper"><img src="<?php echo bloginfo('template_url').'/images/estonian_crafts_heart.png'; ?>" /></div>
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
     
     <h2 class="frontpageSubheading"><?php echo __('Made With Heart','ec');?></h2>
	 <h1><?php echo __('Enim like kogunud tooted','ec');?></h1>
     <div class="heartImgWrapper"><img src="<?php echo bloginfo('template_url').'/images/estonian_crafts_heart.png'; ?>" /></div>
	
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
<!--posts-->		
	
</div><!--container ends-->

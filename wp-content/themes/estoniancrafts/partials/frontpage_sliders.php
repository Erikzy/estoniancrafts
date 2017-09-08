
<div class="container frontpageSliders">

<!--rotator-->	

<!--featured events-->	

<!--top products-->	

	<h1>Top tooted</h1>
    <?php
    
    $args = array(
    	'post_type'=>'product',
    	'post_status' => 'published',
    	'posts_per_page' => -1,
	);
   include(locate_template('partials/frontpage/product_scroller.php')); 
    ?>



<!--most likes-->	

	<h1>Enim like kogunud tooted</h1>
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

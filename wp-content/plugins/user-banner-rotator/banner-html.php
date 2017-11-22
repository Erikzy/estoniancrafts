<?php
/*
 
 	Html template
 */


?>

<div class="user-rotator-banner-wrapper">
 <ul class="user-rotator-banner">
 	<?php
 		foreach($slides as $slide){
			echo '<li class="user-rotator-banner-slide t-'.$slide->wp_attachment_id.' ">'.wp_get_attachment_image($slide->wp_attachment_id, 'user_banner_upload').'</li>';
 		}
 	?>
 </ul>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> 
<script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script> 
<script>var $j = jQuery.noConflict(true);</script>
<script type="text/javascript">

$j(document).ready(function() {
$j('.user-rotator-banner').bxSlider({
    preloadImages:'all',
    infiniteLoop: true,
    auto:true,
	pager: false,
	touchEnabled:false,
	controls: false
});


});

</script>
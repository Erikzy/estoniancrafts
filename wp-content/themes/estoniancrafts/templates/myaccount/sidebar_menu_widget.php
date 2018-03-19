<?php
/**
 * Available variables
 * - $menu:	EC_Menu
 */
?>
<?php if( ! defined( 'ABSPATH' ) ) exit; ?>
<?php do_action( 'woocommerce_before_account_navigation' ) ?>

<?php if(isset($menu) && !empty($menu->items)):
	global $wp;
	$current_url = home_url(add_query_arg(array(),$wp->request)); 
	$active_class = "";
?>
<div id="ec-myaccount-sidebar-menu" class="sidebar-widget woocommerce widget_product_categories">
	<h5 class="widget-title"><?php echo $title ?></h5>
	<ul>
		<?php foreach ( $menu->items as $menuItem ) : 
			if($menuItem->url == $current_url.'/' || $menuItem->url == $current_url )
				$active_class = "menu-item-active";
		?>

			<li id="<?php echo $menuItem->id ?>" class="<?php  echo $menuItem->class.' '.$active_class  ?>">
				<a
				 <?php
				 	if($menuItem->target == "_blank"){
				 		echo 'target="_blank"';
				 	}
				 ?>	
				 href="<?php echo $menuItem->url ?>"><?php echo esc_html( $menuItem->title ); ?></a>
			</li>
		<?php 
		$active_class ="";
		endforeach; ?>
	</ul>
</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_navigation' ) ?>

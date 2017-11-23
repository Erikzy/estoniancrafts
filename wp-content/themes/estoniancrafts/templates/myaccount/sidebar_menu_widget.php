<?php
/**
 * Available variables
 * - $menu:	EC_Menu
 */
?>
<?php if( ! defined( 'ABSPATH' ) ) exit; ?>
<?php do_action( 'woocommerce_before_account_navigation' ) ?>

<?php if(isset($menu) && !empty($menu->items)): ?>
<div id="ec-myaccount-sidebar-menu" class="sidebar-widget woocommerce widget_product_categories">
	<h5 class="widget-title"><?php echo __('My Account', 'woocommerce') ?></h5>
	<ul>
		<?php foreach ( $menu->items as $menuItem ) : ?>
			<li id="<?php echo $menuItem->id ?>" class="<?php  echo $menuItem->class  ?>">
				<a href="<?php echo $menuItem->url ?>"><?php echo esc_html( $menuItem->title ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_navigation' ) ?>

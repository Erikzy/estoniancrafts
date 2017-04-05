<?php
/**
 * Available variables
 * - $menu:	EC_Menu
 */
?>
<?php if( ! defined( 'ABSPATH' ) ) exit; ?>
<?php do_action( 'woocommerce_before_account_navigation' ) ?>

<?php if(isset($menu) && !empty($menu->items)): ?>
<nav class="woocommerce-MyAccount-navigation">
	<ul>
		<?php foreach ( $menu->items as $menuItem ) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $menuItem->url_endpoint ); ?>">
				<a href="<?php echo $menuItem->url ?>"><?php echo esc_html( $menuItem->title ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_navigation' ) ?>

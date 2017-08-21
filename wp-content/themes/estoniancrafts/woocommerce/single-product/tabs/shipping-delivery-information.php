<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>

<table class="shop_attributes">
	<?php
		$expectedDelivery = '';
		// if is stock product and some products exist at stock
		if ($product->managing_stock() && $product->get_stock_quantity() ) {
			$expectedDelivery = get_post_meta( $product->id, '_expected_delivery_in_warehouse', true);
		} else {
			$expectedDelivery = get_post_meta( $product->id, '_expected_delivery_no_warehouse', true);
		}
	?>
	<?php if ($expectedDelivery !== '') : ?>
		<tr>
			<th><?php _e( 'Expected delivery'); ?></th>
			<td><?php echo $expectedDelivery; ?></td>
		</tr>
	<?php endif; ?>
</table>
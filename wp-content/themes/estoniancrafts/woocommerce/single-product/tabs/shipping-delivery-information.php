<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>

<table class="shop_attributes" style="text-transform:none;">
	<?php
		$expectedDelivery = '';
		// if is stock product and some products exist at stock
		echo '<tr>';
		
		
		if ($product->managing_stock() && $product->get_stock_quantity() ) {
			$delivery = get_post_meta( $product->id, '_expected_delivery_in_warehouse', true);
			$durations_array =  ec_get_shipping_durations_array();
			if($delivery !== ''){
				if(is_numeric($delivery) && isset($durations_array[$delivery])){
					$delivery = $durations_array[$delivery];
				}else{
					$delivery = '';
				}
			}
		
		
			echo '<th  style="text-transform:none;">Ready to ship in: </th>';
			echo '<td>'.$delivery.'</td>';	
	
		} else {
			$expectedDelivery = get_post_meta( $product->id, '_expected_delivery_no_warehouse', true);
			echo '<th  style="text-transform:none;">Ready to ship in: </th>';
			echo '<td>'.$expectedDelivery.'</td>';	
	
	
		}
		
		echo '</tr>';
		echo '<tr>';
			echo '<th  style="text-transform:none;">Expected delivery time: </th>';
			echo '<td>Up to 7 days.</td>';	
	
		echo '</tr>';
	?>
</table>
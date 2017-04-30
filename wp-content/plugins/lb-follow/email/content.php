<p>
	<?php _e('The following products were added to: ', 'ktt') ?>
</p>

<p>
	<?php 

	if(count($updates)){

		foreach($updates as $store){

			?>

			<h2><?= $store['name'] ?></h2>

			<ul>
				<?php 
				if(count($store['products'])){
					foreach($store['products'] as $product){
						?>
						<li><a href="<?= get_permalink($product['post_id']) ?>"><?= $product['post_title'] ?></a></li>
						<?php
					}
				}
				?>
			</ul>

			<?php

		}
	}
	?>
</p>

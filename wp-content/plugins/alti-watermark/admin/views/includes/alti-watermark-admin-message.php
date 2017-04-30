<div id="message" class="<?php echo $message['type']; ?>">
	<p>
		<?php echo $message['message']; ?>
		<?php if( !empty($message['id']) ) { 
			?>
			<div>
			<i>[<?php _e('Error code', $this->plugin_name); ?> : <?php echo sprintf('%03d', $message['id']); ?></i>] <a target="_blank" href="http://alticreation.com/en/alti-watermark#error<?php echo sprintf('%03d', $message['id']); ?>"><?php _e('Go to alti Watermark documentation', $this->plugin_name); ?></a>
			</div>
		<?php } ?>
	</p>
</div>
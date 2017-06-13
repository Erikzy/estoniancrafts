<?php
/**
 * Estoniancrafts ask information product tab Template
 */
?>

<h2><?php _e( 'Ask information from seller', 'ktt' ); ?></h2>

<div id="ask_information_form_container">
	<form id="ask_information_form" action="<?= admin_url('admin-ajax.php') ?>" method="POST">
		<input type="hidden" name="action" value="ask_information" />
		<input type="hidden" name="ask_information_token" value="<?= wp_create_nonce('ask-information') ?>" />

		<label>
		  	<span class="title"><?php _e( 'First name' ); ?></span>
			<span class="input-text-wrap"><input class="input-text" type="text" name="first_name" value="" required="true"/></span>
		</label>
		<label>
		  	<span class="title"><?php _e( 'Last name' ); ?></span>
			<span class="input-text-wrap"><input class="input-text" type="text" name="last_name" value="" required="true"/></span>
		</label>
		<label>
		  	<span class="title"><?php _e( 'Email' ); ?></span>
			<span class="input-text-wrap"><input class="input-text" type="email" name="email" value="" required="true" /></span>
		</label>
		<label>
			<span class="title"><?php _e( 'Content' ); ?></span>
			<span class="textarea-wrap"><textarea name="content" required="true"></textarea></span>
		</label>
		<input type="submit" name="send" value="Send" />
	</form>
</div>
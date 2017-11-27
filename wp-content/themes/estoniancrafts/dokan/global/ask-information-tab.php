<?php
/**
 * Estoniancrafts ask information product tab Template
 */
?>
<div>
	<?php $isLoggedIn = $user && $user->ID; ?>

	<div id="ask_information_form_container">
		<form id="ask_information_form" action="<?= admin_url('admin-ajax.php') ?>" method="POST">
			<input type="hidden" name="action" value="ask_information" />
			<input type="hidden" name="ask_information_token" value="<?= wp_create_nonce('ask-information') ?>" />
			<input type="hidden" name="product_id" value="<?= $product->id ?>">

            <?php if ($isLoggedIn) { ?>
                <input type="hidden" name="first_name" value="<?= $isLoggedIn ? $user->first_name : '' ?>" required="true"/>
                <input type="hidden" name="last_name" value="<?= $isLoggedIn ? $user->last_name : '' ?>" required="true"/>
                <input type="hidden" name="email" value="<?= $isLoggedIn ? $user->user_email : '' ?>" required="true" />
            <?php } else { ?>
			<label>
			  	<span class="title"><?php _e( 'First name' ); ?></span>
				<span class="input-text-wrap"><input class="input-text" type="text" name="first_name" value="<?= $isLoggedIn ? $user->first_name : '' ?>" required="true"/></span>
			</label>
			<label>
			  	<span class="title"><?php _e( 'Last name' ); ?></span>
				<span class="input-text-wrap"><input class="input-text" type="text" name="last_name" value="<?= $isLoggedIn ? $user->last_name : '' ?>" required="true"/></span>
			</label>
			<label>
			  	<span class="title"><?php _e( 'Email' ); ?></span>
				<span class="input-text-wrap"><input class="input-text" type="email" name="email" value="<?= $isLoggedIn ? $user->user_email : '' ?>" required="true" /></span>
			</label>
            <?php } ?>

			<label class="stretch">
				<span class="title"><?php _e( 'Content' ); ?></span>
				<span class="textarea-wrap"><textarea name="content" required="true"></textarea></span>
			</label>
			<p>
				<input type="submit" class='smaller-orange-button' name="send" value="Send" />
			</p>
		</form>
	</div>
</div>
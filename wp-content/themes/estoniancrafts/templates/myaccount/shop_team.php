<h2><?= __('Team', 'ktt') ?></h2>
<p>
<form id="ec_add_shop_user" action="<?= admin_url( 'admin-ajax.php' ) ?>" method="POST">
	<?php wp_nonce_field( 'ec_add_shop_user', '_wpnonce') ?>
	<input name="action" value="ec_add_shop_user" type="hidden" />
	<input class="input-text team-mail" type="email" name="email" autocomplete="email" placeholder="<?= __('Insert new user email', 'ktt') ?>" />
	<button style="display: none;" class="btn button btn-color-primary medium-orange-button" type="submit"><?= __('Add user', 'ktt') ?></button>
</form>
</p>
<table id="shop_member_container">
	<?php 
	foreach ($members as $member) {
		$relation = $member['relation'];
		$user = $member['user'];
		if ($relation && $user) {
			include(locate_template('templates/myaccount/shop_team_list_user.php'));
		}
	}

	?>
</table>
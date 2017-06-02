<?php if( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="ec_user_relation_<?= $relation->id ?>">
	<div style="display: inline-block;">
	<img src="<?= get_avatar_url($user->ID) ?>" />
	<div>
	<div style="display: inline-block;">
		<?= $user->display_name ?>
		<form class="ec_add_user_relation_value" action="<?= admin_url( 'admin-ajax.php' ) ?>" method="POST">
			<?php wp_nonce_field( 'ec_add_user_relation_value_' . $relation->id, '_wpnonce') ?>
			<input name="relation_id" value="<?= $relation->id ?>" type="hidden" />
			<input name="action" value="ec_add_user_relation_value" type="hidden" />
			<input class="input-text" type="text" name="value" value="<?= $relation->getValue() ?>" />
			<button style="display: none;" class="btn btn-color-primary" type="submit">Salvesta</button>
		</form>
	</div>
	<a class="ec_remove_shop_user" href="<?= admin_url( 'admin-ajax.php' ) ?>" data-relation-id="<?= $relation->id ?>" data-security="<?= wp_create_nonce( 'ec_remove_shop_user') ?>">Eemalda</a>
</div>

<?php /* ?>
<!-- USE THIS TO ADD IT -->
<!-- add user to the shop widget -->
<form id="ec_add_shop_user" action="<?= admin_url( 'admin-ajax.php' ) ?>" method="POST">
	<?php wp_nonce_field( 'ec_add_shop_user', '_wpnonce') ?>
	<input name="action" value="ec_add_shop_user" type="hidden" />
	<input class="input-text" type="email" name="email" autocomplete="email" />
	<button style="display: none;" class="btn btn-color-primary" type="submit">Add user</button>
</form>
<p id="shop_member_container"></p>
<!-- end if widget -->

<?php */ ?>
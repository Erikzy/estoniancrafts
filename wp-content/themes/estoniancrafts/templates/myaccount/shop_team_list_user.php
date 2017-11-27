<?php if( ! defined( 'ABSPATH' ) ) exit; ?>
<tr <?php if (!$relation->isShopAdmin()):?> id="ec_user_relation_<?= $relation->id ?>" <?php endif; ?>>
	<td class="profile-avatar">
		<img src="<?= get_avatar_url($user->ID) ?>" />
	</td>
	<td class="profile-details">
		<p><a href="<?= home_url('/user/'.$user->data->user_login) ?>"><h3><?= $user->display_name ?></h3></a></p>
		<form class="ec_add_user_relation_value" action="<?= admin_url( 'admin-ajax.php' ) ?>" method="POST">
			<?php wp_nonce_field( 'ec_add_user_relation_value_' . $relation->id, '_wpnonce') ?>
			<input name="relation_id" value="<?= $relation->id ?>" type="hidden" />
			<input name="action" value="ec_add_shop_user_relation_value" type="hidden" />

			<p>
				<label for="value"><?= __('Job title', 'ktt') ?></label>
				<input class="input-text team-title" type="text" name="value" value="<?= $relation->getValue() ?>" width="200px;"/>
				<button style="display: none;" class="btn button btn-color-primary medium-orange-button" type="submit"><?= __('Save', 'ktt') ?></button>
			</p>
		</form>
		<?php if (!$relation->isShopAdmin()): ?>
		<p><a class="ec_remove_shop_user" href="<?= admin_url( 'admin-ajax.php' ) ?>" data-relation-id="<?= $relation->id ?>" data-security="<?= wp_create_nonce( 'ec_remove_shop_user') ?>"><?= __('Remove', 'ktt')?></a>
		<?php endif; ?></p>
	</td>
</tr>
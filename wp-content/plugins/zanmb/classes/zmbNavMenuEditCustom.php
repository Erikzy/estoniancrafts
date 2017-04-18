<?php
/**
 * @package nav-menu-custom-fields
 * @version 0.1.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}
/*
 * Saves new field to postmeta for navigation
 */
add_action( 'wp_update_nav_menu_item', 'zmb_custom_nav_update', 10, 3 );
function zmb_custom_nav_update( $menu_id, $menu_item_db_id, $args ) {
	if ( isset( $_POST['zam-enable-mega'][$menu_item_db_id] ) ) {
		if ( is_array( $_POST['zam-enable-mega'] ) ) {
			$enable_mega = $_POST['zam-enable-mega'][$menu_item_db_id];
			update_post_meta( $menu_item_db_id, '_menu_item_enable_zmb', $enable_mega );
		}
	}
	if ( isset( $_POST['menu-item-mega-url'][$menu_item_db_id] ) ) {
		if ( is_array( $_POST['menu-item-mega-url'] ) ) {
			$menu_item_mega_url = $_POST['menu-item-mega-url'][$menu_item_db_id];
			update_post_meta( $menu_item_db_id, '_menu_item_mega_url', $menu_item_mega_url );
		}
	}
	if ( isset( $_POST['menu-item-mega-width'][$menu_item_db_id] ) ) {
		if ( is_array( $_POST['menu-item-mega-width'] ) ) {
			$menu_item_mega_width = $_POST['menu-item-mega-width'][$menu_item_db_id];
			update_post_meta( $menu_item_db_id, '_menu_item_mega_width', $menu_item_mega_width );
		}
	}
	if ( isset( $_POST['menu-item-icon-classes'][$menu_item_db_id] ) ) {
		if ( is_array( $_POST['menu-item-icon-classes'] ) ) {
			$menu_item_icon_classes = $_POST['menu-item-icon-classes'][$menu_item_db_id];
			update_post_meta( $menu_item_db_id, '_menu_item_icon_classes', $menu_item_icon_classes );
		}
	}
	if ( isset( $_POST['menu-item-icon-img-url'][$menu_item_db_id] ) ) {
		if ( is_array( $_POST['menu-item-icon-img-url'] ) ) {
			$menu_item_icon_img_url = $_POST['menu-item-icon-img-url'][$menu_item_db_id];
			update_post_meta( $menu_item_db_id, '_menu_item_icon_img_url', $menu_item_icon_img_url );
		}
	}
	if ( isset( $_POST['mega-item-drop-direction'][$menu_item_db_id] ) ) {
		if ( is_array( $_POST['mega-item-drop-direction'] ) ) {
			$mega_item_drop_direction = $_POST['mega-item-drop-direction'][$menu_item_db_id];
			update_post_meta( $menu_item_db_id, '_mega_item_drop_direction', $mega_item_drop_direction );
		}
	}
}

/*
 * Adds value of new field to $item object that will be passed to     ZMB_Walker_Nav_Menu_Edit_Custom
 */
add_filter( 'wp_setup_nav_menu_item', 'zmb_custom_nav_item' );
function zmb_custom_nav_item( $menu_item ) {
	$menu_item->enable_mega = get_post_meta( $menu_item->ID, '_menu_item_enable_zmb', true );
	$menu_item->mega_url = get_post_meta( $menu_item->ID, '_menu_item_mega_url', true );
	$menu_item->mega_width = get_post_meta( $menu_item->ID, '_menu_item_mega_width', true );
	$menu_item->icon_classes = get_post_meta( $menu_item->ID, '_menu_item_icon_classes', true );
	$menu_item->icon_img_url = get_post_meta( $menu_item->ID, '_menu_item_icon_img_url', true );
	$menu_item->mega_item_drop_direction = get_post_meta( $menu_item->ID, '_mega_item_drop_direction', true );

	return $menu_item;
}

add_filter( 'wp_edit_nav_menu_walker', 'zmb_custom_nav_edit_walker', 110, 2 );
function zmb_custom_nav_edit_walker( $walker, $menu_id ) {
	return 'ZMB_Walker_Nav_Menu_Edit_Custom';
}

/**
 * Copied from Walker_Nav_Menu_Edit class in core
 *
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since   3.0.0
 * @uses    Walker_Nav_Menu
 */
class ZMB_Walker_Nav_Menu_Edit_Custom extends Walker_Nav_Menu
{
	/**
	 * Starts the list before the elements are added.
	 *
	 * @see   Walker_Nav_Menu::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) { }

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see   Walker_Nav_Menu::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) { }

	/**
	 * Start the element output.
	 *
	 * @see   Walker_Nav_Menu::start_el()
	 * @since 3.0.0
	 *
	 * @global int   $_wp_nav_menu_max_depth
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   Not used.
	 * @param int    $id     Not used.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		// Post types allowed to enable mega menu (zanmb post type is alwaya allowed)
		$post_types_allowed_mega = apply_filters( 'zmb_post_types_allowed_mega', array( 'post', 'page' ) ); // and zanmb
		$can_custom_mega = in_array( esc_attr( $item->object ), $post_types_allowed_mega );

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		}
		elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = get_the_title( $original_object->ID );
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
		);

		if ( $item->enable_mega == 'yes' ) {
			$classes[] = 'zmb-show-mega-options';
		}

		$title = $item->title;

		if ( !empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		}
		elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __( '%s (Pending)' ), $item->title );
		}

		$title = ( !isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

		$submenu_text = '';
		if ( 0 == $depth )
			$submenu_text = 'style="display: none;"';

		?>
	<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
		<div class="menu-item-bar">
			<div class="menu-item-handle">
				<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span
						class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' ); ?></span></span>
    				<span class="item-controls">
					    <?php if ( $can_custom_mega ) {

						    $switch_wrap_class = '';
						    if ( $item->enable_mega == 'yes' ) {
							    $switch_wrap_class .= 'zmb-on';
						    }

						    ?>
						    <span class="zmb-switch-wrap <?php echo esc_attr( $switch_wrap_class ); ?>">
							    <a href="<?php echo esc_url( get_edit_post_link( $item->object_id ) ); ?>"
							       target="_blank"
							       class="zmb-edit"><?php esc_html_e( 'Edit', 'zanmb' ); ?></a>
							    <label class="zmb-switch">
								    <input type="hidden" class="zam-enable-mega"
								           name="zam-enable-mega[<?php echo $item_id; ?>]"
								           value="<?php echo esc_attr( $item->enable_mega ); ?>">
								    <input class="zam-enable-mega-cb"
									    <?php checked( $item->enable_mega == 'yes' ); ?>
									       type="checkbox">
								    <span class="zmb-slider round"></span>
								    <span class="zmb-switch-text"><?php esc_html_e( 'Mega', 'zanmb' ); ?></span>
							    </label>
						    </span>
					    <?php } ?>

					    <?php if ( esc_attr( $item->object ) == 'zanmenu' ) { ?>
						    <a href="<?php echo esc_url( get_edit_post_link( $item->object_id ) ); ?>"
						       target="_blank"
						       class="zmb-edit"><?php esc_html_e( 'Edit', 'zanmb' ); ?></a>
					    <?php } ?>

					    <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
    					<span class="item-order hide-if-js">
    						<a href="<?php
						    echo wp_nonce_url(
							    add_query_arg(
								    array(
									    'action'    => 'move-up-menu-item',
									    'menu-item' => $item_id,
								    ),
								    remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
							    ),
							    'move-menu_item'
						    );
						    ?>" class="item-move-up"><abbr title="<?php esc_attr_e( 'Move up' ); ?>">&#8593;</abbr></a>
    						|
    						<a href="<?php
						    echo wp_nonce_url(
							    add_query_arg(
								    array(
									    'action'    => 'move-down-menu-item',
									    'menu-item' => $item_id,
								    ),
								    remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
							    ),
							    'move-menu_item'
						    );
						    ?>" class="item-move-down"><abbr title="<?php esc_attr_e( 'Move down' ); ?>">&#8595;</abbr></a>
    					</span>
    					<a class="item-edit" id="edit-<?php echo $item_id; ?>"
					       title="<?php esc_attr_e( 'Edit Menu Item' ); ?>" href="<?php
					    echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
					    ?>"><?php _e( 'Edit Menu Item' ); ?></a>
    				</span>
			</div>
		</div>

		<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
			<?php if ( 'custom' == $item->type ) : ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo $item_id; ?>">
						<?php _e( 'URL' ); ?><br/>
						<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>"
						       class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]"
						       value="<?php echo esc_attr( $item->url ); ?>"/>
					</label>
				</p>
			<?php endif; ?>
			<?php if ( esc_attr( $item->object ) == 'zanmenu' ): ?>
				<p class="field-zanmenu-custom field-url description description-wide">
					<label for="edit-menu-item-mega-url<?php echo $item_id; ?>">
						<?php esc_attr_e( 'Mega Item URL', 'zanmb' ); ?><br/>
						<input type="text" id="edit-menu-item-mega-url-<?php echo $item_id; ?>"
						       class="widefat code edit-menu-item-mega-url"
						       name="menu-item-mega-url[<?php echo $item_id; ?>]"
						       value="<?php echo esc_attr( $item->mega_url ); ?>"/>
					</label>
				</p>
			<?php endif; ?>
			<p class="description description-wide">
				<label for="edit-menu-item-title-<?php echo $item_id; ?>">
					<?php _e( 'Navigation Label' ); ?><br/>
					<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>"
					       class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]"
					       value="<?php echo esc_attr( $item->title ); ?>"/>
				</label>
			</p>
			<p class="field-title-attribute description description-wide">
				<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
					<?php _e( 'Title Attribute' ); ?><br/>
					<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>"
					       class="widefat edit-menu-item-attr-title"
					       name="menu-item-attr-title[<?php echo $item_id; ?>]"
					       value="<?php echo esc_attr( $item->post_excerpt ); ?>"/>
				</label>
			</p>
			<p class="field-link-target description">
				<label for="edit-menu-item-target-<?php echo $item_id; ?>">
					<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank"
					       name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
					<?php _e( 'Open link in a new window/tab' ); ?>
				</label>
			</p>
			<p class="field-css-classes description description-thin">
				<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
					<?php _e( 'CSS Classes (optional)' ); ?><br/>
					<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>"
					       class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]"
					       value="<?php echo esc_attr( implode( ' ', $item->classes ) ); ?>"/>
					<em class="desc"><?php esc_attr_e( 'For social icon: zmb-social. Mini cart: zmb-cart. Logo: logo. For other icons: zmb-icon. For toggle sliding content: zmb-show-sliding.', 'zanmb' ); ?></em>
				</label>
			</p>
			<p class="field-icon-classes description description-thin">
				<label for="edit-menu-item-icon-classes-<?php echo $item_id; ?>">
					<?php _e( 'Icon Classes (optional)' ); ?><br/>
					<input type="text" id="edit-menu-item-icon-classes-<?php echo $item_id; ?>"
					       class="widefat code edit-menu-item-icon-classes"
					       name="menu-item-icon-classes[<?php echo $item_id; ?>]"
					       value="<?php echo isset( $item->icon_classes ) ? esc_attr( $item->icon_classes ) : ''; ?>"/>
					<em class="desc"><?php esc_attr_e( 'Ex: fa fa-adjust. Icon picker: ', 'zanmb' ); ?>
						<a href="#" data-font-icon="FontAwesome" class="zmb-icon-picker">FontAwesome</a>
					</em>
				</label>
			</p>
			<div class="field-icon-img-url description description-wide">
				<label for="edit-menu-item-icon-img-url-<?php echo $item_id; ?>">
					<?php _e( 'Icon Image (optional)' ); ?><br/>
					<input type="text" id="edit-menu-item-icon-img-url-<?php echo $item_id; ?>"
					       class="widefat code edit-menu-item-icon-img-url"
					       name="menu-item-icon-img-url[<?php echo $item_id; ?>]"
					       value="<?php echo isset( $item->icon_img_url ) ? esc_attr( $item->icon_img_url ) : ''; ?>"
					       placeholder="<?php esc_attr_e( 'Paste icon image url here', 'zanmb' ); ?>"/>
					<em class="desc"><?php esc_attr_e( 'Input icon image url or choose a default image below', 'zanmb' ); ?></em>
				</label>
				<span class="icon-img-default-wrap">
					<ul class="icon-img-list">
						<?php for ( $i = 12; $i <= 21; $i++ ) { ?>
							<li>
								<a href="#"
								   class="icon-img-url <?php echo $item->icon_img_url == ZMB_IMG_URL . 'menu-icons/' . $i . '.png' ? 'active' : ''; ?>">

									<img
										src="<?php echo esc_url( ZMB_IMG_URL . 'menu-icons/' . $i . '.png' ); ?>"
										alt="<?php echo $i; ?>"/>
								</a>
							</li>
						<?php } ?>
					</ul>
				</span>
			</div>
			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
					<?php _e( 'Link Relationship (XFN)' ); ?><br/>
					<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>"
					       class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]"
					       value="<?php echo esc_attr( $item->xfn ); ?>"/>
				</label>
			</p>
			<p class="field-description description description-wide">
				<label for="edit-menu-item-description-<?php echo $item_id; ?>">
					<?php _e( 'Description' ); ?><br/>
					<textarea id="edit-menu-item-description-<?php echo $item_id; ?>"
					          class="widefat edit-menu-item-description" rows="3" cols="20"
					          name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
					<span
						class="description"><?php _e( 'The description will be displayed in the menu if the current theme supports it.' ); ?></span>
				</label>
			</p>

			<p class="field-move hide-if-no-js description description-wide">
				<label>
					<span><?php _e( 'Move' ); ?></span>
					<a href="#" class="menus-move menus-move-up" data-dir="up"><?php _e( 'Up one' ); ?></a>
					<a href="#" class="menus-move menus-move-down" data-dir="down"><?php _e( 'Down one' ); ?></a>
					<a href="#" class="menus-move menus-move-left" data-dir="left"></a>
					<a href="#" class="menus-move menus-move-right" data-dir="right"></a>
					<a href="#" class="menus-move menus-move-top" data-dir="top"><?php _e( 'To the top' ); ?></a>
				</label>
			</p>

			<?php
			/*
			 * This is the added field for mega menu item
			 */
			if ( esc_attr( $item->object ) == 'zanmenu' || $can_custom_mega ):
				?>
				<p class="field-zanmenu-custom description description-thin">
					<label for="edit-menu-item-mega-width<?php echo $item_id; ?>">
						<?php esc_attr_e( 'Zan Menu Width', 'zanmb' ); ?><br/>
						<input type="number" min="1" max="1920"
						       id="edit-menu-item-mega-width-<?php echo $item_id; ?>"
						       class="widefat code edit-menu-item-mega-witdh"
						       name="menu-item-mega-width[<?php echo $item_id; ?>]"
						       value="<?php echo esc_attr( $item->mega_width ); ?>"/>
					</label>
				</p>
				<p class="field-zanmenu-custom description description-thin">
					<label for="edit-menu-item-mega-item-drop-direction<?php echo $item_id; ?>">
						<?php esc_attr_e( 'Drop Direction', 'zanmb' ); ?><br/>
						<select name="mega-item-drop-direction[<?php echo $item_id; ?>]">
							<option <?php selected( true, $item->mega_item_drop_direction == '' ); ?>
								value=""><?php esc_html_e( 'Default', 'zanmb' ); ?></option>
							<option <?php selected( true, $item->mega_item_drop_direction == 'left' ); ?>
								value="left"><?php esc_html_e( 'Left', 'zanmb' ); ?></option>
							<option <?php selected( true, $item->mega_item_drop_direction == 'right' ); ?>
								value="right"><?php esc_html_e( 'Right', 'zanmb' ); ?></option>
						</select>
					</label>
				</p>
				<?php
			endif; // End if ( esc_attr( $item->object ) == 'zanmenu' )
			/*
			 * end added field
			 */
			?>

			<div class="menu-item-actions description-wide submitbox">
				<?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
					<p class="link-to-original">
						<?php printf( __( 'Original: %s' ), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
					</p>
				<?php endif; ?>
				<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
				echo wp_nonce_url(
					add_query_arg(
						array(
							'action'    => 'delete-menu-item',
							'menu-item' => $item_id,
						),
						admin_url( 'nav-menus.php' )
					),
					'delete-menu_item_' . $item_id
				); ?>"><?php _e( 'Remove' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a
					class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>"
					href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
					?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e( 'Cancel' ); ?></a>
			</div>

			<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]"
			       value="<?php echo $item_id; ?>"/>
			<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->object_id ); ?>"/>
			<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->object ); ?>"/>
			<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
			<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->menu_order ); ?>"/>
			<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]"
			       value="<?php echo esc_attr( $item->type ); ?>"/>
		</div><!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}
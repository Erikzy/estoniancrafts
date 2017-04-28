<?php

class EC_Filters
{
	public static function init()
	{
		// Public section
		add_filter( 'ec_get_page_personal_profile', array(__CLASS__, 'ec_get_page_personal_profile_filter'), 1 );
		add_filter( 'ec_get_store_page', array(__CLASS__, 'ec_get_store_page_filter'), 1 );

		// Merchant section
		add_filter( 'ec_get_page_merchant_products', array(__CLASS__, 'ec_get_page_merchant_products_filter'), 1 );
        add_filter( 'ec_get_myaccount_menu', array(__CLASS__, 'ec_get_myaccount_menu_filter'), 1 );
	}

	/**
	 * @param EC_Merchant_Products $page
	 * @return \EC_Merchant_Products
	 */
	public static function ec_get_page_merchant_products_filter($page=null)
	{
		if(is_null($page))
		{
			global $post;
			include_once('Blocks/Pages/EC_Merchant_Products.php');
			$page = new EC_Merchant_Products();
			$page->load( $post );
		}

		return $page;
	}

	/**
	 * @param EC_Menu $menu
	 * @return \EC_Menu
	 */
    public static function ec_get_myaccount_menu_filter( $menu=null )
	{
		$user = wp_get_current_user();
		$submenuPrefix = '&nbsp; - ';

		include_once(get_stylesheet_directory().'/Blocks/Objects/EC_Menu.php');
		include_once(get_stylesheet_directory().'/Blocks/Objects/EC_MenuItem.php');
		$menu = new EC_Menu();

		// Is merchant
		if(in_array('seller', $user->roles))
		{
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'edit-account',
				'title' => __( 'Edit Account', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/edit-account'),
				'url_endpoint' => 'my-account/dashboard/edit-account'
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'orders',
				'title' => __( 'My Orders', 'ktt' ),
				'url' => get_site_url(null, 'my-account/orders'),
				'url_endpoint' => 'my-account/orders'
			));
            $menu->items[] = new EC_MenuItem(array(
				'id' => 'messages',
				'title' => __( 'My Messages', 'ktt' ) .(bp_get_total_unread_messages_count() > 0 ? ' ('.bp_get_total_unread_messages_count().')' : ''),
				'url' => get_site_url(null, 'members/'.$user->user_nicename.'/messages/'),
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop',
				'title' => __( 'My Shop', 'ktt' )
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-dashboard',
				'title' => $submenuPrefix.__( 'Dashboard', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard'),
				'url_endpoint' => 'my-account/dashboard'
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-products',
				'title' => $submenuPrefix.__( 'Products', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/products'),
				'url_endpoint' => 'my-account/dashboard/products'
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-orders',
				'title' => $submenuPrefix.__( 'Orders', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/orders'),
				'url_endpoint' => 'my-account/dashboard/orders'
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-reports',
				'title' => $submenuPrefix.__( 'Reports', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/reports'),
				'url_endpoint' => 'my-account/dashboard/reports'
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-settings',
				'title' => $submenuPrefix.__( 'Settings', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/settings/store'),
				'url_endpoint' => 'my-account/dashboard/settings/store'
			));
		}
		// Not a merchant
		else
		{
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'edit-account',
				'title' => __( 'Edit Account', 'woocommerce' ),
				'url' => get_site_url(null, 'my-account/edit-account'),
				'url_endpoint' => 'my-account/edit-account'
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'my-account/orders',
				'title' => __( 'My Orders', 'ktt' ),
				'url' => get_site_url(null, 'my-account/orders'),
				'url_endpoint' => 'my-account/orders'
			));
            $menu->items[] = new EC_MenuItem(array(
				'id' => 'messages',
				'title' => __( 'My Messages', 'ktt' ),
				'url' => get_site_url(null, 'members/'.$user->user_nicename.'/messages/'),
			));
//			$menu->items[] = new EC_MenuItem(array(
//				'id' => 'my-account/student',
//				'title' => __( 'Student pages', 'ktt' ),
//				'url' => get_site_url(null, 'student'),
//				'url_endpoint' => 'student'
//			));
		}

		// Global
                $menu->items[] = new EC_MenuItem(array(
			'id' => 'support',
			'title' => __( 'Support', 'ktt' ),
			'url' => get_site_url(null, 'my-account/support'),
			'url_endpoint' => 'my-account/support'
		));
		$menu->items[] = new EC_MenuItem(array(
			'id' => 'logout',
			'title' => __( 'Logout', 'woocommerce' ),
			'url' => get_site_url(null, 'my-account/customer-logout'),
			'url_endpoint' => 'my-account/customer-logout'
		));

		return $menu;
    }

	/**
	 * @param WP_User $user
	 * @return \EC_Personal_Profile_Page
	 */
	public static function ec_get_page_personal_profile_filter($page=null)
	{
		if(is_null($page))
		{
			include_once('Blocks/Pages/EC_Personal_Profile_Page.php');
			$page = new EC_Personal_Profile_Page();
			$page->load();
		}

		return $page;
	}

	/**
	 * @param EC_Block $page
	 * @return \EC_Store_Page
	 */
	public static function ec_get_store_page_filter($page=null)
	{
		if(is_null($page))
		{
			include_once('Blocks/Pages/EC_Store_Page.php');
			$page = new EC_Store_Page();
			$page->load();
		}

		return $page;
	}
}
EC_Filters::init();

add_filter( 'tribe_event_featured_image', 'custom_tribe_event_featured_image' );

function custom_tribe_event_featured_image($featured_image, $post_id = false, $size = false)
{
    $tpl = '<div class="tribe-events-image-header">'.tribe_get_venue().'</div>';
    $tpl .= '<div class="tribe-events-image-header-time">'.custom_tribe_events_event_schedule_details().'</div>';

    $featured_image = str_replace('<div class="tribe-events-event-image">', '<div class="tribe-events-event-image">'.$tpl, $featured_image);
    return $featured_image;
}
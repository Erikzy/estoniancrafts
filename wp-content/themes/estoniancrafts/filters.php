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
            /*$menu->items[] = new EC_MenuItem(array(
				'id' => 'messages',
				'title' => __( 'My Messages', 'ktt' ) .(bp_get_total_unread_messages_count() > 0 ? ' ('.bp_get_total_unread_messages_count().')' : ''),
				'url' => get_site_url(null, 'members/'.$user->user_nicename.'/messages/'),
			));*/
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
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-team',
				'title' => $submenuPrefix.__( 'Team', 'ktt' ),
				'url' => get_site_url(null, 'my-account/team'),
				'url_endpoint' => 'my-account/team'
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
		$menu->items[] = new EC_MenuItem([
			'id' => 'disputes',
			'title' => __('Disputes', 'ktt'),
			'url' => get_site_url(null, 'my-account/disputes'),
			'url_endpoint' => 'my-account/disputes'
		]);
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

/* Shipping&Delivery tab */ //  currently not needed for any task, but I added it anyways, because it will be needed in the future
add_filter( 'woocommerce_product_tabs', 'ec_custom_tabs', 99, 1);

function ec_custom_tabs($tabs)
{
	$tabs['basel_additional_tab']['callback'] = 'ec_additional_product_tab_content';

	return $tabs;
}

function ec_additional_product_tab_content()
{
	include (get_stylesheet_directory() . '/woocommerce/single-product/tabs/shipping-delivery-information.php');
}

// expected delivery
add_filter('ec_order_review_expected_delivery', 'ec_order_review_expected_delivery', 1, 1);

function ec_order_review_expected_delivery($product)
{
	$delivery = '';
	// if is stock item and has items in stock
	if ($product->managing_stock() && $product->get_stock_quantity()) {
		$delivery = get_post_meta( $product->id, '_expected_delivery_in_warehouse', true);
	} else {
		$delivery = get_post_meta( $product->id, '_expected_delivery_no_warehouse', true);
	}

	if ($delivery !== '') {
		echo '<p>';
		_e('Delivery');
		echo ': ' . $delivery;
		echo '</p>';
	}
}

// Insert the email content to user's buddypress inbox
add_filter( 'wp_mail', 'my_mail');

function my_mail($data){

    // Lets not get into loop
    if (isset($data['headers']['ignore_bb'])) {
        return $data;
    }

    if (isset($data['to']) && !empty($data['to']) && is_string($data['to'])) {

        $user = get_user_by( 'email', $data['to'] );
        if ($user) {
            global $wpdb;
            $bp = buddypress();

            // Get new thread ID
            $thread_id = (int) $wpdb->get_var( "SELECT MAX(thread_id) FROM {$bp->messages->table_name_messages}" ) + 1;

            // If we have a logged inuser then use it
            $sender_id = bp_loggedin_user_id() ? bp_loggedin_user_id() : 1;
            $recipient_id = $user->data->ID;
            $subject = ! empty( $data['subject'] ) ? $data['subject'] : false;
            $message = ! empty( $data['message'] ) ? $data['message'] : false;
//            $message = strip_tags($message, '<a><p><h1><h2><h3><h4><table><thead><tbody><tfoot><th><td><tr>');

            $date_sent = bp_core_current_time();

            // First insert the message into the messages table.
            if ( ! $wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->messages->table_name_messages} ( thread_id, sender_id, subject, message, date_sent ) VALUES ( %d, %d, %s, %s, %s )", $thread_id, $sender_id, $subject, $message, $date_sent ) ) ) {
                return false;
            }

            // Add an recipient entry for all recipients.
            $wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->messages->table_name_recipients} ( user_id, thread_id, unread_count ) VALUES ( %d, %d, 1 )", $recipient_id, $thread_id ) );
        }
    }

    return $data;
}

// Extend buddypress messages to send them even by user email address
add_filter( 'bp_messages_recipients', 'custom_bp_messages_recipients');

function custom_bp_messages_recipients($recipients) {

    // Just to be sure we handle an array
    if (is_array($recipients)) {

        // We need to remove the empty recipients
        $recipients = array_filter($recipients);

        // Change the email to username
        foreach ($recipients as $key => $value) {
            if (strpos($value, '@')) {
                $user = get_user_by('email', trim($value));

                // Check if the user is found
                if ($user) {
                    $recipients[$key] = $user->data->user_nicename;
                } else {
                    wp_mail($value,$_POST['subject'], $_POST['content'],['ignore_bb' => true]);

                    if (count($recipients) == 1) {
                        // Setup the link to the logged-in user's messages.
                        $member_messages = trailingslashit( bp_loggedin_user_domain() . bp_get_messages_slug() );
                        $redirect_to = trailingslashit( $member_messages . 'compose' );

                        $feedback    = __( 'Message successfully sent by email.', 'buddypress' );

                        // Add feedback message.
                        bp_core_add_message( $feedback, 'success' );

                        // Redirect to previous page
                        bp_core_redirect( $redirect_to );
                    }
                }
            }
        }
    }

    return $recipients;
}

// Send as an email if it's a regular buddypress message
add_filter( 'messages_message_sent', 'my_messages_message_sent');

function my_messages_message_sent($message) {

    // Just to be sure we handle an object
    if (is_object($message)) {

        // If we have recipients
        if (!empty($message->recipients) and is_array($message->recipients)) {

            // Send an email to all recipient
            foreach ($message->recipients as $row) {
                $user = get_userdata($row->user_id);

                // Check if the user is found
                if ($user) {
                    wp_mail($user->data->user_email,$message->subject, $message->message,['ignore_bb' => true]);
                }
            }
        }
    }
    return true;
}

/**
 * Adds a 'Ask information' tab in product single page
 *
 * @param array $tabs
 * @return array
 */
function ec_dokan_ask_information_product_tab( $tabs) {

    $tabs['ask_information'] = array(
        'title'    => __( 'Ask information', 'ktt' ),
        'priority' => 90,
        'callback' => 'ec_dokan_ask_information_tab'
    );

    return $tabs;
}

add_filter( 'woocommerce_product_tabs', 'ec_dokan_ask_information_product_tab' );


/**
 * Prints information asking form in product single page
 *
 * @param type $val
 */
function ec_dokan_ask_information_tab( $val ) {
	global $product;

	$user = wp_get_current_user();
	
    dokan_get_template_part('global/ask-information-tab', '', [
    	'product' => $product,
    	'user' => $user
    ]);
}

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
		//$submenuPrefix = '&nbsp; - ';
		$submenuPrefix = '&nbsp;';
		//$submenuPrefix = '&nbsp; &nbsp; &nbsp; ';

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
				'title' => __( 'My Purchases', 'ktt' ),
				//'title' => __( 'My Orders', 'ktt' ),
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
				'title' => __( 'My Shop', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard'),
				'url_endpoint' => 'my-account/dashboard'
			));
			
			
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-view',
				'class'=> 'my-shop-item',
				'title' => $submenuPrefix.__( 'View shop', 'ktt' ),
				'url' => get_site_url(null, bp_core_get_username( $user->ID)),
				'url_endpoint' => bp_core_get_username( $user->ID)
			));



/*			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-dashboard',
				'title' => $submenuPrefix.__( 'Dashboard', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard'),
				'url_endpoint' => 'my-account/dashboard'
			));
*/
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-products',
				'class'=> 'my-shop-item',
				'title' => $submenuPrefix.__( 'Products', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/products'),
				'url_endpoint' => 'my-account/dashboard/products'
			));

			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-orders',
				'class'=> 'my-shop-item',
				'title' => $submenuPrefix.__( 'Orders', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/orders'),
				'url_endpoint' => 'my-account/dashboard/orders'
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-reports',
				'class'=> 'my-shop-item',
				'title' => $submenuPrefix.__( 'Reports', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/reports'),
				'url_endpoint' => 'my-account/dashboard/reports'
			));
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-settings',
				'class'=> 'my-shop-item',
				'title' => $submenuPrefix.__( 'Settings', 'ktt' ),
				'url' => get_site_url(null, 'my-account/dashboard/settings/store'),
				'url_endpoint' => 'my-account/dashboard/settings/store'
			));
	/*		$menu->items[] = new EC_MenuItem(array(
				'id' => 'shop-team',
				'class'=> 'my-shop-item',
				'title' => $submenuPrefix.__( 'Team', 'ktt' ),
				'url' => get_site_url(null, 'my-account/team'),
				'url_endpoint' => 'my-account/team'
			));*/
            $menu->items[] = new EC_MenuItem(array(
				'id' => 'blog',
				'class'=> 'my-shop-item',
				'title' => $submenuPrefix.__( 'Blog', 'ktt' ),
				'url' => get_site_url(null, 'my-account/blog'),
				'url_endpoint' => 'my-account/blog'
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
				//'title' => __( 'My Orders', 'ktt' ),
				'title' => __( 'My Purchases', 'ktt' ),
				'url' => get_site_url(null, 'my-account/orders'),
				'url_endpoint' => 'my-account/orders'
			));
			
			$menu->items[] = new EC_MenuItem(array(
				'id' => 'my-account/create-shop',
				//'title' => __( 'My Orders', 'ktt' ),
				'title' => __( 'Create Shop', 'ktt' ),
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
/*		$menu->items[] = new EC_MenuItem([
			'id' => 'disputes',
			'title' => __('Disputes', 'ktt'),
			'url' => get_site_url(null, 'my-account/disputes'),
			'url_endpoint' => 'my-account/disputes'
		]);
        
        
        // Global
		$menu->items[] = new EC_MenuItem([
			'id' => 'portfolio',
			'title' => __('Portfolio', 'ktt'),
			'url' => get_site_url(null, 'my-account/portfolio'),
			'url_endpoint' => 'my-account/portfolio'
		]);*/
        
        
        
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
			$page = EC_Personal_Profile_Page::getInstance();
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
			$page = EC_Store_Page::getInstance();
		}

		return $page;
	}
}
EC_Filters::init();




add_filter( 'tribe_event_featured_image', 'custom_tribe_event_featured_image' );

function custom_tribe_event_featured_image($featured_image, $post_id = null, $size = false)
{
    $tpl = '<div class="tribe-events-image-header">'.tribe_get_venue().'</div>';
    $tpl .= '<div class="tribe-events-image-header-time">'.custom_tribe_events_event_schedule_details().'</div>';

	$featured_image =  wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'event-calendar-image');

	$featured_image = '<img src="'. $featured_image[0].'"  />';
	if ( ! empty( $featured_image ) ) {
			$featured_image = '<a href="' . esc_url( tribe_get_event_link( $post_id ) ) . '">' . $featured_image . '</a>';
		}
	if ( ! empty( $featured_image )) {
			$featured_image = '<div class="tribe-events-event-image">' . $featured_image . '</div>';
		}
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

function ec_dokan_rewrite_rules($custom_store_url)
{


	 add_rewrite_rule( '([^/]+)/generallogin/userhome/', redirect_to_user_appropriate_home(), 'top' );
	add_rewrite_rule( '([^/]+)/?$', 'index.php?pagename=$matches[1]', 'top' );
    add_rewrite_rule( '([^/]+)/page/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&paged=$matches[2]', 'top' );

    add_rewrite_rule( '([^/]+)/section/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&term=$matches[2]&term_section=true', 'top' );
    add_rewrite_rule( '([^/]+)/section/?([0-9]{1,})/page/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&term=$matches[2]&paged=$matches[3]&term_section=true', 'top' );

    add_rewrite_rule( '([^/]+)/toc?$', 'index.php?pagename=$matches[1]&toc=true', 'top' );
    add_rewrite_rule( '([^/]+)/toc/page/?([0-9]{1,})/?$', 'index.php?pagename=$matches[1]&paged=$matches[2]&toc=true', 'top' );

    add_rewrite_rule( '([^/]+)/blog?$', 'index.php?pagename=$matches[1]&blog=true', 'top' );
		
    add_rewrite_rule( '^user/([^/]*)/portfolio/([^/]*)?$', 'index.php?user=$matches[1]&portfolio=$matches[2]', 'top');
    flush_rewrite_rules();
}
add_action('dokan_rewrite_rules_loaded', 'ec_dokan_rewrite_rules', 999, 1);

function ec_post_request($query_vars)
{
	if (array_key_exists('pagename', $query_vars)) {
		// check if it's store
		$seller = get_user_by( 'slug', $query_vars['pagename']);
		if ($seller && dokan_get_store_info($seller->data->ID)) {
			$custom_store_url = dokan_get_option( 'custom_store_url', 'dokan_general', 'store' );
			$query_vars[$custom_store_url] = $query_vars['pagename'];
			unset($query_vars['pagename']);
		} else if (array_key_exists('blog', $query_vars)) { // blog conflixt fix
			unset($query_vars['blog']);
			$query_vars['pagename'] .= '/blog';
		}
	}

	return $query_vars;
}
add_filter('request', 'ec_post_request', 0, 1);

function ec_register_query_vars($vars)
{
	$vars[] = 'blog';
	$vars[] = 'portfolio';

	return $vars;
}
add_filter('query_vars', 'ec_register_query_vars', 10, 1);

function ec_store_blog( $template )
{
	if (get_query_var('blog')) {
		$custom_store_url = dokan_get_option('custom_store_url', 'dokan_general', 'store');
		$store_name = get_query_var($custom_store_url);
		if ($store_name) {
			$store_user = get_user_by('slug', $store_name);
			if (!$store_user) {
				return get_404_template();
			}
			if (!dokan_is_user_seller($store_user->ID)) {
				return get_404_template();
			}


			include_once('Blocks/Pages/EC_Store_Page.php');
			$page = EC_Store_Page::getInstance();
			ob_start();
			include(dokan_locate_template( 'store-blog.php'));
			$page->custom_content = ob_get_clean();
			return dokan_locate_template('store.php');
		}
	}
	return $template;
}
add_filter('template_include', 'ec_store_blog', 1, 1);

function ec_user_portfolio( $template )
{
	if (($portfolioId = get_query_var('portfolio'))) {
		$portfolio = get_post((int)$portfolioId);
		if (!$portfolio) {
			return get_404_template();
		}

		$pictures = get_portfolio_pictures($portfolio);

		include_once('Blocks/Pages/EC_Personal_Profile_Page.php');
		$page = EC_Personal_Profile_Page::getInstance();
		ob_start();
		include (locate_template('templates/myaccount/portfolio-page.php'));
		$page->custom_content = ob_get_clean();
		return locate_template('user-profile.php');
	}
	return $template;
}
add_filter('template_include', 'ec_user_portfolio', 100, 1);

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



/*function m_wp_set_comment_status($comment_id){

}
add_filter('wp_set_comment_status','m_wp_set_comment_status');
*/
function filter_handler( $approved , $commentdata )
{
 
  return 1;
}

add_filter( 'pre_comment_approved' , 'filter_handler' , '99', 2 );



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

function comments_on( $data ) {
    if( $data['post_type'] == 'post' ) {
        $data['comment_status'] = 1;
    }

    return $data;
}

add_filter( 'wp_insert_post_data', 'comments_on' );

add_filter('previous_posts_link_attributes', 'posts_link_attributes_1');
add_filter('next_posts_link_attributes', 'posts_link_attributes_2');

function posts_link_attributes_1() {
    return 'class="prev page-numbers"';
}
function posts_link_attributes_2() {
    return 'class="next page-numbers"';
}

add_filter('media_upload_default_tab', 'wpse74422_switch_tab');
function wpse74422_switch_tab($tab)
{
    return 'type';
}


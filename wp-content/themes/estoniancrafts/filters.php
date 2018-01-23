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
        add_filter( 'eabi_omniva_autosend_data_before',array(__CLASS__,'ec_get_sender_data_filter'),9, 7);
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

	public static function ec_pdf_invoice_filter($address = ''){
	
	
	}


	public static function ec_get_sender_data_filter($requestData, 
													 $order, 
													 $packageValue , 
													 $selectedOffice, 
													 $codCurrency, 
													 $shippingModel,
													 $mainShippingModel){
		/*
		var_dump($requestData);
		$order->update_meta_data( 'my_custom_meta_key', 'my data' );
    	$order->save();
		*/
	
		$company_types = array(
			'1'=>  __( 'FIE', 'ktt'),
		 	'2'=>  __( 'OÜ', 'ktt'),
			'3'=>  __( 'AS', 'ktt')
	);	


		if($shippingModel->id == "eabi_omniva_courier"){
			$dokan_store_id = dokan_get_seller_id_by_order($order->id);
			$store_user = get_user_by('id', $dokan_store_id );
			$extended_settings =  get_user_meta( $dokan_store_id , 'ktt_extended_settings', true );
			$sender_name = $extended_settings['company_name'].' '.$company_types[$extended_settings['company_type']];
			echo var_dump($extended_settings);
			$postcode = '';
			$country = '';
			$street = '';
			$deliverypoint = '';
			$original_address = isset($extended_settings['address'][0]) ? $extended_settings['address'][0] : null;

			if(is_array($original_address))
			{
				$sender_phone = isset($original_address['phone']) ? $original_address['phone'] : null;
				$sender_email = isset($original_address['email']) ? $original_address['email'] : null;
				$street = isset($original_address['address']) ? $original_address['address'] : null;
				$city = isset($original_address['city']) ? $original_address['city'] : null;
				$country = isset($original_address['country']) ? $original_address['country'] : null;
				$postcode = isset($original_address['postcode']) ? $original_address['postcode'] : null;
			}else{
				throw new Eabi_Woocommerce_Postoffice_Exception("Missing address details!");
			}
			throw new Eabi_Woocommerce_Postoffice_Exception("Missing address details!");
			wp_die("Missing address details!");
			$start = date("Y-m-d",strtotime("tomorrow"))."T12:00:00";
			$finish = date("Y-m-d",strtotime("tomorrow"))."T15:00:00";
			$fragile = 0;	
	/*	$start = $order->get_meta('shippingPickup_start');
		if($start == ''){
			$start = date("Y-m-d",strtotime("tomorrow"))."T12:00:00";
		}
	
		$finish = $order->get_meta('shippingPickup_finish');
		if($finish == ''){
			$finish = date("Y-m-d",strtotime("tomorrow"))."T15:00:00";
		}
		$fragile = (int)$order->get_meta('fragile');
		*/	
			
		
		foreach($requestData['interchange']['item_list'] as &$item){
			if($fragile == 1){
				if(isset($item['add_service'])){
						$item['add_service']['option'][] = array(
                               '@attributes' => array(
                                   'code' => 'BC'
                               ),
                           );
				}else{
					$item['add_service'] = array(
						'option' => array()
					);
					$item['add_service']['option'][] = array(
                        '@attributes' => array(
                        'code' => 'BC'
                    	),
                 	);
				}
			}
			
			$item['returnAddressee'] = array(
                            'person_name' => $sender_name,
                            'phone' => $sender_phone,
                            'mobile' => $sender_phone,
                            'email' => $sender_email,
                            'address' => array(
                                '@attributes' => array(
                                    'postcode' => $postcode,
                                    'deliverypoint' => $city,
                                    'country' => $country,
                                    'street' => $street,
                                ),
                            ),
                    	);
        	$item['onloadAddressee'] =  array(
                            'person_name' => $sender_name,
                            'phone' => $sender_phone,
                            'mobile' => $sender_phone,
                            'email' => $sender_email,
                            'address' => array(
                                '@attributes' => array(
                                    'postcode' => $postcode,
                                    'deliverypoint' => $deliverypoint,
                                    'country' => $country,
                                    'street' => $street,
                                ),
                            ),
                            'pick_up_time' =>array(
                            	'@attributes' => array(
                                    'start' => $start,
                                    'finish' => $finish,
                                ),
                            )
                        );               
                        
    	/*   $xml = '<onloadAddressee>
                     <person_name>Sender Name</person_name>
                     <phone>6347384</phone>
                     <mobile>55665566</mobile>
                     <email>test@test.ee</email>
                     <address postcode="10101" deliverypoint="Tallinn" country="EE" street="Pallasti 27"/>
                     <pick_up_time start="2018-12-31T00:00:00" finish="2018-12-31T00:00:00"/>
				</onloadAddressee>';*/
                              
		
		}
		}
										echo '<pre>';
			var_dump($requestData);
						echo '</pre>';
		
		return $requestData;
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
				'url' => get_site_url(null, 'create-shop'),
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
    $class = "tribe-image-c";
   global $wp;

    if( strpos(home_url( $wp->request ) , "event") === false){
    	$featured_image =  wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'event-calendar-image');
    	
    }
    else{ 
    	$featured_image =  wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full');
    	$class .= " tribe-image-c-event";
    }
	$featured_image = '<img class="'.$class. '" src="'. $featured_image[0].'"  />';
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
    /*        $wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->messages->table_name_recipients} ( user_id, thread_id, unread_count ) VALUES ( %d, %d, 1 )", $recipient_id, $thread_id ) );*/
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
                    //wp_mail($user->data->user_email,$message->subject, $message->message,['ignore_bb' => true]);
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
function get_order_number_link($message, $order_number){
	global $wpdb;
	$user = wp_get_current_user();
	preg_match('/>Order #(.*?)<\/h2>/', $message, $match);

	if( !empty($match) ){
			$q = "SELECT  COUNT(*) as count FROM  ktt_posts WHERE ID = %d and post_author = %d";
			$pa = $wpdb->get_results($wpdb->prepare($q , $order_number , get_current_user_id() ) ) ;
		    $role = (int)$pa[0]->count ===  0  ? "customer" : "seller";
		    if(!empty($pa)) {
			    if($role === "customer"){
			        // show purchases link
			       
			       $link  = home_url()."/my-account/view-order/". $order_number ;
			      }
			    elseif( $role === "seller" ){
			        //show orders link
			       
			        $link  = wp_nonce_url( add_query_arg( array( 'order_id' => $order_number  ), dokan_get_navigation_url( 'orders' ) ) );
			         
			      }
			      return array("order_number"=>$order_number , "order_link"=> $link );
		      }
		      else
		      	return false;
	 }
	 else
	 	return false;	 
}

/** checks the message type ***/
function check_message_type($order_number){
	$p= null;
	$od = $order_number;
	global $wpdb;

	$query = "SELECT  post_type FROM ktt_posts WHERE id = %d ";
	$res = $wpdb->get_results($wpdb->prepare( $query , array($od) )  );
	if(!empty($res)) {
		if( $res[0]->post_type  ===  "shop_order" )
			$p = $res[0]->post_type;
	}

	return $p;
}
function getContentBetween($content,$start,$end){
    $w = explode($start, $content);
    if (isset($w[1])){
        $w = explode($end, $w[1]);
        return $w[0];
    }
    return $content;
}
/**** gets the order number from buddypress messages meta ****/
function get_order_from_messages_data($message_id) {
	 $data = bp_messages_get_meta($message_id, "order_conversation");
	 $d = json_decode($data,true);
	 return $d["order_id"];
}


add_filter('bp_get_the_thread_message_content', 'custom_bp_get_the_thread_message_content' );
function custom_bp_get_the_thread_message_content(){
	global $thread_template;
	$thread_id = $thread_template->message->thread_id;
	$order_number = get_order_from_messages_data($thread_id);

	$message =$thread_template->message->message;
	if(check_message_type($order_number) === "shop_order"){
		$on_link = get_order_number_link($thread_template->message->message, $order_number );
		if($on_link !== false)
			$message = " <a href=\"".$on_link["order_link"]."\" > Click here to view the order #".$on_link["order_number"]."</a>";
	}
	else{ 
		$message = strip_tags(getContentBetween(trim($message), "<div id=\"body_content_inner\">", "</div>") );
	}
	return  $message;
}

//add_filter('woocommerce_payment_successful_result', 'bd_woocommerce_payment_successful_result');


/*function bd_woocommerce_payment_successful_result($r = array() , $order_id = null ){
	global $wpdb;
	
		if( !empty($r)){ 
			$oid = getContentBetween($r["redirect"],"order-received/","?key");
			$current_user_id = get_current_user_id();  // the buyer
			$query = "SELECT seller_id FROM ktt_dokan_orders where order_id = %d";
			$seller_id = $wpdb->get_results($wpdb->prepare($query, (int) $oid )); // the seller
			$seller_id = $seller_id[0]->seller_id;
			$query = "SELECT thread_id from ktt_bp_messages_messages where sender_id = %d order by thread_id desc limit 1";
			$thread_id = $wpdb->get_results($wpdb->prepare($query, (int) $current_user_id )); 
			$thread_id = $thread_id[0]->thread_id;
			// we need to add the seller to the conversation
			$wpdb->query( $wpdb->prepare ( "INSERT INTO ktt_bp_messages_recipients (user_id, thread_id) VALUES (%d, %d) " , array( $seller_id,  $thread_id ) ) );
			//$wpdb->query( $wpdb->prepare ( "UPDATE ktt_bp_messages_messages set  sender_id = %d WHERE thread_id = %d " , array( $seller_id , $thread_id) ) );
			// we add metadata
	        $meta_value = json_encode( array("order_id"=>$oid,  "seller_id" => $seller_id , "buyer_id" => $current_user_id, "email_completed"=> 0 , "email_on_hold" =>1 ) );
	        //$meta_value = json_encode( array("order_id"=>$oid, "thread_id"=>$thread_id ,  "seller_id" => $seller_id , "buyer_id" => $current_user_id ) );
	       
			$wpdb->insert("ktt_bp_messages_meta",array("message_id"=> $thread_id , "meta_key"=> "order_conversation", "meta_value" => $meta_value ) , array( "%s", "%s", "%s")  );
			$wpdb->insert("ktt_bp_messages_meta",array("message_id"=> $thread_id , "meta_key"=> "order_conversation_post_order_id", "meta_value" => $oid ) , array( "%s")  );
		}

	return $r;
	
}*/

remove_filter( 'wp_mail', 'my_mail');
add_filter('wp_mail', 'my_custom_mail');
function my_custom_mail($data = array() ){
	//preg_match('/>Order #(.*?)<\/h2>/', $data['message'], $match);
    // Lets not get into loop
/*	var_dump( $data['headers']);
	die();*/
   // if(empty($match))   {
   		// if the header is a string, then it is an order email
	    if (isset($data['headers']['ignore_bb']) ) {
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
	            compare_recipients($thread_id);
	        }
	    }
	  //}
    return $data;
}

function new_wc_headers(  $headers, $object   ){
	$new_header = array("header"=>$headers , "ignore_bb"  => "true");
	return $new_header ;
}

add_filter('woocommerce_email_headers', 'new_wc_headers', 10, 2);

add_filter( 'pre_insert_term', 'prevent_terms', 1, 2 );
function prevent_terms ( $term, $taxonomy ) {
    $user = wp_get_current_user();
    if(in_array("administrator",$user->roles) == false)	
      return new WP_Error( 'term_addition_blocked', __( 'You cannot add terms to this taxonomy' ) );
  	else
  		return $term;
    
}


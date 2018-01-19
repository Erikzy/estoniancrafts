<?php
include_once(get_stylesheet_directory().'/Blocks/EC_Block.php');

class EC_Store_Page extends EC_Block
{
	public $user;						// WP_User
	public $store_info;					// Array
	public $dokan_profile;
	public $ktt_extended_settings;

	public $id;							// Integer
	public $title;						// String
	public $phone;						// String
	public $email;						// String
	public $address;					// EC_Address
	public $logo_url;					// String
	public $sm_links = array();			// Array of EC_Link
	public $contact_us_url;				// String
	public $related_people = array();	// Array of EC_Person
	public $banner;						// EC_Block
	public $custom_content = null;		// Custom content
    public $products;  //all products
    
    
	private static $_instance = null;

	public static function getInstance()
	{
		if (self::$_instance === null) {
			$storePage = new EC_Store_Page();
			$storePage->load();

			self::$_instance = $storePage;
		}
		return self::$_instance;
	}

	/**
	 * @return boolean
	 */
	public function load()
	{
		$this->user = get_userdata( get_query_var( 'author' ) );
		if(!$this->user) {
			return false;
		}

		$this->store_info = dokan_get_store_info( $this->user->ID );
		$this->dokan_profile = get_user_meta( $this->user->ID, 'dokan_profile_settings', true );
		$this->ktt_extended_settings = get_user_meta( $this->user->ID, 'ktt_extended_settings', true );
        
		include_once(get_stylesheet_directory().'/Blocks/Objects/EC_Link.php');
		include_once(get_stylesheet_directory().'/Blocks/Objects/EC_Address.php');
		include_once(get_stylesheet_directory().'/Blocks/Objects/EC_Person.php');

		// Logo
		$this->logo_url = null;
		if(!empty($this->ktt_extended_settings['ec_store_logo'])) {
			$this->logo_url = wp_get_attachment_thumb_url( $this->ktt_extended_settings['ec_store_logo'] );
		}

		$this->id = $this->user->ID;
		$this->title = $this->store_info['store_name'];
		$this->phone = isset($this->ktt_extended_settings['ec_store_phone']) ? $this->ktt_extended_settings['ec_store_phone'] : '';
		$this->email = isset($this->ktt_extended_settings['ec_store_email']) ? $this->ktt_extended_settings['ec_store_email'] : '';
		$this->website = isset($this->ktt_extended_settings['ec_store_website']) ? $this->ktt_extended_settings['ec_store_website'] : '';

		// Address
		$original_address = isset($this->ktt_extended_settings['address'][0]) ? $this->ktt_extended_settings['address'][0] : null;
		if(is_array($original_address))
		{
			$address = new EC_Address();
			$address->street = isset($original_address['address']) ? $original_address['address'] : null;
			$address->city = isset($original_address['city']) ? $original_address['city'] : null;
			$address->state = isset($original_address['state']) ? $original_address['state'] : null;
			$address->country_code = isset($original_address['country']) ? $original_address['country'] : null;
			$address->postcode = isset($original_address['postcode']) ? $original_address['postcode'] : null;

			$this->address = $address;
		}

		// Social media
		$this->sm_links = array();
		if(!empty($this->ktt_extended_settings['ec_store_sm_fb']))
		{
			$link = new EC_Link();
			$link->title = translate('Facebook', 'ktt');
			$link->url = $this->ktt_extended_settings['ec_store_sm_fb'];
			$this->sm_links['facebook'] = $link;
		}
		if(!empty($this->ktt_extended_settings['ec_store_sm_gplus']))
		{
			$link = new EC_Link();
			$link->title = translate('Google+', 'ktt');
			$link->url = $this->ktt_extended_settings['ec_store_sm_gplus'];
			$this->sm_links['googleplus'] = $link;
		}
		if(!empty($this->ktt_extended_settings['ec_store_sm_twitter']))
		{
			$link = new EC_Link();
			$link->title = translate('Twitter', 'ktt');
			$link->url = $this->ktt_extended_settings['ec_store_sm_twitter'];
			$this->sm_links['twitter'] = $link;
		}
		if(!empty($this->ktt_extended_settings['ec_store_sm_linkedin']))
		{
			$link = new EC_Link();
			$link->title = translate('LinkedIn', 'ktt');
			$link->url = $this->ktt_extended_settings['ec_store_sm_linkedin'];
			$this->sm_links['linkedin'] = $link;
		}
		if(!empty($this->ktt_extended_settings['ec_store_sm_youtube']))
		{
			$link = new EC_Link();
			$link->title = translate('YouTube', 'ktt');
			$link->url = $this->ktt_extended_settings['ec_store_sm_youtube'];
			$this->sm_links['youtube'] = $link;
		}
		if(!empty($this->ktt_extended_settings['ec_store_sm_instagram']))
		{
			$link = new EC_Link();
			$link->title = translate('Instagram', 'ktt');
			$link->url = $this->ktt_extended_settings['ec_store_sm_instagram'];
			$this->sm_links['instagram'] = $link;
		}
		if(!empty($this->ktt_extended_settings['ec_store_sm_flickr']))
		{
			$link = new EC_Link();
			$link->title = translate('Flickr', 'ktt');
			$link->url = $this->ktt_extended_settings['ec_store_sm_flickr'];
			$this->sm_links['flickr'] = $link;
		}

		// Contact us url
		// @todo real contact us url
		$this->contact_us_url = 'members/admin/messages/compose/?r='.antispambot( $this->email );

		// Related people
		$this->related_people = array();
		$owner = new EC_Person();
		$owner->load( $this->user->ID );
		$this->related_people[] = $owner;

		// Banner
		$banner = new EC_Block();
		$banner->title = $this->title;
		$banner->link_url = '#';
		if(isset($this->dokan_profile['banner']))
		{
			$banner->image_id = $this->dokan_profile['banner'];
			$banner->image_url = wp_get_attachment_url( $banner->image_id );
		}
		$this->banner = $banner;
        $this->products = $this->getProducts();
		return true;
	}
    
    
    public function getProductCount(){
      if(sizeof($this->products) == 0){
          $this->products = $this->getProducts();
      }
      return sizeof($this->products);  
        
    }
    
    public function getProductFilters(){
        
        
    }
    
    public function getProducts(){
    
        $post_statuses = array('publish');
        $args = array(
			'post_type'      => 'product',
			'post_status'    => $post_statuses,
			'posts_per_page' => 10,
			'author'         => $this->user->ID,
			'tax_query'      => array(
									array(
										'taxonomy' => 'product_type',
										'field'    => 'slug',
										'terms'    => apply_filters( 'dokan_product_listing_exclude_type', array() ),
										'operator' => 'NOT IN',
									),
								),
		);
        
        $product_query = new WP_Query( apply_filters( 'dokan_product_listing_query', $args ) );
        $this->products =  $product_query->get_posts();
        return $this->products;
    }
  
    
    
}
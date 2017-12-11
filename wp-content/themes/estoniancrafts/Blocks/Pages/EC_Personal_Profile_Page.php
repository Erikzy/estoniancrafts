<?php
include_once(get_stylesheet_directory().'/Blocks/EC_Block.php');

class EC_Personal_Profile_Page extends EC_Block
{
	/* @var WP_User */
	public $user;

	public $id;
	public $firstname;
	public $lastname;
	public $name;
	public $nickname;
	public $display_name;
	public $username;
	public $description;
	public $user_description;
	public $email;
	public $profile_url;
	public $contact_us_url;
	public $sm_links = array();				// array of EC_Link
	public $address;						// EC_Address
	public $banner;							// EC_Block
	public $stores = array();				// Array of EC_Block
	public $education_history = array();	// Array of EC_Block
	public $work_history = array();			// Array of EC_Block
	public $certificates = array();			// Array of EC_Block
	public $portfolios = array();


	public $custom_content = null;		// Custom content

	private static $_instance = null;

	public static function getInstance()
	{
		if (self::$_instance === null) {
			$page = new EC_Personal_Profile_Page();
			$page->load();

			self::$_instance = $page;
		}
		return self::$_instance;
	}

	/**
	 * @return boolean
	 */
	public function load()
	{
$mtime1 = microtime(true);
		global $wp_query;

        $current_user = wp_get_current_user();

		$user = get_user_by('login', $wp_query->query_vars['user']);
		if(!$user) {
			return false;
		}

		include_once(get_stylesheet_directory().'/Blocks/Objects/EC_Link.php');
		include_once(get_stylesheet_directory().'/Blocks/Objects/EC_Address.php');

		$this->user = $user;
		$this->store_info = dokan_get_store_info( $user->data->ID );
		$this->map_location = isset( $this->store_info['location'] ) ? esc_attr( $this->store_info['location'] ) : '';
		$this->dokan_profile = get_user_meta( $user->data->ID, 'dokan_profile_settings', true );
		$this->ext_profile = get_user_meta( $user->data->ID, 'ktt_extended_profile', true );
		$this->ext_shop = get_user_meta( $user->data->ID, 'ktt_extended_settings', true );

		$this->avatar_url = null;
		if(!empty($this->dokan_profile['gravatar'])) {
			$this->avatar_url = wp_get_attachment_thumb_url( $this->dokan_profile['gravatar'] );
//			$this->avatar_html = dokan_get_avatar(null, $user->data->ID, 200);
		}

		$this->id = $user->data->ID;
		$this->firstname = isset($user->first_name) ? $user->first_name : null;
		$this->lastname = isset($user->last_name) ? $user->last_name : null;
		$this->name = $this->firstname.' '.$this->lastname;
		$this->nickname = isset($user->data->nickname) ? $user->data->nickname : null;
		$this->display_name = isset($user->data->display_name) ? $user->data->display_name : null;
		$this->username = isset($user->data->user_login) ? $user->data->user_login : null;
		$this->description = isset($this->ext_profile['description']) ? $this->ext_profile['description'] : null;
		$this->email = isset($user->data->user_email) ? $user->data->user_email : null;
		$this->phone = isset($this->ext_profile['mobile']) ? $this->ext_profile['mobile'] : null;
		$this->skype = isset($this->ext_profile['skype']) ? $this->ext_profile['skype'] : null;
		$this->profile_url = isset($user->data->user_url) ? $user->data->user_url : null;
		$this->gender = isset($this->ext_profile['gender']) ? $this->ext_profile['gender'] : null;
		$this->date_of_birth = isset($this->ext_profile['dob']) ? $this->ext_profile['dob'] : null;

		// @todo what should we do with the video?
		// $this->ext_profile['video']

		// Social media
		$this->sm_links = array();
		if(!empty($this->dokan_profile['social']['fb']))
		{
			$link = new EC_Link();
			$link->title = translate('Facebook', 'ktt');
			$link->url = $this->dokan_profile['social']['fb'];
			$this->sm_links['facebook'] = $link;
		}
		if(!empty($this->dokan_profile['social']['gplus']))
		{
			$link = new EC_Link();
			$link->title = translate('Google+', 'ktt');
			$link->url = $this->dokan_profile['social']['gplus'];
			$this->sm_links['googleplus'] = $link;
		}
		if(!empty($this->dokan_profile['social']['twitter']))
		{
			$link = new EC_Link();
			$link->title = translate('Twitter', 'ktt');
			$link->url = $this->dokan_profile['social']['twitter'];
			$this->sm_links['twitter'] = $link;
		}
		if(!empty($this->dokan_profile['social']['linkedin']))
		{
			$link = new EC_Link();
			$link->title = translate('LinkedIn', 'ktt');
			$link->url = $this->dokan_profile['social']['linkedin'];
			$this->sm_links['linkedin'] = $link;
		}
		if(!empty($this->dokan_profile['social']['youtube']))
		{
			$link = new EC_Link();
			$link->title = translate('YouTube', 'ktt');
			$link->url = $this->dokan_profile['social']['youtube'];
			$this->sm_links['youtube'] = $link;
		}
		if(!empty($this->dokan_profile['social']['instagram']))
		{
			$link = new EC_Link();
			$link->title = translate('Instagram', 'ktt');
			$link->url = $this->dokan_profile['social']['instagram'];
			$this->sm_links['instagram'] = $link;
		}
		if(!empty($this->dokan_profile['social']['flickr']))
		{
			$link = new EC_Link();
			$link->title = translate('Flickr', 'ktt');
			$link->url = $this->dokan_profile['social']['flickr'];
			$this->sm_links['flickr'] = $link;
		}

		// Contact us url
		// @todo real contact us url
		$this->contact_us_url = '/members/'.$current_user->data->user_login.'/messages/compose/?r='.antispambot( $this->email );

		// Address
		$address = new EC_Address();
		$address->street = isset($this->ext_profile['address']) ? $this->ext_profile['address'] : null;
		$address->city = isset($ext_profile['city']) ? $ext_profile['city'] : null;
		$address->state = isset($ext_profile['state']) ? $ext_profile['state'] : null;
		$address->country_code = isset($ext_profile['country']) ? $ext_profile['country'] : null;

		// Banner
		$banner = new EC_Block();
		$banner->title = $this->name;
		$banner->description = isset($this->ext_profile['description']) ? $this->ext_profile['description'] : null;
		$banner->link_url = '#';
		if(isset($this->dokan_profile['banner']))
		{
			$banner->image_id = $this->dokan_profile['banner'];
			$banner->image_url = wp_get_attachment_url( $banner->image_id );
		}
		$this->banner = $banner;

		$companyTypeTranslationsAry = array(
			'1' => 'FIE',
			'2' => 'OÜ',
			'3' => 'AS',
			'4' => 'UÜ',
			'5'  => 'MTÜ'
		);

		// Stores
		$this->stores = array();

		// First store
		if(isset($this->dokan_profile['store_name']))
		{
			// Store info
			$store = new EC_Block();
			$store->title = $this->dokan_profile['store_name'];
			$store->description = isset($this->ext_shop['description']) ? $this->ext_shop['description'] : null;
			$store->company_name = isset($this->ext_shop['company_name']) ? $this->ext_shop['company_name'] : null;
			$store->company_reg_no = isset($this->ext_shop['company_nr']) ? $this->ext_shop['company_nr'] : null;
			$store->company_type = null;
			$store->company_type_id = isset($this->ext_shop['company_type']) ? $this->ext_shop['company_type'] : null;
			if(isset($store->company_type_id) && in_array($store->company_type_id, array_keys($companyTypeTranslationsAry))) {
				$this->company_type = translate( $companyTypeTranslationsAry[ $store->company_type_id ], 'ktt' );
			}
			$store->public_url = dokan_get_store_url( $user->data->ID );

			// Store addresses
			$store->addresses = array();
			$storeAddress = new EC_Address();
			$storeAddress->street = isset($this->ext_shop['address']) ? $this->ext_shop['address'] : null;
			$storeAddress->city = isset($this->ext_shop['city']) ? $this->ext_shop['city'] : null;
			$storeAddress->state = isset($this->ext_shop['state']) ? $this->ext_shop['state'] : null;
			$storeAddress->country_code = isset($this->ext_shop['country']) ? $this->ext_shop['country'] : null;
			$storeAddress->email = isset($this->ext_shop['email']) ? $this->ext_shop['email'] : null;
			$storeAddress->phone = isset($this->ext_shop['phone']) ? $this->ext_shop['phone'] : null;
			$store->addresses[] = $storeAddress;

			// Store media
			$store->media = array();
			if(is_array($this->ext_shop['media'])) {
				foreach($this->ext_shop['media'] as $mediaUrl) {
					$store->media[] = $mediaUrl;
				}
			}

			// Store featured products
			$store->featured_products = array();
			$dokan_store_products = dokan_get_latest_products( 6, $user->data->ID );
			if($dokan_store_products && $dokan_store_products->have_posts()) {
				foreach($dokan_store_products->get_posts() as $dokan_product)
				{
					$product = new EC_Block();
					$product->post = $dokan_product;
					$product->title = $dokan_product->post_title;
					$product->public_url = '#';

					// Main image
					$product->image = new EC_Block();
					$product->image->id = 'XXX';
					$product->image->title = $product->title;
					$product->image->url = get_the_post_thumbnail_url( $dokan_product->ID, array(107, 107) );

					$store->featured_products[] = $product;
				}
			}

			$this->stores[] = $store;
		}

		// Education hitory
		if(!empty($this->ext_profile['education']))
		{
			$this->education_history = array();
			$translationsAry = array(
				'1' => 'Basic education',
				'2' => 'Secondary education',
				'3' => 'Vocational education',
				'4' => 'Higher education'
			);

			// First edication
			$education = new EC_Block();
			$education->id = $this->ext_profile['education'];
			$education->title = in_array($education->id, array_keys($translationsAry)) ? translate( $translationsAry[ $education->id ], 'ktt' ) : '';
			$education->school_name = isset($this->ext_profile['education_school']) ? $this->ext_profile['education_school'] : null;
			$education->start_date = isset($this->ext_profile['education_start']) ? $this->ext_profile['education_start'] : null;
			$education->end_date = isset($this->ext_profile['education_end']) ? $this->ext_profile['education_end'] : null;
			$this->education_history[] = $education;
		}

		// Word history
		$this->work_history = array();
		if(isset($this->ext_profile['work_exp']) && !empty($this->ext_profile['work_exp'])) {
			foreach($this->ext_profile['work_exp'] as $work_exp)
			{
				$job = new EC_Block();
				$job->company_name = isset($work_exp['name']) ? $work_exp['name'] : null;
				$job->job_title = isset($work_exp['field']) ? $work_exp['field'] : null;
				$job->start_date = isset($work_exp['start']) ? $work_exp['start'] : null;
				$job->end_date = isset($work_exp['end']) ? $work_exp['end'] : null;
				$this->work_history[] = $job;
			}
		}

		// Certificates
		$this->certificates = array();
		if(isset($this->ext_profile['certificates'])) {
			foreach($this->ext_profile['certificates'] as $crt)
			{
				$certificate = new EC_Block();
				$certificate->title = $crt['name'];
				$certificate->authority_title = $crt['auth'];
				$certificate->start_date = $crt['start'];
				$certificate->end_date = $crt['end'];
				if($crt['file']) {
					$certificate->file_id = $crt['file'];
					$certificate->file_url = wp_get_attachment_url( $crt['file'] );
				}
				$this->certificates[] = $certificate;
			}
		}

		// Portfolios
		$this->portfolios = get_posts([
			'post_author' => $user->ID,
			'post_type' => 'portfolio'
		]);

$mtime2 = microtime(true);
//ec_debug_to_console('runtime', sprintf('%f.8', ($mtime2 - $mtime1)));

		return true;
	}

}
<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

class EC_UserRelation
{
	/*
	* Available user relation keys
	*/
	const SHOP_USER = 'shop_user';
	const SHOP_ADMIN = 'shop_admin';

	protected $id;
	protected $source_user_id;
	protected $target_user_id;
	protected $relation_key;
	protected $value;

	protected static $_membersCache = [];

	public function __construct()
	{
		$this->id = null;
		$this->source_user_id = 0;
		$this->target_user_id = 0;
		$this->relation_key = EC_UserRelation::SHOP_USER;
		$this->value = '';
	}

	/*
	* Populates relation with data
	*
	* @param $params = []
	*
	* @return EC_UserRelation
	*/
	public function populate($params = [])
	{
		if (array_key_exists('id', $params)) {
			$this->id = (int)$params['id'];
			unset($params['id']);
		}
		foreach ($params as $key => $value) {
			$this->$key = $value;
		}

		return $this;
	}

	public function setSource($userId)
	{
		$this->source_user_id = $userId;
		return $this;
	}

	public function setTarget($userId)
	{
		$this->target_user_id = $userId;
		return $this;
	}

	/*
	* Sets relation key and value pair
	*
	* @param $key
	* @param $value
	* 
	* @return EC_UserRelation
	*/
	public function setRelation($key, $value = '')
	{
		$this->setValue($value);
		$this->relation_key = $key;
		return $this;
	}

	/*
	* @return boolean
	*/
	public function isShopUser()
	{
		return $this->relation_key === EC_UserRelation::SHOP_USER;
	}

	/*
	* @return boolean
	*/
	public function isShopAdmin()
	{
		return $this->relation_key === EC_UserRelation::SHOP_ADMIN;
	}

	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}

	public function getValue()
	{
		// add your relation value decoders here
		return $this->value;
	}

	/*
 	* Saves new relation
 	*
 	* @return int|false - number of rows changed
	*/
	public function save()
	{
		$data = [
			'source_user_id' => $this->source_user_id,
			'target_user_id' => $this->target_user_id,
			'relation_key' => $this->relation_key,
			'value' => $this->value
		];

		global $wpdb;
		
		if ($this->id) {
			return $wpdb->update('ktt_ec_user_relations', $data, ['id' => $this->id]);
		} else { // new relation
			$res = $wpdb->insert('ktt_ec_user_relations', $data);
			if ($res) {
				$this->id = $wpdb->insert_id;
			}
			return $res;
		}
	}

	/*
	* @return int|false - number of rows removed
	*/
	public function remove()
	{
		if ($this->id) {
			global $wpdb;
			return $wpdb->delete('ktt_ec_user_relations', ['id' => $this->id]);
		}
	}

	public function __get($key)
	{
		return $this->$key;
	}

	/*
	* Loads relation by source-target-key
	*
	* @param $sourceId
	* @param $key
	* @param $targetId
	*
	* @return EC_UserRelation
	*/
	public static function loadByRelation($sourceId, $key, $targetId)
	{
		global $wpdb;

		$dbres = $wpdb->get_row($wpdb->prepare("SELECT * FROM `ktt_ec_user_relations` AS ur WHERE source_user_id = %d AND target_user_id = %d AND relation_key = %s;", $sourceId, $targetId, $key), ARRAY_A);

		$relation = new EC_UserRelation();
		$relation->populate(
			($dbres && is_array($dbres)) ? 
				$dbres : 
				[
					'source_user_id' => $sourceId,
					'target_user_id' => $targetId,
					'relation_key' => $key
				]
		);

		return $relation;
	}

	public static function loadById($id)
	{
		global $wpdb;

		$dbres = $wpdb->get_row($wpdb->prepare("SELECT * FROM `ktt_ec_user_relations` WHERE id = %d;", $id), ARRAY_A);

		$relation = new EC_UserRelation();
		if ($dbres && is_array($dbres)) {
			$relation->populate($dbres);
		}

		return $relation;
	}

	/*
	* AJAX binding and listen to role change
	*/
	public static function init()
	{
		add_action('wp_ajax_ec_add_shop_user', [__CLASS__, 'add_shop_user_ajax']);
		add_action('wp_ajax_ec_remove_shop_user', [__CLASS__, 'remove_shop_user_ajax']);
		add_action('wp_ajax_ec_add_shop_user_relation_value', [__CLASS__, 'add_shop_user_relation_value_ajax']);

		add_action('set_user_role', [__CLASS__, 'addUserShopRelation'], 1, 3);
	}

	/*
	* Ajax callable to add shop user by user email
	* echo's shop team list user table row (HTML)
	*/
	public static function add_shop_user_ajax()
	{
		// returns with proper json
		$return = function ($status = true, $value = '') {
			echo json_encode([
				'success' => $status,
				'value' => $value
			]);
			die();
		};

		// check access token
		if (!(isset($_POST['_wpnonce']) &&
			wp_verify_nonce($_POST['_wpnonce'], 'ec_add_shop_user'))
		) {
			$return(false);
		}
		unset($_POST['_wpnonce']);

		// check if email is set
		$email = isset($_POST['email']) && $_POST['email'] !== '' ? $_POST['email'] : null;
		if (!$email) {
			$return(false);
		}
		unset($_POST['email']);

		// check get current logged in user and check if seller
		$shopOwner = wp_get_current_user();
		if (!($shopOwner && 
			$shopOwner->ID &&
			dokan_is_seller_enabled($shopOwner->ID)
		)) {
			$return(false);
		}
		// check if the relation is not set for itself
		if ($shopOwner->email === $email) {
			$return(false); // can't set relation to itself
		}

		// check get user with email
		$user = get_user_by('email', $email);
		if (!($user && $user->ID)) {
			$return(false, sprintf(__('User with email "%s" was not found', 'ktt'), $email)
			);
		}
		unset($email);

		// get relation
		$relation = EC_UserRelation::loadByRelation($user->ID, EC_UserRelation::SHOP_USER, $shopOwner->ID);
		if ($relation->id) {
			$return(); // this relation already exists (do nothing)
		}

		// can't assign itself
		if ((int)$relation->source_user_id === (int)$relation->target_user_id) {
			$return(false, __('Can not add yourself as shop member', 'ktt'));
		}
		if ($relation->save()) {
			// render added user profile
			ob_start();
			include(locate_template('templates/myaccount/shop_team_list_user.php'));
			$html = ob_get_contents();
			ob_end_clean();
			$return(true, $html);
		}
		
		$return();
	}

	/*
	* Ajax callable to remove shop user relation
	* echo's removed relation id
	*/
	public static function remove_shop_user_ajax()
	{
		// check access token
		if (!(isset($_POST['_wpnonce']) &&
			wp_verify_nonce($_POST['_wpnonce'], 'ec_remove_shop_user')
		)) {
			die();
		}
		unset($_POST['_wpnonce']);
		// check if relation id is set
		$relationId = isset($_POST['relation_id']) && (int)$_POST['relation_id'] ? (int)$_POST['relation_id'] : null;
		unset($_POST['relation_id']);
		// check get current logged in user and check if seller
		$shopOwner = wp_get_current_user();
		if (!($shopOwner && 
			$shopOwner->ID &&
			dokan_is_seller_enabled($shopOwner->ID)
		)) {
			die();
		}

		// get relation (checks if user has right to even remove relation)
		$relation = EC_UserRelation::loadById($relationId);
		unset($relationId);
		if (!($relation->id && (int)$relation->target_user_id === (int)$shopOwner->ID && $relation->isShopUser())) {
			die();
		}

		// remove store
		if ($relation->remove()) {
			// returns removed relation id
			echo $relation->id;
			die();
		}
		die();
	}

	/*
	* Ajax callable to add user relation some value (only for shop users)
	* echo's relation id if success
	*/
	public static function add_shop_user_relation_value_ajax()
	{
		// check if relation id is set
		$relationId = isset($_POST['relation_id']) && (int)$_POST['relation_id'] ? (int)$_POST['relation_id'] : null;
		unset($_POST['relation_id']);
		if (!$relationId) {
			die();
		}
		// check value
		$value = isset($_POST['value']) ? $_POST['value'] : null;
		$value = trim($value);
		unset($_POST['value']);
		if ($value === null) {
			die();
		}

		// get relation (checks if user has right to even remove relation)
		$relation = EC_UserRelation::loadById($relationId);
		unset($relationId);
		if (!$relation->id) {
			die();
		}

		// check access token for specific relation role
		if (!(isset($_POST['_wpnonce']) &&
			wp_verify_nonce($_POST['_wpnonce'], 'ec_add_user_relation_value_' . $relation->id)
		)) {
			die();
		}
		unset($_POST['_wpnonce']);

		$shopOwner = wp_get_current_user();
		if (!($shopOwner && 
			$shopOwner->ID &&
			dokan_is_seller_enabled($shopOwner->ID) &&
			$shopOwner->ID == (int)$relation->target_user_id
		)) {
			die();
		}

		$relation->setValue($value);
		$relation->save();
		echo $relation->id;
		die();
	}

	/*
	* Loads member list of all shop members (including owner as first member)
	* @param int $ownderId - show owner user id
	* @return array - in format [0 => ['user' => WP_User, 'relation' => EC_UserRelation], 1 => [...], ... ]
	*/
	public static function loadShopMembers($ownerId)
	{
		// caching
		$cacheKey = 'shop_team_' . $ownerId;
		if (!array_key_exists($cacheKey, self::$_membersCache)) {
			global $wpdb;

			// get relations
			$dbrelations = $wpdb->get_results($wpdb->prepare("SELECT * FROM `ktt_ec_user_relations` 
				WHERE target_user_id = %d AND
				(relation_key = %s OR relation_key = %s)
			;", $ownerId, EC_UserRelation::SHOP_USER, EC_UserRelation::SHOP_ADMIN), ARRAY_A);

			$admin = null;
			$members = [];
			// populate and load users
			foreach ($dbrelations as $relationArray) {
				$relation = new EC_UserRelation();
				$relation->populate($relationArray);
				$user = get_user_by('id', $relation->source_user_id);
				$member = ['user' => $user, 'relation' => $relation];
				if ($relation->isShopAdmin()) { // do not include as other members
					$admin = $member;
				} else {
					$members[] = $member;
				}
			}

			// every show has to have admin. Force adds one
			if (!$admin) {
				// add admin to shop members
				$relation = EC_UserRelation::loadByRelation($ownerId, EC_UserRelation::SHOP_ADMIN, $ownerId);
				$relation->save();
				$user = get_user_by('id', $relation->source_user_id);
				$admin = ['user' => $user, 'relation' => $relation];
			}
			// admin as first in the member list
			array_unshift($members, $admin);

			// store in cache
			self::$_membersCache[$cacheKey] = $members;
		}
		return self::$_membersCache[$cacheKey];
	}

	/*
	* If user gets role seller, adds user to his own shop as admin (needed to have 'Job title' enabled for changes
	*/
	public static function addUserShopRelation($id, $role, $old_roles)
	{
		if ($role === 'seller') {
			$relation = EC_UserRelation::loadByRelation($id, EC_UserRelation::SHOP_ADMIN, $id);
			if (!$relation->id) {
				$relation->save();
			}
		}
	}
    
    
   /**
 * return $images_id array()
 */
public static function get_gallery_attachments_to_id($users_id, $filed_name ){
	$ids[] ='';
	$gallery_content = get_post_meta($users_id, $filed_name, true);
	preg_match('/\[gallery.*ids=.(.*).\]/', $gallery_content, $ids);
	$images_id = explode(",", $ids[1]);
	
	return $images_id;
}

}

EC_UserRelation::init();

/*
* Team member list page shortcode
*/
function ec_shop_team($atts)
{	
	// is logged in as seller?
	$shopOwner = wp_get_current_user();
	if ($shopOwner && 
		$shopOwner->ID &&
		dokan_is_seller_enabled($shopOwner->ID)
	) {
		$members = EC_UserRelation::loadShopMembers($shopOwner->ID);

		ob_start();
		include(locate_template('templates/myaccount/shop_team.php'));
		return ob_get_clean();
	}

	return false;
}

add_shortcode('ec_shop_team', 'ec_shop_team');







function gallerys()
{
    global $current_user;
    wp_get_current_user();
    if(isset($_POST['gallery_submit'])  && isset( $_POST['post_nonce_field'] ) && wp_verify_nonce( $_POST['post_nonce_field'], 'post_nonce' ))
    {
        if ( ! add_post_meta( $current_user->ID, 'portfolio_gallery', esc_attr($_POST['portfolio_gallery']), true ) ) { 
            update_post_meta(  $current_user->ID, 'portfolio_gallery', esc_attr($_POST['portfolio_gallery']) );
        }
        else
        {
            add_post_meta(  $current_user->ID, 'portfolio_gallery', esc_attr($_POST['portfolio_gallery']), true );
        }
    }
    include(locate_template('templates/myaccount/gallery_form.php'));
}
add_shortcode('gallerys', 'gallerys');

/*page design*/
function my_enqueue_media_lib_uploader() {
        //Core media script
        wp_enqueue_media();
        // Your custom js file
        wp_register_script( 'media-lib-uploader-js', plugins_url( 'media-lib-uploader.js' , __FILE__ ), array('jquery') );
        wp_enqueue_script( 'media-lib-uploader-js' );
}
add_action('wp_enqueue_scripts',   'my_enqueue_media_lib_uploader');

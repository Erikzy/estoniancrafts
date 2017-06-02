<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

class EC_UserRelation
{
	/*
	* Available user relation keys
	*/
	const SHOP_USER = 'shop_user';

	protected $id;
	protected $source_user_id;
	protected $target_user_id;
	protected $relation_key;
	protected $value;

	public function __construct()
	{
		$this->id = null;
		$this->source_user_id = 0;
		$this->target_user_id = 0;
		$this->key = EC_UserRelation::SHOP_USER;
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

	public function setValue($value)
	{
		if ($this->isShopUser()) {
			// check get current logged in user and check if seller
			$shopOwner = wp_get_current_user();
			if (!($shopOwner && 
				$shopOwner->ID &&
				dokan_is_seller_enabled($shopOwner->ID)
			)) {
				return false;
			}

			if ((int)$this->target_user_id !== (int)$shopOwner->ID) {
				return false;
			}
		}
		
		$this->value = $value;
		return true;
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
			'relation_key' => $this->key,
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
	* AJAX binding
	*/
	public static function init()
	{
		add_action('wp_ajax_ec_add_shop_user', [__CLASS__, 'add_shop_user_ajax']);
		add_action('wp_ajax_ec_remove_shop_user', [__CLASS__, 'remove_shop_user_ajax']);
		add_action('wp_ajax_ec_add_user_relation_value', [__CLASS__, 'add_user_relation_value_ajax']);
	}

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
			$return(false, 'E-posti aadressiga ' . $email . ' kasutajat ei leitud' // @TODO translate, this will be forwarded to front end side (only viable error)
			);
		}
		unset($email);

		// get relation
		$relation = EC_UserRelation::loadByRelation($user->ID, EC_UserRelation::SHOP_USER, $shopOwner->ID);
		if ($relation->id) {
			$return(); // this relation already exists (do nothing)
		}

		if ($relation->save()) {
			// render added user profile
			ob_start();
			include(locate_template('templates/myaccount/shop_member_list_user.php'));
			$html = ob_get_contents();
			ob_end_clean();
			$return(true, $html);
		}
		
		$return();
	}

	public static function remove_shop_user_ajax()
	{
		// check access token
		if (!(isset($_POST['_wpnonce']) &&
			wp_verify_nonce($_POST['_wpnonce'], 'ec_remove_shop_user')
		)) {
			die('token mismatch');
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
			die('invalid access');
		}

		// get relation (checks if user has right to even remove relation)
		$relation = EC_UserRelation::loadById($relationId);
		unset($relationId);
		if (!($relation->id && (int)$relation->target_user_id === (int)$shopOwner->ID && $relation->isShopUser())) {
			die('not allowed to remove this relation');
		}

		// remove store
		if ($relation->remove()) {
			// returns removed relation id
			echo $relation->id;
			die();
		}
		die();
	}

	public static function add_user_relation_value_ajax()
	{
		// check if relation id is set
		$relationId = isset($_POST['relation_id']) && (int)$_POST['relation_id'] ? (int)$_POST['relation_id'] : null;
		unset($_POST['relation_id']);
		if (!$relationId) {

		}
		// check value
		$value = isset($_POST['value']) ? $_POST['value'] : null;
		unset($_POST['value']);
		if ($value === null) {
			die('Puudub väärtus');
		}

		// get relation (checks if user has right to even remove relation)
		$relation = EC_UserRelation::loadById($relationId);
		unset($relationId);
		if (!$relation->id) {
			die('relation with does not exist');
		}

		// check access token for specific relation role
		if (!(isset($_POST['_wpnonce']) &&
			wp_verify_nonce($_POST['_wpnonce'], 'ec_add_user_relation_value_' . $relation->id)
		)) {
			die('token mismatch');
		}
		unset($_POST['_wpnonce']);

		// set value
		if (!$relation->setValue($value)) {
			die('not allowed to modify this relation value');
		}

		if ($relation->save()) {
			// return changed id as token of success
			echo $relation->id;
			die();
		}
		die();
	}

}

EC_UserRelation::init();
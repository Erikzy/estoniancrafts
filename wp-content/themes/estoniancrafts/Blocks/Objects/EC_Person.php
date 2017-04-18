<?php
include_once(get_stylesheet_directory().'/Blocks/EC_Block.php');

class EC_Person extends EC_Base
{
	public $name;					// string
	public $firstname;				// string
	public $lastname;				// string
	public $job_title;				// string
	public $avatar_url;				// string
	public $profile_url;			// string


	/**
	 * @param integer $user_id
	 */
	public function load($user_id)
	{
		$user = get_user_by('id', $user_id);
		if(!$user) {
			return false;
		}

		$this->firstname = isset($user->first_name) ? $user->first_name : null;
		$this->lastname = isset($user->last_name) ? $user->last_name : null;
		$this->name = $this->firstname.' '.$this->lastname;
		$this->profile_url = isset($user->data->user_login) ? '/user/'.$user->data->user_login : null;

		$dokan_profile = get_user_meta( $user_id, 'dokan_profile_settings', true );
		$this->avatar_url = null;
		if(!empty($dokan_profile['gravatar'])) {
			$this->avatar_url = wp_get_attachment_thumb_url( $dokan_profile['gravatar'] );
		}

		return true;
	}
}
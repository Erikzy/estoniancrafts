<?php
include_once(get_stylesheet_directory().'/Blocks/EC_Block.php');

class EC_Address extends EC_Base
{
	public $street;			// string
	public $city;			// string
	public $state;			// string
	public $country_code;	// string
	public $country;		// string
	public $email;			// string
	public $phone;			// string

	/**
	 * @param string $glue
	 * @return string
	 */
	public function get_address_as_string($glue=', ')
	{
		$ary = array();
		if(!empty($this->street)) $ary[] = $this->street;
		if(!empty($this->city)) $ary[] = $this->city;
		if(!empty($this->state)) $ary[] = $this->state;
		if(!empty($this->country)) $ary[] = $this->country;

		return implode($glue, $ary);
	}
}
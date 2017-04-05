<?php

class EC_Base implements ArrayAccess
{
	/**
	 * @param array $data
	 * @return \EC_Base
	 */
	public function __construct($data=array())
	{
		if(is_array($data) && !empty($data))
		{
			$this->set($data);
		}

		return $this;
	}

	/**
	 * Get data.
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key=null, $default=null)
	{
		if(is_null($key))
		{
			return cm_get_object_vars( $this );
		}
		else
		{
			return isset($this->$key)
				? $this->$key
				: $default;
		}
	}

	/**
	 * Set data.
	 * @param string|array $key
	 * @param mixed $val
	 * @return \EC_Base
	 */
	public function set($key, $val=null)
	{
		if(is_array($key))
		{
			foreach($key as $k=>$v)
			{
				$this->$k = $v;
			}
		}
		else
		{
			$this->$key = $val;
		}

		return $this;
	}

	/**
	 * Is attribute set
	 * @param string $key
	 * @return boolean
	 */
	public function ist($key)
	{
		return isset($this->key);
	}

	/**
	 * Unset data
	 * @param string $key
	 * @return \EC_Base
	 */
	public function uns($key)
	{
		if(isset($this->$key))
		{
			unset($this->$key);
		}

		return $this;
	}

	/**
	 * Has data
	 * @param type $key
	 * @return type
	 */
	public function has($key)
	{
		$value = $this->get($key);
		if(is_string($value))
		{
			return strlen($value) > 0 ? true : false;
		}
		else
		{
			return isset($value) && !empty($value) 
				? true 
				: false;
		}
	}


	/**
	 * Magick methods
	 */

	public function __get($key)
	{
		return $this->get($key);
	}


	/**
	 * ArrayAccess
	 */

	public function offsetSet($key, $value)
	{
		if(!is_null($key))
		{
			$this->$key = $value;
		}
	}

	public function offsetExists($key)
	{
		$val = $this->offsetGet($key);
		return !is_null($val);
	}

	public function offsetUnset($key)
	{
		$this->uns($key);
	}

	public function offsetGet($key)
	{
		if(method_exists($this, $key))
		{
			return $this->$key();
		}
		return isset($this->$key) 
			? $this->$key 
			: null;
	}
}

/**
 * Return object public variables only
 * @return array
 */
function cm_get_object_vars($obj)
{
	return get_object_vars($obj);
}

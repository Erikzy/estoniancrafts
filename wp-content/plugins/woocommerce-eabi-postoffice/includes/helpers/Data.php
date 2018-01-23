<?php

/*
   *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */

/**
 * Description of Data
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Helper_Data {
    const CLASS_PREFIX = 'Eabi_Woocommerce_Postoffice_';
    
    protected $_classData = array(
    );
            
    
    private static $_singletons = array();
    
    protected $_cachedServices = array();
    
    
    /**
     *
     * @var Eabi_Woocommerce_Postoffice_Block_Licence
     */
    private static $_licence;
    
    public function __construct() {
        $this->_classData[Eabi_Woocommerce_Postoffice_Helper_Data::CLASS_PREFIX] = array(
            'class_prefix' => Eabi_Woocommerce_Postoffice_Helper_Data::CLASS_PREFIX,
            'include_path' => untrailingslashit(plugin_dir_path(dirname(dirname(__FILE__)))) . '/includes',
        );
    }

    public function addSearchPath($classPrefix, $includePath) {
        if (!isset($this->_classData[$classPrefix])) {
            $this->_classData[$classPrefix] = array(
                'class_prefix' => $classPrefix,
                'include_path' => untrailingslashit($includePath),
            );
        }
        return $this;
    }

    private function __reverse($data) {
        return array_reverse($data, true);
    }

    /**
     * <p>Returns true if WooCommerce is saving any of its setting and <code>$current_section</code> is set and request method is post</p>
     * @global string $current_section
     * @return boolean
     */
    public function isSavingCurrentSection() {
        global $current_section;
        
        if (is_admin() && isset($current_section) && $current_section && isset($_POST) && count($_POST)) {
            return true;
        }
        return false;
    }
    
    
    public function getModel($name) {
        foreach ($this->__reverse($this->_classData) as $classData) {
            $className = $classData['class_prefix'] . 'Model_' . ucfirst($name);
            if (!class_exists($className, false)) {
                $fileName = $classData['include_path'] . '/models/' . ucfirst($name) . '.php';
                if (file_exists($fileName)) {
                    @include_once $fileName;
                }
            }
            if (class_exists($className, false)) {
                //if class is found, return it
                return new $className;
            }
            //else continue looping until something is found
        }
        //finally return false;
        return false;
    }

    /**
     * 
     * @return Eabi_Woocommerce_Postoffice_Block_Licence
     */
    public function licence() {
        if (is_null(self::$_licence)) {
            self::$_licence = $this->getBlock('licence');
        }
        return self::$_licence;
    }
    
    

    public function getServiceCountries() {
        $res = $this->getWooCommerce()->countries->countries;
        $res['*'] = __('All remaining countries', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
        return $res;
    }
    
    
    /**
     * <p>Takes in array of parcel weights and returns number of packages calculated by maximum allowed weight per package</p>
     * <p>Uses better methology to find number of packages than regular cart weight divided by maximum package weight</p>
     * <p>For example, if maximum package weight is 31kg, ang we have 3x 20kg packages, then number of packages would be 3 (not 2)</p>
     * <p>If maximum package weight is not defined, then it returns 1</p>
     * <p>If single item in <code>$itemWeights</code> exceeds <code>$maximumWeight</code> then this function returns false</p>
     * @param array $itemWeights array of item weights
     * @param int $maximumWeight maximum allowed weight of one package
     * @return int
     */
    public function getNumberOfPackagesFromItemWeights(array $itemWeights, $maximumWeight) {
        $numPackages = 1;
        $weight = 0;
        if ($maximumWeight > 0) {
            
            foreach ($itemWeights as $itemWeight) {
                if ($itemWeight > $maximumWeight) {
                    return false;
                }

		if($itemWeight != ""){
              	     $weight += (int)$itemWeight;
               	     if ($weight > $maximumWeight) {
                	    $numPackages++;
                	    $weight = $itemWeight;
                	}
		}
            }
            
        }
        return $numPackages;
    }
    
    
    public function getBlock($name) {
        //load base class first
        $this->getSingleton('template');

        foreach ($this->__reverse($this->_classData) as $classData) {
            $className = $classData['class_prefix'] . 'Block_' . ucfirst($name);
            if (!class_exists($className, false)) {
                $fileName = $classData['include_path'] . '/blocks/' . ucfirst($name) . '.php';
                
                
                if (file_exists($fileName)) {
                    @include_once $fileName;
                }
            }
            if (class_exists($className, false)) {
                //if class is found, return it
                $block = new $className;
                $block->setTemplatePath(untrailingslashit(dirname($classData['include_path'])) . '/templates');
                return $block;
            }
            //else continue looping until something is found
        }
        //finally return false;
        return false;

/*
        $className = self::CLASS_PREFIX . 'Block_' . ucfirst($name);
        if (!class_exists($className)) {
            $fileName = untrailingslashit(plugin_dir_path(dirname(dirname(__FILE__)))) . '/includes/blocks/' . ucfirst($name) . '.php';
            require_once $fileName;
        }
        $block = new $className;
        $block->setTemplatePath(untrailingslashit(plugin_dir_path(dirname(dirname(__FILE__)))) . '/templates/');
        
        return $block;
 * 
 */
    }

    /**
     * 
     * @param WC_Eabi_Postoffice $method
     * @param string $field
     * @param mixed $value
     * @return \Eabi_Woocommerce_Postoffice_Helper_Data
     */
    public function updateSingleField($method, $field, $value) {
        $path = $method->plugin_id . $method->id . '_settings';
        $conf = get_option($path, null);
        if (!$conf || !is_array($conf)) {
            $conf = array();
        }
        $conf[$field] = $value;
        if ($value === null) {
            unset($conf[$field]);
        }
        update_option($path, $conf);
        return $this;
    }

    public function getSingleton($name) {
        if (!isset(self::$_singletons[$name])) {
            $model = $this->getModel($name);
            if (!$model) {
                return false;
            }
            self::$_singletons[$name] = $model;
        }
        return self::$_singletons[$name];
    }
    
    public function toLabelValues($array) {
        $result = array();
        foreach ($array as $k => $v) {
            $result[] = array(
                'label' => $v,
                'value' => $k,
            );
        }
        return $result;
    }
    
    public function getStatus($method, $property) {
        return $this->licence()->getStatus($method, $property);
    }
    
    /**
     * TODO: Implement currency rate conversion
     * @param float $amount
     * @param string $sourceCurrencyCode
     * @param string $targetCurrencyCode
     * @return float
     */
    public function toTargetCurrency($amount, $sourceCurrencyCode, $targetCurrencyCode) {
        return $amount;
    }

    /**
     * 
     * @return Eabi_Woocommerce_Postoffice_Model_Xmlparser
     */
    public function getXmlParser() {
        return $this->getSingleton('xmlparser');
    }
    
    /**
     * 
     * @return Eabi_Woocommerce_Postoffice_Model_Csvparser
     */
    public function getCsvParser() {
        return $this->getSingleton('csvparser');
    }
    
    /**
     * 
     * @return Eabi_Woocommerce_Postoffice_Model_Dialcodehelper
     */
    public function getDialCodeHelper() {
        return $this->getSingleton('dialcodehelper');
    }
    
    /**
     * 
     * @return Eabi_Woocommerce_Postoffice_Model_Addresshelper
     */
    public function getAddressHelper() {
        return $this->getSingleton('addresshelper');
    }

    /**
     * 
     * @global wpdb $wpdb
     * @return wpdb
     */
    public function getWpdbModel() {
        global $wpdb;
        return $wpdb;
    }

    public function getWooCommerce() {
        if (function_exists('WC')) {
            return WC();
        }
        global $woocommerce;
        return $woocommerce;
    }

    /**
     * 
     * @param type $code
     * @return WC_Eabi_Postoffice
     * @throws Exception
     */
    public function getShippingMethodByCode($code, $disableAction = false) {
        
        $woo = $this->getWooCommerce()->shipping();
        
        $db = $this->getWpdbModel();
        $code = esc_sql($code);
        
        $carrierModel = $db->get_row("select * from {$db->prefix}eabi_carriermodule where carrier_code = '{$code}'", ARRAY_A);
        
        if (!$carrierModel) {
            throw new Exception('Carrier could not be detected');
        }
        
        $className = $carrierModel['class_name'];
        $object = new $className((bool)$disableAction);
        if (!($object instanceof WC_Eabi_Postoffice)) {
            throw new Exception('Carrier not instance of WC_Eabi_Postoffice');
        }
        
        return $object;
    }
    
    
    public function getAllSupportedShippingMethodCodes() {
        $db = $this->getWpdbModel();
        $allCarriers = $db->get_results("select * from {$db->prefix}eabi_carriermodule", ARRAY_A);
        $result = array();
        
        foreach ($allCarriers as $carrier) {
            if (!in_array($carrier['carrier_code'], $result)) {
                $result[] = $carrier['carrier_code'];
            }
        }
        return $result;
    }
    
    //woo compatiblity replacement functions start here
    
    public function orderHasShippingMethod($order, $method) {
        if (version_compare(WOOCOMMERCE_VERSION, '2.6.5', '>=')) {
            //bugfix for WOO 2.6.5 upgrade
            return $this->__hasShippingMethod($order, $method);
            
        }
        if (method_exists($order, 'has_shipping_method')) {
            return $order->has_shipping_method($method);
        }
        return $order->shipping_method == $method;
    }
    
    private function __hasShippingMethod($order, $method_id) {
        $shipping_methods = $order->get_shipping_methods();
        $has_method = false;

        if (empty($shipping_methods)) {
            return false;
        }

        foreach ($shipping_methods as $shipping_method) {
            if (is_array($shipping_method)) {
                $shippingMethodId = $shipping_method['method_id'];
            } else {
                $shippingMethodId = $shipping_method->get_method_id();
            }
            if (strpos($shippingMethodId, $method_id) === 0) {
                $has_method = true;
            }
        }

        return $has_method;
    }

    public function getCustomAttributeHtml($instance, $data) {
        
        if (method_exists($instance, 'get_custom_attribute_html')) {
            return $instance->get_custom_attribute_html($data);
        }
        return $this->__get_custom_attribute_html($data);
    }
    
    
    public function getTooltipHtml($instance, $data) {
        
        if (method_exists($instance, 'get_tooltip_html')) {
            return $instance->get_tooltip_html($data);
        }
        return $this->__get_tooltip_html($data);
    }
    public function getDescriptionHtml($instance, $data) {
        
        if (method_exists($instance, 'get_description_html')) {
            return $instance->get_description_html($data);
        }
        return $this->__get_description_html($data);
    }
    
    
    /**
     * 
     * @param string $number
     * @param WC_Order $order
     * @return boolean
     */
    public function isMobilePhone(&$number, $order) {
        $countryId = $order->shipping_country;
        $number = (string) $number;
        $customerNumbers = $this->getDialCodeHelper()
                ->separatePhoneNumberFromCountryCode($number, $countryId);
        $numberToTest = $customerNumbers['dial_code'] . $customerNumbers['phone_number'];

        switch ($countryId) {
            case 'EE':
                if (strlen($numberToTest) >= 10 && substr($numberToTest, 0, 5) === '+3725') {
                    return true;
                }
                break;
            case 'LV':
                if (strlen($numberToTest) >= 12 && substr($numberToTest, 0, 5) === '+3712') {
                    return true;
                }
                break;
            case 'LT':
                if (strlen($numberToTest) >= 11 && substr($numberToTest, 0, 5) === '+3706') {
                    return true;
                }
                if (strlen($numberToTest) >= 12 && substr($numberToTest, 0, 6) === '+37086') {
//                    $number = substr($number, 1);
                    return true;
                }
                break;
            default:
                break;
        }
        return false;
    }

    /**
     * Get HTML for tooltips
     *
     * @param  array $data
     * @return string
     */
    private function __get_tooltip_html($data) {

        if ($data['desc_tip'] === true) {
            $tip = $data['description'];
        } elseif (!empty($data['desc_tip'])) {
            $tip = $data['desc_tip'];
        } else {
            $tip = '';
        }

        return $tip ? '<img class="help_tip" data-tip="' . esc_attr($tip) . '" src="' . $this->getWooCommerce()->plugin_url() . '/assets/images/help.png" height="16" width="16" />' : '';
    }
    
	/**
	 * Get custom attributes
	 *
	 * @param  array $data
	 * @return string
	 */
	private function __get_custom_attribute_html( $data ) {

		$custom_attributes = array();

		if ( ! empty( $data['custom_attributes'] ) && is_array( $data['custom_attributes'] ) ) {

			foreach ( $data['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		return implode( ' ', $custom_attributes );
	}
        
	/**
	 * Get HTML for descriptions
	 *
	 * @param  array $data
	 * @return string
	 */
	private function __get_description_html( $data ) {

		if ( $data['desc_tip'] === true ) {
			$description = '';
		} elseif ( ! empty( $data['desc_tip'] ) ) {
			$description = $data['description'];
		} elseif ( ! empty( $data['description'] ) ) {
			$description = $data['description'];
		} else {
			$description = '';
		}

		return $description ? '<p class="description">' . wp_kses_post( $description ) . '</p>' . "\n" : '';
	}
        
    

    /**
     * 
     * @return Eabi_Woocommerce_Postoffice_Model_Controller
     */
    public function getControllerModel() {
        return $this->getModel('controller');
    }

    /**
     * 
     * @return Eabi_Woocommerce_Postoffice_Model_Installer
     */
    public function getInstallerModel() {
        return $this->getModel('Installer');
    }
    
    public function isServiceAvailableCached($serviceCode, $country) {
        if (!isset($this->_cachedServices[$serviceCode . '_ ' . $country])) {
            $this->_cachedServices[$serviceCode . '_ ' . $country] = $this->licence()
                    ->isServiceAvailable($serviceCode, $country);
        }
        return $this->_cachedServices[$serviceCode . '_ ' . $country];
    }

    /**
     * <p>Adds <code>$appendArray</code> after specified <p>$afterKey</p> inside <code>$inputArray</code></p>
     * @param array $inputArray original assoc array
     * @param array $appendArray array to be appended to original assoc array
     * @param boolean $afterKey when not supplied or key is not found, then appendArray is added to the end
     * @return array
     */
    public function addArrayAfterKey($inputArray, $appendArray, $afterKey = false) {
        $resultingArray = array();
        $appended = false;
        if (!is_string($afterKey)) {
            $afterKey = false;
        }

        foreach ($inputArray as $key => $value) {
            $resultingArray[$key] = $value;
            if ($key === $afterKey) {
                foreach ($appendArray as $iKey => $iValue) {
                    $resultingArray[$iKey] = $iValue;
                }
                $appended = true;
            }
        }

        if (!$appended) {
            foreach ($appendArray as $iKey => $iValue) {
                $resultingArray[$iKey] = $iValue;
            }
            $appended = true;
        }
        return $resultingArray;
    }

    /**
     * 
     * @param WC_Eabi_Postoffice $object
     * @return string
     */
    public function getMainConfigurationClassName($object) {
        for ($classes[] = get_class($object); $object = get_parent_class($object); $classes[] = $object) {
            if ($object == 'WC_Eabi_Postoffice') {
                break;
            }
            
        }
        if (count($classes)) {
            return $classes[count($classes) - 1];
        }
        return false;
    }
    
    
    protected $_cachedConfigurationMethods = array();

    public function getMainConfigurationMethod($object) {
        $className = get_class($object);
        if (!isset($this->_cachedConfigurationMethods[$className])) {
            $mainClassName = $this->getMainConfigurationClassName($object);
            if ($mainClassName) {
                $this->_cachedConfigurationMethods[$className] = new $mainClassName(true);
            } else {
                $this->_cachedConfigurationMethods[$className] = false;
            }
        }
        return $this->_cachedConfigurationMethods[$className];
    }
    
    
    /**
     * <p>Attempts to fetch data from order that is stored by carriers extending WC_Eabi_Postoffice</p>
     * <p>Data is stored in post_meta with field name _eabi_postoffice_autosend_data.</p>
     * <p>If match is found, it is decoded and returned as assoc array</p>
     * @param WC_Order $order
     * @param string $prefix
     * @return array
     */
    public function getDataFromOrder($order, $prefix) {
        $orderData = array();
        $fieldData = get_post_meta($order->id, WC_Eabi_Postoffice::AUTOSEND_DATA, true);
        if ($this->_commentContainsValidData($fieldData, $prefix)) {
            $orderData = @json_decode(@gzuncompress(@base64_decode($this->_getFromComment($fieldData, $prefix))), true);
            if (!is_array($orderData)) {
                //unserialize error on recognized pattern, should throw error or at least log
                $orderData = array();
            }
        }
        return $orderData;
    }
    
    public function setDataToOrder($order, array $data, $prefix) {
        $oldOrderData = $this->getDataFromOrder($order, $prefix);
        foreach ($data as $k => $v) {
            $oldOrderData[$k] = $v;
        }
        update_post_meta($order->id, WC_Eabi_Postoffice::AUTOSEND_DATA, $this->_getCommentFromData($oldOrderData, $prefix));
        return $oldOrderData;
    }

    protected function _commentContainsValidData($comment, $prefix) {
        //TODO: refactor to something better
        return strpos($comment, $prefix) === 0 
                && strlen($comment) > strlen($prefix);
    }
    
    protected function _getCommentFromData($data, $prefix) {
        return $prefix ."\n". chunk_split(base64_encode(gzcompress(json_encode($data))), 40, "\n");
    }
    
    protected function _getFromComment($comment, $prefix) {
        return str_replace($prefix, '', str_replace("\n", '', $comment));
    }
    
    

}

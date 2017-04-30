<?php

/*
  Plugin Name: E-Abi Omniva Shipping method plugin
  Plugin URI: http://www.e-abi.ee/
  Description: Adds Omniva Shipping methods to Woocommerce instance
  Version: 1.5
  Author: Matis Halmann, Aktsiamaailm LLC
  Author URI: http://www.e-abi.ee/
  Copyright: (c) Aktsiamaailm LLC
  License: Aktsiamaailm LLC License
  License URI: http://www.e-abi.ee/litsentsitingimused
 */

/*
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */

if (!function_exists('is_eabi_postoffice_active')) {
    require_once('includes/postoffice-functions.php');
}
if (is_eabi_postoffice_active()) {
    load_plugin_textdomain('wc_eabi-omniva', false, dirname(plugin_basename(__FILE__)) . '/');

    function woocommerce_shipping_eabi_omniva_init() {
        if (!class_exists('WC_Eabi_Omniva_ParcelTerminal')) {

            class WC_Eabi_Omniva_ParcelTerminal extends WC_Eabi_Postoffice {

                protected $_version = '1.5';

                const URL = 'https://www.omniva.ee/locations.csv';

                protected $_classPrefix = 'Eabi_Woocommerce_Omniva_';

                const PLUGIN_TEXT_DOMAIN = 'wc_eabi-omniva';

                /**
                 * <p>Refers to TYPE field in CSV</p>
                 * @var string
                 */
                protected $_pickupPointType = '0';

                /**
                 * <p>Parcel terminals have not generally known zip codes, so it is better to hide them</p>
                 * @var bool
                 */
                protected $_showZipInAddress = false;

                public function __construct($disableAction = false) {
                    $this->init();
                    parent::__construct($disableAction);
                    
                    $pluginTextDomain = WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN;
                    $this->_choosePickupLocationText = __('Choose parcel machine', $pluginTextDomain);
                    $this->_chosenPickupLocationText = __('Chosen parcel machine', $pluginTextDomain);
                    $this->_selectPickupLocationErrorText = __('Please select a parcel machine', $pluginTextDomain);
                    $this->_selectPickupLocationRefreshPageErrorText = __('Please refresh the page and select a different parcel machine', $pluginTextDomain);
                    $this->_chosenPickupLocationChangedText = __('Parcel machine changed to %s', $pluginTextDomain);
                }

                /**
                 * <p>This needs to exist, otherwise models won't be loaded properly</p>
                 * @return system
                 */
                protected function _getFilePath() {
                    return __FILE__;
                }

                public function init() {
                    $this->supportedAdditionalIds = array('post24_estonia', 'post24_latvia', 'post24_lithuania');
                    $this->id = 'eabi_omniva_parcelterminal';
                    $this->method_title = __('Omniva parcel machine', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);
                    $this->admin_page_description = __('Allows customer to choose Omniva parcel machine as a shipping method', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);


                    $this->init_form_fields();
                    $this->init_settings();



                    add_filter('woocommerce_shipping_methods', array(&$this, 'add_omniva_shipping_method'));
                }

                public function getSupportedCountries() {
                    return array('EE', 'LV', 'LT');
                }

                protected function getDefaultPrices() {
                    $defaultPrices = array(
                        '_1436644244750_750' => array(
                            'country_id' => 'EE',
                            's_price' => '2.325',
                            'm_price' => '2.825',
                            'l_price' => '3.325',
                            'free_from' => '',
                        ),
                        '_1436644244750_751' => array(
                            'country_id' => 'LV',
                            's_price' => '5.825',
                            'm_price' => '6.6583',
                            'l_price' => '7.4917',
                            'free_from' => '',
                        ),
                        '_1436644244750_752' => array(
                            'country_id' => 'LT',
                            's_price' => '6.6583',
                            'm_price' => '7.4917',
                            'l_price' => '8.325',
                            'free_from' => '',
                        ),
                    );
                    return json_encode($defaultPrices);
                }

                protected function getDefaultCost() {
                    $prices = json_decode($this->getDefaultPrices(), true);

                    foreach ($prices as $priceData) {
                        if (isset($priceData['l_price'])) {
                            return $priceData['l_price'];
                        }
                    }
                    return '8.325';
                }

                public function getGroupSort($group_name) {
//                    $group_name = strtolower($group_name);
                    $sorts = array(
                        /* EE */
                        'Harjumaa/Tallinn' => 20,
                        'Tartumaa/Tartu' => 19,
                        'Pärnumaa/Pärnu' => 18,
                        'Tallinn' => 20,
                        'Tartu' => 19,
                        'Pärnu' => 18,
                        'Tartu linn' => 19,
                        'Pärnu linn' => 18,
                        /* LV */
                        'Rīga' => 20,
                        'Daugavpils' => 19,
                        'Liepāja' => 18,
                        'Jelgava' => 17,
                        'Cēsis' => 16,
                        'Jēkabpils' => 15,
                        'Jūrmala' => 14,
                        'Ogre' => 13,
                        'Rēzekne' => 12,
                        'Tukums' => 11,
                        'Valmiera' => 10,
                        'Ventspils' => 9,
                        /* LT */
                        'Vilnius' => 20,
                        'Kaunas' => 19,
                        'Klaipėda' => 18,
                        'Šiauliai' => 17,
                    );
                    if (isset($sorts[$group_name])) {
                        return $sorts[$group_name];
                    }
                    if (strpos($group_name, '/') > 0) {
                        return 0;
                    }
                    return 0;
                }

                public function getOfficeList() {
                    $csvParcelTerminals = $this->helper()->getCsvParser()->getData(file_get_contents(self::URL), true, false, ";");
                    $result = array();
                   
                    

                    foreach ($csvParcelTerminals as $csvParcelTerminal) {

                        //Parcel machines are always TYPE = 0
                        if ($csvParcelTerminal['TYPE'] === $this->_pickupPointType) {

                            //In CSV file, there are some string values of NULL, which actually should be empty instead
                            $this->_removeNullStringLiterals($csvParcelTerminal);


                            $city = '';
                            if (stripos($csvParcelTerminal['A2_NAME'], ' vald') === false) {
                                //we have a city
                                $city = $csvParcelTerminal['A2_NAME'];
                            } else {
                                //we should leave city empty
                            }
                            $county = $csvParcelTerminal['A1_NAME'];
                            $allowedCities = array(
                                'Tallinn',
                                'Tartu',
                                'Pärnu',
                                'Tartu linn',
                                'Pärnu linn',
                            );
                            if (!in_array($city, $allowedCities)) {
                                $city = '';
                            } else {
                                $county = '';
                            }
                            $addressLineParts = array_filter(array_map('trim', array(
                                $csvParcelTerminal['A5_NAME'],
                                $csvParcelTerminal['A6_NAME'],
                                $csvParcelTerminal['A7_NAME'],
                                $csvParcelTerminal['A8_NAME'],
                            )));
                            $description = trim(implode(' ', $addressLineParts));

                            if ($description != '') {
                                if (stripos($csvParcelTerminal['A3_NAME'], ' linnaosa') > 0) {
                                    //city parts are not shown on the address field
                                    $csvParcelTerminal['A3_NAME'] = '';
                                }
                                $addressLine2Parts = array_filter(array_map('trim', array(
                                    $csvParcelTerminal['A3_NAME'],
                                    $csvParcelTerminal['A2_NAME'],
                                    ($this->_showZipInAddress ? $csvParcelTerminal['ZIP'] : ''),
                                )));

                                $description .= ', ';
                                if (isset($csvParcelTerminal['SERVICE_HOURS']) && $csvParcelTerminal['SERVICE_HOURS'] && strlen($csvParcelTerminal['SERVICE_HOURS'])) {
//                        $description .= ' (' . $eeCsvParcelTerminal['SERVICE_HOURS'] . ')';
                                    $addressLine2Parts[] = $csvParcelTerminal['SERVICE_HOURS'];
                                }

                                $description .= implode(', ', $addressLine2Parts);

//                    $description = trim(implode(', ', array($description, $eeCsvParcelTerminal['A2_NAME'] . ' ' . $eeCsvParcelTerminal['ZIP'])));
                            } else {
                                //lativa and lithuania has address line 1 on A2_NAME field
                                $description = $csvParcelTerminal['A2_NAME'];
                                $county = '';
                                $city = $csvParcelTerminal['A1_NAME'];
                            }
                                $result[] = array(
                                    'place_id' => (int) $csvParcelTerminal['ZIP'],
                                    'name' => $csvParcelTerminal['NAME'],
                                    'city' => $city,
                                    'county' => $county,
                                    'description' => $description,
                                    'country' => $csvParcelTerminal['A0_NAME'],
                                    'zip' => trim((string) $csvParcelTerminal['ZIP']),
                                    'extra' => $csvParcelTerminal,
//                    'group_sort' => $this->getGroupSort($eeCsvParcelTerminal[6]),
                                );
                        }
                    }
                    return $result;
                }

                public function init_form_fields() {
                    $woocommerce = $this->helper()->getWooCommerce();
                    $this->form_fields = array(
                        'licence' => array(
                            'title' => __('Licence', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'licence_state',
                            'label' => __('Status of current plugins licence', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => '',
                        ),
                        'enabled' => array(
                            'title' => __('Enable/Disable', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'label' => __('Enable this shipping method', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => 'no'
                        ),
                        'title' => array(
                            'title' => __('Method Title', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'description' => __('This controls the title which the user sees during checkout.', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => $this->method_title,
                        ),
                        'price_per_country' => array(
                            'title' => __('Shipping Fee per country', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'countryprice',
                            'description' => __('Shipping fee excluding tax. Enter an amount, e.g. 2.50.', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => $this->getDefaultPrices(),
                        ),
                        'cost' => array(
                            'title' => __('Shipping Fee', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'description' => __('Shipping fee excluding tax. Enter an amount, e.g. 2.50. (used, when fee for country is not found)', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => $this->getDefaultCost(),
                        ),
                        'availability' => array(
                            'title' => __('Method availability', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'default' => 'specific',
                            'class' => 'availability',
                            'options' => array(
                                'all' => __('All allowed countries', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                                'specific' => __('Specific Countries', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)
                            )
                        ),
                        'countries' => array(
                            'title' => __('Specific Countries', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'multiselect',
                            'class' => 'chosen_select',
                            'css' => 'width: 450px;',
                            'default' => $this->getSupportedCountries(),
                            'options' => $woocommerce->countries->countries
                        ),
                        'min_weight' => array(
                            'title' => __('Minimum weight allowed', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'description' => __('If cart contains product less than the weight, this shipping method is not available', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => ''
                        ),
                        'max_weight' => array(
                            'title' => __('Maximum weight allowed', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'description' => __('If cart contains product over the weight, this shipping method is not available', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => '30'
                        ),
                        'use_per_item_weights' => array(
                            'title' => __('Maximum weight limits are calculated per each item', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'label' => __('When disabled, then weight of whole cart is checked against maximum weight limit', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => 'yes'
                        ),
                        'min_amount' => array(
                            'title' => __('Enable this method from cart subtotal', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'description' => __('Empty or 0 disables this setting', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'type' => array(
                            'title' => __('Calculation Type', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'description' => '',
                            'default' => 'order',
                            'options' => array(
                                'order' => __('Per Order - charge shipping for the entire order as a whole', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                                'item' => __('Per Item - charge shipping for each item individually', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            )
                        ),
                        'free_boxes' => array(
                            'title' => __('Amount of free boxes per each paid box', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'description' => __('Used only when calcultion type is *Per Item*', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'tax_status' => array(
                            'title' => __('Tax Status', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'description' => '',
                            'default' => 'taxable',
                            'options' => array(
                                'taxable' => __('Taxable', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                                'none' => __('None', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)
                            )
                        ),
                        'enable_free' => array(
                            'title' => __('Enable free shipping', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'label' => __('Allow free shipping', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => 'no'
                        ),
                        'free_from' => array(
                            'title' => __('Free shipping from cart subtotal', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'description' => __('Free shipping if cart subtotal is equal or greater than amount specified', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => ''
                        ),
                        'free_from_qty' => array(
                            'title' => __('Free shipping from cart quantity', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'description' => __('Free shipping if number of items in cart is equal or greater than amount specified. Empty or 0 disables this setting', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => ''
                        ),
                        'show_names' => array(
                            'title' => __('Show pickup points names', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'description' => '',
                            'default' => 'short',
                            'options' => array(
                                'short' => __('Only pickup points names', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                                'long' => __('Pickup points names with description and address', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)
                            )
                        ),
                        'sort_terminals' => array(
                            'title' => __('Sort pickup points by priority', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'description' => '',
                            'default' => 'yes',
                            'options' => array(
                                'yes' => __('Pickup points from larger cities displayed first, rest sorted alphabetically', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                                'no' => __('Pickup points sorted alphabetically', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)
                            )
                        ),
                        'group_width' => array(
                            'title' => __('City selection menu width in pixels', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '200'
                        ),
                        'office_width' => array(
                            'title' => __('Pickup points selection menu width in pixels', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '250'
                        ),
                        'drop_menu_selection' => array(
                            'title' => __('Show all pickup points in a single select menu', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'label' => __('When disabled, user has to pick city/county first and then a suitable pickup point from previously selected city/county', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => 'yes'
                        ),
                        'hide_group_titles' => array(
                            'title' => __('Hide group titles', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'label' => __('Affects Cities/Counties grouping in pickup point select menu', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => 'no'
                        ),
                        'setting_override' => array(
                            'title' => __('Country specific settings overrides', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'countryoverride',
                            'label' => __('Country specific settings overrides', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => ''
                        ),
                        'enable_log' => array(
                            'title' => __('Enable logging', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'eabi_log',
                            'label' => __('Enable logging', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'default' => 'no'
                        ),
                    );
                }

                public function getTerminalTitle($office) {
                    if ($this->get_option('show_names') == 'long') {
                        return htmlspecialchars($office['name'] . ' (' . $office['description'] . ')');
                    }
                    return htmlspecialchars($office['name']);
                }

                public function getFullTerminalTitle($office) {
                    if ($this->get_option('show_names') == 'long') {
                        return htmlspecialchars($office['group_name'] . ', ' . $office['name'] . ' (' . $office['description'] . ')');
                    }
                    return htmlspecialchars($office['group_name'] . ', ' . $office['name']);
                }

                /**
                 * 
                 * @param array $package
                 * @param Eabi_Woocommerce_Postoffice_Model_Shippingprice $priceCalculationModel
                 * @return float
                 */
                public function order_shipping($package, $priceCalculationModel, $priceData) {
                    $cost = null;
                    $shippingCosts = array();

                    if ($priceData) {
                        if (isset($priceData['s_price'])) {
                            $productSizes = $priceCalculationModel->getProductSizes($package);
                            $priceInfo = array(
                                $priceData['s_price'],
                                $priceData['m_price'],
                                $priceData['l_price'],
                            );

                            foreach ($productSizes as $productSize) {
                                $sizeCost = $priceCalculationModel->getFeeFromSize($productSize, $priceInfo);
                                $shippingCosts[] = $sizeCost;
                            }
                            $cost = max($shippingCosts);
                        }
                    }
                    // Default rates
                    if (is_null($cost)) {
                        $cost = $this->get_option('cost');
                    }


                    // Shipping for whole order
                    return $cost;
                }

                /**
                 * 
                 * @param array $package
                 * @param Eabi_Woocommerce_Postoffice_Model_Shippingprice $priceCalculationModel
                 * @return float
                 */
                public function item_shipping($package, $priceCalculationModel, $priceData) {
                    // Per item shipping so we pass an array of costs (per item) instead of a single value
                    $amountOfFreeBoxes = (int) $this->get_option('free_boxes');

                    $weightSet = 1;
                    $packageWeight = array_sum($this->_getPackageWeights($package));
                    $shippingCosts = array();

                    $packageWeights = $this->_getPackageWeights($package);
                    $cost = null;

                    if ($priceData) {
                        if (isset($priceData['s_price'])) {

                            $productSizes = $priceCalculationModel->getProductSizes($package);
                            $priceInfo = array(
                                $priceData['s_price'],
                                $priceData['m_price'],
                                $priceData['l_price'],
                            );
                            foreach ($productSizes as $productSize) {
                                $sizeCost = $priceCalculationModel->getFeeFromSize($productSize, $priceInfo);
                                for ($i = 0; $i < $productSize['q']; $i++) {
                                    $shippingCosts[] = $sizeCost;
                                }
                            }
                            if ($amountOfFreeBoxes > 0) {
                                $numberOfDesiredItems = ceil(1 / (1 + $amountOfFreeBoxes) * count($shippingCosts));
//                                $cost = 0;
                                $desiredCosts = array();
                                $temporaryCollectedCosts = array_chunk($shippingCosts, 1 + $amountOfFreeBoxes);
                                foreach ($temporaryCollectedCosts as $temporaryCollectedCostValues) {
                                    $desiredCosts[] = max($temporaryCollectedCostValues);
                                }
                                $cost = array_sum($desiredCosts);
//                                for ($i = 0; $i < $numberOfDesiredItems; $i++) {
//                                    $cost += $shippingCosts[$i];
//                                }
                            } else {
                                $cost = array_sum($shippingCosts);
                            }
                        }
                    }

                    if (is_null($cost)) {
                        // Shipping per item
                        $costs = array();
                        foreach ($package['contents'] as $item_id => $values) {
                            $_product = $values['data'];

                            if ($values['quantity'] > 0 && $_product->needs_shipping()) {
                                $itemcost = $this->get_option('cost');

                                for ($q = 0; $q < $values['quantity']; $q++) {
                                    $costs[] = $itemcost;
                                }
//                            $costs[$item_id] = ( ( $cost ) * $values['quantity'] );
                            }
                        }

                        if ($amountOfFreeBoxes > 0) {
                            $cost = 0;
                            $numberOfDesiredItems = ceil(1 / (1 + $amountOfFreeBoxes) * count($costs));
                            for ($i = 0; $i < $numberOfDesiredItems; $i++) {
                                $cost += $costs[$i];
                            }
                        } else {
                            $cost = array_sum($costs);
                        }
                    }




                    return $cost;
                }

                public function add_omniva_shipping_method($methods) {
                    $methods[$this->id] = &$this;
//                    $methods[] = &$this;
                    return $methods;
                }

                protected function _removeNullStringLiterals(&$array) {
                    foreach ($array as $k => $v) {
                        if ($v === 'NULL') {
                            $array[$k] = '';
                        }
                    }
                    return $array;
                }

            }

            class WC_Eabi_Omniva_Postoffice extends WC_Eabi_Omniva_ParcelTerminal {
                protected $_calculationModel = 'shippingpriceoffice';

                /**
                 * <p>Refers to TYPE field in CSV</p>
                 * @var string
                 */
                protected $_pickupPointType = '1';

                /**
                 * <p>Parcel terminals have not generally known zip codes, so it is better to hide them</p>
                 * @var bool
                 */
                protected $_showZipInAddress = true;

                public function __construct($disableAction = false) {
                    $this->init();
                    parent::__construct($disableAction);
                    $pluginTextDomain = WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN;
                    $this->_choosePickupLocationText = __('Choose postal office', $pluginTextDomain);
                    $this->_chosenPickupLocationText = __('Chosen postal office', $pluginTextDomain);
                    $this->_selectPickupLocationErrorText = __('Please select a postal office', $pluginTextDomain);
                    $this->_selectPickupLocationRefreshPageErrorText = __('Please refresh the page and select a different postal office', $pluginTextDomain);
                    $this->_chosenPickupLocationChangedText = __('Postal office changed to %s', $pluginTextDomain);
                }

                /**
                 * <p>This needs to exist, otherwise models won't be loaded properly</p>
                 * @return system
                 */
                protected function _getFilePath() {
                    return __FILE__;
                }

                public function init() {
                    $this->supportedAdditionalIds = array('post24_postkontor');
                    $this->id = 'eabi_omniva_postoffice';
                    $this->method_title = __('Omniva postal offices', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);
                    $this->admin_page_description = __('Allows customer to choose delivery to Omniva postal office as a shipping method', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);


                    $this->init_form_fields();
                    $this->init_settings();



                    add_filter('woocommerce_shipping_methods', array(&$this, 'add_omniva_shipping_method'));
                }

                public function getSupportedCountries() {
                    return array('EE');
                }

                protected function getDefaultPrices() {
                    $defaultPrices = array(
                        '_1436644244750_750' => array(
                            'country_id' => 'EE',
                            's_price' => '2.825',
                            'm_price' => '3.325',
                            'l_price' => '3.7417',
                            'free_from' => '',
                        ),
                    );
                    return json_encode($defaultPrices);
                }

            }

            class WC_Eabi_Omniva_Courier extends WC_Eabi_Omniva_ParcelTerminal {
                protected $_calculationModel = 'shippingpricecourier';

                public function __construct($disableAction = false) {
                    $this->init();
                    parent::__construct($disableAction);
                    $pluginTextDomain = WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN;
                    $this->_choosePickupLocationText = __('Choose shipping method', $pluginTextDomain);
                    $this->_chosenPickupLocationText = __('Chosen shippingmethod', $pluginTextDomain);
                    $this->_selectPickupLocationErrorText = __('Please select a shipping method', $pluginTextDomain);
                    $this->_selectPickupLocationRefreshPageErrorText = __('Please refresh the page and select a different shipping method', $pluginTextDomain);
                    $this->_chosenPickupLocationChangedText = __('Shipping method changed to %s', $pluginTextDomain);
                }

                /**
                 * <p>This needs to exist, otherwise models won't be loaded properly</p>
                 * @return system
                 */
                protected function _getFilePath() {
                    return __FILE__;
                }

                public function init() {
                    $this->supportedAdditionalIds = array();
                    $this->id = 'eabi_omniva_courier';
                    $this->method_title = __('Omniva courier to home or work', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);
                    $this->admin_page_description = __('Allows customer to choose delivery with Omniva courier as a shipping method', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);


                    $this->init_form_fields();
                    $this->init_settings();



                    add_filter('woocommerce_shipping_methods', array(&$this, 'add_omniva_shipping_method'));
                }

                public function getSupportedCountries() {
                    return array('*');
                }

                protected function getDefaultPrices() {
                    $defaultPrices = array(
                        '_1436644244750_750' => array(
                            'country_id' => 'EE',
                            'base_price' => '6.075',
                            'kg_price' => '0',
                            'free_from' => '',
                        ),
                        '_1436644244750_751' => array(
                            'country_id' => 'LV',
                            'base_price' => '8.25',
                            'kg_price' => '0.6667',
                            'free_from' => '',
                        ),
                        '_1436644244750_752' => array(
                            'country_id' => 'LT',
                            'base_price' => '8.9167',
                            'kg_price' => '0.8333',
                            'free_from' => '',
                        ),
                        '_1436644244750_753' => array(
                            'country_id' => 'FI',
                            'base_price' => '18.95',
                            'kg_price' => '0.175',
                            'free_from' => '',
                        ),
                        '_1436644244750_754' => array(
                            'country_id' => '*',
                            'base_price' => '40',
                            'kg_price' => '1',
                            'free_from' => '',
                        ),
                    );
                    return json_encode($defaultPrices);
                }

                protected function getDefaultCost() {
                    $prices = json_decode($this->getDefaultPrices(), true);

                    foreach ($prices as $priceData) {
                        if (isset($priceData['base_price'])) {
                            return $priceData['base_price'];
                        }
                    }
                    return '40';
                }

                /**
                 * <p>This carrier has no parcel terminal selection feature, so one entry must still be added with shipping method title defined for this carrier.</p>
                 * @return array single office element
                 */
                public function getOfficeList() {
                    //we have only one item to insert here
                    $result = array();
                    $result[] = array(
                        'place_id' => 1,
                        'name' => $this->get_option('title'),
                        'city' => '',
                        'county' => '',
                        'description' => '',
                        'country' => '',
                        'zip' => '',
                        'group_sort' => 0,
                    );
                    return $result;
                }

                public function getTerminalTitle($office) {
                    return htmlspecialchars(__($this->get_option('title'), WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN));
                }

                public function getFullTerminalTitle($office) {
                    return htmlspecialchars(__($this->get_option('title'), WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN));
                }

                public function init_form_fields() {
                    parent::init_form_fields();

                    //set columns to price_per_country
                    $this->form_fields['price_per_country']['columns'] = $this->_getPricePerCountryColumns();


                    //set availability to new default

                    $this->form_fields['availability']['default'] = 'all';
                    $this->form_fields['countries']['default'] = '';

                    //set new type options
                    $this->form_fields['type']['options'] = array(
                        'order' => __('Per Order - shipping price is base price + kg price', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN),
                        'item' => __('Per Item - shipping price is base price multiplied by number of (items - free boxes) + kg price', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN),
                    );


                    //remove not required fields
                    unset($this->form_fields['show_names']);
                    unset($this->form_fields['sort_terminals']);
                    unset($this->form_fields['group_width']);
                    unset($this->form_fields['office_width']);
                    unset($this->form_fields['drop_menu_selection']);
                    unset($this->form_fields['hide_group_titles']);
                }

                /**
                 * 
                 * @param array $package
                 * @param Eabi_Woocommerce_Postoffice_Model_Shippingprice $priceCalculationModel
                 * @return float
                 */
                public function item_shipping($package, $priceCalculationModel, $priceData) {
                    // Per item shipping so we pass an array of costs (per item) instead of a single value
                    $amountOfFreeBoxes = (int) $this->get_option('free_boxes');

                    $weightSet = 1;
                    $packageWeight = array_sum($this->_getPackageWeights($package));

                    $packageWeights = $this->_getPackageWeights($package);
                    $cost = null;

                    if ($priceData) {
                        if (isset($priceData['base_price'])) {

                            $weightSet = 1;
                            //we need to have price per every kg, where
                            //0-10kg consists only base price
                            //10,1-20kg equals base price + extra price
                            //20,1-30kg equals base price + extra price * 2
                            $extraWeightCost = max(ceil($packageWeight / $weightSet) * $priceData['kg_price'], 0);

                            $handlingFee = ($this->helper()->getNumberOfPackagesFromItemWeights($packageWeights, $this->get_option('max_weight')) - $amountOfFreeBoxes) * $priceData['base_price'];

                            $cost = $handlingFee + $extraWeightCost;
                        }
                    }

                    if (is_null($cost)) {
                        // Shipping per item
                        $costs = array();
                        foreach ($package['contents'] as $item_id => $values) {
                            $_product = $values['data'];

                            if ($values['quantity'] > 0 && $_product->needs_shipping()) {
                                $itemcost = $this->get_option('cost');

                                for ($q = 0; $q < $values['quantity']; $q++) {
                                    $costs[] = $itemcost;
                                }
//                            $costs[$item_id] = ( ( $cost ) * $values['quantity'] );
                            }
                        }

                        if ($amountOfFreeBoxes > 0) {
                            $cost = 0;
                            $numberOfDesiredItems = ceil(1 / (1 + $amountOfFreeBoxes) * count($costs));
                            for ($i = 0; $i < $numberOfDesiredItems; $i++) {
                                $cost += $costs[$i];
                            }
                        } else {
                            $cost = array_sum($costs);
                        }
                    }




                    return $cost;
                }

                /**
                 * 
                 * @param array $package
                 * @param Eabi_Woocommerce_Postoffice_Model_Shippingprice $priceCalculationModel
                 * @return float
                 */
                public function order_shipping($package, $priceCalculationModel, $priceData) {
                    $cost = null;
                    $shippingCosts = array();
                    $weightSet = 1;
                    $packageWeight = array_sum($this->_getPackageWeights($package));

                    $packageWeights = $this->_getPackageWeights($package);

                    if ($priceData) {
                        if (isset($priceData['base_price'])) {
                            $weightSet = 1;
                            //we need to have price per every kg, where
                            //0-10kg consists only base price
                            //10,1-20kg equals base price + extra price
                            //20,1-30kg equals base price + extra price * 2
                            $extraWeightCost = max(ceil($packageWeight / $weightSet) * $priceData['kg_price'], 0);

                            $cost = $priceData['base_price'] + $extraWeightCost;
                        }
                    }
                    // Default rates
                    if (is_null($cost)) {
                        $cost = $this->get_option('cost');
                    }


                    // Shipping for whole order
                    return $cost;
                }
                
                public function getTerminals($groupId = null, $addressId = null) {
                    $res = $this->getTerminalsWoAddress($groupId, null);
                   
                    return $res;
                }
                
                public function getTerminalsCount($addressId = null) {
                    return parent::getTerminalsCount(null);
                }
                

                protected function _getPricePerCountryColumns() {
                    return array(
                        array(
                            'label' => __('Country', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'name' => 'country_id',
                            'type' => 'select',
                            'class' => '',
                            'style' => '',
                            'options' => WC_Eabi_Postoffice::instance()->helper()->toLabelValues(WC_Eabi_Postoffice::instance()->helper()->getServiceCountries()),
                        ),
                        array(
                            'label' => __('Base price', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN),
                            'name' => 'base_price',
                            'type' => 'text',
                            'class' => '',
                            'style' => '',
                        ),
                        array(
                            'label' => __('Kg price', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN),
                            'name' => 'kg_price',
                            'type' => 'text',
                            'class' => '',
                            'style' => '',
                        ),
                        array(
                            'label' => __('Free shipping from', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'name' => 'free_from',
                            'type' => 'text',
                            'class' => '',
                            'style' => '',
                        ),
                    );
                }

            }

            class WC_Eabi_Omniva_Letter extends WC_Eabi_Omniva_Courier {

                public function init() {
                    $this->supportedAdditionalIds = array();
                    $this->id = 'eabi_omniva_letter';
                    $this->method_title = __('Omniva letter to home or work', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);
                    $this->admin_page_description = __('Allows customer to choose delivery with Omniva letter as a shipping method', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);


                    $this->init_form_fields();
                    $this->init_settings();



                    add_filter('woocommerce_shipping_methods', array(&$this, 'add_omniva_shipping_method'));
                }

            }

            class WC_Eabi_Omniva_Alternateletter extends WC_Eabi_Omniva_Courier {

                public function init() {
                    $this->supportedAdditionalIds = array();
                    $this->id = 'eabi_omniva_alternateletter';
                    $this->method_title = __('Omniva letter to home or work (alternate)', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);
                    $this->admin_page_description = __('Allows customer to choose delivery with Omniva letter (alternate) as a shipping method', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN);
                    $this->init_form_fields();
                    $this->init_settings();



                    add_filter('woocommerce_shipping_methods', array(&$this, 'add_omniva_shipping_method'));
                }

            }

        }
        new WC_Eabi_Omniva_ParcelTerminal();
        new WC_Eabi_Omniva_Postoffice();
        new WC_Eabi_Omniva_Courier();
        new WC_Eabi_Omniva_Letter();
        new WC_Eabi_Omniva_Alternateletter();
    }

    add_action('woocommerce_shipping_init', 'woocommerce_shipping_eabi_omniva_init');
}
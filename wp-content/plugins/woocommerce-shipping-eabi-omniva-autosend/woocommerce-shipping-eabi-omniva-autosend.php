<?php
/*
  Plugin Name: E-Abi Woocommerce Omniva Autosend method plugin
  Plugin URI: https://www.e-abi.ee/
  Description: Adds Omniva automatic data sending methods to Woocommerce instance
  Version: 1.9
  Author: Matis Halmann, Aktsiamaailm LLC
  Author URI: https://www.e-abi.ee/
  Copyright: (c) Aktsiamaailm LLC
  License: Aktsiamaailm LLC License
  License URI: https://www.e-abi.ee/litsentsitingimused
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
    load_plugin_textdomain('wc_eabi_omniva_autosend', false, dirname(plugin_basename(__FILE__)) . '/');

    function woocommerce_shipping_eabi_omniva_autosend_init() {
        if (version_compare(WC_Eabi_Postoffice::instance()->getVersion(), '1.8', '<')) {
            //if postoffice version less than 1.8, then return
            return;
        }

        if (!class_exists('WC_Eabi_Omniva_Autosend')) {
            if (!class_exists('Eabi_Omniva_Estonia_Api')) {
                require_once 'includes/class-eabi-omniva-api.php';
            }

            class WC_Eabi_Omniva_Autosend {

                public $id = 'eabi_omniva_parcelterminal';

                const PLUGIN_TEXT_DOMAIN = 'wc_eabi_omniva_autosend';

                public function __construct() {
                    if (is_admin()) {

                        //add extra form fields
                        add_filter('woocommerce_settings_api_form_fields_' . $this->id, array(&$this, 'addServicesToFormParcelmachine'), 10, 1);
                        add_filter('woocommerce_settings_api_form_fields_eabi_omniva_postoffice', array(&$this, 'addServicesToFormPostoffice'), 10, 1);
                        add_filter('woocommerce_settings_api_form_fields_eabi_omniva_courier', array(&$this, 'addServicesToFormCourier'), 10, 1);
                        add_filter('woocommerce_settings_api_form_fields_eabi_omniva_letter', array(&$this, 'addServicesToFormLetter'), 10, 1);
                        add_filter('woocommerce_settings_api_form_fields_eabi_omniva_alternateletter', array(&$this, 'addServicesToFormLetter'), 10, 1);

                        //add extra licence fields
                        add_action('eabi_woocommerce_postoffice_eabi_omniva_parcelterminal_licence_status_display', array(&$this, 'addAutosendLicenceDisplay'), 10, 2);
                        add_action('eabi_woocommerce_postoffice_eabi_omniva_postoffice_licence_status_display', array(&$this, 'addAutosendLicenceDisplay'), 10, 2);
                        add_action('eabi_woocommerce_postoffice_eabi_omniva_courier_licence_status_display', array(&$this, 'addAutosendLicenceDisplay'), 10, 2);
                        add_action('eabi_woocommerce_postoffice_eabi_omniva_letter_licence_status_display', array(&$this, 'addAutosendLicenceDisplay'), 10, 2);
                        add_action('eabi_woocommerce_postoffice_eabi_omniva_alternateletter_licence_status_display', array(&$this, 'addAutosendLicenceDisplay'), 10, 2);
                    }
                    //add automatic datasend actions
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_AUTOSEND . '_eabi_omniva_parcelterminal', array(&$this, 'autoSendData'), 10, 5);
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_AUTOSEND . '_eabi_omniva_postoffice', array(&$this, 'autoSendData'), 10, 5);
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_AUTOSEND . '_eabi_omniva_courier', array(&$this, 'autoSendData'), 10, 5);
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_AUTOSEND . '_eabi_omniva_letter', array(&$this, 'autoSendData'), 10, 5);
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_AUTOSEND . '_eabi_omniva_alternateletter', array(&$this, 'autoSendData'), 10, 5);

                    //add print packing slip actions
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_PRINT_PACKING_SLIP . '_eabi_omniva_parcelterminal', array(&$this, 'printPackingSlip'), 10, 5);
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_PRINT_PACKING_SLIP . '_eabi_omniva_postoffice', array(&$this, 'printPackingSlip'), 10, 5);
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_PRINT_PACKING_SLIP . '_eabi_omniva_courier', array(&$this, 'printPackingSlip'), 10, 5);
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_PRINT_PACKING_SLIP . '_eabi_omniva_letter', array(&$this, 'printPackingSlip'), 10, 5);
                    add_filter('eabi_postoffice_action_' . WC_Eabi_Postoffice::ACTION_PRINT_PACKING_SLIP . '_eabi_omniva_alternateletter', array(&$this, 'printPackingSlip'), 10, 5);

                    //add print packing slip actions
                    add_filter('eabi_postoffice_can_print_eabi_omniva_parcelterminal', array(&$this, 'canPrintPackingSlip'), 10, 4);
                    add_filter('eabi_postoffice_can_print_eabi_omniva_postoffice', array(&$this, 'canPrintPackingSlip'), 10, 4);
                    add_filter('eabi_postoffice_can_print_eabi_omniva_courier', array(&$this, 'canPrintPackingSlip'), 10, 4);
                    add_filter('eabi_postoffice_can_print_eabi_omniva_letter', array(&$this, 'canPrintPackingSlip'), 10, 4);
                    add_filter('eabi_postoffice_can_print_eabi_omniva_alternateletter', array(&$this, 'canPrintPackingSlip'), 10, 4);

                    add_filter('eabi_omniva_autosend_data_before', array($this, 'addServicesFromConfiguration'), 10, 7);
                }

                /**
                 * 
                 * @param array $autoSendResult
                 * @param WC_Order $order
                 * @param string $pickupLocationId
                 * @param WC_Eabi_Postoffice $shippingModel
                 * @param WC_Eabi_Postoffice $mainShippingModel
                 * @return array
                 */
                public function autoSendData($autoSendResult, $order, $pickupLocationId, $shippingModel, $mainShippingModel) {
                    $comment = '';
                    $barCodes = array();
                    $items = array();

                    $parcelQty = $this->_getNumberOfPackagesForOrder($order, $shippingModel);
                    $selectedOffice = $shippingModel->getTerminal($pickupLocationId);
                    $order->shipping_country = $this->_getShippingCountry($order, $shippingModel);


                    $phoneNumbers = WC_Eabi_Postoffice::instance()->helper()->getDialCodeHelper()
                            ->separatePhoneNumberFromCountryCode($order->billing_phone, $order->shipping_country);

                    $phone = $phoneNumbers['dial_code'] . $phoneNumbers['phone_number'];

                    $servicesDefinitions = $this->_decodeServiceMatrix($shippingModel->get_option('senddata_service_country'));


                    if (!isset($servicesDefinitions[$order->shipping_country])) {
                        $message = sprintf(__('Services have not been defined for country %s, please verify your Omniva configuration', self::PLUGIN_TEXT_DOMAIN), $order->shipping_country);
                        throw new Eabi_Woocommerce_Postoffice_Exception($message);
                    }

                    $serviceDefinition = $servicesDefinitions[$order->shipping_country];




                    $codCurrency = trim($serviceDefinition['currency']);
                    $orderItemSets = $this->_getOrderItemsInSets($order);
                    $codAmount = 0;

                    if (!$order->is_paid()) {
                        $codAmount = WC_Eabi_Postoffice::instance()
                                ->helper()
                                ->toTargetCurrency($order->get_total(), get_woocommerce_currency(), $codCurrency);
                    }

                    $deliveryPoint = $order->shipping_city;

                    if ($order->shipping_state) {
                        $deliveryPoint .= ', ' . $order->shipping_state;
                    }

                    $shippingAddressData = array(
                        'person_name' => $order->shipping_first_name . " " . $order->shipping_last_name,
                        'mobile' => null,
                        'phone' => null,
                        'email' => $order->billing_email,
                        'person_code' => null,
                        'address' => array(
                            '@attributes' => array(
                                'postcode' => $order->shipping_postcode,
                                'deliverypoint' => $deliveryPoint,
                                'country' => $order->shipping_country,
                                'street' => implode(' ', array_filter(array($order->shipping_address_1, $order->shipping_address_2), 'trim')),
                                'offloadPostcode' => $pickupLocationId,
                            ),
                        ),
                    );

                    $offloadPostCodeRequired = array(
                        'eabi_omniva_parcelterminal',
                        'eabi_omniva_postoffice',
                    );
                    if (!in_array($shippingModel->id, $offloadPostCodeRequired)) {
                        unset($shippingAddressData['address']['@attributes']['offloadPostcode']);
                    }

                    if (WC_Eabi_Postoffice::instance()->helper()->isMobilePhone($phone, $order)) {
                        $shippingAddressData['mobile'] = $phoneNumbers['dial_code'] . $phoneNumbers['phone_number'];
                    } else {
                        $shippingAddressData['phone'] = $phoneNumbers['dial_code'] . $phoneNumbers['phone_number'];
                    }

                    $api = $this->_getApi($shippingModel, $mainShippingModel);

                    $requestData = $api->getDefaultBusinessMsgTemplate();

                    //fill it with specified details
                    $requestData['interchange']['header']['@attributes']['file_id'] = $order->id;
                    $requestData['interchange']['header']['@attributes']['currency_cd'] = $serviceDefinition['currency'];

                    //base service
                    $requestData['interchange']['item_list']['item']['@attributes']['service'] = trim($serviceDefinition['base_service']);

                    //extra services
                    $extraServices = $this->_splitExtraServices($serviceDefinition['add_service']);

                    $requestData['interchange']['item_list']['item']['show_return_code_sms'] = $shippingModel->get_option('show_return_code_sms') == 'yes' ? 'true' : 'false';
                    $requestData['interchange']['item_list']['item']['show_return_code_email'] = $shippingModel->get_option('show_return_code_email') == 'yes' ? 'true' : 'false';


                    if ($codAmount > 0) {
                        $codService = trim($serviceDefinition['cod_service']);
                        $codCurrency = trim($serviceDefinition['currency']);
                        if (!$codService || !$codCurrency) {
                            $message = sprintf(__('COD Service or currency has not been defined for country %s, please verify your Omniva configuration', self::PLUGIN_TEXT_DOMAIN), $order->shipping_country);
                            throw new Eabi_Woocommerce_Postoffice_Exception($message);
                        }
                        //append COD service to $extraServices
                        $extraServices[] = $codService;

                        //set monetary values also
                        $requestData['interchange']['item_list']['item']['monetary_values'] = array(
                            'values' => array(),
                        );

                        $requestData['interchange']['item_list']['item']['monetary_values']['values'][] = array(
                            '@attributes' => array(
                                'code' => 'item_value',
                                'amount' => $codAmount,
//                    $this->_getDirectoryHelper()->currencyConvert($order->getBaseTotalDue(), $order->getBaseCurrency()->getCode(), $codCurrency),
                            ),
                        );
                    } else {
                        //no COD means that package has no monetary values
                        unset($requestData['interchange']['item_list']['item']['monetary_values']);
                    }

                    if (!count($extraServices)) {
                        unset($requestData['interchange']['item_list']['item']['add_service']);
                    } else {
                        $requestData['interchange']['item_list']['item']['add_service'] = array(
                            'option' => array(),
                        );
                        foreach ($extraServices as $extraService) {
                            $requestData['interchange']['item_list']['item']['add_service']['option'][] = array(
                                '@attributes' => array(
                                    'code' => $extraService,
                                ),
                            );
                        }
                    }


                    //partner id
                    $requestData['interchange']['item_list']['item']['partnerId'] = $order->id;

                    //shipping address
                    $requestData['interchange']['item_list']['item']['receiverAddressee'] = $shippingAddressData;

                    //determine the function to be used with automatic data sending
                    $sendDataFunction = $mainShippingModel->get_option('senddata_message');
                    if (!in_array($sendDataFunction, $this->_getAllowedSendDataFunctions())) {
                        $sendDataFunction = 'preSendMsg';
                    }






                    $packageValue = WC_Eabi_Postoffice::instance()
                            ->helper()
                            ->toTargetCurrency($order->get_total(), get_woocommerce_currency(), $codCurrency);


                    $requestData = apply_filters('eabi_omniva_autosend_data_before', $requestData, $order, $packageValue, $selectedOffice, $codCurrency, $shippingModel, $mainShippingModel);

                    //MPS package => sends out $mps_nr packages, where each item is identical
                    $mps = $shippingModel->get_option('senddata_mps') == 'yes';
                    $mps_nr = (int) $shippingModel->get_option('senddata_mps_nr');
                    
                    if ($mps && $mps_nr > 1) {
                        $item = $requestData['interchange']['item_list']['item'];
                        //packetUnitIdentificator=MPS container
                        $item['@attributes']['packetUnitIdentificator'] = 'MPS container';
                        $requestData['interchange']['item_list']['item'] = array();
                        $parnerId = $item['partnerId'];
                        for ($i = 0; $i < $mps_nr; $i++) {
                            if ($i > 0) {
                                $item['partnerId'] = $parnerId . '-' . $i;
                            }
                            $requestData['interchange']['item_list']['item'][] = $item;
                        }
                    }

                    //send the data to server
                    $requestResult = $api->$sendDataFunction($requestData);
                    $errors = array();
                    if (isset($requestResult->faultyPacketInfo->barcodeInfo)) {
                        if (isset($requestResult->faultyPacketInfo->barcodeInfo->barcode)) {
                            $errors[] = $requestResult->faultyPacketInfo->barcodeInfo->message;
                        } else {
                            
			   			foreach ($requestResult->faultyPacketInfo->barcodeInfo as $barcodeInfo) {
                        	if(is_object($barcodeInfo)){       	
				 				$errors[] = $barcodeInfo->message;
							}
                        }
			    		$errors[] = $requestResult->prompt;
                        }
                    }
                    if (count($errors)) {
                        throw new Eabi_Woocommerce_Postoffice_Exception(implode(', ', $errors));
                    }

                    if (isset($requestResult->savedPacketInfo->barcodeInfo)) {
                        if (isset($requestResult->savedPacketInfo->barcodeInfo->barcode)) {
                            $barCodes[] = $requestResult->savedPacketInfo->barcodeInfo->barcode;
                        } else {
                            foreach ($requestResult->savedPacketInfo->barcodeInfo as $barcodeInfo) {
                                $barCodes[] = $barcodeInfo->barcode;
                            }
                        }
                    }



                    if (count($barCodes)) {
                    	add_post_meta($order->id,'omniva_barcode',$barCodes );
                    
                        return array('barcodes' => $barCodes);
                    }
                    return array();
                }
                
                /**
                 * 
                 * @param WC_Order $order
                 * @param WC_Eabi_Postoffice $instance
                 * @return string
                 */
                protected function _getShippingCountry($order, $instance) {
                    $shipping_country = $order->shipping_country;
                    if (!$shipping_country) {
                        $shipping_country = get_option('woocommerce_default_country');
                    }
                    if (!$shipping_country) {
                        $shipping_country = $instance->getFirstSupportedCountry();
                    }
                    return $shipping_country;
                }
                

                /**
                 * 
                 * @param string $packingSlipResult
                 * @param WC_Order $order
                 * @param string $barcode
                 * @param WC_Eabi_Postoffice $shippingModel
                 * @param WC_Eabi_Postoffice $mainShippingModel
                 * @return array
                 */
                public function printPackingSlip($packingSlipResult, $order, $barcode, $shippingModel, $mainShippingModel) {
                    if (is_string($barcode) || is_array($barcode)) {
                        $api = $this->_getApi($shippingModel, $mainShippingModel);
                        $res = $api->getAddressCardFile($barcode);
                        if (isset($res->successAddressCards->addressCardData) && is_array($res->successAddressCards->addressCardData)) {
                            //we return pdf for first barcode pdf, because there is no implementation for multiple packing slips yet
                            return $res->successAddressCards->addressCardData[0]->fileData;
                        }
                        if (isset($res->successAddressCards->addressCardData->barcode)) {
                            //fetch the barcode file
                            return ($res->successAddressCards->addressCardData->fileData);
                        }
                    }
                    return false;
                }
                
                
                /**
                 * 
                 * @param bool $barcodeResult default true
                 * @param WC_Order $order
                 * @param WC_Eabi_Postoffice $shippingModel
                 * @param WC_Eabi_Postoffice $mainShippingModel
                 * @return boolean
                 */
                public function canPrintPackingSlip($barcodeResult, $order, $shippingModel, $mainShippingModel) {
                    return $mainShippingModel->get_option('senddata_message') != 'preSendMsg';
                }
                

                /**
                 * 
                 * @param array $requestData
                 * @param WC_Order $order
                 * @param float $packageValue
                 * @param array $selectedOffice
                 * @param string $codCurrency
                 * @param WC_Eabi_Postoffice $shippingModel
                 * @param WC_Eabi_Postoffice $mainShippingModel
                 * @return array
                 */
                public function addServicesFromConfiguration($requestData, $order, $packageValue, $selectedOffice, $codCurrency, $shippingModel, $mainShippingModel) {

                    $fieldService = $mainShippingModel->get_option('field_product_service');
                    $fieldIdCode = $mainShippingModel->get_option('field_customer_id');
                    $wasServicesProcessed = false;
                    //implement additional services from the products
                    if ($fieldService) {

                        foreach ($order->get_items() as $orderItem) {
                            $product = $order->get_product_from_item($orderItem);
                            if (!$product || !$product->exists()) {
                                continue;
                            }
                            $serviceFieldValue = get_post_meta($product->id, $fieldService, true);
                            if (!$serviceFieldValue) {
                                continue;
                            }
                            $servicesFromProduct = $this->_splitExtraServices($serviceFieldValue);
                            if (count($servicesFromProduct)) {
                                $this->_processExtraServices($servicesFromProduct, $requestData['interchange']['item_list']['item']['add_service']['option']);
                                $wasServicesProcessed = true;
                            }
                        }
                    }

                    //check if we have idcheck
                    if ($this->_serviceExists($requestData['interchange']['item_list']['item']['add_service']['option'], 'SI') && !$requestData['interchange']['item_list']['item']['receiverAddressee']['person_code']) {
                        if (!$fieldIdCode) {
                            throw new Eabi_Woocommerce_Postoffice_Exception(__('SI (ID check) service requires social security number', self::PLUGIN_TEXT_DOMAIN));
                        }
                        $user = WC_Eabi_Postoffice::instance()->getUserFromOrder($order);
                        if (!$user) {
                            throw new Eabi_Woocommerce_Postoffice_Exception(__('SI (ID check) service requires registered customer', self::PLUGIN_TEXT_DOMAIN));
                        }
                        $socialSecurityCode = get_user_meta($user->ID, $fieldIdCode, true);
                        if (!$socialSecurityCode) {
                            throw new Eabi_Woocommerce_Postoffice_Exception(sprintf(__('SI (ID check) service requires customer to have social security number in attribute %s', self::PLUGIN_TEXT_DOMAIN), $fieldIdCode));
                        }
                        //set the idcode
                        $requestData['interchange']['item_list']['item']['receiverAddressee']['person_code'] = $socialSecurityCode;
                        $wasServicesProcessed = true;
                    }




                    return $requestData;
                }

                /**
                 * 
                 * @param type $supportedMethod
                 * @param type $block
                 */
                public function addAutosendLicenceDisplay($supportedMethod, $block) {
                    foreach ($supportedMethod->getSupportedCountries() as $supportedCountry) {
                        ?><tr>
                            <td><?php echo htmlspecialchars(__('Automatic data sending', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)) ?></td>
                            <td><?php echo htmlspecialchars($block->getCountryName($supportedCountry)); ?></td>
                            <td><?php echo $block->getLicenceStatus('autosend/' . $supportedMethod->id, $supportedCountry); ?></td>
                        </tr><?php
                    }
                }

//                public function addSettingsToForm($settings) {
//                    $settings = WC_Eabi_Postoffice::instance()->helper()
//                            ->addArrayAfterKey($settings, $this->_getServiceCodeFields(), 'price_per_country');
//                    $settings = WC_Eabi_Postoffice::instance()->helper()
//                            ->addArrayAfterKey($settings, $this->_getServiceGenericFields(), 'sort_terminals');
//                    return $settings;
//                }

                public function addServicesToFormParcelmachine($settings) {
                    $baseCodes = array(
                        'PA' => __('EE | PA - From parcel terminal to parcel terminal', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                        'PP' => __('EE | PP - From postoffice to parcel terminal', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                        'PU' => __('EE | PU - Courier picks up the goods and sends them to parcel terminal', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                        'DA' => __('LT | DA - From parcel terminal to parcel terminal', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                        'DP' => __('LT | DP - From sorting office to parcel terminal', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                    );
                    $settings = WC_Eabi_Postoffice::instance()->helper()
                            ->addArrayAfterKey($settings, $this->_getServiceCodeFields($this->_getDefaultServicesParcelMachine(), $baseCodes), 'price_per_country');
                    $settings = WC_Eabi_Postoffice::instance()->helper()
                            ->addArrayAfterKey($settings, $this->_getServiceGenericFields(), 'sort_terminals');
                    return $settings;
                }

                public function addServicesToFormPostoffice($settings) {
                    $baseCodes = array(
                        'PO' => __('EE | PO - From parcel terminal to postoffice', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                        'CD' => __('EE | CD - From postoffice to postoffice', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                        'CE' => __('EE | CE - Courier picks up the goods and sends them to postoffice', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                    );

                    $settings = WC_Eabi_Postoffice::instance()->helper()
                            ->addArrayAfterKey($settings, $this->_getServiceCodeFields($this->_getDefaultServicesPostoffice(), $baseCodes), 'price_per_country');
                    return $settings;
                }

                public function addServicesToFormCourier($settings) {
                    $settings = WC_Eabi_Postoffice::instance()->helper()
                            ->addArrayAfterKey($settings, $this->_getServiceCodeFields($this->_getDefaultServicesCourier()), 'price_per_country');
                    return $settings;
                }

                public function addServicesToFormLetter($settings) {
                    $settings = WC_Eabi_Postoffice::instance()->helper()
                            ->addArrayAfterKey($settings, $this->_getServiceCodeFields($this->_getDefaultServicesLetter()), 'price_per_country');
                    return $settings;
                }

                protected function _getServiceCodeFields($defaults = false, $baseCodes = false) {
                    if ($defaults === false) {
                        $defaults = $this->_getDefaultServicesParcelMachine();
                    }

                    return array(
                        'senddata_service_country' => array(
                            'title' => __('Omniva services', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'countryoverride',
                            'description' => __('Used only with automatic data sending. Ask your Omniva account manager for proper codes if unhappy with default values.', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'default' => $defaults,
                            'columns' => array(
                                array(
                                    'label' => __('Country', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                                    'name' => 'country_id',
                                    'type' => 'select',
                                    'class' => '',
                                    'style' => '',
                                    'options' => WC_Eabi_Postoffice::instance()->helper()->toLabelValues(WC_Eabi_Postoffice::instance()->helper()->getServiceCountries()),
                                ),
                                array(
                                    'label' => __('Base service code', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                                    'name' => 'base_service',
                                    'type' => is_array($baseCodes) ? 'select' : 'text',
                                    'class' => '',
                                    'style' => '',
                                    'options' => is_array($baseCodes) ? WC_Eabi_Postoffice::instance()->helper()->toLabelValues($baseCodes) : array(),
                                ),
                                array(
                                    'label' => __('Additional service codes (comma separated)', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                                    'name' => 'add_service',
                                    'type' => 'text',
                                    'class' => '',
                                    'style' => '',
                                ),
                                array(
                                    'label' => __('Cash on delivery service code', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                                    'name' => 'cod_service',
                                    'type' => 'text',
                                    'class' => '',
                                    'style' => '',
                                ),
                                array(
                                    'label' => __('Currency', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                                    'name' => 'currency',
                                    'type' => 'select',
                                    'class' => '',
                                    'style' => '',
                                    'options' => WC_Eabi_Postoffice::instance()->helper()->toLabelValues(get_woocommerce_currencies()),
                                ),
                            ),
                        ),
                        'senddata_mps' => array(
                            'title' => __('Enable MPS (multi-package) delivery', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'default' => 'no',
                        ),
                        'senddata_mps_nr' => array(
                            'title' => __('Number of packages per each MPS request', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '1',
                        ),
                        'show_return_code_sms' => array(
                            'title' => __('Show return code sms', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'default' => 'yes',
                        ),
                        'show_return_code_email' => array(
                            'title' => __('Show return code e-mail', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'default' => 'yes',
                        ),
                    );
                }

                protected function _getServiceGenericFields() {
                    $sendEventOptions = array(
                        'after_payment' => __('After order payment', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                        'after_shipment' => __('After order shipping', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                        'manual' => __('I will send data myself by clicking on the button', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                    );
                    $sendpackageUrlOptions = array(
//                        'https://217.159.234.77:443/epmx/services/messagesService.wsdl' => __('Testing endpoint', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
//                        'https://217.159.234.93:443/epmx/services/messagesService.wsdl' => __('Live endpoint', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                        'https://testeservice.post.ee/epmx/services/messagesService.wsdl' => __('Testing endpoint', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                        'https://edixml.post.ee/epmx/services/messagesService.wsdl' => __('Live endpoint', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                    );
                    $sendpackageMessageOptions = array(
                        'preSendMsg' => sprintf(__('%s - Parcel data needs to be confirmed in Omniva self service panel', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN), 'presendMsg'),
                        'businessToClientMsg' => sprintf(__('%s - Parcel data is confirmed, packing slip can be printed from order view', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN), 'businessToClientMsg'),
                    );


                    return array(
                        'senddata_enable' => array(
                            'title' => __('Enable automatic data sending to Omniva server', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'checkbox',
                            'label' => __('Only if the order has been paid for or the order is COD', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'default' => 'no',
                        ),
                        'sendpackage_username' => array(
                            'title' => __('Omniva Web-service username', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'description' => __('Omniva self-service credentials cannot be used, you need to ask for Web-service credentials from Omniva in order to be able to use automatic data sending features', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '',
                        ),
                        'sendpackage_password' => array(
                            'title' => __('Omniva Web-service password', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'password',
                            'default' => '',
                        ),
                        'sendpackage_url' => array(
                            'title' => __('Send parcel data', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'description' => __('By default test environment can not be used. You may need to ask Omniva account manager to activate test environment first.', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'default' => 'https://217.159.234.93:443/epmx/services/messagesService.wsdl',
                            'options' => $sendpackageUrlOptions,
                        ),
                        'senddata_event' => array(
                            'title' => __('When to send parcel data', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'options' => $sendEventOptions,
                            'default' => 'manual',
                        ),
                        'senddata_message' => array(
                            'title' => __('Senddata function used', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'options' => $sendpackageMessageOptions,
                            'default' => 'preSendMsg',
                        ),
                        /*
                          'autosend_emails_to' => array(
                          'title' => __('Send order confirmation with barcode to following e-mails after parcel data send success', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                          'type' => 'text',
                          'default' => '',
                          ),
                         * 
                         */
                        'return_name' => array(
                            'title' => __('Return address name', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '',
                        ),
                        'return_email' => array(
                            'title' => __('Return address e-mail', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '',
                        ),
                        'return_phone' => array(
                            'title' => __('Return address phone', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '',
                        ),
                        'return_street' => array(
                            'title' => __('Return address street', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '',
                        ),
                        'return_citycounty' => array(
                            'title' => __('Return address city, county', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '',
                        ),
                        'return_postcode' => array(
                            'title' => __('Return address zip code', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => '',
                        ),
                        'return_country' => array(
                            'title' => __('Return address country', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'select',
                            'default' => 'EE',
                            'options' => WC_Eabi_Postoffice::instance()->helper()->getWooCommerce()->countries->countries,
                        ),
                        'field_product_service' => array(
                            'title' => __('Product attribute name for declaring extra services in a product', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'description' => __('Refer to readme.txt for extra information about this feature', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => 'eabi_omniva_service',
                        ),
                        'field_customer_id' => array(
                            'title' => __('Customer attribute name, which holds social security code', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'description' => __('Refer to readme.txt for extra information about this feature', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN),
                            'type' => 'text',
                            'default' => 'eabi_social_security_code',
                        ),
                    );
                }


                /**
                 * 
                 * @param array $selectedOffice
                 * @param WC_Order $order
                 * @return array
                 */
                protected function _fillDestination($selectedOffice, $order) {
                    $targetTerminalOrigData = @json_decode($selectedOffice['cached_attributes'], true);

                    if ($order->shipping_country == 'EE') {
                        return array(
                            'place_id' => $selectedOffice['remote_place_id'],
                        );
                    } else if ($order->shipping_country == 'FI') {
                        return array(
                            'postalcode' => $selectedOffice['zip_code'],
                            'routingcode' => $targetTerminalOrigData['routingcode'],
                        );
                    } else {
                        return null;
                    }
                }

                /**
                 * 
                 * @param array $selectedOffice
                 * @param WC_Order $order
                 * @return array
                 */
                protected function _fillAddress($selectedOffice, $order, $mainShippingModel) {
                    $addressDetails = WC_Eabi_Postoffice::instance()
                            ->helper()
                            ->getAddressHelper()
                            ->separateHouseApartmentFromStreet($order, $mainShippingModel);

                    return array(
                        'postalcode' => $order->shipping_postcode,
                        'street' => $addressDetails['street'],
                        'house' => $addressDetails['house'],
                        'apartment' => $addressDetails['apartment'],
                        'city' => implode(', ', array_filter(array($order->shipping_city, $order->shipping_state), 'trim')),
                        'country' => $order->shipping_country,
                        'details' => '',
                        'timewindow' => $selectedOffice['remote_place_id'],
                    );
                }

                protected function _serviceExists($data, $code) {
                    if (!$data) {
                        return false;
                    }
                    if (isset($data['@attributes'])) {
                        $data = array($data);
                    }

                    foreach ($data as $service) {
                        if (isset($service['@attributes']) && isset($service['@attributes']['code'])) {
                            if ($service['@attributes']['code'] == $code) {
                                return true;
                            }
                        }
                    }
                    return false;
                }

                /**
                 * <p>Takes comma separated value as input and returns indexed array as output</p>
                 * <p>Input is cleaned and trimmed from whitespace</p>
                 * @param string $value
                 * @return array
                 */
                protected function _splitExtraServices($value) {
                    $extraServicesFromConfig = explode(",", $value);
                    return array_filter(array_map('trim', $extraServicesFromConfig));
                }

                protected function _processExtraServices(array $extraservices = array(), &$res = array()) {
                    foreach ($extraservices as $extraservice) {
                        if (!$this->_serviceExists($res, $extraservice)) {
                            $res[] = array(
                                '@attributes' => array(
                                    'code' => $extraservice,
                                ),
                            );
                        }
                    }
                    return $res;
                }

                /**
                 * <p>Same as <code>_decodeShippingMatrix</code> but meant for decoding extra services</p>
                 * @param string $input
                 * @return array
                 */
                protected function _decodeServiceMatrix($input) {
                    $shippingMatrix = @json_decode($input, true);
                    $result = array();
                    if (!is_array($shippingMatrix)) {
                        return $result;
                    }
                    foreach ($shippingMatrix as $countryDefinition) {
                        $result[$countryDefinition['country_id']] = $countryDefinition;
                    }
                    return $result;
                }

                /**
                 * <p>Used to split an order into multiple parts for data sending</p>
                 * <p>As of now no splitting is performed and array of 1 element is returned</p>
                 * @param WC_Order $order
                 * @return array
                 */
                protected function _getOrderItemsInSets($order) {
                    $orderItems = array();
                    foreach ($order->get_items() as $orderItem) {
                        $product = $order->get_product_from_item($orderItem);
                        if ($product && $product->exists() && !$product->is_downloadable()) {
                            /* @var $product WC_Product */
                            $orderItem['kg_weight'] = WC_Eabi_Postoffice::instance()->toKg($product->weight);
                            $orderItem['cm_height'] = WC_Eabi_Postoffice::instance()->toCm($product->height);
                            $orderItem['cm_width'] = WC_Eabi_Postoffice::instance()->toCm($product->width);
                            $orderItem['cm_length'] = WC_Eabi_Postoffice::instance()->toCm($product->length);
                            $orderItems[] = $orderItem;
                        }
                    }
                    return array(
                        $orderItems,
                    );
                }

                /**
                 * <p>Returns number or parcels for the order according to Maximum Package Weight defined in Omniva settings</p>
                 * @param WC_Order $order
                 * @param WC_Eabi_Postoffice $shippingMethod
                 * @return int
                 * @see WC_Eabi_Postoffice::getNumberOfPackagesFromItemWeights()
                 */
                protected function _getNumberOfPackagesForOrder($order, $shippingMethod) {
                    $productWeights = array();
                    foreach ($order->get_items() as $orderItem) {
                        $product = $order->get_product_from_item($orderItem);
                        if ($product && $product->exists() && !$product->is_downloadable()) {
                            /* @var $product WC_Product */
                            for ($i = 0; $i < ($orderItem['qty'] - 0); $i++) {
                                $productWeights[] = WC_Eabi_Postoffice::instance()->toKg($product->weight);
                            }
                        }
                    }
                    return WC_Eabi_Postoffice::instance()->helper()->getNumberOfPackagesFromItemWeights($productWeights, $shippingMethod->get_option('max_weight'));
                }

                /**
                 * <p>Returns order items weight in kilograms</p>
                 * @param array $orderItems
                 * @return string
                 */
                protected function _getOrderItemsWeight($orderItems) {
                    $weigtht = 0;
                    foreach ($orderItems as $orderItem) {
                        $weigtht += ($orderItem['kg_weight'] * ($orderItem['qty'] - 0));
                    }
                    return (string) round($weigtht, 3);
                }

                /**
                 * <p>Returns order items parcel size, 1 = smallest, 4 = largest</p>
                 * @param array $orderItems
                 * @return string
                 */
                protected function _getOrderItemsParcelSize($orderItems) {
                    return (string) 1;
                }

                private $_apis = array();

                /**
                 * 
                 * @param WC_Eabi_Postoffice $shippingModel
                 * @param WC_Eabi_Postoffice $mainShippingModel
                 * @return Eabi_Omniva_Estonia_Api
                 */
                protected function _getApi($shippingModel, $mainShippingModel) {
                    $className = get_class($shippingModel);
                    if (!isset($this->_apis[$className])) {
                        $api = new Eabi_Omniva_Estonia_Api();
                        $api->setMainShippingModel($mainShippingModel);
                        $api->setShippingModel($shippingModel);
                        $this->_apis[$className] = $api;
                    }
                    return $this->_apis[$className];
                }

                protected function _getAllowedSendDataFunctions() {
                    return array(
                        'businessToClientMsg',
                        'preSendMsg',
                    );
                }

                protected function _getDefaultServicesParcelMachine() {
                    $defaultServices = array(
                        '_1436644244750_750' => array(
                            'country_id' => 'EE',
                            'base_service' => 'PA',
                            'add_service' => 'ST,SF',
                            'cod_service' => 'BP',
                            'currency' => 'EUR',
                        ),
                        '_1436644244751_751' => array(
                            'country_id' => 'LV',
                            'base_service' => 'PP',
                            'add_service' => 'ST,SF',
                            'cod_service' => 'BP',
                            'currency' => 'EUR',
                        ),
                        '_1436644244752_752' => array(
                            'country_id' => 'LT',
                            'base_service' => 'PP',
                            'add_service' => 'ST,SF',
                            'cod_service' => 'BP',
                            'currency' => 'EUR',
                        ),
                    );
                    return json_encode($defaultServices);
                }

                protected function _getDefaultServicesPostoffice() {
                    $defaultServices = array(
                        '_1436644244750_750' => array(
                            'country_id' => 'EE',
                            'base_service' => 'CD',
                            'add_service' => 'ST,SF',
                            'cod_service' => 'BP',
                            'currency' => 'EUR',
                        ),
                    );
                    return json_encode($defaultServices);
                }

                protected function _getDefaultServicesCourier() {
                    $defaultServices = array(
                        '_1436644244750_750' => array(
                            'country_id' => 'EE',
                            'base_service' => 'QP',
                            'add_service' => 'SF',
                            'cod_service' => 'BP',
                            'currency' => 'EUR',
                        ),
                        '_1436644244751_751' => array(
                            'country_id' => 'LV',
                            'base_service' => 'QP',
                            'add_service' => '',
                            'cod_service' => 'BP',
                            'currency' => 'EUR',
                        ),
                        '_1436644244752_752' => array(
                            'country_id' => 'LT',
                            'base_service' => 'QP',
                            'add_service' => '',
                            'cod_service' => 'BP',
                            'currency' => 'EUR',
                        ),
                    );
                    return json_encode($defaultServices);
                }

                protected function _getDefaultServicesLetter() {
                    $defaultServices = array(
                        '_1436644244751_751' => array(
                            'country_id' => 'EE',
                            'base_service' => 'QP',
                            'add_service' => 'SF',
                            'cod_service' => 'BP',
                            'currency' => 'EUR',
                        ),
                    );
                    return json_encode($defaultServices);
                }

            }

            new WC_Eabi_Omniva_Autosend();
        }
    }

    add_action('woocommerce_loaded', 'woocommerce_shipping_eabi_omniva_autosend_init');
}

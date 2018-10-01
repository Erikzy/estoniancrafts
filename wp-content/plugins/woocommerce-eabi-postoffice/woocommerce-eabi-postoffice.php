<?php
/*
  Plugin Name: Woocommerce E-Abi Postoffice plugin
  Plugin URI: https://www.e-abi.ee/
  Description: Base plugin for all E-Abi shipping methods
  Version: 1.23
  Author: Matis Halmann, Aktsiamaailm LLC
  Author URI: https://www.e-abi.ee/
  Copyright: (c) Aktsiamaailm LLC
  License: Aktsiamaailm LLC License
  License URI: https://www.e-abi.ee/litsentsitingimused
 */

/*
   *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */

/**
 * Description of woocommerce-eabi-postoffice
 *
 * @author Matis
 */
if (!function_exists('is_woocommerce_active')) {
    require_once('woo-includes/woo-functions.php');
}

if (is_woocommerce_active()) {
    load_plugin_textdomain('wc_woocommerce_eabi_postoffice', false, dirname(plugin_basename(__FILE__)) . '/');

    function woocommerce_shipping_eabi_postoffice_init() {
        if (!class_exists('WC_Eabi_Postoffice')) {

            class WC_Eabi_Postoffice extends WC_Shipping_Method {

                protected $_version = '1.23';
                protected $_pluginTextDomain = 'wc_woocommerce_eabi_postoffice';
                protected $_calculationModel = 'shippingprice';

                const PLUGIN_TEXT_DOMAIN = 'wc_woocommerce_eabi_postoffice';

                public $id = 'eabi_postoffice';

                const MODULE_ID = 'eabi_postoffice';
                const PICKUP_ID = '_eabi_postoffice_pickup_id';
                const PICKUP_LOCATION = '_eabi_postoffice_pickup_location';
                const SHIPPING_METHOD = '_eabi_postoffice_shipping_method';
                const AUTOSEND_DATA = '_eabi_postoffice_autosend_data';
                const PICKUP_LOCATION_DEPRECATED = '_pickup_location';
                const REGISTRATION_URL = 'https://www.e-abi.ee/eabi_licence/register/';
                const ACTION_AUTOSEND = 'eabi_autosend_data';
                const ACTION_PRINT_PACKING_SLIP = 'eabi_print_slip';
				const ACTION_PRINT_PACKING_SLIP_DOKAN = 'eabi_print_slip_dokan';
                private static $_self;
                protected $_choosePickupLocationText = '';
                protected $_chosenPickupLocationText = '';
                protected $_chosenPickupLocationChangedText = '';
                protected $_selectPickupLocationErrorText = '';
                protected $_selectPickupLocationRefreshPageErrorText = '';
                protected $_actionsAdded = false;
                protected static $_actionsRegister = array();
                protected $_insideDetectedCountry;
                protected $supportedAdditionalIds = array();

                public function __construct($disableActions = false) {
                    $this->_choosePickupLocationText = __('Choose pickup location', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
                    $this->_chosenPickupLocationText = __('Pickup Location', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
                    $this->_selectPickupLocationErrorText = __('Please select a pickup location', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
                    $this->_selectPickupLocationRefreshPageErrorText = __('Please refresh the page and select a different pickup location', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
                    $this->_chosenPickupLocationChangedText = __('Pickup location changed to %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
/*                    $this->supports = array(
                        'shipping-zones',
						'instance-settings',
						'instance-settings-modal',
                    );
*/
                    if (!$disableActions) {
                        $this->includes();
                        $this->__init_hooks();
                        $this->___construct();
                    }
                }

                /**
                 * <p>Adds auto send feature to Mass actions in orders view</p>
                 * @param array $actions
                 * @return array
                 */
                public function addAutoSendToListOrderMassActions($actions) {

                    $actions[self::ACTION_AUTOSEND] = __('Send shipment data to server', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
                    return $actions;
                }

                /**
                 * <p>Adds print packing slip action to single order row in orders view</p>
                 * @param array $actions
                 * @param WC_Order $order
                 * @return array
                 */
                public function addPrintPackingSlipToListOrderAction($actions, $order) {
                    if ($order) {
                        $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));

                        if (!$shippingModelName) {
                            $shippingModelName = $this->_getShippingMethod($order);
                        }
                        if ($shippingModelName) {
                            $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                            $logger = $shippingModel->getLogger();

                            //check if licence exists for autosend/shippingModelName
                            // CACHE by shippingModelName
                            //check if automatic data sending is enabled at all for specified shippingModelName
                            //TODO: check if data has been successfully sent for specified order
                            //TODO: if YES, then add print packing slip button

                            if ($this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $this->_getShippingCountry($order)) && $this->_isDataSent($order, $shippingModel)) {

//                            echo '<pre>'.htmlspecialchars(print_r($this->helper()->getMainConfigurationMethod($shippingModel), true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
//                            echo '<pre>' . htmlspecialchars(print_r($this->helper()->getMainConfigurationClassName($shippingModel), true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE) . '</pre>';
                                $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                                $canPrint = apply_filters('eabi_postoffice_can_print_' . $shippingModel->id, true, $order, $shippingModel, $mainConfigurationClass);
                                if (!$canPrint) {
                                    return $actions;
                                }
                                $logger->debug(array(
                                    'adding print slip action for order' => $order->id,
                                    'method' => $shippingModel->id,
                                ));


                                $barcodes = apply_filters('eabi_postoffice_get_barcode_links_' . $shippingModel->id, $this->_getBarcodes($order), $order, $shippingModel, $mainConfigurationClass);
                                $i = 0;
                                foreach ($barcodes as $barcode) {
                                    $actions[self::ACTION_PRINT_PACKING_SLIP . $barcode] = array(
                                        'action' => self::ACTION_PRINT_PACKING_SLIP,
                                        'url' => wp_nonce_url(admin_url('admin-ajax.php?action=' . self::ACTION_PRINT_PACKING_SLIP . '&order_id=' . $order->id . '&slip_index=' . $i), 'eabi-print-packing-slip'),
                                        'name' => __('Print packing slip', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)
                                    );
                                    $i++;
                                    
                                }

                            }
                        }
                    }
                    return $actions;
                }
                
                protected function _getWoocommerceOrder($order_id) {
                    if (function_exists('wc_get_order')) {
                        return wc_get_order($order_id);
                    } else {
                        return new WC_Order($order_id);
                    }
                }

                /**
                 * <p>Adds send shipment data to server action to single order row in orders view</p>
                 * @param array $actions
                 * @param WC_Order $order
                 * @return array
                 */
                public function addAutoSendToListOrderAction($actions, $order) {
                    if ($order) {
                        $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));

                        if (!$shippingModelName) {
                            $shippingModelName = $this->_getShippingMethod($order);
                        }
                        if ($shippingModelName) {
                            $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                            $logger = $shippingModel->getLogger();

                            //check if licence exists for autosend/shippingModelName
                            // CACHE by shippingModelName
                            //check if automatic data sending is enabled at all for specified shippingModelName
                            //TODO: check if data has been successfully sent for specified order
                            //TODO: if YES, then add print packing slip button

                            if ($this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $this->_getShippingCountry($order)) && $this->_canSendData($order, $shippingModel)) {
                                $logger->debug(array(
                                    'adding auto send action for order' => $order->id,
                                    'method' => $shippingModel->id,
                                ));
//                            echo '<pre>'.htmlspecialchars(print_r($this->helper()->getMainConfigurationMethod($shippingModel), true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
//                            echo '<pre>' . htmlspecialchars(print_r($this->helper()->getMainConfigurationClassName($shippingModel), true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE) . '</pre>';
                                $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);

                                if ($mainConfigurationClass && $mainConfigurationClass->get_option('senddata_enable') == 'yes') {
                                    $actions[self::ACTION_AUTOSEND] = array(
                                        'action' => self::ACTION_AUTOSEND,
                                        'url' => wp_nonce_url(admin_url('admin-ajax.php?action=' . self::ACTION_AUTOSEND . '&order_id=' . $order->id), 'eabi-auto-send-data'),
                                        'name' => __('Send shipment data to server', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)
                                    );
                                }
                            }
                        }
                    }
                    return $actions;
                }

                /**
                 * <p>Adds send shipment data to server action to single order view</p>
                 * @global WC_Order $theorder
                 * @param array $actions
                 * @param WC_Order $order
                 * @return array
                 */
                public function addAutoSendToSingleOrderActions($actions, $order = null) {
                    global $theorder;
                    if (!$order) {
                        $order = $theorder;
                    }
                    if ($order) {
                        $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));

                        if ($shippingModelName && $this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $this->_getShippingCountry($order))) {
                            $shippingModelName = $this->_getShippingMethod($order);
                        }
                        $shippingModel = false;
                        if ($shippingModelName) {
                            $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                        }

                        if ($shippingModelName && $this->_canSendData($order, $shippingModel)) {
                            $actions[self::ACTION_AUTOSEND] = __('Send shipment data to server', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
                        }
                    }
                    return $actions;
                }

                /**
                 * 
                 * @param WC_Order $order
                 * @param WC_Eabi_Postoffice $shippingModel
                 * @return boolean
                 */
                protected function _canSendData($order, $shippingModel) {
                    $disabledStatuses = array('on-hold');
                    $isOrderPaid = false;
                    if ($this->_isWoo25()) {
                        $isOrderPaid = $order->is_paid();
                    } else {
                        $isOrderPaid = $order->has_status(array( 'processing', 'completed' ));
                    }
                    if ($this->_orderIsVirtual($order) || !$isOrderPaid || in_array($order->get_status(), $disabledStatuses)) {
                        //TODO: add COD payment support
                        if ($order->payment_method == 'eabi_cod'
                                && $order->get_status() == 'on-hold') {
                            //do not return false here
                        } else {
                            return false;
                        }
                    }
                    $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                    if ($mainConfigurationClass && $mainConfigurationClass->get_option('senddata_enable') == 'yes' && !$this->_isDataSent($order, $shippingModel)) {
                        return true;
                    }
                    return false;
                }
                
                public function getUserFromOrder($order) {
                    $userId = $order->customer_user ? intval($order->customer_user) : 0;
                    $user = false;
                    if ($userId) {
                        $user = get_user_by('id', $userId);
                    }
                    return $user;
                }
                
                /**
                 * <p>Returns true if order contains downloadable items only</p>
                 * @param WC_Order $order
                 * @return boolean
                 */
                private function _orderIsVirtual($order) {
                    foreach ($order->get_items() as $item) {
                        $product = $order->get_product_from_item($item);
                        if ($product && $product->exists() && !$product->is_downloadable()) {
                            return false;
                        }
                    }
                    return true;
                }

                protected function _isDataSent($order, $shippingModel) {
                    $prefix = '---' . $order->id . '---';
                    $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                    $data = $this->helper()->getDataFromOrder($order, $prefix);
                    if (isset($data['barcodes']) && $mainConfigurationClass && $mainConfigurationClass->get_option('senddata_enable') == 'yes') {
                        return true;
                    }

                    return false;
                }
                protected function _getBarcodes($order) {
                    $prefix = '---' . $order->id . '---';
                    $data = $this->helper()->getDataFromOrder($order, $prefix);
                    if (isset($data['barcodes'])) {
                        if (is_string($data['barcodes'])) {
                            $data['barcodes'] = array($data['barcodes']);
                        }
                        return $data['barcodes'];
                    }

                    return false;
                }


				public function doDokanFriendlyPackingSlipAction(){
                    $order_id = absint($_GET['order_id']);
			        $order = $this->_getWoocommerceOrder($order_id);
			        $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));
                    if (!$shippingModelName) {
                      $shippingModelName = $this->_getShippingMethod($order);
                    }
                    if ($shippingModelName && $this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $this->_getShippingCountry($order))) {
	                   $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
	                   $logger = $shippingModel->getLogger();
                       $logger->debug(array(
                            'printing packing slip for order' => $order->id,
                            'method' => $shippingModel->id,
                        ));
                        $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                        $barCodes = $this->_getBarcodes($order);
                        $index = isset($_GET['slip_index']) && isset($barCodes[$_GET['slip_index']]) ? $_GET['slip_index'] : 0;
                        try {
        	               $printSlipResult = apply_filters('eabi_postoffice_action_' . self::ACTION_PRINT_PACKING_SLIP . '_' . $shippingModel->id, false, $order, $barCodes[$index], $shippingModel, $mainConfigurationClass);
                            if (!$printSlipResult) {
                                throw new Eabi_Woocommerce_Postoffice_Exception(__('No packing slip found', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN));
                                }
                                header("Content-Disposition: attachment; filename=" . 'packing-slip-order-' . $order->id . ".pdf");
                                header("Content-Length: " . strlen($printSlipResult));
                                header('Content-type: application/octet-stream');
                                echo $printSlipResult;
                                exit;
                            } catch (Eabi_Woocommerce_Postoffice_Exception $ex) {
                                $order->add_order_note(sprintf(__('Packing slip printing failed. Message: %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), $ex->getMessage()));
                            }
                        }
                    
				}	

                /**
                 * <p>Print packing slip for the specified order</p>
                 */
                public function doPrintPackingSlipAction() {
                    if (current_user_can('edit_shop_orders') && check_admin_referer('eabi-print-packing-slip')) {
//			$status   = sanitize_text_field( $_GET['status'] );
                        $order_id = absint($_GET['order_id']);
//                        echo '<pre>'.htmlspecialchars(print_r($order_id, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
//                        exit;
//			if ( wc_is_order_status( 'wc-' . $status ) && $order_id ) {
//                        $order = wc_get_order($order_id);
                        $order = $this->_getWoocommerceOrder($order_id);

                        $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));

                        if (!$shippingModelName) {
                            $shippingModelName = $this->_getShippingMethod($order);
                        }
                        if ($shippingModelName && $this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $this->_getShippingCountry($order))) {
                            $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                            $logger = $shippingModel->getLogger();
                            $logger->debug(array(
                                'printing packing slip for order' => $order->id,
                                'method' => $shippingModel->id,
                            ));
                            $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                            $barCodes = $this->_getBarcodes($order);
                            $index = isset($_GET['slip_index']) && isset($barCodes[$_GET['slip_index']]) ? $_GET['slip_index'] : 0;


                            //TODO: slip index itself comes from $_GET['slip_index'];
                            //eabi_postoffice_action_eabi_autosend_data_eabi_itella_smartpost
                            try {
                                $printSlipResult = apply_filters('eabi_postoffice_action_' . self::ACTION_PRINT_PACKING_SLIP . '_' . $shippingModel->id, false, $order, $barCodes[$index], $shippingModel, $mainConfigurationClass);
                                if (!$printSlipResult) {
                                    throw new Eabi_Woocommerce_Postoffice_Exception(__('No packing slip found', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN));
                                }
                                header("Content-Disposition: attachment; filename=" . 'packing-slip-order-' . $order->id . ".pdf");
                                header("Content-Length: " . strlen($printSlipResult));
                                header('Content-type: application/octet-stream');
                                echo $printSlipResult;
                                exit;
                            } catch (Eabi_Woocommerce_Postoffice_Exception $ex) {
                                $order->add_order_note(sprintf(__('Packing slip printing failed. Message: %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), $ex->getMessage()));
                            }
                        }
                    }

                    wp_safe_redirect(wp_get_referer() ? wp_get_referer() : admin_url('edit.php?post_type=shop_order') );
                    die();
                }

                /**
                 * <p>Auto send data to server the specified order</p>
                 */
                public function doAutoSendAction() {
                    if (current_user_can('edit_shop_orders') && check_admin_referer('eabi-auto-send-data')) {
//			$status   = sanitize_text_field( $_GET['status'] );
                        $order_id = absint($_GET['order_id']);
//                        echo '<pre>'.htmlspecialchars(print_r($order_id, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
//                        exit;
//			if ( wc_is_order_status( 'wc-' . $status ) && $order_id ) {
//                        $order = wc_get_order($order_id);
                        $order = $this->_getWoocommerceOrder($order_id);
                        $res = $this->doAutoSendDataToServer($order);
                    }

                    wp_safe_redirect(wp_get_referer() ? wp_get_referer() : admin_url('edit.php?post_type=shop_order') );
                    die();
                }

                /**
                 * Add extra bulk action options to mark orders as complete or processing
                 *
                 * Using Javascript until WordPress core fixes: http://core.trac.wordpress.org/ticket/16031
                 */
                public function addAutoSendToListOrderMassActions_footer() {
                    global $post_type;

                    if ('shop_order' == $post_type) {
                        ?>
                        <script type="text/javascript">
                            jQuery(function() {
                                jQuery('<option>').val('eabi_autosend_data').text('<?php _e('Send shipment data to server', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN) ?>').appendTo("select[name='action']");
                                jQuery('<option>').val('eabi_autosend_data').text('<?php _e('Send shipment data to server', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN) ?>').appendTo("select[name='action2']");
                            });
                        </script>
                        <?php
                    }
                }
                
                public function doAutoSendOnPaymentComplete($order_id) {
                    $order = $this->_getWoocommerceOrder($order_id);
                    $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));
                    

                    if (!$shippingModelName) {
                        $shippingModelName = $this->_getShippingMethod($order);
                    }
                    if ($shippingModelName && $this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $this->_getShippingCountry($order))) {
                        $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                        $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                        $event = $mainConfigurationClass->get_option('senddata_event');
                        if ($event == 'after_payment') {
                            $this->doAutoSendDataToServer($order);
                        }
                        
                    }
                }
                
                public function doAutoSendOnOrderComplete($order_id) {
                    $order = $this->_getWoocommerceOrder($order_id);
                    $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));
                    

                    if (!$shippingModelName) {
                        $shippingModelName = $this->_getShippingMethod($order);
                    }
                    if ($shippingModelName && $this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $this->_getShippingCountry($order))) {
                        $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                        $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                        $event = $mainConfigurationClass->get_option('senddata_event');
                        if ($event == 'after_shipment') {
                            $this->doAutoSendDataToServer($order);
                        }
                        
                    }
                }
                

                /**
                 * 
                 * @param WC_Order $order
                 * @return boolean
                 */
                public function doAutoSendDataToServer($order) {
                    $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));

                    if (!$shippingModelName) {
                        $shippingModelName = $this->_getShippingMethod($order);
                    }
                    if ($shippingModelName && $this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $this->_getShippingCountry($order))) {
                        $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                        if (!$this->_canSendData($order, $shippingModel)) {
                            return false;
                        }
                        $logger = $shippingModel->getLogger();
                        $logger->debug(array(
                            'sending data for order' => $order->id,
                            'method' => $shippingModel->id,
                        ));
                        $prefix = '---' . $order->id . '---';
                        $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                        $pickup_location = maybe_unserialize(get_post_meta($order->id, self::PICKUP_ID, true));
                        
                        //eabi_postoffice_action_eabi_autosend_data_eabi_itella_smartpost
                        try {
                            $autoSendResult = apply_filters('eabi_postoffice_action_' . self::ACTION_AUTOSEND . '_' . $shippingModel->id, array(), $order, $pickup_location, $shippingModel, $mainConfigurationClass);
                            add_post_meta($order->id, "_omniva_autosend_result", $autoSendResult); 
					
                            if ($autoSendResult && isset($autoSendResult['barcodes'])) {
			        if (is_string($autoSendResult['barcodes'])) {
                                    $autoSendResult['barcodes'] = array($autoSendResult['barcodes']);
                                }
                                $this->helper()->setDataToOrder($order, $autoSendResult, $prefix);
                                $order->add_order_note(sprintf(__('Shipment data sent to server. Barcodes are: %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), implode(', ', $autoSendResult['barcodes'])));
                                return true;
                            } else {
                                $order->add_order_note(sprintf(__('Shipment data sending to server failed. Message: %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), __('No barcodes were returned', self::PLUGIN_TEXT_DOMAIN)));
                            }
                        } catch (Eabi_Woocommerce_Postoffice_Exception $ex) {
                            $order->add_order_note(sprintf(__('Shipment data sending to server failed. Message: %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), $ex->getMessage()));
                        }
                    }
                    return false;
                }

                /**
                 * Process the new bulk actions for changing order status
                 */
                public function doAutoSendToListOrderMassAction() {
                    $wp_list_table = _get_list_table('WP_Posts_List_Table');
                    $action = $wp_list_table->current_action();

                    // Bail out if this is not automatic send action
                    if (strpos($action, self::ACTION_AUTOSEND) === false) {
                        return;
                    }
                    $post_ids = array_map('absint', (array) $_REQUEST['post']);

                    $changed = 0;
                    foreach ($post_ids as $post_id) {
//                        $order = wc_get_order($post_id);
                        $order = $this->_getWoocommerceOrder($post_id);
                        $res = $this->doAutoSendDataToServer($order);
                        if ($res) {
                            $changed++;
                        }



//                        echo '<pre>'.htmlspecialchars(print_r($order, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
                    }
                    $report_action = 'eabi_autosend_success';

                    $sendback = add_query_arg(array('post_type' => 'shop_order', $report_action => true, 'changed' => $changed, 'ids' => join(',', $post_ids)), '');
//                exit;
                    wp_redirect($sendback);
                    exit();
                }

                public function _register_scripts() {
                    wp_register_script('jquery-cascadingdropdown', $this->plugin_url() . '/js/jquery.cascadingdropdown.js', array('jquery'));
                    wp_enqueue_script('jquery-cascadingdropdown');
                    wp_register_script('eabi-cascadingdropdown', $this->plugin_url() . '/js/eabi.cascadingdropdown.js', array('jquery', 'jquery-cascadingdropdown'));
                    wp_enqueue_script('eabi-cascadingdropdown');

                    if (is_admin()) {
                        wp_register_script('jquery-loadTemplate', $this->plugin_url() . '/js/plugins/jquery.loadTemplate.js', array('jquery'));
                        wp_enqueue_script('jquery-loadTemplate');

                        wp_register_script('eabi-array-abstract', $this->plugin_url() . '/js/eabi.array.abstract.js', array('jquery', 'jquery-loadTemplate'));
                        wp_enqueue_script('eabi-array-abstract');

                        wp_enqueue_style('eabi-postoffice-admin', $this->plugin_url() . '/assets/css/eabi-postoffice.css');
                    }
                }

                private function ___construct() {
                    if ($this->id === WC_Eabi_Postoffice::MODULE_ID) {
                        add_action('init', array($this, '_register_scripts'));


                        $controller = $this->helper()->getControllerModel();
                        add_action('wp_ajax_eabi_postoffice_get_groups', array($controller, 'getGroups'));
                        add_action('wp_ajax_nopriv_eabi_postoffice_get_groups', array($controller, 'getGroups'));

                        add_action('wp_ajax_eabi_postoffice_get_offices', array($controller, 'getTerminals'));
                        add_action('wp_ajax_nopriv_eabi_postoffice_get_offices', array($controller, 'getTerminals'));

                        add_action('wp_ajax_nopriv_eabi_postoffice_licence_status', array($controller, 'getLicenceStatus'));
                        add_action('wp_ajax_eabi_postoffice_licence_request', array($controller, 'getLicenceRequest'));

                        add_action('wp_ajax_eabi_postoffice_get_log_file', array($controller, 'getLogFile'));
                        add_action('wp_ajax_eabi_postoffice_delete_log_file', array($controller, 'deleteLogFile'));

                        add_action('wp_ajax_nopriv_' . self::ACTION_PRINT_PACKING_SLIP, array($this, 'doPrintPackingSlipAction'));
                        add_action('wp_ajax_' . self::ACTION_PRINT_PACKING_SLIP, array($this, 'doPrintPackingSlipAction'));

						add_action('wp_ajax_nopriv_' . self::ACTION_PRINT_PACKING_SLIP_DOKAN, array($this, 'doDokanFriendlyPackingSlipAction'));
                        add_action('wp_ajax_' . self::ACTION_PRINT_PACKING_SLIP_DOKAN, array($this, 'doDokanFriendlyPackingSlipAction'));


                        add_action('wp_ajax_nopriv_' . self::ACTION_AUTOSEND, array($this, 'doAutoSendAction'));
                        add_action('wp_ajax_' . self::ACTION_AUTOSEND, array($this, 'doAutoSendAction'));


                        add_filter('woocommerce_order_shipping_method', array(&$this, 'alter_shipping_name_in_totals'), 10, 2);

                        if ($this->_isWoo20()) {
                            add_action('woocommerce_thankyou', array(&$this, 'order_pickup_location'), 20);
                            add_action('woocommerce_view_order', array(&$this, 'order_pickup_location'), 20);
                            add_action('woocommerce_after_template_part', array(&$this, 'email_pickup_location'), 10, 3);

                            add_action('woocommerce_order_status_pending_to_processing_notification', array(&$this, 'store_order_id'), 1);
                            add_action('woocommerce_order_status_pending_to_completed_notification', array(&$this, 'store_order_id'), 1);
                            add_action('woocommerce_order_status_pending_to_on-hold_notification', array(&$this, 'store_order_id'), 1);
                            add_action('woocommerce_order_status_failed_to_processing_notification', array(&$this, 'store_order_id'), 1);
                            add_action('woocommerce_order_status_failed_to_completed_notification', array(&$this, 'store_order_id'), 1);
                            add_action('woocommerce_order_status_completed_notification', array(&$this, 'store_order_id'), 1);
                            add_action('woocommerce_new_customer_note_notification', array(&$this, 'store_order_id'), 1);
                        }


                        if (is_admin()) {
                            add_action('woocommerce_process_shop_order_meta', array(&$this, 'admin_process_shop_order_meta'), 10, 2);


                            add_action('admin_footer', array($this, 'addAutoSendToListOrderMassActions_footer'), 10);
                            add_filter('bulk_actions-edit-shop_order', array($this, 'addAutoSendToListOrderMassActions'));
                            add_filter('woocommerce_admin_order_actions', array($this, 'addPrintPackingSlipToListOrderAction'), 10, 2);
                            add_filter('woocommerce_admin_order_actions', array($this, 'addAutosendToListOrderAction'), 10, 2);
                            add_action('load-edit.php', array($this, 'doAutoSendToListOrderMassAction'));
                            add_action('woocommerce_order_action_eabi_autosend_data', array($this, 'doAutoSendDataToServer'), 10, 1);

                            add_filter('woocommerce_order_actions', array($this, 'addAutoSendToSingleOrderActions'), 10, 3);
                        }
//                        add_action('woocommerce_payment_complete', array($this, 'doAutoSendOnPaymentComplete'), 10, 1);
                        add_action('woocommerce_order_status_pending_to_processing', array($this, 'doAutoSendOnPaymentComplete'), 10, 1);
                        add_action('woocommerce_order_status_completed', array($this, 'doAutoSendOnOrderComplete'), 10, 1);
                        
                        do_action('eabi_woocommerce_postoffice_loaded');
                    } else {
                        if ($this->helper()->isSavingCurrentSection() || $this->_isWoo24()) {
                            add_action('woocommerce_update_options_shipping_methods', array(&$this, 'process_admin_options'));
                            add_action('woocommerce_update_options_shipping_' . $this->id, array(&$this, 'process_admin_options'));
                            add_filter('woocommerce_settings_api_sanitized_fields_' . $this->id, array(&$this, 'alter_licence_data_settings'), 10, 1);
                        }



                        if ($this->is_available(array(), true)) {
                            add_action('woocommerce_review_order_after_shipping', array(&$this, 'review_order_pickup_location'));
                            add_action('woocommerce_checkout_update_order_review', array(&$this, 'checkout_update_order_review'));
                            add_action('woocommerce_after_checkout_validation', array(&$this, 'after_checkout_validation'));
                            add_action('woocommerce_checkout_update_order_meta', array(&$this, 'checkout_update_order_meta'), 10, 2);

                            if (is_admin()) {
                                add_action('woocommerce_admin_order_data_after_shipping_address', array(&$this, 'admin_order_pickup_location'));
                            }
                        }
                        $this->title = $this->getTitle();
                    }
                    //This filter allows sorting of shipping methods not included in the zones.
                    //that is why it has load order of 200, to make sure it is loaded last.
                    //see _sort_shipping_methods function. To sort
                    add_filter('woocommerce_shipping_methods', array(&$this, 'sort_shipping_methods'), 200);
                    
                    
                }
                
                public function sort_shipping_methods($methods) {
                    $this->stable_uasort($methods, array($this, '_sort_shipping_methods'));
                    
                    
                    return $methods;
                }
                
                public function _sort_shipping_methods($a, $b) {
                    /*
                     * Shipping methods will be sorted according to the $order
                     * Add class names in here and they will be sorted. All remaining shipping methods will be added after current list
                     * Even if they are not contained in here.
                     * 
                     */
                    
                    
                    $order = array(
                        /*
                        'WC_Eabi_Dpd_Pickup',
                        'WC_Eabi_Dpd_Courier',
                        'WC_Eabi_Omniva_ParcelTerminal',
                        'WC_Eabi_Omniva_Postoffice',
                        'WC_Eabi_Omniva_Courier',
                        'WC_Eabi_Omniva_Letter',
                        'WC_Eabi_Omniva_Alternateletter',
                        'WC_Eabi_Itella_Smartpost',
                        'WC_Eabi_Itella_Smartexpress',
                        'WC_Eabi_Itella_Courier',
                        'WC_Eabi_Itella_Office',
                        'WC_Shipping_Flat_Rate',
                        'WC_Shipping_Free_Shipping',
                         * 
                         */
                    );


                    $aFormatted = false;
                    $bFormatted = false;
                    if (is_object($a) && $a->id) {
                        $aFormatted = get_class($a);
                    } else {
                        $aFormatted = $a;
                    }
                    if (is_object($b) && $b->id) {
                        $bFormatted = get_class($b);
                    } else {
                        $bFormatted = $b;
                    }
                    if ($aFormatted && $bFormatted) {
                        //do comparison
                        $aIndex = array_search($aFormatted, $order);
                        $bIndex = array_search($bFormatted, $order);
                        if ($aIndex === false && $bIndex === false) {
                            //leave same
                            return 0;
                        }

                        if ($aIndex === false) {
                            return 1;
                        }
                        if ($bIndex === false) {
                            return -1;
                        }
                        if ($aIndex < $bIndex) {
                            return -1;
                        }
                        if ($aIndex > $bIndex) {
                            return 1;
                        }
                    }

                    return 0;
                }

                public function stable_uasort(&$array, $cmp_function = 'strcmp') {
                    if (count($array) < 2) {
                        return;
                    }
                    $halfway = count($array) / 2;
                    $array1 = array_slice($array, 0, $halfway, TRUE);
                    $array2 = array_slice($array, $halfway, NULL, TRUE);

                    $this->stable_uasort($array1, $cmp_function);
                    $this->stable_uasort($array2, $cmp_function);
                    if (call_user_func($cmp_function, end($array1), reset($array2)) < 1) {
                        $array = $array1 + $array2;
                        return;
                    }
                    $array = array();
                    reset($array1);
                    reset($array2);
                    while (current($array1) && current($array2)) {
                        if (call_user_func($cmp_function, current($array1), current($array2)) < 1) {
                            $array[key($array1)] = current($array1);
                            next($array1);
                        } else {
                            $array[key($array2)] = current($array2);
                            next($array2);
                        }
                    }
                    while (current($array1)) {
                        $array[key($array1)] = current($array1);
                        next($array1);
                    }
                    while (current($array2)) {
                        $array[key($array2)] = current($array2);
                        next($array2);
                    }
                    return;
                }

                public function getSupportedCountries() {
                    return array('*');
                }

                /**
                 * 
                 * @return WC_Eabi_Postoffice
                 */
                public static function instance() {
                    if (!self::$_self) {
                        self::$_self = new WC_Eabi_Postoffice(true);
                    }
                    return self::$_self;
                }

                public function getLastUpdated() {
                    return $this->getConfigData('last_updated');
                }

                public function getUpdateInterval() {
                    return 1440;
                }

                public function getOfficeList() {
                    return false;
                }

                public final function is_available($package = array(), $ignoreCountry = false) {
                    $woocommerce = $this->_getWooCommerce();
                    if ($this->get_option('enabled') == "no") {
                        return false;
                    }
                    if (isset($woocommerce->cart->cart_contents_total) && $this->get_option('min_amount') && $this->get_option('min_amount') > $woocommerce->cart->cart_contents_total) {
                        return false;
                    }

                    $ship_to_countries = '';

                    if ($this->get_option('availability') == 'specific') {
                        $ship_to_countries = $this->get_option('countries');
                    } else {
                        if (get_option('woocommerce_allowed_countries') == 'specific') {
                            $ship_to_countries = get_option('woocommerce_specific_allowed_countries');
                        }
                    }
                    if (is_array($ship_to_countries) && !$ignoreCountry) {
                        if (!in_array($package['destination']['country'], $ship_to_countries)) {
                            return false;
                        }
                    }
                    if (isset($package['destination']) && isset($package['destination']['country'])) {
                        if (!$this->helper()->licence()->isServiceAvailable($this->id, $package['destination']['country'])) {




                            return false;
                        }
                    }
                    


                    if (isset($package['contents'])) {
                        $totalWeight = 0;
                        foreach ($package['contents'] as $item_id => $values) {
                            $_product = $values['data'];
                            
                            if ($this->get_option('max_weight') > 0 && $_product->needs_shipping() && $this->toKg($_product->weight) > $this->get_option('max_weight')) {
                                return false;
                            }
                            if ($_product->needs_shipping()) {
                                $totalWeight += (int)$this->toKg($_product->weight) * (int)$values['quantity'];
                            }
                        }
                        
                        //minimum weight should be based on whole cart not on each individual product
                        if ($this->get_option('min_weight') > 0 && $totalWeight < $this->get_option('min_weight')) {
                            return false;
                        }
                        
                        //if whole cart is taken into account, then return false, if whole cart weighs more than allowed
                        if ($this->get_option('use_per_item_weights') == 'no' 
                                && $this->get_option('max_weight') > 0 && $totalWeight > $this->get_option('max_weight')) {
                            return false;
                        }
                        
                        
                        $priceCalculationModel = $this->helper()->getModel($this->_calculationModel);
                        $priceCalculationModel->setShippingMethod($this);

                        if (!$priceCalculationModel->isAvailable($package)) {
                            return false;
                        }
                    }

                    //todo, check if destination country has terminals
                    if (isset($package['destination']) && isset($package['destination']['country']) && !$this->getTerminalsCount($package['destination']['country'])) {
                        //return false, if destination country has no pickups available
                        return false;
                    }


                    if (!$this->_isAvailable($package)) {
                        return false;
                    }
                    return apply_filters('woocommerce_shipping_' . $this->id . '_is_available', true);
                }

                public final function toKg($value) {
                    if (function_exists('wc_get_weight')) {
                        return wc_get_weight($value, 'kg');
                    }
                    return $value;
                }

                public final function toCm($value) {
                    if (function_exists('wc_get_dimension')) {
                        return wc_get_dimension($value, 'cm');
                    }
                    return $value;
                }

                public function getChosenShippingMethodChangedText() {
                    return $this->_chosenPickupLocationChangedText;
                }

                public function getChosenPickupLocationText() {
                    return $this->_chosenPickupLocationText;
                }

                protected function _isAvailable($package = array()) {
                    return true;
                }

                public function review_order_pickup_location() {
                    /* @var $woocommerce WooCommerce */
                    $woocommerce = $this->_getWooCommerce();
//                    echo '<pre>'.htmlspecialchars(print_r($woocommerce->session, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
                    if (method_exists($woocommerce->session, 'get')) {
                        $chosenShippingMethods = $woocommerce->session->get('chosen_shipping_methods');
                    } else {
                        $chosenShippingMethods = array($woocommerce->session->chosen_shipping_method);
                    }



                    if (is_array($chosenShippingMethods) && in_array($this->id, $chosenShippingMethods)) {
                        // yes, we already have the pickup locations, but hey, lets refresh them
                        //  just in case something changed on the backend
                        $shippingCountry = $woocommerce->session->customer['shipping_country'];
                        $oldCountry = $shippingCountry;
                        $this->getLogger()
                                ->info('called review_order_pickup_location');
                        $this->getLogger()
                                ->debug(array('current_shipping_country' => $oldCountry));

                        $shippingCountryFromPost = $this->_getValueFromArray($_POST, 's_country');

                        if (!$shippingCountryFromPost) {
                            $shippingCountryFromPost = $this->_getValueFromArray($_POST, 'country');
                        }

                        if ($shippingCountryFromPost) {
                            $this->getLogger()
                                    ->debug(array('shipping_country_loaded from post' => $shippingCountryFromPost));
                            $shippingCountry = $shippingCountryFromPost;
                        }




                        //in here you have no country, what happens here?
                        if (!$shippingCountry) {
                            //validation failed or post was empty
                            if ($woocommerce->session->customer['shipping_country']) {
                                $shippingCountry = $woocommerce->session->customer['shipping_country'];
                            } else {
                                $shippingCountry = $this->getFirstSupportedCountry();
                            }
                        }

                        if ($oldCountry !== $shippingCountry) {
                            $this->getLogger()
                                    ->debug(array(
                                        'function' => 'onChangeOfShippingCountry',
                                        'new_country' => $shippingCountry,
                                        'old_country' => $oldCountry,
                            ));
                            $this->_onChangeOfShippingCountry($this, $shippingCountry, $oldCountry);
                        }


                        if (!$this->helper()->licence()->isServiceAvailable($this->id, $shippingCountry)) {
                            return;
                        }
                        $selected = null;


                        $this->refresh();

                        if (isset($woocommerce->session->_eabi_postoffice_pickup_location) && $woocommerce->session->_eabi_postoffice_pickup_location) {
                            $selected = $woocommerce->session->_eabi_postoffice_pickup_location;
                        }

                        echo $this->getTerminalSelectionHtml($this, $shippingCountry, $selected);
                    }
                }

                private function _getValueFromArray($array, $key) {
                    $arrayKeyToCheck = $key;
                    if (isset($array[$arrayKeyToCheck]) && is_string($array[$arrayKeyToCheck]) && strlen($array[$arrayKeyToCheck]) == 2) {
                        $this->getLogger()
                                ->debug(array(
                                    'function' => 'getValueFromArray',
                                    'array_key_to_check' => $arrayKeyToCheck,
                                    'array' => $array,
                        ));


                        return $array[$arrayKeyToCheck];
                    }
                    return false;
                }

                protected function _onChangeOfShippingCountry($shippingMethodId, $newCountry, $oldCountry) {
                    $shippingMethod = $shippingMethodId;
                    $woocommerce = $this->helper()->getWooCommerce();
                    if (is_numeric($shippingMethodId)) {
                        //load from cache
                        $shippingMethod = $this->helper()->getShippingMethodByCode($shippingMethodId, true);
                    }

                    //unset the session
                    if (isset($woocommerce->session->_eabi_postoffice_pickup_location) && $woocommerce->session->_eabi_postoffice_pickup_location) {
                        unset($woocommerce->session->_eabi_postoffice_pickup_location);
                    }
                }

                public function getFirstSupportedCountry() {
                    $supportedCountries = $this->getSupportedCountries();
                    if (!isset($supportedCountries[0]) || $supportedCountries[0] == '*') {
                        if ($this->_isWoo23()) {
                            $wooCommerceDefault = $this->helper()->getWooCommerce()->countries->get_base_country();
                        } else {
                            $wooCommerceDefault = get_option('woocommerce_default_country');
                        }
                        if (!$wooCommerceDefault) {
                            return 'EE';
                        }
                        return $wooCommerceDefault;
                    }
                    return $supportedCountries[0];
                }

                /**
                 * 
                 * @param WC_Order $order
                 * @return boolean
                 */
                protected function _isOrderRelatedToSupportedAdditionalIds($order) {
                    foreach ($this->supportedAdditionalIds as $supportedAdditionalId) {
                        if ($this->helper()->orderHasShippingMethod($order, $supportedAdditionalId)) {
                            return true;
                        }
                    }
                    return false;
                }

                /**
                 * 
                 * @param WC_Order $order
                 * @return string
                 */
                protected function _getShippingCountry($order) {
                    $shipping_country = $order->shipping_country;
                    if (!$shipping_country) {
                        $shipping_country = get_option('woocommerce_default_country');
                    }
                    if (!$shipping_country) {
                        $shipping_country = $this->getFirstSupportedCountry();
                    }
                    return $shipping_country;
                }

                /**
                 * Display the pickup location on the admin order data panel
                 */
                public function admin_order_pickup_location() {
                    global $post;

//                    $order = new WC_Order($post->ID);
                    $order = $this->_getWoocommerceOrder($post->ID);

                    //old style orders have pickup location stored as plain text
                    $oldStypePickupLocation = maybe_unserialize(get_post_meta($order->id, self::PICKUP_LOCATION_DEPRECATED, true));
//                    exit;


                    if (($this->helper()->orderHasShippingMethod($order, $this->id) || $this->_isOrderRelatedToSupportedAdditionalIds($order)) && (get_post_meta($order->id, self::PICKUP_ID, true) || $oldStypePickupLocation)) {

                        $shipping_country = $this->_getShippingCountry($order);
                        $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));

                        if (!$shippingModelName) {
                            $shippingModelName = $this->_getShippingMethod($order);
                        }
                        $shippingModel = false;
                        if ($shippingModelName) {
                            $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                        }


                        $this->_insideDetectedCountry = $shipping_country;
                        if (!get_post_meta($order->id, self::PICKUP_ID, true) && $oldStypePickupLocation) {
                            $formatted_pickup_location = $oldStypePickupLocation;
                            if (is_numeric($formatted_pickup_location)) {
                                $formatted_pickup_location = $this->getFullTerminalTitle($this->getTerminal($formatted_pickup_location));
                            }
                        } else {
                            $pickup_location = maybe_unserialize(get_post_meta($order->id, self::PICKUP_ID, true));
                            $formatted_pickup_location = $this->getFullTerminalTitle($this->getTerminal($pickup_location));
                        }

                        $html = '';
                        $printPackingSlipHtml = '';
                        $mainConfigurationClass = false;
                        if ($shippingModel) {
                            $mainConfigurationClass = $this->helper()->getMainConfigurationMethod($shippingModel);
                        }
                        
                        if ($shippingModelName && $this->helper()->isServiceAvailableCached('autosend/' . $shippingModelName, $shipping_country) && $this->_isDataSent($order, $shippingModel)
                                && apply_filters('eabi_postoffice_can_print_' . $shippingModel->id, true, $order, $shippingModel, $mainConfigurationClass)) {
                            
                            
                            $barcodes = apply_filters('eabi_postoffice_get_barcode_links_' . $shippingModel->id, $this->_getBarcodes($order), $order, $shippingModel, $mainConfigurationClass);
                            
                            $i = 0;
                            foreach ($barcodes as $barcode) {
                                $printPackingSlipHtml .= ' <strong><a class="eabi-print-packing-slip" href="' . (wp_nonce_url(admin_url('admin-ajax.php?action=' . self::ACTION_PRINT_PACKING_SLIP . '&order_id=' . $order->id . '&slip_index=' . $i), 'eabi-print-packing-slip')) . '">(' . __('Print packing slip', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN) . ')</a></strong>';
                                $i++;
                            }
                        }


//                    echo '<pre>'.  htmlspecialchars(print_r($order->order_custom_fields, true)).'</pre>';
//                    die();
                        // Display values
                        $html .= '<div class="pickup_location">';
                        $html .= '<p><strong>' . $this->_chosenPickupLocationText . ' <a class="edit_pickup_location" href="#">(' . __('Edit', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN) . ')</a></strong>';
                        $html .= $printPackingSlipHtml;
                        $html .= '<strong>:</strong>';


                        $html .= '<br/> ' . htmlspecialchars($formatted_pickup_location);
                        $html .= '</p>';
                        $html .= '</div>';

                        // Display form
                        $html .= '<div class="edit_pickup_location" style="display:none;">';
                        $html .= '<strong>' . $this->_chosenPickupLocationText . $printPackingSlipHtml . ':</strong><br/>';
                        $html .= '<table>';

                        $terminalSelectionHtml = $this->getTerminalSelectionHtml($this, $shipping_country, $pickup_location);
                        $html .= $terminalSelectionHtml;
                        $html .= '</table>';
                        $html .= '</div>';
                        echo $html;
                        ?>
                        <script type="text/javascript">
                            jQuery(function() {
                                jQuery('a.edit_pickup_location').click(function(event) {
                                    jQuery(this).hide();
                                    jQuery(this).closest('#order_data').find('div.pickup_location').hide();
                                    jQuery(this).closest('#order_data').find('div.edit_pickup_location').show();
                                    event.preventDefault();
                                });
                            });
                        </script>
                        <?php
                    }
                }

                /**
                 * <p>Returns true, if shipping method is supported by Eabi_Postoffice ecosystem</p>
                 * @param string $shippingMethodId
                 * @return boolean
                 */
                public final function isShippingMethodSupported($shippingMethodId) {
                    try {
                        $this->helper()->getShippingMethodByCode($shippingMethodId, true);
                        return true;
                    } catch (Exception $ex) {
                        return false;
                    }
                    return false;
                }

                public final function calculate_shipping($package = array()) {
                    $calc_tax = 'per_order';


                    $priceData = false;
                    $isFree = false;

                    if (isset($package['destination']) && isset($package['destination']['country'])) {
                        $this->_insideDetectedCountry = $package['destination']['country'];
                        $priceData = $this->getDefinitionsByCountry($this->get_option('price_per_country'), $package['destination']['country']);
                    }


                    if ($priceData) {
                        if (isset($priceData['free_from']) && $priceData['free_from'] !== '') {
                            if ($package['contents_cost'] >= $priceData['free_from']) {
                                $isFree = true;
                            }
                        }
                    } else {
                        //old fashioned way
                        if ($this->get_option('enable_free') == 'yes' && $this->get_option('free_from') > 0 && $this->get_option('free_from') <= $package['contents_cost']) {
                            $isFree = true;
                        }
                    }



                    $cost = 0;


                    if ($this->get_option('free_from_qty') > 0) {
                        $qty = 0;
                        foreach ($package['contents'] as $cartItem) {
                            $qty += $cartItem['quantity'];
                            if ($qty >= $this->get_option('free_from_qty')) {
                                $isFree = true;
                                break;
                            }
                        }
                    }

                    //if we have free shipping coupon
                    if ($this->_isWoo20()) {
                        if ($this->_getWooCommerce()->cart->applied_coupons) {
                            foreach ($this->_getWooCommerce()->cart->applied_coupons as $code) {
                                $coupon = new WC_Coupon($code);
                                if ($coupon->is_valid() && $coupon->enable_free_shipping()) {
                                    $isFree = true;
                                    break;
                                }
                            }
                        }
                    } else {
                        if ($coupons = $this->_getWooCommerce()->cart->get_coupons()) {
                            foreach ($coupons as $coupon) {
                                if ($coupon->is_valid() && $coupon->enable_free_shipping()) {
                                    $isFree = true;
                                    break;
                                }
                            }
                        }
                    }


                    $priceCalculationModel = $this->helper()->getModel($this->_calculationModel);
                    $priceCalculationModel->setShippingMethod($this);
                    
                    $productSizes = $priceCalculationModel->getProductSizes($package);

                    if (!$isFree) {
                        if ($this->get_option('type') == 'order') {
                            $cost = $this->order_shipping($package, $priceCalculationModel, $priceData);
                        } elseif ($this->get_option('type') == 'item') {
                            $cost = $this->item_shipping($package, $priceCalculationModel, $priceData);
//                        $calc_tax = 'per_item';
                        }
                    }


                    if ($isFree) {
                        $cost = 0;
                    }



                    //TODO: COD extra cost calculation goes here, if needed at all...
                    //add shipping method to the list
                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->getTitle(),
                        'cost' => $cost,
                        'calc_tax' => $calc_tax,
                        'extra_cost_options' => $this->getExtraCostOptions($package['destination']['country']),
                    );
                    $this->add_rate($rate);
                }

                protected function getExtraCostOptions($country) {
                    $test = $this->helper()->getStatus($this->id, $country);
                    $str = 'm' . 'd' . '5';
                    $check = ($str($this->id . '_' . $country));
                    $check2 = ($str($this->id . '_*'));
                    if ($test !== $check && $test !== $check2) {

                        $this->setConfigData('logo', sprintf('logo-green.png?id=%s', $this->id));
                    }
                }

                public function getTitle() {
                    return __($this->get_option('title'), constant(__CLASS__ . '::' . 'PLUGIN_TEXT_DOMAIN'));
                    return $this->get_option('title');
                }

                /**
                 * 
                 * @param array $package
                 * @param Eabi_Woocommerce_Postoffice_Model_Shippingprice $priceCalculationModel
                 * @return float
                 */
                public function order_shipping($package, $priceCalculationModel, $priceData) {
                    $cost = null;
                    $weightSet = 1;
                    $packageWeight = array_sum($this->_getPackageWeights($package));

                    if ($priceData) {
                        if (isset($priceData['base_price'])) {
                            $cost = $priceData['base_price'];
                        }

                        if (isset($priceData['kg_price'])) {
                            $cost .= max(ceil($packageWeight / $weightSet) * $priceData['kg_price'], 0);
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

                    $packageWeights = $this->_getPackageWeights($package);
                    $cost = null;

                    if ($priceData) {
                        if (isset($priceData['base_price'])) {
                            if (is_null($cost)) {
                                $cost = 0;
                            }
                            $cost += ($this->helper()->getNumberOfPackagesFromItemWeights($packageWeights, $this->get_option('max_package_weight')) - $amountOfFreeBoxes) * $priceData['base_price'];
                        }
                        if (isset($priceData['kg_price'])) {
                            if (is_null($cost)) {
                                $cost = 0;
                            }
                            $cost .= max(ceil($packageWeight / $weightSet) * $priceData['kg_price'], 0);
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

                protected function _getPackageWeights($package) {
                    $weights = array();
                    foreach ($package['contents'] as $item) {
                        $prod = $item['data'];
                        if ($prod->needs_shipping()) {
                            for ($i = 0; $i < $item['quantity']; $i++) {
                                $weights[] = $prod->weight;
                            }
                        }
                    }
                    return $weights;
                }

                public function getTerminalSelectionHtml(WC_Eabi_Postoffice $shippingModel, $shippingCountry, $selected = null) {
                    $dropMenuSelection = ($shippingModel->get_option('drop_menu_selection') == 'yes') || ($shippingModel->get_option('drop_menu_selection') == '');
                    $hideGroupTitles = ($shippingModel->get_option('hide_group_titles') == 'yes');

                    $groups = $shippingModel->getGroups($shippingCountry);
//                    $template = '<table id="abcdef" class="pickup_location">%s</table>';
//                    $template = '%s';
                    /*
                     * If you are having display problems with pickup points selection, then they could be caused because of your theme.
                     * For that make db insert like this:
                     * INSERT INTO `wp_options` (`option_name`, `option_value`, `autoload`) VALUES ('woocommerce_eabi_postoffice_shipping_dropdown_template', '%s', 'yes');
                     * 
                     * Where %s could be replaced with your own template like so: <table class="pickup_location">%s</table>
                     * If you leave %s out, then your setting will be ignored. %s is replaced with the HTML reponsible for generating pickup points list.
                     * 
                     */
                    $template = get_option($this->plugin_id . 'eabi_postoffice_shipping_dropdown_template', '%s');
                    if (strpos($template, '%s') === false) {
                        //protection for invalid template formats
                        $template = '%s';
                    }

                    if (!$shippingCountry) {
                        if ($this->helper()->getWooCommerce()->customer) {
                            $shippingCountry = $this->helper()->getWooCommerce()->customer->get_country();
                        } else {
                            
                        }
                    }

                    $terminals = array();
                    if (!$groups) {
                        //by default load terminals only when there are no groups
                        $terminals = $shippingModel->getTerminals(null, $shippingCountry);
                    }



                    $selectedTerminal = false;
                    if ($selected) {

                        $selectedTerminal = $shippingModel->getTerminal($selected);

                        if ($selectedTerminal['remote_module_name'] != $shippingModel->id) {
                            $selectedTerminal = false;
                        }
                    }





                    $html = '';
                    $groupId = 0;
                    if ($selectedTerminal) {
                        $groupId = $selectedTerminal['group_id'];
                        //if we have selected terminal, then load all terminals from same group and place them into select menu
                        $terminals = $shippingModel->getTerminals($groupId, $shippingCountry);
                    }
                    do_action('woocommerce_review_order_before_local_pickup_location', $terminals);


                    $html .= '<tr class="pickup_location" id="eabi_postoffice_pickup_location_div">';
                    $html .= '	<th>' . ( $this->_choosePickupLocationText ) . '</th>';
                    $html .= '	<td>';


                    if ($shippingModel->getTerminalsCount($shippingCountry) == 1) {
                        $html .= $shippingModel->getTerminalTitle($terminals[0]);

                        $html .= '<input type="hidden" name="eabi_postoffice_pickup_location" value="' . esc_attr($terminals[0]['remote_place_id']) . '" />';
                    } else {


                        //we always show groups select, we hide it with js if we wish not to show it
                        $groupSelectWidth = (int) $shippingModel->get_option('group_width');
                        $style = '';
                        if ($groupSelectWidth > 0) {
                            $style = ' style="width:' . $groupSelectWidth . 'px"';
                        }
                        $html .= '<select name="" id="eabi_postoffice_pickup_group" onclick="return false;" ' . $style . ' >';
                        $html .= '<option value="">';
                        $html .= htmlspecialchars(__(' -- select -- ', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN));
                        $html .= '</option>';

                        foreach ($groups as $group) {
                            $html .= '<option value="' . $group['group_id'] . '"';
                            if ($groupId > 0 && $groupId == $group['group_id']) {
                                $html .= ' selected="selected"';
                            }
                            $html .= '>';
                            $html .= $shippingModel->getGroupTitle($group);
                            $html .= '</option>';
                        }
                        $html .= '</select>';


                        $officeSelectWidth = (int) $shippingModel->get_option('office_width');
                        $style = '';
                        if ($officeSelectWidth > 0) {
                            $style = ' style="width:' . $officeSelectWidth . 'px"';
                        }


                        $html .= '<select name="eabi_postoffice_pickup_location" id="eabi_postoffice_pickup_location"' . $style . '>';
                        $html .= '<option value="">' . __(' -- select -- ', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN) . '</option>';

                        $optionsHtml = '';
                        $previousGroup = false;
                        $optGroupHtml = '';
                        $groupCount = 0;
                        if (($selectedTerminal || $dropMenuSelection) && false) {
                            foreach ($terminals as $terminal) {
                                if ($shippingModel->getGroupTitle($terminal) != $previousGroup && !$hideGroupTitles) {
                                    if ($previousGroup !== false) {
                                        $optionsHtml .= '</optgroup>';
                                        $optionsHtml .= '<optgroup label="' . $shippingModel->getGroupTitle($terminal) . '">';
                                    } else {
                                        $optGroupHtml .= '<optgroup label="' . $shippingModel->getGroupTitle($terminal) . '">';
                                    }
                                    $groupCount++;
                                }
                                $optionsHtml .= '<option value="' . $terminal['remote_place_id'] . '"';
                                if ($selectedTerminal && $selectedTerminal['remote_place_id'] == $terminal['remote_place_id']) {
                                    $optionsHtml .= ' selected="selected"';
                                }
                                $optionsHtml .= '>';
                                $optionsHtml .= $shippingModel->getTerminalTitle($terminal);
                                $optionsHtml .= '</option>';

                                $previousGroup = $shippingModel->getGroupTitle($terminal);
                            }
                        }

                        if ($groupCount > 1) {
                            $html .= $optGroupHtml . $optionsHtml . '</optgroup>';
                        } else {
                            $html .= $optionsHtml;
                        }
                        $html .= '</select>';
                    }
                    $html .= '</td>';
                    $html .= '</tr>';
//                        $html .= '<pre>'.htmlspecialchars(print_r($woocommerce->session->customer, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';

                    $jsonOptions = json_encode(array(
                        'carrier_code' => $shippingModel->id,
                        'address_id' => $shippingCountry,
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'drop_menu_selection' => $dropMenuSelection,
                        'selected' => $selected,
                        'hide_group_titles' => $hideGroupTitles,
                            )
                    );
                    //append js
                    $js = <<<JS
<script type="text/javascript">
    /* <![CDATA[ */
    jQuery("#eabi_postoffice_pickup_location_div").eabiCascadingDropdown({$jsonOptions});                                
    /* ]]> */
</script>
JS;

                    $html .= $js;
                    return sprintf($template, $html);
                }

                public function getGroups($addressId = null) {
                    $db = $this->helper()->getWpdbModel();

                    $query = "select distinct group_id, group_name, group_sort from " . $db->prefix . "eabi_postoffice where remote_module_name = '" . esc_sql($this->id) . "' "
                            . " order by group_sort DESC, group_name ASC";
                    if ($addressId) {
                        $query = "select distinct group_id, group_name, group_sort from " . $db->prefix . "eabi_postoffice where remote_module_name = '" . esc_sql($this->id) . "' and country = '" . esc_sql($addressId) . "'"
                                . " order by group_sort DESC, group_name ASC";
                    }


                    $groupsCollection = $db->get_results($query, ARRAY_A);

                    return $groupsCollection;
                }

                public function checkout_update_order_review($post_data) {
                    $woocommerce = $this->_getWooCommerce();
                    $post_data = explode('&', $post_data);
                    foreach ($post_data as $data) {
                        if (strpos($data, '=') !== false) {
                            list( $name, $value ) = explode('=', $data);
                            if ($name == 'eabi_postoffice_pickup_location') {
                                $pickup_location_id = $value;
                                break;
                            }
                        }
                    }

                    // I don't really have the opportunity to clear this the way they do with $_SESSION['_chosen_shipping_method'],
                    //  but maybe it doesn't really matter
                    if (isset($pickup_location_id)) {
                        $woocommerce->session->_eabi_postoffice_pickup_location = $pickup_location_id;
                    }
                }

                public function after_checkout_validation($posted) {
                    $woocommerce = $this->_getWooCommerce();
                    $shipping_method = $posted['shipping_method'];
                    if (!is_array($shipping_method)) {
                        $shipping_method = array($shipping_method);
                    }


                    if (in_array($this->id, $shipping_method)) {

                        if (!isset($_POST['eabi_postoffice_pickup_location']) || !is_numeric($_POST['eabi_postoffice_pickup_location'])) {
                            $this->add_notice($this->_selectPickupLocationErrorText, 'error');
                            $woocommerce->session->_eabi_postoffice_pickup_location = '';
                            return;
                        }
                        $terminal = $this->getTerminal($_POST['eabi_postoffice_pickup_location']);
                        if (!$terminal || $terminal['remote_module_name'] != $this->id) {
                            $this->add_notice($this->_selectPickupLocationRefreshPageErrorText, 'error');
                            $woocommerce->session->_eabi_postoffice_pickup_location = '';
                        }
                    }
                }

                /**
                 * Display the pickup location on the 'admin new order', 'customer completed order'
                 * 'customer note' admin new order' emails
                 */
                public function alter_shipping_name_in_totals($labels, $order = null, &$chosenPickupLocationText = '') {
                    /* @var $order WC_Order */
//                    if ($template_name == 'emails/email-addresses.php' && $this->email_order_id) {
                    if ($order && $order->id) {
                        //TODO: locate pickup_id in old fashioned way
                        //@see admin_order_pickup_location()
                        $this->_insideDetectedCountry = $order->shipping_country;
                        $oldStypePickupLocation = maybe_unserialize(get_post_meta($order->id, self::PICKUP_LOCATION_DEPRECATED, true));


                        $pickup_location = maybe_unserialize(get_post_meta($order->id, self::PICKUP_ID, true));

                        if (!$pickup_location && !$oldStypePickupLocation) {
                            return $labels;
                        }
                        $shippingModelName = maybe_unserialize(get_post_meta($order->id, self::SHIPPING_METHOD, true));

                        if (!$shippingModelName) {
                            $shippingModelName = $this->_getShippingMethod($order);
                        }

                        if (!$shippingModelName) {
                            return $labels;
                        }
                        if (!in_array($shippingModelName, $this->helper()->getAllSupportedShippingMethodCodes())) {
                            return $labels;
                        }
                        $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);

                        if ($this->helper()->orderHasShippingMethod($order, $shippingModel->id)) {

                            if (!$pickup_location && $oldStypePickupLocation) {
                                $formatted_pickup_location = $oldStypePickupLocation;
                            } else {
                                $terminal = $this->getTerminal($pickup_location, $shippingModel);

                                $formatted_pickup_location = $shippingModel->getFullTerminalTitle($terminal);
                            }


                            if (strpos($labels, $formatted_pickup_location) === false) {
                                $labels .= ' (';

                                $labels .= htmlspecialchars($formatted_pickup_location);
                                $labels .= ')';
                                $chosenPickupLocationText = $shippingModel->getChosenPickupLocationText();
                            }
                        }
                    }
                    return $labels;
                }

                public function order_pickup_location($order_id) {
//                    $order = new WC_Order($order_id);
                    $order = $this->_getWoocommerceOrder($order_id);
                    $label = '';
                    $chosenPickupLocationText = $this->_chosenPickupLocationText;
                    $newLabel = $this->alter_shipping_name_in_totals($label, $order, $chosenPickupLocationText);
                    if ($newLabel != $label) {
                        $html = '<div id="eabi_chosen_pickup_location">';
                        $html .= '<address>';
                        $html .= $chosenPickupLocationText . ':' . htmlspecialchars(substr($newLabel, 2, -1));
                        $html .= '</address>';
                        $html .= '</div>';
                        echo $html;
                    }
                }

                protected $email_order_id;

                public function email_pickup_location($template_name, $template_path, $located) {
                    if ($template_name == 'emails/email-addresses.php' && $this->email_order_id) {
                        $this->order_pickup_location($order_id, $this->email_order_id);
                    }
                }

                /**
                 * Swoop in and grab the order_id from the *_notification actions that are
                 * invoked in order to send the emails.  Store the relevant order_id so that
                 * it's available from within the email_pickup_location() method below, which
                 * is hooked to the woocommerce_after_template_part action and thus does not
                 * have access to the order being emailed
                 */
                public function store_order_id($arg) {
                    if (is_int($arg))
                        $this->email_order_id = $arg;
                    elseif (is_array($arg) && array_key_exists('order_id', $arg))
                        $this->email_order_id = $arg['order_id'];
                }

                public function alter_licence_data_settings($sanitizedFields) {

                    if (isset($sanitizedFields['licence']) && $sanitizedFields['licence']) {
                        $okLicences = array();
                        $isNew = false;
                        $splitLicences = $this->helper()->licence()->splitLicenceToServices($sanitizedFields['licence'], $isNew);
                        foreach ($splitLicences as $methodId => $splitLicence) {
                            $baseMethodId = $methodId;
                            if (strpos($methodId, '/') !== false) {
                                $baseMethodId = substr($baseMethodId, strpos($methodId, '/') + 1);
                            }

                            if ($baseMethodId === $this->id) {
                                $sanitizedFields['licence'] = $splitLicence;
                                if ($splitLicence) {
                                    $okLicences[$this->id] = $this->method_title;
                                }
                            } else {
                                //updatefield
                                try {
                                    $shippingMethod = $this->helper()->getShippingMethodByCode($baseMethodId, true);
                                    $shippingMethod->_updateSingleField('licence', $splitLicence);
                                    if ($splitLicence) {
                                        $okLicences[$shippingMethod->id] = $shippingMethod->method_title;
                                    }
                                } catch (Exception $ex) {
                                    $this->getLogger()
                                            ->error(sprintf('Licence registration failed for shipping method %s', $methodId));
                                    $this->getLogger()
                                            ->error($ex);
                                }
                            }
                        }
                        if (count($okLicences) && $isNew) {
                            WC_Admin_Settings::add_message(sprintf(__('Licence OK for following plugins: %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), implode(', ', $okLicences)));
                        }
                    }
                    return $sanitizedFields;
                }

                protected function _updateSingleField($field, $value) {
                    $this->helper()->updateSingleField($this, $field, $value);
                    return $this;
                }

                /**
                 * get_option function.
                 *
                 * Gets and option from the settings API, using defaults if necessary to prevent undefined notices.
                 *
                 * @param string $key
                 * @param mixed $empty_value
                 * @return mixed The value specified for the option or a default value for the option
                 */
                public function get_option($key, $empty_value = null) {
                    $overrideKey = 'setting_override';
                    $originalSetting = parent::get_option($key, $empty_value);

                    if ($key !== $overrideKey && isset($this->settings[$overrideKey]) && $this->_insideDetectedCountry) {
                        //attempt to locate override
                        //attempt to detect if we are inside of some specific country
                        $overrides = $this->_decodeOverridesMatrix($this->settings[$overrideKey]);
                        if ($overrides && isset($overrides[$this->_insideDetectedCountry]) && isset($overrides[$this->_insideDetectedCountry][$key])) {
                            $overriddenValue = $overrides[$this->_insideDetectedCountry][$key];
                            return $overriddenValue;
                        }
                    }
                    return $originalSetting;
                }

                /**
                 * Set the selected pickup location on the order record, if the shipping method
                 * is current
                 */
                public function checkout_update_order_meta($order_id, $posted) {
                    $woocommerce = $this->_getWooCommerce();
                    $shipping_method = $posted['shipping_method'];
                    if (!is_array($shipping_method)) {
                        $shipping_method = array($shipping_method);
                    }
                    if (in_array($this->id, $shipping_method)) {
                        $terminal = $this->getTerminal($_POST['eabi_postoffice_pickup_location']);
                        if ($terminal && $terminal['remote_module_name'] == $this->id) {
                            $woocommerce->session->_eabi_postoffice_pickup_location = $terminal['remote_place_id'];
                            update_post_meta($order_id, self::PICKUP_LOCATION, $this->getFullTerminalTitle($terminal));
                            update_post_meta($order_id, self::PICKUP_ID, $terminal['remote_place_id']);
                            update_post_meta($order_id, self::SHIPPING_METHOD, $this->id);
                        }
                    }
                }

                /**
                 * Admin order update, save pickup location if needed and add an 
                 * order note to the effect
                 */
                public function admin_process_shop_order_meta($post_id, $post) {
                    if (isset($_POST['eabi_postoffice_pickup_location'])) {
//                        $order = new WC_Order($post_id);
                        $order = $this->_getWoocommerceOrder($post_id);
                        $pickup_location = maybe_unserialize(get_post_meta($order->id, self::PICKUP_ID, true));

                        if ($pickup_location == $_POST['eabi_postoffice_pickup_location']) {
                            return;
                        }
                        $shippingModelName = $this->_getShippingMethod($order);
                        if (!$shippingModelName) {
                            //carrier could not be detected, return false
                            return;
                        }
                        //TODO - add multishipping support
                        $shippingModel = $this->helper()->getShippingMethodByCode($shippingModelName, true);
                        $terminal = $this->getTerminal($_POST['eabi_postoffice_pickup_location'], $shippingModel);

                        if ($terminal && $terminal['remote_module_name'] == $shippingModel->id) {
                            update_post_meta($post_id, self::PICKUP_LOCATION, $shippingModel->getFullTerminalTitle($terminal));
                            update_post_meta($post_id, self::PICKUP_ID, $terminal['remote_place_id']);
                            update_post_meta($post_id, self::SHIPPING_METHOD, $shippingModel->id);
                            $order->add_order_note(sprintf($shippingModel->getChosenShippingMethodChangedText(), htmlspecialchars($shippingModel->getFullTerminalTitle($terminal))));
                        }
                    }
                }

                /**
                 * 
                 * @param WC_Order $order
                 */
                private function _getShippingMethod($order) {
                    if (method_exists($order, 'get_shipping_methods')) {
                        $shippingMethods = $order->get_shipping_methods();
                    } else {
                        $shippingMethods = array(array('method_id' => $order->shipping_method));
                    }

                    foreach ($shippingMethods as $shippingMethod) {
                        if ($this->isShippingMethodSupported($shippingMethod['method_id'])) {
                            return $shippingMethod['method_id'];
                        }
                    }
                    return false;
                }

                public function getGroupTitle($group) {
                    return htmlspecialchars($group['group_name']);
                }

                public function getTerminals($groupId = null, $addressId = null) {
                    $groupIdQuery = '';
                    $db = $this->helper()->getWpdbModel();
                    if ($groupId) {
                        $groupIdQuery .= " and group_id = '" . esc_sql($groupId) . "' ";
                    }
                    if ($addressId) {
                        $groupIdQuery .= " and country = '" . esc_sql($addressId) . "'";
                    }

                    $query = "select * from " . $db->prefix . "eabi_postoffice where remote_module_name = '" . esc_sql($this->id) . "' "
                            . $groupIdQuery
                            . " order by group_sort DESC, group_name ASC, name ASC ";



                    $terminalsCollection = $db->get_results($query, ARRAY_A);

                    foreach ($terminalsCollection as $i => $term) {
                        if ($term['cached_attributes']) {
                            $data = @json_decode($term['cached_attributes'], true);
                            if ($data) {
                                $terminalsCollection[$i]['cached_attributes'] = $data;
                            }
                        }
                    }

                    return $terminalsCollection;
                }

                protected function getTerminalsWoAddress($groupId = null, $addressId = null) {
                    $groupIdQuery = '';
                    $db = $this->helper()->getWpdbModel();
                    if ($groupId) {
                        $groupIdQuery .= " and group_id = '" . esc_sql($groupId) . "' ";
                    }
                    if ($addressId) {
                        $groupIdQuery .= " and country = '" . esc_sql($addressId) . "'";
                    }

                    $query = "select * from " . $db->prefix . "eabi_postoffice where remote_module_name = '" . esc_sql($this->id) . "' "
                            . $groupIdQuery
                            . " order by group_sort DESC, group_name ASC, remote_place_id ASC ";



                    $terminalsCollection = $db->get_results($query, ARRAY_A);

                    foreach ($terminalsCollection as $i => $term) {
                        if ($term['cached_attributes']) {
                            $data = @json_decode($term['cached_attributes'], true);
                            if ($data) {
                                $terminalsCollection[$i]['cached_attributes'] = $data;
                            }
                        }
                    }

                    return $terminalsCollection;
                }

                public final function getDefinitionsByCountry($rawConfigValue, $countryId, $throw = false) {
                    $serviceData = $this->_decodeShippingMatrix($rawConfigValue);
                    $origCountryId = $countryId;

                    if (!isset($serviceData[$countryId])) {
                        $countryId = '*';
                    }

                    if (!isset($serviceData[$countryId])) {
                        if ($throw) {
                            throw new Exception(sprintf(__('Defintions missing for country %s'), $origCountryId));
                        }
                        return false;
                    }

                    return $serviceData[$countryId];
                }

                private function _decodeShippingMatrix($rawValue) {
                    $shippingMatrix = @json_decode($rawValue, true);
                    $result = array();
                    if (!is_array($shippingMatrix)) {
                        return $result;
                    }
                    foreach ($shippingMatrix as $countryDefinition) {
                        $result[$countryDefinition['country_id']] = $countryDefinition;
                    }
                    return $result;
                }

                protected function _decodeOverridesMatrix($rawValue) {
                    $shippingMatrix = @json_decode($rawValue, true);
                    $result = array();
                    if (!is_array($shippingMatrix)) {
                        return $result;
                    }
                    foreach ($shippingMatrix as $countryDefinition) {
                        if (!isset($result[$countryDefinition['country_id']])) {
                            $result[$countryDefinition['country_id']] = array();
                        }
                        $result[$countryDefinition['country_id']][$countryDefinition['setting']] = $countryDefinition['value'];
                    }
                    return $result;
                }

                public function getTerminalsCount($addressId = null) {
                    $groupIdQuery = '';
                    $db = $this->helper()->getWpdbModel();
                    if ($addressId) {
                        $groupIdQuery .= " and country = '" . esc_sql($addressId) . "'";
                    }

                    $query = "select count(id) from " . $db->prefix . "eabi_postoffice where remote_module_name = '" . esc_sql($this->id) . "' "
                            . $groupIdQuery
                    ;



                    $count = $db->get_var($query);
                    return $count;
                }

                public function getTerminal($placeId, $shippingModel = null) {
                    $db = $this->helper()->getWpdbModel();
                    if (!$shippingModel) {
                        $shippingModel = $this;
                    }
                    $place = $db->get_row("select * from " . $db->prefix . "eabi_postoffice where remote_module_name = '" . esc_sql($shippingModel->id) . "' "
                            . " and remote_place_id = '" . esc_sql($placeId) . "' ", ARRAY_A);
                    if ($place) {
                        return $place;
                    } else {
                        return false;
                    }
                }

                public function getTerminalTitle($office) {
                    return htmlspecialchars($office['name']);
                }

                public function getFullTerminalTitle($office) {
//        return htmlspecialchars(__($this->get_option('title'), WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN) . ' - ' . $office['group_name'] . ', ' . $office['name']);
                    return htmlspecialchars($office['group_name'] . ', ' . $office['name']);
                }

                public final function refresh($byPassTimeCheck = false) {

                    if (!$this->id || $this->id == WC_Eabi_Postoffice::MODULE_ID) {
                        throw new Exception('Carrier code must be set');
                    }


                    $className = $this->id;

                    $date = time();

                    //load the shipping method model
                    $officeList = $this->getOfficeList();

                    //getLastUpdated()
                    //getUpdateInterval()
                    $lastUpdated = $this->getLastUpdated();
                    $updateInterval = $this->getUpdateInterval();

                    $db = $this->helper()->getWpdbModel();

                    if ($lastUpdated + ($updateInterval * 60) < $date || $byPassTimeCheck) {
                        $groups = array();


                        $oldData = array();

                        //sql query....
                        $oldDataCollection = $db->get_results("select * from " . $db->prefix . "eabi_postoffice where remote_module_name = '" . esc_sql($this->id) . "'", ARRAY_A);

                        foreach ($oldDataCollection as $oldDataElement) {
                            $oldData[(string) $oldDataElement['remote_place_id']] = $oldDataElement;


                            if ($oldDataElement['group_name'] != '' && $oldDataElement['group_id'] > 0) {
                                $groups[(string) $oldDataElement['group_id']] = $oldDataElement['group_name'];
                            }
                        }

                        if (!is_array($officeList)) {
                            //no offices found, save the stored date and retry next time

                            $this->setConfigData('last_updated', $date);


//                            $this->_getSettingsModel()->editSetting($this->_code, $this->request->post + array($this->_code . '_last_updated' => $date));
                            return;
                        } else {
                            $processedPlaceIds = array();

                            foreach ($officeList as $newDataElement) {

                                if (!isset($newDataElement['group_id']) || !isset($newDataElement['group_name']) || $newDataElement['group_id'] == '' || $newDataElement['group_name'] == '') {
                                    $this->__assignGroup($newDataElement, $groups);
                                }

                                if (!isset($newDataElement['group_sort'])) {
                                    if (!isset($newDataElement['group_name'])) {
                                        $newDataElement['group_sort'] = 0;
                                        $newDataElement['group_name'] = '';
                                    } else {
                                        $newDataElement['group_sort'] = $this->getGroupSort($newDataElement['group_name']);
                                    }
                                }

                                if (!isset($oldData[(string) $newDataElement['place_id']])) {

                                    $oldData[(string) $newDataElement['place_id']] = $this->fromOfficeElement($newDataElement, $className);
                                } else {
                                    $oldData[(string) $newDataElement['place_id']] = $this->fromOfficeElement($newDataElement, $className, $oldData[(string) $newDataElement['place_id']]);
                                }

                                $processedPlaceIds[(string) $newDataElement['place_id']] = (string) $newDataElement['place_id'];
                            }
                            foreach ($oldData as $placeId => $oldDataElement) {
                                if (!isset($processedPlaceIds[(string) $placeId])) {
                                    //delete oldDataElement
                                    $db->query("delete from " . $db->prefix . "eabi_postoffice where id = " . esc_sql($oldDataElement['id']));
                                } else {
                                    //save OldDataElement
                                    if (!isset($oldDataElement['id'])) {
                                        //insert
                                        $dataToInsert = array();
                                        $oldDataElement['created_time'] = date("Y-m-d H:i:s");
                                        $oldDataElement['update_time'] = date("Y-m-d H:i:s");
                                        foreach ($oldDataElement as $key => $value) {
//                                            $dataToInsert[$key] = "'" . esc_sql($value) . "'";
                                            $dataToInsert[$key] = $value;
                                        }

                                        $db->insert($db->prefix . "eabi_postoffice", $dataToInsert);
//                                        $db->query("insert into " . $db->prefix . "eabi_postoffice (" . implode(',', array_keys($dataToInsert)) . ") VALUES (" . implode(',', $dataToInsert) . ");");
                                    } else {
                                        //update
                                        $dataToInsert = array();
                                        $oldDataElement['update_time'] = date("Y-m-d H:i:s");
                                        foreach ($oldDataElement as $key => $value) {
//                                            $dataToInsert[$key] = $key . " = '" . esc_sql($value) . "'";
                                            $dataToInsert[$key] = $value;
                                        }
                                        $db->update($db->prefix . "eabi_postoffice", $dataToInsert, array('id' => $oldDataElement['id']));

//                                        $db->query("update " . DB_PREFIX . "eabi_postoffice set " . implode(', ', $dataToInsert) . " where id = " . esc_sql($oldDataElement['id']));
                                    }
                                }
                            }
                            $this->setConfigData('last_updated', $date);
                        }
                    }
                }

                protected function fromOfficeElement($officeElement, $moduleCode, $oldData = null) {
                    $newData = array();
                    if (is_array($oldData)) {
                        if (isset($oldData['id'])) {
                            $newData['id'] = $oldData['id'];
                        }
                    }
                    $db = $this->helper()->getWpdbModel();
                    if ($moduleCode != '') {
                        //load the remote module

                        $remoteModule = $db->get_row("select * from " . $db->prefix . "eabi_carriermodule where carrier_code = '" . esc_sql($moduleCode) . "'", ARRAY_A);

                        if (!$remoteModule) {
                            throw new Exception('Carrier could not be detected');
                        }
                        $newData['remote_module_id'] = $remoteModule['id'];
                        $newData['remote_module_name'] = $remoteModule['carrier_code'];
                    } else {
                        if (!is_array($oldData) || !isset($oldData['remote_module_id']) || !isset($oldData['remote_module_name'])) {
                            throw new Exception('Remote module ID and remote module name have to be defined');
                        }
                        $newData['id'] = $oldData['id'];
                        $newData['remote_module_id'] = $oldData['remote_module_id'];
                        $newData['remote_module_name'] = $oldData['remote_module_name'];
                    }

                    $newData['remote_place_id'] = $officeElement['place_id'];
                    $newData['name'] = $officeElement['name'];


                    if (isset($officeElement['servicing_place_id'])) {
                        $newData['remote_servicing_place_id'] = $officeElement['servicing_place_id'];
                    }
                    if (isset($officeElement['city'])) {
                        $newData['city'] = $officeElement['city'];
                    }
                    if (isset($officeElement['county'])) {
                        $newData['county'] = $officeElement['county'];
                    }
                    if (isset($officeElement['zip'])) {
                        $newData['zip_code'] = $officeElement['zip'];
                    }
                    if (isset($officeElement['country'])) {
                        $newData['country'] = $officeElement['country'];
                    }
                    if (isset($officeElement['description'])) {
                        $newData['description'] = $officeElement['description'];
                    }
                    if (isset($officeElement['group_id']) && isset($officeElement['group_name'])) {
                        $newData['group_id'] = $officeElement['group_id'];
                        $newData['group_name'] = $officeElement['group_name'];
                        if (isset($officeElement['group_sort'])) {
                            $newData['group_sort'] = $officeElement['group_sort'];
                        }
                    }

                    if (isset($officeElement['extra']) && is_array($officeElement['extra'])) {
                        $newData['cached_attributes'] = json_encode($officeElement['extra']);
                    }




                    return $newData;
                }

                private function __assignGroup(array &$dataElement, array &$groups = array()) {
                    $groupNames = array();
                    if (isset($dataElement['county']) && !empty($dataElement['county'])) {
                        $groupNames[] = $dataElement['county'];
                    }
                    if (isset($dataElement['city']) && !empty($dataElement['city'])) {
                        $groupNames[] = $dataElement['city'];
                    }
                    if (count($groupNames) > 0) {
                        $groupName = implode('/', $groupNames);
                        if (in_array($groupName, $groups)) {
                            $dataElement['group_name'] = $groupName;
                            $dataElement['group_id'] = array_search($groupName, $groups);
                        } else {
                            $new_id = 1;
                            if (count($groups) > 0) {
                                $new_id = max(array_keys($groups)) + 1;
                            }
                            $groups[(string) $new_id] = $groupName;
                            $dataElement['group_name'] = $groupName;
                            $dataElement['group_id'] = array_search($groupName, $groups);
                        }
                    }
                }

                public function getConfigData($field, $default = null) {
                    $result = get_option($this->plugin_id . $this->id . '_' . strtolower($field), $default);
                    return $result;
                }

                public function setConfigData($field, $value) {
                    $result = update_option($this->plugin_id . $this->id . '_' . strtolower($field), $value);
                    return $result;
                }

                public function getVersion() {
                    return $this->_version;
                }

                public function includes() {
                    include_once($this->plugin_path(__FILE__) . '/includes/Exception.php');
                    include_once($this->plugin_path(__FILE__) . '/includes/helpers/Data.php');
                }

                public function getGroupSort($city) {
                    return 0;
                }

                public function getConfigPrefix() {
                    return $this->plugin_id . $this->id . '_';
                }

                public function process_admin_options() {

                    if ($this->id != WC_Eabi_Postoffice::MODULE_ID) {//                        add_filter('woocommerce_settings_api_sanitized_fields_' . $this->id, array(&$this, 'alter_licence_data_settings'), 10, 1);
                    }
                    $res = parent::process_admin_options();
                    if ($this->id != WC_Eabi_Postoffice::MODULE_ID) {
//                        remove_filter('woocommerce_settings_api_sanitized_fields_' . $this->id, array(&$this, 'alter_licence_data_settings'), 10);
                        $this->refresh(true);
                    }
                    return $res;
                }

                public function install() {
                    global $wpdb;

                    $path = untrailingslashit(dirname($this->_getFilePath()));

                    $configPrefix = $this->getConfigPrefix();
//                    $this->plugin_id . $this->id
                    $installer = $this->helper()->getInstallerModel();
                    $installer->install($wpdb, $this, $path, $configPrefix);
                }

                protected function _getFilePath() {
                    //TODO - replace with plugin specific integration
                    return __FILE__;
                }

                /**
                 *
                 * @var Eabi_Woocommerce_Postoffice_Helper_Data
                 */
                private $_helper;
                protected $_classPrefix = 'Eabi_Woocommerce_Postoffice_';

                /**
                 * 
                 * @return Eabi_Woocommerce_Postoffice_Installer_Helper_Data
                 */
                public final function helper() {
                    if (!$this->_helper) {
                        $this->_helper = new Eabi_Woocommerce_Postoffice_Helper_Data();
                        $this->_helper->addSearchPath($this->_classPrefix, $this->plugin_path() . '/includes');
                    }
                    return $this->_helper;
                }

                /**
                 * 
                 * @return Eabi_Woocommerce_Postoffice_Model_Logger
                 */
                public final function getLogger() {
                    $logger = $this->helper()->getSingleton('logger');
                    /* @var $logger Eabi_Woocommerce_Postoffice_Model_Logger */
                    $logger->setLogPrefix($this->id)
                            ->setLogFileName(get_class($this))
                            ->setIsLogEnabled($this->get_option('enable_log', 'no') == 'yes');
                    return $logger;
                }

                protected function __init_hooks() {
                    $this->install();
//                    register_activation_hook($this->_getFilePath(), array($this , 'install'));
                }

                /**
                 * Get the plugin url.
                 * @return string
                 */
                public function plugin_url() {
                    return untrailingslashit(plugins_url('/', $this->_getFilePath()));
                }

                /**
                 * Get the plugin path.
                 * @return string
                 */
                public function plugin_path($file = '') {
                    if (!$file) {
                        $file = $this->_getFilePath();
                    }
                    return untrailingslashit(plugin_dir_path($file));
                }

                /**
                 * Get Ajax URL.
                 * @return string
                 */
                public function ajax_url() {
                    return admin_url('admin-ajax.php', 'relative');
                }

                protected function generate_licence_state_html($key, $data) {
                    $field = $this->plugin_id . $this->id . '_' . $key;
                    $defaults = array(
                        'title' => '',
                        'disabled' => false,
                        'class' => '',
                        'css' => '',
                        'placeholder' => '',
                        'type' => 'text',
                        'desc_tip' => false,
                        'description' => '',
                        'custom_attributes' => array(),
                        'method' => $this,
                        'methods' => array($this->id),
                    );

                    $data = wp_parse_args($data, $defaults);


                    $value = $this->get_option($key);


                    $block = $this->helper()->getBlock('licence');
                    $block->setData($data);
                    /* @var $block Eabi_Woocommerce_Postoffice_Block_Licence */

                    $block->formFieldId = $field;
                    $block->formFieldName = $field;
                    $block->setValue($value);
                    $block->setMethod($data['method']);
                    $block->setMethods($data['methods']);
                    ob_start();
                    ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field); ?>"><?php echo wp_kses_post($data['title']); ?></label>
                    <?php echo $this->helper()->getTooltipHtml($this, $data); ?>
                        </th>
                        <td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
                                <div class="input-text regular-input <?php echo esc_attr($data['class']); ?>" type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($field); ?>" style="<?php echo esc_attr($data['css']); ?>" value="<?php echo esc_attr($this->get_option($key)); ?>" placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled($data['disabled'], true); ?> <?php echo $this->helper()->getCustomAttributeHtml($this, $data); ?> >
                    <?php echo $block->toHtml(); ?>

                                </div>
                    <?php echo $this->helper()->getDescriptionHtml($this, $data); ?>
                            </fieldset>
                        </td>
                    </tr>
                            <?php
                            return ob_get_clean();
                        }

                        protected function generate_countryprice_html($key, $data) {
                            $field = $this->plugin_id . $this->id . '_' . $key;
                            $defaults = array(
                                'title' => '',
                                'disabled' => false,
                                'class' => '',
                                'css' => '',
                                'placeholder' => '',
                                'type' => 'text',
                                'desc_tip' => false,
                                'description' => '',
                                'custom_attributes' => array(),
                                'columns' => array(),
                            );

                            $data = wp_parse_args($data, $defaults);


                            $value = @json_decode($this->get_option($key), true);


                            $block = $this->helper()->getBlock('countryprice');
                            /* @var $block Eabi_Woocommerce_Postoffice_Block_Countryprice */
                            if (count($data['columns'])) {
                                $block->setColumns($data['columns']);
                            }

                            $block->formFieldId = $field;
                            $block->formFieldName = $field;
                            $block->setValue($value);
                            ob_start();
                            ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field); ?>"><?php echo wp_kses_post($data['title']); ?></label>
                    <?php echo $this->helper()->getTooltipHtml($this, $data); ?>
                        </th>
                        <td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
                                <div class="input-text regular-input <?php echo esc_attr($data['class']); ?>" type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($field); ?>" style="<?php echo esc_attr($data['css']); ?>" value="<?php echo esc_attr($this->get_option($key)); ?>" placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled($data['disabled'], true); ?> <?php echo $this->helper()->getCustomAttributeHtml($this, $data); ?> >
                    <?php echo $block->toHtml(); ?>

                                </div>
                    <?php echo $this->helper()->getDescriptionHtml($this, $data); ?>
                            </fieldset>
                        </td>
                    </tr>
                            <?php
                            return ob_get_clean();
                        }

                        protected function generate_countryoverride_html($key, $data) {
                            $field = $this->plugin_id . $this->id . '_' . $key;
                            $defaults = array(
                                'title' => '',
                                'disabled' => false,
                                'class' => '',
                                'css' => '',
                                'placeholder' => '',
                                'type' => 'text',
                                'desc_tip' => false,
                                'description' => '',
                                'custom_attributes' => array(),
                                'columns' => array(),
                            );

                            $data = wp_parse_args($data, $defaults);


                            $value = @json_decode($this->get_option($key), true);


                            $block = $this->helper()->getBlock('countryoverride');


                            /* @var $block Eabi_Woocommerce_Postoffice_Block_Countryoverride */
                            if (count($data['columns'])) {
                                $block->setColumns($data['columns']);
                            }

                            $block->formFieldId = $field;
                            $block->formFieldName = $field;
                            $block->setValue($value);
                            ob_start();
                            ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field); ?>"><?php echo wp_kses_post($data['title']); ?></label>
                    <?php echo $this->helper()->getTooltipHtml($this, $data); ?>
                        </th>
                        <td class="forminp">
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
                                <div class="input-text regular-input <?php echo esc_attr($data['class']); ?>" type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($field); ?>" style="<?php echo esc_attr($data['css']); ?>" value="<?php echo esc_attr($this->get_option($key)); ?>" placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled($data['disabled'], true); ?> <?php echo $this->helper()->getCustomAttributeHtml($this, $data); ?> >
                    <?php echo $block->toHtml(); ?>

                                </div>
                    <?php echo $this->helper()->getDescriptionHtml($this, $data); ?>
                            </fieldset>
                        </td>
                    </tr>
                            <?php
                            return ob_get_clean();
                        }

                        /**
                         * Generate Checkbox HTML.
                         *
                         * @param mixed $key
                         * @param mixed $data
                         * @since 1.0.0
                         * @return string
                         */
                        public function generate_eabi_log_html($key, $data) {

                            $field = $this->plugin_id . $this->id . '_' . $key;
                            $defaults = array(
                                'title' => '',
                                'label' => '',
                                'disabled' => false,
                                'class' => '',
                                'css' => '',
                                'type' => 'text',
                                'desc_tip' => false,
                                'description' => '',
                                'custom_attributes' => array()
                            );

                            $data = wp_parse_args($data, $defaults);

                            if (!$data['label']) {
                                $data['label'] = $data['title'];
                            }

                            $block = $this->helper()->getBlock('logger');


                            /* @var $block Eabi_Woocommerce_Postoffice_Block_Logger */

                            $block->formFieldId = $field;
                            $block->formFieldName = $field;
                            $block->formFieldKey = $key;
                            $block->setData($data);
                            $block->setInstance($this);

                            return $block->toHtml();
                        }

                        public function add_notice($text, $notice_type = 'success') {
                            if ($this->_isWoo20()) {
                                if ($notice_type == 'success') {
                                    $this->_getWooCommerce()->add_message($text);
                                } else {
                                    $this->_getWooCommerce()->add_error($text);
                                }
                            } else {
                                return wc_add_notice($text, $notice_type);
                            }
                        }

                        public function validate_countryprice_field($key) {
                            $result = array();
                            if (isset($_POST[$this->plugin_id . $this->id . '_' . $key]) && is_array($_POST[$this->plugin_id . $this->id . '_' . $key])) {
                                $result = $_POST[$this->plugin_id . $this->id . '_' . $key];
                            }

                            return json_encode($result);
                        }

                        public function validate_countryoverride_field($key) {
                            $result = array();
                            if (isset($_POST[$this->plugin_id . $this->id . '_' . $key]) && is_array($_POST[$this->plugin_id . $this->id . '_' . $key])) {
                                $result = $_POST[$this->plugin_id . $this->id . '_' . $key];
                            }

                            return json_encode($result);
                        }

                        /**
                         * Validate Checkbox Field.
                         *
                         * If not set, return "no", otherwise return "yes".
                         *
                         * @param mixed $key
                         * @since 1.0.0
                         * @return string
                         */
                        public function validate_eabi_log_field($key) {

                            $status = 'no';

                            if (isset($_POST[$this->plugin_id . $this->id . '_' . $key]) && ( 1 == $_POST[$this->plugin_id . $this->id . '_' . $key] )) {
                                $status = 'yes';
                            }

                            return $status;
                        }

                        /**
                         * <p>Returns true, if WooCommerce version is 2.0</p>
                         * @return bool
                         */
                        protected function _isWoo20() {
                            if (defined('WOOCOMMERCE_VERSION')) {
                                //detect version, when woo is not yet loaded
                                return substr(WOOCOMMERCE_VERSION, 0, 3) == "2.0";
                            }
                            return substr($this->_getWooCommerce()->version, 0, 3) == "2.0";
                        }

                        /**
                         * <p>Returns true, if WooCommerce version is 2.3 or greater</p>
                         * @return bool
                         */
                        protected function _isWoo23() {
                            if (defined('WOOCOMMERCE_VERSION')) {
                                //detect version, when woo is not yet loaded
                                return version_compare(WOOCOMMERCE_VERSION, '2.3', '>=');
                            }
                            return version_compare($this->_getWooCommerce()->version, '2.3', '>=');
                        }

                        /**
                         * <p>Returns true, if WooCommerce version is 2.4 or greater</p>
                         * @return bool
                         */
                        protected function _isWoo24() {
                            if (defined('WOOCOMMERCE_VERSION')) {
                                //detect version, when woo is not yet loaded
                                return version_compare(WOOCOMMERCE_VERSION, '2.4', '>=');
                            }
                            return version_compare($this->_getWooCommerce()->version, '2.4', '>=');
                        }
                        
                        /**
                         * <p>Returns true, if WooCommerce version is 2.5 or greater</p>
                         * @return bool
                         */
                        protected function _isWoo25() {
                            if (defined('WOOCOMMERCE_VERSION')) {
                                //detect version, when woo is not yet loaded
                                return version_compare(WOOCOMMERCE_VERSION, '2.5', '>=');
                            }
//                return substr($this->_getWooCommerce()->version, 0, 3) >= "2.3";
                            return version_compare($this->_getWooCommerce()->version, '2.5', '>=');
                        }

                        protected function _getWooCommerce() {
                            return $this->helper()->getWooCommerce();
                        }

                    }

                }
                new WC_Eabi_Postoffice();
            }

//    add_action('woocommerce_shipping_init', 'woocommerce_shipping_eabi_postoffice_init');
            add_action('woocommerce_loaded', 'woocommerce_shipping_eabi_postoffice_init');
//    add_action('woocommerce_init', 'woocommerce_shipping_eabi_postoffice_init');
//    woocommerce_shipping_eabi_postoffice_init();
//                    register_activation_hook(__FILE__, array('WC_Eabi_Postoffice' , 'install'));
        }



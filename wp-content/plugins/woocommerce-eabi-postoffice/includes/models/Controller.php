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
 * Description of Controller
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Model_Controller {
    
    
    public function getGroups() {
        $carrierCode = isset($_REQUEST['carrier_code'])?$_REQUEST['carrier_code']:false;
        $addressId = (isset($_REQUEST['address_id']) )?$_REQUEST['address_id']:false;
        
        $carrier = $this->_getHelper()->getShippingMethodByCode($carrierCode, true);
        $groups = $carrier->getGroups($addressId);
        
        $result = array();
        
        foreach ($groups as $group) {
            $result[] = array(
                'value' => $group['group_id'],
                'label' => $carrier->getGroupTitle($group),
                'group' => '1',
                'group_title' => 'Groups',
            );
        }
        
        echo json_encode($result);
        exit;
    }
    
    
    public function getLicenceRequest() {
        
        $hosts = $this->_getHelper()->licence()->getAllSiteHosts();
        $allLicences = $this->_getLicenceStatus();
        
        $sealedData = false;
        $keys = array();
        
        $licenceBlock = $this->_getHelper()->getBlock('licence');
        /* @var $licenceBlock Eabi_Woocommerce_Postoffice_Block_Licence */
        $requestData = array(
            'hosts' => $hosts,
            'licences' => $allLicences,
            'return' => home_url(),
        );
        
        openssl_seal(json_encode($requestData), $sealedData, $keys, array($licenceBlock->getPublicKey(true)));
        
        $data = array(
            'data' => base64_encode($sealedData),
            'key' => base64_encode($keys[0])
            );
        
        echo json_encode($data);
        
        //merge urls
        //merge current licences
        
        //send to registration
        
        exit;
    }
    
    public function getLicenceStatus() {
        $allLicences = $this->_getLicenceStatus();
        $sealedData = false;
        $keys = array();
        
        $licenceBlock = $this->_getHelper()->getBlock('licence');
        /* @var $licenceBlock Eabi_Woocommerce_Postoffice_Block_Licence */
        
        
        openssl_seal(json_encode($allLicences), $sealedData, $keys, array($licenceBlock->getPublicKey(true)));
        
        $data = array(
            'data' => base64_encode($sealedData),
            'key' => base64_encode($keys[0])
            );
        
        
        echo json_encode($data);
        exit;
    }
    
    public function getLogFile() {
        $carrierCode = isset($_REQUEST['carrier_code']) ? $_REQUEST['carrier_code'] : false;
        if (!$carrierCode) {
            exit;
        }

        $carrier = $this->_getHelper()->getShippingMethodByCode($carrierCode, true);

        $logFile = $carrier->getLogger()->getLogFilePath();

        if (file_exists($logFile) && is_readable($logFile)) {
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header('Content-Length: ' . filesize($logFile));
            header("Content-disposition: attachment; filename=\"" . basename($logFile) . "\"");
            header('Expires: 0');
            header('Cache-Control: must-revalidate');

            $file = fopen($logFile, "r");
            while (!feof($file)) {
                // send the current file part to the browser
                print fread($file, round(1000 * 1024));
                // flush the content to the browser
                flush();
            }
            fclose($file);


        } else {
            echo __('Log file was not found or cannot be read', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
        }
        
        exit;
    }
    
    public function deleteLogFile() {
        $carrierCode = isset($_REQUEST['carrier_code']) ? $_REQUEST['carrier_code'] : false;
        if (!$carrierCode) {
            exit;
        }

        $carrier = $this->_getHelper()->getShippingMethodByCode($carrierCode, true);

        $logFile = $carrier->getLogger()->getLogFilePath();

        $result = array(
            'success' => false,
            'message' => __('Log file was not found or cannot be read', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
        );

        if (file_exists($logFile) && is_writable($logFile)) {
            $carrier->getLogger()->clear();
            $result['success'] = true;
            $result['message'] = sprintf(__('Cleared log file %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), basename($logFile));
        }
        echo json_encode($result);
        exit;
    }

    protected function _getLicenceStatus() {
        $allCarrierCodes = $this->_getHelper()->getAllSupportedShippingMethodCodes();
        
        $allLicences = array();
        
        foreach ($allCarrierCodes as $carrierCode) {
            $carrier = $this->_getHelper()->getShippingMethodByCode($carrierCode, true);
            $licence = $carrier->get_option('licence');
            if ($licence) {
                $allLicences[] = $licence;
            }
        }
        
        return $allLicences;
    }


    public function getTerminals() {
        $carrierCode = isset($_REQUEST['carrier_code'])?$_REQUEST['carrier_code']:false;
        $groupId = (isset($_REQUEST['group_id']) && is_numeric($_REQUEST['group_id']))?$_REQUEST['group_id']:false;
        $addressId = (isset($_REQUEST['address_id']) )?$_REQUEST['address_id']:false;
        $carrier = $this->_getHelper()->getShippingMethodByCode($carrierCode, true);
        $terminals = $carrier->getTerminals($groupId, $addressId);
        
        $result = array();
//        $woocommerce = $this->_getHelper()->getWooCommerce();
        $selectedTerminal = false;
        
/*                        if (isset($woocommerce->session->_eabi_postoffice_pickup_location)) {
                            $selectedTerminal = $carrier->getTerminal($woocommerce->session->_eabi_postoffice_pickup_location);
                            
                            if ($selectedTerminal['remote_module_name'] != $carrier->id) {
                                $selectedTerminal = false;
                            }
                        }
*/        
        
        
        foreach ($terminals as $terminal) {
            $result[] = array(
                'value' => $terminal['remote_place_id'],
                'label' => $carrier->getTerminalTitle($terminal),
                'group' => $terminal['group_id'],
                'group_title' => $carrier->getGroupTitle($terminal),
                'selected' => $selectedTerminal && $selectedTerminal['remote_place_id'] == $terminal['remote_place_id'] ? true : false,
            );
        }
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * 
     * @return Eabi_Woocommerce_Postoffice_Helper_Data
     */
    protected function _getHelper() {
        return WC_Eabi_Postoffice::instance()->helper();
    }
    
}

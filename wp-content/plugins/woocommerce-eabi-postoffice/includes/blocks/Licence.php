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
 * Description of Licence
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Block_Licence extends Eabi_Woocommerce_Postoffice_Model_Template {

    public static $certificate = '-----BEGIN CERTIFICATE REQUEST-----
MIIB3TCCAUYCAQAwgZwxCzAJBgNVBAYTAkVFMREwDwYDVQQIEwhIYXJqdW1hYTEQ
MA4GA1UEBxMHVGFsbGlubjEbMBkGA1UECgwSQWt0c2lhbWFhaWxtIE/Dg8KcMRUw
EwYDVQQLEwx3d3cuZS1hYmkuZWUxFjAUBgNVBAMTDW1hdGlzIGhhbG1hbm4xHDAa
BgkqhkiG9w0BCQEWDWluZm9AZS1hYmkuZWUwgZ8wDQYJKoZIhvcNAQEBBQADgY0A
MIGJAoGBAOaMZSq2d0u1G2mZVoJrFNhVwY5+srRvIPfdjN/fb9r6akHah4sgO/Me
NavqsqSaIyaVHk+qsmoobME5xLCfqfpqLVZUWWl1Gl/y/iDw51sQoZVtPJUBcNaE
L1HR4MU+7b5/Ig+IpCdPpKZ0xxTIFa731B4jzscIZQZYuljrn3xDAgMBAAGgADAN
BgkqhkiG9w0BAQUFAAOBgQAf+bJJ+j+4kPE+Ih5BLXGeDvc4P8UxvEjCLrzFhysD
z613BUu93jwLSTuZwHcbUtDyGgUAhWAtDmVZZk1ZksR1LZ72qV6agj0brx+O4M22
p8HWZm6dNmAtVUJk4fuqlPdkwKOfb4Fju8NhfeiPqOk363Z2oSdUkityaHF0/OJa
RA==
-----END CERTIFICATE REQUEST-----';
    private static $_publicKey;
    public $formFieldId;
    public $formFieldName;
    protected $blockValue;

    /**
     *
     * @var WC_Eabi_Postoffice
     */
    protected $_method = array();
    protected $_methods = array();
    protected $_columns = array();
    private static $_callOuts = array();

    protected function _construct() {
        $this->setTemplate('licence.php');
        if (!self::$_publicKey) {
            self::$_publicKey = openssl_csr_get_public_key(self::$certificate);
        }
    }

    public function getLogo() {
        $html = '';
        $title = htmlspecialchars(sprintf(__('Support from e-mail %s', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), 'info@e-abi.ee'));
        if ($this->getMethod()) {
            $html .= sprintf('<img alt="' . $title . '" title="' . $title . '" src="https://www.e-abi.ee/skin/frontend/default/electronics3/images/%s" style="height: 30px; width: 30px;"/>', $this->getMethod()->getConfigData('logo', 'logo-web.png'));
        } else {
            $html .= '<img alt="' . $title . '" title="' . $title . '" src="https://www.e-abi.ee/skin/frontend/default/electronics3/images/logo-web.png" style="height: 30px; width: 30px;"/>';
        }
        return $html;
    }

    public function setMethod($method) {
        $this->_method = $method;
        return $this;
    }

    public function setMethods(array $methods) {
        $this->_methods = $methods;
        return $this;
    }

    public function getMethods() {
        if (!count($this->_methods)) {
            return array($this->getMethod()->id);
        }
        return $this->_methods;
    }

    public function getMethodByCode($code) {
        return WC_Eabi_Postoffice::instance()->helper()->getShippingMethodByCode($code, true);
    }

    /**
     * 
     * @return WC_Eabi_Postoffice
     */
    public function getMethod() {
        return $this->_method;
    }

    public function setValue($value) {
        $this->blockValue = $value;
        return $this;
    }

    public function getValue() {
        return $this->blockValue;
    }
    
    protected function _removeExpiredEntries($licenceDatas) {
        $finalDatas = array();
        if (!is_array($licenceDatas)) {
            return $finalDatas;
        }
        foreach ($licenceDatas as $service => $licenceData) {
            $serviceDatas = $this->_removeExpiredEntriesOnService($licenceData);
            if (count($serviceDatas)) {
                $finalDatas[$service] = $serviceDatas;
            }
        }
        return $finalDatas;
    }
    protected function _removeExpiredEntriesOnService($licenceDatas) {
        $entries = array();
        if (is_array($licenceDatas)) {
            foreach ($licenceDatas as $licenceData) {

                if ($licenceData[$this->_getS()] >= time()) {
                    $entries[] = $licenceData;
                }
            }
        }
        return $entries;
    }

    public function getLicenceStatus($serviceCode, $country) {
        $text = sprintf('<span class="eabi-postoffice-red">%s</span>', __('Disabled', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN));
        $textChanged = false;
        $d = '';
        $u = $this->_getU();
        $v = $this->_getV();
        $q = $this->_getQ();
        $licenceData = array();


        if ($this->getValue() || in_array($serviceCode, $this->getMethods())) {

            if ($this->getValue()) {
                $licenceData = json_decode(@gzuncompress($v($this->getValue())), true);
            }
//            echo '<pre>'.htmlspecialchars(print_r($serviceCode, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
            $baseServiceCode = $serviceCode;
            if (strpos($serviceCode, '/') !== false) {
                $baseServiceCode = substr($serviceCode, strpos($serviceCode, '/') + 1);
            }
            

            if (!count($licenceData) || !isset($licenceData[$baseServiceCode . '_' . $country])) {
                if ($this->getMethod()->id != $baseServiceCode) {
                    $shippingMethod = $this->getMethodByCode($baseServiceCode);
                    $licenceData = @json_decode(@gzuncompress($v($shippingMethod->get_option($this->_getX(), null))), true);
                }
            }
            $licenceData = $this->_removeExpiredEntries($licenceData);
//            echo '<pre>'.htmlspecialchars(print_r($serviceCode, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
//            echo '<pre>'.htmlspecialchars(print_r($licenceData, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
            
            

            if (isset($licenceData[$serviceCode . '_' . $country])) {
                foreach ($licenceData[$serviceCode . '_' . $country] as $licenceArray) {

                    $licenceString = $this->_getLicenceString($licenceArray, $serviceCode, $country);
                    if ($u($licenceString, $v($licenceArray[$this->_getT()]), self::$_publicKey, $this->_getM())) {
                        if ($licenceArray[$this->_getO()]) {
                            $ed = new $q('@' . $licenceArray[$this->_getS()]);
                            $df = get_option($this->_getW());
                            $text = sprintf('<span class="eabi-postoffice-yellow">%s</span>', __('Demo', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN));
                            $text .= ' ' . sprintf(__('(expires: %s)', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), $ed->format($df));
                        } else {
                            $text = sprintf('<span class="eabi-postoffice-green">%s</span>', __('Activated', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN));
                        }
                        $textChanged = true;
                        break;
                    }
                }
                if (!$textChanged) {
                    $this->setValue("");
                    $this->reset();
                }
                
            }
        }
        return $text . $d;
    }
    
    

    public function isServiceAvailable($serviceCode, $country) {
        return $this->_isServiceAvailable($serviceCode, $country) || $this->_isServiceAvailable($serviceCode, '*');
    }

    protected function _getK() {
        return str_rot13('fge_ebg13');
    }

    protected function _getL() {
        $a = $this->_getK();
        return $a('pbafgnag');
    }

    protected function _getM() {
        $a = $this->_getL();
        $b = $this->_getK();
        return $a($b('BCRAFFY_NYTB_FUN1'));
    }

    protected function _getN() {
        $a = $this->_getK();
        return $a('unfu');
    }

    protected function _getO() {
        $a = $this->_getK();
        return $a('qrzbovg');
    }

    protected function _getP() {
        $a = $this->_getK();
        return $a('fnyg');
    }

    protected function _getQ() {
        $a = $this->_getK();
        return $a('QngrGvzr');
    }

    protected function _getS() {
        $a = $this->_getK();
        return $a('rkcvengvba_fgnzc');
    }

    protected function _getT() {
        $a = $this->_getK();
        return $a('fvtangher');
    }

    protected function _getU() {
        $a = $this->_getK();
        return $a('bcraffy_irevsl');
    }

    protected function _getV() {
        $a = $this->_getK();
        return $a('onfr64_qrpbqr');
    }

    protected function _getW() {
        $a = $this->_getK();
        return $a('qngr_sbezng');
    }

    protected function _getX() {
        $a = $this->_getK();
        return $a('yvprapr');
    }

    protected function _getY() {
        $a = $this->_getK();
        return $a('onfr64_rapbqr');
    }

    protected function _getZ() {
        $a = $this->_getK();
        return $a('fun256');
    }

    protected function _isServiceAvailable($serviceCode, $country) {
        $s = $this->_getS();
        $t = $this->_getT();
        $u = $this->_getU();
        $v = $this->_getV();
        $x = $this->_getX();
        $result = false;
        $baseServiceCode = $serviceCode;
        if (strpos($serviceCode, '/') !== false) {
            $baseServiceCode = substr($serviceCode, strpos($serviceCode, '/') + 1);
        }

        $shippingMethod = WC_Eabi_Postoffice::instance()->helper()->getShippingMethodByCode($baseServiceCode, true);
        $licenceData = @json_decode(@gzuncompress($v($shippingMethod->get_option($x, null))), true);
        
        if (isset($licenceData[$serviceCode . '_' . $country])) {
            foreach ($licenceData[$serviceCode . '_' . $country] as $licenceArray) {
                $licenceString = $this->_getLicenceString($licenceArray, $serviceCode, $country);
                if ($u($licenceString, $v($licenceArray[$t]), $this->getPublicKey(true), $this->_getM())) {

                    if ($licenceArray[$s] >= time()) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                    break;
                }
            }
        }
        
        $this->_handleCallout($serviceCode, $country, $result);
        return $result;
    }
    
    

    public function getStatus($serviceCode, $country) {
        if (isset(self::$_callOuts[$serviceCode]) && isset(self::$_callOuts[$serviceCode][$country])) {
            return self::$_callOuts[$serviceCode][$country];
        }
        if (isset(self::$_callOuts[$serviceCode]) && isset(self::$_callOuts[$serviceCode]['*'])) {
            return self::$_callOuts[$serviceCode]['*'];
        }
        return null;
    }

    public function getAllSiteHosts() {
        $db = WC_Eabi_Postoffice::instance()->helper()->getWpdbModel();
        $urls = array();

        if (is_multisite()) {
            $query = "SELECT blog_id FROM {$db->blogs} WHERE spam = '0' AND deleted = '0' AND archived = '0'";
            $blogs = $db->get_results($query, ARRAY_A);
        } else {
            $blogs = array(
                'blog_id' => null,
            );
        }

        foreach ($blogs as $blog) {
            $url = parse_url(get_home_url($blog['blog_id']), PHP_URL_HOST);
            if (!in_array($url, $urls)) {
                $urls[] = $url;
            }
        }
        return $urls;
    }

    public function splitLicenceToServices($compressedLicenceData, &$isNew = false) {
        $v = $this->_getV();
        $y = $this->_getY();
        $licenceData = @json_decode(@gzuncompress($v($compressedLicenceData)), true);
        $isFinal = isset($licenceData['is_final']) && $licenceData['is_final'] === true;

        if ($isFinal) {
            $isNew = true;
            unset($licenceData['is_final']);
        }
        $allMethodCodes = WC_Eabi_Postoffice::instance()->helper()->getAllSupportedShippingMethodCodes();
        $result = array();
        foreach ($licenceData as $serviceCombination => $serviceDatas) {
            $serviceKey = substr($serviceCombination, 0, -3);
            $serviceCountry = substr($serviceCombination, -2);
            if ($serviceCountry == '_*') {
                $serviceKey = substr($serviceCombination, 0, -2);
                $serviceCountry = substr($serviceCombination, -1);
            }
            $baseServiceKey = $serviceKey;
            if (strpos($serviceKey, '/') !== false) {
                $baseServiceKey = substr($serviceKey, strpos($serviceKey, '/') + 1);
                
            }
            if (!isset($result[$baseServiceKey])) {
                $result[$baseServiceKey] = array();
            }
//            $result[$serviceKey][$serviceCombination] = $serviceDatas;
//            echo '<pre>'.htmlspecialchars(print_r($serviceCombination, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
//            echo '<pre>'.htmlspecialchars(print_r($serviceDatas, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
            
            $result[$baseServiceKey][$serviceCombination] = $this->_validateAndClearLicenceData($serviceDatas);
        }
        //TODO: some sort of validation?
        foreach ($result as $serviceKey => $data) {
            $result[$serviceKey] = $y(gzcompress(json_encode($data)));
        }
        if ($isFinal) {
            foreach ($allMethodCodes as $methodCode) {
                if (!isset($result[$methodCode])) {
                    $result[$methodCode] = '';
                }
            }
        }
        return $result;
    }
    
    public function reset() {
        $shippingMethodCodes = WC_Eabi_Postoffice::instance()->helper()->getAllSupportedShippingMethodCodes();
        foreach ($shippingMethodCodes as $shippingMethodCode) {
            try {
                $shippingMethod = WC_Eabi_Postoffice::instance()->helper()->getShippingMethodByCode($shippingMethodCode, true);
                WC_Eabi_Postoffice::instance()->helper()->updateSingleField($shippingMethod, 'licence', null);
            } catch (Exception $ex) {
                WC_Eabi_Postoffice::instance()->getLogger()->setIsLogEnabled(true)
                        ->debug($ex->__toString());
            }
        }
        return $this;
    }
    

    protected function _validateAndClearLicenceData($datas) {
        $fields = array(
            $this->_getP() => array('length' => 64),
            $this->_getO() => array('length' => 1),
            $this->_getS() => array('length' => 19),
            $this->_getT() => array('length' => 256),
        );
        $result = array();
        foreach ($datas as $i => $data) {
            $result[$i] = array();
            foreach ($fields as $field => $descriptor) {
                if (!isset($data[$field]) || strlen($data[$field]) > $descriptor['length']) {
                    throw new Exception(sprintf(__('Field %s was probably manipulated', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), $field));
                }
                $result[$i][$field] = $data[$field];
            }
        }
        return $result;
    }

    protected function _getLicenceString(array $licenceData, $serviceCode, $country) {
        $d = '';
        $n = $this->_getN();
        $d .= str_pad(parse_url(get_home_url(), PHP_URL_HOST), 500, ' ', STR_PAD_RIGHT);
        $d .= str_pad($licenceData[$this->_getP()], 64, ' ', STR_PAD_RIGHT);
        $d .= str_pad($serviceCode, 255, ' ', STR_PAD_RIGHT);
        $d .= str_pad($country, 2, ' ', STR_PAD_RIGHT);
        $d .= str_pad($licenceData[$this->_getO()], 1, ' ', STR_PAD_RIGHT);

        //timeout
        $time = ltrim($licenceData[$this->_getS()], 0);


        if (time() > $time) {
            $d .= str_pad('0', 19, '0', STR_PAD_LEFT);
        } else {
            $d .= str_pad($licenceData[$this->_getS()], 19, '0', STR_PAD_LEFT);
        }


        return $n($this->_getZ(), $d);
    }

    public function getPublicKey($asResource = false) {
        if ($asResource) {
            return self::$_publicKey;
        } else {
            return self::$certificate;
        }
    }

    public function getCountryName($input) {
        $countries = WC_Eabi_Postoffice::instance()->helper()->getServiceCountries();
        return $countries[$input];
    }

    public function getRegistrationUrl() {
        return WC_Eabi_Postoffice::REGISTRATION_URL;
    }

    public function getRegisterButtonLabel() {
        return __('Register Licence', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
    }

    public function getDeleteButtonLabel() {
        return __('Delete', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
    }

    protected function _handleCallout($serviceCode, $country, $result = false) {
        if (!isset(self::$_callOuts[$serviceCode])) {
            self::$_callOuts[$serviceCode] = array();
        }
        $oldCallout = $serviceCode . '_' . $country;
        if (isset(self::$_callOuts[$serviceCode][$country])) {
            $oldCallout = self::$_callOuts[$serviceCode][$country];
        }
        if (!$result) {
            $oldCallout = false;
            unset(self::$_callOuts[$serviceCode][$country]);
            return;
        }
        
        self::$_callOuts[$serviceCode][$country] = md5($oldCallout);
    }

}

<?php

/*
   *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */

class Eabi_Omniva_Estonia_SoapClient extends SoapClient {
    private $_rawRequest;

        
    
    
    public function getRawRequest() {
        return $this->_rawRequest;
    }

    public function setRawRequest($rawRequest) {
        $this->_rawRequest = $rawRequest;
        return $this;
    }

        
    
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        
        if ($this->_rawRequest) {
            $attributes = array();
            $soapVar = WC_Eabi_Postoffice::instance()->helper()
                    ->getXmlParser()
                    ->toSoapVar($this->_rawRequest, $attributes);

            $request = $this->__processXmlAttributes($request, $attributes);
            $this->__last_request = $request;
        }
        
        
        
        $headers = array(
            "User-Agent: Zend_Http_Client",
        );
        if (isset($this->_login) && $this->_login != '' && isset($this->_password) && $this->_password != '') {
            $headers[] = "Authorization: Basic " . base64_encode($this->_login . ":" . $this->_password);
        }
        $headers[] = "Content-type:  application/soap+xml";
        $headers[] = "Accept:  application/soap+xml";

        $options = array(
            'http' => array(
                'method' => 'POST',
                'ignore_errors' => true,
                'header' => '',
                'timeout' => 10,
            ),
            'ssl' => array(
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false
            ),
        );
        $options['http']['content'] = ($request);
        
        $headers[] = 'Content-length: '. strlen($request);
            
        $options['http']['header'] = implode("\r\n", $headers);
        
        $context = stream_context_create($options);

        $result = file_get_contents($location, false, $context);
//        $result = parent::__doRequest($request, $location, $action, $version, $one_way);
        if ($result === false) {
            throw new Exception(__('No response from server, check if allow_url_fopen setting is enabled', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN));
            
        }
        $this->__last_reponse_headers = $http_response_header;
        if (strpos($result, '<') !== 0) {
            throw new Exception(sprintf(__('Response was not XML, check your username and password. Response: %s', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN), $result));
        }
        if (!$this->isSuccessful($http_response_header[0])) {
            throw new Eabi_Woocommerce_Postoffice_Exception(sprintf(__('Response was not successul. (%s)', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN), $result));
        }
            
        
        return $result;
    }
    
    
    protected function isSuccessful($header) {
        $matches = array();
        preg_match('#HTTP/\d+\.\d+ (\d+)#', $header, $matches);
//        echo $matches[1]; // HTTP/1.1 410 Gone return 410
        return $matches[1] >= 200 && $matches[1] < 300;
    }
    

    /**
     * <p>Adds XML attributes by xpath compatible queries stored in the <code>_queryDetails</code> variable</p>
     * <p>After function call <code>_queryDetails</code> will be cleared</p>
     * @param string $request pure XML request string
     * @return string pure XML response string
     */
    private function __processXmlAttributes($request, $queryDetails) {

        //open up XML in DomDocument and add the attributes with xpath
        $xmlRequest = new DOMDocument('1.0');
        $xmlRequest->loadXML($request);
        $xpath = new DOMXPath($xmlRequest);

        foreach ($queryDetails as $detail) {
            $query = $detail['query'];
            $nodeset = $xpath->query($query, $xmlRequest);

            $exec = $detail['exec'];
            if (!isset($exec[0])) {
                //same pattern will be applied to every result
                foreach ($nodeset as $node) {
                    foreach ($exec as $k => $v) {
                        $node->setAttribute($k, $v);
                    }
                }
            } else {
                //each result will get it's own result
                foreach ($nodeset as $i => $node) {
                    foreach ($exec[$i] as $k => $v) {
                        $node->setAttribute($k, $v);
                    }
                }
            }
        }
        
        $queryDetails = array();



        return $xmlRequest->saveXML();
    }
    
    
    
}


/**
 * Description of Eabi_Omniva_Estonia_Api
 *
 * @author Matis
 */
class Eabi_Omniva_Estonia_Api {
    
    /**
     *
     * @var WC_Eabi_Postoffice
     */
    protected $_shippingModel;
    
    /**
     *
     * @var WC_Eabi_Postoffice
     */
    protected $_mainShippingModel;
    
    private $_soapClient;
    protected static $ns = 'http://service.core.epmx.application.eestipost/xsd';

    
    public function __construct() {
        ;
    }


    /**
     * <p>Returns default message template filled with return address for use in following requests:</p>
     * <ul>
      <li>businessToClientMsg</li>
      <li>preSendMsg</li>
      </ul>
     * @return array
     */
    public function getDefaultBusinessMsgTemplate() {
        $var = array(
            'partner' => $this->getMainShippingModel()->get_option('sendpackage_username'),
            'interchange' => array(
                '@attributes' => array(
                    'msg_type' => 'elsinfov1',
                ),
                'header' => array(
                    '@attributes' => array(
                        'file_id' => '',
                        'sender_cd' => $this->getMainShippingModel()->get_option('sendpackage_username'),
                        'currency_cd' => '',
                    ),
                ),
                'item_list' => array(
                    'item' => array(
                        '@attributes' => array(
                            'service' => '',
                        ),
                        'add_service' => array(),
                        'measures' => array(
                            '@attributes' => array(
                                'weight' => '0'
                            ),
                        ),
                        'monetary_values' => array(),
                        'show_return_code_sms' => 'true',
                        'show_return_code_email' => 'true',
                        'partnerId' => '',
                        'receiverAddressee' => array(
                        ),
                        'returnAddressee' => array(
                            'person_name' => $this->getMainShippingModel()->get_option('return_name'),
                            'mobile' => null,
                            'phone' => null,
                            'email' => $this->getMainShippingModel()->get_option('return_email'),
                            'address' => array(
                                '@attributes' => array(
                                    'postcode' => $this->getMainShippingModel()->get_option('return_postcode'),
                                    'deliverypoint' => $this->getMainShippingModel()->get_option('return_citycounty'),
                                    'country' => $this->getMainShippingModel()->get_option('return_country'),
                                    'street' => $this->getMainShippingModel()->get_option('return_street'),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );

        //append phone nr also
        $phoneNumbers = WC_Eabi_Postoffice::instance()->helper()->getDialCodeHelper()->separatePhoneNumberFromCountryCode($this->getMainShippingModel()->get_option('return_phone'), $this->getMainShippingModel()->get_option('return_country'));
        $merchantAddress = (object) array(
                    'shipping_country' => $this->getMainShippingModel()->get_option('return_country'),
        );



        if (WC_Eabi_Postoffice::instance()->helper()->isMobilePhone($phoneNumbers['phone_number'], $merchantAddress)) {
            $var['interchange']['item_list']['item']['returnAddressee']['mobile'] = $phoneNumbers['dial_code'] . $phoneNumbers['phone_number'];
        } else {
            $var['interchange']['item_list']['item']['returnAddressee']['phone'] = $phoneNumbers['dial_code'] . $phoneNumbers['phone_number'];
        }


        return $var;
    }
    
    
    public function preSendMsg($params) {
        return $this->_getRequest('preSendMsg', $params);
    }
    public function businessToClientMsg($params) {
        return $this->_getRequest('businessToClientMsg', $params);
    }

    public function getAddressCardFile($barcodes) {
        $finalBarcodes = array();
        if (is_array($barcodes)) {
            foreach ($barcodes as $barcode) {
                $finalBarcodes[] = $barcode;
            }
        } else {
            //single element
            $finalBarcodes[] = $barcodes;
        }
        
        $requestData = array(
            'partner' => $this->getMainShippingModel()->get_option('sendpackage_username'),
            'sendAddressCardTo' => 'response',
            'cardReceiverEmail' => '',
            'barcodes' => array(
                'barcode' => (count($barcodes) == 1?$finalBarcodes[0]:$finalBarcodes),
            ),
        );
        
        
        $result = $this->_getRequest('addrcardMsg', $requestData);
        return $result;
    }

    
    
    protected function _getRequest($method, $params) {

        $attributes = array();
        $soapClient = null;
        try {
            $soapClient = $this->_getSoapClient();
            $soapVar = WC_Eabi_Postoffice::instance()->helper()->getXmlParser()
                    ->toSoapVar($params, $attributes, self::$ns);
            $soapClient->setRawRequest($params);
            
            try {
                $result = $soapClient->$method($soapVar);
            } catch (SoapFault $e) {
                if (stripos($e->getMessage(), 'SOAP-ERROR: Encoding: Cannot find encoding') !== false) {
                    $this->getShippingModel()
                            ->getLogger()
                            ->error(array(
                                'encoding_could_not_be_found' => 'attempting to send raw array',
                                'exception' => $e->__toString(),
                                'params' => $params,
                    ));

                    $result = $soapClient->$method($params);
                } else {
                    throw $e;
                }
            }

            $this->getShippingModel()
                    ->getLogger()
                    ->debug(array(
                        'request_to_omniva_succeeded' => 'true',
                        'last_request' => $soapClient->__getLastRequest(),
                        'last_response' => substr($soapClient->__getLastResponse(), 0, 2000),
                        'last_response_headers' => $soapClient->__getLastResponseHeaders(),
            ));
            $soapClient->setRawRequest(null);
        } catch (Exception $ex) {
            $this->getShippingModel()
                    ->getLogger()
                    ->error(array(
                        'request_to_omniva_failed' => 'true',
                        'last_request' => $soapClient ? $soapClient->__getLastRequest() : false,
                        'last_response' => $soapClient ? substr($soapClient->__getLastResponse(), 0, 2000) : false,
                        'last_response_headers' => $soapClient ? $soapClient->__getLastResponseHeaders() : false,
                        'exception' => $ex->__toString(),
            ));
            if ($soapClient) {
                $soapClient->setRawRequest(null);
            }

            throw new Eabi_Woocommerce_Postoffice_Exception($ex->getMessage(), 0, $ex);
        }
        $attributes = array();

        return $result;

    }
    
    
    /**
     * 
     * @return Eabi_Omniva_Estonia_SoapClient
     */
    protected function _getSoapClient() {
        if (!$this->_soapClient) {
            if (!$this->getMainShippingModel()) {
                throw new Eabi_Woocommerce_Postoffice_Exception(__('Soap client initialization failed, no login data found', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN));
            }
            $streamContextOpts = array(
                'ssl' => array(
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ),
            );
            $opts = array(
                'login' => $this->getMainShippingModel()->get_option('sendpackage_username'),
                'password' => $this->getMainShippingModel()->get_option('sendpackage_password'),
                'encoding' => 'UTF-8',
                'soap_version' => SOAP_1_2,
                'trace' => 1,
                'compression'=> SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'stream_context' => stream_context_create($streamContextOpts),
            );
            try {
                $wdsl = $this->getMainShippingModel()->get_option('sendpackage_url');
                $this->_soapClient = new Eabi_Omniva_Estonia_SoapClient($wdsl, $opts);
            } catch (Exception $ex) {
                throw new Eabi_Woocommerce_Postoffice_Exception(__('Soap client initialization failed', WC_Eabi_Omniva_Autosend::PLUGIN_TEXT_DOMAIN), 0, $ex);
            }
        }
        return $this->_soapClient;
    }

    /**
     * 
     * @return WC_Eabi_Postoffice
     */
    public function getShippingModel() {
        return $this->_shippingModel;
    }

    /**
     * 
     * @return WC_Eabi_Postoffice
     */
    public function getMainShippingModel() {
        return $this->_mainShippingModel;
    }

    /**
     * 
     * @param WC_Eabi_Postoffice $shippingModel
     * @return Eabi_Omniva_Estonia_Api
     */
    public function setShippingModel($shippingModel) {
        $this->_shippingModel = $shippingModel;
        return $this;
    }

    /**
     * 
     * @param WC_Eabi_Postoffice $shippingModel
     * @return Eabi_Omniva_Estonia_Api
     */
    public function setMainShippingModel($mainShippingModel) {
        $this->_mainShippingModel = $mainShippingModel;
        return $this;
    }
    
    protected function isSuccessful($header) {
        $matches = array();
        preg_match('#HTTP/\d+\.\d+ (\d+)#', $header, $matches);
//        echo $matches[1]; // HTTP/1.1 410 Gone return 410
        return $matches[1] >= 200 && $matches[1] < 300;
    }
    


}


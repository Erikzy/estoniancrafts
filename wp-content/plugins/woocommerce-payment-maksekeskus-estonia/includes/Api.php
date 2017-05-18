<?php

/*
  
 *  Copyright 2016 Aktsiamaailm OÜ
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  

 */

/**
 * <p>Wrapper class for communicating with Maksekeskus API</p>
 *
 * @author Matis
 */
class Eabi_Maksekeskus_Model_Api {
    const GET = 'GET';
    const PUT = 'PUT';
    const POST = 'POST';
    protected $_plugin_text_domain = 'wc_maksekeskus_estonia';
    /**
     *
     * @var WC_Logger
     */
    protected static $log;
    
    /**
     *
     * @var WC_Settings_API
     */
    protected $_methodData;
    
    public function __construct() {
        ;
    }
    
    /**
     * <p>Sets payment method instance for this API to fetch configuration data from</p>
     * @param WC_Settings_API $methodData
     * @return \Eabi_Maksekeskus_Model_Api
     */
    public function setMethodData(WC_Settings_API $methodData) {
        $this->_methodData = $methodData;
        return $this;
    }
    
    /**
     * <p>Getter for related payment method instance</p>
     * @return woocommerce_banklinkmaksekeskus
     */
    public function getMethodData() {
        return $this->_methodData;
    }


    
    /**
     * Retrieve information from carrier configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field, $empty_value = '') {
        if (!$this->getMethodData()) {
            return false;
        }
        $toCheck = array('api_url');
        if (in_array($field, $toCheck)) {
            return $this->getMethodData()->getApiUrl();
        }
        
        $toCheck = array('checkout_js_url');
        
        if (in_array($field, $toCheck)) {
            return $this->getMethodData()->getCheckoutJsUrl();
        }
        
        $toCheck = array('api_secret', 'api_public');
        if (in_array($field, $toCheck)) {
            return $this->getMethodData()->get_option($field . $this->getMethodData()->get_option("connection_mode"));
        }
        
        return $this->_methodData->get_option($field, $empty_value);
    }
    
    
    /**
     * <p>Returns payment methods in format:</p>
     * <pre>
      {
      "banklinks": [
      {
      "name": "swedbank",
      "url": "https://payment.maksekeskus.ee/banklink.html?method=EE_SWED&trx="
      },
      {
      "name": "lhv",
      "url": "https://payment.maksekeskus.ee/banklink.html?method=EE_LHV&trx="
      }
      ],
      "cards": [
      {
      "name": "visa"
      },
      {
      "name": "mastercard"
      }
      ]
      }         *
     * </pre>
     * @param float $amount
     * @param string $currency
     * @param string $country
     * @return array
     */
    public function methods($amount, $currency, $country = '') {
        $params = array(
            'amount' => round($amount, 2),
            'currency' => $currency,
        );
        if ($country) {
            $params['country'] = strtolower($country);
        }
        return $this->_getRequest('methods', self::GET, $params);
    }
    
    
    /**
     * <p>Returns payment methods in format:</p>
     * <pre>
      {
      "banklinks": [
      {
      "name": "swedbank",
      "url": "https://payment.maksekeskus.ee/banklink.html?method=EE_SWED&trx="
      },
      {
      "name": "lhv",
      "url": "https://payment.maksekeskus.ee/banklink.html?method=EE_LHV&trx="
      }
      ],
      "cards": [
      {
      "name": "visa"
      },
      {
      "name": "mastercard"
      }
      ]
      }         *
     * </pre>
     * 
     * @param string $transaction
     * @return array
     */
    public function getMethodsForTransaction($transaction) {
        $params = array(
            'transaction' => $transaction,
        );
        return $this->_getRequest('methods', self::GET, $params);
    }
    
    
    /**
     * <p>Returned transaction format:</p>
     * <pre>
     * {
      "id": "7c579fd0-8d46-11e1-b0c4-0800200c9a66",
      "object": "transaction",
      "created_at": "2014-04-14T02:15:15Z",
      "status": "COMPLETED",
      "completed_at": "2014-04-14T02:18:35Z",
      "amount": 12.93,
      "currency": "EUR",
      "reference": "abc123",
      "type": "banklink",
      "customer": {
      "id": "7c579fd0-8d46-11e1-b0c4-0800200c9a66",
      "object": "customer",
      "created_at": "2014-04-14T02:15:15Z",
      "email": "customer@email.com",
      "ip": "234.12.34.567",
      "ip_country": "fi",
      "country": "ee",
      "locale": "et"
      }
      }
     * </pre>
     * @param string $transactionId
     * @return array
     */
    public function getTransaction($transactionId) {
        return $this->_getRequest('transactions/'.$transactionId, self::GET);
    }
    
    
    /**
     * <p>Returned transaction format:</p>
     * <pre>
     * {
      "id": "7c579fd0-8d46-11e1-b0c4-0800200c9a66",
      "object": "transaction",
      "created_at": "2014-04-14T02:15:15Z",
      "status": "CREATED",
      "completed_at": "2014-04-14T02:18:35Z",
      "amount": 12.93,
      "currency": "EUR",
      "reference": "abc123",
      "type": "banklink",
      "customer": {
      "id": "7c579fd0-8d46-11e1-b0c4-0800200c9a66",
      "object": "customer",
      "created_at": "2014-04-14T02:15:15Z",
      "email": "customer@email.com",
      "ip": "234.12.34.567",
      "ip_country": "fi",
      "country": "ee",
      "locale": "et"
      }
      }
     * </pre>
     * 
     * @param array $transaction
     * @param array $customer
     * @return array
     */
    public function createTransaction(array $transaction, array $customer) {
        $params = array(
            'transaction' => $transaction,
            'customer' => $customer,
        );
        return $this->_getRequest('transactions', self::POST, $params);
    }
    
    
    /**
     * <p>Returns an array of current shop configuration</p>
     * @return array
     */
    public function shopConfiguration() {
        $params = array(
            'environment' => json_encode(array(
                'platform' => 'Wordpress WooCommerce ' . $this->_getWooCommerce()->version,
                'module' => 'Eabi_Maksekeskus ' . woocommerce_banklinkmaksekeskus::getEabiVersion(),
            )),
        );
        return $this->_getRequest('shop/configuration', self::GET, $params);
    }

    /**
     * <p>Returned payment format:</p>
     * <pre>
     * {
      "id": "7c579fd0-8d46-11e1-b0c4-0800200c9a66",
      "object": "payment",
      "created_at": "2014-04-14T02:15:15Z",
      "status": "CREATED",
      "amount": 12.93,
      "currency": "EUR",
      "card": {
      "type": "visa",
      "name": "M***** K***",
      "last2": 42,
      "exp_month": 12,
      "exp_year": 15
      },
      "transaction": {
      "id": "7c579fd0-8d46-11e1-b0c4-0800200c9a66",
      "object": "transaction",
      "created_at": "2014-04-14T02:15:15Z",
      "status": "COMPLETED",
      "amount": 12.93,
      "currency": "EUR",
      "reference": "abc123",
      "type": "card",
      "customer": {
      "id": "7c579fd0-8d46-11e1-b0c4-0800200c9a66",
      "object": "customer",
      "created_at": "2014-04-14T02:15:15Z",
      "email": "customer@email.com",
      "ip": "234.12.34.567",
      "ip_country": "fi",
      "country": "ee",
      "locale": "et"
      }
      }
      }
     * </pre>
     * @param string $transactionId
     * @param double $amount
     * @param string $currency
     * @param string $token
     * @return array
     */
    public function createPayment($transactionId, $amount, $currency, $token) {
        $params = array(
            'amount' => doubleval($amount),
            'currency' => strval($currency),
            'token' => strval($token),
        );
        return $this->_getRequest('transactions/' . $transactionId . '/payments', self::POST, $params);
    }
    
    
    public function createRefund($transactionId, $amount, $reason) {
        $params = array(
            'amount' => doubleval($amount),
            'comment' => strval($reason),
        );
        $res = array();
        try {
            $res = $this->_getRequest('transactions/' . $transactionId . '/refunds', self::POST, $params);
        } catch (Eabi_Maksekeskus_Exception $ex) {
            //we catch special exception, which asks to enter a comment and display more human friendly error in such a case
            if ($ex->getCode() == '1001') {
                    throw new Eabi_Maksekeskus_Exception(__('Please enter reason for making this refund', $this->_plugin_text_domain));
            }
            throw $ex;
        }
        return $res;
    }


    
    
    
    
    /**
     * <p>Sends actual request to Maksekeskus API, prefills with public and secret API keys and json decodes the result.</p>
     * <p>If return error code is else than 0, then exception is thrown.</p>
     * @param array $params
     * @param string $url
     * @return array
     */
    protected function _getRequest($request, $method = self::GET, $params = array(), $url = null) {
        if (!$url) {
            $url = $this->getConfigData('api_url');
        }
        
        $headers = array(
            "User-Agent: Zend_Http_Client",
            "Authorization: Basic " . base64_encode($this->getConfigData('shop_id').":".$this->getConfigData('api_secret')),
            "Accept: application/json",
            "Content-type: application/json",
        );
        
        $options = array(
            'http' => array(
                'method' => $method,
                'ignore_errors' => true,
                'header' =>  '',
                'timeout' => $this->getConfigData('http_request_timeout') > 10 ? $this->getConfigData('http_request_timeout') : 10,
            ),
        );
        
        
        if ($method != self::GET && count($params)) {
            $options['http']['content'] = json_encode($params);
//            $client->setRawData(json_encode($params), 'application/json');
            $headers[] = 'Content-length: '. strlen($options['http']['content']);
        } else if (count($params)) {
            $request .= '?' . http_build_query($params);
        }
        $options['http']['header'] = implode("\r\n", $headers);
        
        $context = stream_context_create($options);

        $resp = file_get_contents($url . $request, false, $context);
        
        
        $dataToLog = array(
//            'options' => $options,
            'url' => $url . $request,
            'method' => $method,
            'params' => $params,
        );
        //used to remove username and password from url, if they are added there for making log not to contain sensitive data
        $dataToLog['url'] = preg_replace('/\/[[:alnum:]\-_]+\:[[:alnum:]\-_]+@/', '/***:***@', $dataToLog['url']);
        
        $decodeResult = @json_decode($resp, true);
        if (!$decodeResult || !$this->isSuccessful($http_response_header[0])) {
            $dataToLog['response_headers'] = $http_response_header;
            if (!$decodeResult) {
                $dataToLog['response'] = $resp;
            } else {
                $dataToLog['response'] = $decodeResult;
            }
            if ($this->_isLogEnabled()) {
                $stack = debug_backtrace();
                $dataToLog['stack'] = '';
                foreach ($stack as $key => $info) {
                    $dataToLog['stack'] .= "#" . $key . " Called " . $info['function'] . " in " . (isset($info['file'])?$info['file']:'NULL') . " on line " . (isset($info['line'])?$info['line']:'0') . "\r\n";
                }
                self::log($dataToLog, 'ERR');
            }
            
            if ($decodeResult && isset($decodeResult['code'])) {
                if (isset($decodeResult['message']) && $decodeResult['message']) {
                    throw new Eabi_Maksekeskus_Exception(rtrim($decodeResult['message'], '.'), $decodeResult['code']);
                } else {
                    throw new Eabi_Maksekeskus_Exception(sprintf(__('Maksekeskus request failed with response: %s', $this->_plugin_text_domain), print_r($http_response_header, true) . print_r($resp, true)), $decodeResult['code']);
                }
            } else {
                throw new Eabi_Maksekeskus_Exception(sprintf(__('Maksekeskus request failed with response: %s', $this->_plugin_text_domain), print_r($http_response_header, true) . print_r($resp, true)), 0);
            }
        }
        $dataToLog['response'] = $decodeResult;
        if ($this->_isLogEnabled()) {
            self::log($dataToLog, 'DEBUG');
        }
        return $decodeResult;
    }
    
    private static $_isRequestLogged = false;
    
    private static function log($data, $level = 'DEBUG') {
        $postLevel = 'DEBUG';
        //TODO: implement this function....
        if (is_null(self::$log)) {
            self::$log = new WC_Logger();
        }
        
        if (!self::$_isRequestLogged) {
            self::$log->add(__CLASS__, $postLevel. ': GET=' . print_r(self::__replaceWithAsterisk($_GET), true));
            self::$log->add(__CLASS__, $postLevel. ': POST=' . print_r(self::__replaceWithAsterisk($_POST), true));
            self::$_isRequestLogged = true;
        }
        
        if (is_object($data) || is_array($data)) {
//            $data = print_r($data, true);
        }
        
        self::$log->add(__CLASS__, $level. ': DATA=' . print_r($data, true));
        
        
    }
    
    private static function __replaceWithAsterisk($input) {
        $toRemoves = array(
            'woocommerce_banklinkmaksekeskus_shop_id',
            'woocommerce_banklinkmaksekeskus_api_secret_l',
            'woocommerce_banklinkmaksekeskus_api_public_l',
            'woocommerce_banklinkmaksekeskus_api_secret_t',
            'woocommerce_banklinkmaksekeskus_api_public_t',
        );
        $copy = $input;
        foreach ($toRemoves as $toRemove) {
            if (isset($copy[$toRemove]) && is_string($copy[$toRemove])) {
                $copy[$toRemove] = '*** removed ***';
            }
        }
        return $copy;
    }
    
    
    protected function isSuccessful($header) {
        $matches = array();
        preg_match('#HTTP/\d+\.\d+ (\d+)#', $header, $matches);
//        echo $matches[1]; // HTTP/1.1 410 Gone return 410
        return $matches[1] >= 200 && $matches[1] < 300;
    }

    
    /**
     * <p>Returns true, if loggins is enabled for related payment method processor</p>
     * @return bool
     */
    protected function _isLogEnabled() {
        return $this->getConfigData('enable_log') == 'yes';
    }

    
    /**
     * 
     * @global Woocommerce $woocommerce
     * @return Woocommerce
     */
    protected function _getWooCommerce() {
        global $woocommerce;
        return $woocommerce;
    }

}

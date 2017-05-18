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
 * Description of Logger
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Model_Logger {

    private static $_isRequestLogged = false;

    /**
     *
     * @var WC_Logger
     */
    private static $log;
    
    
    protected $_logPrefix = '';
    
    protected $_isLogEnabled = false;
    
    protected $logFileName = 'WC_Eabi_Postoffice';

    /**
     * <p>Returns true, if loggins is enabled for related payment method processor</p>
     * @return bool
     */
    protected function _isLogEnabled() {
        return $this->_isLogEnabled;
    }
    
    public function setIsLogEnabled($isLogEnabled) {
        $this->_isLogEnabled = (bool)$isLogEnabled;
        return $this;
    }
    
    public function getIsLogEnabled() {
        return $this->_isLogEnabled;
    }
    
    
    public function setLogPrefix($logPrefix) {
        $this->_logPrefix = $logPrefix;
        return $this;
    }
    
    public function getLogPrefix() {
        return $this->_logPrefix;
    }
    
    public function getLogFileName() {
        return $this->logFileName;
    }

    public function setLogFileName($logFileName) {
        $this->logFileName = $logFileName;
        return $this;
    }


    /**
     * <p>Returns current log file path, if WooCommerce is at least 2.2</p>
     * @return string|bool
     */
    public function getLogFilePath() {
        $path = false;
        if (function_exists('wc_get_log_file_path')) {
            $path = wc_get_log_file_path($this->getLogFileName() );
        }
        return $path;
    }
    
    public function clear() {
        if (is_null(self::$log)) {
            self::$log = new WC_Logger();
            self::$log->clear($this->getLogFileName());
        }
        return $this;
    }

    protected static function _log($data, $logPrefix, $level, $class) {
        $postLevel = 'DEBUG';
        //TODO: implement this function....
        if (is_null(self::$log)) {
            self::$log = new WC_Logger();
        }

        if (!self::$_isRequestLogged) {
            if (isset($_POST) && count($_POST)) {
                self::$log->add($class, $postLevel . ': POST=' . print_r(self::_removeSensitiveVariables($_POST, $logPrefix), true));
            }
            if (isset($_GET) && count($_GET)) {
                self::$log->add($class, $postLevel . ': GET=' . print_r(self::_removeSensitiveVariables($_GET, $logPrefix), true));
            }
            if (isset($_SERVER) && isset($_SERVER['HTTP_USER_AGENT'])) {
                self::$log->add($class, $postLevel . ': USER_AGENT=' . $_SERVER['HTTP_USER_AGENT']);
            }
            self::$_isRequestLogged = true;
        }

        if (is_object($data) || is_array($data)) {
//            $data = print_r($data, true);
        }
        if ($data instanceof Exception) {
            $data = $data->__toString();
        }

        self::$log->add($class, sprintf('%s: %s %s', $level, $logPrefix, print_r($data, true)));
    }
    
    protected static function _removeSensitiveVariables($input, $logPrefix) {
        $result = $input;
        if (!is_array($result)) {
            return $result;
        }
        $sensitives = array(
            'woocommerce_' . $logPrefix . '_licence',
            'woocommerce_' . $logPrefix . '_sendpackage_username',
            'woocommerce_' . $logPrefix . '_sendpackage_password',
        );
        foreach ($sensitives as $sensitive) {
            if (isset($result[$sensitive])) {
                $result[$sensitive] = '***';
            }
        }
        
        return $result;
    }

    protected function log($dataToLog, $level = 'DEBUG') {

        if ($this->_isLogEnabled()) {
            self::_log($dataToLog, $this->_logPrefix, $level, $this->getLogFileName());
        }
        return $this;
    }
    
    public function error($dataToLog) {
        return $this->log($dataToLog, 'ERROR');
    }
    public function debug($dataToLog) {
        return $this->log($dataToLog, 'DEBUG');
    }
    public function info($dataToLog) {
        return $this->log($dataToLog, 'INFO');
    }
    
    public function logStackTrace($extraInfo = null) {
        $dataToLog = array();
        $dataToLog['info'] = $extraInfo;
        $stack = debug_backtrace();
        $dataToLog['stack'] = '';
        foreach ($stack as $key => $info) {
            $dataToLog['stack'] .= "#" . $key . " Called " . $info['function'] . " in " . $info['file'] . " on line " . $info['line'] . "\r\n";
        }

        return $this->log($dataToLog, 'DEBUG');
    }

}

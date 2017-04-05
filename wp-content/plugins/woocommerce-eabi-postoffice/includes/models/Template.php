<?php

/* 
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */
class Eabi_Woocommerce_Postoffice_Model_Template {

    protected $_template_path;
    protected $_template;
    protected $_data = array();
    
    public function __construct($templatePath = '') {
        $this->_template_path = $templatePath;
        $this->_construct();
    }
    
    public function setTemplatePath($templatePath) {
        $this->_template_path = $templatePath;
        return $this;
    }
    
    protected function _construct() {
        
    }
    
    
    public function setData(array $data) {
        $this->_data = $data;
        return $this;
    }
    
    public function getData($name = '') {
        if ($name) {
            if (!isset($this->_data[$name])) {
                return '';
            }
            return $this->_data[$name];
        }
        return $this->_data;
    }
    


    public function setTemplate($template) {
        $this->_template = $template;
        return $this;
    }
    
    public function getTemplate() {
        return $this->_template;
    }

    protected function _toHtml() {
//        extract ($this->_viewVars, EXTR_SKIP);
        return false;
    }
    
    
    
    

    public final function toHtml() {
        $html = $this->_toHtml();
        if (is_string($html)) {
            return $html;
        }
        ob_start();
        if (file_exists(untrailingslashit($this->_template_path) . '/' . $this->_template)) {
            require untrailingslashit($this->_template_path) . '/' . $this->_template;
        } else {
            //fallback to default
            require untrailingslashit(dirname(dirname(dirname(__FILE__)))) . '/templates/' . $this->_template;
        }
        return ob_get_clean();
    }
}

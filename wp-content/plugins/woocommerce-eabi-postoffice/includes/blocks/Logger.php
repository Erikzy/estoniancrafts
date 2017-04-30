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
class Eabi_Woocommerce_Postoffice_Block_Logger extends Eabi_Woocommerce_Postoffice_Model_Template {
    public $formFieldId;
    public $formFieldName;
    public $formFieldKey;
    
    /**
     *
     * @var WC_Eabi_Postoffice 
     */
    protected $_instance;
    
    protected function _construct() {
        $this->setTemplate('logger.php');
    }
    
    

    /**
     * 
     * @return WC_Eabi_Postoffice
     */
    public function getInstance() {
        return $this->_instance;
    }

    /**
     * 
     * @param WC_Eabi_Postoffice $instance
     * @return \Eabi_Woocommerce_Postoffice_Block_Logger
     */
    public function setInstance($instance) {
        $this->_instance = $instance;
        return $this;
    }

        
    
    
}

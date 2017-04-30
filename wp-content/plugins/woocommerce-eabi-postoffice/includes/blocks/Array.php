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
 * Description of Array
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Block_Array extends Eabi_Woocommerce_Postoffice_Model_Template {
    public $formFieldId;
    public $formFieldName;
    
    protected $blockValue;
    protected $_columns = array();
    
    protected function _construct() {
        $this->setTemplate('abstract-array.php');
        
    }
    
    public function getColumns() {
        return $this->_columns;
    }
    
    public function setValue($value) {
        $this->blockValue = $value;
        return $this;
    }
    
    public function getValue() {
        return $this->blockValue;
    }
    
    public function setColumns($columns) {
        $this->_columns = $columns;
        return $this;
    }
    
    
    public function getAddButtonLabel() {
        return __('Add new', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
    }
    
    public function getDeleteButtonLabel() {
        return __('Delete', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN);
    }
    
    
}


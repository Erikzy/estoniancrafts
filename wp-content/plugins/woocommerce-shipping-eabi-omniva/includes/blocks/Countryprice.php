<?php

/*
   *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */
if (!class_exists('Eabi_Woocommerce_Postoffice_Block_Array')) {
    WC_Eabi_Postoffice::instance()->helper()->getBlock('array');
}

/**
 * Description of Countryprice
 *
 * @author Matis
 */
class Eabi_Woocommerce_Omniva_Block_Countryprice extends Eabi_Woocommerce_Postoffice_Block_Array {

    protected function _construct() {
        parent::_construct();
        $this->_columns = array(
            array(
                'label' => __('Country', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                'name' => 'country_id',
                'type' => 'select',
                'class' => '',
                'style' => '',
                'options' => WC_Eabi_Postoffice::instance()->helper()->toLabelValues(WC_Eabi_Postoffice::instance()->helper()->getServiceCountries()),
            ),
            array(
                'label' => __('S price', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN),
                'name' => 's_price',
                'type' => 'text',
                'class' => '',
                'style' => '',
            ),
            array(
                'label' => __('M price', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN),
                'name' => 'm_price',
                'type' => 'text',
                'class' => '',
                'style' => '',
            ),
            array(
                'label' => __('L price', WC_Eabi_Omniva_ParcelTerminal::PLUGIN_TEXT_DOMAIN),
                'name' => 'l_price',
                'type' => 'text',
                'class' => '',
                'style' => '',
            ),
            array(
                'label' => __('Free shipping from', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN),
                'name' => 'free_from',
                'type' => 'text',
                'class' => '',
                'style' => '',
            ),
        );
    }

}

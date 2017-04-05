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
 * Description of Shippingprice
 *
 * @author Matis
 */
class Eabi_Woocommerce_Postoffice_Model_Shippingprice {
    
    /**
     *
     * @var WC_Eabi_Postoffice 
     */
    protected $_shippingMethod;
    
    
    
    public function isAvailable($package) {
        return true;
    }
    
    public function getProductSizes($package) {
        $sizes = array();
        $totalWeight = 0;
        $totalQuantity = 0;
        foreach ($package['contents'] as $content) {
            $product = $content['data'];
            /* @var $product WC_Product_Simple */
            if ($product->needs_shipping()) {
                $productSize = $this->getProductSize($product, $content);
                $totalWeight += ($productSize['weight'] * $productSize['q']);
                $totalQuantity += $productSize['q'];
            }
        }
        $sizes[] = array(
            'h' => 0,
            'w' => 0,
            'd' => 0,
            'q' => $totalQuantity,
            'weight' => $totalWeight,
        );
        return $sizes;
    }

    public function getProductSize($product, $quoteItem) {
        return array(
            'h' => WC_Eabi_Postoffice::instance()->toCm($product->height),
            'w' => WC_Eabi_Postoffice::instance()->toCm($product->width),
            'd' => WC_Eabi_Postoffice::instance()->toCm($product->length),
            'q' => $quoteItem['quantity'],
            'weight' => WC_Eabi_Postoffice::instance()->toKg($product->weight),
        );
    }
    
    public function getFeeFromSize($productSize, $costs = array()) {
        //return package weight in kg rounded up
        $parcelSize = ceil($productSize['weight']);
        if (isset($costs[$parcelSize])) {
            return $costs[$parcelSize];
        }
        return $parcelSize;
    }
    
    
    /**
     * 
     * @return WC_Eabi_Postoffice
     */
    public function getShippingMethod() {
        return $this->_shippingMethod;
    }

    /**
     * 
     * @param WC_Eabi_Postoffice $shippingMethod
     * @return \Eabi_Woocommerce_Postoffice_Model_Shippingprice
     */
    public function setShippingMethod($shippingMethod) {
        $this->_shippingMethod = $shippingMethod;
        return $this;
    }


    
}

<?php

/*
   *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */
if (!class_exists('Eabi_Woocommerce_Postoffice_Model_Shippingprice')) {
    WC_Eabi_Postoffice::instance()->helper()->getModel('shippingprice');
}

/**
 * Description of Shippingprice
 *
 * @author Matis
 */
class Eabi_Woocommerce_Omniva_Model_Shippingprice extends Eabi_Woocommerce_Postoffice_Model_Shippingprice {

    public function isAvailable($package) {
        $sizes = $this->getProductSizes($package);
        foreach ($sizes as $size) {
            if ($this->getFeeFromSize($size) === false) {
                return false;
            }
        }
        return true;
    }

    public function getProductSizes($package) {
        $sizes = array();
        foreach ($package['contents'] as $content) {
            $product = $content['data'];
            /* @var $product WC_Product_Simple */
            if ($product->needs_shipping()) {
                $productSize = $this->getProductSize($product, $content);
                $sizes[] = $productSize;
            }
        }
        return $sizes;
    }

    public function getFeeFromSize($productSize, $costs = array()) {
        $size = array(
            $productSize['w'],
            $productSize['d'],
            $productSize['h'],
        );
        sort($size);
        if ($size[1] > 38) {
            $swap = $size[1];
            $size[1] = $size[0];
            $size[0] = $swap;
        }
        
        if ($size[2] > 64 || $size[1] > 39 || $size[0] > 38) {
            return false;
        }


        if ($size[0] <= 9) {
            $cost = 0;
        } else if ($size[0] <= 19) {
            $cost = 1;
        } else if ($size[0] <= 38) {
            $cost = 2;
        }
        if ($costs && isset($costs[$cost])) {
            return $costs[$cost];
        }
        return $cost;
    }

}

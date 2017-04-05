<?php

/* 
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */

class Eabi_Woocommerce_Postoffice_Model_Addresshelper {

    /**
     * <p>Returns array with following structure:</p>
     * <p>        array(
      'street' =&gt; 'street name',
      'house' =&gt; 'house number',
      'apartment' =&gt; 'apartment number',
      );</p>
     * @param WC_Order $address
     * @param WC_Eabi_Postoffice $shippingMethodModel
     */
    public function separateHouseApartmentFromStreet($address, $shippingMethodModel) {
        $fieldAddrHouse = $shippingMethodModel->get_option('field_addr_house');
        $fieldAddrApartment = $shippingMethodModel->get_option('field_addr_apartment');

        $addressParts = array(
            'street' => '',
            'house' => '',
            'apartment' => '',
        );

        if ($fieldAddrApartment && $fieldAddrHouse && get_post_meta($address->id, $fieldAddrHouse, true)) {
            //we have separate fields collected from the customers already
            $addressParts['street'] = $address->shipping_address_1;
            $addressParts['house'] = get_post_meta($address->id, $fieldAddrHouse, true);
            $addressParts['apartment'] = get_post_meta($address->id, $fieldAddrApartment, true);
        } else {
            //do the separation
            $addressParts = $this->_separateStreet($address->shipping_address_1);
        }
        

        return $addressParts;
    }

    protected function _separateStreet($street) {
        //split the address into chunks by " " and "-"
        $addressChunks = preg_split('/[\s\-]/', $street);
        
        $addressParts = array(
            'street' => '',
            'house' => '',
            'apartment' => '',
        );
        
        
        //detect numerical chunks
        $numericalChunks = array();
        $offset = 0;
        foreach ($addressChunks as $addressChunk) {
            if (preg_match('/[0-9]+/', $addressChunk)) {
                $numChunk = array(
                    'chunk' => $addressChunk,
                    'position' => strpos($street, $addressChunk, $offset),
                );
                $numericalChunks[] = $numChunk;
                $offset = $numChunk['position'];
                
            }
        }
        
        if (count($numericalChunks) === 0) {
            //do nothing
        } else if (count($numericalChunks) === 1) {
            //street 1
            $addressParts['house'] = $numericalChunks[0]['chunk'];
        } else {
            //we have more numerical indexes
            //first try to resolve
            //street 1-1 and street 1 1 positions, if it fails, then try to locate the position of the street itself.
            for ($i = 0; $i < count($numericalChunks) - 1; $i++) {

                if (strpos($street, $numericalChunks[$i]['chunk'] . '-' . $numericalChunks[$i + 1]['chunk']) !== false) {
                    //street 1-1
                    $addressParts['house'] = $numericalChunks[$i]['chunk'];
                    $addressParts['apartment'] = $numericalChunks[$i + 1]['chunk'];
                    break;
                } else if (strpos($street, $numericalChunks[$i]['chunk'] . ' ' . $numericalChunks[$i + 1]['chunk']) !== false) {
                    //street 1 1
                    $addressParts['house'] = $numericalChunks[$i]['chunk'];
                    $addressParts['apartment'] = $numericalChunks[$i + 1]['chunk'];
                    break;
                }
            }
            
            //we still have not managed to fetch the house, and we have two or more numeric chunks
            if (!$addressParts['house']) {
                //assign last number as house
                $last = count($numericalChunks) - 1;
                $addressParts['house'] = $numericalChunks[$last];
            }
            
        }
        
        
        //assign the street
        if ($addressParts['apartment']) {
            //apartment and house
            $addressParts['street'] = str_replace(array(
                $addressParts['house'].' '.$addressParts['apartment'],
                $addressParts['house'].'-'.$addressParts['apartment'],
            ), '', $street);
            
        } else {
            //only house
            $addressParts['street'] = str_replace(' ' . $addressParts['house'], '', $street);
            
        }
        return $addressParts;
    }
    
}
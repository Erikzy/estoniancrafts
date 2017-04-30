<?php

/*
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */
/* @var $wpdb wpdb */
/* @var $model WC_Eabi_Postoffice */
$sqls = array();

$modelClass = get_class($model);
$sqls[] = ( "INSERT INTO ". $wpdb->prefix."eabi_carriermodule (carrier_code, class_name, update_time) VALUES ('{$model->id}', '{$modelClass}', NOW());");

foreach ($sqls as $sql) {
    $wpdb->query($sql);
    if ($wpdb->last_error != '') {
        throw new Eabi_Woocommerce_Postoffice_Exception($wpdb->last_error);
    }
}

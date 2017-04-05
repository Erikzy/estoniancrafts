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


$sqls[] = ( "    CREATE TABLE " . $wpdb->prefix . "eabi_postoffice (
        `id` int(11) unsigned NOT NULL auto_increment,
        `remote_module_id` int(11) unsigned NOT NULL,
        `remote_module_name` varchar(255) NOT NULL,
        `remote_place_id` int(11) unsigned NOT NULL,
        `remote_servicing_place_id` int(11) unsigned NULL,

        `name` varchar(255) NOT NULL,
        `city` varchar(255) NULL,
        `county` varchar(255) NULL,
        `zip_code` varchar(255) NULL,
        `country` varchar(2) NULL,
        `description` text NULL,

        `group_id` int(11) unsigned NULL,
        `group_name` varchar(255) NULL,
        `group_sort` int(11) unsigned NULL,

        `local_carrier_id` int(11) unsigned NULL,
    
        `created_time` datetime NULL,
        `update_time` datetime NULL,
        `cached_attributes` text NULL,
    
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");


$sqls[] = ("    ALTER TABLE " . $wpdb->prefix . "eabi_postoffice ADD UNIQUE (
		`remote_module_id`,
		`remote_place_id`
	);
");
$sqls[] = ("CREATE TABLE " . $wpdb->prefix . "eabi_carriermodule (
        `id` int(11) unsigned NOT NULL auto_increment,
        `carrier_code` varchar(255) NOT NULL,
        `class_name` varchar(255) NOT NULL,
        `update_time` datetime NULL,
    
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

foreach ($sqls as $sql) {
    $wpdb->query($sql);
    if ($wpdb->last_error != '') {
        throw new Eabi_Woocommerce_Postoffice_Exception($wpdb->last_error);
    }
}

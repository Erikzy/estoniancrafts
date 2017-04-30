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
 * Eabi_Postoffice Dependency checker
 *
 * Checks if WooCommerce is enabled
 */
class Eabi_Postoffice_Dependencies {

    private static $active_plugins;

    public static function init() {

        self::$active_plugins = (array) get_option('active_plugins', array());

        if (is_multisite()) {
            self::$active_plugins = array_merge(self::$active_plugins, get_site_option('active_sitewide_plugins', array()));
        }
    }

    public static function eabi_postoffice_active_check() {

        if (!self::$active_plugins) {
            self::init();
        }

        return in_array('woocommerce-eabi-postoffice/woocommerce-eabi-postoffice.php', self::$active_plugins) || array_key_exists('woocommerce-eabi-postoffice/woocommerce-eabi-postoffice.php', self::$active_plugins);
    }

}

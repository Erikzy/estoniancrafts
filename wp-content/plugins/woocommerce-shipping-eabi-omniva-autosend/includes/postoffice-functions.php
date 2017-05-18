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
 * Functions used by plugins
 */
if (!class_exists('Eabi_Postoffice_Dependencies')) {
    require_once( 'class-eabi-postoffice-dependencies.php' );
}

/**
 * WC Detection
 * */
function is_eabi_postoffice_active() {

    return Eabi_Postoffice_Dependencies::eabi_postoffice_active_check();
}

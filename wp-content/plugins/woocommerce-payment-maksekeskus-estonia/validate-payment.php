<?php
/*
 * 
 *  Copyright 2016 Aktsiamaailm OÜ
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  

 */

$_GET['validateMaksekeskus'] = 'true';
$_GET['wc-api'] = 'woocommerce_banklinkmaksekeskus';
foreach ($_SERVER as $k => $v) {
	$_SERVER[$k] = str_replace('/wp-content/plugins/woocommerce-payment-maksekeskus-estonia/validate-payment', '/index', $_SERVER[$k]);
}
chdir(dirname(dirname(dirname(dirname(__FILE__)))));
require_once('index.php');

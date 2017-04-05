<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

if($wp_query->query_vars['lbpdf'] == 'bc'){
	
	lbPdf::generate_fullsize_bcard();

}else if($wp_query->query_vars['lbpdf'] == 'bc-stand'){

	lbPdf::generate_standard_bcard();

}else if($wp_query->query_vars['lbpdf'] == 'pricetag-a4'){

	lbPdf::generate_a4_pricetag($_GET['id']);

}else if($wp_query->query_vars['lbpdf'] == 'pricetag-a5'){

	lbPdf::generate_a5_pricetag($_GET['id']);

}else if($wp_query->query_vars['lbpdf'] == 'pricetag-bcard'){

	lbPdf::generate_bcard_pricetag($_GET['id']);

}

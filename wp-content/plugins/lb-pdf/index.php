<?php
/**
 * Plugin Name: Dokan PDF extension 
 * Description: Käsitööturg custom PDF extension for Dokan plugin
 * Version: 1.0
 */

require_once('tcpdf/tcpdf.php');

class lbPdf{

	public static $a4_size = ['w' => 210, 'h' =>297];

	function __construct(){


	}

	public static function setup_a4( $orientation = 'P', $font_size = 14){

		// create new PDF document
		$pdf = new TCPDF($orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Käsitööturg');
		$pdf->SetTitle('Käsitööturg PDF');
		
		// remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set JPEG quality
		$pdf->setJPEGQuality(80);

		// convert TTF font to TCPDF format and store it on the fonts folder
		$fontname = TCPDF_FONTS::addTTFfont(plugin_dir_path( __FILE__ ).'OpenSans-Light.ttf', 'TrueTypeUnicode', '', 96);

		// use the font
		$pdf->SetFont($fontname, '', $font_size, '', false);

		return $pdf;

	}

	
	public static function generate_bcard_pricetag($product_id){

		$_pf = new WC_Product_Factory();  
		$_product = $_pf->get_product($product_id);

		if(	!$_product ){
			return false;
		}

		// TODO: correct logo & better positioning & better fonts!
		$pdf = self::setup_a4('P', 10);

		// add a page
		$pdf->AddPage();


		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		$left = PDF_MARGIN_LEFT;
		$top = PDF_MARGIN_TOP;

		// set some text to print
		$txt = $_product->get_title()."<br><br>".$_product->get_price_html();

		for($i=0; $i < 8; $i++){

			$left = PDF_MARGIN_LEFT + $i%2 * 90;

			if( $i != 0 && $i != 1 && $i != 4 && $i != 5 ){

				$pdf->Image(plugin_dir_path( __FILE__ ).'google.png', $left, $top, 30, 25);
				$pdf->writeHTMLCell(50, 0, $left + 30, $top, $txt); //Again where x and y are offset
			}

			$top += $i%2 * 70;
		}
		

		//Close and output PDF document
		$pdf->Output('pricetag-bcard.pdf', 'I');

	}

	public static function generate_a5_pricetag($product_id){

		$_pf = new WC_Product_Factory();  
		$_product = $_pf->get_product($product_id);

		if(	!$_product ){
			return false;
		}

		// TODO: correct logo & better positioning & better fonts!
		$pdf = self::setup_a4();

		// add a page
		$pdf->AddPage();


		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		// Image example with resizing
		$pdf->Image(plugin_dir_path( __FILE__ ).'google.png', 0, self::$a4_size['h'] / 2, 130, 107);


		// set some text to print
		$txt = $_product->get_title()."<br><br>".$_product->get_price_html();

		// $pdf->MultiCell(100, 5, $txt, 0, 'L', 0, 1, 150, '', true);
		$pdf->writeHTMLCell(self::$a4_size['h'] - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT, 0, PDF_MARGIN_LEFT, self::$a4_size['h'] / 2 + 50, $txt, 0, 0, false, true, 'C'); //Again where x and y are offset

		//Close and output PDF document
		$pdf->Output('pricetag-a5.pdf', 'I');

	}

	public static function generate_a4_pricetag($product_id){

		$_pf = new WC_Product_Factory();  
		$_product = $_pf->get_product($product_id);

		if(	!$_product ){
			return false;
		}

		// TODO: correct logo & better positioning & better fonts!
		$pdf = self::setup_a4('L');

		// add a page
		$pdf->AddPage();


		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		// Image example with resizing
		$pdf->Image(plugin_dir_path( __FILE__ ).'google.png', 0, 0, 130, 107);


		// set some text to print
		$txt = $_product->get_title()."<br><br>".$_product->get_price_html();

		// $pdf->MultiCell(100, 5, $txt, 0, 'L', 0, 1, 150, '', true);
		$pdf->writeHTMLCell(self::$a4_size['h'] - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT, 0, PDF_MARGIN_LEFT, self::$a4_size['w'] / 2, $txt, 0, 0, false, true, 'C'); //Again where x and y are offset

		//Close and output PDF document
		$pdf->Output('pricetag-a4.pdf', 'I');

	}

	public static function generate_standard_bcard(){

		// TODO: correct logo & better positioning & better fonts!
		$current_user = wp_get_current_user();
		$ext_profile = get_user_meta( $current_user->ID, 'ktt_extended_profile', true );

		$pdf = self::setup_a4('P', 9);

		// add a page
		$pdf->AddPage();

		$left = PDF_MARGIN_LEFT;
		$top = PDF_MARGIN_TOP;

		// set some text to print
		$txt = $current_user->user_firstname." ".$current_user->user_lastname."<br><br>";
		$txt .= $ext_profile['mobile']."<br>";
		$txt .= $current_user->user_email."<br>";
		$txt .= site_url()."/user/".$current_user->user_login;

		for($i=0; $i < 8; $i++){

			$left = PDF_MARGIN_LEFT + $i%2 * 90;

			$pdf->Image(plugin_dir_path( __FILE__ ).'google.png', $left, $top, 30, 25);
			$pdf->writeHTMLCell(50, 0, $left + 30, $top, $txt); //Again where x and y are offset

			$top += $i%2 * 70;
		}
		

		//Close and output PDF document
		$pdf->Output('bc-'.$current_user->user_login.'.pdf', 'I');

	}

	public static function generate_fullsize_bcard(){
		
		// TODO: correct logo & better positioning & better fonts!
		
		$current_user = wp_get_current_user();
		$ext_profile = get_user_meta( $current_user->ID, 'ktt_extended_profile', true );

		$pdf = self::setup_a4('L');

		// add a page
		$pdf->AddPage();


		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		// Image example with resizing
		$pdf->Image(plugin_dir_path( __FILE__ ).'google.png', 0, 0, 130, 107);


		// set some text to print
		$txt = $current_user->user_firstname." ".$current_user->user_lastname."\n\n";
		$txt .= $ext_profile['mobile']."\n";
		$txt .= $current_user->user_email."\n";
		$txt .= site_url()."/user/".$current_user->user_login;

		$pdf->MultiCell(100, 5, $txt, 0, 'L', 0, 1, 150, '', true);
		

		//Close and output PDF document
		$pdf->Output('bc-full-'.$current_user->user_login.'.pdf', 'I');


	}

	public static function display_bcard_links(){

		echo '<a href="'.site_url().'/lbpdf/bc-stand/" target="_blank">'.__( 'Business cards', 'dokan' ).'</a><br>';
		echo '<a href="'.site_url().'/lbpdf/bc/" target="_blank">'.__( 'A4 business card', 'dokan' ).'</a><br><br>';
		echo '<a href="'.site_url().'?add-to-cart=135" target="_blank">'.__( 'Order a business card', 'dokan' ).'</a>';

	}

}

new lbPdf();

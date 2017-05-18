<?php
/**
 * Email Header
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<!DOCTYPE html>
<html dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
        <style>
        <?php
        // Load colours
        $bg              = get_option( 'woocommerce_email_background_color' );
        $body            = get_option( 'woocommerce_email_body_background_color' );
        $base            = get_option( 'woocommerce_email_base_color' );
        $base_text       = wc_light_or_dark( $base, '#202020', '#ffffff' );
        $text            = get_option( 'woocommerce_email_text_color' );

        $bg_darker_10    = wc_hex_darker( $bg, 10 );
        $body_darker_10  = wc_hex_darker( $body, 10 );
        $base_lighter_20 = wc_hex_lighter( $base, 20 );
        $base_lighter_40 = wc_hex_lighter( $base, 40 );
        $text_lighter_20 = wc_hex_lighter( $text, 20 );

        // !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
        ?>
        #wrapper {
            background-color: <?php echo esc_attr( $bg ); ?>;
            margin: 0;
            padding: 70px 0 70px 0;
            -webkit-text-size-adjust: none !important;
            width: 100%;
        }

        #template_container {
            box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
            background-color: <?php echo esc_attr( $body ); ?>;
            border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>;
            border-radius: 3px !important;
        }

        #template_header {
            background-color: <?php echo esc_attr( $base ); ?>;
            border-radius: 3px 3px 0 0 !important;
            color: <?php echo esc_attr( $base_text ); ?>;
            border-bottom: 0;
            font-weight: bold;
            line-height: 100%;
            vertical-align: middle;
            font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
        }

        #template_header h1 {
            color: <?php echo esc_attr( $base_text ); ?>;
        }

        #template_footer td {
            padding: 0;
            -webkit-border-radius: 6px;
        }

        #template_footer #credit {
            border:0;
            color: <?php echo esc_attr( $base_lighter_40 ); ?>;
            font-family: Arial;
            font-size:12px;
            line-height:125%;
            text-align:center;
            padding: 0 48px 48px 48px;
        }

        #body_content {
            background-color: <?php echo esc_attr( $body ); ?>;
        }

        #body_content table td {
            padding: 48px;
        }

        #body_content table td td {
            padding: 12px;
        }

        #body_content table td th {
            padding: 12px;
        }

        #body_content p {
            margin: 0 0 16px;
        }

        #body_content_inner {
            color: <?php echo esc_attr( $text_lighter_20 ); ?>;
            font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
            font-size: 14px;
            line-height: 150%;
            text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
        }

        .td {
            color: <?php echo esc_attr( $text_lighter_20 ); ?>;
            border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
        }

        .text {
            color: <?php echo esc_attr( $text ); ?>;
            font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
        }

        .link {
            color: <?php echo esc_attr( $base ); ?>;
        }

        #header_wrapper {
            padding: 36px 48px;
            display: block;
        }

        h1 {
            color: <?php echo esc_attr( $base ); ?>;
            font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
            font-size: 30px;
            font-weight: 300;
            line-height: 150%;
            margin: 0;
            text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
            text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>;
            -webkit-font-smoothing: antialiased;
        }

        h2 {
            color: <?php echo esc_attr( $base ); ?>;
            display: block;
            font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
            font-size: 18px;
            font-weight: bold;
            line-height: 130%;
            margin: 16px 0 8px;
            text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
        }

        h3 {
            color: <?php echo esc_attr( $base ); ?>;
            display: block;
            font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
            font-size: 16px;
            font-weight: bold;
            line-height: 130%;
            margin: 16px 0 8px;
            text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
        }

        a {
            color: <?php echo esc_attr( $base ); ?>;
            font-weight: normal;
            text-decoration: underline;
        }

        img {
            border: none;
            display: inline;
            font-size: 14px;
            font-weight: bold;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            text-transform: capitalize;
        }
        </style>
	</head>
    <body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
    	<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'?>">
        	<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
            	<tr>
                	<td align="center" valign="top">
						<div id="template_header_image">
	                		<?php
	                			if ( $img = get_option( 'woocommerce_email_header_image' ) ) {
	                				echo '<p style="margin-top:0;"><img src="' . esc_url( $img ) . '" alt="' . get_bloginfo( 'name', 'display' ) . '" /></p>';
	                			}
	                		?>
						</div>
                    	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Header -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header">
                                        <tr>
                                            <td id="header_wrapper">
                                            	<h1><?php echo $email_heading; ?></h1>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Header -->
                                </td>
                            </tr>
                        	<tr>
                            	<td align="center" valign="top">
                                    <!-- Body -->
                                	<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_body">
                                    	<tr>
                                            <td valign="top" id="body_content">
                                                <!-- Content -->
                                                <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td valign="top">
                                                            <div id="body_content_inner">

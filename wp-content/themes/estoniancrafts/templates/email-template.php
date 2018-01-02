<?php
$bg              = get_option( 'woocommerce_email_background_color' );
$body            = get_option( 'woocommerce_email_body_background_color' );
$base            = get_option( 'woocommerce_email_base_color' );
$base_text       = wc_light_or_dark( $base, '#202020', '#ffffff' );
$text            = get_option( 'woocommerce_email_text_color' );
$estonian_craft_dark_head = "#1B1919";
$estonian_craft_orange = "#ef7f27";
$bg_darker_10    = wc_hex_darker( $bg, 10 );
$body_darker_10  = wc_hex_darker( $body, 10 );
$base_lighter_20 = wc_hex_lighter( $base, 20 );
$base_lighter_40 = wc_hex_lighter( $base, 40 );
$text_lighter_20 = wc_hex_lighter( $text, 20 );


	$body = <<<EOF
<style>
		@import url('https://fonts.googleapis.com/css?family=Roboto');

			@font-face {
			    font-family: 'Aino-Regular';
			    src: url('https://beta.estoniancrafts.com/wp-content/themes/estoniancrafts/aino-font/Aino-Regular.woff') format('woff');
			    font-weight: normal;
			    font-style: normal;
			}
			@font-face {
			    font-family: 'Aino-Bold';
			    src: url('../../../aino-font/Aino-Bold.woff') format('woff');
			    font-weight: bold;
			    font-style: normal;
			}
			@font-face {
			    font-family: 'Aino-Headline';
			    src: url('../../../aino-font/Aino-Headline.woff') format('woff');
			    font-weight: bold;
			    font-style: normal;
			}
			#wrapper {
				background-color:  esc_attr( $bg );
				margin: 0;
				padding: 70px 0 70px 0;
				-webkit-text-size-adjust: none !important;
				width: 100%;
			}

			#template_container {
				box-shadow: 0 1px 4px rgba(0,0,0,0.1) !important;
				background-color:   esc_attr( $body ) ;
				border: 1px solid  esc_attr( $bg_darker_10 ) ;
				border-radius: 3px !important;
			}

			#template_header {
				background-color: #ffffff <?php //echo esc_attr( $bg ); ?>;
				border-radius: 3px 3px 0 0 !important;
				color:  esc_attr( $base_text ); 
				border-bottom: 0;
				font-weight: bold;
				line-height: 100%;
				vertical-align: middle;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
			}

			#template_header h1,
			#template_header h1 a {
				color:   esc_attr( $estonian_craft_dark_head);  
			}

			#template_footer td {
				padding: 0;
				-webkit-border-radius: 6px;
			}

			#template_footer #credit {
				border:0;
				color:  esc_attr( $base_lighter_40 );  
				font-family: Arial;
				font-size:12px;
				line-height:125%;
				text-align:center;
				/*padding: 0 48px 48px 48px;*/
			}

			#body_content {
				background-color: esc_attr( $body );  
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
				color:  esc_attr( $text_lighter_20 ); 
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				font-size: 14px;
				line-height: 150%;
				text-align:    is_rtl() ? 'right' : 'left'; 
			}

			.td {
				color: esc_attr( $text_lighter_20 );  
				border: 1px solid esc_attr( $body_darker_10 );  
			}

			.text {
				color:  esc_attr( $text );  
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
			}

			.link {
				color:  esc_attr( $base );  
			}

			#header_wrapper {
				padding: 20px 48px 15px 48px;
				display: block;
				border-bottom: 2px solid #ef7f27 ;
				color: ##1B1919 !important;
			}
			#h1main{
				padding: 20px 48px 0px 48px;
			}
			h1 {
				color:  esc_attr( $estonian_craft_dark_head ); 
				font-family: "Aino-Regular", Helvetica Neue, Helvetica, Roboto, sans-serif;
				font-size: 30px;
				font-weight: 500;
				line-height: 150%;
				margin: 0;
				text-align:   is_rtl() ? 'right' : 'left';  
				text-shadow: 0 0px 0   esc_attr( $base_lighter_20 );  
				-webkit-font-smoothing: antialiased;
			}

			h2 {
				color:  esc_attr( $estonian_craft_dark_head ); 
				display: block;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				font-size: 18px;
				font-weight: bold;
				line-height: 130%;
				margin: 16px 0 8px;
				text-align:   is_rtl() ? 'right' : 'left';  
			}

			h3 {
				color:    esc_attr( $estonian_craft_dark_head); 
				display: block;
				font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				font-size: 16px;
				font-weight: bold;
				line-height: 130%;
				margin: 16px 0 8px;
				text-align:   is_rtl() ? 'right' : 'left'; 
			}

			a {
				color:  echo esc_attr( $base );  
				font-weight: normal;
				text-decoration: underline;
			}
			.center-block{
				margin:auto; display:block;
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
			#footer_cell {
				border-radius : 0 !important;
				display:block;
				background-color:   esc_attr($estonian_craft_orange);  
			
			#footer_text_estonian_crafts{
				background-color: esc_attr($estonian_craft_orange); 
				height:100%;
				padding-top:2px
				

			}
			#footer_text_p, #footer_text_estonian_crafts p{
				color:white;
				text-align:center;
				font-family:"Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
			}
			#ec_logo{
				width:60%;
				padding:5px 0px;
			}
			.social-nav {
			    float: left;
			    width: 100%;
			    margin: 10px 0;
			}
		</style>
		<html>
		<head></head>
		<body>
		<div id="wrapper">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<tr>
					<td align="center" valign="top">
	

						<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_container">
								<tr>
														<td colspan="2" valign="bottom" id="credit" style="	padding: 20px 48px 15px 48px; display: block; border-bottom: 2px solid #ef7f27 ;color: #1B1919 !important;" >

															<img src="http://www.m8solutions.ee/images/kasitooturg_logo-1.png" style="display:block; margin:auto; width:60%">

														</td>
													</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Header -->
									<table border="0" cellpadding="0" cellspacing="0" width="600" id="template_header">
										<tr>
											<td id="header_wrapper" >
												<h1 id="h1main" style="	color: '#1B1919' ; font-family: "Aino-Regular", Helvetica Neue, Helvetica, Roboto, sans-serif;font-size: 30px;font-weight: 500;	line-height: 150%;margin: 0;	text-align:  left;  
				text-shadow: 0 0px 0   esc_attr( $base_lighter_20 );  
				-webkit-font-smoothing: antialiased;">  $email_heading </h1>
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


															$content


																														</div>
														</td>
													</tr>
												</table>
												<!-- End Content -->
											</td>
										</tr>
									</table>
									<!-- End Body -->
								</td>
							</tr>
							<tr>
								<td align="center" valign="top">
									<!-- Footer -->
									<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
										<tr>
											<td valign="top">
												<table border="0" cellpadding="10" cellspacing="0" width="100%">
						
													<tr>
														<td colspan="2" valign="bottom" id="sm">
										                 <div class=" center-block">

												
													</div>
														

														</td>
													</tr>
													<tr>
														<td id="footer-cell"  style="	padding: 0; -webkit-border-radius: 0px;border-radius : 0 !important;
				display:block; background-color: "#ef7f27 ; ">
															<div style="	padding: 0; -webkit-border-radius: 0px;border-radius : 0 !important;
				display:block; background-color: "#ef7f27 ;height:100% " >
														<p id="footer_text_p" style="color:white;text-align:center;	font-family:"Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;" > Käsitööturg </p>
													</div>
													</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<!-- End Footer -->
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		</body>
		</html>

EOF;



?>

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
														<td id="footer-cell"  style="padding: 0; -webkit-border-radius: 0px;border-radius : 0 !important;
				display:block; background-color: '#ef7f27' " >
															<div tyle="padding: 0; -webkit-border-radius: 0px;border-radius : 0 !important;
				display:block; background-color: '#ef7f27' width:100%; height:100%" >
														<p id="footer_text_p" style='color:white;text-align:center;	font-family:"Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;'  > Käsitööturg </p>
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

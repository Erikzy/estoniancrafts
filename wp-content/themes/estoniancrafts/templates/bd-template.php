<?php
/**
 * BuddyPress email template.
 *
 * Magic numbers:
 *  1.618 = golden mean.
 *  1.35  = default body_text_size multipler. Gives default heading of 20px.
 *
 * @since 2.5.0
 *
 * @package BuddyPress
 * @subpackage Core
 */

/*
Based on the Cerberus "Fluid" template by Ted Goas (http://tedgoas.github.io/Cerberus/).
License for the original template:


The MIT License (MIT)

Copyright (c) 2013 Ted Goas

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// Exit if accessed directly.
/*defined( 'ABSPATH' ) || exit;

$settings = bp_email_get_appearance_settings();
*/
?>

<?php
/*$single = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering /
	<head>
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
																		<table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#ffffff" width="100%" style="max-width: 600px; border-radius: 5px;" class="body_bg">

														<!-- 1 Column Text : BEGIN -->
														<tr>
															<td>
																<table cellspacing="0" cellpadding="0" border="0" width="100%">
																  <tr>
																		<td style="padding: 20px; font-family: sans-serif; mso-height-rule: exactly; line-height:'12px' ; color:#ffffff; font-size: '12px' );" class="body_text_color body_text_size">
																			<span style="font-weight: bold; font-size:12px" class="welcome">bp_email_the_salutation( $settings ); </span>
																			<hr color="#fffffff"><br>
																			{{{content}}}
																		</td>
																  </tr>
																</table>
															</td>
														</tr>
														<!-- 1 Column Text : BEGIN -->

													</table>



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

EOF;*/
 
?>
<?php
/**
 * BuddyPress email template.
 *
 * Magic numbers:
 *  1.618 = golden mean.
 *  1.35  = default body_text_size multipler. Gives default heading of 20px.
 *
 * @since 2.5.0
 *
 * @package BuddyPress
 * @subpackage Core
 */

/*
Based on the Cerberus "Fluid" template by Ted Goas (http://tedgoas.github.io/Cerberus/).
License for the original template:


The MIT License (MIT)

Copyright (c) 2013 Ted Goas

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$settings = bp_email_get_appearance_settings();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
	<meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering /
	<head>
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
				-webkit-font-smoothing: antialiased;">   Message from EstonianCrafts </h1>
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
																		<table cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#ffffff" width="100%" style="max-width: 600px; border-radius: 5px;" class="body_bg">

														<!-- 1 Column Text : BEGIN -->
														<tr>
															<td>
																<table cellspacing="0" cellpadding="0" border="0" width="100%">
																  <tr>
																		<td style="padding: 20px; font-family: sans-serif; mso-height-rule: exactly; line-height:'12px' ; color:#ffffff; font-size: '12px' );" class="body_text_color body_text_size">
																			<span style="font-weight: bold; font-size:12px" class="welcome">bp_email_the_salutation( $settings ); </span>
																			<hr color="#fffffff"><br>
																			{{{content}}}
																		</td>
																  </tr>
																</table>
															</td>
														</tr>
														<!-- 1 Column Text : BEGIN -->

													</table>



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
								<td align="center" valign="top" style="background-color: #ef7f27" >
									<!-- Footer -->
									<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
										<tr>
											<td valign="top">
												<table border="0" cellpadding="10" cellspacing="0" width="100%">

													<tr>
														<td id="footer-cell"  style="padding: 0; -webkit-border-radius: 0px;border-radius : 0 !important;
				display:block; background-color: #ef7f27 " >
															<div style="padding: 0; -webkit-border-radius: 0px;border-radius : 0 !important;
				display:block; background-color: #ef7f27; width:100%; height:100%" >
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


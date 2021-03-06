<?php
/**
 * Login Form
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.0
 */
function load_in_footer()
{
?>

<div id="status" style="display:none"></div>

<?php
}
add_action('wp_footer', 'load_in_footer', 100);

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$firstName  = '';
$lastName   = '';
$regHash    = '';

$tabs = basel_get_opt( 'login_tabs' );
$text = basel_get_opt( 'reg_text' );

$class = 'basel-registration-page';

if( $tabs && get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
	$class .= ' basel-register-tabs';

    if (isset($_GET['reghash']) && strlen($_GET['reghash'])) {
        $regHashTmp = esc_sql(trim($_GET['reghash']));

        global $wpdb;
        $idcardData = $wpdb->get_row(
            $wpdb->prepare(
                "select * from $wpdb->prefix" . "idcard_users WHERE userid=0 AND reghash=%s", $regHashTmp
            )
        );
        if ($idcardData) {
            $firstName  = $idcardData->firstname;
            $lastName   = $idcardData->lastname;
            $regHash    = $idcardData->reghash;
            $class .= ' active-register';
        }
    }
}

if( $tabs && get_option( 'woocommerce_enable_myaccount_registration' ) !== 'yes' ) {
	$class .= ' basel-no-registration';
}






?>

	
<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="<?php echo esc_attr( $class ); ?>">

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="u-columns col2-set" id="customer_login">

	<div class="u-column1 col-1 col-login">

<?php endif; ?>

		<h2><?php _e( 'Login', 'woocommerce' ); ?></h2>

		<form method="post" class="login">

		<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
				<label for="username"><?php _e( 'Username or email address', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
			</p>
			<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
				<label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>
			
			<p class="woocommerce-LostPassword lost_password">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost your password?', 'woocommerce' ); ?></a>
			</p>
			
			<p class="form-row">
				<?php wp_nonce_field( 'woocommerce-login' ); ?>
				<label for="rememberme" class="inline">
					<input class="woocommerce-Input woocommerce-Input--checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember me', 'woocommerce' ); ?>
				</label>
				<input type="submit" class="woocommerce-Button button smaller-orange-button lbutton" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>" />
            </p>
            <div class='row' style="text-align:center;">
	           		<?php
	            	 	 echo do_shortcode('[ec_facebook_login_button]' ); 
	            	 	?>
	            	 
	            	<?php echo do_shortcode('[smart_id]') ?>
	            
            </div>


			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

	<div class="u-column2 col-2 col-register">

		<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>

		<form method="post" class="register">

            <?php if (strlen($regHash)) {
               echo '<input type="hidden" name="reghash" value="'.esc_attr($regHash).'">';
            } ?>

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
					<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
				</p>

			<?php endif; ?>

			<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
				<label for="reg_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
					<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" />
					
				</p>
	
			<?php endif; ?>

			<!-- Spam Trap -->
			<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php do_action( 'woocommerce_register_form' ); ?>
			<?php do_action( 'register_form' ); ?>

			<p class="woocomerce-FormRow form-row">
	
				<label for="privacy" class="inline">
						<input class="woocommerce-Input woocommerce-Input--checkbox" name="privacy" type="checkbox" id="privacy"  /> <?php printf( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">privacy  policy</a>', 'woocommerce' ), esc_url(get_site_url(null, 'privacy-policy')) ); ?>
					</label>
			
				<?php wp_nonce_field( 'woocommerce-register' ); ?>
				<input type="submit" style="display:none !important;" id="regform-submit-button" class="woocommerce-Button button smaller-orange-button lbutton" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>" />
			</p>

			 <div class='row' id="regform-submit-row" style="text-align:center;display:none;">
	           		<?php
	            	 	 echo do_shortcode('[ec_facebook_login_button]' ); 
	            	 	?>
	            	 
	            	<?php echo do_shortcode('[smart_id]') ?>
	            
            </div>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

	<?php if ( $tabs ): ?>
		<div class="col-2 col-register-text">

			<span class="register-or"><?php _e('Or', 'basel') ?></span>

			<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>

			<?php if ($text): ?>
				<div class="registration-info"><?php echo ($text); ?></div>
			<?php endif ?>

			<a href="#" class="btn btn-color-black basel-switch-to-register " data-login="<?php _e( 'Login', 'basel') ?>" data-register="<?php _e( 'Register', 'basel') ?>"><?=strlen($regHash) ?_e( 'Login', 'basel') :_e( 'Register', 'basel')?></a>

		</div>
	<?php endif ?>
	
</div>
<?php endif; ?>

</div><!-- .basel-registration-page -->

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>


<script type="text/javascript">
	jQuery("#privacy").change(function(){
		if(this.checked) {
			jQuery("#regform-submit-row").css("display","block");
			jQuery("#regform-submit-button").css("display","block");
		}else{
			jQuery("#regform-submit-row").css("display","none");
			jQuery("#regform-submit-button").css("cssText","display:none !important;");	
		}
	});

</script>

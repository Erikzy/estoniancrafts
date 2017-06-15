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

<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '<?php echo get_option('facebook_apid'); ?>',
            cookie     : true,
            xfbml      : true,
            version    : 'v2.9'
        });
/*
FB.getLoginStatus(function(response) {
console.log(response);
if(response.status === 'connected')
{
testAPI();
document.getElementById('status').innerHTML = 'we are connected';
} else if(response.status === 'not_authorized'){
document.getElementById('status').innerHTML = 'we are not authorized';
} else {
document.getElementById('status').innerHTML = 'You are not logged';
}
});
*/ 
    };


    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    
    function fblogin()
    {
        FB.login(function(response){
            console.log(response);
            if(response.status === 'connected')
            {
                AfterSuces();
                // document.getElementById('status').innerHTML = 'we are connected';
            } else if(response.status === 'not_authorized'){
                // document.getElementById('status').innerHTML = 'we are not authorized';
            } else {
                // document.getElementById('status').innerHTML = 'You are not logged';
            } 
        }, {scope: 'email'})
    }
/*
    function fblogout()
    {
        FB.logout(function(response) {
            if(response.status === 'connected')
            {
                //document.getElementById('status').innerHTML = 'we are connected';
            } else if(response.status === 'not_authorized'){
                // document.getElementById('status').innerHTML = 'we are not authorized';
            } else {
                // document.getElementById('status').innerHTML = 'You are not logged';
            }
        });
    }
*/

    function AfterSuces() {
        var ajaxUrl = "<?php echo admin_url('admin-ajax.php')?>";
        //console.log('Welcome!  Fetching your information.... ');
        var url = '/me?fields=name,email';
        FB.api(url, function(response) {
            jQuery.ajax({
                type:"POST",
                url: ajaxUrl,
                data: {
                    action: "Generate_Session",
                    username: response.name,
                    email : response.email,
                },
                success:function(data){
                    location.href = 'dashboard';
                }
            })
            //console.log(response);
            //console.log('Successful login for: ' + response.name);
            //document.getElementById('status').innerHTML = 'Thanks for logging in, ' + response.name + '!';
        });
    }

</script>
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
				<input type="submit" class="woocommerce-Button button" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>" />
                 <input type="button" class="woocommerce-Button button" onclick="fblogin()"  name="facebooklogin" value="<?php esc_attr_e( 'Login using facebook', 'woocommerce' ); ?>" />
			</p>

            <?php echo do_shortcode('[smart_id]') ?>

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
				<?php wp_nonce_field( 'woocommerce-register' ); ?>
				<input type="submit" class="woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>" />
                
                <input type="button" class="woocommerce-Button button" onclick="fblogin()"  name="facebooklogin" value="<?php esc_attr_e( 'Register using facebook', 'woocommerce' ); ?>" />
			</p>

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

			<a href="#" class="btn btn-color-black basel-switch-to-register" data-login="<?php _e( 'Login', 'basel') ?>" data-register="<?php _e( 'Register', 'basel') ?>"><?=strlen($regHash) ?_e( 'Login', 'basel') :_e( 'Register', 'basel')?></a>

		</div>
	<?php endif ?>
	
</div>
<?php endif; ?>

</div><!-- .basel-registration-page -->

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

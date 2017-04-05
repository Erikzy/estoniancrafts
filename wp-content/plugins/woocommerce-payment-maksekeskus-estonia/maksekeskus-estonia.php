<?php
/*
  Plugin Name: Woocommerce Maksekeskus Estonia
  Plugin URI: http://www.e-abi.ee/
  Description: Adds Maksekeskus Estonia Payment method to Woocommerce instance
  Version: 2.33
  Author: Matis Halmann, Aktsiamaailm LLC
  Author URI: http://www.e-abi.ee/
  Copyright: (c) Aktsiamaailm LLC
  License: Aktsiamaailm LLC License
  License URI: http://www.e-abi.ee/litsentsitingimused
 */

/*
 * 
 *  Copyright 2016 Aktsiamaailm OÜ
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  

 */

/**
 * Description of maksekeskus-estonia
 *
 * @author matishalmann
 */
if (!function_exists('is_woocommerce_active')) {
    require_once('woo-includes/woo-functions.php');
}

if (is_woocommerce_active()) {
    load_plugin_textdomain('wc_maksekeskus_estonia', false, dirname(plugin_basename(__FILE__)) . '/');

    function woocommerce_payment_banklinkmaksekeskus_estonia_init() {
        if (!class_exists('Eabi_Maksekeskus_Model_Api')) {
            require_once('includes/Api.php');
        }
        if (!class_exists('Eabi_Maksekeskus_Exception')) {
            require_once('includes/Maksekeskus_Exception.php');
        }

        /**
         * Maksekeskus Estonia Payment Gateway
         * 
         *
         * @class 		woocommerce_banklinkmaksekeskus
         * @package		WooCommerce
         * @category	Payment Gateways
         * @author		Aktsiamaailm LLC
         */
        class woocommerce_banklinkmaksekeskus extends WC_Payment_Gateway {

            public $id = 'banklinkmaksekeskus';

            /**
             * <p>transaction has been initiated</p>
             */
            const TRX_CREATED = 'CREATED';

            /**
             * <p>payment process is in progress, ie. customer has chosen to pay with a banklink and has been redirected to their bank</p>
             */
            const TRX_PENDING = 'PENDING';

            /**
             * <p>transaction has been intentionally cancelled by the customer</p>
             */
            const TRX_CANCELLED = 'CANCELLED';

            /**
             * <p>transaction was initiated, but payment was not made by the customer (expiration threshold 25 mins)</p>
             */
            const TRX_EXPIRED = 'EXPIRED';

            /**
             * <p>payment has been made by the customer, Maksekeskus/merchant action needed</p>
             */
            const TRX_APPROVED = 'APPROVED';

            /**
             * <p>payment has been made and transaction has been fulfilled</p>
             */
            const TRX_COMPLETED = 'COMPLETED';

            /**
             * <p>transaction has been partially refunded</p>
             */
            const TRX_PART_REFUNDED = 'PART_REFUNDED';

            /**
             * <p>transaction has been fully refunded</p>
             */
            const TRX_REFUNDED = 'REFUNDED';

            /**
             * <p>Payment method type is Credit card</p>
             */
            const PAYMENT_METHOD_TYPE_CARDS = 'cards';

            /**
             * <p>Payment method is offsite payment processor like PayPal</p>
             */
            const PAYMENT_METHOD_TYPE_BANKLINKS = 'banklinks';

            /**
             * <p>3d-secure notification sends CC token to Return url, this type is used in the validation result</p>
             */
            const TOKEN_RETURN = 'token_return';

            /**
             * <p>payment notification sends payment message to Return url, this type is used in the validation result</p>
             */
            const PAYMENT_RETURN = 'payment_return';

            /**
             * <p>3d-secure notification sends CC token to Return url, this type is used in the validation result</p>
             */
            const TOKEN_RETURN_SUCCESS = 'token_return-success';

            /**
             * <p>3d-secure notification sends CC token to Return url, this type is used in the validation result</p>
             */
            const TOKEN_RETURN_FAILURE = 'token_return-failure';
            
            const MODE_TEST = '_t';
            const MODE_LIVE = '_l';
            

            protected $_plugin_text_domain = 'wc_maksekeskus_estonia';
            protected $_destination_url;
            protected $_shop_id;
            protected $_locale;
            protected $_api_secret;
            protected $_currency;
            protected $_availablity;
            protected $_countries;
            protected $_paymentMethodType = self::PAYMENT_METHOD_TYPE_BANKLINKS;

            /**
             *
             * @var Eabi_Maksekeskus_Model_Api
             */
            protected $_api;
            private static $_isRequestLogged = false;
            
            protected $_MaksekeskusUrls = array(
                'checkout_js_url_l' => 'https://payment.maksekeskus.ee/checkout/dist/checkout.min.js',
                'checkout_js_url_t' => 'https://payment-test.maksekeskus.ee/checkout/dist/checkout.min.js',
                'api_url_l' => 'https://api.maksekeskus.ee/v1/',
                'api_url_t' => 'https://api-test.maksekeskus.ee/v1/',
            );

            /**
             *
             * @var WC_Logger
             */
            private static $log;

            public function __construct() {

                $this->icon = apply_filters('woocommerce_' . $this->id . '_icon', plugins_url('/assets/images/icons/logo_maksekeskus.png', __FILE__));
                $this->has_fields = false;

                // Load the form fields.
                $this->init_form_fields();

                // Load the settings.
                $this->init_settings();
                

                // Define user set variables
                $this->title = $this->get_option('title');
                $this->description = $this->get_option('description');

                $this->_shop_id = $this->get_option('shop_id');
                $this->_currency = $this->get_option('currency');
                $this->_availablity = $this->get_option('availability');
                $this->_countries = $this->get_option('countries');
                $this->_locale = $this->get_option('locale');
                $this->_api = new Eabi_Maksekeskus_Model_Api();
                $this->_api->setMethodData($this);
                $this->has_fields = true;
                $this->supports = array(
                    'products',
                    'refunds',
                );
                
                add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));

                // Actions
                add_action('init', array(&$this, 'check_banklink_response'));
                add_action('woocommerce_api_' . strtolower(get_class($this)), array($this, 'check_banklink_response'));
                add_action('valid-' . $this->id . '-request', array(&$this, 'successful_request'));
                add_action('woocommerce_receipt_' . $this->id, array(&$this, 'receipt_page'));
                
                add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));
                



                if (!$this->is_valid_for_use())
                    $this->enabled = false;
            }

            /**
             * <p>Returns true if WooCommerce is saving any of its setting and <code>$current_section</code> is set and request method is post</p>
             * @global string $current_section
             * @return boolean
             */
            public function isSavingCurrentSection() {
                global $current_section;

                if (is_admin() && isset($current_section) && $current_section && isset($_POST) && count($_POST)) {
                    return true;
                }
                return false;
            }

            /**
             * Check if this gateway is enabled and available in the user's country
             */
            function is_valid_for_use() {

                return true;
            }

            /**
             * <p>Returns true when this payment method should be available.</p>
             * <p>On API mode creates test query to Maksekeskus to verify that API is up and alive</p>
             * @return boolean
             */
            public function is_available() {
                if ($this->enabled == "yes") {

                    if ($this->_getWooCommerce()->customer) {
                        
                        
                        /* @var $customer WC_Customer */
                        $customer = $this->_getWooCommerce()->customer;
                        if ($this->_availablity == 'specific') {
                            if (!in_array($this->_getPreferredCountry(), $this->_countries)) {
                                return false;
                            }
                        }
                    }

                    if ($this->_api) {
                        try {
                            //when api down, return method as not available
                            //test with 1 eur

                            if ($this->get_option('disable_other_currency') === 'yes') {
                                if (get_option('woocommerce_currency') != $this->_currency) {
                                    //disable when quote currency is not EUR
                                    return false;
                                }
                            }
                            $order = false;
                            if ($order_id = get_query_var('order-pay')) {
                                $order = new WC_Order($order_id);
                            }


                            $allowedMethods = $this->_getAvailablePaymentMethods(false, $this->_paymentMethodType, $order);
                            if (!count($allowedMethods)) {
                                return false;
                            }
                        } catch (Exception $ex) {
                            return false;
                        }
                    }


                    return true;
                }
            }

            /**
             * Admin Panel Options 
             * - Options for bits like 'title' and availability on a country-by-country basis
             *
             * @since 1.0.0
             */
            public function admin_options() {
                ?>
                <h3><?php _e('Maksekeskus Estonia payment gateway', $this->_plugin_text_domain); ?></h3>
                <p><?php _e('Clients can pay for their order using all possible payment options in Estonia', $this->_plugin_text_domain); ?></p>
                <table class="form-table">
                <?php
                if ($this->is_valid_for_use()) :
                    

                    // Generate the HTML For the settings form.
                    $this->init_form_fields();
//                endif;
                    $this->generate_settings_html();

                else :
                    ?>
                        <div class="inline error"><p><strong><?php _e('Gateway Disabled', $this->_plugin_text_domain); ?></strong>: <?php _e('There has been an error processing Maksekeskus payment gateway.', $this->_plugin_text_domain); ?></p></div>
                    <?php
                    endif;
                    ?>
                </table><!--/.form-table-->
                    <?php
                }

// End admin_options()

                /**
                 * Initialise Gateway Settings Form Fields
                 */
                function init_form_fields() {
                    
                    $connectionModes = array(
                        self::MODE_LIVE => __('Live mode', $this->_plugin_text_domain),
                        self::MODE_TEST => __('Test mode', $this->_plugin_text_domain),
                    );

                    $this->form_fields = array(
                        'enabled' => array(
                            'title' => __('Enable/Disable', $this->_plugin_text_domain),
                            'type' => 'checkbox',
                            'label' => __('Enable Maksekeskus payment module', $this->_plugin_text_domain),
                            'default' => 'no'
                        ),
                        'enabled_cc' => array(
                            'title' => __('Enable/Disable Credit card payments', $this->_plugin_text_domain),
                            'type' => 'checkbox',
                            'label' => __('Enable Credit card payments', $this->_plugin_text_domain),
                            'default' => 'yes'
                        ),
                        'title' => array(
                            'title' => __('Title', $this->_plugin_text_domain),
                            'type' => 'text',
                            'description' => __('This controls the title which the user sees during checkout.', $this->_plugin_text_domain),
                            'default' => __('Tasun pangalingiga', $this->_plugin_text_domain)
                        ),
                        'title_cc' => array(
                            'title' => __('Title for Credit card payment', $this->_plugin_text_domain),
                            'type' => 'text',
                            'description' => __('This controls the title which the user sees during checkout.', $this->_plugin_text_domain),
                            'default' => __('Tasun krediitkaardiga', $this->_plugin_text_domain),
                        ),
                        'description' => array(
                            'title' => __('Description', $this->_plugin_text_domain),
                            'type' => 'textarea',
                            'description' => __('This controls the description which the user sees during checkout.', $this->_plugin_text_domain),
                            'default' => __("", $this->_plugin_text_domain)
                        ),
                        'description_cc' => array(
                            'title' => __('Description for credit card payment', $this->_plugin_text_domain),
                            'type' => 'textarea',
                            'description' => __('This controls the description which the user sees during checkout.', $this->_plugin_text_domain),
                            'default' => __("", $this->_plugin_text_domain)
                        ),
                        'connection_mode' => array(
                            'title' => __('Payment method mode', $this->_plugin_text_domain),
                            'type' => 'select',
                            'options' => $connectionModes,
                            'default' => self::MODE_LIVE,
                            'description' => sprintf("<a href=\"%s\" target=\"_blank\">%s</a>",'https://makecommerce.net/en/for-developers/test-environment/', __('What is test mode?', $this->_plugin_text_domain)),
                        ),
                        'shop_id' => array(
                            'title' => __('Shop ID', $this->_plugin_text_domain),
                            'type' => 'text',
                            'description' => sprintf(__('Maksekeskus provides you with %s.', $this->_plugin_text_domain), __('Shop ID', $this->_plugin_text_domain)),
                            'default' => ''
                        ),
                        'api_secret_l' => array(
                            'title' => __('API secret for live account', $this->_plugin_text_domain),
                            'type' => 'text',
                            'description' => sprintf("<a href=\"%s\" target=\"_blank\">%s</a>",'https://merchant.maksekeskus.ee/', __('Live account credentials can be obtained here', $this->_plugin_text_domain)),
                            'default' => $this->get_option('api_secret'), 
                        ),
                        'api_public_l' => array(
                            'title' => __('API public for live account', $this->_plugin_text_domain),
                            'type' => 'text',
                            'default' => $this->get_option('api_public'), 
                        ),
                        'api_secret_t' => array(
                            'title' => __('API secret for test account', $this->_plugin_text_domain),
                            'type' => 'text',
                            'description' => sprintf("<a href=\"%s\" target=\"_blank\">%s</a>",'https://merchant-test.maksekeskus.ee/', __('Test account credentials can be obtained here', $this->_plugin_text_domain)),
                            'default' => $this->get_option('api_secret'), 
                        ),
                        'api_public_t' => array(
                            'title' => __('API public for test account', $this->_plugin_text_domain),
                            'type' => 'text',
                            'default' => $this->get_option('api_public'), 
                        ),
                        
                        'prefill_data' => array(
                            'title' => __('Pre-fill customer details on the credit card form', $this->_plugin_text_domain),
                            'type' => 'checkbox',
                            'label' => __('Pre-fill customer details on the credit card form', $this->_plugin_text_domain),
                            'default' => 'yes'
                        ),
                        'shop_name' => array(
                            'title' => __('Shop name on credit card form', $this->_plugin_text_domain),
                            'type' => 'text',
                            'default' => ''
                        ),
                        'shop_description' => array(
                            'title' => __('Order description displayed under shop name', $this->_plugin_text_domain),
                            'description' => __('%s will be replaced with order increment id or with quote reserved order id or with quote id', $this->_plugin_text_domain),
                            'type' => 'text',
                            'default' => '%s'
                        ),
                        
                        'locale' => array(
                            'title' => __('Preferred locale', $this->_plugin_text_domain),
                            'type' => 'text',
                            'description' => __('RFC-2616 format locale. Like et,en,ru', $this->_plugin_text_domain),
                            'default' => 'et'
                        ),
                        
                        
                        
                        'method_logo_size' => array(
                            'title' => __('Payment option logo size at checkout', $this->_plugin_text_domain),
                            'type' => 'select',
                            'description' => __('Large logo is 60x60px with 12px padding, small logo is 48x48px (scaled) with 2px padding', $this->_plugin_text_domain),
                            'default' => 'small',
                            'class' => 'availability',
                            'options' => array(
                                'small' => __('Small', $this->_plugin_text_domain),
                                'large' => __('Large', $this->_plugin_text_domain)
                            )
                        ),
                        'currency' => array(
                            'title' => __('Accepted currency by this gateway', $this->_plugin_text_domain),
                            'type' => 'select',
                            'description' => __('Other currencies will be converted to accepted currency', $this->_plugin_text_domain),
                            'options' => get_woocommerce_currencies(),
                            'default' => 'EUR'
                        ),
                        'disable_other_currency' => array(
                            'title' => __('Disable this method, when order currency is not EUR', $this->_plugin_text_domain),
                            'type' => 'checkbox',
                            'label' => __('Conversion will be attempted when this method is not disabled', $this->_plugin_text_domain),
                            'default' => 'yes'
                        ),
                        
                        'availability' => array(
                            'title' => __('Method availability', $this->_plugin_text_domain),
                            'type' => 'select',
                            'default' => 'all',
                            'class' => 'availability',
                            'options' => array(
                                'all' => __('All allowed countries', $this->_plugin_text_domain),
                                'specific' => __('Specific Countries', $this->_plugin_text_domain)
                            )
                        ),
                        'countries' => array(
                            'title' => __('Specific Countries', $this->_plugin_text_domain),
                            'type' => 'multiselect',
                            'class' => 'chosen_select',
                            'css' => 'width: 450px;',
                            'default' => array('EE', 'LV', 'LT', 'FI'),
                            'options' => $this->_getWooCommerce()->countries->countries
                        ),
                        'enable_log' => array(
                            'title' => __('Log API requests', $this->_plugin_text_domain),
                            'type' => 'checkbox',
                            'label' => __('Log API requests', $this->_plugin_text_domain),
                            'default' => 'no'
                        ),
                    );
                }

// End init_form_fields()

                /**
                 * There are no payment fields for Maksekeskus banklink, but we want to show the description if set.
                 * */
                function payment_fields() {
                    if (!$this->_api) {
                        if ($this->description) {
                            echo wpautop(wptexturize(__($this->description, $this->_plugin_text_domain)));
                        }
                    } else {

                        $transactionKey = false;
                        $order = false;

                        if ($order_id = get_query_var('order-pay')) {
                            $order = new WC_Order($order_id);
                            $transactionKey = $this->_initializeTransactionKey(false, $order);
                        }
                        $allowedMethods = $this->_getAvailablePaymentMethods($transactionKey, $this->_paymentMethodType, $order);
                        ?>
                    <?php if ($this->_paymentMethodType === self::PAYMENT_METHOD_TYPE_CARDS) : ?>
                        <?php
                        if ($this->get_option('description_cc')) {
                            echo wpautop(wptexturize(__($this->get_option('description_cc'), $this->_plugin_text_domain)));
                        }

                    endif;
                    ?>
                    <?php if ($this->_paymentMethodType === self::PAYMENT_METHOD_TYPE_BANKLINKS) : ?>
                        <?php
                        if ($this->get_option('description')) {
                            echo wpautop(wptexturize(__($this->get_option('description'), $this->_plugin_text_domain)));
                        }
                    endif;



                    if ($this->_paymentMethodType === self::PAYMENT_METHOD_TYPE_BANKLINKS || true) :
                        ?>
                        <ul class="maksekeskus-form-list" id="payment_form_<?php echo $this->id; ?>" >
                            <li>
                                <div id="maksekeskus_wrapper_<?php echo $this->id ?>" class="input-box eabi_maksekeskus_select">
                                    <select name="PRESELECTED_METHOD_<?php echo $this->id ?>" id="maksekeskus_selector_<?php echo $this->id ?>">
                                        <option value=""> -- please select -- </option>
                        <?php foreach ($allowedMethods as $allowedMethod): ?>
                                            <option value="<?php echo $allowedMethod['value']; ?>"
                                                    data-type="<?php echo $allowedMethod['type']; ?>"
                                            <?php
                                            if ($allowedMethod['image']) {
                                                echo 'data-image="' . $allowedMethod['image'] . '"';
                                            }
                                            ?>
                                                    <?php
                                                    if ($allowedMethod['selected']) {
                                                        echo ' selected="selected"';
                                                    }
                                                    ?>
                                                    ><?php echo htmlspecialchars($allowedMethod['title']); ?></option>
                                                <?php endforeach; ?>
                                    </select>
                                </div>
                            </li>
                        </ul>

                        <script type="text/javascript">
                            /* <![CDATA[ */

                            (function() {
                                function eabi_imageselector(wrapperDom, selectDom, allowModalOpen, autoClickSelected, initParams, checkoutJsUrl, ulCssClassName, $) {
                                    /*get the elements from the selectDom*/
                                    var selectOptions = $(selectDom).find('option'),
                                            listElements = [],
                                            ul = $('<ul>'),
                                            elToClick;

                                    selectOptions.each(function(index, elem) {
                                        var li = $('<li>'),
                                                a = $('<a>'),
                                                img = $('<img>'),
                                                title = $('<span>'),
                                                item = $(elem),
                                                id = 'maksekeskus_' + item.attr('value');

                                        if (item.attr('value')) {
                                            a.attr('href', '#');
                                            a.attr('id', id);
                                            a.attr('title', item.text());

                                            a.attr('onclick', 'jQuery(\'' + wrapperDom + '\').find(\'a\').each(function(index, item) { jQuery(item).removeClass(\'selected\'); }); jQuery(\'' + selectDom + '\').val(\'' + item.attr('value') + '\'); jQuery(this).addClass(\'selected\'); return false;');
                                            if (item.attr('data-type') === 'cards' && allowModalOpen) {
                                                a.attr('onclick', 'jQuery(\'' + wrapperDom + '\').select(\'a\').each(function(index, item) { jQuery(item).removeClass(\'selected\');}); jQuery(\'' + selectDom + '\').val(\'' + item.attr('value') + '\'); jQuery(this).addClass(\'selected\'); Maksekeskus.Checkout.showModal(); return false;');
                                            }

                                            if (item.attr('selected') && item.attr('selected') === 'selected') {
                                                a.addClass('selected');
                                                if (autoClickSelected) {
                                                    elToClick = a;
                                                }
                                            }

                                            img.attr('alt', a.attr('title'));
                                            if (item.attr('data-image')) {
                                                img.attr('src', item.attr('data-image'));
                                                a.append(img);

                                            } else {
                                                title.html(a.attr('title'));
                                                a.append(title);
                                            }
                                            li.append(a);
                                            listElements.push(li);
                                        }
                                        $.each(listElements, function(index, item) {
                                            ul.append(item);
                                        });
                                        if (ulCssClassName) {
                                            ul.addClass(ulCssClassName);
                                        }
                                        $(wrapperDom).append(ul);

                                    });
                                    $(selectDom).hide();

                                    if (initParams) {
                                        if (!window.Maksekeskus) {
                                            (function() {
                                                var js = $('<script>');
                                                js.attr('type', 'text/javascript');
                                                js.attr('src', checkoutJsUrl);
                                                $('body').append(js);
                                            })();
                                        }
                                        if (window.Maksekeskus) {
                                            if (!window['eabi_maksekeskus_js_inited']) {
                                                Maksekeskus.Checkout.initialize(initParams);
                                                Maksekeskus.Checkout.renderButton();
                                                window['eabi_maksekeskus_js_inited'] = true;
                                            }
                                            Maksekeskus.Checkout.extendOptions(initParams, 'init');
                                        }

                                    }

                                    if (elToClick) {
                                        elToClick.click();
                                    }


                                }
                                eabi_imageselector('#maksekeskus_wrapper_<?php echo $this->id ?>', '#maksekeskus_selector_<?php echo $this->id ?>', <?php echo json_encode(false) ?>, <?php echo json_encode(false) ?>, <?php echo json_encode(false); ?>, <?php echo json_encode($this->getCheckoutJsUrl()); ?>, <?php echo json_encode($this->_getUlCssClassName()); ?>, jQuery);

                            })();

                            /* ]]> */
                        </script>
                        <?php
                    else:
                        if ($this->get_option('description_cc')) {
                            echo wpautop(wptexturize(__($this->get_option('description_cc'), $this->_plugin_text_domain)));
                        } else {
                            return false;
                        }
                    endif;
                }
            }

            /**
             * payment_scripts function.
             *
             * Outputs scripts used for simplify payment
             */
            public function payment_scripts() {
                if (!is_checkout() || !$this->is_available()) {
//                    return;
                }
//                plugins_url('/assets/css/eabi_maksekeskuys.css', __FILE__)
//                wp_enqueue_script('maksekeskus-api-complete', plugins_url('/assets/js/eabi_maksekeskus.js', __FILE__), array('jquery'), WC_VERSION, true);
                wp_enqueue_style('maksekeskus-api', plugins_url('/assets/css/eabi_maksekeskus.css', __FILE__));
            }

            /**
             * <p>Fetches Checkout JS URL initialization parameters for specified Order</p>
             * <p>Returns boolean false, if API mode is disabled or parameters could not be fetched</p>
             * @param WC_Order $order
             * @return array|bool
             */
            protected function _getOrderJsInitParams($order, $transaction) {
                if (!$this->_api) {
                    return false;
                }
                $grandTotal = $order->order_total;
                $currency = get_option('woocommerce_currency');
                if (!in_array($currency, $this->_getAllowedCurrencies())) {
                    $grandTotal = $this->_toTargetAmount($grandTotal, $currency);
                    $currency = $this->_currency;
                }
                $idReference = $order->id;
//                $transaction = $this->_initializeTransactionKey(false, $order);
                $params = array(
//                    'key' => $this->get_option('api_public'),
                    'key' => $this->get_option('api_public' . $this->get_option('connection_mode')),
                    'transaction' => $transaction,
                    'selector' => '',
                    'completed' => 'eabi_maksekeskus_datacompleted',
                    'name' => (string) $this->get_option('shop_name'),
                    'noConflict' => false,
                    'description' => (string) sprintf($this->get_option('shop_description'), (string) $idReference),
                    'amount' => $grandTotal,
                    'locale' => $this->_getPreferredLocale(),
                );


                $clientName = $order->billing_first_name . ' ' . $order->billing_last_name;
                $isOrderWaitingPayment = false;
                if (isset($this->_getWooCommerce()->session) && isset($this->_getWooCommerce()->session->order_awaiting_payment)
                        && (string)$this->_getWooCommerce()->session->order_awaiting_payment === (string)$order->id) {
                    $isOrderWaitingPayment = true;
                }
               
                if ($this->get_option('prefill_data') == 'yes' && $isOrderWaitingPayment) {
                    $params['clientName'] = (string) $clientName;
                    $params['email'] = (string) $order->billing_email;
                }
                return $params;
            }

            /**
             * <p>Fetches Checkout JS API initialization parameters for specified Payment Methods</p>
             * <p>Returns boolean false if API mode is disabled</p>
             * @param array $allowedMethods
             * @return boolean|array
             */
            protected function _getJsInitParams($allowedMethods) {
                if (!$this->_api) {
                    return false;
                }
                $selector = '';
                foreach ($allowedMethods as $availablePaymentMethod) {
                    if ($availablePaymentMethod['type'] === self::PAYMENT_METHOD_TYPE_CARDS) {
                        $selector = '#maksekeskus_' . $availablePaymentMethod['value'];
                        break;
                    }
                }
                $cart = $this->_getWooCommerce()->cart;

                $grandTotal = $cart->total;
                $currency = get_option('woocommerce_currency');
                if (!in_array($currency, $this->_getAllowedCurrencies())) {
                    $grandTotal = $this->_toTargetAmount($grandTotal, $currency);
                    $currency = $this->_currency;
                }
                $idReference = $this->_initializeTransactionKey();

                $params = array(
                    'key' => $this->get_option('api_public' . $this->get_option('connection_mode')),
                    'transaction' => $idReference,
                    'selector' => $selector,
                    'completed' => 'eabi_maksekeskus_datacompleted',
                    'name' => (string) $this->get_option('shop_name'),
                    'noConflict' => false,
                    'description' => (string) sprintf($this->get_option('shop_description'), (string) $idReference),
                    'amount' => (string) $grandTotal,
                    'locale' => $this->_getPreferredLocale(),
                );


                $clientName = '';
                $customerEmail = '';
                if ($this->get_option('prefill_data') == 'yes') {
                    $params['clientName'] = (string) $clientName;
                    $params['email'] = (string) $customerEmail;
                }
                return $params;
            }

            /**
             * <p>Returns additional CSS class name to indicate that small logo size should be used.</p>
             * <p>Returns <code>maksekeskus_small</code> when small logo size is declared</p>
             * <p>Otherwise boolean false is returned</p>
             * @return string|boolean
             */
            protected function _getUlCssClassName() {
                if ($this->get_option('method_logo_size') == 'small') {
                    return 'maksekeskus_small';
                }
                return false;
            }

            /**
             * <p>Fetches all available payment methods for current order</p>
             * <p>If <code>$transactionId</code> is not supplied, then it is created and made sure, that it would always be in CREATED state</p>
             * <p>If <code>$transactionId</code> is supplied, then this transaction is used irrelevant of it's status</p>
             * @param string $transactionId
             * @param WC_Order $order
             * @return array
             */
            protected function _getAvailablePaymentMethods($transactionId = false, $paymentTypeFilter = false, $order = null) {
                $allDescribedMethods = @unserialize('a:17:{s:15:"_1408407535_501";a:4:{s:6:"active";s:1:"1";s:5:"value";s:8:"swedbank";s:5:"title";s:25:"Pay from Swedbank account";s:5:"image";s:35:"media/eabi_maksekeskus/swedbank.png";}s:15:"_1408407536_164";a:4:{s:6:"active";s:1:"1";s:5:"value";s:3:"seb";s:5:"title";s:20:"Pay from SEB account";s:5:"image";s:30:"media/eabi_maksekeskus/seb.png";}s:15:"_1408407537_739";a:4:{s:6:"active";s:1:"1";s:5:"value";s:3:"lhv";s:5:"title";s:20:"Pay from LHV account";s:5:"image";s:30:"media/eabi_maksekeskus/lhv.png";}s:15:"_1408407537_273";a:4:{s:6:"active";s:1:"1";s:5:"value";s:6:"nordea";s:5:"title";s:23:"Pay from Nordea account";s:5:"image";s:33:"media/eabi_maksekeskus/nordea.png";}s:15:"_1408407538_627";a:4:{s:6:"active";s:1:"1";s:5:"value";s:6:"danske";s:5:"title";s:27:"Pay from Danskebank account";s:5:"image";s:33:"media/eabi_maksekeskus/danske.png";}s:15:"_1408407539_230";a:4:{s:6:"active";s:1:"1";s:5:"value";s:12:"krediidipank";s:5:"title";s:29:"Pay from Krediidipank account";s:5:"image";s:39:"media/eabi_maksekeskus/krediidipank.png";}s:15:"_1408407540_414";a:4:{s:6:"active";s:1:"1";s:5:"value";s:4:"visa";s:5:"title";s:13:"Pay with Visa";s:5:"image";s:31:"media/eabi_maksekeskus/visa.png";}s:15:"_1408407541_244";a:4:{s:6:"active";s:1:"1";s:5:"value";s:10:"mastercard";s:5:"title";s:19:"Pay with MasterCard";s:5:"image";s:37:"media/eabi_maksekeskus/mastercard.png";}s:15:"_1410994055_514";a:4:{s:6:"active";s:1:"1";s:5:"value";s:8:"citadele";s:5:"title";s:25:"Pay from Citadele account";s:5:"image";s:35:"media/eabi_maksekeskus/citadele.png";}s:15:"_1417633675_438";a:4:{s:6:"active";s:1:"1";s:5:"value";s:7:"maestro";s:5:"title";s:16:"Pay with Maestro";s:5:"image";s:34:"media/eabi_maksekeskus/maestro.png";}s:15:"_1426257424_818";a:4:{s:6:"active";s:1:"1";s:5:"value";s:7:"pohjola";s:5:"title";s:24:"Pay from Pohjola account";s:5:"image";s:34:"media/eabi_maksekeskus/pohjola.png";}s:15:"_1426257424_941";a:4:{s:6:"active";s:1:"1";s:5:"value";s:7:"tapiola";s:5:"title";s:24:"Pay from Tapiola account";s:5:"image";s:34:"media/eabi_maksekeskus/tapiola.png";}s:15:"_1426257424_276";a:4:{s:6:"active";s:1:"1";s:5:"value";s:12:"alandsbanken";s:5:"title";s:30:"Pay from Ålandsbanken account";s:5:"image";s:39:"media/eabi_maksekeskus/alandsbanken.png";}s:15:"_1426257424_667";a:4:{s:6:"active";s:1:"1";s:5:"value";s:13:"handelsbanken";s:5:"title";s:30:"Pay from Handelsbanken account";s:5:"image";s:40:"media/eabi_maksekeskus/handelsbanken.png";}s:15:"_1473925442_309";a:4:{s:6:"active";s:1:"1";s:5:"value";s:7:"spankki";s:5:"title";s:25:"Pay from S-Pankki account";s:5:"image";s:34:"media/eabi_maksekeskus/spankki.png";}s:15:"_1473925647_761";a:4:{s:6:"active";s:1:"1";s:5:"value";s:7:"pocopay";s:5:"title";s:16:"Pay with Pocopay";s:5:"image";s:34:"media/eabi_maksekeskus/pocopay.png";}s:15:"_1477937532_472";a:4:{s:6:"active";s:1:"1";s:5:"value";s:12:"saastopankki";s:5:"title";s:23:"Pay with Säästopankki";s:5:"image";s:39:"media/eabi_maksekeskus/saastopankki.png";}}');
                $result = array();
                if (!is_array($allDescribedMethods)) {
                    $allDescribedMethods = array();
                }

                $cart = $this->_getWooCommerce()->cart;
                $customer = $this->_getWooCommerce()->customer;
                /* @var $cart WC_Cart */
//                $cart->

                $baseUrl = plugins_url('/assets/images/icons/', __FILE__);
                $grandTotal = $cart->total;
                if ($order) {
                    $grandTotal = $order->order_total;
                }
                $currency = get_option('woocommerce_currency');
                if (!in_array($currency, $this->_getAllowedCurrencies())) {
                    $grandTotal = $this->_toTargetAmount($grandTotal, $currency);
                    $currency = $this->_currency;
                }
                if ($grandTotal <= 0) {
                    return $result;
                }

                if ($transactionId) {
                    $paymentMethods = $this->_api->getMethodsForTransaction($transactionId);
                } else {
//                    $paymentMethods = $this->_api->methods($grandTotal, $currency, $this->_getPreferredCountry());
                    $paymentMethods = $this->getPaymentMethodsCached($this, $this->_api, $grandTotal, $currency, $this->_getPreferredCountry($order));
                }
//                $paymentMethods = array('banklinks' => array(), 'cards' => array());
                $selectedMethod = $this->_getWooCommerce()->session->eabi_maksekeskus_preselected_method;

                foreach ($allDescribedMethods as $describedMethod) {
                    if (isset($describedMethod['active']) && $describedMethod['active'] && $this->_containsMethod($describedMethod['value'], $paymentMethods)) {
                        $describedMethod['image'] = $describedMethod['image'] ? $baseUrl . $describedMethod['image'] : '';
                        $describedMethod['selected'] = $describedMethod['value'] === $selectedMethod ? true : false;
                        $describedMethod['type'] = $this->_getPaymentMethodType($describedMethod['value'], $paymentMethods);
                        $describedMethod['data'] = $this->_getPaymentMethodData($describedMethod['value'], $paymentMethods);

                        if ($paymentTypeFilter) {
                            if ($paymentTypeFilter == $describedMethod['type']) {
                                $result[$describedMethod['value']] = $describedMethod;
                            }
                        } else {
                            $result[$describedMethod['value']] = $describedMethod;
                        }
                    }
                }
                //handle payment methods, which are not described
                $this->_appendNotDescribedMethods($result, $paymentMethods, $selectedMethod, $paymentTypeFilter);

                //sort methods in a way, that credit cards are always on last place
                $this->mergesort($result, array(&$this, '_sortAvailablePaymentMethods'));

                return $result;
            }
            

            /**
             * 
             * @param woocommerce_banklinkmaksekeskus $method
             * @param Eabi_Maksekeskus_Model_Api $api
             * @param double $grandTotal
             * @param string $currency
             * @param string $countryId
             */
            public function getPaymentMethodsCached($method, $api, $grandTotal, $currency, $countryId) {
                
                $normalizedPaymentMethods = $this->_getNormalizedPaymentMethods($method, $api);
                $paymentMethods = $this->_getPaymentMethodsForCountry($normalizedPaymentMethods, $countryId);
                
                
                
                
                return $paymentMethods;
                
            }
            
    /**
     * 
             * @param woocommerce_banklinkmaksekeskus $method
             * @param Eabi_Maksekeskus_Model_Api $api
     * @return array
     */
    protected function _getNormalizedPaymentMethods($method, $api) {

        $cachedPaymentMethods = @json_decode(@gzuncompress(@base64_decode(get_option('payment/eabi_maksekeskus/cached_methods', 'false'))), true);
        $cachedStamp = get_option('payment/eabi_maksekeskus/cached_stamp', 0);
        $cacheDiff = get_option('payment/eabi_maksekeskus/cache_diff', 86400);
        $now = time();
        if (!$cachedPaymentMethods || !is_array($cachedPaymentMethods) || $cachedStamp + $cacheDiff < $now) {
            $configPaymentMethods = $api->shopConfiguration();

            //TODO: caching
            $cachedPaymentMethods = $this->_normalizePaymentMethods($configPaymentMethods['paymentMethods']);

            //put to cache
            update_option('payment/eabi_maksekeskus/cached_methods', base64_encode(gzcompress(json_encode($cachedPaymentMethods))));
            update_option('payment/eabi_maksekeskus/cached_stamp', $now);
//            $this->setConfigData('payment/eabi_maksekeskus/cached_methods', base64_encode(gzcompress(json_encode($cachedPaymentMethods))), 'stores', $method->getStore());
//            $this->setConfigData('payment/eabi_maksekeskus/cached_stamp', $now, 'stores', $method->getStore(), true);
            
        }

        return $cachedPaymentMethods;
    }
    
    /**
     * 
     * @param type $paymentMethods
     * @return array
     */
            protected function _normalizePaymentMethods($paymentMethods) {
                $normalizedPaymentMethods = array();
                foreach ($paymentMethods['banklinks'] as $paymentMethod) {

                    //init payment methods array for destined country
                    if (!isset($normalizedPaymentMethods[$paymentMethod['country']])) {
                        $normalizedPaymentMethods[$paymentMethod['country']] = array(
                            'banklinks' => array(),
                            'cards' => $paymentMethods['cards'],
                            'cash' => $paymentMethods['cash'],
                            'other' => $paymentMethods['other'],
                        );
                    }

                    //append banklinks to their appropriate slots
                    $normalizedPaymentMethods[$paymentMethod['country']]['banklinks'][] = $paymentMethod;
                }

                //finally add '*' country
                $normalizedPaymentMethods['*'] = array(
                    'banklinks' => array(),
                    'cards' => $paymentMethods['cards'],
                    'cash' => $paymentMethods['cash'],
                    'other' => $paymentMethods['other'],
                );

                return $normalizedPaymentMethods;
            }

            protected function _getPaymentMethodsForCountry($normalizedPaymentMethods, $country) {
                //country may not be NULL
                $country = strtolower($country);
                if (isset($normalizedPaymentMethods[$country])) {
                    return $normalizedPaymentMethods[$country];
                }
                //return normalizedPaymentMethods for all countries
                return $normalizedPaymentMethods['*'];
            }

            /**
             * <p>Sorting callback function based on element['type'], marks banklinks as first and creditcards as last.</p>
             * @param type $a
             * @param type $b
             * @return int
             */
            public function _sortAvailablePaymentMethods($a, $b) {
                if (!$b) {
                    return -1;
                }

                if ($a['type'] == $b['type']) {
                    return 0;
                }
                if ($a['type'] == self::PAYMENT_METHOD_TYPE_CARDS) {
                    return 1;
                }
                if ($b['type'] == self::PAYMENT_METHOD_TYPE_CARDS) {
                    return -1;
                }
                return 0;
            }

            /**
             * <p>Sorts array by maintaining the original order of elements, when they are not sorted</p>
             * @param array $array
             * @param callback $cmp_function
             * @return void
             */
            public function mergesort(&$array, $cmp_function = 'strcmp') {
                if (count($array) < 2) {
                    return;
                }
                $halfway = count($array) / 2;
                $array1 = array_slice($array, 0, $halfway, TRUE);
                $array2 = array_slice($array, $halfway, NULL, TRUE);

                $this->mergesort($array1, $cmp_function);
                $this->mergesort($array2, $cmp_function);
                if (call_user_func($cmp_function, end($array1), reset($array2)) < 1) {
                    $array = $array1 + $array2;
                    return;
                }
                $array = array();
                reset($array1);
                reset($array2);
                while (current($array1) && current($array2)) {
                    if (call_user_func($cmp_function, current($array1), current($array2)) < 1) {
                        $array[key($array1)] = current($array1);
                        next($array1);
                    } else {
                        $array[key($array2)] = current($array2);
                        next($array2);
                    }
                }
                while (current($array1)) {
                    $array[key($array1)] = current($array1);
                    next($array1);
                }
                while (current($array2)) {
                    $array[key($array2)] = current($array2);
                    next($array2);
                }
                return;
            }

            /**
             * <p>Returns array of allowed currency codes for this payment method</p>
             * @return array
             */
            protected function _getAllowedCurrencies() {
                $allowedCurrencies = array_filter(array_map('trim', explode(',', 'EUR')));
                return $allowedCurrencies;
            }

            /**
             * <p>Finds out payment methods from Maksekeskus, which are not described in the store and prepares them to be entered into described payment methods</p>
             * <p>State of newly discovered payment method depends if new payment methods are displayed to the customers right away or not</p>
             * @param array $methods described payment methods
             * @param array $actualAvailableMethods payment methods from Maksekeskus API
             * @param string $selectedMethod currently selected payment method by the customer
             * @see Eabi_Maksekeskus_Block_Adminhtml_Config_Form_Field_Method_Detail
             */
            protected function _appendNotDescribedMethods(&$methods, $actualAvailableMethods, $selectedMethod = null, $paymentTypeFilter = null) {
                foreach ($actualAvailableMethods['banklinks'] as $banklink) {
                    if (!isset($methods[$banklink['name']])) {
                        $selected = $banklink['name'] === $selectedMethod ? true : false;
                        if ($paymentTypeFilter) {
                            if ($paymentTypeFilter == self::PAYMENT_METHOD_TYPE_BANKLINKS) {
                                $this->_appendNotDescribedMethod($methods, $banklink, self::PAYMENT_METHOD_TYPE_BANKLINKS, $selected);
                            }
                        } else {
                            $this->_appendNotDescribedMethod($methods, $banklink, self::PAYMENT_METHOD_TYPE_BANKLINKS, $selected);
                        }
                    }
                }
                foreach ($actualAvailableMethods['cards'] as $card) {
                    if (!isset($methods[$card['name']])) {
                        $selected = $card['name'] === $selectedMethod ? true : false;
                        if ($paymentTypeFilter) {
                            if ($paymentTypeFilter == self::PAYMENT_METHOD_TYPE_CARDS) {
                                $this->_appendNotDescribedMethod($methods, $card, self::PAYMENT_METHOD_TYPE_CARDS, $selected);
                            }
                        } else {
                            $this->_appendNotDescribedMethod($methods, $card, self::PAYMENT_METHOD_TYPE_CARDS, $selected);
                        }
                    }
                }
            }

            /**
             * <p>Prepares single previously not know payment method to be inserted into current configuration</p>
             * <p>Calls event with name of <code>eabi_maksekeskus_on_undefined_payment_method</code> for every newly discovered payment method</p>
             * @param array $methods currently desribed payment methods
             * @param array $actualNotExistingMethod
             * @param string $type credit card or banklink
             * @param string $selected current customer selected method
             * @see Eabi_Maksekeskus_Block_Adminhtml_Config_Form_Field_Method_Detail
             * @see Eabi_Maksekeskus_Model_Observer::addMaksekeskusPaymentMethodToConfiguration
             */
            protected function _appendNotDescribedMethod(&$methods, $actualNotExistingMethod, $type, $selected = false) {
                $method = array(
                    'image' => '', //$baseUrl . 'media/eabi_maksekeskus/maksekeskus.png',
                    'selected' => $selected,
                    'type' => $type,
                    'value' => $actualNotExistingMethod['name'],
                    'title' => $actualNotExistingMethod['name'],
                    'active' => '1',
                    'data' => $actualNotExistingMethod,
                );
                if ($method['active']) {
                    $methods[$method['value']] = $method;
                }
            }

            /**
             * <p>Returns true, if specified payment method code is contained in Maksekeskus API payment methods</p>
             * @param string $methodCode
             * @param array $actualAvailableMethods payment method from Maksekeskus API
             * @return boolean
             */
            protected function _containsMethod($methodCode, $actualAvailableMethods) {
                if (isset($actualAvailableMethods['banklinks'])) {
                    foreach ($actualAvailableMethods['banklinks'] as $banklink) {
                        if ($banklink['name'] == $methodCode) {
                            return true;
                        }
                    }
                }
                if (isset($actualAvailableMethods['cards'])) {
                    foreach ($actualAvailableMethods['cards'] as $card) {
                        if ($card['name'] == $methodCode) {
                            return true;
                        }
                    }
                }
                return false;
            }

            /**
             * <p>Returns type of payment method for the specified payment method code</p>
             * <p>Possible types are</p>
             * <ul>
              <li><strong>banklinks</strong> - offsite payment processor like PayPal</li>
              <li><strong>cards</strong> - credit card - customer can enter payment without leaving site</li>
              </ul>
             * @param string $methodCode
             * @param array $actualAvailableMethods payment method from Maksekeskus API
             * @return boolean
             */
            protected function _getPaymentMethodType($methodCode, $actualAvailableMethods) {
                foreach ($actualAvailableMethods['banklinks'] as $banklink) {
                    if ($banklink['name'] == $methodCode) {
                        return self::PAYMENT_METHOD_TYPE_BANKLINKS;
                    }
                }
                foreach ($actualAvailableMethods['cards'] as $card) {
                    if ($card['name'] == $methodCode) {
                        return self::PAYMENT_METHOD_TYPE_CARDS;
                    }
                }
                return false;
            }

            /**
             * <p>Returns payment method data from Maksekeskus API for the specified payment method code</p>
             * <p>Returned data format is assoc array with following keys</p>
             * <ul>
              <li><code>name</code> - payment method name or code</li>
              <li><code></code> - (optional) URL for starting the payment (offsite payment only)</li>
              </ul>
             * @param string $methodCode
             * @param array $actualAvailableMethods payment method from Maksekeskus API
             * @return boolean
             */
            protected function _getPaymentMethodData($methodCode, $actualAvailableMethods) {
                foreach ($actualAvailableMethods['banklinks'] as $banklink) {
                    if ($banklink['name'] == $methodCode) {
                        return $banklink;
                    }
                }
                foreach ($actualAvailableMethods['cards'] as $card) {
                    if ($card['name'] == $methodCode) {
                        return $card;
                    }
                }
                return false;
            }

            /**
             * Generate the start payment button link
             * */
            public function generate_banklinkmaksekeskus_form($order_id) {

                $order = new WC_Order($order_id);
                $destinationUrl = $this->get_return_url($order);









                $module_args_array = array();


                $isCreditCardPayment = false;
                $isPaymentComplete = false;

                if ($this->_api) {
                    $paymentMethodCode = get_post_meta($order_id, '_eabi_maksekeskus_preselected_method', true);
                    $isCreditCardPayment = $this->_isCreditCardPayment($paymentMethodCode);

                    $this->log(array('is_credit_card_payment' => $isCreditCardPayment));

                    //detect if we already have transaction and post data
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $isCreditCardPayment) {
                        //check the existance of the token
                        $transactionKey = get_post_meta($order_id, '_eabi_maksekeskus_transaction_id', true);
                        $token = isset($_POST['paymentToken']) ? sanitize_text_field($_POST['paymentToken']) : false;
                        $this->log(array('transaction_key' => $transactionKey,
                            'token' => $token));
                        if ($token && $transactionKey) {
                            $this->log('Token & TransactionKey exist');
                            //attempt to mark the order as paid
                            $grandTotal = $order->order_total;
                            $currency = get_option('woocommerce_currency');
                            if (!in_array($currency, $this->_getAllowedCurrencies())) {
                                $grandTotal = $this->_toTargetAmount($grandTotal, $currency);
                                $currency = $this->_currency;
                            }

                            try {
                                $apiPaymentResult = $this->_api->createPayment($transactionKey, $grandTotal, $currency, $token);
                                //put payment info to order as well
                                $order->add_order_note($this->_getCreditCardCaptureResult($apiPaymentResult));
                                $destinationUrl = $this->get_return_url($order);

                                // Payment complete
                                $order->payment_complete($transactionKey);
                                $this->_getWooCommerce()->cart->empty_cart();
                                $this->_clearDataFromSession();
                                $isPaymentComplete = true;
                            } catch (Eabi_Maksekeskus_Exception $ex) {
                                $order->add_order_note($this->_getCreditCardFailureResult($ex, $grandTotal));


                                if ($ex->getCode()) {
                                    if (strpos($ex->getMessage(), 'Maksekeskus request failed with response:') === false) {
                                        $errorMessage = __($ex->getMessage(), $this->_plugin_text_domain);
                                        $this->_add_notice(sprintf(__('Payment failed. Message: %1$s. Error code [%2$s]', $this->_plugin_text_domain), $errorMessage, $ex->getCode()), 'error');
                                    } else {
                                        $this->_add_notice(sprintf(__('Payment failed. Error code [%s]', $this->_plugin_text_domain), $ex->getCode()), 'error');
                                    }
                                } else {
                                    $this->_add_notice(sprintf(__('Payment failed with no error code', $this->_plugin_text_domain), $ex->getCode()), 'error');
                                }


                                $this->_clearDataFromSession();
                                
                                //redirect to somewhere???
                                $destinationUrl = htmlspecialchars_decode($order->get_cancel_order_url());
                                
                                throw $ex;
                            }
                        } else {
                            $this->log('Token & TransactionKey does not exist');
                        }
                    } else {
                        $this->log('Request method not post and not credit card');
                        $this->log(array('request_method' => $_SERVER['REQUEST_METHOD'],
                            'is-credit-cardpayment' => $isCreditCardPayment));
                    }


                    if (!$isPaymentComplete) {
                        $this->log('Payment is not complete');
                        //make transaction
                        $transactionKey = $this->_initializeTransactionKey(false, $order);
                        if ($isCreditCardPayment) {
                            //credit card would be submitted to receipt page itself
                            $destinationUrl = '';
                            $isCreditCardPayment = true;
                            $jsInitParams = $this->_getOrderJsInitParams($order, $transactionKey);
                            $jsInitParams['selector'] = '#submit_banklinkmaksekeskus_payment_form';
                            $jsInitParams['open-on-load'] = 'true';
                            $jsInitParams['client-name'] = isset($jsInitParams['clientName']) ? $jsInitParams['clientName'] : '';
                            $jsInitParams['completed'] = 'eabi_maksekeskus_datacompleted_inline';
//                            $scriptSrc = htmlspecialchars($this->get_option('checkout_js_url'));
                            $scriptSrc = htmlspecialchars($this->getCheckoutJsUrl());
                            
                            //refactor checkout_js_url into something
                            
                            
                            $module_args_array[] = <<<SCRIPT
                                <script type="text/javascript">
                                /* <![CDATA[ */
                                
                                
function eabi_maksekeskus_datacompleted_inline(dataIn) {
    var el = jQuery('<input>'), buttons;
    if (dataIn['paymentToken']) {
        el.attr('type', 'hidden');
        el.attr('name', 'paymentToken');
        el.attr('id', 'mk_paymentToken');
        if (!jQuery('#mk_paymentToken').length) {
            jQuery('#banklinkmaksekeskus_payment_form').append(el);
        }
        jQuery('#mk_paymentToken').attr('value', dataIn['paymentToken']);
        jQuery("#banklinkmaksekeskus_payment_form").submit();



    } else {
        /*we have no token*/
    }
}
                                
                                /* ]]> */
                                </script>
SCRIPT;
                            $module_args_array[] = <<<SCRIPT
                                <script type="text/javascript" src="{$scriptSrc}" {$this->_toHtmlAttributes($jsInitParams)}></script>
SCRIPT;
                        } else {
                            //banklink payment
                            //fetch new target URL
                            $paymentMethods = $this->_getAvailablePaymentMethods($transactionKey, false, $order);
                            $destinationUrl = $paymentMethods[$paymentMethodCode]['data']['url'];
                        }
                    }
                } else {
                    $currency = get_option('woocommerce_currency');
                    $price = $order->order_total;

                    $paymentMessage = array(
                        'shop' => $this->_shop_id,
                        'amount' => number_format($this->_toTargetAmount($price, $currency), 2, '.', ''),
                        'reference' => (string) $order_id,
                        'country' => strtolower($this->_getPreferredCountry($order)),
                        'locale' => $this->_getPreferredLocale(),
                    );


                    $macFields = Array(
                        'json' => json_encode($paymentMessage),
                        'mac' => $this->_getRedirectSignature(json_encode($paymentMessage), $this->get_option('api_secret' . $this->get_option('connection_mode'))),
                    );

                    foreach ($macFields as $key => $value) {
                        $module_args_array[] = '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" />';
                    }
                }


                if (!$isCreditCardPayment || $isPaymentComplete) {
                    $textBlock = '
			jQuery("body").block({ 
					message: "<img src=\"' . esc_url($this->_getWooCommerce()->plugin_url()) . '/assets/images/ajax-loader@2x.gif\" alt=\"Redirecting...\" style=\"float:left; margin-right: 10px;\" />' . __('Täname tellimuse eest, Teid suunatakse panka maksma...', $this->_plugin_text_domain) . '", 
					overlayCSS: 
					{ 
						background: "#fff", 
						opacity: 0.6 
					},
					css: { 
				        padding:        20, 
				        textAlign:      "center", 
				        color:          "#555", 
				        border:         "3px solid #aaa", 
				        backgroundColor:"#fff", 
				        cursor:         "wait",
				        lineHeight:		"32px"
				    } 
				});
			jQuery("#submit_banklinkmaksekeskus_payment_form").click();
		';
                    if ($this->_isWoo23()) {
                        wc_enqueue_js($textBlock);
                    } else {
                        $this->_getWooCommerce()->add_inline_js($textBlock);
                    }
                }
                $this->log(array(
                    'start-banklink-payment-request' => $module_args_array,
                    'payment-type' => $this->_api ? 'api' : 'redirect',
                    'destination-url' => $destinationUrl,
                ));



                return '<form action="' . htmlspecialchars($destinationUrl) . '" method="post" id="banklinkmaksekeskus_payment_form">
				<input type="submit" class="button-alt" id="submit_banklinkmaksekeskus_payment_form" value="' . __($this->title, $this->_plugin_text_domain) . '" /> <a class="button cancel" href="' . esc_url($order->get_cancel_order_url()) . '">' . __('Cancel order &amp; restore cart', $this->_plugin_text_domain) . '</a>
				' . implode('', $module_args_array) . '
			</form>';
            }

            /**
             * <p>Takes in Maksekeskus transaction payment creation result and returns human readable string about the transaction</p>
             * <p>Details returned:</p>
             * <ul>
              <li>Transaction ID</li>
              <li>Type of credit card used</li>
              <li>Name on credit card (initials only)</li>
              <li>Last digits of credit card number used</li>
              <li>Expiration date on credit card</li>
              </ul>
             * @param type $transactionData
             * @return string
             */
            protected function _getCreditCardCaptureResult($transactionData) {
                $result = array();
                $result[] = __('Transaction ID', $this->_plugin_text_domain) . ':' . $transactionData['transaction']['id'];
                $result[] = __('Credit card type', $this->_plugin_text_domain) . ':' . $transactionData["card"]['type'];
                $result[] = __('Name on card', $this->_plugin_text_domain) . ':' . $transactionData["card"]['name'];
                $result[] = __('Last digits', $this->_plugin_text_domain) . ':' . $transactionData["card"]['last2'];
                $result[] = __('Masked PAN', $this->_plugin_text_domain) . ':' . $transactionData["card"]['maskedPan'];
                if (isset($transactionData["card"]['exp_month']) && $transactionData["card"]['exp_year']) {
                    $expiration = $transactionData["card"]['exp_month'];
                    $expiration .= '/';
                    if (strlen($transactionData["card"]['exp_year']) == 2) {
                        $expiration .= '20' . $transactionData["card"]['exp_year'];
                    } else {
                        $expiration .= $transactionData["card"]['exp_year'];
                    }
                    $result[] = __('Expiration', $this->_plugin_text_domain) . ':' . $expiration;
                }
                return implode("\r\n", $result);
            }
            

            /**
             * 
             * @param Exception $exception
             * @param float $amount
             * @return type
             */
            protected function _getCreditCardFailureResult($exception, $amount = 0) {
                $result = array();
                $result[] = sprintf(__('Payment for amount of %1$s failed with message %2$s, code %3$s', $this->_plugin_text_domain), $amount, $exception->getMessage(), $exception->getCode());
                return implode("\r\n", $result);
            }
            

            /**
             * <p>Takes in JS initialization parameters and retuns them as data-*=value string, where * is array key and value is corresponding value in supplied array</p>
             * @param array $input
             * @return string
             */
            protected function _toHtmlAttributes($input) {
                $result = array();
                foreach ($input as $key => $value) {
                    $result[] = 'data-' . htmlspecialchars($key) . '=' . '"' . htmlspecialchars($value) . '"';
                }
                return implode(' ', $result);
            }

            /**
             * <p>Converts input variable to json string</p>
             * @param mixed $var
             * @return string
             */
            protected function _toJson($var) {
                return json_encode($var);
            }

            /**
             * <p>Returns true if payment method code is destinated for processing credit cards</p>
             * @param string $paymentMethodCode
             * @return bool
             */
            protected function _isCreditCardPayment($paymentMethodCode) {
                return in_array($paymentMethodCode, array(
                    'visa',
                    'mastercard',
                ));
            }

            /**
             * Process the payment and return the result
             * */
            function process_payment($order_id) {

                $order = new WC_Order($order_id);

                if ($this->_api) {
                    $selected = isset($_POST['PRESELECTED_METHOD_' . $this->id]) ? sanitize_text_field($_POST['PRESELECTED_METHOD_' . $this->id]) : false;
                    if ($this->_paymentMethodType === self::PAYMENT_METHOD_TYPE_CARDS) {
                        $selected = 'visa';
                    }

                    update_post_meta($order_id, '_eabi_maksekeskus_preselected_method', $selected);
                }

                return array(
                    'result' => 'success',
                    'redirect' => $this->_getOrderConfirmationUrl($order),
                );
            }


            /**
             * 
             * @param WC_Order $order
             * @return type
             */
            protected function _getOrderConfirmationUrl($order) {
//                echo 'kala';
//                exit;
                $url = add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, $order->get_checkout_payment_url(true)));
                if ($this->_isWoo21()) {
//                    $url = add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, $order->get_checkout_payment_url(true)));
                    $url = $order->get_checkout_payment_url(true);
                    
                }
                if ($this->_isWoo20()) {
                    $url = add_query_arg('order', $order->id, add_query_arg('key', $order->order_key, get_permalink(get_option('woocommerce_pay_page_id'))));
                }
                return $url;
            }

            /**
             * <p>Returns true, if WooCommerce version is 2.0</p>
             * @return bool
             */
            protected function _isWoo20() {
                return substr($this->_getWooCommerce()->version, 0, 3) == "2.0";
            }

            /**
             * <p>Returns true, if WooCommerce version is 2.3 or greater</p>
             * @return bool
             */
            protected function _isWoo21() {
                if (defined('WOOCOMMERCE_VERSION')) {
                    //detect version, when woo is not yet loaded
                    return version_compare(WOOCOMMERCE_VERSION, '2.1', '>=');
                }
//                return substr($this->_getWooCommerce()->version, 0, 3) >= "2.3";
                return version_compare($this->_getWooCommerce()->version, '2.1', '>=');
            }
            /**
             * <p>Returns true, if WooCommerce version is 2.3 or greater</p>
             * @return bool
             */
            protected function _isWoo23() {
                if (defined('WOOCOMMERCE_VERSION')) {
                    //detect version, when woo is not yet loaded
                    return version_compare(WOOCOMMERCE_VERSION, '2.3', '>=');
                }
//                return substr($this->_getWooCommerce()->version, 0, 3) >= "2.3";
                return version_compare($this->_getWooCommerce()->version, '2.3', '>=');
            }
            /**
             * <p>Returns true, if WooCommerce version is 2.3 or greater</p>
             * @return bool
             */
            protected function _isWoo24() {
                if (defined('WOOCOMMERCE_VERSION')) {
                    //detect version, when woo is not yet loaded
                    return version_compare(WOOCOMMERCE_VERSION, '2.4', '>=');
                }
//                return substr($this->_getWooCommerce()->version, 0, 3) >= "2.3";
                return version_compare($this->_getWooCommerce()->version, '2.4', '>=');
            }
            /**
             * <p>Returns true, if WooCommerce version is 2.5 or greater</p>
             * @return bool
             */
            protected function _isWoo25() {
                if (defined('WOOCOMMERCE_VERSION')) {
                    //detect version, when woo is not yet loaded
                    return version_compare(WOOCOMMERCE_VERSION, '2.5', '>=');
                }
//                return substr($this->_getWooCommerce()->version, 0, 3) >= "2.3";
                return version_compare($this->_getWooCommerce()->version, '2.5', '>=');
            }
            /**
             * <p>Returns true, if WooCommerce version is $version or greater</p>
             * @return bool
             */
            protected function _isGteWoo($version) {
                if (defined('WOOCOMMERCE_VERSION')) {
                    //detect version, when woo is not yet loaded
                    return version_compare(WOOCOMMERCE_VERSION, $version, '>=');
                }
//                return substr($this->_getWooCommerce()->version, 0, 3) >= "2.3";
                return version_compare($this->_getWooCommerce()->version, $version, '>=');
            }

            /**
             * receipt_page
             * */
            function receipt_page($order) {

                echo '<p>' . __('Thank you for the order, please click on the button to start the payment.', $this->_plugin_text_domain) . '</p>';

                echo $this->generate_banklinkmaksekeskus_form($order);
            }

            /**
             * Check IF the request has valid signature
             * */
            function check_banklink_is_valid(&$validationResult) {

                return $validationResult['status'] != 'failed';
            }

            /**
             * Check for Banklink response
             * */
            function check_banklink_response() {

                if (isset($_GET['validateMaksekeskus']) && $_GET['validateMaksekeskus']):
                    @ob_clean();

                    $_REQUEST = stripslashes_deep($_REQUEST);


                    $validationResult = $this->validateApiPayment($_REQUEST);
                    $this->log(array(
                        'end-banklink-payment-incoming-request' => $_REQUEST,
                        'validation-type' => $this->_api ? 'api' : 'redirect',
                        'validation-result' => $validationResult,
                    ));



                    if ($this->check_banklink_is_valid($validationResult)) {

                        do_action("valid-banklinkmaksekeskus-request", $validationResult);
                    } else {
                        //go to homepage
                        $url = get_option('home');
                        wp_redirect($url);
                    }


                endif;
                //go to homepage here
            }

            /**
             * Successful Payment!
             * */
            function successful_request($validationResult) {
                $url = get_option('home');

                if ($validationResult['status'] == self::TRX_COMPLETED) {

                    $order = new WC_Order((int) $validationResult['data']);
                    if ($this->_toTargetAmount($order->order_total, get_option('woocommerce_currency')) != $validationResult['amount']) {
                        echo 'Order amount does not match the actual paid amount';
                        exit;
                    }

                    if ($order->status != 'completed') {
                        //update the order
                        if (isset($validationResult['transactionId']) && $validationResult['transactionId']) {
                            $orderNote = array();
                            $orderNote[] = __('Transaction ID', $this->_plugin_text_domain) . ':' . $validationResult['transactionId'];
                            $orderNote[] = __('Payment option', $this->_plugin_text_domain) . ':' . get_post_meta($order->id, '_eabi_maksekeskus_preselected_method', true);

                            $order->add_order_note(implode("\r\n", $orderNote));
                            $order->payment_complete($validationResult['transactionId']);
                        } else {
                            $order->add_order_note(__('Payment completed', $this->_plugin_text_domain));
                            $order->payment_complete();
                        }


                        // Payment complete
                        $this->_getWooCommerce()->cart->empty_cart();
                        $this->_clearDataFromSession();
                    }
                    $url = $this->get_return_url($order);


                    //and always tell the user, that everything is superb....
                } else if ($validationResult['status'] == self::TRX_APPROVED) {
                    $order = new WC_Order((int) $validationResult['data']);
                    if ($this->_toTargetAmount($order->order_total, get_option('woocommerce_currency')) != $validationResult['amount']) {
                        echo 'Order amount does not match the actual paid amount';
                        exit;
                    }

                    if ($order->status != 'completed') {
                        //update the order
                        if (isset($validationResult['transactionId']) && $validationResult['transactionId']) {
                            $orderNote = array();
                            $orderNote[] = __('Transaction ID', $this->_plugin_text_domain) . ':' . $validationResult['transactionId'];
                            $orderNote[] = __('Payment option', $this->_plugin_text_domain) . ':' . get_post_meta($order->id, '_eabi_maksekeskus_preselected_method', true);

                            $order->add_order_note(implode("\r\n", $orderNote));
                            $order->reduce_order_stock();
//                            $order->payment_complete($validationResult['transactionId']);
                        } else {
                            $order->add_order_note(__('Payment completed', $this->_plugin_text_domain));
                            $order->reduce_order_stock();
//                            $order->payment_complete();
                        }


                        // Payment complete
                        $this->_getWooCommerce()->cart->empty_cart();
                        $this->_clearDataFromSession();
                    }
                    $url = $this->get_return_url($order);
                } else if ($validationResult['status'] == self::TRX_PENDING) {
                    //in here guide the user nicely to the cart and go out again.
                    $order = new WC_Order((int) $validationResult['data']);
//                    $url = $order->get_cancel_order_url();
                    $url = htmlspecialchars_decode($order->get_cancel_order_url());
                    $this->_clearDataFromSession();
                } else if ($validationResult['status'] == self::TRX_CANCELLED) {
                    //in here guide the user nicely to the cart and go out again.
                    $order = new WC_Order((int) $validationResult['data']);
//                    $url = $order->get_cancel_order_url();
                    $url = htmlspecialchars_decode($order->get_cancel_order_url());
                    $this->_clearDataFromSession();
                } else if ($validationResult['status'] == self::TOKEN_RETURN_SUCCESS) {
                    $order = new WC_Order((int) $validationResult['data']);

                    //make the post request somehow with two params:
                    //$_POST['paymentToken']; that one you need to POST, everything else we alreay have.
                    $_POST['paymentToken'] = $validationResult['token'];

                    try {
                        @ob_start();
                        $this->receipt_page($order->id);

                        @ob_end_clean();
                        $message = sprintf(__('3D Secure authentication successful', $this->_plugin_text_domain), __($validationResult['message'], $this->_plugin_text_domain));
                        $this->_add_notice($message, 'success');
                        $url = $this->get_return_url($order);
                    } catch (Exception $ex) {
                        @ob_end_clean();
                        
                        $url = $order->get_cancel_order_url();
                        
                    }





                    //redirect to success page
                } else if ($validationResult['status'] == self::TOKEN_RETURN_FAILURE) {
                    //possibility forward a message?
                    $order = new WC_Order((int) $validationResult['data']);
                    $message = sprintf(__('3D Secure authentication failed [%s]', $this->_plugin_text_domain), __($validationResult['message'], $this->_plugin_text_domain));
                    $this->_add_notice($message, 'error');
                    $this->_clearDataFromSession();

                    $url = htmlspecialchars_decode($order->get_cancel_order_url());
//                    $url = $this->get_return_url($order);
                } else {
                    //wrong signature, send to front page
                }

                wp_redirect($url);
                exit;
            }

            /**
             * <p>If API mode is enabled, then this validation verifies, that customer has selected suitable payment option</p>
             * @return boolean
             */
            public function validate_fields() {
                if ($this->_api) {
                    $selected = isset($_POST['PRESELECTED_METHOD_' . $this->id]) ? sanitize_text_field($_POST['PRESELECTED_METHOD_' . $this->id]) : false;

                    if (!$selected) {
                        $this->_add_notice(__('Please select suitable payment option!', $this->_plugin_text_domain), 'error');
                    } else {
                        $this->_getWooCommerce()->session->eabi_maksekeskus_preselected_method = $selected;
                    }
                }

                return true;
            }

            protected function _add_notice($text, $notice_type = 'success') {
                if ($this->_isWoo20()) {
                    if ($notice_type == 'success') {
                        $this->_getWooCommerce()->add_message($text);
                    } else {
                        $this->_getWooCommerce()->add_error($text);
                    }
                } else {
                    return wc_add_notice($text, $notice_type);
                }
            }

            /**
             * <p>Determines if transaction key has been fetched from server</p>
             * <p>Determines if stored transaction details match with the quote itself</p>
             * <p>Checked transaction details are:</p>
             * <ul>
              <li>Transaction amount</li>
              <li>Transaction currency</li>
              <li>Billing address for the transaction</li>
             * <li>If transaction is at created status, since other statuses require merchant action</li>
              </ul>
             * @param WC_Order $order
             * @return boolean
             */
            protected function _isSameTransaction($order = null) {
                if (!isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_id)) {
                    return false;
                }

                $cart = $this->_getWooCommerce()->cart;
                $customer = $this->_getWooCommerce()->customer;
                $grandTotal = $cart->total;
                $currency = get_option('woocommerce_currency');
                if (!in_array($currency, $this->_getAllowedCurrencies())) {
                    $grandTotal = $this->_toTargetAmount($grandTotal, $currency);
                    $currency = $this->_currency;
                }

                if (!isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_amount) || round($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_amount, 2) != round($grandTotal, 2)) {
                    return false;
                }

                if (!isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_country) || $this->_getWooCommerce()->session->eabi_maksekeskus_transaction_country != $customer->get_country()) {
                    return false;
                }

                if (!isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_currency) || $this->_getWooCommerce()->session->eabi_maksekeskus_transaction_currency != $currency) {

                    return false;
                }

                $transaction = $this->_api->getTransaction($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_id);
                $allowedStatuses = array(
                    self::TRX_CREATED,
                    self::TRX_PENDING,
                );
                if (!in_array($transaction['status'], $allowedStatuses)) {
                    return false;
                }
                if ($order && $order->id) {
                    //validate if reference matches
                    if ($transaction['reference'] != $order->id) {
                        //if transaction reference does not match order id, then return false
                        return false;
                    }
                }

                return true;
            }

            /**
             * <p>Creates new Transaction with Maksekeskus API or returns previously created transaction ID.</p>
             * @param bool $forceNew
             * @param WC_Order $order
             * @return boolean
             */
            protected function _initializeTransactionKey($forceNew = false, $order = null) {
                
                if ($this->_isSameTransaction($order) && !$forceNew) {
                    return $this->_getWooCommerce()->session->eabi_maksekeskus_transaction_id;
                }
                //we do not have transaction key, we need to create it
                //amount
                //currency
                //remote_ip
                if ($order) {
                    $country = $order->billing_country;
                    $grandTotal = $order->order_total;
                    $reference = $order->id;
                } else {
                    $country = $this->_getWooCommerce()->customer->get_country();
                    $grandTotal = $this->_getWooCommerce()->cart->total;
                    $reference = 'cart-' . time();
                }

                if (!$country) {
                    $country = get_option('woocommerce_default_country');
//                    return false;
                }
                $currency = get_option('woocommerce_currency');
                if (!in_array($currency, $this->_getAllowedCurrencies())) {
                    $grandTotal = $this->_toTargetAmount($grandTotal, $currency);
                    $currency = $this->_currency;
                }

                $transaction = array(
                    'amount' => round($grandTotal, 2),
                    'currency' => $currency,
                    'reference' => $reference,
                );
                $ip = $_SERVER['REMOTE_ADDR'];

                $returnUrl = plugins_url('/validate-payment.php', __FILE__);

                $transaction['transaction_url'] = array(
                    'return_url' => array(
                        'url' => $returnUrl,
                        'method' => 'POST',
                    ),
                    'cancel_url' => array(
                        'url' => $returnUrl,
                        'method' => 'POST',
                    ),
                    'notification_url' => array(
                        'url' => $returnUrl,
                        'method' => 'POST',
                    ),
                );


                $customerData = array(
                    'ip' => $this->_getValidatedIp($ip),
                    'country' => strtolower($country),
                    'locale' => $this->_getPreferredLocale(),
                );

                try {
                    $transactionData = $this->_api->createTransaction($transaction, $customerData);
                } catch (Eabi_Maksekeskus_Exception $ex) {
                    //on expired transaction Exception is also thrown, so in this case we return false, but exception is still logged.
                    return false;
                }

                //save the transaction to extrainfo
                $this->_applyTransactionData($transactionData, $grandTotal);
                if ($order) {
                    //put transaction to post meta
                    update_post_meta($order->id, '_eabi_maksekeskus_transaction_id', $transactionData['id']);
                }

                return $transactionData['id'];
            }


            protected function _getValidatedIp($input) {
                $isIpValid = filter_var($input, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
                if (!$isIpValid) {
                    $inputs = $this->_csvToArray($input);
                    if (isset($inputs[0]) && filter_var($inputs[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
                        return $inputs[0];
                    }
                    //all failed, return server host
                    return $_SERVER['SERVER_ADDR'];
                } else {
                    return $input;
                }
            }


            protected function _csvToArray($inputText) {
                $values = explode(",", $inputText);
                return array_filter(array_map('trim', $values));
            }


                        /**
             * <p>Injects Maksekeskus <code>Transaction</code> to session</p>
             * @param array $transactionData
             * @param float $quoteOrderGrandTotal
             * @throws Exception
             */
            protected function _applyTransactionData($transactionData, $quoteOrderGrandTotal) {
                $this->_getWooCommerce()->session->eabi_maksekeskus_transaction_id = $transactionData['id'];
                $transactionData['amount'] = str_replace(',', '', $transactionData['amount']);
                if ($quoteOrderGrandTotal <= 0 || $transactionData['amount'] <= 0) {
                    throw new Exception('Total transaction amount is 0');
                }
                $this->_getWooCommerce()->session->eabi_maksekeskus_transaction_amount = $quoteOrderGrandTotal;
                $this->_getWooCommerce()->session->eabi_maksekeskus_transaction_amount_authorized = $transactionData['amount'];
                $this->_getWooCommerce()->session->eabi_maksekeskus_transaction_country = strtoupper($transactionData['customer']['country']);
                $this->_getWooCommerce()->session->eabi_maksekeskus_transaction_currency = $transactionData['currency'];
            }

            /**
             * <p>Removes transaction data from the session</p>
             */
            protected function _clearDataFromSession() {
                if (isset($this->_getWooCommerce()->session->eabi_maksekeskus_preselected_method)) {
                    unset($this->_getWooCommerce()->session->eabi_maksekeskus_preselected_method);
                }
                if (isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_id)) {
                    unset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_id);
                }
                if (isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_amount)) {
                    unset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_amount);
                }
                if (isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_amount_authorized)) {
                    unset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_amount_authorized);
                }
                if (isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_country)) {
                    unset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_country);
                }
                if (isset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_currency)) {
                    unset($this->_getWooCommerce()->session->eabi_maksekeskus_transaction_currency);
                }
            }

            protected function _getWooCommerce() {
                global $woocommerce;
                return $woocommerce;
            }

            /**
             * Process refunds
             * WooCommerce 2.2 or later
             *
             * @param  int $order_id
             * @param  float $amount
             * @param  string $reason
             * @return bool|WP_Error
             */
            public function process_refund($order_id, $amount = null, $reason = '') {
                if ($this->_api) {
                    try {
                        $transactionId = get_post_meta($order_id, '_eabi_maksekeskus_transaction_id', true);
                        $this->_api->createRefund($transactionId, $amount, $reason);
                        $order = new WC_Order((int) $order_id);
                        $order->add_order_note(sprintf(__('Refund completed for amount %s', $this->_plugin_text_domain), $amount));


                        return true;
                    } catch (Eabi_Maksekeskus_Exception $e) {
                        return new WP_Error('eabi_maksekeskus_refund_error', $e->getMessage());
                    }

                    return false;
                }
                return false;
            }

            /**
             * <p>Validates input data as payment message and returns the result as Redirect payment model</p>
             * <p>Returns data with following information:</p>
             * <ul>
              <li><code>data</code> - order increment id, if payment transaction was created from order</li>
              <li><code>amount</code> - order total in EUR paid</li>
              <li><code>status</code> - CREATED,COMPLETED,CANCELLED</li>
              </ul>
             * @param array $params
             * @return array
             * @deprecated since version 2.18
             */
            protected function validateBanklinkPayment($params) {

                $macFields = false;
                $result = array(
                    'data' => '',
                    'amount' => 0,
                    'status' => 'failed',
                );
                foreach ((array) $params as $f => $v) {
                    if ($f == 'json') {
                        $macFields = $v;
                    }
                }
                if (!$macFields) {
                    return $result;
                }
                $paymentMessage = @json_decode($macFields, true);
                if (!$paymentMessage) {
                    $paymentMessage = @json_decode(stripslashes($macFields), true);
                }
                if (!$paymentMessage) {
                    $paymentMessage = @json_decode(htmlspecialchars_decode($paymentMessage), true);
                }
                if (!$paymentMessage || !isset($paymentMessage['signature']) || !$paymentMessage['signature']) {
                    return $result;
                }
                $sentSignature = $paymentMessage['signature'];

                if (isset($paymentMessage['shopId'])) {
                    $paymentFailure = $paymentMessage['shopId'] != $this->_shop_id;
                } else {
                    $paymentFailure = false;
                }

                if ($this->_getReturnSignature($paymentMessage, $this->get_option('api_secret' . $this->get_option('connection_mode'))) != $sentSignature || $paymentFailure) {
                    return $result;
                } else {
                    if ($paymentMessage['status'] == 'RECEIVED') {
                        $result['status'] = self::TRX_CREATED;
                        $result['data'] = $paymentMessage['paymentId'];
                        $result['amount'] = $paymentMessage['amount'];
                    } else if ($paymentMessage['status'] == 'PAID') {
                        $result['status'] = self::TRX_COMPLETED;
                        $result['data'] = $paymentMessage['paymentId'];
                        $result['amount'] = $paymentMessage['amount'];
                    } else if ($paymentMessage['status'] == 'CANCELLED') {
                        $result['status'] = self::TRX_CANCELLED;
                        $result['data'] = $paymentMessage['paymentId'];
                    }

                    return $result;
                }
            }

            /**
             * <p>Validates the payment payload for API mode</p>
             * <p>Returns data with following information:</p>
             * <ul>
              <li><code>data</code> - order increment id, if payment transaction was created from order</li>
              <li><code>amount</code> - order total in EUR paid</li>
              <li><code>status</code> - failed,CREATED,EXPIRED,COMPLETED,PENDING,REFUNDED,PART_REFUNDED,CANCELLED</li>
              <li><code>auto</code> - if request was notify or not.</li>
              <li><code>quote</code> - quote id, if payment transaction was created directly from quote</li>
              </ul>
             * @param array $params POST or GET data
             * @return array
             */
            protected function validateApiPayment($params) {
                $macFields = false;
                $maxTimeDiff = 60 * 60 * 1; //1 hour
                $sentMac = false;
                $result = array(
                    'data' => '',
                    'amount' => 0,
                    'status' => 'failed',
                    'auto' => false,
                );
                foreach ((array) $params as $f => $v) {
                    if ($f == 'json') {
                        $macFields = $v;
                    }
                    if ($f == 'mac') {
                        $sentMac = $v;
                    }
                }
                if (!$macFields || !$sentMac) {
                    return $result;
                }
                $origMacFields = $macFields;
                $paymentMessage = @json_decode($macFields, true);
                if (!$paymentMessage) {
                    $paymentMessage = @json_decode(stripslashes($macFields), true);
                    $macFields = stripslashes($origMacFields);
                }
                if (!$paymentMessage) {
                    $paymentMessage = @json_decode(htmlspecialchars_decode($macFields), true);
                    $macFields = htmlspecialchars_decode($origMacFields);
                }


                if ($this->_getRedirectSignature($macFields, $this->get_option('api_secret' . $this->get_option('connection_mode'))) != $sentMac) {
                    return $result;
                } else {
                    //2014-12-15T11:15:53+0000
//                    $incomingDateFormat = 'yyyy-MM-ddTHH:mm:ssZZ';
                    //verify time
                    $now = time();
                    try {
                        $message_time = $this->_getDateTimestamp($paymentMessage['message_time']);
                    } catch (Exception $ex) {
                        echo '<pre>' . htmlspecialchars(print_r($paymentMessage['message_time'], true)) . '</pre>';
                        echo '<pre>' . htmlspecialchars(print_r($ex->__toString(), true)) . '</pre>';
                        exit;
                    }
                    if ($now - $message_time > $maxTimeDiff) {
                        //if too long from the message time has passed, then drop the message
                        return $result;
                    }


                    if ($paymentMessage['message_type'] == self::TOKEN_RETURN) {
                        $api = $this->_api;
                        $mkTransaction = $api->getTransaction($paymentMessage['transaction']['id']);
                        $this->_fillDataOrQuote($result, $mkTransaction);

                        if (!$result['data']) {
                            //we have no related order, which means that we are in the iframe, thus quote is not needed
                            $result['quote'] = false;
                        }

                        if (isset($paymentMessage['token']) && isset($paymentMessage['token']['id'])) {
                            $result['status'] = self::TOKEN_RETURN_SUCCESS;
                            $result['token'] = $paymentMessage['token']['id'];
                            $result['transactionId'] = $paymentMessage['transaction']['id'];
                        } else if (isset($paymentMessage['error'])) {
                            $result['status'] = self::TOKEN_RETURN_FAILURE;
                            $result['message'] = $paymentMessage['error']['message'];
                            $result['transactionId'] = $paymentMessage['transaction']['id'];
                        }
                    } else if ($paymentMessage['message_type'] == self::PAYMENT_RETURN) {
                        if ($paymentMessage['status'] == self::TRX_EXPIRED) {
                            $result['status'] = self::TRX_EXPIRED;
                            $result['transactionId'] = $paymentMessage['transaction'];
                            $result['currency'] = $paymentMessage['currency'];
                            $result['amount'] = $paymentMessage['amount'];
                            $result['auto'] = isset($paymentMessage['auto']) && $paymentMessage['auto'];
                            $this->_fillDataOrQuote($result, $paymentMessage);
                        } else if ($paymentMessage['status'] == self::TRX_COMPLETED) {
                            $result['status'] = self::TRX_COMPLETED;
                            $result['transactionId'] = $paymentMessage['transaction'];
                            $this->_fillDataOrQuote($result, $paymentMessage);
                            $result['currency'] = $paymentMessage['currency'];
                            $result['amount'] = $paymentMessage['amount'];
                            $result['auto'] = isset($paymentMessage['auto']) && $paymentMessage['auto'];
                        } else if ($paymentMessage['status'] == self::TRX_APPROVED) {
                            $result['status'] = self::TRX_APPROVED;
                            $result['transactionId'] = $paymentMessage['transaction'];
                            $this->_fillDataOrQuote($result, $paymentMessage);
                            $result['currency'] = $paymentMessage['currency'];
                            $result['amount'] = $paymentMessage['amount'];
                            $result['auto'] = isset($paymentMessage['auto']) && $paymentMessage['auto'];
                        } else if ($paymentMessage['status'] == self::TRX_CANCELLED) {
                            $result['status'] = self::TRX_CANCELLED;
                            $result['transactionId'] = $paymentMessage['transaction'];
                            $this->_fillDataOrQuote($result, $paymentMessage);
                            $result['currency'] = $paymentMessage['currency'];
                            $result['amount'] = $paymentMessage['amount'];
                            $result['auto'] = isset($paymentMessage['auto']) && $paymentMessage['auto'];
                        } else if ($paymentMessage['status'] == self::TRX_PENDING) {
                            $result['status'] = self::TRX_PENDING;
                            $result['transactionId'] = $paymentMessage['transaction'];
                            $this->_fillDataOrQuote($result, $paymentMessage);
                            $result['currency'] = $paymentMessage['currency'];
                            $result['amount'] = $paymentMessage['amount'];
                            $result['auto'] = isset($paymentMessage['auto']) && $paymentMessage['auto'];
                        } else if ($paymentMessage['status'] == self::TRX_REFUNDED) {
                            $result['status'] = self::TRX_REFUNDED;
                            $result['transactionId'] = $paymentMessage['transaction'];
                            $this->_fillDataOrQuote($result, $paymentMessage);
                            $result['currency'] = $paymentMessage['currency'];
                            $result['amount'] = $paymentMessage['amount'];
                            $result['auto'] = isset($paymentMessage['auto']) && $paymentMessage['auto'];
                        } else if ($paymentMessage['status'] == self::TRX_PART_REFUNDED) {
                            $result['status'] = self::TRX_PART_REFUNDED;
                            $result['transactionId'] = $paymentMessage['transaction'];
                            $this->_fillDataOrQuote($result, $paymentMessage);
                            $result['currency'] = $paymentMessage['currency'];
                            $result['amount'] = $paymentMessage['amount'];
                            $result['auto'] = isset($paymentMessage['auto']) && $paymentMessage['auto'];
                        }
                    } else {
                        return $result;
                    }
                    return $result;
                }
            }

            /**
             * <p>Converts <code>2014-12-15T11:15:53+0000</code> into timestamp. Takes offset into account as well.</p>
             * @param string $inputDate
             */
            private function _getDateTimestamp($inputDate) {
                $dateChunks = preg_split('/[T\-:]/', substr($inputDate, 0, 19));

                $zoneChunk = substr($inputDate, 19);

                /*
                 * <p>Explodes input date of 2014-12-15T11:15:53+0000 into array pieces, where elements are following:</p>
                  first element is year
                  second element is month
                  third element is day
                  fourth element is hours
                  fifth element is minutes
                  sixth element is seconds
                  sevent element is offset, which we can split into hours and minutes.
                 * 
                 */
                $timestamp = mktime($dateChunks[3], $dateChunks[4], $dateChunks[5], $dateChunks[1], $dateChunks[2], $dateChunks[0]);

                //finally addjust with timestamp the offset itself.
                $offset = array(
                    'mark' => substr($zoneChunk, 0, 1),
                    'hours' => substr($zoneChunk, 1, 2),
                    'minutes' => substr($zoneChunk, 3, 2),
                );
                if ($offset['mark'] === '+') {
                    $timestamp -= (int) ($offset['hours'] * 3600 + $offset['minutes'] * 60);
                } elseif ($offset['mark'] === '-') {
                    $timestamp += (int) ($offset['hours'] * 3600 + $offset['minutes'] * 60);
                }

                //put our offset on top
                $timestamp += date_offset_get(new DateTime());

                return $timestamp;
            }

            /**
             * <p>Fills payment validation data with following array keys:</p>
             * <ul>
              <li><code>data</code> - related order id from <code>paymentMessage</code></li>
              </ul>
             * @param array $data
             * @param array $paymentMessage
             */
            protected function _fillDataOrQuote(&$data, $paymentMessage) {
                $data['data'] = $paymentMessage['reference'];
                $data['quote'] = false;
            }

            /**
             * <p>Calculates Maksekeskus payment start signature for offsite payment</p>
             * <p>Not used in API mode</p>
             * @param array $paymentMessage
             * @param string $apiSecret
             * @return string
             * @deprecated since version 2.18
             */
            protected function _getStartSignature($paymentMessage, $apiSecret) {
                $variableOrder = array(
                    'shopId',
                    'paymentId',
                    'amount',
                );
                $stringToHash = '';
                foreach ($variableOrder as $messagePart) {
                    $stringToHash .= $paymentMessage[$messagePart];
                }
                return strtoupper(hash('sha512', $stringToHash . $apiSecret));
            }

            /**
             * <p>Calculates payment return message signature on return and on notify event</p>
             * <p>Signature calculation depends on whether API mode is enabled or not</p>
             * @param string $paymentMessage
             * @param string $apiSecret
             * @return string
             * @deprecated since version 2.18
             */
            protected function _getReturnSignature($paymentMessage, $apiSecret) {
                $variableOrder = array(
                    'paymentId',
                    'amount',
                    'status',
                );
                if ($this->_api) {
                    $variableOrder = array(
                        'amount',
                        'currency',
                        'reference',
                        'transaction',
                        'status',
                    );
                }


                $stringToHash = '';
                foreach ($variableOrder as $messagePart) {
                    $stringToHash .= $paymentMessage[$messagePart];
                }

                return strtoupper(hash('sha512', $stringToHash . $apiSecret));
            }

            protected function _getRedirectSignature($json, $apiSecret) {
//                echo '<pre>'.htmlspecialchars(print_r($json.$apiSecret, true), ENT_COMPAT | ENT_HTML401 | ENT_IGNORE).'</pre>';
//                exit;

                return strtoupper(hash('sha512', $json . $apiSecret));
            }

            /**
             * <p>Converts input to destined currency</p>
             * @param float $input
             * @param string $currency
             * @return float
             */
            protected function _toTargetAmount($input, $currency) {
                if ($currency == $this->_currency) {
                    return $input;
                }
                return round($input * $this->_getExchangeRate($currency, $this->_currency), 2);
            }

            /**
             * Copied from: http://stackoverflow.com/questions/13134574/how-to-do-usd-to-inr-currency-conversion-on-the-fly-woocommerce
             * @param string $from
             * @param string $to
             * @return float
             */
            protected function _getExchangeRate($from, $to) {
                $url = "http://www.google.com/ig/calculator?hl=en&q=1%s=?%s";   //url for the currency convertor
                $url = "https://www.google.com/finance/converter?hl=en&a=1&from=%s&to=%s";
                $result = wp_remote_retrieve_body($response = wp_remote_get(sprintf($url, $from, $to))); // fetches the result from the url
                if (is_wp_error($response)) {
                    return 1;
                }

                $get = explode("<span class=bld>", $result);
                $get = explode("</span>", $get[1]);
                $result = preg_replace("/[^0-9\.]/", null, $get[0]);

                return ( $result == 0 ) ? 1 : $result;
            }

            /**
             * <p>Returns the preferred locale for the offsite payment or credit card payment form</p>
             * @return string
             */
            protected function _getPreferredLocale() {
                $defaultLocale = 'et';
                $locale = $this->_locale;
                if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE
                        && strlen(ICL_LANGUAGE_CODE) == 2) {
                    $locale = ICL_LANGUAGE_CODE;
                }
                
                if ($locale) {
                    $localeParts = explode('_', $locale);
                    if (strlen($localeParts[0]) == 2) {
                        return strtolower($localeParts[0]);
                    } else {
                        return $defaultLocale;
                    }
                }
                return $defaultLocale;
            }

            public function _getPreferredCountry($order = null) {
                $country = null;
                if ($order) {
                    $country = $order->billing_country;
                }
                if (!$country) {
                    $country = $this->_getWooCommerce()->customer->get_country();
                }
                if (!$country) {
                    $country = get_option('woocommerce_default_country');
//                    return false;
                }
                
                return $country;
            }

            /**
             * <p>Returns true, if loggins is enabled for related payment method processor</p>
             * @return bool
             */
            protected function _isLogEnabled() {
                return $this->get_option('enable_log') == 'yes';
            }

            /**
             * Return the gateways title
             *
             * @access public
             * @return string
             */
            function get_title() {
                return apply_filters('woocommerce_gateway_title', __($this->title, $this->_plugin_text_domain), $this->id);
            }

            protected static function _log($data, $level = 'DEBUG') {
                $postLevel = 'DEBUG';
                //TODO: implement this function....
                if (is_null(self::$log)) {
                    self::$log = new WC_Logger();
                }
                $class = 'Eabi_Maksekeskus_Model_Api';

                if (!self::$_isRequestLogged) {
                    self::$log->add($class, $postLevel . ': POST=' . print_r($_POST, true));
                    self::$log->add($class, $postLevel . ': GET=' . print_r($_GET, true));
                    self::$log->add($class, $postLevel . ': USER_AGENT=' . $_SERVER['HTTP_USER_AGENT']);
                    self::$_isRequestLogged = true;
                }

                if (is_object($data) || is_array($data)) {
//            $data = print_r($data, true);
                }

                self::$log->add($class, $level . ': DATA=' . print_r($data, true));
            }

            protected function log($dataToLog, $level = 'DEBUG') {

                if ($this->_isLogEnabled()) {
                    self::_log($dataToLog, $level);
                }
            }

            /**
             * Generate Text Input HTML.
             *
             * @access public
             * @param mixed $key
             * @param mixed $data
             * @since 1.0.0
             * @return string
             */
            public function generate_maksekeskusreturn_html($key, $data) {
                $woocommerce = $this->_getWooCommerce();

                $html = '';

                $data['title'] = isset($data['title']) ? $data['title'] : '';
                $data['disabled'] = empty($data['disabled']) ? false : true;
                $data['class'] = isset($data['class']) ? $data['class'] : '';
                $data['css'] = isset($data['css']) ? $data['css'] : '';
                $data['placeholder'] = isset($data['placeholder']) ? $data['placeholder'] : '';
                $data['type'] = isset($data['type']) ? $data['type'] : 'text';
                $data['desc_tip'] = isset($data['desc_tip']) ? $data['desc_tip'] : false;
                $data['description'] = isset($data['description']) ? $data['description'] : '';

                // Description handling
                if ($data['desc_tip'] === true) {
                    $description = '';
                    $tip = $data['description'];
                } elseif (!empty($data['desc_tip'])) {
                    $description = $data['description'];
                    $tip = $data['desc_tip'];
                } elseif (!empty($data['description'])) {
                    $description = $data['description'];
                    $tip = '';
                } else {
                    $description = $tip = '';
                }

                // Custom attribute handling
                $custom_attributes = array();

                if (!empty($data['custom_attributes']) && is_array($data['custom_attributes']))
                    foreach ($data['custom_attributes'] as $attribute => $attribute_value)
                        $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';

                $html .= '<tr valign="top">' . "\n";
                $html .= '<th scope="row" class="titledesc">';
                $html .= '<label for="' . esc_attr($this->plugin_id . $this->id . '_' . $key) . '">' . wp_kses_post($data['title']) . '</label>';

                if ($tip)
                    $html .= '<img class="help_tip" data-tip="' . esc_attr($tip) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />';

                $html .= '</th>' . "\n";
                $html .= '<td class="forminp">' . "\n";
                $html .= '<fieldset><legend class="screen-reader-text"><span>' . wp_kses_post($data['title']) . '</span></legend>' . "\n";
                $html .= '<input class="input-text regular-input ' . esc_attr($data['class']) . '" type="' . esc_attr($data['type']) . '" name="' . esc_attr($this->plugin_id . $this->id . '_' . $key) . '" id="' . esc_attr($this->plugin_id . $this->id . '_' . $key) . '" style="' . esc_attr($data['css']) . '" value="' . esc_attr(plugins_url('/validate-payment.php', __FILE__)) . '" placeholder="' . esc_attr($data['placeholder']) . '" ' . disabled($data['disabled'], true, false) . ' ' . implode(' ', $custom_attributes) . ' />';

                if ($description)
                    $html .= ' <p class="description">' . wp_kses_post($description) . '</p>' . "\n";

                $html .= '</fieldset>';
                $html .= '</td>' . "\n";
                $html .= '</tr>' . "\n";

                return $html;
            }
            
            public function process_admin_options() {
                if ($this->_isGteWoo('2.6')) {
                    $result = parent::process_admin_options();
                    $this->init_settings();
                    $this->pingMaksekeskusServer();
                    return $result;
                } else {
                    $this->validate_settings_fields();

                    if (count($this->errors) > 0) {
                        $this->display_errors();
                        return false;
                    } else {

                        update_option($this->plugin_id . $this->id . '_settings', $this->sanitized_fields);
                        if ($this->isSavingCurrentSection() || $this->_isWoo24()) {
                            $this->init_settings();
                            $this->pingMaksekeskusServer();
                        }

                        return true;
                    }
                }
            }

            public function getCheckoutJsUrl() {
                return $this->_MaksekeskusUrls['checkout_js_url' . $this->get_option('connection_mode')];
            }
            public function getApiUrl() {
                return $this->_MaksekeskusUrls['api_url' . $this->get_option('connection_mode')];
            }
            
            
            public function pingMaksekeskusServer() {
                $this->_api; //on täidetud

                delete_option('payment/eabi_maksekeskus/cached_methods');
                delete_option('payment/eabi_maksekeskus/cached_stamp');
                if ($this->get_option('enabled') == 'yes') {
                    try {
                        $this->_api->shopConfiguration();
                        WC_Admin_Settings::add_message(__('Connection to Maksekeskus server was successful', $this->_plugin_text_domain));
                    } catch (Eabi_Maksekeskus_Exception $ex) {
                        WC_Admin_Settings::add_error(sprintf(__('Connection to Maksekeskus server failed with message: %s', $this->_plugin_text_domain), $ex->getMessage()));
                    }
                }
            }

            public static function getEabiVersion() {
                return '2.33';
            }

        }

        class woocommerce_banklinkmaksekeskus_cc extends woocommerce_banklinkmaksekeskus {

            public $id = 'banklinkmaksekeskuscc';
            protected $_parent_id = 'banklinkmaksekeskus';
            protected $_paymentMethodType = self::PAYMENT_METHOD_TYPE_CARDS;

            public function __construct() {
                parent::__construct();
                $this->title = $this->get_option('title_cc');
            }

            /**
             * Initialise Gateway Settings
             *
             * Store all settings in a single database entry
             * and make sure the $settings array is either the default
             * or the settings stored in the database.
             *
             * @since 1.0.0
             * @uses get_option(), add_option()
             * @access public
             * @return void
             */
            public function init_settings() {

                if (!empty($this->settings))
                    return;

                // Load form_field settings
                $this->settings = get_option($this->plugin_id . $this->_parent_id . '_settings', null);

                if (!$this->settings || !is_array($this->settings)) {

                    // If there are no settings defined, load defaults
                    if ($this->form_fields)
                        foreach ($this->form_fields as $k => $v)
                            $this->settings[$k] = isset($v['default']) ? $v['default'] : '';
                }

                if ($this->settings && is_array($this->settings)) {
                    $this->settings = array_map(array($this, 'format_settings_eabi'), $this->settings);
                    $this->enabled = $this->get_option('enabled_cc') == 'yes';
                }
            }
            
    public function format_settings_eabi( $value ) {
    	return ( is_array( $value ) ) ? $value : html_entity_decode( $value );
    }
            

            public function process_admin_options() {
                if ($this->_isGteWoo('2.6')) {
                    $this->init_settings();

                    $post_data = $this->get_post_data();

                    foreach ($this->get_form_fields() as $key => $field) {
                        if ('title' !== $this->get_field_type($field)) {
                            try {
                                $this->settings[$key] = $this->get_field_value($key, $field, $post_data);
                            } catch (Exception $e) {
                                $this->add_error($e->getMessage());
                            }
                        }
                    }

                    return update_option($this->plugin_id . $this->_parent_id . '_settings', apply_filters('woocommerce_settings_api_sanitized_fields_' . $this->id, $this->settings));
                } else {
                    $this->validate_settings_fields();

                    if (count($this->errors) > 0) {
                        $this->display_errors();
                        return false;
                    } else {

                        update_option($this->plugin_id . $this->_parent_id . '_settings', $this->sanitized_fields);

                        return true;
                    }
                }
            }

            /**
             * <p>If API mode is enabled, then this validation verifies, that customer has selected suitable payment option</p>
             * @return boolean
             */
            public function validate_fields() {
                if ($this->_api) {
                    $this->_getWooCommerce()->session->eabi_maksekeskus_preselected_method = 'visa';
                } else {
                    return false;
                }

                return true;
            }

        }

    }

    function woocommerce_payment_banklinkmaksekeskus_estonia_addmethod($methods) {
        /**
         * Add the gateway to WooCommerce
         * */
        $methods[] = 'woocommerce_banklinkmaksekeskus';
        $methods[] = 'woocommerce_banklinkmaksekeskus_cc';
        return $methods;
    }

    add_action('plugins_loaded', 'woocommerce_payment_banklinkmaksekeskus_estonia_init');

    add_action('woocommerce_payment_gateways', 'woocommerce_payment_banklinkmaksekeskus_estonia_addmethod');
}
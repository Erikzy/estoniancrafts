<?php
/**
 * Plugin Name: WSDesk - Wordpress Support Ticket System (BASIC)
 * Plugin URI: https://www.wsdesk.com
 * Description: Enhances your customer service and enables efficient handling of customer issues.
 * Version: 1.0.2
 * Author: WSDesk
 * Author URI: https://www.wsdesk.com
 */
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('EH_CRM_MAIN_URL')) {
    define('EH_CRM_MAIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('EH_CRM_MAIN_PATH')) {
    define('EH_CRM_MAIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('EH_CRM_MAIN_IMG')) {
    define('EH_CRM_MAIN_IMG', EH_CRM_MAIN_URL . "assets/img/");
}
if (!defined('EH_CRM_MAIN_CSS')) {
    define('EH_CRM_MAIN_CSS', EH_CRM_MAIN_URL . "assets/css/");
}
if (!defined('EH_CRM_MAIN_JS')) {
    define('EH_CRM_MAIN_JS', EH_CRM_MAIN_URL . "assets/js/");
}
if (!defined('EH_CRM_MAIN_VENDOR')) {
    define('EH_CRM_MAIN_VENDOR', EH_CRM_MAIN_PATH . "vendor/");
}
if (!defined('EH_CRM_MAIN_VIEWS')) {
    define('EH_CRM_MAIN_VIEWS', EH_CRM_MAIN_PATH . "views/");
}
if (!defined('EH_CRM_VERSION')) {
    define('EH_CRM_VERSION', '1.0.1');
}

require_once(ABSPATH . "wp-admin/includes/plugin.php");
// Change the Pack IF BASIC  mention switch('BASIC') ELSE mention switch('PREMIUM')
switch ('BASIC') {
    case 'PREMIUM':
        $conflict = 'basic';
        $base = 'premium';
        break;
    case 'BASIC':
        $conflict = 'premium';
        $base = 'basic';
        break;
}
// Enter your plugin unique option name below $option_name variable
$option_name = 'wsdesk_pack';
if (get_option($option_name) == $conflict) {
    add_action('admin_notices', 'wsdesk_admin_notices', 99);
    deactivate_plugins(plugin_basename(__FILE__));
    function wsdesk_admin_notices() {
        is_admin() && add_filter('gettext', function($translated_text, $untranslated_text, $domain) {
                    $old = array(
                        "Plugin <strong>activated</strong>.",
                        "Selected plugins <strong>activated</strong>."
                    );
                    $error_text = '';
                    // Change the Pack IF BASIC  mention switch('BASIC') ELSE mention switch('PREMIUM')
                    switch ('BASIC') {
                        case 'PREMIUM':
                            $error_text = "BASIC Version of this Plugin Installed. Please uninstall the BASIC Version before activating PREMIUM.";
                            break;
                        case 'BASIC':
                            $error_text = "PREMIUM Version of this Plugin Installed. Please uninstall the PREMIUM Version before activating BASIC.";
                            break;
                    }
                    $new = "<span style='color:red'>" . $error_text . "</span>";
                    if (in_array($untranslated_text, $old, true)) {
                        $translated_text = $new;
                    }
                    return $translated_text;
                }, 99, 3);
    }

    return;
} else {
    update_option($option_name, $base);
    
    function eh_crm_run() {
        if (!class_exists('CRM_Init_Handler')) {
            require_once (EH_CRM_MAIN_PATH . "includes/class-crm-public-functions.php");
            require_once (EH_CRM_MAIN_PATH . "includes/class-crm-init-handler.php");
            require_once (EH_CRM_MAIN_PATH . "includes/class-crm-ajax-functions.php");
            require_once (EH_CRM_MAIN_PATH . "includes/class-crm-settings-handler.php");
            require_once (EH_CRM_MAIN_PATH . "includes/class-crm-email-oauth.php");
            require_once (EH_CRM_MAIN_PATH . "includes/class-crm-cron-setup.php");
            require_once (EH_CRM_MAIN_PATH . "includes/Mailbox.php");
            require_once (EH_CRM_MAIN_PATH . "includes/IncomingMail.php");
            new EH_CRM_Init_Handler();
            new EH_CRM_Cron_Setup();
        }
    }

    eh_crm_run();
    add_action('admin_init', 'wsdesk_welcome');
    add_action('admin_menu', 'wsdesk_welcome_screen');
    add_action('admin_head', 'wsdesk_welcome_screen_remove_menus');
    register_activation_hook(__FILE__, 'eh_crm_install');
    register_deactivation_hook(__FILE__, 'wsdesk_deactivate_work');
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'wsdesk_action_link');
//    add_filter('plugin_row_meta', 'wsdesk_plugin_row_meta', 10, 2);
    add_action( 'plugins_loaded', 'eh_crm_update_function' );

    function eh_crm_update_function() {
        global $base;
        if(get_option('wsdesk_version_'.$base) != EH_CRM_VERSION)
        {
            require_once (EH_CRM_MAIN_PATH . "includes/class-crm-install-functions.php");
            EH_CRM_Install::update_tables($base);
        }
    }
    
    function wsdesk_deactivate_work() {
        $cron = new EH_CRM_Cron_Setup();
        $cron->crawler_schedule_terminate();
        update_option('wsdesk_pack', '');
    }

    function eh_crm_install() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        require_once (EH_CRM_MAIN_PATH . "includes/class-crm-install-functions.php");
        EH_CRM_Install::install_tables();
        set_transient('_welcome_screen_activation_redirect', true, 30);
    }

    function wsdesk_welcome() {
        if (!get_transient('_welcome_screen_activation_redirect')) {
            return;
        }
        delete_transient('_welcome_screen_activation_redirect');
        wp_safe_redirect(add_query_arg(array('page' => 'WSDesk-Welcome'), admin_url('index.php')));
    }

    function wsdesk_welcome_screen() {
        add_dashboard_page('Welcome To WSDesk', 'Welcome To WSDesk', 'read', 'WSDesk-Welcome', 'wsdesk_screen_content');
    }

    function wsdesk_screen_content() {
        include EH_CRM_MAIN_VIEWS . 'welcome/welcome.php';
    }

    function wsdesk_welcome_screen_remove_menus() {
        remove_submenu_page('index.php', 'WSDesk-Welcome');
    }

    function wsdesk_action_link($links) {
        $plugin_links = array(
            '<a href="' . admin_url('admin.php?page=wsdesk_tickets') . '">Tickets</a>',
            '<a href="' . admin_url('admin.php?page=wsdesk_settings') . '">Settings</a>'
        );
        return array_merge($plugin_links, $links);
    }

    function wsdesk_plugin_row_meta($links, $file) {
        if ($file == plugin_basename(__FILE__)) {
            $row_meta = array(
                '<a href="https://www.wsdesk.com" target="_blank">Documentation</a>',
                '<a href="https://www.wsdesk.com/" target="_blank">Support</a>'
            );
            return array_merge($links, $row_meta);
        }
        return (array) $links;
    }

}

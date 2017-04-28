<?php

if (!defined('ABSPATH')) {
    exit;
}

class EH_CRM_Init_Handler {

    private $settings;

    function __construct() {
        add_action('admin_menu', array($this, 'eh_crm_menu_add'));
        add_action('admin_init', array($this, 'eh_register_styles_scripts'));
        add_action('wp_ajax_eh_crm_search_post', array("CRM_Ajax", 'eh_crm_search_post'));
        add_action('wp_ajax_nopriv_eh_crm_search_post', array("CRM_Ajax", 'eh_crm_search_post'));
        add_action('wp_ajax_eh_crm_search_tags', array("CRM_Ajax", 'eh_crm_search_tags'));
        add_action('wp_ajax_eh_crm_ticket_general', array("CRM_Ajax", "eh_crm_ticket_general"));
        add_action('wp_ajax_eh_crm_ticket_appearance', array("CRM_Ajax", "eh_crm_ticket_appearance"));
        add_action('wp_ajax_eh_crm_ticket_field', array("CRM_Ajax", "eh_crm_ticket_field"));
        add_action('wp_ajax_eh_crm_ticket_field_edit', array("CRM_Ajax", "eh_crm_ticket_field_edit"));
        add_action('wp_ajax_eh_crm_ticket_label', array("CRM_Ajax", "eh_crm_ticket_label"));
        add_action('wp_ajax_eh_crm_ticket_label_edit', array("CRM_Ajax", "eh_crm_ticket_label_edit"));
        add_action('wp_ajax_eh_crm_ticket_tag', array("CRM_Ajax", "eh_crm_ticket_tag"));
        add_action('wp_ajax_eh_crm_ticket_tag_edit', array("CRM_Ajax", "eh_crm_ticket_tag_edit"));
        add_action('wp_ajax_eh_crm_agent_add', array("CRM_Ajax", "eh_crm_agent_add"));
        add_action('wp_ajax_eh_crm_edit_agent_html', array("CRM_Ajax", "eh_crm_edit_agent_html"));
        add_action('wp_ajax_eh_crm_edit_agent', array("CRM_Ajax", "eh_crm_edit_agent"));
        add_action('wp_ajax_eh_crm_remove_agent', array("CRM_Ajax", "eh_crm_remove_agent"));
        add_action('wp_ajax_eh_crm_new_ticket_post', array("CRM_Ajax", "eh_crm_new_ticket_post"));
        add_action('wp_ajax_nopriv_eh_crm_new_ticket_post', array("CRM_Ajax", "eh_crm_new_ticket_post"));
        add_action('wp_ajax_eh_crm_new_ticket_form', array("CRM_Ajax", "eh_crm_new_ticket_form"));
        add_action('wp_ajax_nopriv_eh_crm_new_ticket_form', array("CRM_Ajax", "eh_crm_new_ticket_form"));
        add_action('wp_ajax_eh_crm_ticket_single_view', array("CRM_Ajax", "eh_crm_ticket_single_view"));
        add_action('wp_ajax_eh_crm_ticket_single_save_props', array("CRM_Ajax", "eh_crm_ticket_single_save_props"));
        add_action('wp_ajax_eh_crm_ticket_single_delete', array("CRM_Ajax", "eh_crm_ticket_single_delete"));
        add_action('wp_ajax_eh_crm_ticket_multiple_delete', array("CRM_Ajax", "eh_crm_ticket_multiple_delete"));
        add_action('wp_ajax_eh_crm_ticket_refresh_left_bar', array("CRM_Ajax", "eh_crm_ticket_refresh_left_bar"));
        add_action('wp_ajax_eh_crm_ticket_refresh_right_bar', array("CRM_Ajax", "eh_crm_ticket_refresh_right_bar"));
        add_action('wp_ajax_eh_crm_ticket_reply_agent', array("CRM_Ajax", "eh_crm_ticket_reply_agent"));
        add_action('wp_ajax_eh_crm_ticket_single_ticket_action', array("CRM_Ajax", "eh_crm_ticket_single_ticket_action"));
        add_action('wp_ajax_eh_crm_ticket_multiple_ticket_action', array("CRM_Ajax", "eh_crm_ticket_multiple_ticket_action"));
        add_action('wp_ajax_eh_crm_ticket_search', array("CRM_Ajax", "eh_crm_ticket_search"));
        add_action('wp_ajax_eh_crm_ticket_add_new', array("CRM_Ajax", "eh_crm_ticket_add_new"));
        add_action('wp_ajax_eh_crm_ticket_new_submit', array("CRM_Ajax", "eh_crm_ticket_new_submit"));
        add_action('wp_ajax_eh_crm_check_ticket_request', array("CRM_Ajax", "eh_crm_check_ticket_request"));
        add_action('wp_ajax_nopriv_eh_crm_check_ticket_request', array("CRM_Ajax", "eh_crm_check_ticket_request"));
        add_action('wp_ajax_eh_crm_ticket_single_view_client', array("CRM_Ajax", "eh_crm_ticket_single_view_client"));
        add_action('wp_ajax_eh_crm_ticket_reply_raiser', array("CRM_Ajax", "eh_crm_ticket_reply_raiser"));
        add_action('wp_ajax_eh_crm_ticket_client_section_load', array("CRM_Ajax", "eh_crm_ticket_client_section_load"));
        add_action('wp_ajax_eh_crm_activate_oauth', array("CRM_Ajax", "eh_crm_activate_oauth"));
        add_action('wp_ajax_eh_crm_deactivate_oauth', array("CRM_Ajax", "eh_crm_deactivate_oauth"));
        add_action('wp_ajax_eh_crm_activate_email_protocol', array("CRM_Ajax", "eh_crm_activate_email_protocol"));
        add_action('wp_ajax_eh_crm_deactivate_email_protocol', array("CRM_Ajax", "eh_crm_deactivate_email_protocol"));
        add_action('wp_ajax_eh_crm_email_support_save', array("CRM_Ajax", "eh_crm_email_support_save"));
        add_action('wp_ajax_eh_crm_zendesk_library', array("CRM_Ajax", "eh_crm_zendesk_library"));
        add_action('wp_ajax_eh_crm_zendesk_pull_tickets', array("CRM_Ajax", "eh_crm_zendesk_pull_tickets"));
        add_action('wp_ajax_eh_crm_zendesk_stop_pull_tickets', array("CRM_Ajax", "eh_crm_zendesk_stop_pull_tickets"));
        add_action('wp_ajax_eh_crm_zendesk_save_data', array("CRM_Ajax", "eh_crm_zendesk_save_data"));
        add_action('wp_ajax_eh_crm_live_log', array("CRM_Ajax", "eh_crm_live_log"));
        $this->settings = new EH_CRM_Settings_Handler();
        add_shortcode('wsdesk_support', array($this, 'eh_crm_support_page'));
        add_action('wp_enqueue_scripts',array($this,'support_shortcode_scripts'));
    }

    function eh_crm_menu_add() {
        $id = get_current_user_id();
        $user = new WP_User($id);
        $auth = FALSE;
        $user_role = $user->roles;
        $user_roles_default = array("WSDesk_Agents", "WSDesk_Supervisor","administrator");
        foreach($user_role as $value)
        {
            if(in_array($value, $user_roles_default))
            {
                $auth = TRUE;
            }
        }
        if($auth)
        {
            if(in_array("administrator", $user_role))
            {
                $cap = "administrator";
            }
            else
            {
                $cap = "crm_role";
            }
            add_menu_page("Tickets", "WSDesk", $cap, "wsdesk_tickets", array($this->settings, "eh_crm_tickets_main_menu_callback"), "dashicons-tickets", 25);
            add_submenu_page('wsdesk_tickets', 'Tickets', 'Tickets', $cap, 'wsdesk_tickets', array($this->settings, 'eh_crm_tickets_main_menu_callback'));
            if($user->has_cap("settings_page") || in_array("administrator", $user_role))
            {
                add_submenu_page('wsdesk_tickets', 'Settings', 'Settings', $cap, 'wsdesk_settings', array($this->settings, 'eh_crm_settings_sub_menu_callback'));
            }
            if($user->has_cap("agents_page") || in_array("administrator", $user_role))
            {
                add_submenu_page('wsdesk_tickets', 'Agents', 'Agents', $cap, 'wsdesk_agents', array($this->settings, 'eh_crm_agents_sub_menu_callback'));
            }
            add_submenu_page('wsdesk_tickets', 'Reports', 'Reports', $cap, 'wsdesk_reports', array($this->settings, 'eh_crm_reports_sub_menu_callback'));
        }
        add_submenu_page('wsdesk_tickets', 'E-Mail', 'E-Mail', 'administrator', 'wsdesk_email', array($this->settings, 'eh_crm_email_sub_menu_callback'));
        add_submenu_page('wsdesk_tickets', 'Import', 'Import', 'administrator', 'wsdesk_import', array($this->settings, 'eh_crm_import_sub_menu_callback'));
    }

    function eh_register_styles_scripts() {
        $page = (isset($_GET['page']) ? $_GET['page'] : '');
        $include_page = array("wsdesk_tickets", "wsdesk_settings", "wsdesk_agents", "wsdesk_email","wsdesk_reports","wsdesk_import");
        if (in_array($page, $include_page)) {
            wp_enqueue_script("bootstrap", EH_CRM_MAIN_JS . "bootstrap.js");
            wp_enqueue_style("bootstrap", EH_CRM_MAIN_CSS . "bootstrap.css");
            wp_enqueue_script("dialog", EH_CRM_MAIN_JS . "dialog.js");
            wp_enqueue_style("dialog", EH_CRM_MAIN_CSS . "dialog.css");
            wp_enqueue_style("boot", EH_CRM_MAIN_CSS . "boot.css");
            wp_enqueue_script("jquery");
            if ($page === 'wsdesk_tickets') {
                wp_enqueue_script("crm_tickets", EH_CRM_MAIN_JS . "crm_tickets.js");
                wp_enqueue_style("crm_tickets", EH_CRM_MAIN_CSS . "crm_tickets.css");
                wp_enqueue_style("select2", EH_CRM_MAIN_CSS . "select2.css");
                wp_enqueue_script("select2", EH_CRM_MAIN_JS . "select2.js");
            }
            if ($page === 'wsdesk_settings') {
                wp_enqueue_script("jquery-ui-sortable");
                wp_enqueue_script("crm_settings", EH_CRM_MAIN_JS . "crm_settings.js");
                wp_enqueue_style("crm_settings", EH_CRM_MAIN_CSS . "crm_settings.css");
                wp_enqueue_script("fieldChooser", EH_CRM_MAIN_JS . "fieldChooser.js");
                wp_enqueue_style("select2", EH_CRM_MAIN_CSS . "select2.css");
                wp_enqueue_script("select2", EH_CRM_MAIN_JS . "select2.js");
            }
            if ($page === 'wsdesk_agents') {
                wp_enqueue_script("crm_agents", EH_CRM_MAIN_JS . "crm_agents.js");
                wp_enqueue_style("crm_agents", EH_CRM_MAIN_CSS . "crm_agents.css");
                wp_enqueue_style("select2", EH_CRM_MAIN_CSS . "select2.css");
                wp_enqueue_script("select2", EH_CRM_MAIN_JS . "select2.js");
            }
            if ($page === 'wsdesk_email') {
                wp_enqueue_script("crm_email", EH_CRM_MAIN_JS . "crm_email.js");
                wp_enqueue_style("crm_email", EH_CRM_MAIN_CSS . "crm_email.css");
            }
            if($page === "wsdesk_reports")
            {
                wp_enqueue_style("select2", EH_CRM_MAIN_CSS . "select2.css");
                wp_enqueue_script("select2", EH_CRM_MAIN_JS . "select2.js");
                wp_enqueue_script("raphael", EH_CRM_MAIN_JS . "raphael.js");
                wp_enqueue_script("morris", EH_CRM_MAIN_JS . "morris.js");
                wp_enqueue_script("crm_reports", EH_CRM_MAIN_JS . "crm_reports.js");
                wp_enqueue_style("crm_reports", EH_CRM_MAIN_CSS . "crm_reports.css");
            }
            if ($page === 'wsdesk_import') {
                wp_enqueue_style("select2", EH_CRM_MAIN_CSS . "select2.css");
                wp_enqueue_script("select2", EH_CRM_MAIN_JS . "select2.js");
                wp_enqueue_script("crm_import", EH_CRM_MAIN_JS . "crm_import.js");
                wp_enqueue_style("crm_import", EH_CRM_MAIN_CSS . "crm_import.css");
            }
        }
    }

    function eh_crm_support_page() {
        return include(EH_CRM_MAIN_VIEWS."support/crm_support_page.php");
    }

    function support_shortcode_scripts() {
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'wsdesk_support')) {
            wp_enqueue_script( 'jquery' );
            $handle = 'bootstrap.min.js';
            $handle1 = 'bootstrap.js';
            $list = 'enqueued';
            if (!wp_script_is( $handle, $list ) && !wp_script_is( $handle1, $list )) {
                wp_enqueue_script("bootstrap", EH_CRM_MAIN_JS . "bootstrap.js");
            }
            wp_enqueue_script('support_scripts',EH_CRM_MAIN_JS . "crm_support.js");
            wp_enqueue_style("support_styles", EH_CRM_MAIN_CSS . "crm_support.css");
            wp_localize_script( 'support_scripts', 'support_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
        }
    }
}

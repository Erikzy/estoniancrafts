<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$title= eh_crm_get_settingsmeta(0, 'main_ticket_form_title');
?>
<div class="eh_crm_support_main">
    <div class="support_option_choose">
        <?php echo ($title!=='')?'<h3>'.$title.'</h3>':''; ?>
        <button data-loading-text="Fetching Request Form..." class="btn btn-primary eh_crm_new_request button  btn-add smaller-gray-button ">
            Submit a Request
        </button>
        <br>
        <br>
        <button data-loading-text="Loading your Request..." class="btn btn-primary eh_crm_check_request button  btn-add smaller-gray-button ">
            Check your Existing Request
        </button>
    </div>
    <div class="ticket_table_wrapper">
    </div>
</div>
<?php
return ob_get_clean();
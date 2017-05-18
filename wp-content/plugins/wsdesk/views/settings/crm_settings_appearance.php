<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$input_width = eh_crm_get_settingsmeta('0', "input_width");
$main_ticket_form_title = eh_crm_get_settingsmeta('0', "main_ticket_form_title");
$new_ticket_form_title = eh_crm_get_settingsmeta('0', "new_ticket_form_title");
$existing_ticket_title = eh_crm_get_settingsmeta('0', "existing_ticket_title");
?>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="input_elements_width" style="padding-right:1em !important;">Input Width</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Set custom width for input elements</span>
        <div class="input-group">
            <input type="number" class="form-control" id="input_elements_width" value="<?php echo $input_width; ?>" placeholder="Enter Width" aria-describedby="basic-addon2">
            <span class="input-group-addon" id="basic-addon2">%</span>
        </div>
    </div>
</div>
<span class="crm-divider"></span>                            
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="main_ticket_form_title" style="padding-right:1em !important;">Main Support Form Title</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Set custom title for the main support form</span>
        <input type="text" class="form-control" id="main_ticket_form_title" value="<?php echo $main_ticket_form_title; ?>" placeholder="Enter Title">
    </div>
</div>
<span class="crm-divider"></span>                            
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="new_ticket_form_title" style="padding-right:1em !important;">New Support Form Title</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Set custom title for new support form</span>
        <input type="text" class="form-control" id="new_ticket_form_title" value="<?php echo $new_ticket_form_title; ?>" placeholder="Enter Title">
    </div>
</div>
<span class="crm-divider"></span>                            
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="existing_ticket_title" style="padding-right:1em !important;">Existing Support Form Title</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Set custom title for the existing form</span>
        <input type="text" class="form-control" id="existing_ticket_title" value="<?php echo $existing_ticket_title; ?>" placeholder="Enter Title">
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <button type="button" id="save_appearance" class="btn btn-primary btn-sm">Save Appearance</button>
</div>
<?php
return ob_get_clean();

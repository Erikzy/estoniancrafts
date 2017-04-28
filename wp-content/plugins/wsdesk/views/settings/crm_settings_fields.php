<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$args = array("type" => "field");
$fields = array("slug","title","settings_id");
$avail_fields = eh_crm_get_settings($args,$fields);
$selected = eh_crm_get_settingsmeta("0", "selected_fields");
if(empty($selected))
{
    $selected = array();
}
?>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_fields" style="padding-right:1em !important;">Ticket Fields</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">In order to configure your Ticket Fields, drag from the left column to the right column</span>
        <div class="panel panel-default crm-panel">
            <div class="panel-body" style="padding: 5px !important;">
                <div class="col-md-6" style="text-align: center;padding: 5px 0px;">Available Fields<?php echo (count($avail_fields)<4)?" ( No Fields )":'';?></div>
                <div class="col-md-6" style="text-align: center;padding: 5px 0px;">Selected Fields</div><br>
                <center>                
                <div id="ticket_fields_configure_available">
                    <?php
                        for($i=3;$i<count($avail_fields);$i++)
                        {
                            $field_type = eh_crm_get_settingsmeta($avail_fields[$i]['settings_id'], "field_type");
                            if(!in_array($avail_fields[$i]['slug'], $selected))
                            {
                                echo '<div id="'.$avail_fields[$i]['slug'].'"> <span class="fc-field-name col-md-1">'.$avail_fields[$i]['title'].'</span><span class="fc-field-type col-md-1">[ '. ucfirst($field_type).' ]</span> </div>';
                            }
                        }
                    ?>
                </div>
                <div id="ticket_fields_configure_selected" tabindex="1">
                    <?php
                        for($i=0;$i<count($selected);$i++)
                        {
                            for($j=3;$j<count($avail_fields);$j++)
                            {
                                if($avail_fields[$j]['slug'] === $selected[$i])
                                {
                                    $field_type = eh_crm_get_settingsmeta($avail_fields[$j]['settings_id'], "field_type");
                                    echo '<div id="'.$avail_fields[$j]['slug'].'"> <span class="fc-field-name col-md-1">'.$avail_fields[$j]['title'].'</span><span class="fc-field-type col-md-1">[ '. ucfirst($field_type).' ]</span> </div>';
                                }
                            }
                                
                        }
                    ?>
                </div>
                <div id="ticket_fields_configure_final" tabIndex="1"></div>
                </center>
            </div>
        </div>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_fields_remove" style="padding-right:1em !important;">Remove Ticket Fields</label>
    </div>
    <div class="col-md-9">
        <span class="help-block"> In order to remove Ticket Field, select it and click on Save</span>
        <select multiple="multiple" size="10" name="ticket_fields_remove" id="ticket_fields_remove" class="ticket_fields_remove">
            <?php
                for($i=3;$i<count($avail_fields);$i++)
                {
                    $field_type = eh_crm_get_settingsmeta($avail_fields[$i]['settings_id'], "field_type");
                    echo '<option value="'.$avail_fields[$i]["slug"].'">'.$avail_fields[$i]["title"].' -> [ '. ucfirst($field_type).' ]</option>';
                }
            ?>
        </select>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_field_add" style="padding-right:1em !important;">Add Ticket Field</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Want to add new Ticket Field? <input type="checkbox" style="margin-top: 0px; vertical-align: sub;"  id="add_new_field_yes" class="form-control" name="add_new_field_yes" value="yes"> Yes</span>
        <span style="vertical-align: middle;display: none;" id="ticket_field_add_section">
            <span class="help-block">Which type of field do you need? </span>
            <select id="ticket_field_add_type" style="width: 100% !important;display: inline !important" class="form-control" aria-describedby="helpBlock">
                <option value="">Select the Type of Field</option>
                <option value="text">Text Box</option>
                <option value="password">Password</option>
                <option value="select">Select</option>
                <option value="radio">Radio</option>
                <option value="checkbox">Checkbox</option>
                <option value="number">Number</option>
                <option value="email">Email</option>
                <option value="textarea">Text Area</option>
                <option value="file">Attachment</option>
            </select>
            <span style="vertical-align: middle;" id="ticket_field_add_append"></span>
        </span>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_field_edit" style="padding-right:1em !important;">Edit Ticket Field</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Select the field to edit?</span>
        <span style="vertical-align: middle;" id="ticket_field_edit_section">
            <select id="ticket_field_edit_type" style="width: 100% !important;display: inline !important" class="form-control" aria-describedby="helpBlock">
                <option value="">Choose the Field</option>
                <?php
                    for($i=0;$i<count($avail_fields);$i++)
                    {
                        $field_type = eh_crm_get_settingsmeta($avail_fields[$i]['settings_id'], "field_type");
                        echo '<option value="'.$avail_fields[$i]["slug"].'">'.$avail_fields[$i]["title"].' -> [ '. ucfirst($field_type).' ]</option>';
                    }
                ?>
            </select>
            <span style="vertical-align: middle;" id="ticket_field_edit_append"></span>
        </span>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <button type="button" id="save_ticket_fields" class="btn btn-primary btn-sm">Save Ticket Fields</button>
</div>
<?php
return ob_get_clean();
<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$args = array("type" => "label");
$fields = array("slug","title","settings_id");
$avail_labels= eh_crm_get_settings($args,$fields);
?>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_labels_available" style="padding-right:1em !important;">Available Ticket Labels</label>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default crm-panel">
            <div class="panel-body" style="padding: 5px !important;background-color: whitesmoke;">
                <ul class="list-group">
                    <?php
                        if(!empty($avail_labels))
                        {
                            for($i=0;$i<count($avail_labels);$i++)
                            {
                                $label_color = eh_crm_get_settingsmeta($avail_labels[$i]['settings_id'], "label_color");
                                echo '<li class="list-group-item"><span class="badge" style="background-color:'.$label_color.' !important;">'.$label_color.'</span>'.$avail_labels[$i]['title'].'</li>';
                            }
                        }
                        else
                        {
                            echo '<li class="list-group-item">There are no Labels! Create One Label.</li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_labels_remove" style="padding-right:1em !important;">Remove Ticket Labels</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">In order to remove Ticket Label, select it and click on Save</span>
        <select multiple="multiple" size="10" name="ticket_labels_remove" id="ticket_labels_remove" class="ticket_labels_remove">
            <?php
                for($i=3;$i<count($avail_labels);$i++)
                {
                    echo '<option value="'.$avail_labels[$i]['slug'].'">'.$avail_labels[$i]['title'].'</option>';

                }
            ?>
        </select>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_label_add" style="padding-right:1em !important;">Add Ticket Label</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Want to add new Ticket Label? <input type="checkbox" style="margin-top: 0px; vertical-align: sub;"  id="add_new_label_yes" class="form-control" name="add_new_label_yes" value="yes"> Yes</span>
        <span style="vertical-align: middle;display: none;" id="ticket_label_add_section">
            <span class="help-block">Enter Details for New Label? </span>
            <input type="text" id="ticket_label_add_title" placeholder="Enter Title" class="form-control crm-form-element-input">
            <span class="help-block">Do you want to change the Label color?.</span>
            <span style="vertical-align: middle;">
                <input type="color" id="ticket_label_add_color"/><span>Click and Pick the Color</span>
            </span>
            <span class="help-block">Want to use this field for Filter Tickets? </span>
            <input type="radio" style="margin-top: 0;" checked id="ticket_label_add_filter" class="form-control" name="ticket_label_add_filter" value="yes"> Yes! I will use it for Filter<br>
            <input type="radio" style="margin-top: 0;" id="ticket_label_add_filter" class="form-control" name="ticket_label_add_filter" value="no"> No! Just for Information
        </span>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_label_edit_type" style="padding-right:1em !important;">Edit Ticket Label</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Select the Label to edit?</span>
        <span style="vertical-align: middle;" id="ticket_label_edit_section">
            <select id="ticket_label_edit_type" style="width: 100% !important;display: inline !important" class="form-control" aria-describedby="helpBlock">
                <?php
                    if(!empty($avail_labels))
                    {
                        echo '<option value="">Choose the Label</option>';
                        for($i=0;$i<count($avail_labels);$i++)
                        {
                            echo '<option value="'.$avail_labels[$i]['slug'].'">'.$avail_labels[$i]['title'].'</option>';

                        }
                    }
                    else
                    {
                        echo '<option value="">No Labels Available</option>';
                    }
                ?>
            </select>
            <span style="vertical-align: middle;" id="ticket_label_edit_append"></span>
        </span>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <button type="button" id="save_ticket_labels" class="btn btn-primary btn-sm">Save Ticket Labels</button>
</div>
<?php
return ob_get_clean();
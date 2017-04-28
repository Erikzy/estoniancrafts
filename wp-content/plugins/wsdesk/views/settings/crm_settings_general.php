<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$users_data = get_users(array("role__in"=>array("administrator","WSDesk_Agents","WSDesk_Supervisor")));
$users = array();
$select = array();
for($i=0;$i<count($users_data);$i++)
{
    $current = $users_data[$i];
    $temp = array();
    $roles = $current->roles;
    foreach ($roles as $value) {
        $current_role = $value;
        array_push($temp,ucfirst(str_replace("_", " ", $current_role)));
    }
    $users[implode(' & ', $temp)][$current->ID] = $current->data->display_name;
}
$args = array("type" => "label");
$fields = array("slug","title","settings_id");
$avail_labels= eh_crm_get_settings($args,$fields);
$ticket_rows = eh_crm_get_settingsmeta('0', "ticket_rows");
?>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="default_assignee" style="padding-right:1em !important;">Default Assignee</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Set a default assignee for new tickets</span>
        <select id="default_assignee" style="width: 100% !important;display: inline !important" class="form-control" aria-describedby="helpBlock">
            <?php 
                $assignee = eh_crm_get_settingsmeta('0', "default_assignee");
                $tag_selected = '';
                $no_assignee = '';
                switch($assignee)
                {
                    case 'no_assignee':
                        $no_assignee = 'selected';
                        break;
                }
                echo '
                <option value="no_assignee" '.$no_assignee.'>No Assignee</option>';
                foreach ($users as $key => $value) {
                    echo '<optgroup label="'.$key.'">';
                    foreach ($value as $id => $name)
                    {
                        $selected = '';
                        if($assignee == $id)
                        {
                            $selected = 'selected';
                        }
                        echo '<option value="'.$id.'" '.$selected.'>'.$name.'</option>';
                    }
                    echo "</optgroup>";
                }
            ?>
        </select>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="default_label" style="padding-right:1em !important;">Default Label</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Set a default label for new tickets</span>
        <select id="default_label" style="width: 100% !important;display: inline !important" class="form-control" aria-describedby="helpBlock">
            <?php 
                $label= eh_crm_get_settingsmeta('0', "default_label");
                for($i=0;$i<count($avail_labels);$i++)
                {
                    $selected = '';
                    if($label === $avail_labels[$i]['slug'])
                    {
                        $selected = 'selected';
                    }
                    echo '<option value="'.$avail_labels[$i]['slug'].'" '.$selected.'>'.$avail_labels[$i]['title'].'</option>';
                }
            ?>
        </select>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_raiser" style="padding-right:1em !important;">Tickets Raisers</label>
    </div>
    <div class="col-md-9">
        <span class="help-block"> Who can raise the tickets?</span>
        <span style="vertical-align: middle;">
            <?php 
                $ticket_raiser = eh_crm_get_settingsmeta('0', "ticket_raiser");
                $all = '';
                $registered = '';
                $guest = '';
                switch ($ticket_raiser) {
                    case "all":
                        $all = 'checked';
                        $registered = '';
                        $guest = '';
                        break;
                    case "registered":
                        $all = '';
                        $registered = 'checked';
                        $guest = '';
                        break;
                    case "guest":
                        $all = '';
                        $registered = '';
                        $guest = 'checked';
                        break;
                }
            ?>
            <input type="radio" style="margin-top: 0;" id="ticket_raiser" class="form-control" name="ticket_raiser" <?php echo $all; ?> value="all"> All<br>
            <input type="radio" style="margin-top: 0;" id="ticket_raiser" class="form-control" name="ticket_raiser" <?php echo $registered; ?> value="registered"> Registerd Users<br>
            <input type="radio" style="margin-top: 0;" id="ticket_raiser" class="form-control" name="ticket_raiser" <?php echo $guest; ?> value="guest"> Guest Users
        </span>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_display_row" style="padding-right:1em !important;">Tickets Row</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">How many ticket rows are available per page?</span>
        <input type="text" id="ticket_display_row" placeholder="Enter Row Count" value="<?php echo $ticket_rows; ?>" class="form-control crm-form-element-input">
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <button type="button" id="save_general" class="btn btn-primary btn-sm">Save General</button>
</div>
<?php
return ob_get_clean();
<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$users_data = get_users(array("role__in"=>array("editor","contributor","author","shop_manager"),"role__not_in"=>array("WSDesk_Agents","WSDesk_Supervisor")));
$users = array();
$select = array();
for($i=0;$i<count($users_data);$i++)
{
    $current = $users_data[$i];
    $temp = array();
    $roles = $current->roles;
    foreach ($roles as $value) {
        $current_role = $value;
        $temp[$i] = ucfirst(str_replace("_", " ", $current_role));
    }
    $users[implode(' & ', $temp)][$current->ID] = $current->data->display_name;
    $select[$current->ID] = md5($current->data->user_email);
}
?>
<input type="hidden" id="user_key_hash" value='<?php echo json_encode($select);?>'>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="add_agents_select" style="padding-right:1em !important;">Add Users</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Add Single or Multiple users</span>
        <select class="add_agents_select" multiple="multiple">
            <?php 
            foreach ($users as $key => $value) {
                echo "<optgroup label=\"$key\">\n";
                foreach ($value as $id => $name)
                {
                    echo "<option value=\"$id\">$name</option>\n";
                }
                echo "</optgroup>\n";
              }
            ?>
        </select>
    </div>
</div>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="add_agents_role" style="padding-right:1em !important;">WSDesk Role</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Roles for the selected users</span>
        <span style="vertical-align: middle;">
            <input type="radio" style="margin-top: 0;" class="form-control" id="add_agents_role" name="add_agents_role" value="agents" checked> WSDesk Agents<br>
            <input type="radio" style="margin-top: 0;" class="form-control" id="add_agents_role" name="add_agents_role" value="supervisor"> WSDesk Supervisor<br>
        </span>
    </div>
</div>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="add_agents_rights" style="padding-right:1em !important;">WSDesk Rights</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">User(s) are entitled to the following rights</span>
        <span style="vertical-align: middle;" id="add_agents_access_rights">
            <input type="checkbox" style="margin-top: 0;" class="form-control" name="add_agents_rights" id="add_agents_rights_reply" value="reply"> Reply to Tickets<br>
            <input type="checkbox" style="margin-top: 0;" class="form-control" name="add_agents_rights" id="add_agents_rights_delete" value="delete"> Delete Tickets<br>
            <input type="checkbox" style="margin-top: 0;" class="form-control" name="add_agents_rights" id="add_agents_rights_manage" value="manage"> Manage Tickets<br>
        </span>
    </div>
</div>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="add_agents_tags" style="padding-right:1em !important;">Add tags</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Depending on the tags, the tickets will be assigned automatically to the default assignee</span>
        <select class="add_agents_tags" multiple="multiple">
        </select>
    </div>
</div>

<span class="crm-divider"></span>
<div class="crm-form-element">
    <button type="button" id="save_add_agents" class="btn btn-primary btn-sm">Save Add Agents</button>
</div>
<?php
return ob_get_clean();
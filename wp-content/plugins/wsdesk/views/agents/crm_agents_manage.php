<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$users = get_users(array("role__in" => array("WSDesk_Agents", "WSDesk_Supervisor")));
$users_data = array();
for ($i = 0; $i < count($users); $i++) {
    $current = $users[$i];
    $id = $current->ID;
    $user = new WP_User($id);
    $users_data[$i]['id'] = $id;
    $users_data[$i]['name'] = $user->display_name;
    $users_data[$i]['email'] = $user->user_email;
    $users_data[$i]['avatar'] = get_avatar_url($id);
    $users_data[$i]['role'] = $user->roles;
    $users_data[$i]['caps'] = $user->caps;
    $users_data[$i]['tags'] = get_user_meta($id, "wsdesk_tags", true);
}
?>
<div class="panel-group" id="manage_role" style="margin-bottom: 0px !important">
    <?php
    if(count($users_data)!==0)
    {
        for ($i = 0; $i < count($users_data); $i++) {
            $id = $users_data[$i]['id'];
            if (in_array("WSDesk_Agents", $users_data[$i]['role'])) {
                $role = 'WSDesk Agents';
            } else {
                $role = 'WSDesk Supervisor';
            }
            $roles_temp = $users_data[$i]['role'];
            $roles = array();
            foreach ($roles_temp as $value) {
                $current_role = $value;
                array_push($roles,ucfirst(str_replace("_", " ", $current_role)));
            }
            $caps_temp = array_keys($users_data[$i]['caps']);
            $caps = '';
            for ($j = 0; $j < count($caps_temp); $j++) {
                switch ($caps_temp[$j]) {
                    case "reply_tickets":
                        $caps .= '<span class="tags">Reply Tickets</span> ';
                        break;
                    case "delete_tickets":
                        $caps .= '<span class="tags">Delete Tickets</span> ';
                        break;
                    case "manage_tickets":
                        $caps .= '<span class="tags">Manage Tickets</span> ';
                        break;
                    case "settings_page":
                        $caps .= '<span class="tags">Settings Manage</span> ';
                        break;
                    case "agents_page":
                        $caps .= '<span class="tags">Agents Manage</span> ';
                        break;
                }
            }
            if($caps === '')
            {
                $caps.='No Capabilities Assigned';
            }
            $tags_temp = $users_data[$i]['tags'];
            $tags = '';
            if(!empty($tags_temp))
            {
                for ($j = 0; $j < count($tags_temp); $j++) {
                    $tag = eh_crm_get_settings(array("slug" => $tags_temp[$j], "type" => "tag"), array("title"));
                    if(!empty($tag))
                    {
                        $tags .= '<span class="tags">' . $tag[0]['title'] . '</span>';
                    }
                }
            }
            else
            {
                $tags.= 'No Tags Mapped';
            }
            if($tags=="")
            {
                $tags.= 'No Tags Mapped';
            }
            $overall_replies = eh_crm_get_ticket_value_count("ticket_parent", 0,TRUE,"ticket_author",$users_data[$i]['id']);
            $overall_assigned = eh_crm_get_ticketmeta_value_count("ticket_assignee",$users_data[$i]['id']);
            echo '<div class="panel panel-default">
                        <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#manage_role" data-target="#content_' . $id . '">
                            <span class ="manage-role-toggle"></span>
                            <h4 class="panel-title">
                                    ' . $users_data[$i]['name'] . ' ( ' . $role . ' )
                            </h4>
                        </div>
                        <div id="content_' . $id . '" class="panel-collapse collapse">
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <div class="well profile">
                                       <div class="col-sm-12">
                                           <div class="col-xs-12 col-sm-8">
                                               <h2>' . $users_data[$i]['name'] . '</h2>
                                               <p><strong>Roles: </strong> ' . implode(', ', $roles) . ' </p>
                                               <p><strong>Email: </strong> ' . $users_data[$i]['email'] . ' </p>
                                               <p><strong>Capability: </strong>
                                                   <span style="line-height:1.75"> ' . $caps . ' </span>
                                               </p>
                                           </div>             
                                           <div class="col-xs-12 col-sm-4 text-center">
                                                <center>
                                                    <figure>
                                                        <img src="' . $users_data[$i]['avatar'] . '" alt="" class="img-circle img-responsive">
                                                        <figcaption class="ratings">
                                                            <p style="line-height:1.75"><strong>Tags: </strong>
                                                                ' . $tags . '
                                                            </p>
                                                        </figcaption>
                                                    </figure>
                                                </center>
                                           </div>
                                       </div>            
                                       <div class="col-xs-12 divider text-center">
                                           <div class="col-xs-12 col-sm-4 emphasis">
                                               <h2><strong> '.count($overall_replies).' </strong></h2>                    
                                               <p><small>Overall Replies</small></p>
                                               <button class="btn btn-success btn-block edit_user" id="user_edit_' . $id . '"><span class="glyphicon glyphicon-edit"></span> Edit Profile</button>
                                           </div>
                                           <div class="col-xs-12 col-sm-4 emphasis">
                                               <h2><strong> '.count($overall_assigned).' </strong></h2>
                                               <p><small>Tickets Assigned</small></p>
                                               <a class="btn btn-info btn-block" target="_blank" href="'. admin_url("admin.php?page=wsdesk_reports&user=".$id).'" ><span class="glyphicon glyphicon-signal"></span> View Reports </a>
                                           </div>
                                           <div class="col-xs-12 col-sm-4 emphasis">
                                               <h2><strong> 0 </strong></h2>                    
                                               <p><small>Support Rating</small></p>
                                               <div class="btn-group dropup btn-block">
                                                    <button type="button" class="btn btn-primary dropdown-toggle" style="width:100% !important" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span class="glyphicon glyphicon-user"></span> Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                      <li><span class="user_actions user_actions_remove" id="user_actions_remove_' . $id . '"><span class="glyphicon glyphicon-remove pull-right"></span>Remove WSDesk Role</span></li>
                                                    </ul>
                                                </div>
                                           </div>
                                       </div>
                                    </div>                 
                                </div>
                            </div>
                            <div id="user_content_change_' . $id . '">
                            </div>
                        </div>                    
                    </div>';
        }
    }
    else
    {
        ?>
        <div style="text-align: center">
            <h1><span class="glyphicon glyphicon-screenshot" style="font-size: 2em;color: lightgrey;"></span></h1><br>
            <?php
                echo "No WSDesk role assigned to any users / No Agents added";
            ?>
        </div>
        <?php
    }
    ?>
</div>
<?php
return ob_get_clean();

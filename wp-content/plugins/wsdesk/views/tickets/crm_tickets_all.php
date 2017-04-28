<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$avail_labels_wf = eh_crm_get_settings(array("type" => "label"), array("slug", "title", "settings_id"));
$avail_labels_f = eh_crm_get_settings(array("type" => "label", "filter" => "yes"), array("slug", "title", "settings_id"));
$avail_tags_wf = eh_crm_get_settings(array("type" => "tag"), array("slug", "title", "settings_id"));
$avail_tags_f = eh_crm_get_settings(array("type" => "tag", "filter" => "yes"), array("slug", "title", "settings_id"));
$user_roles_default = array("WSDesk_Agents", "WSDesk_Supervisor","administrator");
$user_caps_default = array("reply_tickets","delete_tickets","manage_tickets");
$users = get_users(array("role__in" => $user_roles_default));
$users_data = array();
for ($i = 0; $i < count($users); $i++) {
    $current = $users[$i];
    $id = $current->ID;
    $user = new WP_User($id);
    $users_data[$i]['id'] = $id;
    $users_data[$i]['name'] = $user->display_name;
    $users_data[$i]['caps'] = $user->caps;
    $users_data[$i]['email'] = $user->user_email;
}
$table_title = 'All Tickets';
$ticket_rows = eh_crm_get_settingsmeta(0, "ticket_rows");
$section_tickets_id = eh_crm_get_ticket_value_count("ticket_parent",0,false,"","","ticket_id","DESC",$ticket_rows,0);
$avail_caps = array("reply_tickets","delete_tickets","manage_tickets");
$access = array();
$logged_user = wp_get_current_user();
$logged_user_caps = array_keys($logged_user->caps);
if(!in_array("administrator", $logged_user->roles))
{
    for($i=0;$i<count($logged_user_caps);$i++)
    {
        if(!in_array($logged_user_caps[$i], $avail_caps))
        {
            unset($logged_user_caps[$i]);
        }
    }
    $access = $logged_user_caps;
}
else
{
    $access = $avail_caps;
}
$current_page = 0;
?>
<div class="container">
    <div class="row">
        <div class="col-md-9 col-md-offset-3">
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle mulitple_ticket_action_button" data-toggle="dropdown">
                    Actions <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <?php
                        if(in_array("manage_tickets", $access) || in_array("delete_tickets", $access))
                        {
                            if(in_array("manage_tickets", $access))
                            {
                                for($j=0;$j<count($avail_labels_wf);$j++)
                                {
                                    echo '<li><a href="#" class="multiple_ticket_action" id="'.$avail_labels_wf[$j]['slug'].'">Mark as '.$avail_labels_wf[$j]['title'].'</a></li>';
                                }
                            }
                            if(in_array("delete_tickets", $access))
                            {
                                echo '<li class="divider"></li>';
                                echo '<li><a href="#" class="multiple_ticket_action" id="delete_tickets">Delete Tickets</a></li>';
                                echo '<li class="text-center"><small class="text-muted">Delete Tickets Will delete tickets Permanently</small></li>';
                            }
                        }
                        else
                        {
                            echo '<li style="padding: 3px 20px;">No Actions</li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-sm-4 col-md-3" id="left_bar_all_tickets" style="max-height: 100vh;overflow: auto;overflow-x: hidden;">
            <ul class="nav nav-pills nav-stacked side-bar-filter" id="all_section">
                <li class="active"><a href="#" id="all"><span class="badge pull-right"><?php echo count(eh_crm_get_ticket_value_count("ticket_parent",0)); ?></span> All Tickets </a></li>
            </ul>
            <hr>
            <h4>
                Labels
                <span class="spinner_loader labels_loader">
                    <span class="bounce1"></span>
                    <span class="bounce2"></span>
                    <span class="bounce3"></span>
                </span>
            </h4>
            <ul class="nav nav-pills nav-stacked side-bar-filter" id="labels">
                <?php
                    for ($i = 0; $i < count($avail_labels_f); $i++) {
                        $label_color = eh_crm_get_settingsmeta($avail_labels_f[$i]['settings_id'], "label_color");
                        $current_label_count=eh_crm_get_ticketmeta_value_count("ticket_label",$avail_labels_f[$i]['slug']);
                        echo '<li><a href="#" id="'.$avail_labels_f[$i]['slug'].'"><span class="badge pull-right" style="background-color:' . $label_color . ' !important;">'.count($current_label_count).'</span> '.$avail_labels_f[$i]['title'].' </a></li>';
                    }
                ?>
            </ul>
            <?php
            if(!empty($users_data))
            {
                ?>
                <hr>
                <h4>
                    Agents
                    <span class="spinner_loader agents_loader">
                        <span class="bounce1"></span>
                        <span class="bounce2"></span>
                        <span class="bounce3"></span>
                    </span>
                </h4>
                <ul class="nav nav-pills nav-stacked side-bar-filter" id="agents">
                    <?php
                        for ($i = 0; $i < count($users_data); $i++) {
                            $current_agent_count=eh_crm_get_ticketmeta_value_count("ticket_assignee",$users_data[$i]['id']);
                            echo '<li><a href="#" id="'.$users_data[$i]['id'].'"><span class="badge pull-right">'.count($current_agent_count).'</span> '.$users_data[$i]['name'].' </a></li>';
                        }
                        $current_agent_count=eh_crm_get_ticketmeta_value_count("ticket_assignee",array());
                    ?>
                    <li><a href="#" id="unassigned"><span class="badge pull-right"><?php echo count($current_agent_count);?></span> Unassigned </a></li>
                </ul>
                <?php 
            }
            ?>
            <?php
            if(!empty($avail_tags_f))
            {
                ?>
                <hr>
                <h4>
                    Tags
                    <span class="spinner_loader tags_loader">
                        <span class="bounce1"></span>
                        <span class="bounce2"></span>
                        <span class="bounce3"></span>
                    </span>
                </h4>
                <ul class="nav nav-pills nav-stacked side-bar-filter" id="tags">
                    <?php
                        for ($i = 0; $i < count($avail_tags_f); $i++) {
                            $current_tags_count=eh_crm_get_ticketmeta_value_count("ticket_tags",$avail_tags_f[$i]['slug']);
                            echo '<li><a href="#" id="'.$avail_tags_f[$i]['slug'].'"><span class="badge pull-right">'.count($current_tags_count).'</span> '.$avail_tags_f[$i]['title'].' </a></li>';
                        }
                    ?>
                </ul>
                <?php 
            }
            ?>
            <h4>
                Users
                <span class="spinner_loader users_loader">
                    <span class="bounce1"></span>
                    <span class="bounce2"></span>
                    <span class="bounce3"></span>
                </span>
            </h4>
            <ul class="nav nav-pills nav-stacked side-bar-filter" id="users">
                <?php
                    $registered_count = eh_crm_get_ticket_value_count("ticket_author",0,true,"ticket_parent",0);
                    echo '<li><a href="#" id="registeredU" class="user_section"><span class="badge pull-right">'.count($registered_count).'</span> Registered Users </a></li>';
                    $guest_count = eh_crm_get_ticket_value_count("ticket_author",0,false,"ticket_parent",0);
                    echo '<li><a href="#" id="guestU" class="user_section"><span class="badge pull-right">'.count($guest_count).'</span> Guest Users </a></li>';
                ?>
            </ul>
        </div>
        <div class="col-sm-10 col-md-9" id="right_bar_all_tickets" style="padding-right: 0px;">
            <div class="panel panel-default tickets_panel">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo $table_title;?>
                        <span class="spinner_loader table_loader">
                            <span class="bounce1"></span>
                            <span class="bounce2"></span>
                            <span class="bounce3"></span>
                        </span>
                    </h3>
                    <div class="pull-right">
                        <span class="clickable filter" data-toggle="tooltip" title="Tickets Filter" data-container="body">
                            <i class="glyphicon glyphicon-filter"></i>
                        </span>
                    </div>
                    <div class="pull-right" style="margin: -15px 0px 0px 0px;">
                        <span class="text-muted"><b><?php echo ($current_page!=0)?($current_page)*$ticket_rows:"1"; ?></b>–<b><?php echo (($current_page)*$ticket_rows)+count($section_tickets_id);?></b> of <b><?php echo count(eh_crm_get_ticket_value_count("ticket_parent",0)); ?></b></span>
                        <div class="btn-group btn-group-sm">
                            <?php
                                    if($current_page != 0)
                                    {
                                        ?>
                                            <button type="button"  class="btn btn-default pagination_tickets" id="prev" title="Previous <?php echo $ticket_rows?>" data-container="body">
                                                <span class="glyphicon glyphicon-chevron-left"></span>
                                            </button>
                                        <?php
                                    }
                            ?>                        
                            <input type="hidden" id="current_page_no" value="<?php echo $current_page ?>">
                            <?php 
                                    if(count($section_tickets_id) == $ticket_rows)
                                    {
                                        ?>
                                            <button type="button"  class="btn btn-default pagination_tickets" id="next" title="Next <?php echo $ticket_rows?>" data-container="body">
                                                <span class="glyphicon glyphicon-chevron-right"></span>
                                            </button>
                                        <?php
                                    }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <input type="text" class="form-control" id="dev-table-filter" data-action="filter" data-filters="#dev-table" placeholder="Filter Anything" />
                </div>
                <table class="table table-hover" id="dev-table">
                    <thead>
                        <tr class="except_view">
                            <th><input type="checkbox" class="ticket_select_all"></th>
                            <th>View</th>
                            <th>#</th>
                            <th>Requester</th>
                            <th>Subject</th>
                            <th>Requested</th>
                            <th>Assignee</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(empty($section_tickets_id))
                            {
                                echo '<tr class="except_view">
                                    <td colspan="12">No Tickets </td></tr>';
                            }
                            else
                            {
                                for($i=0;$i<count($section_tickets_id);$i++)
                                {
                                    $current = eh_crm_get_ticket(array("ticket_id"=>$section_tickets_id[$i]['ticket_id']));
                                    $current_meta = eh_crm_get_ticketmeta($section_tickets_id[$i]['ticket_id']);
                                    $action_value = '';
                                    $eye_color='';
                                    for($j=0;$j<count($avail_labels_wf);$j++)
                                    {
                                        if(in_array("manage_tickets", $access))
                                        {
                                            $action_value .= '<li id="'.$current[0]['ticket_id'].'"><a href="#" class="single_ticket_action" id="'.$avail_labels_wf[$j]['slug'].'">Mark as '.$avail_labels_wf[$j]['title'].'</a></li>';

                                        }
                                        if($avail_labels_wf[$j]['slug'] == $current_meta['ticket_label'])
                                        {
                                            $eye_color = eh_crm_get_settingsmeta($avail_labels_wf[$j]['settings_id'], "label_color");
                                        }
                                    }
                                    $ticket_raiser = $current[0]['ticket_email'];
                                    if($current[0]['ticket_author'] != 0)
                                    {
                                        $current_user = new WP_User($current[0]['ticket_author']);
                                        $ticket_raiser = $current_user->display_name;
                                    }
                                    $ticket_assignee_name =array();
                                    $ticket_assignee_email = array();
                                    if(isset($current_meta['ticket_assignee']))
                                    {
                                        $current_assignee = $current_meta['ticket_assignee'];
                                        for($k=0;$k<count($current_assignee);$k++)
                                        {
                                            for($l=0;$l<count($users_data);$l++)
                                            {
                                                if($users_data[$l]['id'] == $current_assignee[$k])
                                                {
                                                    array_push($ticket_assignee_name, $users_data[$l]['name']);
                                                    array_push($ticket_assignee_email, $users_data[$l]['email']);
                                                }
                                            }
                                        }
                                    }
                                    $ticket_assignee_name = empty($ticket_assignee_name)?"No Assignee":implode(", ", $ticket_assignee_name);
                                    $latest_reply_id = eh_crm_get_ticket_value_count("ticket_category","agent_note" ,true,"ticket_parent",$current[0]['ticket_id'],'ticket_id','DESC','1');
                                    $latest_content = array();
                                    $attach = "";
                                    if(!empty($latest_reply_id))
                                    {
                                        $latest_ticket_reply = eh_crm_get_ticket(array("ticket_id"=>$latest_reply_id[0]["ticket_id"]));
                                        $latest_content['content'] = $latest_ticket_reply[0]['ticket_content'];
                                        $latest_content['author_email'] = $latest_ticket_reply[0]['ticket_email'];
                                        $latest_content['reply_date'] = $latest_ticket_reply[0]['ticket_date'];
                                        if($latest_ticket_reply[0]['ticket_author'] != 0)
                                        {
                                            $reply_user = new WP_User($latest_ticket_reply[0]['ticket_author']);
                                            $latest_content['author_name'] = $reply_user->display_name;
                                        }
                                        else
                                        {
                                            $latest_content['author_name'] = "Guest";
                                        }
                                        $latest_reply_meta = eh_crm_get_ticketmeta($latest_reply_id[0]["ticket_id"]);
                                        if(isset($latest_reply_meta['ticket_attachment']))
                                        {
                                            $attach = ' | <small class="glyphicon glyphicon-pushpin"></small> <small style="opacity:0.7;"> '.count($latest_reply_meta['ticket_attachment']).' Attachment</small>';
                                        }
                                    }
                                    else
                                    {
                                        $latest_content['content'] = $current[0]['ticket_content'];
                                        $latest_content['author_email'] = $current[0]['ticket_email'];
                                        $latest_content['reply_date'] = $current[0]['ticket_date'];
                                        if($current[0]['ticket_author'] != 0)
                                        {
                                            $current_user = new WP_User($current[0]['ticket_author']);
                                            $latest_content['author_name'] = $current_user->display_name;
                                        }
                                        else
                                        {
                                            $latest_content['author_name'] = "Guest";
                                        }
                                        if(isset($current_meta['ticket_attachment']))
                                        {
                                            $attach = ' | <small class="glyphicon glyphicon-pushpin"></small> <small style="opacity:0.7;"> '.count($current_meta['ticket_attachment']).' Attachment</small>';
                                        }
                                    }
                                    $ticket_tags = "";
                                    if(!empty($avail_tags_wf))
                                    {
                                        for($j=0;$j<count($avail_tags_wf);$j++)
                                        {
                                            $current_ticket_tags=(isset($current_meta['ticket_tags'])?$current_meta['ticket_tags']:array());
                                            for($k=0;$k<count($current_ticket_tags);$k++)
                                            {
                                                if($avail_tags_wf[$j]['slug'] == $current_ticket_tags[$k])
                                                {
                                                    $ticket_tags .= '<span class="label label-info">#'.$avail_tags_wf[$j]['title'].'</span>';
                                                }
                                            }
                                        }
                                    }
                                    $ticket_rating = (isset($current_meta['ticket_rating'])?$current_meta['ticket_rating']:0);
                                    $raiser_voice = eh_crm_get_ticket_value_count("ticket_parent",$section_tickets_id[$i]['ticket_id'],false,"ticket_category","raiser_reply");
                                    $agent_voice = eh_crm_get_ticket_value_count("ticket_parent",$section_tickets_id[$i]['ticket_id'],false,"ticket_category","agent_reply");
                                    echo '
                                    <tr class="clickable ticket_row" id="'.$current[0]['ticket_id'].'">
                                        <td class="except_view"><input type="checkbox" class="ticket_select" id="ticket_select" value="'.$current[0]['ticket_id'].'"></td>
                                        <td class="except_view"><button class="btn btn-default btn-xs accordion-toggle quick_view_ticket" style="background-color: '.$eye_color.' !important" data-toggle="collapse" data-target="#expand_'.$current[0]['ticket_id'].'" ><span class="glyphicon glyphicon-eye-open"></span></button></td>
                                        <td>'.$current[0]['ticket_id'].'</td>
                                        <td>'.$ticket_raiser.'</td>
                                        <td class="wrap_content" data-toggle="tooltip" title="'.$current[0]['ticket_title'].'" data-container="body">'.$current[0]['ticket_title'].'</td>
                                        <td>'.get_date_from_gmt($current[0]['ticket_date'], "M d, Y h:i:s A").'</td>
                                        <td>'.$ticket_assignee_name.'</td>
                                    </tr>
                                    <tr class="except_view">
                                        <td colspan="12" class="hiddenRow">
                                            <div class="accordian-body collapse" id="expand_'.$current[0]['ticket_id'].'">
                                                <table class="table table-striped" style="margin-bottom: 0px !important">
                                                    <thead>
                                                        <tr>
                                                            <td colspan="12" style="white-space: normal;">
                                                            <div style="padding:5px 0px;">
                                                                <small class="glyphicon glyphicon-user"></small> <small style="opacity:0.7;">'.$latest_content['author_name'].'</small>
                                                                | <small class="glyphicon glyphicon-envelope"></small> <small style="opacity:0.7;">'.$latest_content['author_email'].'</small>
                                                                | <small class="glyphicon glyphicon-calendar"></small> <small style="opacity:0.7;">'.get_date_from_gmt($latest_content['reply_date'], "M d, Y h:i:s A").'</small>
                                                                '.$attach.'
                                                            </div>
                                                            <hr>
                                                            <p>
                                                                '.preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~","<a href=\"\\0\" title='\\0' target='_blank'>\\0</a>", stripslashes($latest_content['content'])).'
                                                            </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Actions</th>
                                                            <th>Reply Requester</th>
                                                            <th>Raiser Voices</th>
                                                            <th>Agent Voices</th>
                                                            <th>Tags</th>
                                                            <th>Rating</th>
                                                            <th>Source</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-default dropdown-toggle single_ticket_action_button_'.$current[0]['ticket_id'].'" data-toggle="dropdown">
                                                                        Actions <span class="caret"></span>
                                                                    </button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                        '.(($action_value != "")?$action_value:'<li style="padding: 3px 20px;">No Actions</li>').'
                                                                        <li class="divider"></li>
                                                                        <li class="text-center">
                                                                            <small class="text-muted">
                                                                                Select label to assign
                                                                            </small>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="#reply_'.$current[0]['ticket_id'].'" data-toggle="modal"  title="Compose Reply">
                                                                    '.$current[0]['ticket_email'].'
                                                                </a>
                                                            </td>
                                                            <td>'.count($raiser_voice).'</td>
                                                            <td>'.count($agent_voice).'</td>
                                                            <td>'.(($ticket_tags!="")?$ticket_tags:"No Tags").'</td>
                                                            <td>'.$ticket_rating.'</td>
                                                            <td>'.((isset($current_meta['ticket_source']))?$current_meta['ticket_source']:"").'</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <!-- Modal -->
                                                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="reply_'.$current[0]['ticket_id'].'" class="modal fade" style="display: none;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                                                <h4 class="modal-title">Compose Reply</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p style="margin-top: 5px;font-size: 16px;">
                                                                ';  
                                                                if(in_array("manage_tickets", $access))
                                                                {
                                                                    echo '<input type="text" value="'.$current[0]['ticket_title'].'" id="direct_ticket_title_'.$current[0]['ticket_id'].'" class="ticket_title_editable">';
                                                                }
                                                                else
                                                                {
                                                                    echo $current[0]['ticket_title'];
                                                                }
                                                                if(in_array("reply_tickets",$access))
                                                                {
                                                                    ?>
                                                                    </p>
                                                                    <div class="row" style="margin-bottom: 20px;">
                                                                        <div class="col-md-12">
                                                                            <div class="widget-area no-padding blank">
                                                                                <div class="status-upload">
                                                                                    <?php wp_nonce_field('ajax_crm_nonce', 'direct_security'.$current[0]['ticket_id']); ?>
                                                                                    <textarea rows="10" cols="30" class="form-control direct_reply_textarea" id="direct_reply_textarea_<?php echo $current[0]['ticket_id']; ?>" name="reply_textarea_<?php echo $current[0]['ticket_id']; ?>"></textarea> 
                                                                                    <div class="form-group">
                                                                                        <div class="input-group col-md-12">
                                                                                            <span class="btn btn-send fileinput-button">
                                                                                                <i class="glyphicon glyphicon-plus"></i>
                                                                                                <span>Attachment</span>
                                                                                                <input type="file" name="direct_files" id="direct_files_<?php echo $current[0]['ticket_id']; ?>" class="direct_attachment_reply" multiple="">
                                                                                            </span>
                                                                                            <div class="btn-group pull-right">
                                                                                                <button type="button" class="btn btn-send dropdown-toggle direct_ticket_reply_action_button_<?php echo $current[0]['ticket_id']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                  Submit as <span class="caret"></span>
                                                                                                </button>
                                                                                                <ul class="dropdown-menu">
                                                                                                    <?php
                                                                                                        if(in_array("manage_tickets", $access))
                                                                                                        {
                                                                                                            for($j=0;$j<count($avail_labels_wf);$j++)
                                                                                                            {
                                                                                                                echo '<li id="'.$current[0]['ticket_id'].'"><a href="#" class="direct_ticket_reply_action" id="'.$avail_labels_wf[$j]['slug'].'">Submit as '.$avail_labels_wf[$j]['title'].'</a></li>';
                                                                                                            }
                                                                                                        }
                                                                                                        else
                                                                                                        {
                                                                                                            echo '<li id="'.$current[0]['ticket_id'].'"><a href="#" class="direct_ticket_reply_action" id="'.$ticket_label_slug.'">Submit as '.$ticket_label.'</a></li>';
                                                                                                        }
                                                                                                    ?>
                                                                                                    <li role="separator" class="divider"></li>
                                                                                                    <li id="<?php echo $current[0]['ticket_id'];?>"><a href="#" class="direct_ticket_reply_action" id="note">Submit as Note</a></li>
                                                                                                    <li class="text-center"><small class="text-muted">Notes visible to Agents and Supervisors</small></li>
                                                                                                </ul>
                                                                                              </div>
                                                                                        </div>
                                                                                        <div class="direct_upload_preview_files_<?php echo $current[0]['ticket_id'];?>"></div>
                                                                                    </div>
                                                                                </div><!-- Status Upload  -->
                                                                            </div><!-- Widget Area -->
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                }
                                                                else
                                                                {
                                                                    echo "<p>You don't Have permisson to Reply this ticket</p>";
                                                                }
                                                            echo'
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                            </div>
                                        </td>
                                    </tr>
                                    ';
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
return ob_get_clean();

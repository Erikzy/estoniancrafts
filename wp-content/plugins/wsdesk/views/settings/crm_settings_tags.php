<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$args = array("type" => "tag");
$fields = array("slug","title","settings_id");
$avail_tags = eh_crm_get_settings($args,$fields);
?>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_tags_available" style="padding-right:1em !important;">Available Ticket Tags</label>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default crm-panel">
            <div class="panel-body" style="padding: 5px !important;background-color: whitesmoke;">
                <ul class="list-group">
                    <?php
                        if(!empty($avail_tags))
                        {
                            for($i=0;$i<count($avail_tags);$i++)
                            {
                                $tag_posts = eh_crm_get_settingsmeta($avail_tags[$i]['settings_id'], "tag_posts");
                                echo '<li class="list-group-item"><span class="badge" style="background-color:#337ab7;">'.count($tag_posts).' Tagged Posts</span>'.$avail_tags[$i]['title'].'</li>';
                            }
                        }
                        else
                        {
                            echo '<li class="list-group-item">There are no Tags! Create One Tag.</li>';
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
        <label for="ticket_tags_remove" style="padding-right:1em !important;">Remove Ticket Tags</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">In order to remove Ticket Tag, select it and click on Save</span>
        <select multiple="multiple" size="10" name="ticket_tags_remove" id="ticket_tags_remove" class="ticket_tags_remove">
            <?php
                for($i=0;$i<count($avail_tags);$i++)
                {
                    echo '<option value="'.$avail_tags[$i]["slug"].'">'.$avail_tags[$i]["title"].'</option>';
                }
            ?>
        </select>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_tag_add" style="padding-right:1em !important;">Add Ticket Tag</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Want to add new Ticket Tag? <input type="checkbox" style="margin-top: 0px; vertical-align: sub;"  id="add_new_tag_yes" class="form-control" name="add_new_tag_yes" value="yes"> Yes</span>
        <span style="vertical-align: middle;display: none;" id="ticket_tag_add_section">
            <span class="help-block">Enter Details for New Tag? </span>
            <input type="text" id="ticket_tag_add_title" placeholder="Enter Title" class="form-control crm-form-element-input">
            <span class="help-block">Select the Post which should be Tagged if required? </span>
            <select class="ticket_tag_add_posts form-control crm-form-element-input" multiple="multiple">
            </select>
            <span class="help-block">Want to use this Tag for Filter Tickets? </span>
            <input type="radio" style="margin-top: 0;"  id="ticket_tag_add_filter" checked class="form-control" name="ticket_tag_add_filter" value="yes"> Yes! I will use it for Filter<br>
            <input type="radio" style="margin-top: 0;" id="ticket_tag_add_filter" class="form-control" name="ticket_tag_add_filter" value="no"> No! Just for Information

        </span>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-3">
        <label for="ticket_tag_edit_type" style="padding-right:1em !important;">Edit Ticket Tag</label>
    </div>
    <div class="col-md-9">
        <span class="help-block">Select the Tag to edit?</span>
        <span style="vertical-align: middle;" id="ticket_tag_edit_section">
            <select id="ticket_tag_edit_type" style="width: 100% !important;display: inline !important" class="form-control" aria-describedby="helpBlock">
                <?php
                    if(!empty($avail_tags))
                    {
                        echo '<option value="">Choose the Tag</option>';
                        for($i=0;$i<count($avail_tags);$i++)
                        {
                            echo '<option value="'.$avail_tags[$i]['slug'].'">'.$avail_tags[$i]['title'].'</option>';

                        }
                    }
                    else
                    {
                        echo '<option value="">No Tags Available</option>';
                    }
                ?>
            </select>
            <span style="vertical-align: middle;" id="ticket_tag_edit_append"></span>
        </span>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <button type="button" id="save_ticket_tags" class="btn btn-primary btn-sm">Save Ticket tags</button>
</div>
<?php
return ob_get_clean();

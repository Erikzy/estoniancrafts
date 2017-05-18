<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$support_email_name = eh_crm_get_settingsmeta('0', "support_reply_email_name");
$support_email = eh_crm_get_settingsmeta('0', "support_reply_email");
$support_email_reply_text = eh_crm_get_settingsmeta('0', "support_email_reply_text");
?>
<div class="crm-form-element">
    <div class="col-md-12">
        <span class="help-block">Enter your Support Reply Email Name?</span>
        <input type="text" id="support_reply_email_name" placeholder="Enter your Support Reply Email Name" value="<?php echo $support_email_name; ?>" class="form-control crm-form-element-input">
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <div class="col-md-12">
        <span class="help-block">Enter your Support Reply Email?</span>
        <input type="text" id="support_reply_email" placeholder="Enter your Support Reply Email" value="<?php echo $support_email; ?>" class="form-control crm-form-element-input">
    </div>
</div>
<span class="crm-divider"></span>
<div class="panel-group" id="email_reply_role" style="margin-bottom: 0px !important">
    <div class="panel panel-default">
        <div class="panel-heading collapsed" data-toggle="collapse" data-parent="#email_reply_role" data-target="#content_reply_email">
            <span class ="email-reply-toggle"></span>
            <h4 class="panel-title">
                Code for Agent Reply Email
            </h4>
        </div>
        <div id="content_reply_email" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            [id]
                        </div>
                        <div class="col-md-10">
                            To Insert Ticket Number in the Reply
                        </div>
                    </div>
                    <span class="crm-divider"></span>
                    <div class="row">
                        <div class="col-md-2">
                            [assignee]
                        </div>
                        <div class="col-md-10">
                            To Insert Ticket Assignee in the Reply
                        </div>
                    </div>
                    <span class="crm-divider"></span>
                    <div class="row">
                        <div class="col-md-2">
                            [tags]
                        </div>
                        <div class="col-md-10">
                            To Insert Ticket Tags in the Reply
                        </div>
                    </div>
                    <span class="crm-divider"></span>
                    <div class="row">
                        <div class="col-md-2">
                            [date]
                        </div>
                        <div class="col-md-10">
                            To Insert Ticket Date and Time in the Reply
                        </div>
                    </div>
                    <span class="crm-divider"></span>
                    <div class="row">
                        <div class="col-md-2">
                            [content]
                        </div>
                        <div class="col-md-10">
                            To Insert Ticket Content in the Reply
                        </div>
                    </div>
                    <span class="crm-divider"></span>
                    <div class="row">
                        <div class="col-md-2">
                            [agent_replied]
                        </div>
                        <div class="col-md-10">
                            To Insert Ticket Agent who replied in the Reply
                        </div>
                    </div>
                    <span class="crm-divider"></span>
                    <div class="row">
                        <div class="col-md-2">
                            [status]
                        </div>
                        <div class="col-md-10">
                            To Insert Ticket Status in the Reply
                        </div>
                    </div>
                </div>
            </div>
        </div>                    
    </div>
</div>
<div class="crm-form-element">
    <div class="col-md-12">
        <span class="help-block">Enter your Agent Reply Format?</span>
        <?php
            wp_editor
            (
                $support_email_reply_text, 
                "support_email_reply_text", 
                array
                (
                    'tinymce' => false,
                    "media_buttons" => false,
                    "default_editor"=> "html",
                    "editor_height" => "300px",
                )
            );
        ?>
    </div>
</div>
<span class="crm-divider"></span>
<div class="crm-form-element">
    <button type="button" id="save_email_support" class="btn btn-primary btn-sm">Save Support Email</button>
</div>
<?php
return ob_get_clean();

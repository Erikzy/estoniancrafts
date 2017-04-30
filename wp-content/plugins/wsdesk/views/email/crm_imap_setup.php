<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$server_url = eh_crm_get_settingsmeta('0', "imap_server_url");
$server_port = eh_crm_get_settingsmeta('0', "imap_server_port");
$server_email = eh_crm_get_settingsmeta('0', "imap_server_email");
$server_email_pwd = eh_crm_get_settingsmeta('0', "imap_server_email_pwd");
$imap_activation = eh_crm_get_settingsmeta('0', "imap_activation");
if(!in_array("imap", get_loaded_extensions()))
{
?>
    <div style="text-align: center">
        <h1><span class="glyphicon glyphicon-screenshot" style="font-size: 2em;color: lightgrey;"></span></h1><br>
        <?php
            echo "IMAP not Enabled on your Server. Please Contact your Service Provider";
        ?>
    </div>
    <?php
}
else
{
?>
<center>
    <div class="crm-form-element">
        <div class="col-md-12">
            <span class="help-block">Enter your IMAP Server SSL URL?</span>
            <input type="text" id="server_url" placeholder="Enter your IMAP Server SSL URL" value="<?php echo $server_url;?>" class="form-control crm-form-element-input">
        </div>
    </div>
    <div class="crm-form-element">
        <div class="col-md-12">
            <span class="help-block">Enter your IMAP Server SSL Port?</span>
            <input type="text" id="server_port" placeholder="Enter your Server SSL Port" value="<?php echo $server_port?>" class="form-control crm-form-element-input">
        </div>
    </div>
    <div class="crm-form-element">
        <div class="col-md-12">
            <span class="help-block">Enter your IMAP EMail?</span>
            <input type="text" id="server_email" autocomplete="off" placeholder="Enter your IMAP EMail" value="<?php echo $server_email;?>" class="form-control crm-form-element-input">
        </div>
    </div>
    <div class="crm-form-element">
        <div class="col-md-12">
            <span class="help-block">Enter your EMail Password?</span>
            <input type="password" id="server_email_pwd" autocomplete="off" placeholder="Enter your EMail Password" value="<?php echo $server_email_pwd?>" class="form-control crm-form-element-input">
        </div>
    </div>
    <div class="crm-form-element">
        <div class="col-md-12">
            <?php
                if($imap_activation == "activated")
                {
                    ?>
                    <span class="help-block" style="text-align: center">Deactivate EMail IMAP for Pulling Reply from Mail for Tickets.</span>
                    <button type="button" id="deactivate_imap" data-loading-text="Revoking IMAP..." class="btn btn-danger btn-lg">Deactivate IMAP</button>
                    <?php
                }
                else
                {
                    ?>
                    <span class="help-block" style="text-align: center">Activate EMAIL IMAP for Pulling Reply from Mail for Tickets.</span>
                    <button type="button" data-loading-text="Accessing IMAP..." id="activate_imap" class="btn btn-primary btn-lg">Activate IMAP</button>
                    <?php
                }
            ?>
        </div>
    </div>
</center>
<?php
}
return ob_get_clean();
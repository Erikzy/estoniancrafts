<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$client_id = eh_crm_get_settingsmeta('0', "oauth_client_id");
$client_secret = eh_crm_get_settingsmeta('0', "oauth_client_secret");
$oauth_activation = eh_crm_get_settingsmeta('0', "oauth_activation");
?>
<center>
    <div class="crm-form-element">
        <div class="col-md-12">
            <span class="help-block">Enter your Google API client ID?</span>
            <input type="text" id="oauth_client_id" placeholder="Enter your Google API Client ID" value="<?php echo $client_id;?>" class="form-control crm-form-element-input">
        </div>
    </div>
    <div class="crm-form-element">
        <div class="col-md-12">
            <span class="help-block">Enter your Google API Client Secret?</span>
            <input type="text" id="oauth_client_secret" placeholder="Enter your Google API Client Secret" value="<?php echo $client_secret?>" class="form-control crm-form-element-input">
        </div>
    </div>
    <?php
    if($oauth_activation == "activated")
    {
        ?>
        <div class="crm-form-element">
            <div class="col-md-12">
                <span class="help-block">Access Token</span>
                <input type="text" readonly value="<?php echo eh_crm_get_settingsmeta('0', "oauth_accesstoken")?>" class="form-control crm-form-element-input">
            </div>
        </div>
        <div class="crm-form-element">
            <div class="col-md-12">
                <span class="help-block">Refresh Token</span>
                <input type="text" readonly value="<?php echo eh_crm_get_settingsmeta('0', "oauth_refreshtoken")?>" class="form-control crm-form-element-input">
            </div>
        </div>
        <?php
    }
    ?>
    <div class="crm-form-element">
        <div class="col-md-12">
            <?php
                if($oauth_activation == "activated")
                {
                    ?>
                    <span class="help-block" style="text-align: center">Deactivate OAuth for Pulling Reply from Mail for Tickets.</span>
                    <button type="button" id="deactivate_oauth" data-loading-text="Revoking OAuth..." class="btn btn-danger btn-lg">Deactivate OAuth</button>
                    <?php
                }
                else
                {
                    ?>
                    <span class="help-block" style="text-align: center">Activate OAuth for Pulling Reply from Mail for Tickets.</span>
                    <button type="button" data-loading-text="Accessing OAuth..." id="activate_oauth" class="btn btn-primary btn-lg">Activate OAuth</button>
                    <?php
                }
            ?>
        </div>
    </div>
</center>
<?php
return ob_get_clean();

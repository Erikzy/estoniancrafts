<?php
if (!defined('ABSPATH')) {
    exit;
}
ob_start();
$zendesk_accesstoken = eh_crm_get_settingsmeta('0', "zendesk_accesstoken");
$zendesk_subdomain = eh_crm_get_settingsmeta('0', "zendesk_subdomain");
$zendesk_username = eh_crm_get_settingsmeta('0', "zendesk_username");
$zendesk = false;
if(file_exists(EH_CRM_MAIN_VENDOR."zendesk/autoload.php"))
{
    $zendesk = true;
}
else
{
    $zendesk = false;
}
if(!$zendesk)
{
    ?>
        <center>
            <div class="crm-form-element">
                <div class="col-md-12">
                    <span class="help-block" style="text-align: center">Activate Zendesk Import to Get all Zendesk Tickets</span>
                    <button class="btn btn-primary btn-lg" id="activate_zendesk">Activate Zendesk</button>
                </div>
            </div>
        </center>
    <?php
}
else
{
    ?>
<center>
    <div class="crm-form-element" id="zendesk_progress_bar">
        <div class="col-md-12">
            <span class="help-block" id="zendesk_data_progress">Importing Tickets may take some time...</span>
            <div class="progress">
                <div class="progress-bar progress-bar-striped active" id="zendesk_importing_width" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 1%">
                    <span class="sr-only" style="position: inherit;color: black" id="zendesl_per_progress">1% Completed</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8 col-md-offset-2" id="live_import_main">
        <div id="live_import_log"></div>
        <div>
            <span class="help-block">Stop Pulling Tickets to WSDesk</span>
            <button type="button" data-loading-text="Stoping Zendesk import..." id="stop_pull_tickets" class="btn btn-primary">Stop Import</button>
        </div>
    </div>
    <div id="blur_on_import">
        <div class="crm-form-element">
            <div class="col-md-12">
                <span class="help-block">What is your plan in Zendesk? <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="Depands Upon the Plan your Importing tickets will be slow" data-container="body"></span></span>
                <select id="zendesk_plan" style="width: 50% !important;display: inline !important" class="form-control" aria-describedby="helpBlock">
                        <option value="essential">Essential</option>
                        <option value="team">Team</option>
                        <option value="professional">Professional</option>
                        <option value="enterprise">Enterprise</option>
                        <option value="high">High Volume API Add-On (Professional or Enterprise)</option>
                </select>
            </div>
        </div>
        <div class="crm-form-element">
            <div class="col-md-12">
                <span class="help-block">Enter your Zendesk Access Token</span>
                <input type="text" id="zendesk_accesstoken" placeholder="Zendesk Token" value="<?php echo $zendesk_accesstoken; ?>" class="form-control crm-form-element-input">
            </div>
        </div>
        <div class="crm-form-element">
            <div class="col-md-12">
                <span class="help-block">Enter your Zendesk Subdomain ( Without https:// and .zendesk.com )</span>
                <input type="text" id="zendesk_subdomain" placeholder="Zendesk Subdomain" value="<?php echo $zendesk_subdomain; ?>" class="form-control crm-form-element-input">
            </div>
        </div>
        <div class="crm-form-element">
            <div class="col-md-12">
                <span class="help-block">Enter your Zendesk Username</span>
                <input type="text" id="zendesk_username" autocomplete="off" placeholder="Zendesk Username" value="<?php echo $zendesk_username; ?>" class="form-control crm-form-element-input">
            </div>
        </div>
        <div class="crm-form-element">
            <div class="col-md-12">
                <span class="help-block">Want to download attachment locally?</span>
                    <span style="vertical-align: middle;">
                        <input type="radio" style="margin-top: 0;" id="download_attachment" class="form-control" name="download_attachment" value="yes">Yes! Download 
                        <input type="radio" style="margin-top: 0;" id="download_attachment" class="form-control" name="download_attachment" checked="" value="no"> No! I don't want<br>
                    </span>
            </div>
        </div>
    </div>
    <div class="crm-form-element">
        <div class="col-md-12">
            <span class="help-block">Pull Tickets to WSDesk</span>
            <button type="button" data-loading-text="Accessing Zendesk..." id="zendesk_pull_tickets" class="btn btn-primary btn-lg">Pull Tickets</button>
        </div>
    </div>
</center>
<?php
}
return ob_get_clean();

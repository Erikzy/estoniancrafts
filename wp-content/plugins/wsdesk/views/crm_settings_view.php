<div class="container">
    <div class="row">
        <ol class="breadcrumb crm-panel-right" style="margin-left: 0 !important;">
            <li>Settings</li>
            <li id="breadcrump_section" class="active">General</li>
        </ol>
        <div class="col-md-3 crm-panel-left">
            <div class="panel panel-default crm-panel">
                <div class="panel-body">
                    <ul class="nav nav-pills nav-stacked" role="tablist">
                        <li class="active">
                            <a href="#general_tab" data-toggle="tab" class="general">General</a>
                        </li>
                        <li>
                            <a href="#ticket_fields_tab" data-toggle="tab" class="ticket_fields">Ticket Fields</a>
                        </li>
                        <li>
                            <a href="#ticket_labels_tab" data-toggle="tab" class="ticket_labels">Ticket Labels</a>
                        </li>
                        <li>
                            <a href="#ticket_tags_tab" data-toggle="tab" class="ticket_tags">Ticket Tags</a>
                        </li>
                        <li>
                            <a href="#appearance_tab" data-toggle="tab" class="appearance">Appearance</a>
                        </li>
                        <li>
                            <a href="#premium_tab" data-toggle="tab" class="premium" style="background-color: #186918;color: #fff !important">Premium Upgrade</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="alert alert-success" style="display: none" role="alert">
                <div id="success_alert_text"></div>
            </div>
            <div class="alert alert-danger" style="display: none" role="alert">
                <div id="danger_alert_text"></div>
            </div>
        </div>
        <div class="col-xs-12 col-md-9">
            <div class="panel panel-default crm-panel">
                <div class="panel-body" style="padding: 5px !important">
                    <div class="tab-content">
                        <div class="loader"></div>
                        <div class="tab-pane active" id="general_tab">
                            <?php echo include(EH_CRM_MAIN_VIEWS."settings/crm_settings_general.php"); ?>
                        </div>
                        <div class="tab-pane" id="ticket_fields_tab">
                            <?php echo include(EH_CRM_MAIN_VIEWS."settings/crm_settings_fields.php"); ?>
                        </div>
                        <div class="tab-pane" id="ticket_labels_tab">
                            <?php echo include(EH_CRM_MAIN_VIEWS."settings/crm_settings_labels.php"); ?>
                        </div>
                        <div class="tab-pane" id="ticket_tags_tab">
                            <?php echo include(EH_CRM_MAIN_VIEWS."settings/crm_settings_tags.php"); ?>
                        </div>
                        <div class="tab-pane" id="appearance_tab">
                            <?php echo include(EH_CRM_MAIN_VIEWS."settings/crm_settings_appearance.php"); ?>
                        </div>
                        <div class="tab-pane" id="premium_tab">
                            <?php include(EH_CRM_MAIN_VIEWS."settings/crm_settings_premium.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
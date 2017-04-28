<div class="container">
    <div class="row">
        <ol class="breadcrumb crm-panel-right" style="margin-left: 0 !important;">
            <li>WSDesk Agents</li>
            <li id="breadcrump_section" class="active">Add Agents</li>
        </ol>
        <div class="col-md-3 crm-panel-left">
            <div class="panel panel-default crm-panel">
                <div class="panel-body">
                    <ul class="nav nav-pills nav-stacked" role="tablist">
                        <li class="active">
                            <a href="#add_agents_tab" data-toggle="tab" class="add_agents">Add Agents</a>
                        </li>
                        <li>
                            <a href="#manage_agents_tab" data-toggle="tab" class="manage_agents">Manage Agents</a>
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
                        <div class="tab-pane active" id="add_agents_tab">
                            <?php echo include(EH_CRM_MAIN_VIEWS."agents/crm_agents_add.php"); ?>
                        </div>
                        <div class="tab-pane" id="manage_agents_tab">
                            <?php echo include(EH_CRM_MAIN_VIEWS."agents/crm_agents_manage.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
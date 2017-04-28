jQuery(function () {

    //Change Breadcrump Text while switching tab
    jQuery(".nav-pills").on("click", "a", function (e) {
        e.preventDefault();
        switch (jQuery(this).prop("class"))
        {
            case 'oauth_setup':
                jQuery('#breadcrump_section').html("Google OAuth Setup");
                break;
            case 'imap_setup':
                jQuery('#breadcrump_section').html("IMAP EMail Setup");
                break;
            case 'email_support':
                jQuery('#breadcrump_section').html("Support Email");
                break;
        }
    });
    jQuery("#oauth_setup_tab").on("click", "#activate_oauth", function (e) {
        e.preventDefault();
        var client_id = jQuery("#oauth_client_id").val();
        var client_secret = jQuery("#oauth_client_secret").val();
        if(client_id != "" && client_secret != "")
        {
            var btn = jQuery(this).button('loading');
            jQuery("#oauth_client_id").css("border", "1px solid #ddd");
            jQuery("#oauth_client_secret").css("border", "1px solid #ddd;");
            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'eh_crm_activate_oauth',
                    client_id: client_id,
                    client_secret : client_secret
                },
                success: function (data) {
                    window.location.href = data;
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
        else
        {
            if(client_id == "")
            {
                jQuery("#oauth_client_id").css("border", "1px solid red");
            }
            if(client_secret == "")
            {
                jQuery("#oauth_client_secret").css("border", "1px solid red");
            }
        }
    });
    
    jQuery("#imap_setup_tab").on("click", "#activate_imap", function (e) {
        e.preventDefault();
        var server_url = jQuery("#server_url").val();
        var server_port = jQuery("#server_port").val();
        var email = jQuery("#server_email").val();
        var email_pwd = jQuery("#server_email_pwd").val();
        if(server_url != "" && server_port != "" && email != "" && email_pwd != "")
        {
            var btn = jQuery(this).button('loading');
            jQuery("#server_url").css("border", "1px solid #ddd");
            jQuery("#server_port").css("border", "1px solid #ddd;");
            jQuery("#server_email").css("border", "1px solid #ddd");
            jQuery("#server_email_pwd").css("border", "1px solid #ddd;");
            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'eh_crm_activate_email_protocol',
                    server_url: server_url,
                    server_port : server_port,
                    email : email,
                    email_pwd : email_pwd
                },
                success: function (data) {
                    btn.button('reset');
                    var parse = JSON.parse(data);
                    if(parse.status == 'success')
                    {
                        jQuery(".alert-success").css("display", "block");
                        jQuery(".alert-success").css("opacity", "1");
                        jQuery("#success_alert_text").html("<strong>IMAP EMail Setup</strong><br>"+parse.message+"!");
                        window.setTimeout(function () {
                            jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                                jQuery(this).css("display", "none");
                            });
                        }, 4000);
                        jQuery("#imap_setup_tab").html(parse.content);
                    }
                    else
                    {
                        jQuery(".alert-danger").css("display", "block");
                        jQuery(".alert-danger").css("opacity", "1");
                        jQuery("#danger_alert_text").html("<strong>IMAP EMail Setup</strong><br>"+parse.message+"!");
                        window.setTimeout(function () {
                            jQuery(".alert-danger").fadeTo(500, 0).slideUp(500, function () {
                                jQuery(this).css("display", "none");
                            });
                        }, 4000);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
        else
        {
            if(server_url == "")
            {
                jQuery("#server_url").css("border", "1px solid red");
            }
            if(server_port == "")
            {
                jQuery("#server_port").css("border", "1px solid red");
            }
            if(email == "")
            {
                jQuery("#server_email").css("border", "1px solid red");
            }
            if(email_pwd == "")
            {
                jQuery("#server_email_pwd").css("border", "1px solid red");
            }
        }
    });
    jQuery("#oauth_setup_tab").on("click", "#deactivate_oauth", function (e) {
        e.preventDefault();
        var btn = jQuery(this).button('loading');
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_deactivate_oauth'
            },
            success: function (data) {
                btn.button('reset');
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>Google OAuth Setup</strong><br>Google OAuth Revoked!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                jQuery("#oauth_setup_tab").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
    jQuery("#imap_setup_tab").on("click", "#deactivate_imap", function (e) {
        e.preventDefault();
        var btn = jQuery(this).button('loading');
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_deactivate_email_protocol'
            },
            success: function (data) {
                btn.button('reset');
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>IMAP EMail Setup</strong><br>IMAP EMail Deactivated!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                jQuery("#imap_setup_tab").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
    
    jQuery("#email_support_tab").on("click", "#save_email_support", function (e) {
        e.preventDefault();
        jQuery(".loader").css("display", "block");
        var support_email_name  = jQuery("#support_reply_email_name").val();
        var support_email       = jQuery("#support_reply_email").val();
        var reply_ticket        = jQuery("#support_email_reply_text").val();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_email_support_save',
                support_email_name : support_email_name,
                support_email : support_email,
                reply_ticket_text:reply_ticket
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>Support EMail</strong><br>Updated and Saved Successfully!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
});

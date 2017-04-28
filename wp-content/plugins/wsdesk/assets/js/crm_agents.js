jQuery(document).ready(function () {
    add_agents_tab_load();
});
function edit_select2(id)
{
    jQuery(".edit_agents_tags_" + id).select2({
        width: '100%',
        placeholder: "Search Tags",
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            type: 'post',
            delay: 250,
            data: function (params) {
                return {
                    action: 'eh_crm_search_tags',
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatResponse, // omitted for brevity, see the source of this page
        templateSelection: formatResponseSelection, // omitted for brevity, see the source of this page
        formatNoMatches: function () {
            return "No Tags Found";
        }
    });
}
function add_agents_tab_load()
{
    jQuery(".add_agents_select").select2({
        width: '100%',
        placeholder: "Select User",
        templateResult: formatUser,
        formatNoMatches: function () {
            return "No Users Found";
        },
        language: {
            noResults: function (params) {
                return "No Users Found";
            }
        }
    });
    //Search Post for Tags
    jQuery(".add_agents_tags").select2({
        width: '100%',
        placeholder: "Search Tags",
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            type: 'post',
            delay: 250,
            data: function (params) {
                return {
                    action: 'eh_crm_search_tags',
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: false
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1,
        templateResult: formatResponse, // omitted for brevity, see the source of this page
        templateSelection: formatResponseSelection, // omitted for brevity, see the source of this page
        formatNoMatches: function () {
            return "No Tags Found";
        }
    });
}
function formatResponse(response) {
    if (response.loading)
        return response.title;
    var markup = "<div class=''>" + response.title + "<div class=''>" + response.posts + "</div></div>";
    return markup;
}

function formatResponseSelection(response) {
    var title = response.title;
    if (title.length > 15)
        return title.substr(0, 15) + '..';
    else
        return title;
}
function formatUser(user)
{
    if (!user.id) {
        return user.text;
    }
    var hash = jQuery("#user_key_hash").val();
    var user_hash = jQuery.parseJSON(hash);
    var key_value = user.element.value;
    var html = jQuery('<span><img src="http://0.gravatar.com/avatar/' + user_hash[key_value] + '?s=26&d=mm&r=g" class="img-flag" /> ' + user.text + '</span>');
    return html;
}
jQuery(function () {
    jQuery(".nav-pills").on("click", "a", function (e) {
        e.preventDefault();
        switch (jQuery(this).prop("class"))
        {
            case 'add_agents':
                jQuery('#breadcrump_section').html("Add Agents");
                break;
            case 'manage_agents':
                jQuery('#breadcrump_section').html("Manage Agents");
                break;
        }
    });
    
    jQuery("#manage_agents_tab").on("click", ".edit_user", function (e) {
        e.preventDefault();
        var user_id = jQuery(this).attr('id').split("user_edit_").pop();
        jQuery(".loader").css("display", "block");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_edit_agent_html',
                user_id: user_id
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery("#user_content_change_" + user_id).html(data);
                edit_select2(user_id);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
    
    jQuery("#manage_agents_tab").on("click", ".save_edit_agents", function (e) {
        e.preventDefault();
        var user_id = jQuery(this).attr('id').split("save_edit_agents_").pop();
        var rights = getValue_checkbox_values('edit_agents_rights_'+user_id);
        var tags = jQuery(".edit_agents_tags_"+user_id).val();
        jQuery(".loader").css("display", "block");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_edit_agent',
                user_id: user_id,
                rights : rights,
                tags: (tags !== null) ? tags.join(",") : ""
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>WSDesk Agents</strong><br>Agents Updated Successfully!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                var response = jQuery.parseJSON(data);
                jQuery("#add_agents_tab").html(response.add);
                jQuery("#manage_agents_tab").html(response.manage);
                add_agents_tab_load();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
    
    jQuery("#manage_agents_tab").on("click", ".cancel_edit_agents", function (e) {
        e.preventDefault();
        var user_id = jQuery(this).attr('id').split("cancel_edit_agents_").pop();
        jQuery("#user_content_change_"+user_id).empty();
    });
    
    jQuery("#manage_agents_tab").on("click", ".user_actions_remove", function (e) {
        e.preventDefault();
        var user_id = jQuery(this).attr('id').split("user_actions_remove_").pop();
        BootstrapDialog.show({
            message: 'Do You want to remove the WSDesk Role?',
            buttons: [{
                label: 'Yes! Remove',
                // no title as it is optional
                cssClass: 'btn-primary',
                action: function(dialogItself){
                    jQuery(".loader").css("display", "block");
                    jQuery.ajax({
                        type: 'post',
                        url: ajaxurl,
                        data: {
                            action: 'eh_crm_remove_agent',
                            user_id: user_id
                        },
                        success: function (data) {
                            jQuery(".loader").css("display", "none");
                            jQuery(".alert-success").css("display", "block");
                            jQuery(".alert-success").css("opacity", "1");
                            jQuery("#success_alert_text").html("<strong>WSDesk Agents</strong><br>Agents Removed Successfully!");
                            window.setTimeout(function () {
                                jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                                    jQuery(this).css("display", "none");
                                });
                            }, 4000);
                            var response = jQuery.parseJSON(data);
                            jQuery("#add_agents_tab").html(response.add);
                            jQuery("#manage_agents_tab").html(response.manage);
                            add_agents_tab_load();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                    dialogItself.close();
                }
            }, {
                label: 'Close',
                action: function(dialogItself){
                    dialogItself.close();
                }
            }]
        });
        
    });
    
    function getValue_checkbox_values(name) {
        var chkArray = [];
        jQuery("input[name='" + name + "']:checked").each(function () {
            chkArray.push(jQuery(this).val());
        });
        var selected;
        selected = chkArray.join(',') + ",";
        if (selected.length > 1) {
            return (selected.slice(0, -1));
        } else {
            return ("");
        }
    }
    
    jQuery("#add_agents_tab").on("click", "#save_add_agents", function (e) {
        e.preventDefault();
        var users = jQuery(".add_agents_select").val();
        var role = jQuery("input[name='add_agents_role']:checked").val();
        var rights = getValue_checkbox_values('add_agents_rights');
        var tags = jQuery(".add_agents_tags").val();
        jQuery(".loader").css("display", "block");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_agent_add',
                users: (users !== null) ? users.join(",") : "",
                role: role,
                rights: rights,
                tags: (tags !== null) ? tags.join(",") : "",
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>WSDesk Agents</strong><br>Agents added Successfully!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                var response = jQuery.parseJSON(data);
                jQuery("#add_agents_tab").html(response.add);
                jQuery("#manage_agents_tab").html(response.manage);
                add_agents_tab_load();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
    
    jQuery("#add_agents_tab").on("change", "#add_agents_role", function (e) {
        e.preventDefault();
        var supervisor_check = '<span id="add_agents_rights_supervisor"><input type="checkbox" style="margin-top: 0;" class="form-control" name="add_agents_rights" id="add_agents_rights_settings" value="settings"> Show Settings Page<br>\
                                <input type="checkbox" style="margin-top: 0;" class="form-control" name="add_agents_rights" id="add_agents_rights_agents" value="agents"> Show Agents Page<br></span>';
        if (jQuery(this).val() === "agents")
        {
            jQuery("#add_agents_rights_supervisor").remove();
        } else
        {
            jQuery("#add_agents_access_rights").append(supervisor_check);
        }
    }).change();
});


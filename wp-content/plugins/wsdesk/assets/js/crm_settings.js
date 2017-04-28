// Load Functions to set data
jQuery(document).ready(function () {
    general_tab_load();
    field_tab_load();
    tag_tab_load();
    label_tab_load();
});
function label_tab_load()
{
    jQuery("#ticket_labels_remove").select2({
        width: '100%',
        allowClear: true,
        placeholder: "Select Ticket Labels",
        formatNoMatches: function () {
            return "No Labels Found";
        },
        language: {
            noResults: function (params) {
                return "No Labels Found";
            }
        }
    });
    jQuery("#ticket_label_edit_type").select2({
        width: '100%',
        minimumResultsForSearch: -1
    });
}
function general_tab_load()
{
    jQuery("#default_assignee").select2({
        width: '100%',
        minimumResultsForSearch: -1
    });
    jQuery("#default_label").select2({
        width: '100%',
        minimumResultsForSearch: -1
    });
}
function field_tab_load()
{
    var chooser = jQuery("#ticket_fields_configure_final").fieldChooser();
    var sourceFields = jQuery("#ticket_fields_configure_available").children();
    var destinationFields = jQuery("#ticket_fields_configure_selected").children();
    chooser.getSourceList().add(sourceFields);
    chooser.getDestinationList().add(destinationFields);
    jQuery(".fc-source-fields").addClass("col-md-6");
    jQuery(".fc-destination-fields").addClass("col-md-6");
    jQuery("#ticket_fields_remove").select2({
        width: '100%',
        allowClear: true,
        placeholder: "Select Ticket Fields",
        formatNoMatches: function () {
            return "No Fields Found";
        },
        language: {
            noResults: function (params) {
                return "No Fields Found";
            }
        }
    });
    jQuery("#ticket_field_add_type").select2({
        width: '100%',
        minimumResultsForSearch: -1
    });
    jQuery("#ticket_field_edit_type").select2({
        width: '100%',
        minimumResultsForSearch: -1
    });
}
function tag_tab_edit_load()
{
    //Search Post for Tags
    jQuery(".ticket_tag_edit_posts").select2({
        width: '100%',
        allowClear: true,
        closeOnSelect: false,
        placeholder: "Search and Choose",
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            type: 'post',
            delay: 250,
            data: function (params) {
                return {
                    action: 'eh_crm_search_post',
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
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
        minimumInputLength: 3,
        templateResult: formatResponse, // omitted for brevity, see the source of this page
        templateSelection: formatResponseSelection, // omitted for brevity, see the source of this page
        formatNoMatches: function () {
            return "No Posts Found";
        }
    });
}
function tag_tab_load()
{
    jQuery("#ticket_tags_remove").select2({
        width: '100%',
        allowClear: true,
        placeholder: "Select Ticket Tags",
        formatNoMatches: function () {
            return "No Tags Found";
        },
        language: {
            noResults: function (params) {
                return "No Tags Found";
            }
        }
    });    
    jQuery("#ticket_tag_edit_type").select2({
        width: '100%',
        minimumResultsForSearch: -1
    });
    //Search Post for Tags
    jQuery(".ticket_tag_add_posts").select2({
        width: '100%',
        allowClear: true,
        closeOnSelect: false,
        placeholder: "Search and Choose",
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            type: 'post',
            delay: 250,
            data: function (params) {
                return {
                    action: 'eh_crm_search_post',
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                // parse the results into the format expected by Select2
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data, except to indicate that infinite
                // scrolling can be used
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
        minimumInputLength: 3,
        templateResult: formatResponse, // omitted for brevity, see the source of this page
        templateSelection: formatResponseSelection, // omitted for brevity, see the source of this page
        formatNoMatches: function () {
            return "No Posts Found";
        }
    });
}
function formatResponse(response) {
    if (response.loading)
        return response.title;
    var markup = "<div class=''>" + response.title + "<div class=''>" + response.type + "</div></div>";
    return markup;
}

function formatResponseSelection(response) {
    var title = response.title;
    if (title.length > 15)
        return title.substr(0, 15) + '..';
    else
        return title;
}
jQuery(function () {

    //Change Breadcrump Text while switching tab
    jQuery(".nav-pills").on("click", "a", function (e) {
        e.preventDefault();
        switch (jQuery(this).prop("class"))
        {
            case 'general':
                jQuery('#breadcrump_section').html("General");
                break;
            case 'ticket_fields':
                jQuery('#breadcrump_section').html("Ticket Fields");
                break;
            case 'ticket_labels':
                jQuery('#breadcrump_section').html("Ticket labels");
                break;
            case 'ticket_tags':
                jQuery('#breadcrump_section').html("Ticket Tags");
                break;
            case 'appearance':
                jQuery('#breadcrump_section').html("Appearance");
                break;
        }
    });

    // General Settings Section All Events
    //Save General Button Action
    jQuery("#general_tab").on("click", "#save_general", function (e) {
        e.preventDefault();
        jQuery(".loader").css("display", "block");
        var default_assignee = jQuery("#default_assignee").val();
        var default_label = jQuery("#default_label").val();
        var ticket_raiser = jQuery("input[name='ticket_raiser']:checked").val();
        var ticket_rows = jQuery("#ticket_display_row").val();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_ticket_general',
                default_assignee: default_assignee,
                default_label : default_label,
                ticket_raiser: ticket_raiser,
                ticket_rows:ticket_rows
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>General Settings</strong><br>Updated and Saved Successfully!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                jQuery("#general_tab").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

    //Appearance Settings Section All Events
    //Save Appearance Button Action
    jQuery("#appearance_tab").on("click", "#save_appearance", function (e) {
        e.preventDefault();
        jQuery(".loader").css("display", "block");
        var input_elements_width    = jQuery("#input_elements_width").val();
        var main_ticket_form_title    = jQuery("#main_ticket_form_title").val();
        var new_ticket_form_title    = jQuery("#new_ticket_form_title").val();
        var existing_ticket_title    = jQuery("#existing_ticket_title").val();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action      : 'eh_crm_ticket_appearance',
                input_width : input_elements_width,
                main_ticket_title       : main_ticket_form_title,
                new_ticket_title        : new_ticket_form_title,
                existing_ticket_title   : existing_ticket_title
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>Appearance Settings</strong><br>Updated and Saved Successfully!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                jQuery("#appearance_tab").html(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

    //Ticket Fields Settings Section All Events
    //Save Ticket Fields Action
    jQuery("#ticket_fields_tab").on("click", "#save_ticket_fields", function (e) {
        e.preventDefault();
        var selected_fields = jQuery(".fc-destination-fields div[id]")
                .map(function () {
                    return this.id;
                })
                .get();
        var fields_remove = jQuery("#ticket_fields_remove").val();
        var new_field = {};
        var edit_field = {};
        if (jQuery("#add_new_field_yes").is(':checked') && jQuery("#ticket_field_add_type").val() !== "") {
            new_field["type"] = jQuery("#ticket_field_add_type").val();
            new_field["title"] = jQuery("#ticket_field_add_title").val();
            new_field["filter"] = jQuery("input[name='ticket_field_add_filter']:checked").val();
            new_field["required"] = "no";
            new_field["placeholder"] = "";
            new_field["default"] = "";
            new_field["values"] = "";
            new_field["description"] = jQuery("#ticket_field_add_description").val();;
            switch (jQuery("#ticket_field_add_type").val())
            {
                case "text":
                case "number":
                case "email":
                case "password":
                    new_field["default"] = jQuery("#ticket_field_add_default").val();
                    new_field["required"] = jQuery("input[name='ticket_field_add_require']:checked").val();
                    new_field["placeholder"] = jQuery("#ticket_field_add_placeholder").val();
                    break;
                case "radio":
                case "checkbox":
                case "select":
                    var values = [];
                    jQuery(".ticket_field_add_values").each(function () {
                        values.push(jQuery(this).val());
                    });
                    if (jQuery("#ticket_field_add_default").val() != '')
                    {
                        if (jQuery.inArray(jQuery("#ticket_field_add_default").val(), values) != '-1')
                        {
                            new_field["default"] = jQuery("#ticket_field_add_default").val();
                        } else
                        {
                            jQuery(".loader").css("display", "none");
                            jQuery(".alert-danger").css("display", "block");
                            jQuery(".alert-danger").css("opacity", "1");
                            jQuery("#danger_alert_text").html("<strong>Ticket Fields</strong><br>Default Value is not Matched!");
                            window.setTimeout(function () {
                                jQuery(".alert-danger").fadeTo(500, 0).slideUp(500, function () {
                                    jQuery(this).css("display", "none");
                                });
                            }, 4000);
                            jQuery('html, body').animate({
                                scrollTop: jQuery("#ticket_fields_tab").offset().top
                            }, 1000);
                            return false;
                        }

                    }
                    new_field["values"] = values;
                    break;
                case 'file':
                    new_field["required"] = jQuery("input[name='ticket_field_add_require']:checked").val();
                    new_field["file_type"] = jQuery("input[name='ticket_field_add_file_type']:checked").val();
                    break;
                case 'textarea':
                    new_field["default"] = jQuery("#ticket_field_add_default").val();
                    new_field["required"] = jQuery("input[name='ticket_field_add_require']:checked").val();
                    break;
            }
        }
        if (jQuery("#ticket_field_edit_type").val() !== "")
        {
            edit_field["slug"] = (jQuery("#ticket_field_edit_type").val() !== undefined) ? jQuery("#ticket_field_edit_type").val() : "";
            edit_field["title"] = (jQuery("#ticket_field_edit_title").val() !== undefined) ? jQuery("#ticket_field_edit_title").val() : "";
            edit_field["filter"] = (jQuery("input[name='ticket_field_edit_filter']:checked").val() !== undefined) ? jQuery("input[name='ticket_field_edit_filter']:checked").val() : "";
            edit_field["required"] = (jQuery("input[name='ticket_field_edit_require']:checked").val() !== undefined) ? jQuery("input[name='ticket_field_edit_require']:checked").val() : "";
            edit_field["file_type"] = (jQuery("input[name='ticket_field_edit_file_type']:checked").val() !== undefined) ? jQuery("input[name='ticket_field_edit_file_type']:checked").val() : "";
            edit_field["placeholder"] = (jQuery("#ticket_field_edit_placeholder").val() !== undefined) ? jQuery("#ticket_field_edit_placeholder").val() : '';
            edit_field["default"] = "";
            edit_field["values"] = "";
            edit_field["description"]=(jQuery("#ticket_field_edit_description").val() !== undefined) ? jQuery("#ticket_field_edit_description").val() : "";
            var values = [];
            if (jQuery('.ticket_field_edit_values').length !== 0)
            {
                jQuery(".ticket_field_edit_values").each(function () {
                    values.push(jQuery(this).val());
                });
                edit_field["values"] = values;
            }
            console.log(values);
            if (jQuery("#ticket_field_edit_default").val() != '' && jQuery('#ticket_field_edit_default').length != 0)
            {
                if (jQuery.inArray(jQuery("#ticket_field_edit_default").val(), values) !== -1)
                {
                    edit_field["default"] = jQuery("#ticket_field_edit_default").val();
                }
                else
                {
                    jQuery(".loader").css("display", "none");
                    jQuery(".alert-danger").css("display", "block");
                    jQuery(".alert-danger").css("opacity", "1");
                    jQuery("#danger_alert_text").html("<strong>Edit Ticket Field</strong><br>Default Value is not Matched!");
                    window.setTimeout(function () {
                        jQuery(".alert-danger").fadeTo(500, 0).slideUp(500, function () {
                            jQuery(this).css("display", "none");
                        });
                    }, 4000);
                    jQuery('html, body').animate({
                        scrollTop: jQuery("#ticket_fields_tab").offset().top
                    }, 1000);
                    return false;
                }
            }
        }
        jQuery(".loader").css("display", "block");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_ticket_field',
                selected_fields: (selected_fields !== null) ? selected_fields.join(",") : '',
                fields_remove: (fields_remove !== null) ? fields_remove.join(",") : '',
                new_field: JSON.stringify(new_field),
                edit_field: JSON.stringify(edit_field)
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>Ticket Fields</strong><br>Updated and Saved Successfully!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                jQuery("#ticket_fields_tab").html(data);
                field_tab_load();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
    //Edit Ticket Fields Action
    jQuery("#ticket_fields_tab").on("change", "#ticket_field_edit_type", function (e) {
        e.preventDefault();
        var field = jQuery(this).val();
        if (field === "")
        {
            jQuery("#ticket_field_edit_append").empty();
        } else
        {
            jQuery(".loader").css("display", "block");
            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'eh_crm_ticket_field_edit',
                    field: field
                },
                success: function (data) {
                    jQuery(".loader").css("display", "none");
                    jQuery("#ticket_field_edit_append").empty();
                    jQuery("#ticket_field_edit_append").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    });
    //Add New Ticket Fields Action
    jQuery("#ticket_fields_tab").on("change", "#add_new_field_yes", function (e) {
        e.preventDefault();
        if (jQuery(this).is(':checked')) {
            jQuery("#ticket_field_add_section").css("display", "block");
        } else {
            jQuery("#ticket_field_add_section").css("display", "none");
        }
    });

    //Add Type of Ticket Fields Action
    jQuery("#ticket_fields_tab").on("change", "#ticket_field_add_type", function (e) {
        e.preventDefault();
        var val = jQuery(this).val();
        var html = jQuery("#ticket_field_add_type option[value='" + val + "']").html();
        var add_value = '<button class="button" id="ticket_field_add_values_add" style="vertical-align: baseline;margin-bottom: 10px;">Add Value</button>';
        var remove_value = '<button class="button" id="ticket_field_add_values_remove" style="margin:0px 10px; vertical-align: baseline;margin-bottom: 10px;">Remove Value</button>';
        var input = '<span class="help-block">Enter Details for custom ' + html + '? </span>';
        input += '<input type="text" id="ticket_field_add_title" placeholder="Enter Title" class="form-control crm-form-element-input">';
        switch (val)
        {
            case '':
                break;
            case 'radio':
                input += '<span class="help-block">Specify the Radio values! </span>';
                input += '<input type="text" id="ticket_field_add_values[0]" placeholder="Enter value" class="form-control ticket_field_add_values crm-form-element-input">' + add_value + remove_value;
                input += '<br><input type="text" id="ticket_field_add_default" placeholder="Enter Default Values" class="form-control crm-form-element-input">';
                input += '<br><span class="help-block">Want to give some description to this field? </span><textarea id="ticket_field_add_description" class="form-control crm-form-element-input" style="padding: 10px !important;"/>';
                input += '<span class="help-block">Want to use this field for Filter Tickets? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_filter" checked class="form-control" name="ticket_field_add_filter" value="yes"> Yes! I will use it for Filter<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_filter" class="form-control" name="ticket_field_add_filter" value="no"> No! Just for Information<br>';
                break;
            case 'checkbox':
                input += '<span class="help-block">Specify the Checkbox values! </span>';
                input += '<input type="text" id="ticket_field_add_values[0]" placeholder="Enter value" class="form-control ticket_field_add_values crm-form-element-input">' + add_value + remove_value;
                input += '<br><input type="text" id="ticket_field_add_default" placeholder="Enter Default Values" class="form-control crm-form-element-input">';
                input += '<br><span class="help-block">Want to give some description to this field? </span><textarea id="ticket_field_add_description" class="form-control crm-form-element-input" style="padding: 10px !important;"/>';
                input += '<span class="help-block">Want to use this field for Filter Tickets? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_filter" checked class="form-control" name="ticket_field_add_filter" value="yes"> Yes! I will use it for Filter<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_filter" class="form-control" name="ticket_field_add_filter" value="no"> No! Just for Information<br>';
                break;
            case 'select':
                input += '<span class="help-block">Specify the Dropdown values! </span>';
                input += '<input type="text" id="ticket_field_add_values[0]" placeholder="Enter value" class="form-control ticket_field_add_values crm-form-element-input">' + add_value + remove_value;
                input += '<br><input type="text" id="ticket_field_add_default" placeholder="Enter Default Values" class="form-control crm-form-element-input">';
                input += '<br><span class="help-block">Want to give some description to this field? </span><textarea id="ticket_field_add_description" class="form-control crm-form-element-input" style="padding: 10px !important;"/>';
                input += '<span class="help-block">Want to use this field for Filter Tickets? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_filter" checked class="form-control" name="ticket_field_add_filter" value="yes"> Yes! I will use it for Filter<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_filter" class="form-control" name="ticket_field_add_filter" value="no"> No! Just for Information<br>';
                break;
            case 'textarea':
                input += '<span class="help-block">Specify whether this Field is Optional or Required? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_require" checked class="form-control" name="ticket_field_add_require" value="yes"> Yes! This Field is Mandatory<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_require" class="form-control" name="ticket_field_add_require" value="no"> No! Its an Optional Field <br>';
                input += '<br><input type="text" id="ticket_field_add_default" placeholder="Enter Default Values" class="form-control crm-form-element-input">';
                input += '<br><span class="help-block">Want to give some description to this field? </span><textarea id="ticket_field_add_description" class="form-control crm-form-element-input" style="padding: 10px !important;"/>';
                input += '<span class="help-block">Want to use this field for Filter Tickets? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_filter" checked class="form-control" name="ticket_field_add_filter" value="yes"> Yes! I will use it for Filter<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_filter" class="form-control" name="ticket_field_add_filter" value="no"> No! Just for Information<br>';
                break;
            case 'file':
                input += '<span class="help-block">Specify whether this Field is Optional or Required? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_require" checked class="form-control" name="ticket_field_add_require" value="yes"> Yes! This Field is Mandatory<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_require" class="form-control" name="ticket_field_add_require" value="no"> No! Its an Optional Field <br>';
                input += '<br><span class="help-block">Specify whether this Field is Single or Multiple Attachment? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_file_type" checked class="form-control" name="ticket_field_add_file_type" value="single"> Single Attachment <br><input type="radio" style="margin-top: 0;" id="ticket_field_add_file_type" class="form-control" name="ticket_field_add_file_type" value="multiple"> Multiple Attachment <br>';
                input += '<br><span class="help-block">Want to give some description to this field? </span><textarea id="ticket_field_add_description" class="form-control crm-form-element-input" style="padding: 10px !important;"/>';
                input += '<span class="help-block">Want to use this field for Filter Tickets? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_filter" checked class="form-control" name="ticket_field_add_filter" value="yes"> Yes! I will use it for Filter<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_filter" class="form-control" name="ticket_field_add_filter" value="no"> No! Just for Information<br>';
                break;
            default :
                input += '<span class="help-block">Specify whether this Field is Optional or Required? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_require" checked class="form-control" name="ticket_field_add_require" value="yes"> Yes! This Field is Mandatory<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_require" class="form-control" name="ticket_field_add_require" value="no"> No! Its an Optional Field <br>';
                input += '<br><input type="text" id="ticket_field_add_placeholder" placeholder="Enter Placeholder" class="form-control crm-form-element-input">';
                input += '<input type="text" id="ticket_field_add_default" placeholder="Enter Default Values" class="form-control crm-form-element-input">';
                input += '<br><span class="help-block">Want to give some description to this field? </span><textarea id="ticket_field_add_description" class="form-control crm-form-element-input" style="padding: 10px !important;"/>';
                input += '<span class="help-block">Want to use this field for Filter Tickets? </span><input type="radio" style="margin-top: 0;"  id="ticket_field_add_filter" checked class="form-control" name="ticket_field_add_filter" value="yes"> Yes! I will use it for Filter<br><input type="radio" style="margin-top: 0;" id="ticket_field_add_filter" class="form-control" name="ticket_field_add_filter" value="no"> No! Just for Information<br>';
                break;
        }
        jQuery("#ticket_field_add_append").empty();
        if (val !== '')
        {
            jQuery("#ticket_field_add_append").html(input);
        }
    }).change();

    //Ticket Fields Add new Value for Dropdown Action
    jQuery("#ticket_fields_tab").on("click", "#ticket_field_add_values_add", function (e) {
        e.preventDefault();
        var count = jQuery('.ticket_field_add_values').length;
        var html = '<input type="text" id="ticket_field_add_values[' + count + ']" placeholder="Enter next value" class="form-control ticket_field_add_values crm-form-element-input">';
        jQuery('.ticket_field_add_values').last().after(html);
    });

    //Ticket Fields removing last Value for Dropdown Action
    jQuery("#ticket_fields_tab").on("click", "#ticket_field_add_values_remove", function (e) {
        e.preventDefault();
        if (jQuery('.ticket_field_add_values').length !== 1)
        {
            jQuery('.ticket_field_add_values').last().remove();
        }
    });

    //Ticket Fields Edit new Value for Dropdown Action
    jQuery("#ticket_fields_tab").on("click", "#ticket_field_edit_values_add", function (e) {
        e.preventDefault();
        var count = jQuery('.ticket_field_edit_values').length;
        var html = '<input type="text" id="ticket_field_edit_values[' + count + ']" placeholder="Enter next value" class="form-control ticket_field_edit_values crm-form-element-input">';
        jQuery('.ticket_field_edit_values').last().after(html);
    });

    //Ticket Fields EDit removing last Value for Dropdown Action
    jQuery("#ticket_fields_tab").on("click", "#ticket_field_edit_values_remove", function (e) {
        e.preventDefault();
        if (jQuery('.ticket_field_edit_values').length !== 1)
        {
            jQuery('.ticket_field_edit_values').last().remove();
        }
    });

    //Ticket Labels Settings Section All Events
    //Save Ticket Labels Action
    jQuery("#ticket_labels_tab").on("click", "#save_ticket_labels", function (e) {
        e.preventDefault();
        var new_label = {};
        var edit_label = {};
        var labels_remove = jQuery("#ticket_labels_remove").val();
        if (jQuery("#add_new_label_yes").is(':checked'))
        {
            new_label['title'] = jQuery("#ticket_label_add_title").val();
            new_label['color'] = jQuery("#ticket_label_add_color").val();
            new_label['filter'] = jQuery("input[name='ticket_label_add_filter']:checked").val();
        }
        if (jQuery("#ticket_label_edit_type").val() !== "")
        {
            edit_label['slug'] = jQuery("#ticket_label_edit_type").val();
            edit_label['title'] = jQuery("#ticket_label_edit_title").val();
            edit_label['color'] = jQuery("#ticket_label_edit_color").val();
            edit_label['filter'] = jQuery("input[name='ticket_label_edit_filter']:checked").val();
        }
        jQuery(".loader").css("display", "block");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_ticket_label',
                new_label: JSON.stringify(new_label),
                labels_remove: (labels_remove !== null) ? labels_remove.join(",") : '',
                edit_label: JSON.stringify(edit_label)
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>Ticket Label</strong><br>Updated and Saved Successfully!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                jQuery("#ticket_labels_tab").html(data);
                label_tab_load();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
    //Edit Ticket Label Action
    jQuery("#ticket_labels_tab").on("change", "#ticket_label_edit_type", function (e) {
        e.preventDefault();
        var label = jQuery(this).val();
        if (label === "")
        {
            jQuery("#ticket_label_edit_append").empty();
        } else
        {
            jQuery(".loader").css("display", "block");
            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'eh_crm_ticket_label_edit',
                    label: label
                },
                success: function (data) {
                    jQuery(".loader").css("display", "none");
                    jQuery("#ticket_label_edit_append").empty();
                    jQuery("#ticket_label_edit_append").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    });
    //Add New Ticket Labels Action
    jQuery("#ticket_labels_tab").on("change", "#add_new_label_yes", function (e) {
        e.preventDefault();
        if (jQuery(this).is(':checked')) {
            jQuery("#ticket_label_add_section").css("display", "block");
        } else {
            jQuery("#ticket_label_add_section").css("display", "none");
        }
    });

    //Ticket Tags Settings Section All Events
    //Save Ticket Tags Action
    jQuery("#ticket_tags_tab").on("click", "#save_ticket_tags", function (e) {
        e.preventDefault();
        jQuery(".loader").css("display", "block");
        var tags_remove = jQuery("#ticket_tags_remove").val();
        var new_tag = {};
        var edit_tag = {};
        if (jQuery("#add_new_tag_yes").is(':checked')) {
            new_tag["title"] = jQuery("#ticket_tag_add_title").val();
            new_tag["posts"] = jQuery(".ticket_tag_add_posts").val();
            new_tag["filter"] = jQuery("input[name='ticket_tag_add_filter']:checked").val()
        }
        if (jQuery("#ticket_tag_edit_type").val() !== "")
        {
            edit_tag['slug'] = jQuery("#ticket_tag_edit_type").val();
            edit_tag['title'] = jQuery("#ticket_tag_edit_title").val();
            edit_tag['posts'] = jQuery(".ticket_tag_edit_posts").val();
            edit_tag['filter'] = jQuery("input[name='ticket_tag_edit_filter']:checked").val();
        }
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'eh_crm_ticket_tag',
                tags_remove: (tags_remove !== null) ? tags_remove.join(",") : '',
                new_tag: JSON.stringify(new_tag),
                edit_tag: JSON.stringify(edit_tag)
            },
            success: function (data) {
                jQuery(".loader").css("display", "none");
                jQuery(".alert-success").css("display", "block");
                jQuery(".alert-success").css("opacity", "1");
                jQuery("#success_alert_text").html("<strong>Ticket Tags</strong><br>Updated and Saved Successfully!");
                window.setTimeout(function () {
                    jQuery(".alert-success").fadeTo(500, 0).slideUp(500, function () {
                        jQuery(this).css("display", "none");
                    });
                }, 4000);
                jQuery("#ticket_tags_tab").html(data);
                tag_tab_load();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });
    //Edit Ticket Label Action
    jQuery("#ticket_tags_tab").on("change", "#ticket_tag_edit_type", function (e) {
        e.preventDefault();
        var tag = jQuery(this).val();
        if (tag === "")
        {
            jQuery("#ticket_tag_edit_append").empty();
        } else
        {
            jQuery(".loader").css("display", "block");
            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                    action: 'eh_crm_ticket_tag_edit',
                    tag: tag
                },
                success: function (data) {
                    jQuery(".loader").css("display", "none");
                    jQuery("#ticket_tag_edit_append").empty();
                    jQuery("#ticket_tag_edit_append").html(data);
                    tag_tab_edit_load();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        }
    });

    //Remove Ticket Fields Action
    jQuery("#ticket_tags_tab").on("click", "#remove_ticket_tags", function (e) {
        e.preventDefault();
        jQuery(".loader").css("display", "block");
        var ticket_tags_remove = jQuery('[name="ticket_tags_remove"]').val();
        jQuery(".loader").css("display", "none");
        for (i = 0; i < ticket_tags_remove.length; i++)
        {
            jQuery("#ticket_tags_remove option[value='" + ticket_tags_remove[i] + "']").remove();
        }
    });

    //Add New Ticket Labels Action
    jQuery("#ticket_tags_tab").on("change", "#add_new_tag_yes", function (e) {
        e.preventDefault();
        if (jQuery(this).is(':checked')) {
            jQuery("#ticket_tag_add_section").css("display", "block");
        } else {
            jQuery("#ticket_tag_add_section").css("display", "none");
        }
    });

});
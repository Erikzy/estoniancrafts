jQuery(document).ready(function ($) {

    "use strict";

    if ($('#post-body-content .menu-settings').length) {
        $('#post-body-content .menu-settings').prepend(zmb_admin['nav_menu_term_meta_html']);
    }

    // Update Redux outside the panel options
    $(document).on('change', '.zmb-menu-theme-locations .zmb-menu-location-setting input', function () {
        var $this          = $(this);
        var thisDD         = $this.closest('.zmb-menu-location-setting');
        var menu_location  = thisDD.attr('data-location');
        var is_enable_mega = thisDD.find('.zmb-enable-mega-location').is(':checked') ? 'yes' : 'no';
        var break_point    = thisDD.find('.zmb-location-breakpoint').val();

        if (thisDD.is('.locked')) {
            return false;
        }

        thisDD.addClass('locked');
        if (!thisDD.find('.zmb-message').length) {
            thisDD.append('<em class="zmb-message"></em>');
        }

        thisDD.find('.zmb-message').html(zmb_admin['message']['updating']);

        var data = {
            action : 'zmb_update_menu_location_settings_via_ajax',
            menu_location : menu_location,
            is_enable_mega : is_enable_mega,
            break_point : break_point
        };

        $.post(ajaxurl, data, function (response) {

            console.log(response);
            thisDD.removeClass('locked');
            thisDD.find('.zmb-message').remove();

        });

    });

});
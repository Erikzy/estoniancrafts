jQuery(document).ready(function ($) {
    "use strict";

    // Menu item edit image icon picker
    $(document).on('click', '.field-icon-img-url .icon-img-list a', function (e) {

        var $this    = $(this);
        var thisWrap = $this.closest('.field-icon-img-url');
        var img_url  = $this.find('img').attr('src');

        if (typeof img_url != 'undefined' && typeof img_url != false) {
            thisWrap.find('input[type=text]').val(img_url);
            thisWrap.find('.icon-img-list a').removeClass('active');
            $this.addClass('active');
        }

        e.preventDefault();
    });

    // Turn on mega item for another post types
    $(document).on('change', '.zmb-switch-wrap .zam-enable-mega-cb', function () {
        var $this          = $(this);
        var thisSwitchWrap = $this.closest('.zmb-switch-wrap');
        if ($this.is(':checked')) {
            thisSwitchWrap.addClass('zmb-on');
            thisSwitchWrap.find('.zam-enable-mega').val('yes');
            $this.closest('.menu-item').addClass('zmb-show-mega-options');
        }
        else {
            thisSwitchWrap.removeClass('zmb-on');
            thisSwitchWrap.find('.zam-enable-mega').val('');
            $this.closest('.menu-item').removeClass('zmb-show-mega-options');
        }
    });

    // Font icon picker
    $(document).on('click', '.menu-item .zmb-icon-picker', function (e) {
        var $this         = $(this);
        var thisIconField = $this.closest('.field-icon-classes');

        if ($('.zmb-add-icon-class-popup-wrap').length) {

            $('.menu-item .edit-menu-item-icon-classes').removeClass('zmb-current');
            thisIconField.find('.edit-menu-item-icon-classes').addClass('zmb-current');

            $('.zmb-add-icon-class-popup-wrap').css({
                'display' : 'block'
            });
        }

        e.preventDefault();

    });

    // Pick an icon
    $(document).on('click', '.zmb-icons-list-wrap .zmb-icon-for-picker', function (e) {
        var $this      = $(this);
        var thisPopup  = $this.closest('.zmb-popup-wrap');
        var icon_class = $this.find('i').attr('class');
        $('.menu-item .edit-menu-item-icon-classes.zmb-current').val(icon_class).trigger('change');
        thisPopup.css({
            'display' : 'none'
        });
        e.preventDefault();
    });

    // Close popup
    $(document).on('click', function (e) {
        var $target = $(e.target);
        if (!$target.closest('.zmb-popup-inner').length && !$target.is('.zmb-icon-picker')) {
            $('.zmb-popup-wrap').css({
                'display' : 'none'
            });
        }
    });

    // Color Picker
    $('.zmb-color-picker').each(function () {
        var $this     = $(this);
        var cur_color = $this.val();
        $this.spectrum({
            color : cur_color,
            flat : false,
            showInput : true,
            showInitial : true,
            allowEmpty : true,
            showAlpha : true,
            // disabled: bool,
            // localStorageKey: string,
            showPalette : true,
            // showPaletteOnly: false,
            // togglePaletteOnly: true,
            showSelectionPalette : true,
            clickoutFiresChange : true,
            // cancelText: string,
            // chooseText: string,
            // togglePaletteMoreText: string,
            // togglePaletteLessText: string,
            // containerClassName: string,
            // replacerClassName: string,
            preferredFormat : 'rgb',
            // maxSelectionSize: int,
            // palette: [[string]],
            selectionPalette : ['rgba(0, 0, 0, 0)', '#eec15b'],
            move : function (color) {
                //$this.css('background-color',color.toRgbString());
                $this.val(color.toRgbString());
            }
        });
    });

});
/* 
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */
(function($, undefined) {
    'use strict';



    function EabiCascadingDropdown(element, options) {
        this.el = $(element);
        this.options = $.extend({selectBoxes: []}, options);
        this._init();
    }


    EabiCascadingDropdown.prototype = {
        _init: function() {
            var self = this;
            if (self.options.drop_menu_selection) {
                self.el.find("#eabi_postoffice_pickup_group").val("");
                self.el.find("#eabi_postoffice_pickup_group").hide(0);
            }

            this.el.cascadingDropdown({
                selectBoxes: [{
                        selector: '#eabi_postoffice_pickup_group'
                    },
                    {
                        selector: '#eabi_postoffice_pickup_location',
                        requires: self.options.drop_menu_selection ? [] : ['#eabi_postoffice_pickup_group'],
                        source: function(request, response) {
                            request['action'] = 'eabi_postoffice_get_offices';
                            request['carrier_code'] = self.options.carrier_code;
                            request['group_id'] = $(this.parent.dropdowns[0].el).val();
                            request['address_id'] = self.options.address_id;
                            $.post(self.options.ajax_url, request, function(data) {
                                var selectOnlyOption = data.length <= 1;

                                response(
                                        $.map(data, function(item, index) {
                                            return {
                                                label: item.label,
                                                value: item.value,
                                                group: self.options.hide_group_titles ? false : item.group,
                                                group_title: self.options.hide_group_titles ? false : item.group_title,
                                                selected: selectOnlyOption || item.selected || self.options.selected === item.value
                                            };

                                        })
                                        );


                            }, 'json')
                        }
                    }
                ],
                onChange: function(event, dropdownData) {
                    if ($(event.target).attr('id') == 'eabi_postoffice_pickup_group') {
                        this.dropdowns[1].update();
                    }
                },
                onReady: function(event, dropdownData) {
                    if ($(event.target).attr('id') == 'eabi_postoffice_pickup_group' && self.options.drop_menu_selection) {
                        this.dropdowns[0].hide();
                    }
                }
            });

        }

    }


    // jQuery plugin declaration
    $.fn.eabiCascadingDropdown = function(methodOrOptions) {
        var $this = $(this),
                args = arguments,
                instance = $this.data('plugin_eabiCascadingDropdown');

        if (typeof methodOrOptions === 'object' || !methodOrOptions) {
            return !instance && $this.data('plugin_eabiCascadingDropdown', new EabiCascadingDropdown(this, methodOrOptions));
        } else if (typeof methodOrOptions === 'string') {
            if (!instance) {
                $.error('Cannot call method ' + methodOrOptions + ' before init.');
            } else if (instance[methodOrOptions]) {
                return instance[methodOrOptions].apply(instance, Array.prototype.slice.call(args, 1))
            }
        } else {
            $.error('Method ' + methodOrOptions + ' does not exist in jQuery.plugin_eabiCascadingDropdown');
        }
    };


})(jQuery);



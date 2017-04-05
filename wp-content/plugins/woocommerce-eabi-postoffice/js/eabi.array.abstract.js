/* 
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */


/**
 * <p>Adds multiple-values form field DOM</p>
 * @param {object} $
 * @param {type} undefined
 * @returns {undefined}
 */
(function($, undefined ) {
    var dataKey = 'eabi_array_abstract';
    $.eabi_array_abstract = {
        conf: {
            form_field_name: false,
            form_field_id: false
        }
    };
    /**
     * 
     * @param {object} conf
     * @param {dom} template
     * @returns {function}
     */
    function initTemplate(conf, template) {

        return {
            add: function(data) {
                var d = new Date(),
                        id = '_' + d.getTime() + '_' + d.getMilliseconds();
                if (!data) {
                    data = {};
                }
                if (!data.id) {
                    data.id = id;
                }
                data.id = conf.form_field_name + '[' + data.id + ']';

                $('#grid_' + conf.form_field_id + ' table tr:last-child').loadTemplate(template, data, {before: true});
                
            },
            remove: function(dom) {
                $(dom).closest('tr').remove();
            }
        };
    }
    
    
    
    $.fn.eabi_array_abstract = function(conf, initialData) {
        conf = $.extend(true, {}, $.eabi_array_abstract.conf, conf),
                $that = $(this);
        
        $.addTemplateFormatter("nameFormatter", function(value, template) {
            return template.replace("#{_id}", value);
        });
        
        if (!$that.data(dataKey)) {
            $that.data(dataKey, initTemplate(conf, $that));
            if (initialData && typeof initialData == 'object') {
                for (var i in initialData) {
                    initialData[i].id = i;
                    $that.data(dataKey).add(initialData[i]);
                }
            }
        }
        return $that.data(dataKey);
    };

}( jQuery ));

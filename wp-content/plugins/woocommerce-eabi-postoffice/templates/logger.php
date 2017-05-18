<?php
/*
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */
/* @var $this Eabi_Woocommerce_Postoffice_Block_Logger */
$data = $this->getData();
$field = $this->formFieldId;
$logFile = $this->getInstance()->getLogger()->getLogFilePath();
?><tr valign="top" id="row_<?php echo $this->formFieldId; ?>">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr($field); ?>"><?php echo wp_kses_post($data['title']); ?></label>
        <?php echo $this->getInstance()->helper()->getTooltipHtml($this->getInstance(), $data); ?>
    </th>
    <td class="forminp">
        <fieldset>
            <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
            <label for="<?php echo esc_attr($field); ?>">
                <input <?php disabled($data['disabled'], true); ?> class="<?php echo esc_attr($data['class']); ?>" type="checkbox" name="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($field); ?>" style="<?php echo esc_attr($data['css']); ?>" value="1" <?php checked($this->getInstance()->get_option($this->formFieldKey), 'yes'); ?> <?php echo $this->getInstance()->helper()->getCustomAttributeHtml($this->getInstance(), $data); ?> /> <?php echo wp_kses_post($data['label']); ?></label>
            <?php if ($logFile && file_exists($logFile) && filesize($logFile) > 0 && is_writable($logFile)): ?>
                <span class="logger-links">(<a class="logger-download" href="" title="<?php echo htmlspecialchars(basename($logFile)) ?>" target="_blank"><?php echo __('Download', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN); ?></a>,
                    <a href="" class="logger-delete" title="<?php echo htmlspecialchars(basename($logFile)) ?>" target="_blank"><?php echo __('Clear log file', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN); ?></a>)</span>
                <?php endif; ?>
            <br/>
            <?php echo $this->getInstance()->helper()->getDescriptionHtml($this->getInstance(), $data); ?>
        </fieldset>
    </td>
</tr>
<script type="text/javascript">
    /* <![CDATA[ */
    (function() {
        jQuery("#row_<?php echo $this->formFieldId; ?> .logger-download").on('click', function(event) {
            var formId = 'eabi-postoffice-logger-form';

            if (!jQuery("#" + formId).size()) {
                jQuery("<form target='_blank' method='POST' style='display:none;' id='" + formId + "'></form>")
                        .attr('action', ajaxurl)
                        .appendTo('body')
                        ;
            }
            /* make form empty */
            jQuery("#" + formId).html(' ');

            //append values
            jQuery('<input type="hidden" />').attr({name: 'action', value: 'eabi_postoffice_get_log_file'}).appendTo(jQuery("#" + formId));
            jQuery('<input type="hidden" />').attr({name: 'carrier_code', value: <?php echo json_encode($this->getInstance()->id); ?>}).appendTo(jQuery("#" + formId));
            jQuery("#" + formId).submit();
            jQuery("#" + formId).remove();
            event.preventDefault();
        });
        jQuery("#row_<?php echo $this->formFieldId; ?> .logger-delete").on('click', function(event) {
            jQuery.ajax({
                url: ajaxurl,
                data : { 
                    action: 'eabi_postoffice_delete_log_file',
                    carrier_code: <?php echo json_encode($this->getInstance()->id);?>
                        },
                type: 'POST',
                dataType: 'json',
                async: true,
                success: function(data) {
                    var message = data.message ? data.message : '';
                    if (data.success) {
                        alert(message);
                        jQuery("#row_<?php echo $this->formFieldId; ?> .logger-links").hide();
                    } else {
                        alert(message);
                    }
                },
                error: function(data) {
                    alert(<?php echo json_encode(htmlspecialchars(__('There was an error while processing request', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)));?>);
                }
            });
            event.preventDefault();
        });
    })();
    /* ]]> */
</script>
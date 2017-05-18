<?php
/*
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */
/* @var $this Eabi_Woocommerce_Postoffice_Block_Licence */
?><span id="span_<?php echo $this->formFieldId; ?>"><a href="#"><?php echo $this->getLogo(); ?><?php echo htmlspecialchars(__('Show', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)); ?> &raquo;</a></span><div class="grid" id="grid_<?php echo $this->formFieldId; ?>">
    <table cellpadding="0" cellspacing="0" class="border widefat eabi-postoffice eabi-form-licence-status" id="table_<?php echo $this->formFieldId; ?>" style="display: none;">
        <tbody>

            <tr class="headings" id="headings_<?php echo $this->formFieldId; ?>">
                <th><?php echo htmlspecialchars(__('Service', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)); ?><?php echo $this->getLogo(); ?></th>
                <th><?php echo htmlspecialchars(__('Country', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)); ?></th>
                <th><?php echo htmlspecialchars(__('Status', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN)); ?></th>
            </tr>
            <?php foreach ($this->getMethods() as $supportedMethodCode) : ?>
                <?php /* @var $supportedMethod WC_Eabi_Postoffice             */ ?>
                <?php $supportedMethod = $this->getMethodByCode($supportedMethodCode); ?>
                <?php foreach ($supportedMethod->getSupportedCountries() as $supportedCountry) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($supportedMethod->method_title) ?></td>
                        <td><?php echo htmlspecialchars($this->getCountryName($supportedCountry)); ?></td>
                        <td><?php echo $this->getLicenceStatus($supportedMethodCode, $supportedCountry); ?></td>
                    </tr>
                <?php endforeach; ?>
                    <?php do_action('eabi_woocommerce_postoffice_' . $supportedMethodCode. '_licence_status_display', $supportedMethod, $this); ?>
            <?php endforeach; ?>

            <tr id="addRow_<?php echo $this->formFieldId; ?>">
                <td colspan="3">
                    <button style="" onclick="return false;" class="button scalable add" type="button" id="addToEndBtn_<?php echo $this->formFieldId; ?>">
                        <span><?php echo $this->getRegisterButtonLabel(); ?></span>
                    </button>
                    <div class="eabi-register-licence" id="register-licence_<?php echo $this->formFieldId; ?>" style="display: none;">
                        <p><?php echo __('Copy your licence key into the field below and save settings', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN); ?>
                        </p>
                        <input type="text" class="input-text regular-input <?php echo esc_attr( $this->getData('class') ); ?>" name="<?php echo esc_attr( $this->formFieldName ); ?>" id="<?php echo esc_attr( $this->formFieldId ); ?>" value="<?php echo htmlspecialchars($this->getValue());?>" />
                        <input  id="save-licence_<?php echo $this->formFieldId; ?>" type="submit" class="button-primary" name="save" value="<?php echo __( 'Save changes', 'woocommerce' ) ?>" style="display: none;" />
                        <p class="description" id="register-licence-description_<?php echo $this->formFieldId; ?>"><?php echo sprintf(__('Get your licence key by clicking <a href="%s">here</a>', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN), admin_url('admin-ajax.php')); ?>
                            <br/>
                            <?php echo __('Look up your order confirmation e-mail for smoother registration process!', WC_Eabi_Postoffice::PLUGIN_TEXT_DOMAIN); ?>
                        </p>
                    </div>

                </td>
            </tr>

        </tbody>
    </table>
</div>
<script type="text/javascript">
    /* <![CDATA[ */
    jQuery(document).ready(function() {
        jQuery('#span_<?php echo $this->formFieldId; ?>').click(function(event) {
            jQuery('#span_<?php echo $this->formFieldId; ?>').hide();
            jQuery('#table_<?php echo $this->formFieldId; ?>').show();
            event.preventDefault();
        });
        if (!jQuery('#grid_<?php echo $this->formFieldId; ?> .eabi-postoffice-green').size()) {
            jQuery('#span_<?php echo $this->formFieldId; ?>').click();
        }
        
        jQuery("#addToEndBtn_<?php echo $this->formFieldId; ?>").click(function(event) {
            jQuery("#grid_<?php echo $this->formFieldId; ?> #register-licence_<?php echo $this->formFieldId; ?>").show();
        });
        jQuery('#<?php echo esc_attr( $this->formFieldId ); ?>').on('paste', function() {
            jQuery('#save-licence_<?php echo $this->formFieldId; ?>').show();
        });
        jQuery("#register-licence-description_<?php echo $this->formFieldId; ?> a").click(function(event) {
            jQuery.ajax({
                url : ajaxurl,
                data : {action: 'eabi_postoffice_licence_request'},
                success: function(data) {
                        var formId = 'eabi-licence-request-form';
                        if (!jQuery("#" + formId).size()) {
                            jQuery("<form target='_blank' method='POST' style='display:none;' id='" + formId + "'></form>")
                                    .attr('action', <?php echo json_encode($this->getRegistrationUrl())?>)
                                    .appendTo('body')
                            ;
                        }
                        /* make form empty */
                        jQuery("#" + formId).html(' ');
                        
                        //append values
                        jQuery('<input type="hidden" />').attr({ name : 'data', value : data['data']}).appendTo(jQuery("#" + formId));
                        jQuery('<input type="hidden" />').attr({ name : 'key', value : data['key']}).appendTo(jQuery("#" + formId));
                        jQuery("#" + formId).submit();
                        jQuery("#" + formId).remove();
                        
                    },
                type : 'POST',
                dataType : 'json',
                async : false
            });
            event.preventDefault();
        });
    });


    /*  ]]> */
</script>
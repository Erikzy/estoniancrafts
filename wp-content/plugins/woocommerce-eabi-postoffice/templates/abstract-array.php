<?php

/* 
 *    *  (c) 2016 Aktsiamaailm OÜ - Kõik õigused kaitstud
 *  Litsentsitingimused on saadaval http://www.e-abi.ee/litsentsitingimused
 *  
 *  (c) 2016 Aktsiamaailm OÜ - All rights reserved
 *  Licence terms are available at http://en.e-abi.ee/litsentsitingimused
 *  

 */
?><div class="grid" id="grid_<?php echo $this->formFieldId; ?>">
    <table cellpadding="0" cellspacing="0" class="border widefat">
        <tbody>

            <tr class="headings" id="headings_<?php echo $this->formFieldId; ?>">
                <?php foreach ($this->getColumns() as $column) :?>
                    <th><?php echo htmlspecialchars($column['label']); ?></th>
                <?php endforeach; ?>
                <th ></th>
            </tr>

            <tr id="addRow_<?php echo $this->formFieldId; ?>">
                <td colspan="<?php echo count($this->getColumns()); ?>"></td>
                <td >
                    <button style="" onclick="jQuery('#template_<?php echo $this->formFieldId; ?>').eabi_array_abstract().add({});" class="button scalable add" type="button" id="addToEndBtn_<?php echo $this->formFieldId; ?>">
                        <span><?php echo $this->getAddButtonLabel(); ?></span>
                    </button>
                </td>
            </tr>

        </tbody>
    </table>
</div>
<script type="text/html" id="template_<?php echo $this->formFieldId; ?>">
    <tr id="#{_id}" data-template-bind='[{"attribute": "id", "value":"id"}]'>
        <?php foreach ($this->getColumns() as $column) :?>
        <td>
            <?php if ($column['type'] == 'select') :?>
                <select  data-template-bind='[{"attribute": "name", "value":"id", "formatter": "nameFormatter", "formatOptions" : "#{_id}[<?php echo $column['name']; ?>]"}]' name=""   data-value="<?php echo $column['name']; ?>"  class="<?php echo $column['class']; ?>" style="<?php echo $column['style']; ?>">
                    <?php foreach ($column['options'] as $option): ?>
                        <option value="<?php echo htmlspecialchars($option['value']); ?>"><?php echo htmlspecialchars($option['label']); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php /*elseif ($column['type'] == 'multiselect') :*/?>
            <?php else: ?>
                <input type="text" data-template-bind='[{"attribute": "name", "value":"id", "formatter": "nameFormatter", "formatOptions" : "#{_id}[<?php echo $column['name']; ?>]"}]' data-value="<?php echo $column['name']; ?>"  class="<?php echo $column['class']; ?>" style="<?php echo $column['style']; ?>"/>
            <?php endif; ?>
        </td>
        
        <?php endforeach; ?>
        <td>
            <button onclick="jQuery('#template_<?php echo $this->formFieldId; ?>').eabi_array_abstract().remove(this);" class="button scalable delete" type="button"><span><?php echo $this->getDeleteButtonLabel(); ?></span></button>
        </td>
    </tr>
</script>
<script type="text/javascript">
    /* <![CDATA[ */
    jQuery(document).ready(function() {
         jQuery('#template_<?php echo $this->formFieldId; ?>').eabi_array_abstract({ 
             form_field_name: '<?php echo $this->formFieldName; ?>',
             form_field_id: '<?php echo $this->formFieldId; ?>'
         },<?php echo json_encode($this->getValue()); ?> );
    });


   /*  ]]> */
</script>
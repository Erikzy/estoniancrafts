<?php
/**
 * Dokan Dashboard Settings Store Form Template
 *
 * @since 2.4
 */
?>
<?php

    $extended_settings = get_user_meta( get_current_user_id(), 'ktt_extended_settings', true );
    $company_name  = isset( $extended_settings['company_name'] ) ? esc_attr( $extended_settings['company_name'] ) : '';
    $company_nr  = isset( $extended_settings['company_nr'] ) ? esc_attr( $extended_settings['company_nr'] ) : '';
    $company_type  = isset( $extended_settings['company_type'] ) ? esc_attr( $extended_settings['company_type'] ) : '';
    $description  = isset( $extended_settings['description'] ) ? esc_attr( $extended_settings['description'] ) : '';

    $media_links  = isset( $extended_settings['media'] ) ? $extended_settings['media'] : [''];
    
    $addresses  = isset( $extended_settings['address'] ) ? $extended_settings['address'] : [['country' => false, 'state' => '', 'city' => '', 'address' => '', 'email' => '', 'phone' => '']];


    $gravatar   = isset( $profile_info['gravatar'] ) ? absint( $profile_info['gravatar'] ) : 0;
    $banner     = isset( $profile_info['banner'] ) ? absint( $profile_info['banner'] ) : 0;
    $storename  = isset( $profile_info['store_name'] ) ? esc_attr( $profile_info['store_name'] ) : '';
    $store_ppp  = isset( $profile_info['store_ppp'] ) ? esc_attr( $profile_info['store_ppp'] ) : '';
    $phone      = isset( $profile_info['phone'] ) ? esc_attr( $profile_info['phone'] ) : '';
    $show_email = isset( $profile_info['show_email'] ) ? esc_attr( $profile_info['show_email'] ) : 'no';
    $show_more_ptab = isset( $profile_info['show_more_ptab'] ) ? esc_attr( $profile_info['show_more_ptab'] ) : 'yes';

    $is_enable_op_discount = dokan_get_option( 'discount_edit', 'dokan_selling' );
    $is_enable_op_discount = $is_enable_op_discount ? $is_enable_op_discount : array();
    
    $is_enable_order_discount = isset( $profile_info['show_min_order_discount'] ) ? esc_attr( $profile_info['show_min_order_discount'] ) : 'no';
    $setting_minimum_order_amount = isset( $profile_info['setting_minimum_order_amount'] ) ? esc_attr( $profile_info['setting_minimum_order_amount'] ) : 0;
    $setting_order_percentage = isset( $profile_info['setting_order_percentage'] ) ? esc_attr( $profile_info['setting_order_percentage'] ) : 0;

    $address         = isset( $profile_info['address'] ) ? $profile_info['address'] : '';
    $address_street1 = isset( $profile_info['address']['street_1'] ) ? $profile_info['address']['street_1'] : '';
    $address_street2 = isset( $profile_info['address']['street_2'] ) ? $profile_info['address']['street_2'] : '';
    $address_city    = isset( $profile_info['address']['city'] ) ? $profile_info['address']['city'] : '';
    $address_zip     = isset( $profile_info['address']['zip'] ) ? $profile_info['address']['zip'] : '';
    $address_country = isset( $profile_info['address']['country'] ) ? $profile_info['address']['country'] : '';
    $address_state   = isset( $profile_info['address']['state'] ) ? $profile_info['address']['state'] : '';

    $map_location   = isset( $profile_info['location'] ) ? esc_attr( $profile_info['location'] ) : '';
    $map_address    = isset( $profile_info['find_address'] ) ? esc_attr( $profile_info['find_address'] ) : '';
    $dokan_category = isset( $profile_info['dokan_category'] ) ? $profile_info['dokan_category'] : '';
    $enable_tnc     = isset( $profile_info['enable_tnc'] ) ? $profile_info['enable_tnc'] : '';
    $store_tnc      = isset( $profile_info['store_tnc'] ) ? $profile_info['store_tnc'] : '' ;

    if ( is_wp_error( $validate ) ) {
        $storename    = $_POST['dokan_store_name'];
        $map_location = $_POST['location'];
        $map_address  = $_POST['find_address'];

        $address_street1 = $_POST['dokan_address']['street_1'];
        $address_street2 = $_POST['dokan_address']['street_2'];
        $address_city    = $_POST['dokan_address']['city'];
        $address_zip     = $_POST['dokan_address']['zip'];
        $address_country = $_POST['dokan_address']['country'];
        $address_state   = $_POST['dokan_address']['state'];
    }

    $dokan_appearance = get_option( 'dokan_appearance' );

?>
<?php do_action( 'dokan_settings_before_form', $current_user, $profile_info ); ?>

    <?php lbDokan::get_instance()->user->display_shop_profile_completeness($current_user) ?>

    <form method="post" id="store-form"  action="" class="dokan-form-horizontal">

        <?php wp_nonce_field( 'dokan_store_settings_nonce' ); ?>
        <input type="hidden" value="<?php echo $gravatar; ?>" name="dokan_gravatar">

        <?php if ( ! empty( $dokan_appearance['store_header_template'] ) && 'layout3' !== $dokan_appearance['store_header_template'] ): ?>
            <div class="dokan-banner">

                <div class="image-wrap<?php echo $banner ? '' : ' dokan-hide'; ?>">
                    <?php $banner_url = $banner ? wp_get_attachment_url( $banner ) : ''; ?>
                    <input type="hidden" class="dokan-file-field" value="<?php echo $banner; ?>" name="dokan_banner">
                    <img class="dokan-banner-img" src="<?php echo esc_url( $banner_url ); ?>">

                    <a class="close dokan-remove-banner-image">&times;</a>
                </div>

                <div class="button-area<?php echo $banner ? ' dokan-hide' : ''; ?>">
                    <i class="fa fa-cloud-upload"></i>

                    <a href="#" class="dokan-banner-drag dokan-btn dokan-btn-info dokan-theme"><?php _e( 'Upload banner', 'dokan' ); ?></a>
                    <p class="help-block">
                        <?php
                        /**
                         * Filter `dokan_banner_upload_help`
                         *
                         * @since 2.4.10
                         */
                        $general_settings = get_option( 'dokan_general', [] );
                        $banner_width = ! empty( $general_settings['store_banner_width'] ) ? $general_settings['store_banner_width'] : 625;
                        $banner_height = ! empty( $general_settings['store_banner_height'] ) ? $general_settings['store_banner_height'] : 300;

                        $help_text = sprintf(
                            __('Upload a banner for your store. Banner size is (%sx%s) pixels.', 'dokan' ),
                            $banner_width, $banner_height
                        );

                        echo apply_filters( 'dokan_banner_upload_help', $help_text );
                        ?>
                    </p>
                </div>
            </div> <!-- .dokan-banner -->

            <?php do_action( 'dokan_settings_after_banner', $current_user, $profile_info ); ?>
        <?php endif; ?>

        <?php $user = get_user_by( 'id', get_current_user_id() ); ?>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_store_id"><?php _e( 'Store ID', 'ktt' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <input id="dokan_store_id" required value="<?php echo $user->user_nicename; ?>" name="dokan_store_id" class="dokan-form-control" type="text" disabled>
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_store_name"><?php _e( 'Store Name', 'dokan' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <input id="dokan_store_name" required value="<?php echo $storename; ?>" name="dokan_store_name" placeholder="<?php _e( 'store name', 'dokan'); ?>" class="dokan-form-control" type="text">
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_company_name"><?php _e( 'Company Name', 'ktt' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <input id="dokan_store_name" required value="<?php echo $company_name; ?>" name="dokan_company_name" placeholder="<?php _e( 'company name', 'ktt'); ?>" class="dokan-form-control" type="text">
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_company_nr"><?php _e( 'Company reg nr', 'ktt' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <input id="dokan_company_nr" required value="<?php echo $company_nr; ?>" name="dokan_company_nr" placeholder="<?php _e( 'company registration number', 'ktt'); ?>" class="dokan-form-control" type="text">
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_company_type"><?php _e( 'Company type', 'ktt' ); ?></label>

            <div class="dokan-w5 dokan-text-left">

                <select name="dokan_company_type" id="dokan_company_type">
                    <option value="none"> - <?php _e( 'company type', 'ktt'); ?> - </option>
                    <option value="1" <?= (($company_type == '1')? 'selected': '') ?>><?php _e( 'FIE', 'ktt'); ?></option>
                    <option value="2" <?= (($company_type == '2')? 'selected': '') ?>><?php _e( 'OÜ', 'ktt'); ?></option>
                    <option value="3" <?= (($company_type == '3')? 'selected': '') ?>><?php _e( 'AS', 'ktt'); ?></option>
                </select>
            </div>
        </div>


        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_description"><?php _e( 'Short description', 'ktt' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <textarea id="dokan_description" name="dokan_description"><?php echo $description; ?></textarea>
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_media"><?php _e( 'Media links', 'ktt' ); ?></label>

            <div class="dokan-w5 dokan-text-left">

                <div class="lb-elastic-container">
                    <div class="lb-elastic-elements">

                        <?php 

                            foreach($media_links as $link){ ?>

                                <div class="lb-elastic-element">
                                    <input value="<?php echo $link; ?>" name="dokan_media[]" placeholder="<?php _e( 'http://', 'ktt'); ?>" class="dokan-form-control" type="text">
                                </div>

                        <?php } ?>
                        
                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more</a>
                </div>
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_description"><?php _e( 'Address', 'ktt' ); ?></label>

            <div class="dokan-w5 dokan-text-left">

                <div class="lb-elastic-container">
                    <div class="lb-elastic-elements">

                        <?php
                            $i = 0;
                            foreach( $addresses as $address){
                        ?>
                        
                            <div class="lb-elastic-element lb-input-margins">
                                <?php lb_display_country_select($address['country'], 'dokan_address['.$i.'][country]') ?>
                               
                                <input value="<?= $address['state'] ?>" name="dokan_address[<?= $i ?>][state]" placeholder="<?php _e( 'State', 'ktt'); ?>" class="dokan-form-control" type="text">
                                <input value="<?= $address['city'] ?>" name="dokan_address[<?= $i ?>][city]" placeholder="<?php _e( 'City', 'ktt'); ?>" class="dokan-form-control" type="text">
                                <input value="<?= $address['address'] ?>" name="dokan_address[<?= $i ?>][address]" placeholder="<?php _e( 'Address', 'ktt'); ?>" class="dokan-form-control" type="text">
                                <input value="<?= $address['email'] ?>" name="dokan_address[<?= $i ?>][email]" placeholder="<?php _e( 'Shop e-mail', 'ktt'); ?>" class="dokan-form-control" type="email">
                                <input value="<?= $address['phone'] ?>" name="dokan_address[<?= $i ?>][phone]" placeholder="<?php _e( 'Shop phone', 'ktt'); ?>" class="dokan-form-control" type="text">
                            </div>
                        <?php $i++; } ?>
                
                        
                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more shops</a>
                </div>
            </div>
        </div>

        <?php  /*

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_store_ppp"><?php _e( 'Store Product Per Page', 'dokan' ); ?></label>

            <div class="dokan-w5 dokan-text-left">
                <input id="dokan_store_ppp" value="<?php echo $store_ppp; ?>" name="dokan_store_ppp" placeholder="10" class="dokan-form-control" type="number">
            </div>
        </div>
         <!--address-->
        */ ?>
        <?php  /*
        <?php
        $verified = false;

        if ( isset( $profile_info['dokan_verification']['info']['store_address']['v_status'] ) ) {
            if ( $profile_info['dokan_verification']['info']['store_address']['v_status'] == 'approved' ){
                $verified = true;
            }
        }

        dokan_seller_address_fields( $verified );

        ?>
        <!--address-->
    
        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="setting_phone"><?php _e( 'Phone No', 'dokan' ); ?></label>
            <div class="dokan-w5 dokan-text-left">
                <input id="setting_phone" value="<?php echo $phone; ?>" name="setting_phone" placeholder="<?php _e( '+123456..', 'dokan' ); ?>" class="dokan-form-control input-md" type="text">
            </div>
        </div>
       
        <?php if ( ! is_int( key( $is_enable_op_discount ) ) && array_key_exists("order-discount", $is_enable_op_discount ) == "order-discount" ) : ?>
            <div class="dokan-form-group">
                <label class="dokan-w3 dokan-control-label"><?php _e( 'Discount ', 'dokan' ); ?></label>
                <div class="dokan-w5 dokan-text-left">
                    <div class="checkbox">
                        <label class="dokan-control-label" for="lbl_setting_minimum_quantity">
                            <input type="hidden" name="setting_show_minimum_discount_option" value="no">
                            <input id="lbl_setting_minimum_quantity" type="checkbox" name="setting_show_minimum_order_discount_option" value="yes"<?php checked( $is_enable_order_discount, 'yes' ); ?>>
                            <?php _e( 'Enable storewide discount', 'dokan' ); ?>
                        </label>
                    </div>
                    <div class="dokan-text-left dokan-form-group show_if_needs_sw_discount <?php echo ($is_enable_order_discount=='yes') ? '' : 'hide_if_order_discount' ;?>">
                        <input id="setting_minimum_order_amount" value="<?php echo $setting_minimum_order_amount; ?>" name="setting_minimum_order_amount" placeholder="<?php _e( 'Minimum Order Amount', 'dokan' ); ?>" class="dokan-form-control input-md" type="number">
                    </div>
                    <div class="dokan-text-left dokan-form-group show_if_needs_sw_discount <?php echo ($is_enable_order_discount=='yes') ? '' : 'hide_if_order_discount' ;?>">
                        <input id="setting_order_percentage" value="<?php echo $setting_order_percentage; ?>" name="setting_order_percentage" placeholder="<?php _e( 'Percentage', 'dokan' ); ?>" class="dokan-form-control input-md" type="number">
                    </div>
                </div>
            </div>
        <?php endif;?>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label"><?php _e( 'Email', 'dokan' ); ?></label>
            <div class="dokan-w5 dokan-text-left">
                <div class="checkbox">
                    <label>
                        <input type="hidden" name="setting_show_email" value="no">
                        <input type="checkbox" name="setting_show_email" value="yes"<?php checked( $show_email, 'yes' ); ?>> <?php _e( 'Show email address in store', 'dokan' ); ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label"><?php _e( 'More product', 'dokan' ); ?></label>
            <div class="dokan-w5 dokan-text-left">
                <div class="checkbox">
                    <label>
                        <input type="hidden" name="setting_show_more_ptab" value="no">
                        <input type="checkbox" name="setting_show_more_ptab" value="yes"<?php checked( $show_email, 'yes' ); ?>> <?php _e( 'Enable tab on product single page view', 'dokan' ); ?>
                    </label>
                </div>
            </div>
        </div>
        

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="setting_map"><?php _e( 'Map', 'dokan' ); ?></label>

            <div class="dokan-w6 dokan-text-left">
                <input id="dokan-map-lat" type="hidden" name="location" value="<?php echo $map_location; ?>" size="30" />

                <div class="dokan-map-wrap">
                    <div class="dokan-map-search-bar">
                        <input id="dokan-map-add" type="text" class="dokan-map-search" value="<?php echo $map_address; ?>" name="find_address" placeholder="<?php _e( 'Type an address to find', 'dokan' ); ?>" size="30" />
                        <a href="#" class="dokan-map-find-btn" id="dokan-location-find-btn" type="button"><?php _e( 'Find Address', 'dokan' ); ?></a>
                    </div>

                    <div class="dokan-google-map" id="dokan-map"></div>
                </div>
            </div> <!-- col.md-4 -->
        </div> <!-- .dokan-form-group -->
     */ ?>
        <!--terms and conditions enable or not -->
        <?php
        $tnc_enable = dokan_get_option( 'seller_enable_terms_and_conditions', 'dokan_general', 'off' );
        if ( $tnc_enable == 'on' ) :
            ?>
            <div class="dokan-form-group">
                <label class="dokan-w3 dokan-control-label"><?php _e( 'Terms and Conditions', 'dokan' ); ?></label>
                <div class="dokan-w5 dokan-text-left dokan_tock_check">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="dokan_store_tnc_enable" value="on" <?php echo $enable_tnc == 'on' ? 'checked':'' ; ?> name="dokan_store_tnc_enable" ><?php _e( 'Show terms and conditions in store page', 'dokan' ); ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="dokan-form-group" id="dokan_tnc_text">
                <label class="dokan-w3 dokan-control-label" for="dokan_store_tnc"><?php _e( 'TOC Details', 'dokan' ); ?></label>
                <div class="dokan-w8 dokan-text-left">
                    <?php
                        $settings = array(
                            'editor_height' => 200,
                            'media_buttons' => false,
                            'teeny'         => true,
                            'quicktags'     => false
                        );
                        wp_editor( $store_tnc, 'dokan_store_tnc', $settings );
                    ?>
                </div>
            </div>

        <?php endif;?>


        <?php do_action( 'dokan_settings_form_bottom', $current_user, $profile_info ); ?>

        <div class="dokan-form-group">

            <div class="dokan-w4 ajax_prev dokan-text-left" style="margin-left:24%;">
                <button name="dokan_update_store_settings" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" data-balloon-length="medium" data-balloon="<?php _e( 'Make sure you didn\'t make any spelling mistakes. This data will be sent to our translators shortly.', 'ktt' ); ?>" data-balloon-pos="up" ><?php esc_attr_e( 'Save changes', 'dokan' ); ?></button>

            </div>
        </div>
    </form>

    <?php do_action( 'dokan_settings_after_form', $current_user, $profile_info ); ?>

<script type="text/javascript">

    (function($) {
        var dokan_address_wrapper = $( '.dokan-address-fields' );
        var dokan_address_select = {
            init: function () {

                dokan_address_wrapper.on( 'change', 'select.country_to_state', this.state_select );
            },
            state_select: function () {
                var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
                    states = $.parseJSON( states_json ),
                    $statebox = $( '#dokan_address_state' ),
                    input_name = $statebox.attr( 'name' ),
                    input_id = $statebox.attr( 'id' ),
                    input_class = $statebox.attr( 'class' ),
                    value = $statebox.val(),
                    selected_state = '<?php echo $address_state; ?>',
                    input_selected_state = '<?php echo $address_state; ?>',
                    country = $( this ).val();

                if ( states[ country ] ) {

                    if ( $.isEmptyObject( states[ country ] ) ) {

                        $( 'div#dokan-states-box' ).slideUp( 2 );
                        if ( $statebox.is( 'select' ) ) {
                            $( 'select#dokan_address_state' ).replaceWith( '<input type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required />' );
                        }

                        $( '#dokan_address_state' ).val( 'N/A' );

                    } else {
                        input_selected_state = '';

                        var options = '',
                            state = states[ country ];

                        for ( var index in state ) {
                            if ( state.hasOwnProperty( index ) ) {
                                if ( selected_state ) {
                                    if ( selected_state == index ) {
                                        var selected_value = 'selected="selected"';
                                    } else {
                                        var selected_value = '';
                                    }
                                }
                                options = options + '<option value="' + index + '"' + selected_value + '>' + state[ index ] + '</option>';
                            }
                        }

                        if ( $statebox.is( 'select' ) ) {
                            $( 'select#dokan_address_state' ).html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );
                        }
                        if ( $statebox.is( 'input' ) ) {
                            $( 'input#dokan_address_state' ).replaceWith( '<select type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required ></select>' );
                            $( 'select#dokan_address_state' ).html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );
                        }
                        $( '#dokan_address_state' ).removeClass( 'dokan-hide' );
                        $( 'div#dokan-states-box' ).slideDown();

                    }
                } else {


                    if ( $statebox.is( 'select' ) ) {
                        input_selected_state = '';
                        $( 'select#dokan_address_state' ).replaceWith( '<input type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required="required"/>' );
                    }
                    $( '#dokan_address_state' ).val(input_selected_state);

                    if ( $( '#dokan_address_state' ).val() == 'N/A' ){
                        $( '#dokan_address_state' ).val('');
                    }
                    $( '#dokan_address_state' ).removeClass( 'dokan-hide' );
                    $( 'div#dokan-states-box' ).slideDown();
                }
            }
        }

    })(jQuery);
</script>

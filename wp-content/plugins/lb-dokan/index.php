<?php
/**
 * Plugin Name: Dokan extension
 * Description: Käsitööturg custom extension for Dokan plugin
 * Version: 1.0
 */

require_once('lbDokanUser.php');

class lbDokan{

    private static $instance = null;
    public $user;

    public static function get_instance() {
 
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
 
        return self::$instance;
 
    }

	function __construct(){

        $this->user = new lbDokanUser();

        add_action( 'wp_enqueue_scripts', [$this, 'register_scripts'], 15 );
		add_action( 'admin_enqueue_scripts', [$this, 'admin_register_scripts'], 15 );
        
        add_action( 'dokan_product_edit_after_options', [$this, 'add_product_options_form'] );
		add_action( 'dokan_product_updated', [$this, 'product_updated'] );
		
        add_action( 'add_meta_boxes', [$this, 'add_box'] );
        add_action( 'save_post_product', [$this, 'save_post'], 11, 2 );

        add_action( 'wp_head', [$this, 'wp_head'] );
        add_action( 'wp_ajax_lb_tags', [$this, 'available_tags'] );
        add_action( 'dokan_process_product_meta', [$this, 'save_tags']);


    }

	function register_scripts(){
        global $wp_scripts;

        if (!is_admin()) {
            wp_enqueue_style(  'lb-balloon', plugin_dir_url( __FILE__ ) . 'balloon.min.css', false,'1.0','all');
            wp_enqueue_style(  'lb-dokan', plugin_dir_url( __FILE__ ) . 'dokan.css', false,'1.0','all');
            wp_enqueue_script( 'lb-dokan', plugin_dir_url( __FILE__ ) . 'dokan.js', false,'1.0','all');
            wp_enqueue_style(  'lb-select2', plugin_dir_url( __FILE__ ) . 'select2/css/select2.min.css', false,'1.0','all');
            wp_enqueue_script( 'lb-balloon', plugin_dir_url( __FILE__ ) . 'select2/js/select2.full.min.js', false,'1.0','all');
        }

    }

    function admin_register_scripts(){
        global $wp_scripts;

        wp_enqueue_style( 'lb-dokan-admin', plugin_dir_url( __FILE__ ) . 'dokan-admin.css', false,'1.0','all');
        wp_enqueue_script( 'lb-dokan-admin', plugin_dir_url( __FILE__ ) . 'dokan-admin.js', false,'1.0','all');
        
    }

    function wp_head(){
        ?>
        <script type="text/javascript">
            var siteurl = '<?= get_bloginfo("url"); ?>';
        </script>
        <?php
    }

    function available_tags(){

        $output = [];

        $terms = get_terms([
            'taxonomy' => 'product_tag',
            'search' => $_POST['term'],
            'number' => 5,
            'hide_empty' => false
        ]);

        $offer_new_tag = true;
        if(strlen($_POST['term']) < 3){
            $offer_new_tag = false;
        }

        if( count($terms) ){

            foreach($terms as $term){

                if( mb_strtolower( $term->name ) == mb_strtolower( $_POST['term'] ) ){
                    $offer_new_tag = false;
                }

                $output[] = [ 'id' => $term->term_id, 'text' => $term->name ];

            }

        }

        if($offer_new_tag){
            array_unshift($output, ['id' => 'lb-new-'.$_POST['term'], 'text' => $_POST['term'].' (new)']);
        }

        $this->json( ['results' => $output] );

    }

    /**
     * Save tags
     */
    function save_tags( $post_id ){

        $tag_ids = [];

        if( isset($_POST['lb-tags']) && count($_POST['lb-tags']) ){
            foreach ($_POST['lb-tags'] as $tag) {
                
                if( strpos($tag, 'lb-new-') === 0 ){
                    $new_tag = str_replace('lb-new-', '', $tag);

                    // Create a new tag
                    $new_term = wp_insert_term(
                        $new_tag,
                        'product_tag'
                    );

                    if(!is_wp_error($new_term)){
                        $tag_ids[] = (int)$new_term['term_id'];
                    }

                }else{
                    $tag_ids[] = (int)$tag;
                }
            
            }
        }

        wp_set_object_terms( $post_id, $tag_ids, 'product_tag', false );

    }

    private function json($data){

        header('Content-Type: application/json');
        echo json_encode( $data );
        exit;

    }

    static function display_product_completeness($product_id){

        if( !$product_id ){
            return;
        }

        $completeness = 0;
        $meta_data = get_post_meta($product_id, '', true);

        $tag_count = count( wp_get_post_terms( $product_id, 'product_tag' ) );

        $required_fields = ['_manufacturing_method', '_manufacturing_desc', '_manufacturing_time', '_manufacturing_qty', '_maintenance_info'];

        $number_of_req_items = count($required_fields) + 7; // 

        if($tag_count){
            $completeness += 100/$number_of_req_items;
        }

        foreach($meta_data as $key => $meta){

            $meta = $meta[0];

            if( in_array($key, $required_fields) && !empty($meta) ){

                $completeness += 100/$number_of_req_items;
            
            }

        }

        if( isset($meta_data['_media_links']) && is_array($meta_data['_media_links']) ){

            $link = unserialize($meta_data['_media_links'][0]);

            if( array_shift($link) != ''){    
                $completeness += 100/$number_of_req_items;
            }

        }

        if( isset($meta_data['_certificates']) && is_array($meta_data['_certificates']) ){

            $cert = unserialize($meta_data['_certificates'][0]);
            $cert = array_shift($cert);

            if( $cert['type'] != '' && $cert['file'] != ''){    
                $completeness += 100/$number_of_req_items;
            }

        }

        if( isset($meta_data['_materials']) && is_array($meta_data['_materials']) ){
            
            $material = unserialize($meta_data['_materials'][0]);
            $material = array_shift($material);

            if( $material['country'] != '' ){    
                $completeness += 100/$number_of_req_items;
            }
            if( $material['name'] != '' ){    
                $completeness += 100/$number_of_req_items;
            }
            if( $material['contents'] != '' ){    
                $completeness += 100/$number_of_req_items;
            }
            if( $material['desc'] != '' ){    
                $completeness += 100/$number_of_req_items;
            }

        }
        
        self:: display_completeness_bar( round( 25 + 75 * ceil($completeness)/100 ), __( 'Product complete', 'ktt' ) ); // 25% default
        
    }

    static function display_completeness_bar($percentage, $message){

        ?>
        <div class="dokan-panel dokan-panel-default">
            <div class="dokan-panel-body">
            <div class="dokan-progress lb-progress">
                <div class="dokan-progress-bar dokan-progress-bar-info dokan-progress-bar-striped" role="progressbar" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?= $percentage ?>%">
                    <?= $percentage ?>% <?= $message ?></div>
            </div>
           </div>
        </div>
        <?php

    }

    /**
     * Displays extended form fields whed adding/editing new products
     */
    function add_product_options_form(){
        global $post;
        
        $this->product_extended_form($post->ID);

    }

    function product_extended_form($post_id){

        ?>

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Shipping options', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
                <div class="dokan-form-group">

                    <?php $is_fragile = ( get_post_meta($post_id, '_fragile_cargo', true) == 'yes' ) ? 'yes' : 'no'; ?>

                    <?php dokan_post_input_box( $post_id, '_fragile_cargo', array('value' => $is_fragile, 'label' => __( 'Fragile cargo', 'ktt' ) ), 'checkbox' ); ?>
                </div>

            </div>
        </div><!-- .lb-dokan-options -->


        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Used materials', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
                <div class="lb-elastic-container">
                    <div class="lb-elastic-elements">
                        <?php

                        $materials = get_post_meta($post_id, '_materials', true);

                        // var_dump($materials);die();

                        if( !is_array($materials) || !count($materials) ){
                            $materials = [['country' => '', 'name' => '', 'contents' => '', 'desc' => '']];
                        }

                        // var_dump($materials);die();


                        foreach($materials as $material){

                        ?>
                            <div class="lb-elastic-element lb-input-margins">

                                <div class="dokan-form-group">
                                    <label class="form-label"><?php _e( 'Material country', 'ktt' ); ?></label>
                                    <?php lb_display_country_select($material['country'], '_material_country[]') ?>
                                </div>

                                <div class="dokan-form-group">
                                    <label class="form-label"><?php _e( 'Material name', 'ktt' ); ?></label>
                                    <?php dokan_post_input_box( $post_id, '_material_name[]', array( 'placeholder' => __( 'Material name', 'ktt' ), 'value' => $material['name'] ), 'text' ); ?>
                                </div>

                                <div class="dokan-form-group">
                                    <label class="form-label"><?php _e( 'Material contents', 'ktt' ); ?></label>
                                    <?php dokan_post_input_box( $post_id, '_material_contents[]', array( 'placeholder' => __( 'Material contents', 'ktt' ), 'value' => $material['contents'] ), 'text' ); ?>
                                </div>

                                <div class="dokan-form-group">
                                    <label class="form-label"><?php _e( 'Description', 'ktt' ); ?></label>
                                    <?php dokan_post_input_box( $post_id, '_material_desc[]', array( 'placeholder' => __( 'Description', 'ktt' ), 'value' => $material['desc'] ), 'text' ); ?>
                                </div>
                                <hr>
                            </div>

                        <?php 
                        }
                        ?>

                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                </div>
            </div>

        </div><!-- .lb-dokan-options -->

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Manufacturing info', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
               
                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing method', 'ktt' ); ?></label>
                 
                    <?php dokan_post_input_box( $post_id, '_manufacturing_method', array( 'options' => array(
                                            ''  => __(' - select method - ', 'ktt'),
                                            'hand' => __( 'Hand crafted', 'ktt' ),
                                            'machine' => __( 'Machined', 'ktt' )
                                        ), 'value' => get_post_meta($post_id, '_manufacturing_method', true) ), 'select' ); ?>
                
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing description', 'ktt' ); ?></label>
                    <?php dokan_post_input_box( $post_id, '_manufacturing_desc', array( 'placeholder' => __( 'Manufacturing description', 'ktt' ), 'value' => get_post_meta($post_id, '_manufacturing_desc', true) ), 'text' ); ?>
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing time', 'ktt' ); ?></label>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_time', array( 'placeholder' => __( 'Manufacturing time', 'ktt' ), 'value' => get_post_meta($post_id, '_manufacturing_time', true) ), 'number' ); ?>
                        </div>
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_time_unit', array( 'options' => array(
                                                'hour' => __( 'Hours', 'ktt' ),
                                                'day' => __( 'Days', 'ktt' ),
                                                'week' => __( 'Weeks', 'ktt' ),
                                                'month' => __( 'Months', 'ktt' )
                                            ), 'value' => get_post_meta($post_id, '_manufacturing_time_unit', true) ), 'select' ); ?>
                        </div>
                    </div>
                    
                </div>

                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Manufacturing quantity', 'ktt' ); ?></label>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_qty', array( 'placeholder' => __( 'Manufacturing quantity', 'ktt' ), 'value' => get_post_meta($post_id, '_manufacturing_qty', true) ), 'number' ); ?>
                        </div>
                        <div class="col-md-6">
                            <?php dokan_post_input_box( $post_id, '_manufacturing_qty_unit', array( 'options' => array(
                                                'hour' => __( 'in an hour', 'ktt' ),
                                                'day' => __( 'in a day', 'ktt' ),
                                                'week' => __( 'in a week', 'ktt' ),
                                                'month' => __( 'in a month', 'ktt' ),
                                                'year' => __( 'in a year', 'ktt' )
                                            ), 'value' => get_post_meta($post_id, '_manufacturing_qty_unit', true) ), 'select' ); ?>
                        </div>
                    </div>
                    
                </div>

            </div>

        </div><!-- .lb-dokan-options -->

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Maintenance', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
               
                <div class="dokan-form-group">
                    <label class="form-label"><?php _e( 'Maintenance info', 'ktt' ); ?></label>
                 
                    <?php wp_editor( get_post_meta($post_id, '_maintenance_info', true), '_maintenance_info', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_excerpt') ); ?>
                
                </div>


            </div>

        </div><!-- .lb-dokan-options -->

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'External media links', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
               
                <div class="lb-elastic-container">

                    <div class="lb-elastic-elements">
                    <?php

                    $media_links = get_post_meta($post_id, '_media_links', true);

                    if(!is_array($media_links)){
                        $media_links = [''];
                    }

                    foreach($media_links as $link){

                        ?>

                        <div class="lb-elastic-element lb-input-margins">
                            <div class="dokan-form-group">
                                <?php dokan_post_input_box( $post_id, '_media_link[]', array( 'placeholder' => 'http://', 'value' => $link ), 'text' ); ?>
                            </div>
                        </div>
                        
                        <?php

                    }

                    ?>

                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                    
                </div>

            </div>

        </div><!-- .lb-dokan-options -->

        <div class="lb-dokan-options dokan-edit-row dokan-clearfix">
            <div class="dokan-side-left">
                <h2><?php _e( 'Patent / Certificate', 'ktt' ); ?></h2>
            </div>

            <div class="dokan-side-right">
               
                <div class="lb-elastic-container">
                    <div class="lb-elastic-elements">

                        <?php

                        $certificates = get_post_meta($post_id, '_certificates', true);

                        if(!is_array($certificates) ){
                            $certificates = [['type' => '', 'file' => '']];
                        }

                        foreach($certificates as $cert){

                            $file = (int)$cert['file'];

                        ?>

                            <div class="lb-elastic-element lb-input-margins">
                                <div class="dokan-form-group">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php dokan_post_input_box( $post_id, '_cert_type[]', array( 'options' => array(
                                                                '' => __( ' - select type - ', 'ktt' ),
                                                                'patent' => __( 'Patent', 'ktt' ),
                                                                'trademark' => __( 'Trademark', 'ktt' ),
                                                                'certificate' => __( 'Certificate', 'ktt' )
                                                            ), 'value' => $cert['type'] ), 'select' ); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="hidden" class="input-text" name="_cert_file[]" value="<?= $file; ?>" />

                                            <a href="#remove" class="lb-file-placeholder <?php if( $file ){ echo 'active'; } ?>"></a>
                                            <a href="#add-file" class="lb-add-doc <?php if( !$file ){ echo 'active'; } ?>"> + <?php _e( 'Add document', 'ktt' ); ?></a>
                                            <a href="#add-file" class="lb-remove-doc <?php if( $file ){ echo 'active'; } ?>"> + <?php _e( 'Remove document', 'ktt' ); ?></a>
                                            
                                        </div>
                                    </div>

                                </div>
                            </div>
                        
                        <?php } ?>

                    </div>
                    <a href="#lb-add-more" class="lb-elastic-add"> + add more...</a>
                    
                </div>

            </div>

        </div><!-- .lb-dokan-options -->

        <?php

    }

    /**
     * Save additional Dokan fields to product meta
     */
    function product_updated($product_id){

        update_post_meta( $product_id, '_backorder_time', wc_clean($_POST['_backorder_time']));
        update_post_meta( $product_id, '_fragile_cargo', wc_clean($_POST['_fragile_cargo']));
        update_post_meta( $product_id, '_manufacturing_method', wc_clean($_POST['_manufacturing_method']));
        update_post_meta( $product_id, '_manufacturing_desc', wc_clean($_POST['_manufacturing_desc']));
        update_post_meta( $product_id, '_manufacturing_time', wc_clean($_POST['_manufacturing_time']));
        update_post_meta( $product_id, '_manufacturing_time_unit', wc_clean($_POST['_manufacturing_time_unit']));
        update_post_meta( $product_id, '_manufacturing_qty', wc_clean($_POST['_manufacturing_qty']));
        update_post_meta( $product_id, '_manufacturing_qty_unit', wc_clean($_POST['_manufacturing_qty_unit']));

        update_post_meta( $product_id, '_maintenance_info', $_POST['_maintenance_info']);
        
        if( ! empty( $_POST['_media_link'] ) ){

            $media = $_POST['_media_link'];

            // Remove all empty strings first
            $media = array_diff($media, array('http://', 'https://', ''));

            // Make sure all media links have http:// or https:// in front of them
            $media = array_map(function($element) {
                    return (strpos($element, 'http://') !== 0 && strpos($element, 'https://') !== 0)? 'http://'.$element : $element;
                },
                $media
            );

            if(!count($media)){ $media = ['']; }
            
            update_post_meta( $product_id, '_media_links', wc_clean($media));

        }



        if( ! empty( $_POST['_material_country'] ) ){

            $material_array = [];

            foreach ($_POST['_material_country'] as $index => $country) {

                $material = [ 'country' => $country, 'name' => $_POST['_material_name'][$index], 'contents' => $_POST['_material_contents'][$index], 'desc' => $_POST['_material_desc'][$index] ];

                $data_entered = array_diff($material, array('', ' '));
                if( count($data_entered) ) { 
                    $material_array[] = $material; 
                }

            }

            if(!count($material_array)){ 
                $material_array = [['country' => '', 'name' => '', 'contents' => '', 'desc' => '']];
            }

            update_post_meta( $product_id, '_materials', wc_clean($material_array));

        }


        if( ! empty( $_POST['_cert_file'] ) ){

            $certificates = [];

            foreach ($_POST['_cert_file'] as $index => $file) {

                if( $file == 0 || $file == '0' || empty($_POST['_cert_type'][$index]) ){
                    continue;
                }

                $cert = [ 'type' => $_POST['_cert_type'][$index], 'file' => $file ];

                $data_entered = array_diff($cert, array('', ' '));
                if( count($data_entered) ) { 
                    $certificates[] = $cert; 
                }

            }

            if(!count($certificates)){ 
                $certificates = [['type' => '', 'file' => '']];
            }

            update_post_meta( $product_id, '_certificates', wc_clean($certificates));

        }

    }


    function save_post( $post_id, $post ){

        //If is doing auto-save: exit function
        if( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) return;

        //If is doing auto-save via AJAX: exit function
        if( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;

        if (isset($post->post_status) && 'auto-draft' == $post->post_status) {
            return;
        }

        if( is_admin() && ! ( wp_is_post_revision( $product_id) || wp_is_post_autosave( $product_id ) ) ) {
        // if( !wp_is_post_revision($post_id) ) {
            $this->product_updated( $post_id );
        }

    }

    public function add_box(){

        add_meta_box(
            'lb-dokan-extra',                           // Unique ID
            'Extra fields',                             // Box title
            [$this, 'product_box_extra_fields'],        // Content callback
            ['product']                                 // post type
        );

    }

    function product_box_extra_fields( $post ) {

        $this->product_extended_form($post->ID);
        
    }

}

lbDokan::get_instance();
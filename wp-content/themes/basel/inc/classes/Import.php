<?php if ( ! defined('BASEL_THEME_DIR')) exit('No direct script access allowed');

/**
 * BASEL_Import
 *
 */

class BASEL_Import {
	
	private $_basel_versions = array();

	private $_response = array();

	private $_importer;

	private $_file_path;

	private $_version;

	private $_active_widgets;

	private $_widgets_counter = 1;

	private $_import_attachments = true;

	private $_shop_page_id = 0;

	private $_process = array();

	public function __construct() {

		$this->_basel_versions = basel_get_config( 'versions' );

		$this->_response = array( 'status' => 'fail', 'message' => '' );

		$this->_file_path = BASEL_THEMEROOT . '/inc/dummy-content/';
		//$this->_file_path = BASEL_ASSETS . '/dummy-content/';

		add_action( 'admin_menu', array( $this, 'page_in_menu' ) );

		add_action( 'wp_ajax_basel_import_data', array( $this, 'import_action' ) ); 

	}

	public function page_in_menu() {

		if( ! basel_get_opt( 'dummy_import' ) ) return;
		
		add_menu_page( 
			__( 'BASEL Import', 'basel' ), 
			__( 'Dummy content', 'basel' ), 
			'manage_options', 
			'basel_import', 
			array( $this, 'admin_import_screen' ),
			BASEL_ASSETS . '/images/theme-admin-icon.png', 
			64 
		);

	}

	public function admin_import_screen() {

		?>
			<div class="wrap metabox-holder basel-import-page">
				<h2><?php _e( 'Import BASEL Demo Content', 'basel' ) ?></h2>
				<br>
				<div class="postbox">

					<h3 class="hndle"><span><?php _e( 'Import box', 'basel' ) ?></span></h3>

					<div class="inside">

						<?php if ( ! function_exists( 'is_shop' ) ): ?>
							<p class="basel-notice">
								<?php 
									printf(
										__('To import data properly we recommend you to install <strong><a href="%s">WooCommerce</a></strong> plugin', 'basel'), 
										esc_url( add_query_arg( 'page', urlencode( 'tgmpa-install-plugins' ), self_admin_url( 'themes.php' ) ) )
									); 
								?>
							</p>
						<?php endif ?>

						<?php if( $this->_required_plugins() ): ?>
							<p class="basel-warning">
								<?php 
									printf(
										__('You need to install the following plugins to use our import function: <strong><a href="%s">%s</a></strong>', 'basel'), 
										esc_url( add_query_arg( 'page', urlencode( 'tgmpa-install-plugins' ), self_admin_url( 'themes.php' ) ) ),
										implode(', ', $this->_required_plugins()) 
									); 
								?>
							</p>
						<?php endif; ?>

						<form action="#" method="post" id="basel-import-form">

							<div class="basel-response"></div>

							<table class="form-table">
								<tr>
									<th><label for="basel_version"><?php _e( 'Choose page to import', 'basel' ) ?></label></th>
									<td>
											
										<select class="" id="basel_version" name="basel_version">
											<option>--select--</option>
											<?php foreach ($this->_basel_versions as $key => $value): ?>
												<?php if( $this->_able_to_import( $key ) ): ?>
													<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value['title'] ); ?></option>
												<?php endif; ?>
											<?php endforeach ?>
										</select>

										<div class="page-preview">
											<img src="<?php echo BASEL_DUMMY; ?>/base/preview.jpg" data-dir="<?php echo BASEL_DUMMY; ?>" alt="" />
										</div>

										<p class="description">
											<?php _e( 'Choose version from the dropdown and click import to set up version content, images, sliders and options', 'basel' ) ?>
										</p>

										<p>	
											<label for="import_attachments">
												<input name="import_attachments" type="checkbox" id="import_attachments" value="1" checked="checked">
												Download and import file attachments
											</label>
										</p>

										<?php if ( ! $this->_required_plugins() ): ?>
											<p class="submit">
												<input type="submit" name="basel-submit" id="basel-submit" class="button button-primary" value="Import data">
											</p>
										<?php endif ?>

										<div class="basel-import-progress animated" data-progress="0">
											<div style="width: 0;"></div>
										</div>

									</td>
								</tr>
							</table>

						</form>

					</div>
				</div>
			</div>
		<?php
	}

	private function _need_process( $process ) {
		$result = false;

		if( in_array($process, $this->_process) ) {
			$result = true;
		}

		return $result;
	}

	public function import_action() {



		if( ! empty( $_GET['basel_version'] ) ) {

			$this->_version = sanitize_text_field( $_GET['basel_version'] );


			if( ! $this->_is_valid_version_slug( $this->_version ) ) {

				$this->_send_fail_msg( 'Wrong version name' );

			}

			if( ! $this->_able_to_import( $this->_version ) ) {

				$this->_send_fail_msg( 'You can\'t import this version one more time' );

			}
			
			$this->_process = explode(',', $this->_basel_versions[$this->_version]['process']);

			// Load importers API
			$this->_load_importers();

			// Import xml file
			
			if ( $this->_need_process('xml') ) {
				$this->_import_xml();
			}

			//  Set up home page
			if ( $this->_need_process('home') ) {
				$this->_set_up_pages();
			}

			//  Set up shop page 
			if ( $this->_need_process('shop') ) {
				$this->_set_up_shop_page();
			}

			//  Set up menu 
			if ( $this->_need_process('menu') ) {
				$this->_set_up_menu();
			}

			// Add page to menu
			if ( $this->_need_process('page_menu') ) {
				$this->_add_page_menu();
			}

			//  Set up widgets
			if ( $this->_need_process('widgets') ) {
				$this->_set_up_widgets();
			}

			// Import sliders 
			if ( $this->_need_process('sliders') ) {
				$this->_import_sliders();
			}

			// Import options
			if ( $this->_need_process('options') ) {
				$this->_import_options();
			}

			if( $this->_version == 'base') {
				add_option( 'basel_version_imported_' . $this->_version, 1 );
			}

		} else {

			$this->_send_fail_msg( 'Wrong version name' );

		}

		$this->_send_response();

	}

	public function _able_to_import( $ver ) {
		return true;//! ( get_option( 'basel_version_imported_' . $ver ) == 1 );
	}

	public function sizes_array( $sizes ) {
		return array();
	}

	private function _import_xml() {

		$file = $this->_get_file_to_import( 'content.xml' );
		
		// Check if XML file exists
		if( ! $file ) {
			$this->_send_fail_msg( "File doesn't exist <strong>" . $this->_version . "/content.xml</strong>");
		} 

		try{

	    	ob_start();

	    	// Prevent generating of thumbnails for 8 sizes. Only original
	    	add_filter( 'intermediate_image_sizes', array( $this, 'sizes_array') );

			$this->_importer->fetch_attachments = $this->_import_attachments;

			// Run WP Importer for XML file
			$this->_importer->import( $file );

			$output = ob_get_contents();

			ob_end_clean();
			
			$this->_add_msg( $output );

			
		} catch (Exception $e) {
			$this->_send_fail_msg("Error while importing");
		}
	}

	private function _set_up_shop_page() {

		$shopPage = get_page_by_title('Shop');

		$this->_shop_page_id = $shopPage->ID;

		$shop_metas = array(
			'_basel_page-title-size' => 'small',
		);

		foreach ($shop_metas as $key => $value) {
			update_post_meta($this->_shop_page_id, $key, $value);
		}


	}

	private function _set_up_pages() {

		$home_page_title = 'Home ' . $this->_version;
		$home_page = get_page_by_title( $home_page_title );
		if( ! is_null( $home_page )) {

			update_option( 'page_on_front', $home_page->ID );
			update_option( 'show_on_front', 'page' );

			$this->_add_msg( 'Front page set to <strong>"' . $home_page_title . '"</strong>' );
		} else {
			$this->_add_msg( 'Front page is not changed' );
		}


		if( $this->_version == 'base') {
			$blog_page_title = 'Blog';
			$blog_page = get_page_by_title( $blog_page_title );
			if( ! is_null( $blog_page ) ) {
				update_option( 'page_for_posts', $blog_page->ID );
				update_option( 'show_on_front', 'page' );
				$this->_add_msg( 'Blog page set to <strong>"' . $blog_page_title . '"</strong>' );
			} else {
				$this->_add_msg( 'Blog page is not changed' );
			}

			// Move Hello World post to trash
			 wp_trash_post( 1 );
			 
			// Move Sample Page to trash
			 wp_trash_post( 2 );
		}


	}

	private function _set_up_menu() {
		global $wpdb, $basel_options;
		//$this->_add_msg( 'Run - "_set_up_menu"' );
		
		$location 		= 'main-menu';
		$mobilelocation = 'mobile-menu';
		
		$tablename = $wpdb->prefix.'terms';
		$menu_ids = $wpdb->get_results(
		    "
		    SELECT term_id
		    FROM ".$tablename." 
		    WHERE name IN ('Main navigation', 'Categories')
		    ORDER BY name ASC
		    "
		);

		if( count( $menu_ids ) == 2) {
			$categories_menu_id = $menu_ids[0]->term_id;
			$menu_id = $menu_ids[1]->term_id;
		}

		if( ! empty( $categories_menu_id ) ) {
			$basel_options['categories-menu'] = $categories_menu_id;
		}
		
		foreach($menu_ids as $menu) {
			$menu_id = $menu->term_id;
		}
		    
		$itemData =  array(
			'menu-item-object-id'	=> $this->_shop_page_id,
			'menu-item-parent-id'	=> 0,
			'menu-item-position'  	=> 2,
			'menu-item-object' 		=> 'page',
			'menu-item-type'      	=> 'post_type',
			'menu-item-status'    	=> 'publish'
		);

		wp_update_nav_menu_item($menu_id, 0, $itemData);
	    if( ! has_nav_menu( $location ) ){
	        $locations = get_theme_mod('nav_menu_locations');
	        $locations[$location] 		= $menu_id;
	        $locations[$mobilelocation] = $menu_id;
	        set_theme_mod( 'nav_menu_locations', $locations );
	    }

	}

	private function _add_page_menu() {
		global $wpdb;
		
		$page_title = $this->_basel_versions[$this->_version]['title'];

		$page = get_page_by_title( $page_title );

		if( is_null( $page ) ) return;

		$tablename = $wpdb->prefix.'terms';
		$menu_ids = $wpdb->get_results(
		    "
		    SELECT term_id
		    FROM ".$tablename." 
		    WHERE name IN ('Main navigation')
		    ORDER BY name ASC
		    "
		);
		
		foreach($menu_ids as $menu) {
			$menu_id = $menu->term_id;
		}


		$menu_item = array_filter(wp_get_nav_menu_items($menu_id), function( $item ) use($page_title) {
			return $item->title == $page_title;
		});

		if( ! empty($menu_item) ) return;

		wp_update_nav_menu_item($menu_id, 0,  array(
		    'menu-item-title' => $page_title,
		    'menu-item-object' => 'page',
		    #'menu-item-parent-id' => $new_menu_obj[ $nav_item['parent'] ]['id'],
		    'menu-item-object-id' => $page->ID,
		    'menu-item-type' => 'post_type',
		    'menu-item-status' => 'publish'
	    ) );
	}

	private function _set_up_widgets() {

		$widgets = basel_get_config( 'widgets-import' );

		$version_widgets_file = $this->_get_file_to_import( 'widgets.json' );

		if( $version_widgets_file ) {
			$version_widgets = json_decode( $this->_get_local_file_content( $version_widgets_file ), true );
			$widgets = wp_parse_args( $version_widgets, $widgets ); 
		}

	    // We don't want to undo user changes, so we look for changes first.
	    $this->_active_widgets = get_option( 'sidebars_widgets' );

		$this->_widgets_counter = 1;

	    foreach ($widgets as $area => $params) {
		    if ( ! empty ( $this->_active_widgets[$area] ) && $params['flush'] ) {
		    	$this->_flush_widget_area($area);
	    	} else if(! empty ( $this->_active_widgets[$area] ) && ! $params['flush'] ) {
	    		continue;
	    	}
	    	foreach ($params['widgets'] as $widget => $args) {
			    $this->_add_widget($area, $widget, $args);
	    	}
	    }

	    // Now save the $active_widgets array.
	    update_option( 'sidebars_widgets', $this->_active_widgets );

		$this->_add_msg( 'Widgets updated' );

	}

	private function _add_widget( $sidebar, $widget, $options = array() ) {

		$this->_active_widgets[ $sidebar ][] = $widget . '-' . $this->_widgets_counter;

	    $widget_content = get_option( 'widget_' . $widget );

	    $widget_content[ $this->_widgets_counter ] = $options;

	    update_option(  'widget_' . $widget, $widget_content );

		$this->_widgets_counter++;
	}

	private function _flush_widget_area( $area ) {

		unset($this->_active_widgets[ $area ]);

	}


	private function _import_sliders() {
		if( ! class_exists('RevSlider') ) return;
		$this->_revolution_import( 'revslider.zip' );
		$this->_revolution_import( 'revslider2.zip' );
	}

	private function _revolution_import( $filename ) {
		$file = $this->_get_file_to_import( $filename );
		if( ! $file ) return;
		$revapi = new RevSlider();
		ob_start();
		$slider_result = $revapi->importSliderFromPost(true, true, $file);
		ob_end_clean();
	}

	private function _get_file_to_import( $filename ) {

		$file = $this->_file_path . $this->_version . '/' . $filename;

		// Check if ZIP file exists
		if( ! file_exists( $file ) ) {
			return false;
		} 

		return $file;
	}

	private function _import_options() {
		global $basel_options;

		$file = $this->_get_file_to_import( 'options.json' );

		if( ! $file ) return;

		try{

			if( class_exists('ReduxFrameworkInstances') ) {
				
				$new_options = json_decode( $this->_get_local_file_content( $file ), true );

				if( ! empty( $basel_options['categories-menu'] ) ) {
					$new_options['categories-menu'] = $basel_options['categories-menu'];
				}

				$new_options = wp_parse_args( $new_options, $basel_options ); 

				$redux = ReduxFrameworkInstances::get_instance( 'basel_options' );

	            if ( isset ( $redux->validation_ran ) ) {
	                unset ( $redux->validation_ran );
	            }

	            $redux->set_options( $redux->_validate_options( $new_options ) );
			}

			
		} catch (Exception $e) {
			$this->_send_fail_msg("Error while importing options");
		}
		$this->_add_msg( 'Options updated' );
	}

	private function _get_local_file_content( $file ) {
		ob_start();
		include $file;
		$file_content = ob_get_contents();
		ob_end_clean();
		return $file_content;
	}
 
	private function _load_importers() {

		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if( ! function_exists( 'BASEL_Theme_Plugin' ) ) {

			$this->_send_fail_msg( 'Please install theme core plugin' );

		}

		$this->_import_attachments = ( ! empty($_GET['import_attachments']) );

		$importerError = false;

		//check if wp_importer, the base importer class is available, otherwise include it
		if ( !class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) 
				require_once($class_wp_importer);
			else 
				$importerError = true;
		}

		$plugin_dir = BASEL_Theme_Plugin()->plugin_path();

		$path = apply_filters('basel_require', $plugin_dir . '/importer/wordpress-importer.php');

		if( file_exists( $path ) ) {
			require_once $path;
		} else {
			$this->_send_fail_msg( 'wordpress-importer.php file doesn\'t exist' );
		}

		if($importerError !== false) {
			$this->_send_fail_msg( "The Auto importing script could not be loaded. Please use the wordpress importer and import the XML file that is located in your themes folder manually." );
		} 

		if(class_exists('WP_Importer') && class_exists('WP_Import')){
			
			$this->_importer = new WP_Import();

		} else {

			$this->_send_fail_msg( 'Can\'t find WP_Importer or WP_Import class' );

		}

	}

	private function _required_plugins() {
		$plugins = array();

		if( ! class_exists('Redux') ) {
			$plugins[] = 'Redux Framework';
		}

		if( ! class_exists('CMB2') ) {
			$plugins[] = 'CMB2';
		}

		if( ! class_exists('RevSlider') ) {
			$plugins[] = 'Revolution Slider';
		}

		if( ! class_exists('BASEL_Post_Types') ) {
			$plugins[] = 'Theme Post Types';
		}

		if( ! empty( $plugins ) ) {
			return $plugins;
		}

		return false;
	}

	private function _send_response( $array = array() ) {

		if( empty( $array ) && ! empty( $this->_response ) ) 
			echo json_encode( $this->_response );
		elseif( ! empty( $array ) ) 
			echo json_encode( $array );
		else 
			echo json_encode( array( 'message' => 'empty response') );

		die();
	}

	private function _add_msg( $msg ) {
		$this->_response['status'] = 'success';
		$this->_response['message'] .= $msg . '<br>';
	}

	private function _send_success_msg( $msg ) {

		$this->_send_msg( 'success', $msg );

	}

	private function _send_fail_msg( $msg ) {

		$this->_send_msg( 'fail', $msg );

	}

	private function _send_msg( $status, $message ) {
		$this->_response = array(
			'status' => $status,
			'message' => $message
		);

		$this->_send_response();
	}

	private function _is_valid_version_slug( $ver ) {
		if( in_array($ver, array_keys( $this->_basel_versions ) )) return true;
		return false;
	}

}
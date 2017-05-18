<?php
/**
 * For full documentation, please visit: https://github.com/ReduxFramework/ReduxFramework/wiki
 * */

if ( !defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( !class_exists( 'ZanMbReduxFrameworkConfig' ) ) {

	class ZanMbReduxFrameworkConfig
	{

		public $args     = array();
		public $sections = array();
		public $theme;
		public $ReduxFramework;

		public function __construct() {

			if ( !class_exists( 'ReduxFramework' ) ) {
				return;
			}

			$this->initSettings();
		}

		public function initSettings() {

			// Just for demo purposes. Not needed per say.
			$this->theme = wp_get_theme();

			// Set the default arguments
			$this->setArguments();

			// Set a few help tabs so you can see how it's done
			$this->setHelpTabs();

			// Create the sections and fields
			$this->setSections();

			if ( !isset( $this->args['opt_name'] ) ) { // No errors please
				return;
			}

			// If Redux is running as a plugin, this will remove the demo notice and links
			//add_action( 'redux/loaded', array( $this, 'remove_demo' ) );

			// Function to test the compiler hook and demo CSS output.
			//add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2);
			// Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
			// Change the arguments after they've been declared, but before the panel is created
			//add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
			// Change the default value of a field after it's been set, but before it's been useds
			//add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
			// Dynamically add a section. Can be also used to modify sections/fields
			add_filter( 'redux/options/' . $this->args['opt_name'] . '/sections', array( $this, 'dynamic_section' ) );

			$this->ReduxFramework = new ReduxFramework( $this->sections, $this->args );
		}

		/**
		 *
		 * This is a test function that will let you see when the compiler hook occurs.
		 * It only runs if a field   set with compiler=>true is changed.
		 * */
		function compiler_action( $options, $css ) {

		}

		function ts_redux_update_options_user_can_register( $options, $css ) {
			global $zanmb;
			$users_can_register = isset( $zanmb['opt-users-can-register'] ) ? $zanmb['opt-users-can-register'] : 0;
			update_option( 'users_can_register', $users_can_register );
		}

		/**
		 *
		 * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
		 * Simply include this function in the child themes functions.php file.
		 *
		 * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
		 * so you must use get_template_directory_uri() if you want to use any of the built in icons
		 * */
		function dynamic_section( $sections ) {
			//$sections = array();
			$sections[] = array(
				'title'  => esc_html__( 'Section via hook', 'zanmb' ),
				'desc'   => wp_kses( __( '<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'zanmb' ), array( 'p' => array( 'class' => array() ) ) ),
				'icon'   => 'el-icon-paper-clip',
				// Leave this as a blank section, no options just some intro text set above.
				'fields' => array(),
			);

			return $sections;
		}

		/**
		 *
		 * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
		 * */
		function change_arguments( $args ) {
			//$args['dev_mode'] = true;

			return $args;
		}

		/**
		 *
		 * Filter hook for filtering the default value of any given field. Very useful in development mode.
		 * */
		function change_defaults( $defaults ) {
			$defaults['str_replace'] = "Testing filter hook!";

			return $defaults;
		}

		// Remove the demo link and the notice of integrated demo from the redux-framework plugin
		function remove_demo() {

			// Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
			if ( class_exists( 'ReduxFrameworkPlugin' ) ) {
				remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::instance(), 'plugin_metalinks' ), null, 2 );

				// Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
				remove_action( 'admin_notices', array( ReduxFrameworkPlugin::instance(), 'admin_notices' ) );

			}
		}

		public function setSections() {

			/**
			 * Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
			 * */
			// Background Patterns Reader
			$sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
			$sample_patterns_url = ReduxFramework::$_url . '../sample/patterns/';
			$sample_patterns = array();

			if ( is_dir( $sample_patterns_path ) ) :

				if ( $sample_patterns_dir = opendir( $sample_patterns_path ) ) :
					$sample_patterns = array();

					while ( ( $sample_patterns_file = readdir( $sample_patterns_dir ) ) !== false ) {

						if ( stristr( $sample_patterns_file, '.png' ) !== false || stristr( $sample_patterns_file, '.jpg' ) !== false ) {
							$name = explode( ".", $sample_patterns_file );
							$name = str_replace( '.' . end( $name ), '', $sample_patterns_file );
							$sample_patterns[] = array( 'alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file );
						}
					}
				endif;
			endif;

			ob_start();

			$ct = wp_get_theme();
			$this->theme = $ct;
			$item_name = $this->theme->get( 'Name' );
			$tags = $this->theme->Tags;
			$screenshot = $this->theme->get_screenshot();
			$class = $screenshot ? 'has-screenshot' : '';

			$customize_title = sprintf( __( 'Customize &#8220;%s&#8221;', 'zanmb' ), $this->theme->display( 'Name' ) );
			?>
			<div id="current-theme" class="<?php echo esc_attr( $class ); ?>">
				<?php if ( $screenshot ) : ?>
					<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
						<a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize"
						   title="<?php echo esc_attr( $customize_title ); ?>">
							<img src="<?php echo esc_url( $screenshot ); ?>"
							     alt="<?php esc_attr_e( 'Current theme preview', 'zanmb' ); ?>"/>
						</a>
					<?php endif; ?>
					<img class="hide-if-customize" src="<?php echo esc_url( $screenshot ); ?>"
					     alt="<?php esc_attr_e( 'Current theme preview', 'zanmb' ); ?>"/>
				<?php endif; ?>

				<h4>
					<?php echo sanitize_text_field( $this->theme->display( 'Name' ) ); ?>
				</h4>

				<div>
					<ul class="theme-info">
						<li><?php printf( __( 'By %s', 'zanmb' ), $this->theme->display( 'Author' ) ); ?></li>
						<li><?php printf( __( 'Version %s', 'zanmb' ), $this->theme->display( 'Version' ) ); ?></li>
						<li><?php echo '<strong>' . esc_html__( 'Tags', 'zanmb' ) . ':</strong> '; ?><?php printf( $this->theme->display( 'Tags' ) ); ?></li>
					</ul>
					<p class="theme-description"><?php echo esc_attr( $this->theme->display( 'Description' ) ); ?></p>
					<?php
					if ( $this->theme->parent() ) {
						printf(
							' <p class="howto">' . wp_kses( __( 'This <a href="%1$s">child theme</a> requires its parent theme, %2$s.', 'zanmb' ), array( 'a' => array( 'href' => array() ) ) ) . '</p>', esc_html__( 'http://codex.wordpress.org/Child_Themes', 'zanmb' ), $this->theme->parent()
							                                                                                                                                                                                                                                                            ->display( 'Name' )
						);
					}
					?>

				</div>

			</div>

			<?php
			$item_info = ob_get_contents();

			ob_end_clean();

			$sampleHTML = '';

			$general_settings_fields = array(
				'general_introduction'     => array(
					'id'    => 'general_introduction',
					'type'  => 'info',
					'style' => 'success',
					'title' => esc_html__( 'Welcome to WordPress Mega Menu options panel', 'zanmb' ),
					'icon'  => 'el-icon-info-sign',
					'desc'  => esc_html__( 'From here you can config WordPress Mega Menu in the way you need.', 'zanmb' ),
				),
				'enable_on_menu_locations' => array(
					'id'      => 'enable_on_menu_locations',
					'type'    => 'select',
					'title'   => esc_html__( 'Enable Mega Menu On Locations', 'zanmb' ),
					'multi'   => true,
					// Must provide key => value pairs for select options
					'data'    => 'menu_locations',
					'default' => ''
				),
			);

			$general_settings_fields['general_introduction'] = apply_filters( 'zanmb_setting_field_general_introduction', $general_settings_fields['general_introduction'] );
			$general_settings_fields['enable_on_menu_locations'] = apply_filters( 'zanmb_setting_field_enable_on_menu_locations', $general_settings_fields['enable_on_menu_locations'] );


			global $_wp_registered_nav_menus;
			if ( isset( $_wp_registered_nav_menus ) ) {
				if ( !empty( $_wp_registered_nav_menus ) ) {
					foreach ( $_wp_registered_nav_menus as $location => $menu_name ) {
						$general_settings_fields[] = array(
							'id'       => 'divider_' . $location,
							'type'     => 'divide',
							'required' => array( 'enable_on_menu_locations', '=', array( $location ) ),
						);
						$general_settings_fields[] = array(
							'id'       => $location . '_break_point',
							'type'     => 'text',
							'title'    => sprintf( esc_html__( '"%s" Break Point', 'zanmb' ), esc_html( $menu_name ) ),
							'default'  => 767,
							'validate' => 'numeric',
							'required' => array( 'enable_on_menu_locations', '=', array( $location ) ),
						);
						$general_settings_fields[] = array(
							'id'       => $location . '_enable_sticky',
							'type'     => 'switch',
							'title'    => sprintf( esc_html__( 'Enable Sticky For "%s"', 'zanmb' ), esc_html( $menu_name ) ),
							'default'  => 0,
							'on'       => esc_html__( 'Enable', 'zanmb' ),
							'off'      => esc_html__( 'Disable', 'zanmb' ),
							'required' => array( 'enable_on_menu_locations', '=', array( $location ) ),
						);
					}
				}
			}

			$general_settings_fields[] = array(
				'id'      => 'enable_vertical_menu_for_locations',
				'type'    => 'select',
				'title'   => esc_html__( 'Enable Vertical Menu Style For Locations', 'zanmb' ),
				'desc'    => esc_html__( 'Note: You need enable mega menu on these locations also.', 'zanmb' ),
				'multi'   => true,
				// Must provide key => value pairs for select options
				'data'    => 'menu_locations',
				'default' => ''
			);

			if ( isset( $_wp_registered_nav_menus ) ) {
				if ( !empty( $_wp_registered_nav_menus ) ) {
					foreach ( $_wp_registered_nav_menus as $location => $menu_name ) {
						$general_settings_fields[] = array(
							'id'       => 'divider_vertical_' . $location,
							'type'     => 'divide',
							'required' => array( 'enable_vertical_menu_for_locations', '=', array( $location ) ),
						);
						$general_settings_fields[] = array(
							'id'       => $location . '_vertical_base_width_type',
							'type'     => 'select',
							'title'    => sprintf( esc_html__( '"%s" Base Width Type (Vertical)', 'zanmb' ), esc_html( $menu_name ) ),
							'multi'    => false,
							// Must provide key => value pairs for select options
							'options'  => array(
								'closest' => esc_html__( 'Closest Selector', 'zanmb' ),
								'number'  => esc_html__( 'Number', 'zanmb' ),
							),
							'default'  => 'closest',
							'required' => array( 'enable_vertical_menu_for_locations', '=', array( $location ) ),
						);
						$general_settings_fields[] = array(
							'id'       => $location . '_vertical_lv0_width',
							'type'     => 'text',
							'title'    => sprintf( esc_html__( '"%s" Level 0 Width', 'zanmb' ), esc_html( $menu_name ) ),
							'desc'     => esc_html__( 'Menu level 0 width. It should be smaller than base width. Defautl 200 (unit is pixel)', 'zanmb' ),
							'default'  => 200,
							'validate' => 'number',
							'required' => array( 'enable_vertical_menu_for_locations', '=', array( $location ) ),
						);
						$general_settings_fields[] = array(
							'id'       => $location . '_vertical_base_width_closest',
							'type'     => 'text',
							'title'    => sprintf( esc_html__( '"%s" Base Width Closest Selector (Vertical)', 'zanmb' ), esc_html( $menu_name ) ),
							'desc'     => esc_html__( 'The closest selector to calculate sub menu width', 'zanmb' ),
							'default'  => '.page',
							'validate' => 'no_html',
							'required' => array( $location . '_vertical_base_width_type', '=', array( 'closest' ) ),
						);
						$general_settings_fields[] = array(
							'id'       => $location . '_vertical_base_width',
							'type'     => 'text',
							'title'    => sprintf( esc_html__( '"%s" Base Width (Vertical)', 'zanmb' ), esc_html( $menu_name ) ),
							'default'  => 1170,
							'validate' => 'numeric',
							'required' => array( $location . '_vertical_base_width_type', '=', array( 'number' ) ),
						);
					}
				}
			}

			$general_settings_fields[] = array(
				'id'       => 'custom_css_code',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'Custom CSS', 'zanmb' ),
				'subtitle' => esc_html__( 'Paste your custom CSS code here.', 'zanmb' ),
				'mode'     => 'css',
				'theme'    => 'monokai',
				'desc'     => esc_html__( 'Custom css code.', 'zanmb' ),
				'default'  => '',
			);

			$general_settings_fields[] = array(
				'id'       => 'custom_js_code',
				'type'     => 'ace_editor',
				'title'    => esc_html__( 'Custom JS', 'zanmb' ),
				'subtitle' => esc_html__( 'Paste your custom JS code here.', 'zanmb' ),
				'mode'     => 'javascript',
				'theme'    => 'chrome',
				'desc'     => esc_html__( 'Custom javascript code', 'zanmb' ),
				//'default' => "jQuery(document).ready(function(){\n\n});"
			);

			/*-- General Settings--*/
			$this->sections[] = array(
				'icon'   => 'el-icon-cogs',
				'title'  => esc_html__( 'General Settings', 'zanmb' ),
				'fields' => $general_settings_fields
			);

			$logo_settings_fields = array(
				array(
					'id'      => 'show_logo_on_menu_locations',
					'type'    => 'select',
					'title'   => esc_html__( 'Show Logo On Locations', 'zanmb' ),
					'multi'   => true,
					// Must provide key => value pairs for select options
					'data'    => 'menu_locations',
					'default' => ''
				),
				array(
					'id'       => 'logo',
					'type'     => 'media',
					'url'      => true,
					'title'    => esc_html__( 'Logo', 'zanmb' ),
					'compiler' => 'true',
					//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
					'desc'     => esc_html__( 'Upload your logo image', 'zanmb' ),
					'subtitle' => esc_html__( 'Upload your custom logo image', 'zanmb' ),
					'default'  => array( 'url' => ZMB_BASE_URL . 'assets/images/logo.png' ),
				),
				array(
					'id'       => 'logo_size',
					'type'     => 'text',
					'title'    => esc_html__( 'Logo Size', 'zanmb' ),
					'desc'     => wp_kses( __( 'Format {width}x{height}. Default <strong>32x32</strong>.', 'zanmb' ), array( 'strong' => array(), 'b' => array() ) ),
					'default'  => '32x32',
					'validate' => 'no_html',
					'required' => array( 'logo', '!=', '' ),
				),
				array(
					'id'       => 'logo_sticky',
					'type'     => 'media',
					'url'      => true,
					'title'    => esc_html__( 'Logo Sticky', 'zanmb' ),
					'subtitle' => esc_html__( 'Logo on sticky menu', 'zanmb' ),
					'compiler' => 'true',
					//'mode' => false, // Can be set to false to allow any media type, or can also be set to any mime type.
					'desc'     => esc_html__( 'Upload your logo image', 'zanmb' ),
					'default'  => array( 'url' => ZMB_BASE_URL . 'assets/images/logo.png' ),
				),
				array(
					'id'       => 'logo_sticky_size',
					'type'     => 'text',
					'title'    => esc_html__( 'Logo Sticky Size', 'zanmb' ),
					'desc'     => wp_kses( __( 'Format {width}x{height}. Default <strong>32x32</strong>.', 'zanmb' ), array( 'strong' => array(), 'b' => array() ) ),
					'default'  => '32x32',
					'validate' => 'no_html',
					'required' => array( 'logo_sticky', '!=', '' ),
				),
			);

			/*-- Logo Settings--*/
			$this->sections[] = array(
				'icon'   => 'el-icon-photo',
				'title'  => esc_html__( 'Logo Settings', 'zanmb' ),
				'fields' => $logo_settings_fields
			);

			/*-- Sliding Menu --*/

			$sliding_fields = array(
				array(
					'id'      => 'enable_sliding_menu',
					'type'    => 'switch',
					'title'   => esc_html__( 'Enable Sliding Menu', 'zanmb' ),
					'on'      => esc_html__( 'Yes', 'zanmb' ),
					'off'     => esc_html__( 'No', 'zanmb' ),
					'default' => 0,
				),
				array(
					'id'       => 'sliding_menu_content_id',
					'type'     => 'select',
					'title'    => esc_html__( 'Choose Sliding Menu Content', 'zanmb' ),
					'data'     => 'post',
					'args'     => array(
						'post_type' => 'zanmenu',
						'showposts' => -1
					),
					'required' => array( 'enable_sliding_menu', '=', 1 ),
				),
				array(
					'id'       => 'sliding_width',
					'type'     => 'text',
					'title'    => esc_html__( 'Sliding Width', 'zanmb' ),
					'default'  => 350,
					'validate' => 'numeric',
					'required' => array( 'enable_sliding_menu', '=', 1 ),
				),
				array(
					'id'       => 'sliding_btn_target',
					'type'     => 'text',
					'title'    => esc_html__( 'Button Target', 'zanmb' ),
					'default'  => '.zmb-show-sliding',
					'validate' => 'no_html',
					'desc'     => esc_html__( 'Button target selector to show sliding content on click. Ex: .zmb-show-sliding', 'zanmb' ),
					'required' => array( 'enable_sliding_menu', '=', 1 ),
				),
				array(
					'id'       => 'show_sliding_on',
					'type'     => 'text',
					'title'    => esc_html__( 'Show Sliding Content On', 'zanmb' ),
					//'default'  => '',
					'validate' => 'no_html',
					'desc'     => esc_html__( 'Additional event will fire sliding menu. Use comma for multi event. Ex: added_to_cart', 'zanmb' ),
					'required' => array( 'enable_sliding_menu', '=', 1 ),
				),
				array(
					'id'                => 'sliding_content_bg',
					'type'              => 'background',
					'title'             => esc_html__( 'Sliding Content Background', 'zanmb' ),
					'subtitle'          => esc_html__( 'Change/Remove background of sliding content', 'zanmb' ),
					'background-clip'   => false,
					'background-origin' => false,
					'background-size'   => false,
					//							'default'           => array(
					//								'background-color' => '#ffffff',
					//							),
					'output'            => array( '.zmb-sliding-content-wrap' ),
				),
			);

			$sliding_fields = apply_filters( 'zmb_sliding_fields', $sliding_fields );

			$this->sections[] = array(
				'icon'   => 'el-icon-list',
				'title'  => esc_html__( 'Sliding Menu Settings', 'zanmb' ),
				'fields' => $sliding_fields
			);

			/*-- Color Settings--*/
			$this->sections[] = array(
				'icon'   => 'el-icon-magic',
				'title'  => esc_html__( 'Color Settings', 'zanmb' ),
				'fields' => array(
					array(
						'id'       => 'base_color',
						'type'     => 'color_rgba',
						'title'    => esc_html__( 'Base Color', 'zanmb' ),
						'subtitle' => esc_html__( 'Base color for all pages on the frontend', 'zanmb' ),
						'default'  => array(
							'color' => '#49c3df',
							'alpha' => 1,
						),
						'options'  => array(
							'show_input'             => true,
							'show_initial'           => true,
							'show_alpha'             => false,
							'show_palette'           => true,
							'show_palette_only'      => false,
							'show_selection_palette' => true,
							'max_palette_size'       => 10,
							'allow_empty'            => true,
							'clickout_fires_change'  => false,
							'choose_text'            => esc_html__( 'Choose', 'zanmb' ),
							'cancel_text'            => esc_html__( 'Cancel', 'zanmb' ),
							'show_buttons'           => true,
							'use_extended_classes'   => true,
							'palette'                => array(
								'#49c3df', '#eec15b', '#bda47d',
							),
							'input_text'             => esc_html__( 'Select Color', 'zanmb' ),
						),
					),
				)
			);

			/*-- Resource Settings--*/
			$this->sections[] = array(
				'icon'   => 'el-icon-hdd',
				'title'  => esc_html__( 'Resource Settings', 'zanmb' ),
				'fields' => array(
					array(
						'id'      => 'load_simple_line_font_icons',
						'type'    => 'switch',
						'title'   => esc_html__( 'Load Simple Line Font Icons', 'zanmb' ),
						'on'      => esc_html__( 'Yes', 'zanmb' ),
						'off'     => esc_html__( 'No', 'zanmb' ),
						'default' => 1,
					),
					array(
						'id'      => 'load_awesome_font_icon',
						'type'    => 'switch',
						'title'   => esc_html__( 'Load Awesome Font Icons', 'zanmb' ),
						'on'      => esc_html__( 'Yes', 'zanmb' ),
						'off'     => esc_html__( 'No', 'zanmb' ),
						'default' => 1,
					)
				)
			);

			/*-- Compatibility Settings --*/
			$this->sections[] = array(
				'icon'   => 'el-icon-view-mode',
				'title'  => esc_html__( 'Compatibility Settings', 'zanmb' ),
				'fields' => array(
					array(
						'id'       => 'apply_filter_the_content',
						'type'     => 'switch',
						'title'    => esc_html__( 'Use Apply Filter The Content', 'zanmb' ),
						'subtitle' => esc_html__( 'If you have problem with menu item content, try to turn this option to "No"', 'zanmb' ),
						'on'       => esc_html__( 'Yes', 'zanmb' ),
						'off'      => esc_html__( 'No', 'zanmb' ),
						'default'  => 1,
					),
				)
			);
		}

		public function setHelpTabs() {

			// Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
			$this->args['help_tabs'][] = array(
				'id'      => 'redux-opts-1',
				'title'   => esc_html__( 'Theme Information 1', 'zanmb' ),
				'content' => wp_kses( __( '<p>This is the tab content, HTML is allowed.</p>', 'zanmb' ), array( 'p' ) ),
			);

			$this->args['help_tabs'][] = array(
				'id'      => 'redux-opts-2',
				'title'   => esc_html__( 'Theme Information 2', 'zanmb' ),
				'content' => wp_kses( __( '<p>This is the tab content, HTML is allowed.</p>', 'zanmb' ), array( 'p' ) ),
			);

			// Set the help sidebar
			$this->args['help_sidebar'] = wp_kses( __( '<p>This is the tab content, HTML is allowed.</p>', 'zanmb' ), array( 'p' ) );
		}

		/**
		 *
		 * All the possible arguments for Redux.
		 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
		 * */
		public function setArguments() {

			$theme = wp_get_theme(); // For use with some settings. Not necessary.

			$this->args = array(
				// TYPICAL -> Change these values as you need/desire
				'opt_name'           => 'zanmb', // This is where your data is stored in the database and also becomes your global variable name.
				'display_name'       => '<span class="zan-plugin-name">' . esc_html__( 'WordPress Mega Menu', 'zanmb' ) . '</span>', // Name that appears at the top of your panel
				'display_version'    => ZMB_VERSION, // Version that appears at the top of your panel
				//'menu_type'          => 'submenu', //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
				'allow_sub_menu'     => false, // Show the sections below the admin menu item or not
				'menu_title'         => esc_html__( 'WP Mega Menu', 'zanmb' ),
				'page_title'         => esc_html__( 'WordPress Mega Menu', 'zanmb' ),
				// You will need to generate a Google API key to use this feature.
				// Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
				'google_api_key'     => '', // Must be defined to add google fonts to the typography module
				//'async_typography'    => true, // Use a asynchronous font on the front end or font string
				//'admin_bar'           => false, // Show the panel pages on the admin bar
				'global_variable'    => 'zanmb', // Set a different name for your global variable other than the opt_name
				'dev_mode'           => false, // Show the time the page took to load, etc
				'customizer'         => false, // Enable basic customizer support
				// OPTIONAL -> Give you extra features
				//'page_priority'      => null, // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
				//'page_parent'        => 'themes.php', // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
				'page_permissions'   => 'manage_options', // Permissions needed to access the options panel.
				'menu_icon'          => '', // Specify a custom URL to an icon
				'last_tab'           => '', // Force your panel to always open to a specific tab (by id)
				'page_icon'          => 'icon-themes', // Icon displayed in the admin panel next to your menu_title
				'page_slug'          => 'zanmb_options', // Page slug used to denote the panel
				'save_defaults'      => true, // On load save the defaults to DB before user clicks save or not
				'default_show'       => false, // If true, shows the default value next to each field that is not the default value.
				'default_mark'       => '', // What to print by the field's title if the value shown is default. Suggested: *
				// CAREFUL -> These options are for advanced use only
				'transient_time'     => 60 * MINUTE_IN_SECONDS,
				'output'             => true, // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
				'output_tag'         => true, // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
				//'domain'              => 'redux-framework', // Translation domain key. Don't change this unless you want to retranslate all of Redux.
				'footer_credit'      => esc_html__( 'Zan Themes WordPress Team', 'zanmb' ), // Disable the footer credit of Redux. Please leave if you can help it.
				// FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
				'database'           => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
				'show_import_export' => true, // REMOVE
				'system_info'        => false, // REMOVE
				'help_tabs'          => array(),
				'help_sidebar'       => '', // esc_html__( '', $this->args['domain'] );
				'hints'              => array(
					'icon'          => 'icon-question-sign',
					'icon_position' => 'right',
					'icon_color'    => 'lightgray',
					'icon_size'     => 'normal',

					'tip_style'    => array(
						'color'   => 'light',
						'shadow'  => true,
						'rounded' => false,
						'style'   => '',
					),
					'tip_position' => array(
						'my' => 'top left',
						'at' => 'bottom right',
					),
					'tip_effect'   => array(
						'show' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'mouseover',
						),
						'hide' => array(
							'effect'   => 'slide',
							'duration' => '500',
							'event'    => 'click mouseleave',
						),
					),
				),
			);

			$this->args['share_icons'][] = array(
				'url'   => 'https://www.facebook.com/thuydungcafe',
				'title' => 'Like us on Facebook',
				'icon'  => 'el-icon-facebook',
			);
			$this->args['share_icons'][] = array(
				'url'   => 'http://twitter.com/',
				'title' => 'Follow us on Twitter',
				'icon'  => 'el-icon-twitter',
			);

			// Panel Intro text -> before the form
			if ( !isset( $this->args['global_variable'] ) || $this->args['global_variable'] !== false ) {
				if ( !empty( $this->args['global_variable'] ) ) {
					$v = $this->args['global_variable'];
				}
				else {
					$v = str_replace( "-", "_", $this->args['opt_name'] );
				}

			}
			else {

			}

		}

	}

	global $ZanMbReduxFrameworkConfig;
	$ZanMbReduxFrameworkConfig = new ZanMbReduxFrameworkConfig();
}


/**
 *
 * Custom function for the callback referenced above
 */
if ( !function_exists( 'redux_my_custom_field' ) ):

	function redux_my_custom_field( $field, $value ) {
		print_r( $field );
		print_r( $value );
	}

endif;

/**
 *
 * Custom function for the callback validation referenced above
 * */
if ( !function_exists( 'redux_validate_callback_function' ) ):

	function redux_validate_callback_function( $field, $value, $existing_value ) {
		$error = false;
		$value = 'just testing';

		$return['value'] = $value;
		if ( $error == true ) {
			$return['error'] = $field;
		}

		return $return;
	}

endif;
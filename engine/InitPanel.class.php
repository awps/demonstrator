<?php 
namespace Demonstrator;

class InitPanel{
	public $id;
	public $title;
	public $settings;

	public function __construct( $id, $title = '', $settings = array() ){
		$this->id       = $id;
		$this->title    = ! empty($title) ? $title : $id;
		$this->settings = $settings;

		add_action('admin_menu', array( $this, 'menu' ), 99 );
		add_filter('dts_settings_panels', array( $this, 'panels' ) );
		add_action( 'admin_init', array( $this, 'panelInit' ) );
		add_action( 'admin_bar_menu', array( $this, 'toolbarLink'), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueue') );
		add_action( 'init', array( $this, 'flushRewriteRules' ) );
	}

	public function adminEnqueue(){
		if( is_admin() && !empty( $_GET[ 'page' ] ) && $_GET[ 'page' ] === $this->id ){

			wp_enqueue_media();

			dmstr()->addStyle( demonstrator_config('id') . '-zero-grid', array(
				'src'     =>dmstr()->assetsURL( 'zero-grid/style.min.css' ),
				'enqueue' => true,
			));
			
			dmstr()->addStyle( demonstrator_config('id') . '-smk-accordion', array(
				'src'     =>dmstr()->assetsURL( 'smk-accordion/css/smk-accordion.css' ),
				'enqueue' => true,
			));
			
			dmstr()->addScript( demonstrator_config('id') . '-smk-accordion', array(
				'src'     => dmstr()->assetsURL( 'smk-accordion/js/smk-accordion.js' ),
				'deps'    => array( 'jquery' ),
				'enqueue' => true,
			));
			
			dmstr()->addStyle( demonstrator_config('id') . '-styles-admin', array(
				'src'     =>dmstr()->assetsURL( 'css/styles-admin.css' ),
				'enqueue' => true,
			));
			

			dmstr()->addScript( demonstrator_config('id') . '-config-admin', array(
				'src'     => dmstr()->assetsURL( 'js/config-admin.js' ),
				'deps'    => array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ),
				'enqueue' => true,
				'zwpocp_presets' => array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'customizer_url' => admin_url( 'customize.php' ),
					'error_save_before_create_preset' => __( 'Please save current settings before you create a new preset!', 'zerowp-oneclick-presets' ),
					'select_screenshot' => __( 'Select screenshot', 'zerowp-oneclick-presets' ),
					'select_preset' => __( 'Select preset', 'zerowp-oneclick-presets' ),
				),
			));

			
		}
	}

	public function toolbarLink( $wp_admin_bar ) {
		$sett = $this->getSettings();

		$args = array(
			'id'    => $this->id,
			'title' => $this->title,
			'href'  => $this->getAdminMenuUrl(),
			'parent' => 'site-name',
		);
		$wp_admin_bar->add_node( $args );
	}

	public function getAdminMenuUrl(){
		$sett = $this->getSettings();
		$url = admin_url( 'admin.php?page=' . $this->id );

		return $url;
	}

	public function getSettings(){
		return wp_parse_args(
			$this->settings,
			array(
				'capability' => 'manage_options',
			)
		);
	}

	public function menu(){
		$sett = $this->getSettings();

		if( ! isset( $sett['parent_slug'] ) ) {
			add_menu_page(
				$this->title,
				$this->title,
				$sett['capability'],
				$this->id,
				array($this, 'panelPage')
			);
		}
		else{
			add_submenu_page(
				$sett['parent_slug'],
				$this->title,
				$this->title,
				$sett['capability'],
				$this->id,
				array($this, 'panelPage')
			);
		}
	}

	/**
	 * Admin panel initialization
	 *
	 * Register setting and init the panel.
	 * Note: this DO NOT render the admin page.
	 *
	 */
	public function panelInit() {
		register_setting( $this->id, $this->id, array($this, 'sanitize') );
	}

	/**
	 * Flush rewrite rules
	 *
	 */
	public function flushRewriteRules() {
		if( is_admin() && !empty( $_GET[ 'page' ] ) && $_GET[ 'page' ] === $this->id && isset($_POST) ){
			flush_rewrite_rules( true );
		}
	}

	/**
	 * Sanitize data
	 *
	 * Sanitize user input before saving it into DB. Uses the sanitize method from each field type.
	 *
	 * @param array $value All fields in array, key pairs. field_id => field_value(user input)
	 * @return array Sanitized fields.
	 */
	public function sanitize( $value ){
		$all_registered_fields = $this->fields();
		$all_registered_field_types = dts_settings_fields_types();
		$new_value = $value;

		if( is_array( $value ) ){
			foreach ($value as $field_id => $field_value) {

				if( array_key_exists($field_id, $all_registered_fields) ){

					// Get field settings
					if( ! empty( $all_registered_fields[ $field_id ] ) ){
						$field_settings = $all_registered_fields[ $field_id ];
					}
					else{
						$new_value[ $field_id ] = $field_value;
						continue;
					}

					// Get field type
					if( !empty($field_settings['field_type']) ){
						$field_type = $field_settings['field_type'];
					}
					else{
						$new_value[ $field_id ] = $field_value;
						continue;
					}

					// Get field class
					if( array_key_exists($field_type, $all_registered_field_types) ){
						$class = $all_registered_field_types[ $field_type ];
					}
					else{
						$new_value[ $field_id ] = $field_value;
						continue;
					}

					// Sanitize
					if( class_exists($class) ){
						$field_class = new $class( $this->id, $field_id, $field_settings );
						$new_value[ $field_id ] = $field_class->sanitize( $field_value );
					}
				}
			}
		}

		return $new_value;
	}

	/**
	 * All registered panels
	 *
	 *All registered panels
	 *
	 * @return array All panels `id => title`
	 */
	public function panels( $panels ){
		if( ! array_key_exists($this->id, $panels) ){
			$panels[ $this->id ] = $this->getSettings();
		}
		return $panels;
	}

	/**
	 * Panel sections
	 *
	 * All active panel sections
	 *
	 * @return array All sections `id => settings`
	 */
	public function sections(){
		return apply_filters( $this->id . '_panel_sections', array(
			'general' => array(
				'title'       => __('General', 'smk_theme'),
				'description' => '',
			)
		) );
	}

	/**
	 * Panel fields
	 *
	 * All active panel fields
	 *
	 * @return array All fields `id => settings`
	 */
	public function fields(){
		return apply_filters( $this->id . '_panel_fields', array() );
	}

	public function panelPage(){
		$this->pageOpen();

		settings_fields( $this->id );

		// Iterate sections
		foreach ($this->sections() as $section_id => $section_settings) {

			// If the section has fields show it otherwise hide.
			if( has_action( $this->id .'_'. $section_id . '_dts_fields' ) ){

				// Section title and description
				$this->sectionTitle( $section_settings['title'] );
				$this->sectionDescription( $section_settings['description'] );

				// Show the fields for this section
				echo '<table class="form-table"><tbody>';
					do_action( $this->id .'_'. $section_id . '_dts_fields' );
				echo '</tbody></table>';
			}
		}
		$this->pageClose();
		// $this->pageDebug();
	}

	public function pageOpen(){
		$output = '<div class="wrap" id="demonstrator-panel-'. $this->id .'">';
		$output .= '<form method="post" action="options.php">';
		$output .= '<h2>'. $this->title .'</h2>';
		$output .= '<div class="zg dts-panel-content">';
		$output .= '<div class="col-md-9">';
		echo $output;
	}

	public function pageClose(){
		$output = submit_button();
		$output .= '</div>';//col-md-
		$output .= '<div class="col-md-3">';
		$output .= $this->rightNotice();
		$output .= $this->rightBlocks();
		$output .= '</div>';//zg
		$output .= '</form>';
		$output .= '</div>';
		echo $output;
	}

	public function rightNotice(){
		return '
			<div class="right-notice">
				<img src="'. dmstr()->assetsURL() .'img/happy.png" />
				<h3>Support the development</h3>
				<p>
				I invested a considerable amount of time in this product. 
				And I still have a lot of work to do on it<em><small>(<a target="_blank" href="https://github.com/ZeroWP/demonstrator#todo">TODO list</a>)</small></em>.<br> 
				Consider making a donation if you find this product useful. <br>
				Don\'t ignore this message. Your donation will make a difference. 
				I would like to improve it as much as I can, but your support is needed.
				</p>
				<a class="right-notice-button" href="https://paypal.me/zerowp" target="_blank">Donate</a>
			</div>
		';
	}

	public function rightBlocks(){
		$output = $this->_rightBlock( 
			__( 'Share with friends', 'demonstrator' ),
			__( 'If you find this plugin useful, then it\'s a big chance that your friends would think the same.', 'demonstrator' ),
			'<div class="zg-lg-2 zg-sm-1 zg-xs-4 zg-1">
				<div><a target="_blank" href="https://twitter.com/intent/tweet?url='. $this->shareUrl() .'&text='. $this->shareSubject() .'" class="social-share twitter"><span class="dashicons dashicons-twitter"></span> Twitter</a></div>
				<div><a target="_blank" href="https://www.facebook.com/sharer.php?s=100&p[title]='. $this->shareTitle() .'&u='. $this->shareUrl() .'&t='. $this->shareTitle() .'&p[summary]='. $this->shareSubject() .'&p[url]='. $this->shareUrl() .'" class="social-share facebook"><span class="dashicons dashicons-facebook-alt"></span> Facebook</a></div>
				<div><a target="_blank" href="https://plus.google.com/share?url='. $this->shareUrl() .'" class="social-share googleplus"><span class="dashicons dashicons-googleplus"></span> Google+</a></div>
				<div><a target="_blank" href="mailto:?subject='. $this->shareTitle() .'&body='. $this->shareSubject() .': '. $this->shareUrl() .'" class="social-share email"><span class="dashicons dashicons-email"></span> Email</a></div>
			</div>'
		);
		$output .= $this->_rightBlock(
			__( 'Support and bugs report:', 'demonstrator' ),
			false,
			'<div>
				<a target="_blank" href="https://github.com/ZeroWP/demonstrator/issues" class="github-link"><img src="'. dmstr()->assetsUrl() .'img/github.png"> Issues tracker</a>
			</div>'
		);

		return $output;
	}

	protected function _rightBlock( $title = false, $description = false, $content = false ){
		$output = '';

		$output .= '<div class="right-block">';
		
		if( !empty( $title ) ){
			$output .= '<h3>'. $title .'</h3>';
		}

		if( !empty( $description ) ){
			$output .= '<p class="description">'. $description .'</p>';
		}

		if( !empty( $content ) ){
			$output .= '<div class="right-block-content">'. $content .'</div>';
		}

		$output .= '</div>';

		return $output;
	}

	public function shareTitle(){
		return esc_attr( '"Demonstrator" WordPress Plugin' );
	}

	public function shareSubject(){
		return esc_attr( 'Hey! Checkout "Demonstrator" plugin for WordPress' );
	}

	public function shareUrl(){
		return esc_url( 'https://wordpress.org/plugins/demonstrator/' );
	}

	public function sectionTitle( $title ){
		echo apply_filters( 'dts_settings_section_title', '<br /><hr /><br /><h3>'. $title .'</h3>', $title );
	}

	public function sectionDescription( $description ){
		if( !empty($description) ){
			echo apply_filters( 'dts_settings_section_description', '<p>'. $description .'</p>', $description );
		}
	}

	public function updateOption( $key, $value ){
		$options = get_option( $this->id );
		$options = ( is_array($options) ) ? $options : array();
		$new_options = wp_parse_args(
			array( $key, $value ),
			$options
		);
		update_option( $this->id, $new_options );
		flush_rewrite_rules();
	}

	public function update(){
		if( !empty($_GET['page']) && $_GET['page'] == $this->id && isset($_GET['update-settings']) ){

		}
	}

	public function pageDebug(){

		// DANGEROUS!!! IT WILL DELETE ALL SAVED SETTINGS
		// delete_option( $this->id );

		if( !empty($_POST) ){
			echo '<pre>';
			print_r( $_POST );
			echo '</pre>';
		}
		echo '<pre>';
			print_r( $this->sections() );
			print_r( $this->fields() );
			print_r( get_option( $this->id ) );
		echo '</pre>';
	}

}

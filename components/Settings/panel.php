<?php
function dts_settings_panels(){
	return apply_filters( 'dts_settings_panels', array() );
}

function dts_settings_fields_types(){
	return apply_filters( 'dts_settings_fields_types', array() );
}

/**
 * Add a section to a settings panel
 *
 * Add a section to a settings panel
 *
 */
class Dts_Settings_Panel_Add_Section{
	public $panel_id;
	public $section_id;
	public $title;
	public $description;

	public function __construct( $panel_id, $section_id, $title = false, $description = '', $priority = 20 ){
		$this->panel_id    = $panel_id;
		$this->section_id  = $section_id;
		$this->title       = $title;
		$this->description = $description;

		add_filter( $this->panel_id . '_panel_sections', array($this, 'add'), $priority );
	}

	public function add( $sections ){
		if( ! array_key_exists($this->section_id, $sections) ){
			$sections[ $this->section_id ] = array(
				'title' => $this->title,
				'description' => $this->description,
			);
		}
		return $sections;
	}
}

/**
 * Add a field to a settings panel
 *
 * Add a field to a settings panel
 *
 */
class Dts_Settings_Panel_Add_Field{
	public $panel_id;
	public $section_id;
	public $field_id;
	public $field_type;
	public $settings;
	public $description;

	public function __construct( $panel_id, $section_id, $field_id, $field_type = 'input', $settings = array(), $priority = 20 ){
		$this->panel_id    = $panel_id;
		$this->section_id  = $section_id;
		$this->field_id    = $field_id;
		$this->field_type  = $field_type;
		$this->settings    = $settings;

		add_filter( $this->panel_id . '_panel_fields', array($this, 'add'), $priority );
		add_action( $this->panel_id .'_'. $section_id . '_dts_fields', array($this, 'render'), $priority );
	}

	public function getSettings(){
		$defaults = array(
			'title' => '',
			'description' => '',
			'default' => '',
		);
		$settings = (array) $this->settings;
		$settings['field_type'] = $this->field_type;
		return wp_parse_args( $settings, $defaults );
	}

	public function add( $fields ){
		if( ! array_key_exists($this->field_id, (array) $fields) ){
			$fields[ $this->field_id ] = $this->getSettings();
		}
		return $fields;
	}

	public function getField(){
		$settings     = $this->getSettings();
		$known_fields = dts_settings_fields_types();
		$type = $settings['field_type'];
		if( array_key_exists($type, $known_fields) ){
			if( isset( $known_fields[ $type ] ) && class_exists( $known_fields[ $type ] ) ){
				$class = $known_fields[ $type ];
				if( method_exists($class, 'render') ){
					$final_obj = new $class( $this->panel_id, $this->field_id, $settings );
					return $final_obj->render();
				}
			}
		}
	}

	public function render(){
		$s = $this->getSettings();
		$full_row  = !empty($s['full_row']) ? 'full-row' : '';
		$description  = !empty($s['description']) ? '<p class="description">'. $s['description'] .'</p>' : '';

		echo '<tr id="'. $this->panel_id .'_'. $this->field_id .'" class="'. $full_row .'">
			<th scope="row">'. $s['title'] .'</th>
			<td>'. $this->getField() . $description .'</td>
		</tr>';
	}
}

//------------------------------------//--------------------------------------//

class Dts_Settings_Panel_Init{
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
				And I still have a lot of work to do on it. <br>
				Consider making a donation if you find this product useful. <br>
				Don\'t ignore this message. Your donation will make a difference. 
				I would like to improve it as much as I can, but your support is needed.
				</p>
				<a class="right-notice-button" href="#" target="_blank">Donate</a>
			</div>
		';
	}

	public function rightBlocks(){
		return '
			<div class="right-block">
				<h3>Share with friends</h3>
				<p class="description">If you find this plugin useful, then it\'s a big chance that your friends would think the same.</p>
				<div class="zg-lg-2 zg-sm-1 zg-xs-4 zg-1">
					<div><a target="_blank" href="https://twitter.com/intent/tweet?url='. $this->shareUrl() .'&text='. $this->shareSubject() .'" class="social-share twitter"><span class="dashicons dashicons-twitter"></span> Twitter</a></div>
					<div><a target="_blank" href="https://www.facebook.com/sharer.php?s=100&p[title]='. $this->shareTitle() .'&u='. $this->shareUrl() .'&t='. $this->shareTitle() .'&p[summary]='. $this->shareSubject() .'&p[url]='. $this->shareUrl() .'" class="social-share facebook"><span class="dashicons dashicons-facebook-alt"></span> Facebook</a></div>
					<div><a target="_blank" href="https://plus.google.com/share?url='. $this->shareUrl() .'" class="social-share googleplus"><span class="dashicons dashicons-googleplus"></span> Google+</a></div>
					<div><a target="_blank" href="mailto:?subject='. $this->shareTitle() .'&body='. $this->shareSubject() .': '. $this->shareUrl() .'" class="social-share email"><span class="dashicons dashicons-email"></span> Email</a></div>
				</div>
			</div>
		';
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

//------------------------------------//--------------------------------------//

/**
 * Add settings panel
 *
 * Create a settings panel or add fields and sections to an existing one
 *
 * @param $panel_id The panel ID. Required
 * @param $panel_settings The panel settings. Optional
 * @return void
 */
class Dts_Settings_Panel{
	public $panel_id;
	public $section;

	public function __construct( $panel_id, $title = '', $settings = array()  ){
		$this->panel_id = $panel_id;
		$this->section  = 'general';
		$all_panels = dts_settings_panels();
		if( ! array_key_exists($panel_id, (array) $all_panels) ){
			new Dts_Settings_Panel_Init( $panel_id, $title, $settings );
		}
	}

	public function addField( $field_id, $field_type = 'input', $settings = array(), $priority = 20 ){
		new Dts_Settings_Panel_Add_Field($this->panel_id, $this->section, $field_id, $field_type, $settings, $priority );
	}

	public function addSection( $section_id, $section_title = false, $description = '', $priority = 20 ){
		// Add new section
		if( !empty($section_title) && trim($section_title) !== false ){
			new Dts_Settings_Panel_Add_Section($this->panel_id, $section_id, $section_title, $description, $priority);
		}

		// Switch to this section
		$this->section = $section_id;
	}

}


class Dts_Register_Field_Type_Obj{
	public $type;
	public $class;

	public function __construct( $type, $class ){
		$this->type = $type;
		$this->class = $class;
		add_filter('dts_settings_fields_types', array($this, 'add'));
	}

	public function add( $types ){
		if( is_array( $types ) && ! array_key_exists( $this->type, $types) ){
			$types[ $this->type ] = $this->class;
		}
		return $types;
	}
}

function dts_register_field_type( $type, $class ){
	new Dts_Register_Field_Type_Obj( $type, $class );
}


//------------------------------------//--------------------------------------//

/**
 * Field type base class
 *
 * Define field type base class. This class is used only as parent and can't be initiated.
 *
 * @param $field_id The field ID. Required
 * @param $settings The field settings. Optional
 * @return void
 */
abstract class Dts_Settings_Field_Type{
	public $id;
	public $panel_id;
	public $settings;

	public function __construct( $panel_id, $field_id, $settings = array() ){
		$this->id       = $field_id;
		$this->panel_id = $panel_id;
		$this->settings = $settings;
	}

	public function defaultSettings(){
		return array();
	}

	public function getSettings(){
		return wp_parse_args( $this->settings, $this->defaultSettings() );
	}

	public function getSetting( $setting_key ){
		$settings = $this->getSettings();
		return ( isset($settings[ $setting_key ]) ) ? $settings[ $setting_key ] : false;
	}

	public function getFieldName(){
		return esc_attr( $this->panel_id .'['. $this->id .']' );
	}

	public function getFieldValue(){
		$option = get_option( $this->panel_id );
		if( !empty($option) &&  isset($option[ $this->id ])  ){
			return $option[ $this->id ];
		}
		else{
			return !empty($this->settings['default']) ? $this->settings['default'] : '';
		}
	}

	public function sanitize( $value ){
		return $value;
	}

	abstract public function render();
}

/*
-------------------------------------------------------------------------------
Text
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Text extends Dts_Settings_Field_Type{

	public function defaultSettings(){
		return array(
			'allow_safe_html' => false,
			'size' => 'regular',
			'type_attr' => 'text',
		);
	}

	public function render(){
		$size = $this->getSetting( 'size' );
		$type_attr = $this->getSetting( 'type_attr' );
		$type  = 'text';
		$size_class = 'regular-text';

		// Input type
 		if( !empty($type_attr) ){
			if( in_array( $type_attr, array('text', 'password', 'email', 'number', 'url', ) ) ){
				$type = $type_attr;
			}
		}

		// Input width
		if( !empty($size) ){
			if( in_array( $size, array('wide', 'widefat', 'large') ) ){
				$size_class = 'widefat';
			}
			elseif( in_array( $size, array('small', 'small-text', 'mini') ) ){
				$size_class = 'small-text';
			}
			elseif( 'none' == $size ){
				$size_class = '';
			}
		}

		return '<input type="'. $type .'" value="'. esc_html( $this->getFieldValue() ) .'" name="'. $this->getFieldName() .'" class="'. $size_class .'" />';
	}

	public function sanitize( $value ){
		$allow_safe_html = $this->getSetting('allow_safe_html');
		return ( !empty($allow_safe_html) ) ? wp_kses_data( $value ) : sanitize_text_field( $value );
	}
}
dts_register_field_type( 'text', 'Dts_Settings_Field_Text' );

/*
-------------------------------------------------------------------------------
Textarea
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Textarea extends Dts_Settings_Field_Type{

	public function defaultSettings(){
		return array(
			'rows' => 5,
			'size' => 'regular',
			'allow_html' => true, // 'safe', 'any' or 'all' or 'unfiltered', 'limited'
		);
	}

	public function render(){
		$size = $this->getSetting( 'size' );
		$rows = absint( $this->getSetting( 'rows' ) );
		$rows = $rows > 0 ? $rows : 5;
		$size_attr = '';

		// Input width
		if( !empty($size) && ($size = trim( $size )) ){
			if( in_array( $size, array('wide', 'widefat', 'large') ) ){
				$size_attr = ' class="widefat"';
			}
			elseif( is_numeric( $size ) ){
				$size_attr = ' cols="'. absint( $size ) .'"';
			}
		}

		return '<textarea name="'. $this->getFieldName() .'"'. $size_attr .' rows="'. $rows .'">'. esc_textarea( $this->getFieldValue() ) .'</textarea>';
	}

	public function sanitize( $value ){
		$allow_html = $this->getSetting('allow_html');

		// Sanitize
		if( 'safe' == $allow_html ){
			$value = wp_kses_post( $value ); // Link in post
		}
		elseif( 'limited' == $allow_html ){
			$value = wp_kses_data( $value ); // Only some inline tags
		}
		elseif( in_array( $allow_html, array('any', 'all', 'unfiltered') ) ){
			$value = $value; // Any HTML tags and attr, even 'script'. RAW
		}
		elseif( $allow_html === false ){
			$value = strip_tags( $value ); // No tags allowed at all
		}
		else{
			$value = wp_kses_post( $value ); // Default. Can use only the tags that are allowed in posts.
		}

		return $value;
	}
}
dts_register_field_type( 'textarea', 'Dts_Settings_Field_Textarea' );

/*
-------------------------------------------------------------------------------
Select
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Select extends Dts_Settings_Field_Type{
	public function render(){
		return '<select name="'. $this->getFieldName() .'"></select>';
	}
}
dts_register_field_type( 'select', 'Dts_Settings_Field_Select' );

/*
-------------------------------------------------------------------------------
Page select
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Page_Select extends Dts_Settings_Field_Type{

	public function defaultSettings(){
		return array(
			'show_empty_label' => true,
		);
	}

	public function render(){
		$show_empty_label = $this->getSetting( 'show_empty_label' );
		$option_none = null;
		if( !empty( $show_empty_label ) ){
			if( true !== $show_empty_label ){
				$option_none = $show_empty_label;
			}
			else{
				$option_none = __('-- Select page --', 'smk_theme');
			}
		}

		$args = array(
			'depth'                 => 0,
			'child_of'              => 0,
			'selected'              => absint( $this->getFieldValue() ),
			'echo'                  => 0,
			'name'                  => $this->getFieldName(),
			'id'                    => null,
			'show_option_none'      => $option_none,
			'show_option_no_change' => null,
			'option_none_value'     => null,
		);
		return wp_dropdown_pages( $args );
	}

	public function sanitize( $value ){
		$page_exists = ( absint( $value ) ) ? get_post( absint( $value ) ) : false;
		return ( !empty($page_exists) ) ? absint( $value ) : '';
	}
}
dts_register_field_type( 'page_select', 'Dts_Settings_Field_Page_Select' );

/*
-------------------------------------------------------------------------------
Checkbox
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Checkbox extends Dts_Settings_Field_Type{

	public function defaultSettings(){
		return array(
			'display_inline' => false,
		);
	}

	public function render(){
		$options        = $this->getSetting( 'options' );
		$display_inline = $this->getSetting( 'display_inline' );
		$value          = $this->getFieldValue();

		$output = '<input name="'. $this->getFieldName() .'" type="hidden" value="" />';

		// If is multicheckbox.
		if( !empty($options) && is_array($options) ){
			foreach ($options as $option_value => $option_label) {
				$checked = ( in_array($option_value, (array) $value ) ) ? ' checked="checked"' : '';
				$inline  = ( !empty($display_inline) ) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '<br />';
				$output .= '<label><input type="checkbox" value="'. $option_value .'" name="'. $this->getFieldName() .'[]"'. $checked .' />'. esc_html( $option_label ) .'</label>'. $inline;
			}
		}

		// Else is single checkbox with 'on' value
		else{
			$output .= '<label><input type="checkbox" value="on" name="'. $this->getFieldName() .'" '. checked( $this->getFieldValue(), 'on', false ) .' />'. $this->getSetting( 'label' ) .'</label>';
		}

		return $output;
	}
}
dts_register_field_type( 'checkbox', 'Dts_Settings_Field_Checkbox' );

/*
-------------------------------------------------------------------------------
Checkbox
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Radio extends Dts_Settings_Field_Type{

	public function defaultSettings(){
		return array(
			'display_inline' => false,
		);
	}

	public function render(){
		$options        = $this->getSetting( 'options' );
		$display_inline = $this->getSetting( 'display_inline' );
		$value          = $this->getFieldValue();

		$output = '<input name="'. $this->getFieldName() .'" type="hidden" value="" />';

		if( !empty($options) && is_array($options) ){
			foreach ($options as $option_value => $option_label) {
				$checked = ( in_array($option_value, (array) $value ) ) ? ' checked="checked"' : '';
				$inline  = ( !empty($display_inline) ) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '<br />';
				$output .= '<label><input type="radio" value="'. $option_value .'" name="'. $this->getFieldName() .'"'. $checked .' />'. esc_html( $option_label ) .'</label>'. $inline;
			}
		}

		else{
			$output .= '<label><input type="radio" value="on" name="'. $this->getFieldName() .'" '. checked( $this->getFieldValue(), 'on', false ) .' />'. __('On', 'smk_theme') .'</label>'. $inline;
			$output .= '<label><input type="radio" value="off" name="'. $this->getFieldName() .'" '. checked( $this->getFieldValue(), 'off', false ) .' />'. __('Off', 'smk_theme') .'</label>'. $inline;
		}

		return $output;
	}
}
dts_register_field_type( 'radio', 'Dts_Settings_Field_Radio' );

/*
-------------------------------------------------------------------------------
Info Table
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Info_Box_Table extends Dts_Settings_Field_Type{

	public function render(){
		$content = $this->getSetting( 'content' );
		$table = '';

		if( is_array( $content ) ){
			$table .= '<table class="wp-list-table widefat fixed striped" style="max-width: 800px;">';
			foreach ($content as $key => $value) {
				$table .= '<tr>
					<td><strong>'. $key .'</strong></td>
					<td>'. $value .'</td>
				</tr>';
			}
			$table .= '</table>';
		}

		return $table;
	}
}
dts_register_field_type( 'info_box_table', 'Dts_Settings_Field_Info_Box_Table' );

/*
-------------------------------------------------------------------------------
Info field
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Info_Field extends Dts_Settings_Field_Type{

	public function render(){
		$content = $this->getSetting( 'content' );

		if( !empty( $content ) ){
			return '<div class="notice inline notice-warning notice-alt"><p>'. $content .'</p></div>';
		}
	}
}
dts_register_field_type( 'info_field', 'Dts_Settings_Field_Info_Field' );

/*
-------------------------------------------------------------------------------
Uploader
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Image extends Dts_Settings_Field_Type{

	public function defaultSettings(){
		// return array(
		// );
	}

	public function render(){
		$save_img = '';
	
		if( !empty( $this->getFieldValue() ) ){
			$save_img = '<img src="'. esc_html( $this->getFieldValue() ) .'" alt="" />';
		}

		return '<div class="demonstrator_uploader">
			<div class="media-container">'. $save_img .'</div>
			<input type="hidden" value="'. esc_html( $this->getFieldValue() ) .'" name="'. $this->getFieldName() .'" class="media-input" />
			<span class="demonstrator_uploader_btn button">Select image</span>
		</div>';
	}

	// public function sanitize( $value ){
	// 	return esc_url_raw( $value );
	// }
}
dts_register_field_type( 'image', 'Dts_Settings_Field_Image' );

/*
-------------------------------------------------------------------------------
Themes
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Themes extends Dts_Settings_Field_Type{

	protected $minumum_sections = 0;

	public function defaultSettings(){
		// return array(
		// );
	}

	public function render(){
		$value = $this->getFieldValue();
		$output = '';

		$output .= '<ul class="demonstrator_themes themes-repeatable-block" data-minimum-sections="'. absint($this->minumum_sections) .'">';
			
			$output .= $this->getSingleItem( '__noindex__', array() );
			
			if( ! empty( $value ) ){
				foreach ($value as $theme_id => $theme) {
					$output .= $this->getSingleItem( $theme_id, $theme );
				}
			}
			elseif( $this->minumum_sections > 0 ){
				$output .= $this->getSingleItem( uniqid(), array() );
			}
			

		$output .= '</ul>';

		$output .= '<span class="demonstrator_add_theme form-repeatable-button">'. $this->labelAddNew() .'</span>';

		return $output;
	}

	public function labelAddNew(){
		return __( 'Add theme', 'demonstrator' );
	}

	public function sectionHeader( $label, $theme_id, $category ){
		return '
			<span class="section-theme-title">'. $label .'</span>
			<span class="badge section-theme-id">'. $theme_id .'</span>
			<span class="badge section-theme-category">'. $category .'</span>
			<span class="badge delete-theme cancel-drag" title="'. __('Delete', 'demonstrator') .'">
				<i class="dashicons dashicons-trash"></i>
			</span>
		';
	}

	public function fields( $theme_id, $value ){
		$name              = $this->getFieldName() . '['. $theme_id .']';
		$label             = !empty( $value['label'] ) ? $value['label'] : '';
		$img               = !empty( $value['img'] ) ? $value['img'] : '';
		$purchase_url      = !empty( $value['purchase_url'] ) ? $value['purchase_url'] : '';
		$category          = !empty( $value['category'] ) ? $value['category'] : '';
		// $demo_url          = !empty( $value['demo_url'] ) ? $value['demo_url'] : '';
		$price             = !empty( $value['price'] ) ? $value['price'] : '';
		$short_description = !empty( $value['short_description'] ) ? $value['short_description'] : '';
		$status            = !empty( $value['status'] ) ? $value['status'] : '';
		$styles            = !empty( $value['styles'] ) ? $value['styles'] : '';

		$output = '';
		
		$output .= '<div class="col-sm-3">';

			$output .= $this->tRow( 
				__( 'Image', 'demonstrator' ),
				$this->getUploader( $name . '[img]', $img )
			);

		$output .= '</div>';
		
	
		$output .= '<div class="col-sm-9">';
			$output .= '<div class="zg">';
			
				$output .= $this->tRow( 
					__( 'ID', 'demonstrator' ),
					'<input name="'. $name . '[id]' .'" type="text" class="widefat this-section-theme-id-field" value="'. $theme_id .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Name', 'demonstrator' ),
					'<input name="'. $name . '[label]' .'" type="text" class="widefat this-section-theme-title-field" value="'. $label .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Category', 'demonstrator' ),
					'<input name="'. $name . '[category]' .'" type="text" class="widefat this-section-theme-category-field" value="'. $category .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Price', 'demonstrator' ),
					'<input name="'. $name . '[price]' .'" type="text" class="widefat" value="'. $price .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Purchase URL', 'demonstrator' ),
					'<input name="'. $name . '[purchase_url]' .'" type="text" class="widefat" value="'. $purchase_url .'" />'
				);

				// $output .= $this->tRow( 
				// 	__( 'Demo URL', 'demonstrator' ),
				// 	'<input name="'. $name . '[demo_url]' .'" type="text" class="widefat" value="'. $demo_url .'" />'
				// );

				$output .= $this->tRow( 
					__( 'Short description', 'demonstrator' ),
					'<textarea name="'. $name . '[short_description]' .'" class="widefat">'. esc_textarea( $short_description ) .'</textarea>',
					'col-sm-12'
				);

				$output .= $this->tRow( 
					__( 'Status', 'demonstrator' ),
					'<select name="'. $name . '[status]' .'" class="widefat">
						<option value="published" '. selected( 'published', $status, false ) .'>
							'. __( 'Published', 'demonstrator' ) .'
						</option>
						<option value="unlisted" '. selected( 'unlisted', $status, false ) .'>
							'. __( 'Unlisted', 'demonstrator' ) .'
						</option>
						<option value="private" '. selected( 'private', $status, false ) .'>
							'. __( 'Private', 'demonstrator' ) .'
						</option>
					</select>
					'
				);

				$switcher_id = !empty( $_GET['page'] ) ? str_replace( 'demonstrator_instance_', '', $_GET['page'] ) : false;
				$router = new Demonstrator\Router( $switcher_id );

				if( $switcher_id ){
					$output .= $this->tRow( 
						__( 'Demo URL', 'demonstrator' ),
						'<a class="demo_url_link" data-base-url="'. $router->getThemeUrl( '__noindex__' ) .'" href="'. $router->getThemeUrl( $theme_id ) .'" target="_blank">'. $router->getThemeUrl( $theme_id ) .'</a>'
					);
				}

			$output .= '</div>';
		$output .= '</div>';
		
		/* Styles --------------*/
		$output .= '<div class="col-12">';
			$output .= '<div class="zg demonstrator-styles-group">';

				$output .= '<div class="demonstrator-styles-header col-12">'. __( 'Styles', 'demonstrator' ) .'</div>';
				$output .= '<ul class="demonstrator_styles demonstrator-styles-list col-12">';
					
					$output .= $this->getSingleStyle( $theme_id, '__stylenoindex__', array() );
					
					if( ! empty( $styles ) ){
						foreach ($styles as $style_id => $style) {
							$output .= $this->getSingleStyle( $theme_id, $style_id, $style );
						}
					}
					else{
						$output .= $this->getSingleStyle( $theme_id, 'st1', array() );
					}
					

				$output .= '</ul>';

				$output .= '<div class="col-12">';
					$output .= '<span class="demonstrator_add_style form-repeatable-button simple">'. __( 'Add style', 'demonstrator' ) .'</span>';
				$output .= '</div>';

			$output .= '</div>';
		$output .= '</div>';
		/* / Styles --------------*/

		return $output;
	}

	public function getSingleItem( $theme_id, $value ){
		$label             = !empty( $value['label'] ) ? $value['label'] : '';
		$category          = !empty( $value['category'] ) ? $value['category'] : '';
		
		$output = '';

		$list_class = ( '__noindex__' == $theme_id ) ? 'sfa-theme-noindex' : '';

		$output .= '<li class="'. $list_class .'">';
			
			$output .= '<div>'. $this->sectionHeader( $label, $theme_id, $category ) .'</div>'; // nth 1
			
			$output .= '<div class="wp-clearfix">';
				$output .= '<div class="zg">';

					$output .= $this->fields( $theme_id, $value );
				
				$output .= '</div>'; // .zg
			$output .= '</div>'; // .wp-clearfix
		$output .= '</li>';

		return $output;
	}

	public function tRow( $title, $field, $col_class = 'col-12' ){
		$output = '<div class="'. $col_class .'">';
			$output .= '<div class="trow">';
				$output .= '<div class="tlabel">'.$title .'</div>';
				$output .= '<div class="tfield">'. $field .'</div>';
			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	public function getSingleStyle( $theme_id, $style_id, $value ){
		$name  = $this->getFieldName() . '['. $theme_id .'][styles]['. $style_id .']';
		$label = !empty( $value['label'] ) ? $value['label'] : '';
		$img   = !empty( $value['img'] ) ? $value['img'] : '';
		$url   = !empty( $value['url'] ) ? $value['url'] : '';

		$output = '';

		$list_class = ( '__stylenoindex__' == $style_id ) ? 'sfa-style-noindex' : '';

		$output .= '<li class="'. $list_class .'">';
			$output .= '<span class="delete-style" title="'. __('Delete', 'demonstrator') .'"><i class="dashicons dashicons-no"></i></span>';

			$output .= '<div class="zg">';

				$output .= '<div class="col-sm-3">';

					$output .= $this->tRow( 
						__( 'Image', 'demonstrator' ),
						$this->getUploader( $name . '[img]', $img )
					);

				$output .= '</div>';

				$output .= '<div class="col-sm-9">';
					$output .= '<div class="zg">';

						$output .= $this->tRow( 
							__( 'Demo URL', 'demonstrator' ),
							'<input name="'. $name . '[url]' .'" type="text" class="widefat" value="'. $url .'" />'
						);

						$output .= $this->tRow( 
							__( 'Name', 'demonstrator' ),
							'<input name="'. $name . '[label]' .'" type="text" class="widefat this-section-style-title-field" value="'. $label .'" />',
							'col-sm-8'
						);
						
						$output .= $this->tRow( 
							__( 'ID', 'demonstrator' ),
							'<input name="'. $name . '[id]' .'" type="text" class="widefat this-section-style-id-field" value="'. $style_id .'" />',
							'col-sm-4'
						);

					$output .= '</div>';
				$output .= '</div>';

			$output .= '</div>'; // .zg
		$output .= '</li>';

		return $output;
	}

	public function getUploader( $uploader_name, $uploader_value ){
		$img = '';

		if( !empty( $uploader_value ) ){
			$img = '<img src="'. esc_html( $uploader_value ) .'" alt="" />';
		}

		return '<div class="demonstrator_uploader">
			<div class="media-container">'. $img .'</div>
			<input type="hidden" value="'. esc_html( $uploader_value ) .'" name="'. $uploader_name .'" class="media-input" />
			<span class="demonstrator_uploader_btn button">Select image</span>
		</div>';
	}

	public function sanitize( $value ){
		unset( $value['__noindex__'] );

		if( is_array( $value ) ){
			$new_themes = $value;
			foreach ($value as $temp_id => $theme) {
				unset( $new_themes[ $temp_id ]['styles']['__stylenoindex__'] );

				$styles = $new_themes[ $temp_id ]['styles'];
				
				foreach ($styles as $style_id => $style) {
					
					if( empty( $style['url'] ) ){
						unset( $styles[ $style_id ] );
					}
					
					if( ! empty( $style['id'] ) && ! array_key_exists( $style['id'], $styles )  ){
						$styles[ $style['id'] ] = $style;
						unset( $styles[ $style_id ] );
					}

				}

				$new_themes[ $temp_id ]['styles'] = $styles;

				// if( ! array_key_exists( $theme['id'], $new_themes ) ){
				// 	$new_themes[ $theme['id'] ] = $theme;
				// 	unset( $new_themes[ $temp_id ] );
				// }
				$new_themes = $this->replaceArrayKey( $new_themes, $temp_id, $theme['id'] );

			}			
			$value = $new_themes;
		}

		return $value;
	}

	public function replaceArrayKey($array, $key1, $key2){
		$keys = array_keys($array);
		$index = array_search($key1, $keys);

		if ($index !== false) {
			$keys[$index] = $key2;
			$array = array_combine($keys, $array);
		}

		return $array;
	}

}
dts_register_field_type( 'themes', 'Dts_Settings_Field_Themes' );
/*
-------------------------------------------------------------------------------
Themes
-------------------------------------------------------------------------------
*/
class Dts_Settings_Field_Switchers extends Dts_Settings_Field_Themes{

	protected $minumum_sections = 1;

	public function labelAddNew(){
		return __( 'Add switcher', 'demonstrator' );
	}

	public function sectionHeader( $label, $switcher_id, $category ){
		return '
			<span class="section-theme-title">'. $label .'</span>
			<span class="badge section-theme-id">'. $switcher_id .'</span>
			<span class="badge delete-theme cancel-drag" title="'. __('Delete', 'demonstrator') .'">
				<i class="dashicons dashicons-trash"></i>
			</span>
		';
	}

	public function fields( $switcher_id, $value ){
		$name                    = $this->getFieldName() . '['. $switcher_id .']';
		$label                   = !empty( $value['label'] ) ? $value['label'] : '';
		$switcher_logo           = !empty( $value['switcher_logo'] ) ? $value['switcher_logo'] : '';
		$site_url                = !empty( $value['site_url'] ) ? $value['site_url'] : '';
		$envato_username         = !empty( $value['envato_username'] ) ? $value['envato_username'] : '';
		$creativemarket_username = !empty( $value['creativemarket_username'] ) ? $value['creativemarket_username'] : '';
		
		$themes_grid             = !empty( $value['themes_grid'] ) ? absint( $value['themes_grid'] ) : 3;
		$themes_grid             = ( $themes_grid > 0 && $themes_grid < 5 ) ? $themes_grid : 3;
		$styles_grid             = !empty( $value['styles_grid'] ) ? absint( $value['styles_grid'] ) : 3;
		$styles_grid             = ( $styles_grid > 0 && $styles_grid < 5 ) ? $styles_grid : 3;

		$output = '';
		
		$output .= '<div class="col-sm-3">';

			$output .= $this->tRow( 
				__( 'Switcher logo', 'demonstrator' ),
				$this->getUploader( $name . '[switcher_logo]', $switcher_logo )
			);

		$output .= '</div>';
		
	
		$output .= '<div class="col-sm-9">';
			$output .= '<div class="zg">';
			
				$output .= $this->tRow( 
					__( 'Endpoint ID', 'demonstrator' ),
					'<input name="'. $name . '[id]' .'" type="text" class="widefat this-section-theme-id-field" value="'. $switcher_id .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Label', 'demonstrator' ),
					'<input name="'. $name . '[label]' .'" type="text" class="widefat this-section-theme-title-field" value="'. $label .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Your site URL', 'demonstrator' ),
					'<input name="'. $name . '[site_url]' .'" type="text" class="widefat" value="'. $site_url .'" />',
					'col-sm-6'
				);

				$output .= $this->tRow( 
					__( 'Themes grid', 'demonstrator' ),
					'
					<label>
						<input type="radio" value="4" '. checked( '4', $themes_grid, false ) .' name="'. $name . '[themes_grid]' .'">
						'. __( '4 columns', 'demonstrator' ) .'
					</label>
					<label>
						<input type="radio" value="3" '. checked( '3', $themes_grid, false ) .' name="'. $name . '[themes_grid]' .'">
						'. __( '3 columns', 'demonstrator' ) .'
					</label>
					<label>
						<input type="radio" value="2" '. checked( '2', $themes_grid, false ) .' name="'. $name . '[themes_grid]' .'">
						'. __( '2 columns', 'demonstrator' ) .'
					</label>
					<label>
						<input type="radio" value="1" '. checked( '1', $themes_grid, false ) .' name="'. $name . '[themes_grid]' .'">
						'. __( '1 column', 'demonstrator' ) .'
					</label>
					',
					'col-sm-6'
				);

				$output .= $this->tRow( 
					__( 'Styles grid', 'demonstrator' ),
					'
					<label>
						<input type="radio" value="4" '. checked( '4', $styles_grid, false ) .' name="'. $name . '[styles_grid]' .'">
						'. __( '4 columns', 'demonstrator' ) .'
					</label>
					<label>
						<input type="radio" value="3" '. checked( '3', $styles_grid, false ) .' name="'. $name . '[styles_grid]' .'">
						'. __( '3 columns', 'demonstrator' ) .'
					</label>
					<label>
						<input type="radio" value="2" '. checked( '2', $styles_grid, false ) .' name="'. $name . '[styles_grid]' .'">
						'. __( '2 columns', 'demonstrator' ) .'
					</label>
					<label>
						<input type="radio" value="1" '. checked( '1', $styles_grid, false ) .' name="'. $name . '[styles_grid]' .'">
						'. __( '1 column', 'demonstrator' ) .'
					</label>
					',
					'col-sm-6'
				);

				$output .= $this->tRow( 
					__( 'Envato Username', 'demonstrator' ),
					'<input name="'. $name . '[envato_username]' .'" type="text" class="widefat" value="'. $envato_username .'" />',
					'col-sm-6'
				);

				$output .= $this->tRow( 
					__( 'CreativeMarket Username', 'demonstrator' ),
					'<input name="'. $name . '[creativemarket_username]' .'" type="text" class="widefat" value="'. $creativemarket_username .'" />',
					'col-sm-6'
				);

			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	public function sanitize( $value ){
		unset( $value['__noindex__'] );

		if( is_array( $value ) ){
			$new_themes = $value;
			foreach ($value as $temp_id => $theme) {
	
				$new_themes = $this->replaceArrayKey( $new_themes, $temp_id, $theme['id'] );

				if( $temp_id !== $theme['id'] ){
					$old_items = get_option( 'demonstrator_instance_' . $temp_id, null );
					if( isset($old_items) ){
						update_option( 'demonstrator_instance_' . $theme['id'], $old_items );
						delete_option( 'demonstrator_instance_' . $temp_id );
					}
				}

			}			
			$value = $new_themes;
		}

		return $value;
	}

}
dts_register_field_type( 'switchers', 'Dts_Settings_Field_Switchers' );
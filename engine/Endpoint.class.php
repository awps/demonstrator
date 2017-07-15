<?php 
namespace Demonstrator;

class Endpoint {

	public $endpoint_key = false;
	
	public function __construct() {
		$this->endpoint_key = get_option( 'demonstrator_permalink_slug', false );

		add_action( 'init', array( $this, 'addEndpoint' ) );
		add_filter( 'template_redirect', array( $this, 'templateInclude' ) );
		
		add_action('admin_init', array( $this, 'settingsInit' ));
		add_action('admin_init', array( $this, 'settingsSave' ));
	}
	
	/*
	-------------------------------------------------------------------------------
	Apply endpoint
	-------------------------------------------------------------------------------
	*/
	public function addEndpoint(){
		if( !empty( $this->endpoint_key ) ){
			add_rewrite_endpoint( $this->endpoint_key, EP_ALL );
		}
	}

	public function templateInclude(){
		if( is_admin() )
		return;

		if( !empty( $this->endpoint_key ) ){
			$th = get_query_var( $this->endpoint_key, null );
			if( ! isset( $th ) )
				return;
		}

		include DEMONSTRATOR_PATH .'components/template.php';
		exit;
	}

	/*
	-------------------------------------------------------------------------------
	Fields
	-------------------------------------------------------------------------------
	*/
	public function settingsInit() {
		$this->addField( 
			'demonstrator_permalink_slug', 
			array( $this, 'demonstratorSlugInput' ), 
			__( 'Demonstrator endpoint', 'demonstrator' ) 
		);
	}

	/*
	-------------------------------------------------------------------------------
	Callbacks
	-------------------------------------------------------------------------------
	*/
	public function demonstratorSlugInput() {
		echo $this->input( 'demonstrator_permalink_slug', '');
	}

	/*
	-------------------------------------------------------------------------------
	Save settings
	-------------------------------------------------------------------------------
	*/
	public function settingsSave() {
		if ( ! is_admin() ) return;

		$this->saveField( 'demonstrator_permalink_slug' );
	}

	/*
	-------------------------------------------------------------------------------
	Helpers
	-------------------------------------------------------------------------------
	*/
	public function input( $option_name, $placeholder = '' ) {
		$slug = get_option( $option_name );
		$value = ( isset( $slug ) ) ? esc_attr( $slug ) : '';
		return '<input name="'. $option_name .'" type="text" class="regular-text code" value="'. $slug .'" placeholder="'. $placeholder .'" />';
	}

	public function addField( $option_name, $callback, $title ){
		add_settings_field(
			$option_name, // id
			$title,       // setting title
			$callback,    // display callback
			'permalink',  // settings page
			'optional'    // settings section
		);
	}

	public function saveField( $option_name ){
		if ( isset( $_POST[$option_name] ) ) {
			$permalink_structure = sanitize_title( $_POST[$option_name] );
			$permalink_structure = untrailingslashit( $permalink_structure );

			update_option( $option_name, $permalink_structure );
		}
	}
}
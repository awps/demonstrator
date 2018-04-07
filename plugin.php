<?php

final class DEMONSTRATOR_Plugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version;

	//------------------------------------//--------------------------------------//

	/**
	 * Assets injector
	 *
	 * @var string
	 */
	public $assets;

	/**
	 * This is the only instance of this class.
	 *
	 * @var string
	 */
	protected static $_instance = null;

	//------------------------------------//--------------------------------------//

	/**
	 * Plugin instance
	 *
	 * Makes sure that just one instance is allowed.
	 *
	 * @return object
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Cloning is forbidden.
	 *
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'demonstrator' ), '1.0' );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'demonstrator' ), '1.0' );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Plugin configuration
	 *
	 * @param string $key Optional. Get the config value by key.
	 *
	 * @return mixed
	 */
	public function config( $key = false ) {
		return demonstrator_config( $key );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Build it!
	 */
	public function __construct() {
		$this->version = DEMONSTRATOR_VERSION;

		/* Include core
		--------------------*/
		include_once $this->rootPath() . "autoloader.php";
		include_once $this->rootPath() . "functions.php";

		$this->assets = new Demonstrator\Assets\Manage;

		/* Activation and deactivation hooks
		-----------------------------------------*/
		register_activation_hook( DEMONSTRATOR_PLUGIN_FILE, array( $this, 'onActivation' ) );
		register_deactivation_hook( DEMONSTRATOR_PLUGIN_FILE, array( $this, 'onDeactivation' ) );

		/* Init core
		-----------------*/
		add_action( $this->config( 'action_name' ), array( $this, 'init' ), 0 );

		/* Load components, if any...
		----------------------------------*/
		$this->loadComponents();

		/* Plugin fully loaded and executed
		----------------------------------------*/
		do_action( 'demonstrator:loaded' );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Init the plugin.
	 *
	 * Attached to `init` action hook. Init functions and classes here.
	 *
	 * @return void
	 */
	public function init() {
		do_action( 'demonstrator:before_init' );

		$this->loadTextDomain();

		// Call plugin classes/functions here.
		do_action( 'demonstrator:init' );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Localize
	 *
	 * @return void
	 */
	public function loadTextDomain() {
		load_plugin_textdomain(
			'demonstrator',
			false,
			$this->config( 'lang_path' )
		);
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Load components
	 *
	 * @return void
	 */
	public function loadComponents() {
		$components = glob( DEMONSTRATOR_PATH . 'components/*', GLOB_ONLYDIR );
		foreach ( $components as $component_path ) {
			require_once trailingslashit( $component_path ) . 'component.php';
		}
	}

	/*
	-------------------------------------------------------------------------------
	Styles
	-------------------------------------------------------------------------------
	*/
	public function addStyles( $styles ) {
		$this->assets->addStyles( $styles );
	}

	public function addStyle( $handle, $s = false ) {
		$this->assets->addStyle( $handle, $s );
	}

	/*
	-------------------------------------------------------------------------------
	Scripts
	-------------------------------------------------------------------------------
	*/
	public function addScripts( $scripts ) {
		$this->assets->addScripts( $scripts );
	}

	public function addScript( $handle, $s = false ) {
		$this->assets->addScript( $handle, $s );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Actions when the plugin is activated
	 *
	 * @return void
	 */
	public function onActivation() {
		// Code to be executed on plugin activation
		do_action( 'demonstrator:on_activation' );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Actions when the plugin is deactivated
	 *
	 * @return void
	 */
	public function onDeactivation() {
		// Code to be executed on plugin deactivation
		do_action( 'demonstrator:on_deactivation' );
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get Root URL
	 *
	 * @return string
	 */
	public function rootURL() {
		return DEMONSTRATOR_URL;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get Root PATH
	 *
	 * @return string
	 */
	public function rootPath() {
		return DEMONSTRATOR_PATH;
	}

	//------------------------------------//--------------------------------------//

	/**
	 * Get assets url.
	 *
	 * @param string $file Optionally specify a file name
	 *
	 * @return string
	 */
	public function assetsURL( $file = false ) {
		$path = DEMONSTRATOR_URL . 'assets/';

		if ( $file ) {
			$path = $path . $file;
		}

		return $path;
	}

}


/*
-------------------------------------------------------------------------------
Main plugin instance
-------------------------------------------------------------------------------
*/
function dmstr() {
	return DEMONSTRATOR_Plugin::instance();
}

/*
-------------------------------------------------------------------------------
Rock it!
-------------------------------------------------------------------------------
*/
dmstr();

<?php
class DEMONSTRATOR_Plugin_Autoloader{

	public function __construct(){
		spl_autoload_register( array( $this, 'autoloadEngine' ) );
		spl_autoload_register( array( $this, 'autoloadComponents' ) );
	}

	/*
	-------------------------------------------------------------------------------
	Engine Autoloader
	-------------------------------------------------------------------------------
	*/
	public function autoloadEngine( $class ){
		$prefix   = demonstrator_config( 'namespace' ) .'\\';
		$base_dir = DEMONSTRATOR_PATH .'engine/';

		$this->locateFile( $class, $prefix, $base_dir );
	}
	
	/*
	-------------------------------------------------------------------------------
	Components Autoloader
	-------------------------------------------------------------------------------
	*/
	public function autoloadComponents( $class ){
		$prefix   = demonstrator_config( 'namespace' ) .'\\Component\\';
		$base_dir = DEMONSTRATOR_PATH .'components/';

		$this->locateFile( $class, $prefix, $base_dir );
	}

	/*
	-------------------------------------------------------------------------------
	Locate file for autoload
	-------------------------------------------------------------------------------
	*/
	public function locateFile( $class, $prefix, $base_dir ){
		// does the class use the namespace prefix?
		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) !== 0) {
			// no, move to the next registered autoloader
			return;
		}

		// get the relative class name
		$relative_class = substr($class, $len);

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .class.php
		$file = $base_dir . str_replace('\\', '/', $relative_class) . '.class.php';

		// if the file exists, require it
		if (file_exists($file)) {
			require_once $file;
		}
	}

}

new DEMONSTRATOR_Plugin_Autoloader;
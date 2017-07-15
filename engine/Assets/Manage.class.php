<?php 
namespace Demonstrator\Assets;

class Manage{

	public function __construct() {
		$this->version = DEMONSTRATOR_VERSION;
	}

	public function addStyle( $handle, $s = false ){
		/* If just calling an already registered style
		------------------------------------------------------*/
		if( is_numeric( $handle ) && !empty($s) ){
			wp_enqueue_style( $s );
			return false;
		}
		elseif( !empty( $handle ) && empty($s) ){
			wp_enqueue_style( $handle );
			return false;
		}

		/* Merge with defaults
		------------------------------*/
		$s = wp_parse_args( $s, array(
			'deps'    => array(),
			'ver'     => $this->version,
			'media'   => 'all',
			'enqueue' => true,
			'enqueue_callback' => false,
		));
		
		/* Register style
		-------------------------*/
		wp_register_style( $handle, $s['src'], $s['deps'], $s['ver'], $s['media'] );
		
		/* Enqueue style
		------------------------*/
		$this->_enqueue( 'style', $s, $handle );
	}

	public function addScript( $handle, $s = false ){
		/* If just calling an already registered script
		----------------------------------------------------*/
		if( is_numeric( $handle ) && !empty($s) ){
			wp_enqueue_script( $s );
			return false;
		}
		elseif( !empty( $handle ) && empty($s) ){
			wp_enqueue_script( $handle );
			return false;
		}

		/* Register
		----------------*/
		// Merge with defaults
		$s = wp_parse_args( $s, array(
			'deps'      => array( 'jquery' ),
			'ver'       => $this->version,
			'in_footer' => true,
			'enqueue'   => true,
			'enqueue_callback' => false,
		));
		
		wp_register_script( $handle, $s['src'], $s['deps'], $s['ver'], $s['in_footer'] );
		
		/* Enqueue
		---------------*/
		$this->_enqueue( 'script', $s, $handle );

		/* Localize 
		-----------------*/
		// Remove known keys 
		unset( $s['src'], $s['deps'], $s['ver'], $s['in_footer'], $s['enqueue'], $s['enqueue_callback'] );

		// Probably we have localization strings
		if( !empty( $s ) ){

			// Get first key from array. This may contain the strings for wp_localize_script
			$localize_key = key( $s );

			// Get strings
			$localization_strings = $s[ $localize_key ];

			// Localize strings
			if( !empty( $localization_strings ) && is_array( $localization_strings ) ){
				wp_localize_script( $handle, $localize_key, $localization_strings );
			}

		}
	}

	public function addStyles( $styles ){
		if( !empty( $styles ) ){

			foreach ($styles as $handle => $s) {
				$this->addStyle( $handle, $s );
			}

		}
	}

	/*
	-------------------------------------------------------------------------------
	Scripts
	-------------------------------------------------------------------------------
	*/
	public function addScripts( $scripts ){
		if( !empty( $scripts ) ){

			foreach ($scripts as $handle => $s) {
				$this->addScript( $handle, $s );
			}

		}
	}

	//------------------------------------//--------------------------------------//
	
	/**
	 * Enqueue
	 *
	 * Try to enqueue, but first check the callback
	 *
	 * @param string $type 'script' or 'style'
	 * @param array $s Parameters
	 * @param string $handle Asset handle
	 * @return void 
	 */
	protected function _enqueue( $type, $s, $handle ){
		if( $s['enqueue'] ){
			if( empty( $s['enqueue_callback'] ) || ( 
				! empty( $s['enqueue_callback'] ) 
				&& is_callable( $s['enqueue_callback'] ) 
				&& call_user_func( $s['enqueue_callback'] )
			) ){
				
				if( 'style' == $type ){
					wp_enqueue_style( $handle );
				}
				elseif( 'script' == $type ){
					wp_enqueue_script( $handle );
				}

			}
		}
	}

}
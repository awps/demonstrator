<?php 
namespace Demonstrator;

class Redirect {

	public function __construct(){
		add_filter( 'init', array( $this, 'buy' ), 1 );
	}

	public function buy(){
		if( is_admin() )
			return;

		if( ! empty( $_GET['buy'] ) ){
			$theme_id = sanitize_key( $_GET['buy'] );
			$themes = demonstrator_themes();
			
			if( !empty($themes) && array_key_exists($theme_id, $themes) ){
				wp_redirect( $themes[ $theme_id ][ 'purchase_url' ] );
				exit;
			}
		}
	}

}
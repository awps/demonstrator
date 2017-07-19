<?php 
namespace Demonstrator;

class Router{

	public $switcher_id;
	public $query_var;

	public function __construct( $switcher_id ){
		$this->switcher_id = $switcher_id;

		// Cache the query var
		$this->getQueryVar();
	}

	// Return the current query var. Structure: <site_adress>/<switcher_id>/<query_var>
	public function getQueryVar(){
		if( ! isset( $this->query_var ) ) {
			$this->query_var = get_query_var( $this->switcher_id, false );
		}
		
		return $this->query_var;
	}

	public function getThemeId(){
		if( empty( $this->query_var ) )
			return false;

		$vars = explode( '/', $this->query_var );

		return $vars[0];
	}

	public function getStyleId(){
		if( empty( $this->query_var ) )
			return false;

		$vars = explode( '/', $this->query_var );

		if( empty( $vars[1] ) )
			return false;

		return $vars[1];
	}

	public function getSwitcherAdminUrl(){
		return admin_url( 'admin.php?page=demonstrator_instance_' . $this->switcher_id );
	}

	public function getSwitcherUrl( $query_var_value = '' ){
		$permalink = home_url();

		if ( get_option( 'permalink_structure' ) ) {
			$url = trailingslashit( $permalink ) . $this->switcher_id . '/' . $query_var_value;
		} 
		else {
			$url = add_query_arg( $this->switcher_id, $query_var_value, $permalink );
		}

		return apply_filters( 'switcher_url', $url, $this->switcher_id, $query_var_value );
	}

	public function getThemeUrl( $theme_id ){
		return $this->getSwitcherUrl( $theme_id );
	}

	public function getStyleUrl( $theme_id, $style_id ){
		return $this->getSwitcherUrl( $theme_id .'/'. $style_id );
	}

}
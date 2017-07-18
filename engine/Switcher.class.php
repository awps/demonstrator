<?php 
namespace Demonstrator;

class Switcher{
	
	public $switcher_id;
	public $switcher;

	public function __construct( $switcher_id ){
		$this->switcher_id = $switcher_id;

		$this->prepareItems();
	}

	public function getLogoUrl(){
		return !empty( $this->switcher['switcher_logo'] ) ? $this->switcher['switcher_logo'] : false;
	}

	public function getSiteUrl(){
		return !empty( $this->switcher['site_url'] ) ? $this->switcher['site_url'] : false;
	}

	public function getSwitcherTitle(){
		$title = !empty( $this->switcher['label'] ) ? $this->switcher['label'] : $this->switcher['id'];
		return esc_html( $title );
	}

	public function getThemes(){
		return demonstrator_themes( $this->switcher_id );
	}

	public function prepareItems(){
		$items = dts_get_option( 'demonstrator_settings', 'items', false );
		$this->switcher = ( ! empty( $items[ $this->switcher_id ] ) ) ? $items[ $this->switcher_id ] : false;
	}

	public function getThemesGrid(){
		return ( !empty( $this->switcher['themes_grid'] ) ) ? $this->switcher['themes_grid'] : 3;
	}

	public function getStylesGrid(){
		return ( !empty( $this->switcher['styles_grid'] ) ) ? $this->switcher['styles_grid'] : 3;
	}

	public function getThemeColumnsClass(){
		$theme_columns = $this->getThemesGrid();

		return $this->_getColumnsClass( $theme_columns );
	}

	public function getStyleColumnsClass(){
		$style_columns = $this->getStylesGrid();

		return $this->_getColumnsClass( $style_columns );
	}

	protected function _getColumnsClass( $columns ){
		if( 3 == $columns ){
			$class = 'zg-sm-3 zg-xs-2';
		}
		else if( 2 == $columns ){
			$class = 'zg-xs-2';
		}
		else if( 1 == $columns ){
			$class = 'zg-1';
		}
		else{
			$class = 'zg-md-4 zg-sm-3 zg-xs-2';
		}

		return $class;
	}

}
<?php 
/**
 * Field type base class
 *
 * Define field type base class. This class is used only as parent and can't be initiated.
 *
 * @param $field_id The field ID. Required
 * @param $settings The field settings. Optional
 * @return void
 */
namespace Demonstrator;

abstract class AbstractFieldType{
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
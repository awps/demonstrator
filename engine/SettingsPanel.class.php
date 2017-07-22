<?php 
/**
 * Add settings panel
 *
 * Create a settings panel or add fields and sections to an existing one
 *
 * @param $panel_id The panel ID. Required
 * @param $panel_settings The panel settings. Optional
 * @return void
 */

namespace Demonstrator;

class SettingsPanel{
	public $panel_id;
	public $section;

	public function __construct( $panel_id, $title = '', $settings = array()  ){
		$this->panel_id = $panel_id;
		$this->section  = 'general';
		$all_panels = dts_settings_panels();
		if( ! array_key_exists($panel_id, (array) $all_panels) ){
			new InitPanel( $panel_id, $title, $settings );
		}
	}

	public function addField( $field_id, $field_type = 'input', $settings = array(), $priority = 20 ){
		new AddField($this->panel_id, $this->section, $field_id, $field_type, $settings, $priority );
	}

	public function addSection( $section_id, $section_title = false, $description = '', $priority = 20 ){
		// Add new section
		if( !empty($section_title) && trim($section_title) !== false ){
			new AddSection($this->panel_id, $section_id, $section_title, $description, $priority);
		}

		// Switch to this section
		$this->section = $section_id;
	}

}
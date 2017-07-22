<?php 
namespace Demonstrator;

class AddSection{
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
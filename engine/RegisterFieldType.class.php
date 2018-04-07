<?php

namespace Demonstrator;

class RegisterFieldType {
	public $type;
	public $class;

	public function __construct( $type, $class ) {
		$this->type  = $type;
		$this->class = $class;
		add_filter( 'dts_settings_fields_types', array( $this, 'add' ) );
	}

	public function add( $types ) {
		if ( is_array( $types ) && ! array_key_exists( $this->type, $types ) ) {
			$types[ $this->type ] = $this->class;
		}

		return $types;
	}
}

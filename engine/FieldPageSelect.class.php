<?php

namespace Demonstrator;

class FieldPageSelect extends AbstractFieldType {

	public function defaultSettings() {
		return array(
			'show_empty_label' => true,
		);
	}

	public function render() {
		$show_empty_label = $this->getSetting( 'show_empty_label' );
		$option_none      = null;
		if ( ! empty( $show_empty_label ) ) {
			if ( true !== $show_empty_label ) {
				$option_none = $show_empty_label;
			} else {
				$option_none = __( '-- Select page --', 'smk_theme' );
			}
		}

		$args = array(
			'depth'                 => 0,
			'child_of'              => 0,
			'selected'              => absint( $this->getFieldValue() ),
			'echo'                  => 0,
			'name'                  => $this->getFieldName(),
			'id'                    => null,
			'show_option_none'      => $option_none,
			'show_option_no_change' => null,
			'option_none_value'     => null,
		);

		return wp_dropdown_pages( $args );
	}

	public function sanitize( $value ) {
		$page_exists = ( absint( $value ) ) ? get_post( absint( $value ) ) : false;

		return ( ! empty( $page_exists ) ) ? absint( $value ) : '';
	}
}

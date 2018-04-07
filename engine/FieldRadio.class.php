<?php

namespace Demonstrator;

class FieldRadio extends AbstractFieldType {

	public function defaultSettings() {
		return array(
			'display_inline' => false,
		);
	}

	public function render() {
		$options        = $this->getSetting( 'options' );
		$display_inline = $this->getSetting( 'display_inline' );
		$inline         = ( ! empty( $display_inline ) ) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '<br />';
		$value          = $this->getFieldValue();

		$output = '<input name="' . $this->getFieldName() . '" type="hidden" value="" />';

		if ( ! empty( $options ) && is_array( $options ) ) {
			foreach ( $options as $option_value => $option_label ) {
				$checked = ( in_array( $option_value, (array) $value ) ) ? ' checked="checked"' : '';
				$output  .= '<label><input type="radio" value="' . $option_value . '" name="' . $this->getFieldName() . '"' . $checked . ' />' . esc_html( $option_label ) . '</label>' . $inline;
			}
		} else {
			$output .= '<label><input type="radio" value="on" name="' . $this->getFieldName() . '" ' . checked( $this->getFieldValue(), 'on', false ) . ' />' . __( 'On', 'smk_theme' ) . '</label>' . $inline;
			$output .= '<label><input type="radio" value="off" name="' . $this->getFieldName() . '" ' . checked( $this->getFieldValue(), 'off', false ) . ' />' . __( 'Off', 'smk_theme' ) . '</label>' . $inline;
		}

		return $output;
	}
}

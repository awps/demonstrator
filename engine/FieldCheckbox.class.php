<?php

namespace Demonstrator;

class FieldCheckbox extends AbstractFieldType {

	public function defaultSettings() {
		return array(
			'display_inline' => false,
		);
	}

	public function render() {
		$options        = $this->getSetting( 'options' );
		$display_inline = $this->getSetting( 'display_inline' );
		$value          = $this->getFieldValue();

		$output = '<input name="' . $this->getFieldName() . '" type="hidden" value="" />';

		// If is multicheckbox.
		if ( ! empty( $options ) && is_array( $options ) ) {
			foreach ( $options as $option_value => $option_label ) {
				$checked = ( in_array( $option_value, (array) $value ) ) ? ' checked="checked"' : '';
				$inline  = ( ! empty( $display_inline ) ) ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' : '<br />';
				$output  .= '<label><input type="checkbox" value="' . $option_value . '" name="' . $this->getFieldName() . '[]"' . $checked . ' />' . esc_html( $option_label ) . '</label>' . $inline;
			}
		} // Else is single checkbox with 'on' value
		else {
			$output .= '<label><input type="checkbox" value="on" name="' . $this->getFieldName() . '" ' . checked( $this->getFieldValue(), 'on', false ) . ' />' . $this->getSetting( 'label' ) . '</label>';
		}

		return $output;
	}
}

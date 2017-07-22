<?php 
namespace Demonstrator;

class FieldText extends AbstractFieldType{

	public function defaultSettings(){
		return array(
			'allow_safe_html' => false,
			'size' => 'regular',
			'type_attr' => 'text',
		);
	}

	public function render(){
		$size = $this->getSetting( 'size' );
		$type_attr = $this->getSetting( 'type_attr' );
		$type  = 'text';
		$size_class = 'regular-text';

		// Input type
 		if( !empty($type_attr) ){
			if( in_array( $type_attr, array('text', 'password', 'email', 'number', 'url', ) ) ){
				$type = $type_attr;
			}
		}

		// Input width
		if( !empty($size) ){
			if( in_array( $size, array('wide', 'widefat', 'large') ) ){
				$size_class = 'widefat';
			}
			elseif( in_array( $size, array('small', 'small-text', 'mini') ) ){
				$size_class = 'small-text';
			}
			elseif( 'none' == $size ){
				$size_class = '';
			}
		}

		return '<input type="'. $type .'" value="'. esc_html( $this->getFieldValue() ) .'" name="'. $this->getFieldName() .'" class="'. $size_class .'" />';
	}

	public function sanitize( $value ){
		$allow_safe_html = $this->getSetting('allow_safe_html');
		return ( !empty($allow_safe_html) ) ? wp_kses_data( $value ) : sanitize_text_field( $value );
	}
}
<?php 
namespace Demonstrator;

class FieldTextarea extends AbstractFieldType{

	public function defaultSettings(){
		return array(
			'rows' => 5,
			'size' => 'regular',
			'allow_html' => true, // 'safe', 'any' or 'all' or 'unfiltered', 'limited'
		);
	}

	public function render(){
		$size = $this->getSetting( 'size' );
		$rows = absint( $this->getSetting( 'rows' ) );
		$rows = $rows > 0 ? $rows : 5;
		$size_attr = '';

		// Input width
		if( !empty($size) && ($size = trim( $size )) ){
			if( in_array( $size, array('wide', 'widefat', 'large') ) ){
				$size_attr = ' class="widefat"';
			}
			elseif( is_numeric( $size ) ){
				$size_attr = ' cols="'. absint( $size ) .'"';
			}
		}

		return '<textarea name="'. $this->getFieldName() .'"'. $size_attr .' rows="'. $rows .'">'. esc_textarea( $this->getFieldValue() ) .'</textarea>';
	}

	public function sanitize( $value ){
		$allow_html = $this->getSetting('allow_html');

		// Sanitize
		if( 'safe' == $allow_html ){
			$value = wp_kses_post( $value ); // Link in post
		}
		elseif( 'limited' == $allow_html ){
			$value = wp_kses_data( $value ); // Only some inline tags
		}
		elseif( in_array( $allow_html, array('any', 'all', 'unfiltered') ) ){
			$value = $value; // Any HTML tags and attr, even 'script'. RAW
		}
		elseif( $allow_html === false ){
			$value = strip_tags( $value ); // No tags allowed at all
		}
		else{
			$value = wp_kses_post( $value ); // Default. Can use only the tags that are allowed in posts.
		}

		return $value;
	}
}
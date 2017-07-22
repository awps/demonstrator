<?php 
namespace Demonstrator;

class FieldImage extends AbstractFieldType{

	public function defaultSettings(){
		// return array(
		// );
	}

	public function render(){
		$save_img = '';
	
		if( !empty( $this->getFieldValue() ) ){
			$save_img = '<img src="'. esc_html( $this->getFieldValue() ) .'" alt="" />';
		}

		return '<div class="demonstrator_uploader">
			<div class="media-container">'. $save_img .'</div>
			<input type="hidden" value="'. esc_html( $this->getFieldValue() ) .'" name="'. $this->getFieldName() .'" class="media-input" />
			<span class="demonstrator_uploader_btn button">Select image</span>
		</div>';
	}

	// public function sanitize( $value ){
	// 	return esc_url_raw( $value );
	// }
}
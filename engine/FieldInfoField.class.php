<?php 
namespace Demonstrator;

class FieldInfoField extends AbstractFieldType{

	public function render(){
		$content = $this->getSetting( 'content' );

		if( !empty( $content ) ){
			return '<div class="notice inline notice-warning notice-alt"><p>'. $content .'</p></div>';
		}
	}
}
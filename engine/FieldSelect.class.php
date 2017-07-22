<?php 
namespace Demonstrator;

class FieldSelect extends AbstractFieldType{
	public function render(){
		return '<select name="'. $this->getFieldName() .'"></select>';
	}
}
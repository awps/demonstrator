<?php 
namespace Demonstrator;

class FieldThemes extends AbstractFieldType{

	protected $minumum_sections = 0;

	public function defaultSettings(){
		// return array(
		// );
	}

	public function render(){
		$value = $this->getFieldValue();
		$output = '';

		$output .= '<ul class="demonstrator_themes themes-repeatable-block" data-minimum-sections="'. absint($this->minumum_sections) .'">';
			
			$output .= $this->getSingleItem( '__noindex__', array() );
			
			if( ! empty( $value ) ){
				foreach ($value as $theme_id => $theme) {
					$output .= $this->getSingleItem( $theme_id, $theme );
				}
			}
			elseif( $this->minumum_sections > 0 ){
				$output .= $this->getSingleItem( uniqid(), array() );
			}
			

		$output .= '</ul>';

		$output .= '<span class="demonstrator_add_theme form-repeatable-button">'. $this->labelAddNew() .'</span>';

		return $output;
	}

	public function labelAddNew(){
		return __( 'Add theme', 'demonstrator' );
	}

	public function sectionHeader( $label, $theme_id, $category ){
		return '
			<span class="section-theme-title">'. $label .'</span>
			<span class="badge section-theme-id">'. $theme_id .'</span>
			<span class="badge section-theme-category">'. $category .'</span>
			<span class="badge delete-theme cancel-drag" title="'. __('Delete', 'demonstrator') .'">
				<i class="dashicons dashicons-trash"></i>
			</span>
		';
	}

	public function fields( $theme_id, $value ){
		$name              = $this->getFieldName() . '['. $theme_id .']';
		$label             = !empty( $value['label'] ) ? $value['label'] : '';
		$img               = !empty( $value['img'] ) ? $value['img'] : '';
		$purchase_url      = !empty( $value['purchase_url'] ) ? $value['purchase_url'] : '';
		$category          = !empty( $value['category'] ) ? $value['category'] : '';
		// $demo_url          = !empty( $value['demo_url'] ) ? $value['demo_url'] : '';
		$price             = !empty( $value['price'] ) ? $value['price'] : '';
		$short_description = !empty( $value['short_description'] ) ? $value['short_description'] : '';
		$status            = !empty( $value['status'] ) ? $value['status'] : '';
		$styles            = !empty( $value['styles'] ) ? $value['styles'] : '';

		$output = '';
		
		$output .= '<div class="col-sm-3">';

			$output .= $this->tRow( 
				__( 'Image', 'demonstrator' ),
				$this->getUploader( $name . '[img]', $img )
			);

		$output .= '</div>';
		
	
		$output .= '<div class="col-sm-9">';
			$output .= '<div class="zg">';
			
				$output .= $this->tRow( 
					__( 'ID', 'demonstrator' ),
					'<input name="'. $name . '[id]' .'" type="text" class="widefat this-section-theme-id-field" value="'. $theme_id .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Name', 'demonstrator' ),
					'<input name="'. $name . '[label]' .'" type="text" class="widefat this-section-theme-title-field" value="'. $label .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Category', 'demonstrator' ),
					'<input name="'. $name . '[category]' .'" type="text" class="widefat this-section-theme-category-field" value="'. $category .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Price', 'demonstrator' ),
					'<input name="'. $name . '[price]' .'" type="text" class="widefat" value="'. $price .'" />',
					'col-sm-3'
				);

				$output .= $this->tRow( 
					__( 'Purchase URL', 'demonstrator' ),
					'<input name="'. $name . '[purchase_url]' .'" type="text" class="widefat" value="'. $purchase_url .'" />'
				);

				// $output .= $this->tRow( 
				// 	__( 'Demo URL', 'demonstrator' ),
				// 	'<input name="'. $name . '[demo_url]' .'" type="text" class="widefat" value="'. $demo_url .'" />'
				// );

				$output .= $this->tRow( 
					__( 'Short description', 'demonstrator' ),
					'<textarea name="'. $name . '[short_description]' .'" class="widefat">'. esc_textarea( $short_description ) .'</textarea>',
					'col-sm-12'
				);

				$output .= $this->tRow( 
					__( 'Status', 'demonstrator' ),
					'<select name="'. $name . '[status]' .'" class="widefat">
						<option value="published" '. selected( 'published', $status, false ) .'>
							'. __( 'Published', 'demonstrator' ) .'
						</option>
						<option value="unlisted" '. selected( 'unlisted', $status, false ) .'>
							'. __( 'Unlisted', 'demonstrator' ) .'
						</option>
						<option value="private" '. selected( 'private', $status, false ) .'>
							'. __( 'Private', 'demonstrator' ) .'
						</option>
					</select>
					'
				);

				$switcher_id = !empty( $_GET['page'] ) ? str_replace( 'demonstrator_instance_', '', $_GET['page'] ) : false;
				$router = new Router( $switcher_id );

				if( $switcher_id ){
					$output .= $this->tRow( 
						__( 'Demo URL', 'demonstrator' ),
						'<a class="demo_url_link" data-base-url="'. $router->getThemeUrl( '__noindex__' ) .'" href="'. $router->getThemeUrl( $theme_id ) .'" target="_blank">'. $router->getThemeUrl( $theme_id ) .'</a>'
					);
				}

			$output .= '</div>';
		$output .= '</div>';
		
		/* Styles --------------*/
		$output .= '<div class="col-12">';
			$output .= '<div class="zg demonstrator-styles-group">';

				$output .= '<div class="demonstrator-styles-header col-12">'. __( 'Styles', 'demonstrator' ) .'</div>';
				$output .= '<ul class="demonstrator_styles demonstrator-styles-list col-12">';
					
					$output .= $this->getSingleStyle( $theme_id, '__stylenoindex__', array() );
					
					if( ! empty( $styles ) ){
						foreach ($styles as $style_id => $style) {
							$output .= $this->getSingleStyle( $theme_id, $style_id, $style );
						}
					}
					else{
						$output .= $this->getSingleStyle( $theme_id, 'st1', array() );
					}
					

				$output .= '</ul>';

				$output .= '<div class="col-12">';
					$output .= '<span class="demonstrator_add_style form-repeatable-button simple">'. __( 'Add style', 'demonstrator' ) .'</span>';
				$output .= '</div>';

			$output .= '</div>';
		$output .= '</div>';
		/* / Styles --------------*/

		return $output;
	}

	public function getSingleItem( $theme_id, $value ){
		$label             = !empty( $value['label'] ) ? $value['label'] : '';
		$category          = !empty( $value['category'] ) ? $value['category'] : '';
		
		$output = '';

		$list_class = ( '__noindex__' == $theme_id ) ? 'sfa-theme-noindex' : '';

		$output .= '<li class="'. $list_class .'">';
			
			$output .= '<div>'. $this->sectionHeader( $label, $theme_id, $category ) .'</div>'; // nth 1
			
			$output .= '<div class="wp-clearfix">';
				$output .= '<div class="zg">';

					$output .= $this->fields( $theme_id, $value );
				
				$output .= '</div>'; // .zg
			$output .= '</div>'; // .wp-clearfix
		$output .= '</li>';

		return $output;
	}

	public function tRow( $title, $field, $col_class = 'col-12' ){
		$output = '<div class="'. $col_class .'">';
			$output .= '<div class="trow">';
				$output .= '<div class="tlabel">'.$title .'</div>';
				$output .= '<div class="tfield">'. $field .'</div>';
			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	public function getSingleStyle( $theme_id, $style_id, $value ){
		$name  = $this->getFieldName() . '['. $theme_id .'][styles]['. $style_id .']';
		$label = !empty( $value['label'] ) ? $value['label'] : '';
		$img   = !empty( $value['img'] ) ? $value['img'] : '';
		$url   = !empty( $value['url'] ) ? $value['url'] : '';

		$output = '';

		$list_class = ( '__stylenoindex__' == $style_id ) ? 'sfa-style-noindex' : '';

		$output .= '<li class="'. $list_class .'">';
			$output .= '<span class="delete-style" title="'. __('Delete', 'demonstrator') .'"><i class="dashicons dashicons-no"></i></span>';

			$output .= '<div class="zg">';

				$output .= '<div class="col-sm-3">';

					$output .= $this->tRow( 
						__( 'Image', 'demonstrator' ),
						$this->getUploader( $name . '[img]', $img )
					);

				$output .= '</div>';

				$output .= '<div class="col-sm-9">';
					$output .= '<div class="zg">';

						$output .= $this->tRow( 
							__( 'Demo URL', 'demonstrator' ),
							'<input name="'. $name . '[url]' .'" type="text" class="widefat" value="'. $url .'" />'
						);

						$output .= $this->tRow( 
							__( 'Name', 'demonstrator' ),
							'<input name="'. $name . '[label]' .'" type="text" class="widefat this-section-style-title-field" value="'. $label .'" />',
							'col-sm-8'
						);
						
						$output .= $this->tRow( 
							__( 'ID', 'demonstrator' ),
							'<input name="'. $name . '[id]' .'" type="text" class="widefat this-section-style-id-field" value="'. $style_id .'" />',
							'col-sm-4'
						);

					$output .= '</div>';
				$output .= '</div>';

			$output .= '</div>'; // .zg
		$output .= '</li>';

		return $output;
	}

	public function getUploader( $uploader_name, $uploader_value ){
		$img = '';

		if( !empty( $uploader_value ) ){
			$img = '<img src="'. esc_html( $uploader_value ) .'" alt="" />';
		}

		return '<div class="demonstrator_uploader">
			<div class="media-container">'. $img .'</div>
			<input type="hidden" value="'. esc_html( $uploader_value ) .'" name="'. $uploader_name .'" class="media-input" />
			<span class="demonstrator_uploader_btn button">Select image</span>
		</div>';
	}

	public function sanitize( $value ){
		unset( $value['__noindex__'] );

		if( is_array( $value ) ){
			$new_themes = $value;
			foreach ($value as $temp_id => $theme) {
				unset( $new_themes[ $temp_id ]['styles']['__stylenoindex__'] );

				$styles = $new_themes[ $temp_id ]['styles'];
				
				foreach ($styles as $style_id => $style) {
					
					if( empty( $style['url'] ) ){
						unset( $styles[ $style_id ] );
					}
					
					if( ! empty( $style['id'] ) && ! array_key_exists( $style['id'], $styles )  ){
						$styles[ $style['id'] ] = $style;
						unset( $styles[ $style_id ] );
					}

				}

				$new_themes[ $temp_id ]['styles'] = $styles;

				// if( ! array_key_exists( $theme['id'], $new_themes ) ){
				// 	$new_themes[ $theme['id'] ] = $theme;
				// 	unset( $new_themes[ $temp_id ] );
				// }
				$new_themes = $this->replaceArrayKey( $new_themes, $temp_id, $theme['id'] );

			}			
			$value = $new_themes;
		}

		return $value;
	}

	public function replaceArrayKey($array, $key1, $key2){
		$keys = array_keys($array);
		$index = array_search($key1, $keys);

		if ($index !== false) {
			$keys[$index] = $key2;
			$array = array_combine($keys, $array);
		}

		return $array;
	}

}
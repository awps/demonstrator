<?php

namespace Demonstrator;

class FieldSwitchers extends FieldThemes {

	protected $minumum_sections = 1;

	public function labelAddNew() {
		return __( 'Add switcher', 'demonstrator' );
	}

	public function sectionHeader( $label, $switcher_id, $category ) {
		return '
			<span class="section-theme-title">' . $label . '</span>
			<span class="badge section-theme-id">' . $switcher_id . '</span>
			<span class="badge delete-theme cancel-drag" title="' . __( 'Delete', 'demonstrator' ) . '">
				<i class="dashicons dashicons-trash"></i>
			</span>
		';
	}

	public function fields( $switcher_id, $value ) {
		$name                    = $this->getFieldName() . '[' . $switcher_id . ']';
		$label                   = ! empty( $value['label'] ) ? $value['label'] : '';
		$switcher_logo           = ! empty( $value['switcher_logo'] ) ? $value['switcher_logo'] : '';
		$site_url                = ! empty( $value['site_url'] ) ? $value['site_url'] : '';
		$envato_username         = ! empty( $value['envato_username'] ) ? $value['envato_username'] : '';
		$creativemarket_username = ! empty( $value['creativemarket_username'] ) ? $value['creativemarket_username'] : '';
		$img_size_ratio          = ! empty( $value['img_size_ratio'] ) ? $value['img_size_ratio'] : '';

		$themes_grid = ! empty( $value['themes_grid'] ) ? absint( $value['themes_grid'] ) : 3;
		$themes_grid = ( $themes_grid > 0 && $themes_grid < 5 ) ? $themes_grid : 3;
		$styles_grid = ! empty( $value['styles_grid'] ) ? absint( $value['styles_grid'] ) : 3;
		$styles_grid = ( $styles_grid > 0 && $styles_grid < 5 ) ? $styles_grid : 3;

		$output = '';

		$output .= '<div class="col-sm-3">';

		$output .= $this->tRow(
			__( 'Switcher logo', 'demonstrator' ),
			$this->getUploader( $name . '[switcher_logo]', $switcher_logo )
		);

		$output .= '</div>';


		$output .= '<div class="col-sm-9">';
		$output .= '<div class="zg">';

		$output .= $this->tRow(
			__( 'Endpoint ID', 'demonstrator' ),
			'<input name="' . $name . '[id]' . '" type="text" class="widefat this-section-theme-id-field" value="' . $switcher_id . '" />',
			'col-sm-3'
		);

		$output .= $this->tRow(
			__( 'Label', 'demonstrator' ),
			'<input name="' . $name . '[label]' . '" type="text" class="widefat this-section-theme-title-field" value="' . $label . '" />',
			'col-sm-3'
		);

		$output .= $this->tRow(
			__( 'Your site URL', 'demonstrator' ),
			'<input name="' . $name . '[site_url]' . '" type="text" class="widefat" value="' . $site_url . '" />',
			'col-sm-6'
		);

		$output .= $this->tRow(
			__( 'Themes grid', 'demonstrator' ),
			'
					<label>
						<input type="radio" value="4" ' . checked( '4', $themes_grid, false ) . ' name="' . $name . '[themes_grid]' . '">
						' . __( '4 columns', 'demonstrator' ) . '
					</label>
					<label>
						<input type="radio" value="3" ' . checked( '3', $themes_grid, false ) . ' name="' . $name . '[themes_grid]' . '">
						' . __( '3 columns', 'demonstrator' ) . '
					</label>
					<label>
						<input type="radio" value="2" ' . checked( '2', $themes_grid, false ) . ' name="' . $name . '[themes_grid]' . '">
						' . __( '2 columns', 'demonstrator' ) . '
					</label>
					<label>
						<input type="radio" value="1" ' . checked( '1', $themes_grid, false ) . ' name="' . $name . '[themes_grid]' . '">
						' . __( '1 column', 'demonstrator' ) . '
					</label>
					',
			'col-sm-6'
		);

		$output .= $this->tRow(
			__( 'Styles grid', 'demonstrator' ),
			'
					<label>
						<input type="radio" value="4" ' . checked( '4', $styles_grid, false ) . ' name="' . $name . '[styles_grid]' . '">
						' . __( '4 columns', 'demonstrator' ) . '
					</label>
					<label>
						<input type="radio" value="3" ' . checked( '3', $styles_grid, false ) . ' name="' . $name . '[styles_grid]' . '">
						' . __( '3 columns', 'demonstrator' ) . '
					</label>
					<label>
						<input type="radio" value="2" ' . checked( '2', $styles_grid, false ) . ' name="' . $name . '[styles_grid]' . '">
						' . __( '2 columns', 'demonstrator' ) . '
					</label>
					<label>
						<input type="radio" value="1" ' . checked( '1', $styles_grid, false ) . ' name="' . $name . '[styles_grid]' . '">
						' . __( '1 column', 'demonstrator' ) . '
					</label>
					',
			'col-sm-6'
		);

		$output .= $this->tRow(
			__( 'Envato Username', 'demonstrator' ),
			'<input name="' . $name . '[envato_username]' . '" type="text" class="widefat" value="' . $envato_username . '" />',
			'col-sm-4'
		);

		$output .= $this->tRow(
			__( 'CreativeMarket Username', 'demonstrator' ),
			'<input name="' . $name . '[creativemarket_username]' . '" type="text" class="widefat" value="' . $creativemarket_username . '" />',
			'col-sm-4'
		);


		$select = '<select name="' . $name . '[img_size_ratio]' . '" class="widefat">';
		$select .= $this->option( 'auto', $img_size_ratio, __( ' Auto ', 'demonstrator' ) );
		$select .= $this->option( 'ratio-1-1', $img_size_ratio, __( ' 1:1 ', 'demonstrator' ) );
		$select .= $this->option( 'ratio-4-3', $img_size_ratio, __( ' 4:3 ', 'demonstrator' ) );
		$select .= $this->option( 'ratio-16-9', $img_size_ratio, __( ' 16:9 ', 'demonstrator' ) );
		$select .= $this->option( 'ratio-16-10', $img_size_ratio, __( ' 16:10 ', 'demonstrator' ) );
		$select .= $this->option( 'ratio-1-2', $img_size_ratio, __( ' 1:2 ', 'demonstrator' ) );
		$select .= $this->option( 'ratio-3-4', $img_size_ratio, __( ' 3:4 ', 'demonstrator' ) );
		$select .= $this->option( 'ratio-9-16', $img_size_ratio, __( ' 9:16 ', 'demonstrator' ) );
		$select .= $this->option( 'ratio-10-16', $img_size_ratio, __( ' 10:16 ', 'demonstrator' ) );
		$select .= '</select>';

		$output .= $this->tRow(
			__( 'Image aspect ratio', 'demonstrator' ),
			$select,
			'col-3'
		);

		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	public function sanitize( $value ) {
		unset( $value['__noindex__'] );

		if ( is_array( $value ) ) {
			$new_themes = $value;
			foreach ( $value as $temp_id => $theme ) {

				$new_themes = $this->replaceArrayKey( $new_themes, $temp_id, $theme['id'] );

				if ( $temp_id !== $theme['id'] ) {
					$old_items = get_option( 'demonstrator_instance_' . $temp_id, null );
					if ( isset( $old_items ) ) {
						update_option( 'demonstrator_instance_' . $theme['id'], $old_items );
						delete_option( 'demonstrator_instance_' . $temp_id );
					}
				}

			}
			$value = $new_themes;
		}

		return $value;
	}

	protected function option( $value, $check_for_value, $label ) {
		return '<option value="' . $value . '" ' . selected( $check_for_value, $value, false ) . '>' . $label . '</option>';
	}

}

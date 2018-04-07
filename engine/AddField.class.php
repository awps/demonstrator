<?php

namespace Demonstrator;

class AddField {
	public $panel_id;
	public $section_id;
	public $field_id;
	public $field_type;
	public $settings;
	public $description;

	public function __construct( $panel_id, $section_id, $field_id, $field_type = 'input', $settings = array(), $priority = 20 ) {
		$this->panel_id   = $panel_id;
		$this->section_id = $section_id;
		$this->field_id   = $field_id;
		$this->field_type = $field_type;
		$this->settings   = $settings;

		add_filter( $this->panel_id . '_panel_fields', array( $this, 'add' ), $priority );
		add_action( $this->panel_id . '_' . $section_id . '_dts_fields', array( $this, 'render' ), $priority );
	}

	public function getSettings() {
		$defaults               = array(
			'title'       => '',
			'description' => '',
			'default'     => '',
		);
		$settings               = (array) $this->settings;
		$settings['field_type'] = $this->field_type;

		return wp_parse_args( $settings, $defaults );
	}

	public function add( $fields ) {
		if ( ! array_key_exists( $this->field_id, (array) $fields ) ) {
			$fields[ $this->field_id ] = $this->getSettings();
		}

		return $fields;
	}

	public function getField() {
		$settings     = $this->getSettings();
		$known_fields = dts_settings_fields_types();
		$type         = $settings['field_type'];
		if ( array_key_exists( $type, $known_fields ) ) {
			if ( isset( $known_fields[ $type ] ) && class_exists( $known_fields[ $type ] ) ) {
				$class = $known_fields[ $type ];
				if ( method_exists( $class, 'render' ) ) {
					$final_obj = new $class( $this->panel_id, $this->field_id, $settings );

					return $final_obj->render();
				}
			}
		}
	}

	public function render() {
		$s           = $this->getSettings();
		$full_row    = ! empty( $s['full_row'] ) ? 'full-row' : '';
		$description = ! empty( $s['description'] ) ? '<p class="description">' . $s['description'] . '</p>' : '';

		echo '<tr id="' . $this->panel_id . '_' . $this->field_id . '" class="' . $full_row . '">
			<th scope="row">' . $s['title'] . '</th>
			<td>' . $this->getField() . $description . '</td>
		</tr>';
	}
}

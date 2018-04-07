<?php

namespace Demonstrator;

class FieldInfoBoxTable extends AbstractFieldType {

	public function render() {
		$content = $this->getSetting( 'content' );
		$table   = '';

		if ( is_array( $content ) ) {
			$table .= '<table class="wp-list-table widefat fixed striped" style="max-width: 800px;">';
			foreach ( $content as $key => $value ) {
				$table .= '<tr>
					<td><strong>' . $key . '</strong></td>
					<td>' . $value . '</td>
				</tr>';
			}
			$table .= '</table>';
		}

		return $table;
	}
}

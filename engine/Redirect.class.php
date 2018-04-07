<?php

namespace Demonstrator;

class Redirect {

	public function __construct() {
		add_action( 'init', array( $this, 'buy' ), 1 );
	}

	public function buy() {
		if ( is_admin() ) {
			return;
		}

		if ( empty( $_GET['buy'] ) ) {
			return;
		}

		$buy_key = $_GET['buy'];
		$keys    = explode( ':', $buy_key );

		if ( ! empty( $keys[0] ) && ! empty( $keys[1] ) ) {

			$switcher_id = sanitize_key( $keys[0] );
			$theme_id    = sanitize_key( $keys[1] );
			$themes      = demonstrator_themes( $switcher_id );

			if ( ! empty( $themes ) && array_key_exists( $theme_id, $themes ) ) {
				wp_redirect( $themes[ $theme_id ]['purchase_url'] );
				exit;
			}

		}
	}

}

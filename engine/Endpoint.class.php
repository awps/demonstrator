<?php

namespace Demonstrator;

class Endpoint {

	public $endpoints = false;

	public function __construct() {
		$this->prepareEndpoints();

		add_action( 'init', array( $this, 'addEndpoint' ) );
		add_filter( 'template_redirect', array( $this, 'templateInclude' ) );
	}

	public function prepareEndpoints() {
		$switchers = dts_get_option( 'demonstrator_settings', 'items', array() );

		if ( ! empty( $switchers ) ) {
			$this->endpoints = array_keys( $switchers );
		}
	}

	/*
	-------------------------------------------------------------------------------
	Apply endpoint
	-------------------------------------------------------------------------------
	*/
	public function addEndpoint() {
		if ( ! empty( $this->endpoints ) ) {
			foreach ( $this->endpoints as $endpoint ) {
				add_rewrite_endpoint( $endpoint, EP_ALL );
			}
		}
	}

	public function templateInclude() {
		if ( is_admin() ) {
			return;
		}

		$ep_is_set         = false;
		$current_query_var = false;
		if ( ! empty( $this->endpoints ) ) {
			foreach ( $this->endpoints as $endpoint ) {
				$ep = get_query_var( $endpoint, null );

				if ( ! isset( $ep ) ) {
					continue;
				} else {
					$ep_is_set         = true;
					$current_query_var = $endpoint;
					break;
				}
			}
		}

		if ( ! $ep_is_set ) {
			return;
		}

		// Dirty! But it's better than doing the loop again in template.php ...
		$GLOBALS['demonstrator_current_query_var'] = $current_query_var;

		include DEMONSTRATOR_PATH . 'components/template.php';
		exit;
	}

}

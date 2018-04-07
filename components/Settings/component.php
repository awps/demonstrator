<?php
/* No direct access allowed!
---------------------------------*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Create endpoints
------------------------*/
new Demonstrator\Endpoint;

new Demonstrator\Redirect;


/* Add settings fields
---------------------------*/
add_action( 'demonstrator:init', function () {
	$panel = new Demonstrator\SettingsPanel( 'demonstrator_settings', __( 'Demonstrator', 'demonstrator' ) );

	$panel->addSection( 'switchers', __( 'Switchers', 'demonstrator' ) );

	$panel->addField( 'items', 'switchers', array(
		'full_row' => true,
	) );

} );


/* Add settings fields
---------------------------*/
add_action( 'demonstrator:init', function () {

	$switchers = dts_get_option( 'demonstrator_settings', 'items', array() );

	if ( ! empty( $switchers ) ) {

		foreach ( $switchers as $switcher_id => $switcher ) {

			$panel = new Demonstrator\SettingsPanel( 'demonstrator_instance_' . $switcher_id, $switcher['label'], array(
				'parent_slug' => 'demonstrator_settings',
			) );

			$panel->addSection( 'theme-items', __( 'Themes', 'demonstrator' ) );

			$panel->addField( 'items', 'themes', array(
				'full_row' => true,
			) );

		}
	}

} );

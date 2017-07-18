<?php 
/* No direct access allowed!
---------------------------------*/
if ( ! defined( 'ABSPATH' ) ) exit;

/* Create settings page
----------------------------*/
require_once DEMONSTRATOR_PATH .'components/Settings/panel.php';

/* Create endpoints
------------------------*/
new Demonstrator\Endpoint;

new Demonstrator\Redirect;


/* Add settings fields
---------------------------*/
add_action( 'demonstrator:init', function(){
	$panel = new Dts_Settings_Panel( 'demonstrator_settings', __('Demonstrator', 'demonstrator'));

	$panel->addSection( 'switchers', __( 'Switchers', 'demonstrator' ) );

	$panel->addField( 'items', 'switchers', array(
		'full_row' => true,
	));

} );


/* Add settings fields
---------------------------*/
add_action( 'demonstrator:init', function(){

	$switchers = dts_get_option( 'demonstrator_settings', 'items', array() );

	if( !empty($switchers) ){

		foreach ($switchers as $switcher_id => $switcher) {

			$panel = new Dts_Settings_Panel( 'demonstrator_instance_' . $switcher_id, $switcher[ 'label' ], array(
				'parent_slug' => 'demonstrator_settings',
			) );

			$panel->addSection( 'theme-items', __( 'Themes', 'demonstrator' ) );

			$panel->addField( 'items', 'themes', array(
				'full_row' => true,
			));

		}
	}

} );
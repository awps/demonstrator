<?php 
/* No direct access allowed!
---------------------------------*/
if ( ! defined( 'ABSPATH' ) ) exit;

/* Create settings page
----------------------------*/
require_once DEMONSTRATOR_PATH .'components/Page/panel.php';

/* Create endpoints
------------------------*/
new Demonstrator\Endpoint;

/* Add settings fields
---------------------------*/
add_action( 'demonstrator:init', function(){
	$panel = new Dts_Settings_Panel( 'demonstrator_options', __('Demonstrator', 'demonstrator') );

	$panel->addSection( 'theme-items', __( 'Themes', 'demonstrator' ) );

	$panel->addField( 'items', 'themes', array(
		'title' => __('Items', 'demonstrator'),
	));

	$panel->addSection( 'general settings', __( 'General settings', 'demonstrator' ) );

	$panel->addField( 'brand_logo', 'image', array(
		'title' => __('Logo', 'demonstrator'),
		'description' => __('The best image has a height of exactly 50px', 'demonstrator'),
	));

	$panel->addField( 'brand_site_url', 'text', array(
		'title' => __('Your site URL', 'demonstrator'),
	));

	$panel->addField( 'envato_username', 'text', array(
		'title' => __('Envato Username', 'demonstrator'),
		'description' => __('Enter your Envato that should be added to URL as a referal.', 'demonstrator'),
	));

	$panel->addField( 'creativemarket_username', 'text', array(
		'title' => __('CreativeMarket Username', 'demonstrator'),
		'description' => __('Enter your CreativeMarket that should be added to URL as a referal.', 'demonstrator'),
	));

	$panel->addField( 'theme_columns', 'radio', array(
		'title' => __('Theme columns', 'demonstrator'),
		'default' => '4',
		'options' => array(
			'4' => '4',
			'3' => '3',
			'2' => '2',
			'1' => '1',
		),
		'display_inline' => true,
	));

	$panel->addField( 'style_columns', 'radio', array(
		'title' => __('Style columns', 'demonstrator'),
		'default' => '4',
		'options' => array(
			'4' => '4',
			'3' => '3',
			'2' => '2',
			'1' => '1',
		),
		'display_inline' => true,
	));

} );
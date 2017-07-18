<?php 
function _activate_demonstrator(){
	$switchers = array ( 
		'items' => array (
			'themes' => array (
				'switcher_logo' => '',
				'id' => 'demo',
				'label' => 'Themes Demo',
				'site_url' => '',
				'themes_grid' => '3',
				'styles_grid' => '3',
				'envato_username' => '',
				'creativemarket_username' => '',
			),
	  ),
	);

	$themes = array (
		'items' => array (
			'gustoswp' => array (
				'img' => 'https://s3.envato.com/files/226458785/images/001-banner.__large_preview.jpg',
				'id' => 'gustoswp',
				'label' => 'Gustos',
				'category' => 'WordPress',
				'price' => '53$',
				'purchase_url' => 'https://themeforest.net/item/gustos-communitydriven-recipes-wordpress-theme/10408604',
				'short_description' => '',
				'status' => 'published',
				'styles' => array (
					'style1' => array (
						'img' => '',
						'url' => 'http://gustoswp.themes.market/',
						'label' => '',
						'id' => 'style1',
					),
				),
			),
		),
	);


	$opt_sw = get_option( 'demonstrator_settings' );
	$opt_th = get_option( 'demonstrator_instance_themes' );

	if( empty( $opt_sw ) ){
		update_option( 'demonstrator_settings', $switchers );
	}

	if( empty( $opt_th ) ){
		update_option( 'demonstrator_instance_themes', $themes );
	}

	flush_rewrite_rules( true );
}

register_activation_hook( DEMONSTRATOR_PLUGIN_FILE, '_activate_demonstrator' );
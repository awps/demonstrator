<?php 
/**
 * Get a option from DB
 *
 * Get the saved options from DB or a single option from the multidimensional array.
 *
 * @param string $panel_id The registered panel. It's the ID registered with Dts_Settings_Panel( $id, ... )
 * @param string $option_name The field ID. It's the ID registered with $panel->addField( $id, ... )
 * @param string $default Default value to show if the option is not saved in DB.
 * @return mixed
 */
function dts_get_option( $panel_id, $option_name = false, $default = false ){
	$options = get_option( $panel_id );

	// If is the user does not indicate the option name to retrieve, return all options.
	if( empty($option_name) ){
		return $options;
	}

	// If the option is saved return its value.
	if( isset( $options[ $option_name ] ) ){
		return $options[ $option_name ];
	}

	// If the option is not saved yet and a default is set by user return it.
	elseif( $default !== false ){
		return $default;
	}

	// If all above fails, return false. This means, the option is not saved in DB.
	else{
		return false;
	}
}

function demonstrator_themes( $switcher_id ){
	$themes = dts_get_option( 'demonstrator_instance_' . $switcher_id, 'items' );

	if( !empty( $themes ) ){
		$new_themes = $themes;

		foreach ($themes as $theme_id => $theme) {
			$theme = apply_filters( 'demonstrator_before_theme_parse', $theme, $theme_id );
			
			if( 'private' == $theme['status'] && ! current_user_can( 'manage_options' ) ) {
				unset( $new_themes[ $theme_id ] );
				continue;
			}
			
			// If he name is not set, assign the ID
			if( empty( $theme['label'] ) ) {
				$theme['label'] = $theme_id;
			}
			
			$parser = new Demonstrator\ThemeParser(  $switcher_id, $theme_id, $theme );

			$parser->parsePurchaseUrl()->parseShortBuyUrl()->filterStyles()->parseDemoUrl();

			$new_themes[ $theme_id ] = apply_filters( 'demonstrator_theme_parse', $parser->getTheme(), $theme_id );
		}

		$themes = $new_themes;
	}

	return apply_filters( 'demonstrator_themes', $themes );
}
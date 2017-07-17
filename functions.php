<?php 
function demonstrator_themes(){
	$themes = dts_get_option( 'demonstrator_options', 'items' );

	if( !empty( $themes ) ){

		$new_themes = $themes;
		
		foreach ($themes as $theme_id => $theme) {
			$theme = apply_filters( 'demonstrator_before_theme_parse', $theme, $theme_id );

			$parser = new Demonstrator\ThemeParser( $theme_id, $theme );

			$parser->parsePurchaseUrl()->parseShortBuyUrl()->filterStyles()->parseDemoUrl();

			$new_themes[ $theme_id ] = apply_filters( 'demonstrator_theme_parse', $parser->getTheme(), $theme_id );
		}

		$themes = $new_themes;

	}

	return apply_filters( 'demonstrator_themes', $themes );
}

function demonstrator_get_columns_class( $theme_columns ){
	if( 3 == $theme_columns ){
		$class = 'zg-sm-3 zg-xs-2';
	}
	else if( 2 == $theme_columns ){
		$class = 'zg-xs-2';
	}
	else if( 1 == $theme_columns ){
		$class = 'zg-1';
	}
	else{
		$class = 'zg-md-4 zg-sm-3 zg-xs-2';
	}

	return $class;
}

function demonstrator_get_endpoint_url( $endpoint, $value = '', $permalink = '' ){
	if ( ! $permalink ) {
		$permalink = get_permalink();
	}

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $permalink, '?' ) ) {
			$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
			$permalink    = current( explode( '?', $permalink ) );
		} 
		else {
			$query_string = '';
		}

		$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
	} 
	else {
		$url = add_query_arg( $endpoint, $value, $permalink );
	}

	return apply_filters( 'demonstrator_get_endpoint_url', $url, $endpoint, $value, $permalink );
}

function demonstrator_get_theme_url( $theme_id ){
	$endpoint = get_option( 'demonstrator_permalink_slug', false );

	$switcher_url = home_url();
	if( !empty( $ndpoint ) ){
		$switcher_url = demonstrator_get_endpoint_url( $endpoint, '', $switcher_url );
	}

	return apply_filters( 
		'demonstrator_theme_url', 
		esc_url_raw( add_query_arg( 'theme', $theme_id, $switcher_url ) ), 
		$theme_id 
	);
}
<?php 
function demonstrator_themes(){
	$themes = dts_get_option( 'demonstrator_options', 'items' );

	if( !empty( $themes ) ){
		$themes = array_map( function( $theme ){
			
			$envato_username         = dts_get_option( 'demonstrator_options', 'envato_username' );
			$creativemarket_username = dts_get_option( 'demonstrator_options', 'creativemarket_username' );
			$purl                    = $theme['purchase_url'];

			// Envato support
			if( 
				!empty( $envato_username ) && (
					stripos($purl, 'themeforest.net') !== false || 
					stripos($purl, 'codecanyon.net') !== false 
				)
			){
				$theme['purchase_url'] = esc_url_raw( add_query_arg( array(
					'ref' => $envato_username
				), $purl ) );
			}

			// CreativeMarket support
			if( 
				!empty( $creativemarket_username ) && (
					stripos($purl, 'creativemarket.com') !== false || 
					stripos($purl, 'crmrkt.com') !== false || 
					stripos($purl, 'crtv.mk') !== false 
				)
			){
				$theme['purchase_url'] = esc_url_raw( add_query_arg( array(
					'u' => $creativemarket_username
				), $purl ) );
			}

			$theme[ 'short_buy_url' ] = esc_url_raw( add_query_arg( 'buy', $theme['id'], home_url() ) );

			// Build demo url
			if( !empty($theme['styles']) ){
				
				// Remove all styles that does not have a demo URL
				// $styles = array_filter( $theme['styles'], 'demonstrator_filter_style' );
				$styles = demonstrator_filter_styles( $theme['styles'] );
				
				// Remove original styles array. We don't need it anymore.
				unset($theme['styles']);
				
				// If still have styles
				if( !empty($styles) ){

					// Have only one style
					if( count($styles) == 1 ){
						
						// Reset the array. Just to be safe.
						reset( $styles );

						$demo_url = current( $styles );
						$demo_url = $demo_url['url'];

						$theme['demo_url'] = $demo_url;
					}

					// Have more than one style
					else{
						$theme['demo_url'] = $styles;
					}

				}
				else{
					$theme['demo_url'] = false;
				}
			}

			return $theme;

		}, $themes );
	}

	return $themes;
}

function demonstrator_filter_styles( $styles ){
	if( !empty( $styles ) ){
		foreach ($styles as $style) {
			if( $style['url'] == '' ){
				unset( $styles[ $style['id'] ] );
			}
		}
		return $styles;
	}
	else{
		return false;
	}
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


add_filter( 'init', 'demonstrator_go_to_purchase' );
function demonstrator_go_to_purchase() {
	if( ! empty( $_GET['buy'] ) ){
		$theme_id = sanitize_key( $_GET['buy'] );
		$themes = demonstrator_themes();
		
		if( !empty($themes) && array_key_exists($theme_id, $themes) ){
			wp_redirect( $themes[ $theme_id ][ 'purchase_url' ] );
			exit;
		}
	}
}
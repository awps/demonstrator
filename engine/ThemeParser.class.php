<?php 
namespace Demonstrator;

class ThemeParser {
	
	protected $theme_id;

	protected $theme;

	protected $purchase_url;

	protected $styles;

	public function __construct( $theme_id, $theme ){
		$this->theme_id     = $theme_id;
		$this->theme        = $theme;
		$this->purchase_url = !empty( $this->theme['purchase_url'] ) ?  $this->theme['purchase_url'] : '';
		$this->styles       = !empty( $this->theme['styles'] ) ?  $this->theme['styles'] : '';
	}

	public function getTheme(){
		return $this->theme;
	}

	public function parseDemoUrl(){

		// Remove original styles array. We don't need it anymore.
		unset($this->theme['styles']);
		
		// If still have styles
		if( !empty($this->styles) ){

			// Have only one style
			if( count($this->styles) == 1 ){
				
				// Reset the array. Just to be safe.
				reset( $this->styles );

				$demo_url = current( $this->styles );
				$demo_url = $demo_url['url'];

				$this->theme['demo_url'] = $demo_url;
			}

			// Have more than one style
			else{
				$this->theme['demo_url'] = $this->styles;
			}

		}
		else{
			$this->theme['demo_url'] = false;
		}

		return $this;
	}

	public function filterStyles(){
		if( !empty( $this->styles ) ){
			foreach ($this->styles as $style) {
				if( $style['url'] == '' ){
					unset( $this->styles[ $style['id'] ] );
				}
			}
		}
		else{
			$this->styles = false;
		}

		return $this;
	}

	public function parseShortBuyUrl(){
		$this->theme[ 'short_buy_url' ] = esc_url_raw( add_query_arg( 'buy', $this->theme_id, home_url() ) );

		return $this;
	}

	public function parsePurchaseUrl(){
		$this->purchase_url = apply_filters( 'demonstrator_before_parse_purchase_url', $this->purchase_url );

		$this->doEnvatoReferal()->doCreativeMarketReferal();

		$this->purchase_url = apply_filters( 'demonstrator_parse_purchase_url', $this->purchase_url );

		return $this;
	}

	public function doEnvatoReferal(){
		$envato_username = dts_get_option( 'demonstrator_options', 'envato_username' );
		
		if( 
			!empty( $envato_username ) && (
				stripos($this->purchase_url, 'themeforest.net') !== false || 
				stripos($this->purchase_url, 'codecanyon.net') !== false 
			)
		){
			$this->theme['purchase_url'] = esc_url_raw( add_query_arg( array(
				'ref' => $envato_username
			), $this->purchase_url ) );
		}

		return $this;
	}

	public function doCreativeMarketReferal(){
		$creativemarket_username = dts_get_option( 'demonstrator_options', 'creativemarket_username' );

		if( 
			!empty( $creativemarket_username ) && (
				stripos($this->purchase_url, 'creativemarket.com') !== false || 
				stripos($this->purchase_url, 'crmrkt.com') !== false || 
				stripos($this->purchase_url, 'crtv.mk') !== false 
			)
		){
			$this->theme['purchase_url'] = esc_url_raw( add_query_arg( array(
				'u' => $creativemarket_username
			), $this->purchase_url ) );
		}

		return $this;
	}

}
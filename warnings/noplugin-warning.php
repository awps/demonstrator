<?php 
require_once DEMONSTRATOR_PATH . 'warnings/abstract-warning.php';

class DEMONSTRATOR_NoPlugin_Warning extends DEMONSTRATOR_Astract_Warning{

	public function notice(){
		
		$output = '';
		
		if( count( $this->data ) > 1 ){
			$message = __( 'Please install and activate the following plugins:', 'demonstrator' );
		}
		else{
			$message = __( 'Please install and activate this plugin:', 'demonstrator' );
		}

		$output .= '<h2>' . $message .'</h2>';


		$output .= '<ul class="demonstrator-required-plugins-list">';
			foreach ($this->data as $plugin_slug => $plugin) {
				$plugin_name = '<div class="demonstrator-plugin-info-title">'. $plugin['plugin_name'] .'</div>';

				if( !empty( $plugin['plugin_uri'] ) ){
					$button = '<a href="'. esc_url_raw( $plugin['plugin_uri'] ) .'" class="demonstrator-plugin-info-button" target="_blank">'. __( 'Get the plugin', 'demonstrator' ) .'</a>';
				}
				else{
					$button = '<a href="#" onclick="return false;" class="demonstrator-plugin-info-button disabled">'. __( 'Get the plugin', 'demonstrator' ) .'</a>';
				}

				$output .= '<li>'. $plugin_name . $button .'</li>';
			}
		$output .= '</ul>';

		return $output;
	}

}
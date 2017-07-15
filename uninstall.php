<?php 
/**
 * Uninstall
 *
 * Clean-up some data when the plugin is uninstalled.
 * See: https://developer.wordpress.org/plugins/the-basics/uninstall-methods/
 *
 */

// If uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// else, execute some code on plugin uninstallation
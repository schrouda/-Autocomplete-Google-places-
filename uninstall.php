<?php
/**
 * Uninstall routine for Autocomplete Google places.
 *
 * Runs only when the plugin is deleted from the WordPress admin. Removes the
 * option created by the plugin so no orphaned data is left behind.
 */

// Exit if this file is not called by WordPress during uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'google-places' );

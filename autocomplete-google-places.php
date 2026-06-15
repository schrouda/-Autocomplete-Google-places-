<?php
/**
 * Plugin Name:       Autocomplete Google places 
 * Plugin URI:        https://lecoinapero.com/autocomplete-google-places/
 * Description:       This plugin will help you to add autocomplete google addres features by using google place api, Auto-fill city and postcode when type address.
 * Version:           3.0.0
 * Requires at least: 5.6
 * Tested up to: 7.0
 * Requires PHP:      7.4
 * Author:            Kais chrouda
 * Author URI:        https://lecoinapero.com/kaischrouda/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       autocomplete-google-places
 * Domain Path:       /languages
 */
 
// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

// Prohibit exposing any info when called directly
if ( !function_exists( 'add_action' ) ) {

	echo 'Don\'t do that!';
	exit;
}

define( 'AUTOCOMPLETE_GP_VERSION', '3.0.0' );

// Including setting file
include('admin_options.php');

// Define certain plugin variables as constants.
if ( ! defined( 'AUTOCOMPLETE_GP_ABSPATH' ) ) {
	define( 'AUTOCOMPLETE_GP_ABSPATH_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'AUTOCOMPLETE_GP_ABSPATH' ) ) {
	define( 'AUTOCOMPLETE_GP_ABSPATH_URL', plugin_dir_url( __FILE__ ) );
}

// Including the scripts
add_action( 'wp_enqueue_scripts', 'autocomplete_gp_google_scripts_enqueue' );
function autocomplete_gp_google_scripts_enqueue() {
	$google_api_key = autocomplete_gp_get_option( 'google_place_api' );

	// Without a configured API key there is nothing useful to load, and we must
	// never fall back to a hardcoded key (it would leak a shared credential).
	if ( empty( $google_api_key ) ) {
		return;
	}

	wp_enqueue_script( 'autocompletegp-script', AUTOCOMPLETE_GP_ABSPATH_URL . 'js/autocomplete.js', array(), AUTOCOMPLETE_GP_VERSION, true );

	$maps_src = add_query_arg(
		array(
			'key'       => rawurlencode( $google_api_key ),
			'libraries' => 'places',
		),
		'https://maps.googleapis.com/maps/api/js'
	);
	wp_enqueue_script( 'google-maps', esc_url_raw( $maps_src ), array( 'autocompletegp-script' ), '1.0', true );
}
// Putting on wp head
add_action('wp_head','autocomplete_gp_set_style');
function autocomplete_gp_set_style(){

?>

	<style>
.pac-container:after{
	display:none !important;
}
.pac-item{
	cursor:pointer;
}
	</style>
<?php }

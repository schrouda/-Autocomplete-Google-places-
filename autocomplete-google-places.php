<?php
/**
 * Plugin Name:       Autocomplete Google places 
 * Plugin URI:        https://lecoinapero.com/autocomplete-google-places/
 * Description:       This plugin will help you to add autocomplete google addres features by using google place api, Auto-fill city and postcode when type address.
 * Version:           1.3.4
 * Requires at least: 5.6
 * Tested up to: 5.9
 * Author:            Kais chrouda
 * Author URI:        https://lecoinapero.com/kaischrouda/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       autocomplete-google-places 
 * Domain Path:       /languages
 */
 
// Prohibit exposing any info when called directly
if ( !function_exists( 'add_action' ) ) {

	echo 'Don\'t do that!';
	exit;
}

// Including setting file
include('admin_options.php');

//
define( 'AUTOCOMPLETE_GP_VERSION', '1.3.4' );

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

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
	  wp_enqueue_script('autocompletegp-script',AUTOCOMPLETE_GP_ABSPATH_URL.'js/autocomplete.js',array('jquery-core','jquery'),'',true);
   	  wp_enqueue_script('google-maps','https://maps.googleapis.com/maps/api/js?key='.(!empty($google_api_key) ? $google_api_key : 'AIzaSyAKkd9GnMadV3lpKNMsiKVAVcdZ98eDJ0g').'&libraries=places',array('jquery-core','jquery','autocompletegp-script'),'1.0',true);
}
//
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_support_link' );
function add_support_link( $links ) {
   $links[] = '<a href="https://www.lecoinapero.com/autocomplete-google-places">Support</a>';
   return $links;
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

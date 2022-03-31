<?php
/**
 * This snippet has been updated to reflect the official supporting of options pages by CMB2
 * in version 2.2.5.
 *
 * If you are using the old version of the options-page registration,
 * it is recommended you swtich to this method.
 */
include('library/init.php');
add_action( 'cmb2_admin_init', 'autocomplete_gp_register_theme_options' );
/**
 * Hook in and register a metabox to handle a theme options page and adds a menu item.
 */
function autocomplete_gp_register_theme_options() {
	/**
	 * Registers options page menu item and form.
	 */
	$cmb_options = new_cmb2_box( array(
		'id'           => 'autocomplete_gp_option',
		'title'        => esc_html__( 'Google Places', 'autocomplete_gp' ),
		'object_types' => array( 'options-page' ),
		
		/*
		 * The following parameters are specific to the options-page box
		 * Several of these parameters are passed along to add_menu_page()/add_submenu_page().
		 */
		'option_key'      => 'google-places', // The option key and admin menu page slug.
		'icon_url'        => 'dashicons-location-alt', // Menu icon. Only applicable if 'parent_slug' is left empty.
		// 'menu_title'      => esc_html__( 'Options', 'autocomplete_gp' ), // Falls back to 'title' (above).
		// 'parent_slug'     => 'themes.php', // Make options page a submenu item of the themes menu.
		'capability'      => 'manage_options', // Cap required to view options-page.
		// 'position'        => 1, // Menu position. Only applicable if 'parent_slug' is left empty.
		// 'admin_menu_hook' => 'network_admin_menu', // 'network_admin_menu' to add network-level options page.
		// 'display_cb'      => false, // Override the options-page form output (CMB2_Hookup::options_page_output()).
		// 'save_button'     => esc_html__( 'Save Theme Options', 'autocomplete_gp' ), // The text for the options-page save button. Defaults to 'Save'.
		'cmb_styles' => false,
	) );
	
	/*
	 * Options fields ids only need
	 * to be unique within this box.
	 * Prefix is not needed.
	 */
	$cmb_options->add_field( array(
		'name' => __( 'API Key for Google Place', 'autocomplete_gp' ),
		'desc' => __( '<ul><h3>To get your Google Api key? please follow these simple steps:</h3>
         <li>1.Acess Google console: https://developers.google.com/maps/documentation/javascript/get-api-key.</li>
         <li>2.Click on "Create Project" - create yours.</li>
         <li>3.Click on "Credentials" - click Create credentials > API key.</li>
         <li>4.Idicate "Api Key", then select HTTP referer,type your domain name. </li>
         <li>5.Take the API Key and copy/pastein the plugin.</li>
         <span>Note: don\'t forget to active place API service in google API and services.</span></ul>', 'autocomplete_gp' ),
		'id'   => 'google_place_api',
		'type' => 'text',
		'default' => '',
		'attributes'  =>  array(
                        'required' => 'required',
						'data-validation' => 'required',
                      ),
        
) );
}

/**
 * Wrapper function around cmb2_get_option
 * @since  2.10.1
 * @param  string $key     Options array key
 * @param  mixed  $default Optional default value
 * @return mixed           Option value
 */
function autocomplete_gp_get_option( $key = '', $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( 'google-places', $key, $default );
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( 'google-places', $default );
	$val = $default;
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return $val;
}
//

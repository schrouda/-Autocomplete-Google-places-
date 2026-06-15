<?php
/**
 * Admin settings for Autocomplete Google places.
 *
 * Provides a custom, React (wp.element) powered settings screen where the
 * Google Places API key is stored, the autocomplete country scope is chosen,
 * and the API key can be tested live. Settings are persisted with the
 * WordPress Settings API into the shared `google-places` option.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Countries that can be selected for autocomplete restriction.
 *
 * @return array Map of ISO 3166-1 alpha-2 code => country name.
 */
function autocomplete_gp_countries() {
	return array(
		'US' => 'United States',
		'CA' => 'Canada',
		'MX' => 'Mexico',
		'JP' => 'Japan',
		'AU' => 'Australia',
		'NZ' => 'New Zealand',
		'GB' => 'United Kingdom',
		'IE' => 'Ireland',
		'FR' => 'France',
		'DE' => 'Germany',
		'ES' => 'Spain',
		'PT' => 'Portugal',
		'IT' => 'Italy',
		'NL' => 'Netherlands',
		'BE' => 'Belgium',
		'LU' => 'Luxembourg',
		'CH' => 'Switzerland',
		'AT' => 'Austria',
		'DK' => 'Denmark',
		'SE' => 'Sweden',
		'NO' => 'Norway',
		'FI' => 'Finland',
		'IS' => 'Iceland',
		'PL' => 'Poland',
		'CZ' => 'Czechia',
		'SK' => 'Slovakia',
		'HU' => 'Hungary',
		'RO' => 'Romania',
		'BG' => 'Bulgaria',
		'GR' => 'Greece',
		'HR' => 'Croatia',
		'SI' => 'Slovenia',
		'EE' => 'Estonia',
		'LV' => 'Latvia',
		'LT' => 'Lithuania',
		'CY' => 'Cyprus',
		'MT' => 'Malta',
	);
}

/**
 * Read a single value from the shared `google-places` option array.
 *
 * @param string $key     Option array key, or 'all' for the whole array.
 * @param mixed  $default Fallback when the key is missing/empty.
 * @return mixed
 */
function autocomplete_gp_get_option( $key = '', $default = false ) {
	$opts = get_option( 'google-places', array() );
	if ( 'all' === $key ) {
		return $opts;
	}
	if ( is_array( $opts ) && array_key_exists( $key, $opts ) && '' !== $opts[ $key ] ) {
		return $opts[ $key ];
	}
	return $default;
}

add_action( 'admin_menu', 'autocomplete_gp_register_menu' );
function autocomplete_gp_register_menu() {
	add_menu_page(
		esc_html__( 'Google Places', 'autocomplete-google-places' ),
		esc_html__( 'Google Places', 'autocomplete-google-places' ),
		'manage_options',
		'google-places',
		'autocomplete_gp_render_settings_page',
		'dashicons-location-alt'
	);
}

add_action( 'admin_init', 'autocomplete_gp_register_settings' );
function autocomplete_gp_register_settings() {
	register_setting(
		'autocomplete_gp_settings',
		'google-places',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'autocomplete_gp_sanitize',
			'default'           => array(),
		)
	);
}

/**
 * Sanitize and validate the submitted settings.
 *
 * @param mixed $input Raw submitted option value.
 * @return array
 */
function autocomplete_gp_sanitize( $input ) {
	$out = array();

	$out['google_place_api']   = isset( $input['google_place_api'] ) ? sanitize_text_field( $input['google_place_api'] ) : '';
	$out['restrict_worldwide'] = empty( $input['restrict_worldwide'] ) ? '0' : '1';

	$valid     = array_keys( autocomplete_gp_countries() );
	$countries = array();
	if ( ! empty( $input['countries'] ) && is_array( $input['countries'] ) ) {
		foreach ( $input['countries'] as $code ) {
			$code = strtoupper( sanitize_text_field( $code ) );
			if ( in_array( $code, $valid, true ) && ! in_array( $code, $countries, true ) ) {
				$countries[] = $code;
			}
		}
	}
	// Google Places allows a maximum of 5 country restrictions.
	$out['countries'] = array_slice( $countries, 0, 5 );

	add_settings_error( 'google-places', 'autocomplete_gp_saved', esc_html__( 'Settings saved.', 'autocomplete-google-places' ), 'updated' );

	return $out;
}

add_action( 'admin_enqueue_scripts', 'autocomplete_gp_admin_assets' );
function autocomplete_gp_admin_assets( $hook ) {
	if ( 'toplevel_page_google-places' !== $hook ) {
		return;
	}

	wp_enqueue_style( 'autocompletegp-admin', AUTOCOMPLETE_GP_ABSPATH_URL . 'css/admin.css', array(), AUTOCOMPLETE_GP_VERSION );
	wp_enqueue_script( 'autocompletegp-admin', AUTOCOMPLETE_GP_ABSPATH_URL . 'js/admin.js', array( 'wp-element' ), AUTOCOMPLETE_GP_VERSION, true );

	$option = get_option( 'google-places', array() );
	if ( ! is_array( $option ) ) {
		$option = array();
	}

	wp_localize_script(
		'autocompletegp-admin',
		'AutocompleteGPAdmin',
		array(
			'option'    => array(
				'google_place_api'   => isset( $option['google_place_api'] ) ? $option['google_place_api'] : '',
				'restrict_worldwide' => isset( $option['restrict_worldwide'] ) ? (string) $option['restrict_worldwide'] : '1',
				'countries'          => isset( $option['countries'] ) && is_array( $option['countries'] ) ? array_values( $option['countries'] ) : array(),
			),
			'countries' => autocomplete_gp_countries(),
			'i18n'      => array(
				'apiKeyTitle'   => __( 'Google Places API key', 'autocomplete-google-places' ),
				'apiKeyHelp'    => __( 'Enter your Google Maps JavaScript API key (with the Places library enabled), then test it.', 'autocomplete-google-places' ),
				'apiKeyLabel'   => __( 'API key', 'autocomplete-google-places' ),
				'testBtn'       => __( 'Test API key', 'autocomplete-google-places' ),
				'testing'       => __( 'Testing…', 'autocomplete-google-places' ),
				'valid'         => __( 'Success! The API key works and the Places library is enabled.', 'autocomplete-google-places' ),
				'invalid'       => __( 'This API key is invalid or not authorized for this site.', 'autocomplete-google-places' ),
				'requestDenied' => __( 'Google rejected the request', 'autocomplete-google-places' ),
				'noPlaces'      => __( 'The key loaded but the Places library is not available.', 'autocomplete-google-places' ),
				'network'       => __( 'Could not load Google Maps (network error or blocked key).', 'autocomplete-google-places' ),
				'timeout'       => __( 'Timed out waiting for Google Maps to respond.', 'autocomplete-google-places' ),
				'noKey'         => __( 'Please enter an API key first.', 'autocomplete-google-places' ),
				'reloadNote'    => __( 'To test a different key, save and reload this page first.', 'autocomplete-google-places' ),
				'countryTitle'  => __( 'Country scope', 'autocomplete-google-places' ),
				'worldwide'     => __( 'Worldwide (all countries)', 'autocomplete-google-places' ),
				'worldwideOn'   => __( 'Autocomplete works for every country (Europe, USA, Canada, Japan and more).', 'autocomplete-google-places' ),
				'countryHelp'   => __( 'Choose up to 5 countries to restrict suggestions. Google allows a maximum of 5.', 'autocomplete-google-places' ),
				'selected'      => __( 'selected', 'autocomplete-google-places' ),
			),
		)
	);
}

/**
 * Render the settings page. Outputs a Settings-API form with a no-JS fallback
 * inside #agp-settings-root; admin.js enhances it into a React UI.
 */
function autocomplete_gp_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$countries = autocomplete_gp_countries();
	$option    = get_option( 'google-places', array() );
	$saved_key = isset( $option['google_place_api'] ) ? $option['google_place_api'] : '';
	$worldwide = ! isset( $option['restrict_worldwide'] ) || '0' !== (string) $option['restrict_worldwide'];
	$selected  = isset( $option['countries'] ) && is_array( $option['countries'] ) ? $option['countries'] : array();
	?>
	<div class="wrap agp-wrap">
		<h1 class="agp-title"><span class="dashicons dashicons-location-alt"></span> <?php esc_html_e( 'Google Places', 'autocomplete-google-places' ); ?></h1>
		<?php settings_errors( 'google-places' ); ?>
		<form action="options.php" method="post" class="agp-form">
			<?php settings_fields( 'autocomplete_gp_settings' ); ?>
			<div id="agp-settings-root">
				<noscript><p><?php esc_html_e( 'JavaScript gives the best experience, but the fields below still work.', 'autocomplete-google-places' ); ?></p></noscript>
				<p>
					<label><strong><?php esc_html_e( 'Google Places API key', 'autocomplete-google-places' ); ?></strong></label><br />
					<input type="text" class="regular-text" name="google-places[google_place_api]" value="<?php echo esc_attr( $saved_key ); ?>" />
				</p>
				<p>
					<label><input type="checkbox" name="google-places[restrict_worldwide]" value="1" <?php checked( $worldwide ); ?> /> <?php esc_html_e( 'Worldwide (all countries)', 'autocomplete-google-places' ); ?></label>
				</p>
				<fieldset>
					<legend><strong><?php esc_html_e( 'Restrict to countries (max 5)', 'autocomplete-google-places' ); ?></strong></legend>
					<?php foreach ( $countries as $code => $name ) : ?>
						<label style="display:inline-block;min-width:220px;margin:4px 0;">
							<input type="checkbox" name="google-places[countries][]" value="<?php echo esc_attr( $code ); ?>" <?php checked( in_array( $code, $selected, true ) ); ?> />
							<?php echo esc_html( $name . ' (' . $code . ')' ); ?>
						</label>
					<?php endforeach; ?>
				</fieldset>
			</div>
			<?php submit_button( __( 'Save settings', 'autocomplete-google-places' ) ); ?>
		</form>
	</div>
	<?php
}

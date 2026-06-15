/**
 * Autocomplete Google places - admin settings UI.
 *
 * Built with WordPress's bundled React (wp.element), no build step required.
 * Enhances the no-JS fallback form inside #agp-settings-root into a reactive
 * UI for the API key, a live key tester, and the country-scope selector.
 */

( function ( wp ) {
	if ( ! wp || ! wp.element ) {
		return;
	}

	const { createElement: el, useState, render } = wp.element;
	const data = window.AutocompleteGPAdmin || {};
	const COUNTRIES = data.countries || {};
	const INITIAL = data.option || {};
	const I18N = data.i18n || {};

	const t = ( key, fallback ) => ( I18N[ key ] != null ? I18N[ key ] : fallback );

	let mapsRequested = false;

	/**
	 * Validate a Google Maps JS API key by loading the API client-side and
	 * making a real Places request. Invalid keys trigger gm_authFailure.
	 */
	function testGoogleKey( key, cb ) {
		const verify = () => {
			try {
				if (
					window.google &&
					google.maps &&
					google.maps.places &&
					google.maps.places.AutocompleteService
				) {
					const svc = new google.maps.places.AutocompleteService();
					svc.getPlacePredictions( { input: 'Paris' }, function ( predictions, status ) {
						const S = google.maps.places.PlacesServiceStatus;
						if ( status === S.OK || status === S.ZERO_RESULTS ) {
							cb( true, t( 'valid', 'Success! The API key works.' ) );
						} else {
							cb( false, t( 'requestDenied', 'Google rejected the request' ) + ' (' + status + ')' );
						}
					} );
				} else {
					cb( false, t( 'noPlaces', 'Places library not available.' ) );
				}
			} catch ( e ) {
				cb( false, e.message );
			}
		};

		// Google Maps JS can only be loaded once per page.
		if ( window.google && window.google.maps ) {
			verify();
			return;
		}
		if ( mapsRequested ) {
			cb( false, t( 'reloadNote', 'Reload the page to test a different key.' ) );
			return;
		}
		mapsRequested = true;

		let done = false;
		const finish = ( ok, msg ) => {
			if ( done ) {
				return;
			}
			done = true;
			clearTimeout( timer );
			cb( ok, msg );
		};
		const timer = setTimeout( function () {
			finish( false, t( 'timeout', 'Timed out waiting for Google Maps.' ) );
		}, 10000 );

		window.gm_authFailure = function () {
			finish( false, t( 'invalid', 'This API key is invalid or not authorized.' ) );
		};
		window.__agpMapsReady = function () {
			// Give gm_authFailure a brief chance to fire for invalid keys.
			setTimeout( function () {
				if ( done ) {
					return;
				}
				clearTimeout( timer );
				done = true;
				verify();
			}, 1200 );
		};

		const s = document.createElement( 'script' );
		s.src =
			'https://maps.googleapis.com/maps/api/js?key=' +
			encodeURIComponent( key ) +
			'&libraries=places&callback=__agpMapsReady';
		s.async = true;
		s.onerror = function () {
			finish( false, t( 'network', 'Could not load Google Maps.' ) );
		};
		document.body.appendChild( s );
	}

	function App() {
		const [ apiKey, setApiKey ] = useState( INITIAL.google_place_api || '' );
		const [ worldwide, setWorldwide ] = useState( '0' !== String( INITIAL.restrict_worldwide ) );
		const [ countries, setCountries ] = useState(
			Array.isArray( INITIAL.countries ) ? INITIAL.countries : []
		);
		const [ test, setTest ] = useState( { status: 'idle', message: '' } );

		const toggleCountry = ( code ) => {
			if ( countries.indexOf( code ) !== -1 ) {
				setCountries( countries.filter( ( c ) => c !== code ) );
			} else if ( countries.length < 5 ) {
				setCountries( countries.concat( [ code ] ) );
			}
		};

		const runTest = () => {
			const key = ( apiKey || '' ).trim();
			if ( ! key ) {
				setTest( { status: 'error', message: t( 'noKey', 'Please enter an API key first.' ) } );
				return;
			}
			setTest( { status: 'testing', message: t( 'testing', 'Testing…' ) } );
			testGoogleKey( key, function ( ok, msg ) {
				setTest( { status: ok ? 'success' : 'error', message: msg } );
			} );
		};

		const banner =
			'idle' === test.status
				? null
				: el( 'div', { className: 'agp-banner agp-banner--' + test.status }, test.message );

		const grid = Object.keys( COUNTRIES ).map( ( code ) => {
			const checked = countries.indexOf( code ) !== -1;
			const disabled = ! checked && countries.length >= 5;
			return el(
				'label',
				{
					key: code,
					className:
						'agp-country' +
						( checked ? ' is-selected' : '' ) +
						( disabled ? ' is-disabled' : '' ),
				},
				el( 'input', {
					type: 'checkbox',
					name: 'google-places[countries][]',
					value: code,
					checked: checked,
					disabled: disabled,
					onChange: () => toggleCountry( code ),
				} ),
				el( 'span', null, COUNTRIES[ code ] + ' (' + code + ')' )
			);
		} );

		return el(
			'div',
			{ className: 'agp-app' },
			// API key + tester card.
			el(
				'section',
				{ className: 'agp-card' },
				el( 'h2', null, t( 'apiKeyTitle', 'Google Places API key' ) ),
				el( 'p', { className: 'agp-help' }, t( 'apiKeyHelp', '' ) ),
				el(
					'div',
					{ className: 'agp-row' },
					el( 'input', {
						type: 'text',
						className: 'agp-input',
						name: 'google-places[google_place_api]',
						value: apiKey,
						placeholder: 'AIza…',
						onChange: ( e ) => setApiKey( e.target.value ),
					} ),
					el(
						'button',
						{
							type: 'button',
							className: 'agp-btn',
							onClick: runTest,
							disabled: 'testing' === test.status,
						},
						'testing' === test.status ? t( 'testing', 'Testing…' ) : t( 'testBtn', 'Test API key' )
					)
				),
				banner
			),
			// Country scope card.
			el(
				'section',
				{ className: 'agp-card' },
				el( 'h2', null, t( 'countryTitle', 'Country scope' ) ),
				el(
					'label',
					{ className: 'agp-switch' },
					el( 'input', {
						type: 'checkbox',
						name: 'google-places[restrict_worldwide]',
						value: '1',
						checked: worldwide,
						onChange: ( e ) => setWorldwide( e.target.checked ),
					} ),
					el( 'span', { className: 'agp-slider' } ),
					el( 'span', { className: 'agp-switch-label' }, t( 'worldwide', 'Worldwide (all countries)' ) )
				),
				el(
					'p',
					{ className: 'agp-help' },
					worldwide ? t( 'worldwideOn', '' ) : t( 'countryHelp', '' )
				),
				el( 'div', { className: 'agp-grid' + ( worldwide ? ' is-muted' : '' ) }, grid ),
				el( 'p', { className: 'agp-count' }, countries.length + '/5 ' + t( 'selected', 'selected' ) )
			)
		);
	}

	function mount() {
		const root = document.getElementById( 'agp-settings-root' );
		if ( root ) {
			render( el( App ), root );
		}
	}

	if ( 'loading' !== document.readyState ) {
		mount();
	} else {
		document.addEventListener( 'DOMContentLoaded', mount );
	}
} )( window.wp );

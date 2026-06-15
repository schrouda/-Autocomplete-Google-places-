/**
 * Autocomplete Google places
 *
 * Modern vanilla-JS (ES6+) rewrite of the original jQuery-based script.
 * Wires Google Places Autocomplete to WooCommerce-style billing and shipping
 * address fields and auto-fills street, city, state, postcode and country.
 */

class AddressAutocomplete {
	/**
	 * @param {string} prefix         Field prefix, e.g. "billing" or "shipping".
	 * @param {string} specialCountry Country code that uses the alternate
	 *                                street-number formatting (kept from the
	 *                                original behavior: "FR" billing, "KR" shipping).
	 */
	constructor( prefix, specialCountry ) {
		this.prefix = prefix;
		this.specialCountry = specialCountry;
		this.autocomplete = null;
		this.streetNumber = '';

		this.fieldIds = {
			address_1: `${ prefix }_address_1`,
			address_2: `${ prefix }_address_2`,
			city: `${ prefix }_city`,
			state: `${ prefix }_state`,
			postcode: `${ prefix }_postcode`,
			country: `${ prefix }_country`,
		};

		// Maps a Google address-component type to [target field id, value key].
		this.componentForm = {
			street_number: [ this.fieldIds.address_1, 'short_name' ],
			route: [ this.fieldIds.address_1, 'long_name' ],
			locality: [ this.fieldIds.city, 'long_name' ],
			administrative_area_level_1: [ this.fieldIds.state, 'short_name' ],
			country: [ this.fieldIds.country, 'long_name' ],
			postal_code: [ this.fieldIds.postcode, 'short_name' ],
		};

		this.formFieldsValue = {};
	}

	initialize() {
		if ( typeof google === 'undefined' || ! google.maps || ! google.maps.places ) {
			return;
		}

		const addressField = document.getElementById( this.fieldIds.address_1 );
		if ( ! addressField ) {
			return;
		}

		this.autocomplete = new google.maps.places.Autocomplete( addressField, {
			types: [ 'geocode' ],
		} );
		this.autocomplete.addListener( 'place_changed', () => this.fillInAddress() );

		addressField.addEventListener(
			'focus',
			() => this.setAutocompleteCountry(),
			true
		);

		const countryField = document.getElementById( this.fieldIds.country );
		if ( countryField ) {
			countryField.addEventListener(
				'change',
				() => this.setAutocompleteCountry(),
				true
			);
		}
	}

	fillInAddress() {
		this.clearFormValues();
		this.resetForm();

		const place = this.autocomplete.getPlace();
		if ( ! place || ! place.address_components ) {
			return;
		}

		const countryField = document.getElementById( this.fieldIds.country );

		for ( const component of place.address_components ) {
			for ( const type of component.types ) {
				const mapping = this.componentForm[ type ];
				if ( ! mapping ) {
					continue;
				}

				if ( type === 'street_number' ) {
					this.streetNumber = component.short_name;
				} else if ( countryField && countryField.value === this.specialCountry ) {
					const components = place.address_components;
					this.streetNumber = `${ components[ 0 ].short_name },${ components[ 1 ].long_name }`;
				}

				const [ targetId, valueKey ] = mapping;
				if ( Object.prototype.hasOwnProperty.call( component, valueKey ) ) {
					this.formFieldsValue[ targetId ] = component[ valueKey ];
				}
			}
		}

		this.appendStreetNumber();
		this.fillForm();

		const stateField = document.getElementById( this.fieldIds.state );
		if ( stateField ) {
			stateField.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		}

		if ( typeof FireCheckout !== 'undefined' ) {
			checkout.update( checkout.urls[ `${ this.prefix }_address` ] );
		}
	}

	clearFormValues() {
		this.formFieldsValue = {};
		this.streetNumber = '';
	}

	appendStreetNumber() {
		if ( this.streetNumber === '' ) {
			return;
		}
		const addressId = this.fieldIds.address_1;
		this.formFieldsValue[ addressId ] =
			`${ this.streetNumber } ${ this.formFieldsValue[ addressId ] || '' }`;
	}

	fillForm() {
		for ( const [ fieldId, value ] of Object.entries( this.formFieldsValue ) ) {
			if ( fieldId === this.fieldIds.country ) {
				this.selectRegion( fieldId, value );
				continue;
			}

			const field = document.getElementById( fieldId );
			if ( field ) {
				field.value = value;
			}
		}
	}

	selectRegion( fieldId, label ) {
		const select = document.getElementById( fieldId );
		if ( ! select ) {
			return false;
		}

		const option = Array.from( select.options ).find(
			( opt ) => opt.text === label
		);
		if ( option ) {
			select.value = option.value;
		}
	}

	resetForm() {
		const address2 = document.getElementById( this.fieldIds.address_2 );
		if ( address2 ) {
			address2.value = '';
		}
	}

	setAutocompleteCountry() {
		if ( ! this.autocomplete ) {
			return;
		}
		const countryField = document.getElementById( this.fieldIds.country );
		const country = countryField ? countryField.value : 'FR';
		this.autocomplete.setComponentRestrictions( { country } );
	}
}

window.addEventListener( 'load', () => {
	new AddressAutocomplete( 'billing', 'FR' ).initialize();
	new AddressAutocomplete( 'shipping', 'KR' ).initialize();
} );

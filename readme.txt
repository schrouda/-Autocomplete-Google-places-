=== Autocomplete Google places ===
Contributors: kaischrouda
Tags: google address autocomplete, autocomplete google address, address autocomplete, autocomplete
Requires at least: 5.6
Tested up to: 7.0
Stable tag: 3.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add Google Places address autocomplete to your address fields and auto-fill city, state and postcode.

== Description ==

This plugin enables Google Places address auto-completion on text input fields (order, checkout and registration pages). When a user starts typing an address and picks a suggestion, the plugin auto-fills the street, city, state, postcode and country fields. You only need a valid Google Places API key.

The plugin targets the standard WooCommerce-style billing and shipping address fields (for example `billing_address_1` and `shipping_address_1`).

As of version 3.0.0 the front-end script is written in modern, dependency-free vanilla JavaScript (no jQuery required) and the bundled CMB2 settings library has been updated for security.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/autocomplete-google-places` directory, or install the plugin through the WordPress Plugins screen directly.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Open the **Google Places** admin menu (location-pin icon) and enter your Google Places API key.
4. Save. Autocomplete is now active on pages that contain the supported address fields.

== Frequently Asked Questions ==

= Is this a paid plugin? =

No, it is completely free.

= Do I need a Google API key? =

Yes. As of 3.0.0 there is no built-in fallback key. Create a key in the Google Cloud Console with the Maps JavaScript API and the Places library enabled, then enter it on the **Google Places** settings page.

= Does it still require jQuery? =

No. As of 3.0.0 the front-end script is pure vanilla JavaScript and no longer enqueues jQuery.

= Which fields are supported? =

The standard WooCommerce-style billing and shipping address fields. When an address is selected, address line 1, city, state, postcode and country are filled automatically.

== Screenshots ==

1. The "Google Places" settings page where you enter your API key.
2. Address fields auto-filled after selecting a Google Places suggestion.

== Changelog ==

= 3.0.0 =
* Major release.
* Rewrote the front-end script in modern vanilla JavaScript (ES6); removed the jQuery dependency from the script and its enqueue.
* Security: upgraded the bundled CMB2 library from 2.10.1 to 2.12.0 (addresses CVE-2024-1792) and removed the dead root CMB2 loader.
* Security: removed the hardcoded Google Maps API key fallback; the Maps script now loads only when an API key is configured.
* Security: the Google Maps URL is now built with rawurlencode() and escaped with esc_url_raw().
* Hardening: moved the direct-access guard ahead of all includes, removed unused example files, and added a directory-listing guard to /js.
* Added uninstall cleanup of stored plugin options.
* Compatibility: raised the minimum PHP requirement to 7.4 (required by CMB2 2.12.0); updated "Tested up to".

= 1.3.4 =
* WordPress 5.9 version compatibility.

== Upgrade Notice ==

= 3.0.0 =
Requires PHP 7.4+ (bundles CMB2 2.12.0). You must enter your own Google Places API key on the settings page; the old hardcoded fallback key has been removed. If you used a publicly exposed key from an older version, rotate or revoke it in Google Cloud.

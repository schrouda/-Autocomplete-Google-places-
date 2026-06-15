# Autocomplete Google places

=== Autocomplete Google places ===
Contributors: Kais Chrouda
Tags: Google Address Autocomplete,Autocomplete Google Address,Address Autocomplete,Autocomplete
Requires at least: 5.6
Tested up to: 7.0
Stable tag: 3.0.0
Requires PHP: 7.4 or later
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add Google Places address autocomplete to your WordPress address fields and auto-fill city and postcode.

== Description ==

This plugin enables Google Places address auto-completion on text input fields (order, checkout and registration pages). When a user starts typing an address and picks a suggestion, the plugin auto-fills the street, city, state, postcode and country fields. You just need a valid Google Places API key.

The plugin targets the standard WooCommerce-style billing and shipping address fields (`billing_address_1`, `shipping_address_1`, etc.).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/autocomplete-google-places` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Go to the **Google Places** admin menu (location-pin icon) and enter your Google Places API key.
4. Save. The autocomplete is now active on pages that contain the supported address fields.

== What's new in 3.0.0 ==

Version 3.0.0 is a major release focused on modernizing the front-end code and hardening security. It is functionally backwards compatible — the autocomplete/auto-fill behavior is unchanged — but it raises the minimum PHP version (see Upgrade Notice).

= Modern vanilla JavaScript migration =

The front-end script (`js/autocomplete.js`) was fully rewritten from minified, jQuery-dependent code into modern vanilla JavaScript (ES6+):

* The two duplicated billing/shipping objects were unified into a single reusable `AddressAutocomplete` class, parameterized by field prefix.
* **jQuery is no longer required.** The only jQuery call (`$("#..._state").trigger("change")`) is replaced with a native `dispatchEvent(new Event('change', { bubbles: true }))`, which still notifies WooCommerce/FireCheckout state handlers.
* The `jquery-core`/`jquery` dependencies were removed from the script enqueue, so the plugin no longer forces jQuery to load on the front end.
* Uses modern APIs (`const`/`let`, classes, arrow functions, `Object.entries`, template literals) and writes values via `element.value` (never `innerHTML`/`eval`), so there is no DOM-XSS surface.
* All original behavior is preserved: street-number prepend, address-line-2 reset, special-country formatting, and country/state select matching.

= Security & hardening =

* **CVE-2024-1792 fixed:** the bundled CMB2 library was upgraded from 2.10.1 to **2.12.0**. CMB2 ≤ 2.10.1 was vulnerable to PHP Object Injection via the `text_datetime_timestamp_timezone` field. The stale, dead root CMB2 loader was also removed.
* **No more hardcoded API key:** the previous hardcoded Google Maps API key fallback was removed. If no API key is configured, the Maps script is simply not loaded (instead of leaking a shared credential).
* **Hardened Maps URL:** the Google Maps URL is built with `add_query_arg()`, the key is passed through `rawurlencode()`, and the final URL is escaped with `esc_url_raw()`.
* **Earlier direct-access guard:** `defined( 'ABSPATH' ) || die()` now runs before any `include`.
* **Reduced attack surface:** removed unused bundled `example-functions.php` files and added an `index.php` to the `js/` directory to prevent directory listing.

== Upgrade Notice ==

= 3.0.0 =
This release bundles CMB2 2.12.0, which requires **PHP 7.4 or higher**. Ensure your server runs PHP 7.4+ before upgrading. If you previously relied on the built-in (hardcoded) API key, you must now enter your own Google Places API key on the **Google Places** settings page, otherwise the autocomplete script will not load. If you used a publicly exposed key from an older version, rotate/revoke it in Google Cloud.

== Frequently Asked Questions ==

= Is this a paid plugin? =
No, it's totally free.

= Do I need a Google API key? =
Yes. As of 3.0.0 there is no built-in fallback key. Create a key in the Google Cloud Console with the Maps JavaScript API and Places library enabled, then enter it on the **Google Places** settings page.

= Does it still require jQuery? =
No. As of 3.0.0 the front-end script is pure vanilla JavaScript and no longer enqueues jQuery.

== Screenshots ==

1. The "Google Places" settings page where you enter your API key.
2. Address fields auto-filled after selecting a Google Places suggestion.

== Changelog ==

= 3.0.0 =
* Major release.
* Migration: rewrote the front-end script in modern vanilla JavaScript (ES6 `AddressAutocomplete` class); removed the jQuery dependency from the script and its enqueue.
* Security: upgraded the bundled CMB2 library from 2.10.1 to 2.12.0 (addresses CVE-2024-1792) and removed the dead root CMB2 loader.
* Security: removed the hardcoded Google Maps API key fallback; the Maps script now loads only when an API key is configured.
* Security: the Google Maps URL is now built with `add_query_arg()` + `rawurlencode()` and escaped with `esc_url_raw()`.
* Hardening: moved the `ABSPATH` direct-access guard ahead of all includes, removed unused `example-functions.php` files, and added a directory-listing guard (`index.php`) to `/js`.
* Compatibility: raised the minimum PHP requirement to 7.4 (required by CMB2 2.12.0); updated "Tested up to".

= 1.3.4 =
WordPress 5.9 version compatibility

# -Autocomplete-Google-places-
=== Autocomplete Google places ===
Contributors: Kais Chrouda
Tags: Google Address Autocomplete,Autocomplete Google Address,Address Autocomplete,Autocomplete
Requires at least: 5.0
Tested up to: 5.9
Requires PHP: 5.4 or later
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin will help you to use Place Autocomplete API key.

== Description ==

This plugin will help you to use Place Autocomplete API key to enable address auto-completion to any text input fields.You just need a valid API key places from google. 
Important: you can use the plugin in order, checkout and registration pages without any special restriction.
Just when you type the right address, the plugin will auto fill postcode and city.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->Plugin Name screen to configure the plugin
4. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= Is this a paid plugin? =
No, It's totally free.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif).

2. This is the second screen shot
== Changelog ==

= 3.0.0 =
* Major release.
* Rewrote the front-end script in modern vanilla JavaScript (ES6); removed the jQuery dependency.
* Security: upgraded the bundled CMB2 library to 2.12.0 (addresses CVE-2024-1792).
* Security: removed the hardcoded Google Maps API key fallback; the Maps script now only loads when an API key is configured.
* Security: the Google Maps URL is now built with rawurlencode() and escaped with esc_url_raw().
* Hardening: moved the direct-access guard ahead of all includes, removed unused example files, and added a directory-listing guard to /js.

= 1.3.4 =
WordPress 5.9 version compatibility

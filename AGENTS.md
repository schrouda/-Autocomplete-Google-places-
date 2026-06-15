# AGENTS.md

## Cursor Cloud specific instructions

This repository is a single **WordPress plugin** ("Autocomplete Google places"). It is not a standalone app — it only runs inside a WordPress install. The repo also vendors the CMB2 library under `library/` (used for the admin settings page); `init.php`, `example-functions.php`, and `readme.txt` in the repo root are leftover CMB2 files, not the plugin entry point. The real plugin entry point is `autocomplete-google-places.php`.

### Environment layout (provisioned in the VM snapshot)
- PHP 8.3 CLI, MariaDB 10.11 server, and WP-CLI (`wp`) are installed system-wide.
- WordPress core lives at `/home/ubuntu/wp` (DB `wordpress`, user `wp` / pass `wppass`).
- The plugin is wired into WordPress via a symlink: `/home/ubuntu/wp/wp-content/plugins/autocomplete-google-places -> /workspace`. Because it's a symlink, edits to the repo are picked up immediately by WordPress.
- WordPress admin login: user `admin`, password `admin123`.

### Starting services (NOT done by the update script — start these manually each session)
- Start the database: `sudo service mariadb start`
- Start the dev server (from `/home/ubuntu/wp`): `wp server --host=0.0.0.0 --port=8080`
  - Site: `http://localhost:8080`, admin: `http://localhost:8080/wp-admin/` (run in tmux/background so it stays up).

### Plugin settings / functionality
- After activation, a top-level admin menu **"Google Places"** (location-pin icon, slug `google-places`) appears. It stores a single option `google_place_api` via CMB2.
- The option is persisted as a serialized array under the `google-places` option key. Read it programmatically with `wp eval 'echo autocomplete_gp_get_option("google_place_api");'`.
- On front-end pages the plugin enqueues `js/autocomplete.js` plus the Google Maps JS API (`maps.googleapis.com/maps/api/js?key=<configured key>&libraries=places`). If no key is set, the code falls back to a hardcoded key in `autocomplete-google-places.php`. A real, valid Google Maps Places API key is required for live autocomplete suggestions; an arbitrary string is enough to verify the enqueue/settings flow.

### Lint / test / build
- There is **no build step, no test suite, and no linter config** (no `composer.json`/`package.json`). The only meaningful programmatic check is PHP syntax linting, e.g. `php -l autocomplete-google-places.php` (also `admin_options.php`).

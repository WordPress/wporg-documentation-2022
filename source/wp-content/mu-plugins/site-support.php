<?php
// phpcs:disable Generic.Functions.OpeningFunctionBraceKernighanRitchie.ContentAfterBrace
// phpcs:disable Generic.Formatting.DisallowMultipleStatements.SameLine
/**
 * This mu-plugin is loaded only on the wordpress.org/support/ site.
 *
 * Used for theme-switching the new theme on specific pages.
 */

namespace WordPressdotorg\Documentation_2022\MU_Plugin;

const CHILD_THEME  = 'wporg-documentation-2022';
const PARENT_THEME = 'wporg-parent-2021';

/**
 * Helper to check the requested page against our new page list.
 */
function should_use_new_theme() {

	// Request to resolve a template.
	if ( isset( $_GET['_wp-find-template'] ) ) {
		return true;
	}

	$root = '/';
	if ( function_exists( '\get_blog_details' ) ) {
		$root = \get_blog_details( null, false )->path;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? explode( '?', esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ?? '/' )[0] : '/';
	$request_uri = str_replace( $root, '/', $request_uri );

	// Admin page or an API request.
	if ( is_admin() || wp_is_json_request() || 0 === strpos( $request_uri, '/wp-json/wp' ) ) {
		return true;
	}

	// Preview. Can't call is_preview() this early in the process.
	if ( isset( $_GET['preview'] ) && isset( $_GET['preview_id'] ) ) {
		return true;
	}

	// Use the new theme on all article, category, and version pages.
	if (
		str_starts_with( $request_uri, '/article/' ) ||
		str_starts_with( $request_uri, '/category/' ) ||
		str_starts_with( $request_uri, '/wordpress-version/' )
	) {
		return true;
	}

	// A list of specific pages to use the new theme.
	$new_theme_pages = array(
		'/',
	);
	if ( in_array( $request_uri, $new_theme_pages ) ) {
		return true;
	}

	return false;
}

// Only run this code on local envs and in sandboxes.
if ( 'production' !== wp_get_environment_type() ) {
	if ( should_use_new_theme() ) {
		add_filter( 'template', function() { return PARENT_THEME; } );
		add_filter( 'stylesheet', function() { return CHILD_THEME; } );
	} else {
		// Support theme is not nested in local envs.
		if ( 'local' === wp_get_environment_type() ) {
			add_filter( 'template', function() { return 'wporg-support'; } );
			add_filter( 'stylesheet', function() { return 'wporg-support'; } );
		} else {
			add_filter( 'template', function() { return 'pub/wporg-support'; } );
			add_filter( 'stylesheet', function() { return 'pub/wporg-support'; } );
		}
	}
}

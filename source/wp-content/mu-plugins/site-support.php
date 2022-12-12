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

/**
 * Check some site conditions to determine whether to show the new theme.
 *
 * This is different from should_use_new_theme because that checks whether the
 * current page should use the new theme, regardless of user permissions. This
 * checks whether the current environment/user should see the new theme.
 */
function can_preview_theme() {
	// If this is not production (sandbox or local), allow the new theme.
	if ( 'production' !== wp_get_environment_type() ) {
		return true;
	}

	// If the user is a wporg user and they've added the preview string, allow the new theme.
	return current_user_can( 'read' ) && isset( $_GET['_theme_preview'] );
}

/**
 * Override the template value.
 */
function override_template() {
	if ( should_use_new_theme() && can_preview_theme() ) {
		return PARENT_THEME;
	}
	if ( 'local' === wp_get_environment_type() ) {
		return 'wporg-support';
	}
	return 'pub/wporg-support';
}

/**
 * Override the stylesheet value.
 */
function override_stylesheet() {
	if ( should_use_new_theme() && can_preview_theme() ) {
		return CHILD_THEME;
	}
	if ( 'local' === wp_get_environment_type() ) {
		return 'wporg-support';
	}
	return 'pub/wporg-support';
}

add_filter( 'template', __NAMESPACE__ . '\override_template' );
add_filter( 'stylesheet', __NAMESPACE__ . '\override_stylesheet' );

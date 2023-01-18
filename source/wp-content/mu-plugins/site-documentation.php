<?php
/**
 * This mu-plugin is loaded only on the wordpress.org/documentation/ site.
 *
 * Note: This file is synced from github, make edits in this file:
 * https://github.com/WordPress/wporg-documentation-2022/blob/trunk/source/wp-content/mu-plugins/site-documentation.php
 * And sync the changes with ./bin/sync/wporg-documentation.sh.
 */

namespace WordPressdotorg\Documentation_2022\MU_Plugin;

add_action( 'template_redirect', __NAMESPACE__ . '\redirect_to_google_search' );
add_action( 'template_redirect', __NAMESPACE__ . '\redirect_old_content', 9 ); // Before redirect_canonical();

/**
 * Redirects search results to Google Custom Search.
 */
function redirect_to_google_search() {
	$search_terms = get_search_query( false );

	if ( $search_terms ) {
		$search_url = sprintf(
			'https://wordpress.org/search/%s/?in=support_docs',
			rawurlencode( $search_terms )
		);
		wp_safe_redirect( esc_url_raw( $search_url ) );
		exit;
	}
}

/**
 * Redirects old content to the new URL structure.
 *
 * This does not include the article renaming since core will handle that.
 */
function redirect_old_content() {
	$path_redirects = [
		// Removed articles.
		'/documentation/article/wordpress-features/' => 'https://wordpress.org/about/features/',
		'/documentation/article/requirements/'       => 'https://wordpress.org/about/requirements/',

		// Renamed categories.
		'/documentation/category/basic-administration/' => '/documentation/category/dashboard/',
		'/documentation/category/troubleshooting/'      => '/documentation/category/faqs/',
		'/documentation/category/installation/'         => '/documentation/category/installation/',
		'/documentation/category/maintenance/'          => '/documentation/category/maintenance/',
		'/documentation/category/security/'             => '/documentation/category/security/',
		'/documentation/category/getting-started/'      => '/documentation/category/where-to-start/',

		// Top-level category landing pages.
		'/documentation/category/customizing/' => '/documentation/customization/',
		'/documentation/category/basic-usage/' => '/documentation/support-guides/',

		// @todo When the Advanced Administration handbook is updated, add those redirects here.
	];

	$request_uri = $_SERVER['REQUEST_URI'] ?? '/documentation-test/'; // phpcs:ignore

	foreach ( $path_redirects as $old_path => $new_url ) {
		if ( str_starts_with( $request_uri, $old_path ) ) {
			do_redirect_and_exit( $new_url );
		}
	}
}

/**
 * Do the 301 redirect and exit the script.
 */
function do_redirect_and_exit( $location ) {
	header_remove( 'expires' );
	header_remove( 'cache-control' );

	wp_safe_redirect( $location, 301 );
	exit;
}

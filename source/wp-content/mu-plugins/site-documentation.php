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

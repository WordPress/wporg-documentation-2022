<?php

namespace WordPressdotorg\Theme\Documentation_2022;

/**
 * Actions and filters.
 */
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_assets' );
add_filter( 'render_block_core/pattern', __NAMESPACE__ . '\prevent_arrow_emoji', 20 );
add_filter( 'the_content', __NAMESPACE__ . '\prevent_arrow_emoji', 20 );
add_filter( 'wporg_block_site_breadcrumbs', __NAMESPACE__ . '\set_site_breadcrumbs' );

/**
 * Enqueue scripts and styles.
 */
function enqueue_assets() {
	// The parent style is registered as `wporg-parent-2021-style`, and will be loaded unless
	// explicitly unregistered. We can load any child-theme overrides by declaring the parent
	// stylesheet as a dependency.
	wp_enqueue_style(
		'wporg-docs-2021-style',
		get_stylesheet_directory_uri() . '/build/style/style-index.css',
		array( 'wporg-parent-2021-style', 'wporg-global-fonts' ),
		filemtime( __DIR__ . '/build/style/style-index.css' )
	);
}

/**
 * See https://github.com/WordPress/wporg-main-2022/blob/4f8a3a9c1e1f6cb2a3aff648457a85278679d6cb/source/wp-content/themes/wporg-main-2022/functions.php#L88
 * This should be moved to the parent theme.
 *
 * @param string $content Content of the current post.
 * @return string The updated content.
 */
function prevent_arrow_emoji( $content ) {
	return preg_replace( '/([←↑→↓↔↕↖↗↘↙])/u', '\1&#65038;', $content );
}

/**
 * Update the breadcrumbs format for this site.
 *
 * @param array $breadcrumbs An array of breadcrumb links.
 * @return array Updated breadcrumbs.
 */
function set_site_breadcrumbs( $breadcrumbs ) {
	if ( is_front_page() ) {
		return array();
	}

	$breadcrumbs[0]['title'] = __( 'Documentation', 'wporg-docs' );
	return $breadcrumbs;
}

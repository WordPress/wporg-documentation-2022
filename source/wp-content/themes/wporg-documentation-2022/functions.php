<?php

namespace WordPressdotorg\Theme\Documentation_2022;

// Block files
require_once __DIR__ . '/src/table-of-contents/index.php';

/**
 * Actions and filters.
 */
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_assets' );
add_filter( 'wporg_block_site_breadcrumbs', __NAMESPACE__ . '\set_site_breadcrumbs' );
add_action( 'pre_get_posts', __NAMESPACE__ . '\pre_get_posts' );
add_filter( 'comment_form_defaults', __NAMESPACE__ . '\comment_form_defaults' );
add_filter( 'comment_form_field_comment', __NAMESPACE__ . '\hide_field_after_submission' );
add_filter( 'comment_form_submit_field', __NAMESPACE__ . '\hide_field_after_submission' );

// Enforce log in to leave feedback.
add_filter( 'pre_option_comment_registration', '__return_true' );

// Remove table of contents.
add_filter( 'wporg_handbook_toc_should_add_toc', '__return_false' );

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
 * Update the breadcrumbs format for this site.
 *
 * @param array $breadcrumbs An array of breadcrumb links.
 * @return array Updated breadcrumbs.
 */
function set_site_breadcrumbs( $breadcrumbs ) {
	if ( is_front_page() ) {
		return array();
	}

	// Set the first item's title. By default this is the site title, but the
	// site title for `w.org/support` is "WordPress Forums", which is not
	// correct for the breadcrumbs.
	$breadcrumbs[0]['title'] = __( 'Documentation', 'wporg-docs' );

	if ( is_category() ) {
		// Format: home / topic page / category
		$category    = get_queried_object();
		$breadcrumbs = array( $breadcrumbs[0] );
		if ( $category->category_parent ) {
			$parent        = get_term( $category->category_parent );
			$breadcrumbs[] = array(
				'url'   => get_topic_permalink( $parent ),
				'title' => $parent->name,
			);
		}
		$breadcrumbs[] = array(
			'url'   => '',
			'title' => $category->name,
		);
	} elseif ( is_singular( 'helphub_article' ) ) {
		// Format: home / topic page / category / article title
		$breadcrumbs = array( $breadcrumbs[0] );
		$categories  = get_the_category();
		if ( $categories ) {
			$category = $categories[0];
			if ( $category->parent ) {
				$parent        = get_term( $category->parent );
				$breadcrumbs[] = array(
					'url'   => get_topic_permalink( $parent ),
					'title' => $parent->name,
				);
			}
			$breadcrumbs[] = array(
				'url'   => get_term_link( $category->term_id, $category->taxonomy ),
				'title' => $category->name,
			);
		}
		$breadcrumbs[] = array(
			'url'   => '',
			'title' => get_the_title(),
		);
	}

	return $breadcrumbs;
}

/**
 * Get the topic landing page permalink for a given parent category.
 */
function get_topic_permalink( $category ) {
	if ( empty( $category->slug ) ) {
		return '';
	}
	switch ( $category->slug ) {
		case 'wordpress-overview':
			return site_url( '/overview/' );
		case 'technical-guides':
			return site_url( '/technical-guides/' );
		case 'support-guides':
			return site_url( '/support-guides/' );
		case 'customization':
			return site_url( '/customization/' );
	}
}

/**
 * Filter the default query.
 *
 * Used to render posts on archive pages (query.inherit = true).
 *
 * @param \WP_Query $query The WordPress Query object.
 */
function pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	// Show many articles on the archive pages.
	if ( ! $query->is_singular() ) {
		$query->set( 'posts_per_page', 50 );
		$query->set( 'orderby', 'menu_order post_title' );
	}
}

/**
 * Check if the current page has the submission parameter.
 *
 * This seems to work but I'm not sure where it's set.
 */
function has_submitted_comment_form() {
	return isset( $_GET['feedback_submitted'] ) && $_GET['feedback_submitted'];  // phpcs:ignore
}

/**
 * Update the comment form settings.
 *
 * @param array $fields The default comment form arguments.
 *
 * @return array Returns the modified fields.
 */
function comment_form_defaults( $fields ) {
	$post    = get_post();
	$post_id = $post->ID;

	$user          = wp_get_current_user();
	$user_identity = $user->exists() ? $user->display_name : '';

	$str_log_in = sprintf(
		/* translators: 1: log in link, 2: support forums link. */
		__( '<a href="%1$s">Log in</a> to submit feedback. If you need suppport with something that wasn&#39;t covered by this article, please post your question in the <a href="%2$s">support forums</a>.', 'wporg-docs' ),
		esc_url( wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) ) ),
		esc_url( home_url( '/forums/' ) )
	);
	$fields['must_log_in'] = '<p class="must-log-in">' . $str_log_in . '</p>';

	$fields['title_reply_before'] = '<h2 id="reply-title" class="comment-reply-title has-heading-3-font-size" style="margin-top:0">';
	$fields['title_reply_after']  = '</h2>';
	$fields['title_reply']        = __( 'Was this article helpful? How could it be improved?', 'wporg-docs' );
	if ( has_submitted_comment_form() ) {
		$fields['title_reply'] = __( 'Thank you for your feedback!', 'wporg-docs' );
	}

	$fields['label_submit'] = __( 'Submit feedback', 'wporg-docs' );

	$fields['logged_in_as']  = '<p>';
	$fields['logged_in_as'] .= __( 'Feedback you send to us will go only to the folks who maintain documentation. They may reach out in case there are questions or would like to followup feedback. But that too will stay behind the scenes.', 'wporg-docs' );
	$fields['logged_in_as'] .= '</p></p>';
	$fields['logged_in_as'] .= sprintf(
		/* translators: %s: support forums link. */
		__( 'This is not for personalized support. Please create a <a href="%s">forum thread</a> instead to receive help from the community.', 'wporg-docs' ),
		esc_url( home_url( '/forums/' ) )
	);
	$fields['logged_in_as'] .= '</p>';

	$fields['comment_notes_after']  = '<p class="logged-in-as">';
	$fields['comment_notes_after'] .= sprintf(
		/* translators: 1: User name, 2: Logout URL. */
		__( 'Logged in as %1$s (<a href="%2$s">log out?</a>)', 'wporg-docs' ),
		$user_identity,
		wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
	);
	$fields['comment_notes_after'] .= '</p>';

	if ( has_submitted_comment_form() ) {
		$fields['logged_in_as']        = '<p>' . __( 'We will review it as quickly as possible.', 'wporg-docs' ) . '</p>';
		$fields['comment_notes_after'] = '';
	}

	return $fields;
}

/**
 * Remove the field if the form has been submitted.
 *
 * @param string $field The HTML-formatted output of the comment form field.
 *
 * @return string Empty string or initial field.
 */
function hide_field_after_submission( $field ) {
	if ( has_submitted_comment_form() ) {
		return '';
	}
	return $field;
}

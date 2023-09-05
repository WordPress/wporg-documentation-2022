<?php
/**
 * Title: Header
 * Slug: wporg-documentation-2022/header
 * Inserter: no
 */

?>

<!-- wp:group {"align":"full","style":{"spacing":{"blockGap":"0"},"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"charcoal-2","textColor":"white","className":"is-sticky","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull is-sticky has-white-color has-charcoal-2-background-color has-text-color has-background has-link-color">

	<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"18px","bottom":"18px","left":"var:preset|spacing|edge-space","right":"var:preset|spacing|edge-space"}},"border":{"top":{"color":"var:preset|color|opacities-white-10","width":"1px"}}},"className":"has-background","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignfull has-background" style="border-top-color:var(--wp--preset--color--opacities-white-10);border-top-width:1px;padding-top:18px;padding-right:var(--wp--preset--spacing--edge-space);padding-bottom:18px;padding-left:var(--wp--preset--spacing--edge-space)">
	
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size"><?php esc_html_e( 'Documentation', 'wporg-docs' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:template-part {"slug":"header-navigation"} /-->
	
	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->

<?php
/**
 * Title: Page Header w/ Breadcrumbs
 * Slug: ucsc-2022/page-header
 * Block Types: core/cover, core/heading
 * Categories: banners
 */
?>

<!-- wp:group {"align":"full","className":"ucsc-page-header","layout":{"type":"default"},"fontSize":"base"} -->
<div class="wp-block-group alignfull ucsc-page-header has-base-font-size">
	<!-- wp:cover {"dimRatio":50,"contentPosition":"center center","isDark":false,"className":"ucsc-page-header__content","style":{"spacing":{"padding":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-cover is-light ucsc-page-header__content" style="padding-top:var(--wp--preset--spacing--50)">
		<span aria-hidden="true" class="wp-block-cover__background has-background-dim"></span>
		<div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"default"}} -->
			<div class="wp-block-group">
				<!-- wp:post-title {"level":1,"style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}}},"backgroundColor":"ucsc-primary-yellow","textColor":"ucsc-primary-blue","className":"ucsc-page-header__title primary-post-title","fontSize":"seven"} /--></div>
			<!-- /wp:group --></div>
	</div>
	<!-- /wp:cover --></div>
<!-- /wp:group -->

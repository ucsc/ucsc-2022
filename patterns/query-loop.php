<?php
/**
 * Title: UCSC Query
 * Slug: ucsc-2022/query-loop
 * Block Types: core/query
 * Categories: grid page_layout posts query_loop
 */
?>
<!-- wp:query {"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:group {"className":"ucsc__card ucsc__card\u002d\u002dquery-loop","layout":{"type":"default"}} -->
<div class="wp-block-group ucsc__card ucsc__card--query-loop"><!-- wp:post-featured-image {"isLink":true,"width":"","height":"","sizeSlug":"sixteen-nine","className":"ucsc-query-loop__image"} /-->

<!-- wp:post-terms {"term":"category","style":{"elements":{"link":{"color":{"text":"var:preset|color|ucsc-secondary-blue"},":hover":{"color":{"text":"var:preset|color|ucsc-primary-blue"}}}}},"textColor":"black","className":"ucsc-query-loop__category","fontSize":"small"} /-->

<!-- wp:post-title {"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|ucsc-secondary-blue"},":hover":{"color":{"text":"var:preset|color|ucsc-primary-blue"}}}}},"fontSize":"three"} /-->

<!-- wp:post-date {"style":{"spacing":{"margin":{"bottom":"0"}}},"textColor":"black","className":"ucsc-query-loop__post-date","fontSize":"base"} /-->

<!-- wp:post-excerpt {"className":"ucsc-query-loop__excerpt"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->


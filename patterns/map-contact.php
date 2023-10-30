<?php
/**
 * Title: Map + Contact
 * Slug: ucsc-2022/map-contact
 * Block Types: core/columns, core/paragraph, core/heading, core/html
 * Categories: text_layout
 */
?>

<!-- wp:columns {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":{"top":"0","left":"0"}}},"className":"ucsc-map-contact"} -->
<div class="wp-block-columns ucsc-map-contact" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:column {"className":"ucsc-map-contact__map"} -->
	<div class="wp-block-column ucsc-map-contact__map"><!-- wp:html -->
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12746.83386746128!2d-122.0694595657337!3d36.992891465250615!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808e41a2ff8cbf4f%3A0x3a8e3b7c928320d5!2sUniversity%20of%20California%20Santa%20Cruz!5e0!3m2!1sen!2sus!4v1689093782636!5m2!1sen!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
		<!-- /wp:html --></div>
	<!-- /wp:column -->

	<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"backgroundColor":"ucsc-primary-yellow","className":"ucsc-map-contact__connect"} -->
	<div class="wp-block-column ucsc-map-contact__connect has-ucsc-primary-yellow-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:columns {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"},"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
		<div class="wp-block-columns" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:column {"style":{"spacing":{"padding":{"top":"0","right":"0","bottom":"0","left":"0"}}}} -->
			<div class="wp-block-column" style="padding-top:0;padding-right:0;padding-bottom:0;padding-left:0"><!-- wp:heading -->
				<h2 class="wp-block-heading">Column 1</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>Address Placehoder<br>1234 Lorem ipsum dolor sit amet consectetur adipiscing elit.<br>Ut vel ante nec diam placerat vulputate.</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph -->
				<p><strong>Additional Info</strong><br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vel ante nec diam placerat vulputate.</p>
				<!-- /wp:paragraph --></div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:heading -->
				<h2 class="wp-block-heading">Column 2</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p>Contact Info: placeholder</p>
				<!-- /wp:paragraph --></div>
			<!-- /wp:column --></div>
		<!-- /wp:columns --></div>
	<!-- /wp:column --></div>
<!-- /wp:columns -->

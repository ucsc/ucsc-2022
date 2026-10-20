<button class="site-header__navigation-toggle" aria-label="Toggle main menu">
	<span></span>
	<span></span>
	<span></span>
</button>

<nav class="site-header__navigation alignfull" data-js="main-nav">
	<?php wp_nav_menu(
		array(
			'theme_location' => 'primary',
			'depth'          => 2,
		)
	);?>
</nav>

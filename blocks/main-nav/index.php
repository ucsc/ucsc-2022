<button class="site-header__navigation-toggle">
	<span></span>
	<span></span>
	<span></span>
</button>

<nav class="site-header__navigation alignfull" data-js="main-nav">
	<?php echo wp_nav_menu(
		array(
			'theme_location' => 'primary',
		)
	);?>
</nav>

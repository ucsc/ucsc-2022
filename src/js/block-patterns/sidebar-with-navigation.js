const blocks = document.querySelectorAll( '.ucsc__sidebar-with-navigation' );

// Cache object, populated upon initialization.
let c = {};

/**
 * Show or hide the submenu associated with a given toggle button.
 */
const toggleSubmenu = ( toggle ) => {
	const item = toggle.closest( c.itemSelector );
	const submenu = item.querySelector( c.submenuSelector );

	// Hide
	if ( item.classList.contains( c.itemExpandedClass ) ) {
		item.classList.remove( c.itemExpandedClass );
		submenu.setAttribute( 'aria-hidden', true );
		submenu.querySelectorAll( c.linkSelector ).forEach( ( subItemLink ) => {
			subItemLink.setAttribute( 'tabIndex', -1 );
		} );
	}
	// Show
	else {
		item.classList.add( c.itemExpandedClass );
		submenu.setAttribute( 'aria-hidden', false );
		submenu.querySelectorAll( c.linkSelector ).forEach( ( subItemLink ) => {
			subItemLink.removeAttribute( 'tabIndex' );
		} );
	}
};

/**
 * Fix toggle button ARIA attributes, which the core block JS sets incorrectly
 * since we've disabled accordion behavior.
 */
const fixAriaAttributes = () => {
	blocks.forEach( ( block ) => {
		block.querySelectorAll( c.itemSelector ).forEach( ( item ) => {
			const isExpanded = item.classList.contains( c.itemExpandedClass );

			const toggle = item.querySelector( c.toggleSelector );
			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', isExpanded );
			}

			const submenu = item.querySelector( c.submenuSelector );
			if ( submenu ) {
				toggle.setAttribute( 'aria-hidden', ! isExpanded );
			}
		} );
	} );
};

/**
 * Bind event listeners.
 */
const bindEvents = () => {
	blocks.forEach( ( block ) => {
		block.querySelectorAll( c.toggleSelector ).forEach( ( toggle ) => {
			toggle.addEventListener( 'click', () => {
				toggleSubmenu( toggle );
			} );
		} );

		block
			.querySelectorAll( '.' + c.submenuWrapperClass )
			.forEach( ( wrapper ) => {
				wrapper.addEventListener( 'transitionend', () => {
					fixAriaAttributes();
				} );
			} );
	} );
};

/**
 * Set up custom block markup.
 */
const initBlockMarkup = () => {
	blocks.forEach( ( block ) => {
		block.querySelectorAll( c.submenuSelector ).forEach( ( submenu ) => {
			// Initialize submenu ARIA attribute.
			submenu.setAttribute( 'aria-hidden', true );
			submenu
				.querySelectorAll( c.linkSelector )
				.forEach( ( subItemLink ) => {
					subItemLink.setAttribute( 'tabIndex', -1 );
				} );

			// Add a wrapper around submenus in order to do height transitions.
			const item = submenu.closest( c.itemSelector );
			const wrapper = document.createElement( 'div' );
			wrapper.classList.add( c.submenuWrapperClass );
			item.append( wrapper );
			wrapper.append( submenu );
		} );

		// Remove nested submenus and their toggles.
		block
			.querySelectorAll( c.submenuSelector + ' ' + c.submenuSelector )
			.forEach( ( nestedSubmenu ) => {
				const item = nestedSubmenu.closest( c.itemSelector );
				const toggle = item.querySelector( c.toggleSelector );
				toggle.remove();
				nestedSubmenu.remove();
			} );
	} );
};

/**
 * Initialize custom block behaviors.
 */
const init = () => {
	if ( ! blocks.length ) {
		return;
	}

	c = {
		itemSelector: '.wp-block-navigation-item',
		itemExpandedClass: 'ucsc__sidebar-with-navigation--expanded',
		linkSelector: 'a.wp-block-navigation-item__content',
		toggleSelector: '.wp-block-navigation-submenu__toggle',
		submenuWrapperClass: 'wp-block-navigation__submenu-transition-wrapper',
		submenuSelector: '.wp-block-navigation__submenu-container',
	};

	initBlockMarkup();
	bindEvents();
	fixAriaAttributes();
};

export default init;

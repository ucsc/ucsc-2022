const el = document.querySelectorAll( '.ucsc__sidebar-with-navigation' );

const selectors = {
	itemSelector: '.wp-block-navigation-item',
	itemExpandedClass: 'ucsc__sidebar-with-navigation--expanded',
	linkSelector: 'a.wp-block-navigation-item__content',
	toggleSelector: '.wp-block-navigation-submenu__toggle',
	submenuWrapperClass: 'wp-block-navigation__submenu-transition-wrapper',
	submenuSelector: '.wp-block-navigation__submenu-container',
};

/**
 * Show or hide the submenu associated with a given toggle button.
 *
 * @param {HTMLElement} toggle The actioned submenu toggle button.
 */
const toggleSubmenu = ( toggle ) => {
	const item = toggle.closest( selectors.itemSelector );
	const submenu = item.querySelector( selectors.submenuSelector );

	// Hide
	if ( item.classList.contains( selectors.itemExpandedClass ) ) {
		item.classList.remove( selectors.itemExpandedClass );
		submenu.setAttribute( 'aria-hidden', true );
		submenu
			.querySelectorAll( selectors.linkSelector )
			.forEach( ( subItemLink ) => {
				subItemLink.setAttribute( 'tabIndex', -1 );
			} );
		// Show
	} else {
		item.classList.add( selectors.itemExpandedClass );
		submenu.setAttribute( 'aria-hidden', false );
		submenu
			.querySelectorAll( selectors.linkSelector )
			.forEach( ( subItemLink ) => {
				subItemLink.removeAttribute( 'tabIndex' );
			} );
	}
};

/**
 * Fix toggle button ARIA attributes, which the core block JS sets incorrectly
 * since we've disabled accordion behavior.
 */
const fixAriaAttributes = () => {
	el.forEach( ( block ) => {
		block.querySelectorAll( selectors.itemSelector ).forEach( ( item ) => {
			const isExpanded = item.classList.contains(
				selectors.itemExpandedClass
			);

			const toggle = item.querySelector( selectors.toggleSelector );
			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', isExpanded );
			}

			const submenu = item.querySelector( selectors.submenuSelector );
			if ( submenu ) {
				submenu.setAttribute( 'aria-hidden', ! isExpanded );
			}
		} );
	} );
};

/**
 * Bind event listeners.
 */
const bindEvents = () => {
	el.forEach( ( block ) => {
		block
			.querySelectorAll( selectors.toggleSelector )
			.forEach( ( toggle ) => {
				toggle.addEventListener( 'click', () => {
					toggleSubmenu( toggle );
				} );
			} );

		block
			.querySelectorAll( '.' + selectors.submenuWrapperClass )
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
	el.forEach( ( block ) => {
		block
			.querySelectorAll( selectors.submenuSelector )
			.forEach( ( submenu ) => {
				// Initialize submenu ARIA attribute.
				submenu.setAttribute( 'aria-hidden', true );
				submenu
					.querySelectorAll( selectors.linkSelector )
					.forEach( ( subItemLink ) => {
						subItemLink.setAttribute( 'tabIndex', -1 );
					} );

				// Add a wrapper around submenus in order to do height transitions.
				const item = submenu.closest( selectors.itemSelector );
				const wrapper = document.createElement( 'div' );
				wrapper.classList.add( selectors.submenuWrapperClass );
				item.append( wrapper );
				wrapper.append( submenu );
			} );

		// Remove nested submenus and their toggles.
		block
			.querySelectorAll(
				`${ selectors.submenuSelector } ${ selectors.submenuSelector }`
			)
			.forEach( ( nestedSubmenu ) => {
				const item = nestedSubmenu.closest( selectors.itemSelector );
				const toggle = item.querySelector( selectors.toggleSelector );
				toggle.remove();
				nestedSubmenu.remove();
			} );
	} );
};

/**
 * Initialize custom block behaviors.
 */
const init = () => {
	if ( ! el.length ) {
		return;
	}

	initBlockMarkup();
	bindEvents();
	fixAriaAttributes();
};

export default init;

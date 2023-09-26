/**
 * @module Main Navigation
 * @description scripts for the main nav.
 */

import { on } from '../utils/events';
import state from '../config/state';
import { NAVIGATION_BREAKPOINT } from '../config/options';
import * as bodyLock from '../utils/body-lock';

// Cache some elements and state.
const cache = {
	desktopBreakpoint: NAVIGATION_BREAKPOINT,
	trussHeader: null,
	stickyHeader: null,
	stickyHeaderObserver: null,
	nav: null,
	navIsOpen: false,
	navToggle: null,
	menuItems: [],
};

/**
 * Show the main-nav menu on mobile.
 */
const showMenu = () => {
	// Stop observing the sticky header when we open the menu, because it's going
	// to float to the top of the viewport and not reflect our actual scroll
	// position.
	cache.stickyHeaderObserver.unobserve( cache.stickyHeader );
	bodyLock.lock();

	document.documentElement.classList.add( 'main-nav--is-open' );
	if ( ! cache.navIsOpen ) {
		document.documentElement.classList.add( 'main-nav--is-opening' );
	}

	cache.nav.setAttribute( 'aria-hidden', false );
	cache.navToggle.setAttribute( 'aria-label', 'Hide main navigation menu' );
	cache.navIsOpen = true;
};

/**
 * Hide the main-nav menu on mobile.
 */
const hideMenu = () => {
	document.documentElement.classList.remove( 'main-nav--is-open' );
	if ( cache.navIsOpen ) {
		document.documentElement.classList.add( 'main-nav--is-closing' );
	}

	cache.nav.setAttribute( 'aria-hidden', true );
	cache.navToggle.setAttribute( 'aria-label', 'Show main navigation menu' );

	// Reset submenus when the main nav is hidden.
	cache.menuItems.forEach( ( data ) => {
		deactivateMenuItem( data );
	} );

	// If we unlock the body before our closing animation completes, the scroll
	// position will be off in some cases. For that reason, we unlock the body in
	// our transitionended event handler. However, if the nav is already hidden,
	// no transition will occur, so we handle that here.
	if ( ! cache.navIsOpen && bodyLock.isLocked() ) {
		bodyLock.unlock();
	}

	cache.stickyHeaderObserver.observe( cache.stickyHeader );
	cache.navIsOpen = false;
};

/**
 * Toggle the main-nav menu on mobile.
 */
const toggleMenu = () => {
	if ( cache.navIsOpen ) {
		hideMenu();
	} else {
		showMenu();
	}
};

/**
 * Add JS behaviors to the main-nav menu.
 */
const initMainNav = () => {
	cache.nav = document.querySelector( '[data-js="main-nav"]' );
	if ( ! cache.nav ) {
		return;
	}

	// Observer when our sticky header becomes "stuck".
	cache.stickyHeader = document.querySelector( '.header-region' );
	cache.stickyHeaderObserver = new window.IntersectionObserver(
		( entries ) => {
			document.documentElement.classList.toggle(
				'header-region--stuck',
				entries[ 0 ].intersectionRatio < 1
			);
		},
		{
			threshold: 1,
			rootMargin: '-1px 0px 0px 0px',
		}
	);

	// Get our mobile-to-desktop breakpoint.
	const breakpoint = window
		.getComputedStyle( cache.nav )
		.getPropertyValue( '--desktop-breakpoint' )
		.replace( 'px', '' );
	if ( breakpoint ) {
		cache.desktopBreakpoint = parseInt( breakpoint );
	}

	// Update CSS classes for navigation animation states.
	const navigation = document.querySelector( '.site-header__navigation' );
	navigation.addEventListener( 'transitionend', () => {
		document.documentElement.classList.remove(
			'main-nav--is-opening',
			'main-nav--is-closing'
		);
		// Unlock the body after the nav is closed.
		if ( ! cache.navIsOpen && bodyLock.isLocked() ) {
			bodyLock.unlock();
		}
	} );

	// Add mobile nav toggle.
	cache.navToggle = document.querySelector(
		'.site-header__navigation-toggle'
	);
	cache.navToggle.addEventListener( 'click', toggleMenu );

	// Add menu item behaviors.
	cache.nav
		.querySelectorAll( '.menu > .menu-item' )
		.forEach( ( menuItem ) => {
			// Cache menu item data, including references to the different elements
			// connected to this menu item.
			const data = {
				menuItem,
				submenu: null,
				toggle: null,
				links: null,
				isActive: false,
			};
			cache.menuItems.push( data );

			// Listen for mouse and keyboard interactions.
			data.menuItem.addEventListener( 'focusin', () =>
				updateMenuItem( data )
			);
			data.menuItem.addEventListener( 'focusout', () =>
				updateMenuItem( data )
			);
			data.menuItem.addEventListener( 'mouseover', () =>
				updateMenuItem( data )
			);
			data.menuItem.addEventListener( 'mouseout', () =>
				updateMenuItem( data )
			);

			// There's nothing more to do if this menu item has no submenu.
			const submenuEl =
				data.menuItem.querySelector( ':scope > .sub-menu' );
			if ( ! submenuEl ) {
				return;
			}

			// Cache submenu links.
			data.links = submenuEl.querySelectorAll( 'a' );

			// Add a wrapper around the submenu for animating height transitions.
			data.submenu = document.createElement( 'div' );
			data.submenu.classList.add( 'sub-menu-wrapper' );
			data.submenu.append( submenuEl );

			// Add a submenu toggle button.
			data.toggle = document.createElement( 'button' );
			data.toggle.classList.add( 'sub-menu-toggle' );
			data.toggle.addEventListener( 'click', () => {
				if ( data.isActive ) {
					deactivateMenuItem( data );
				} else {
					activateMenuItem( data );
				}
			} );

			// Insert the toggle before the submenu.
			data.menuItem.append( data.toggle );
			data.menuItem.append( data.submenu );
		} );

	// Start closed.
	hideMenu();
};

/**
 * Check if our main-nav menu is in mobile mode (vs desktop mode).
 */
const menuIsMobile = () => {
	return (
		Math.max(
			document.documentElement.clientWidth || 0,
			window.innerWidth || 0
		) < cache.desktopBreakpoint
	);
};

/**
 * Update a top-level menu item's active status.
 *
 * @param {Object} data The menu item data object from cache.menuItems.
 */
const updateMenuItem = ( data ) => {
	// On mobile, only open menu items when the associated toggle button is
	// pressed.
	if ( menuIsMobile() ) {
		return;
	}

	// On desktop, open menu items on :focus-within or :hover.
	const shouldBeActive =
		data.menuItem.matches( ':focus-within' ) ||
		data.menuItem.matches( ':hover' );
	if ( data.isActive && ! shouldBeActive ) {
		deactivateMenuItem( data );
	} else if ( ! data.isActive && shouldBeActive ) {
		activateMenuItem( data );
	}
};

/**
 * Mark a top-level menu item as active and show its submenu if it has one.
 *
 * @param {Object} data The menu item data object from cache.menuItems.
 */
const activateMenuItem = ( data ) => {
	data.menuItem.classList.add( 'menu-item--is-active' );
	if ( data.submenu ) {
		data.submenu.setAttribute( 'aria-hidden', false );
		data.toggle.setAttribute( 'aria-label', 'Hide submenu' );
		data.links.forEach( ( link ) => {
			link.removeAttribute( 'tabIndex' );
		} );
	}
	data.isActive = true;
};

/**
 * Mark a top-level menu item as inactive and hide its submenu if it has one.
 *
 * @param {Object} data The menu item data object from cache.menuItems.
 */
const deactivateMenuItem = ( data ) => {
	data.menuItem.classList.remove( 'menu-item--is-active' );
	if ( data.submenu ) {
		data.submenu.setAttribute( 'aria-hidden', true );
		data.toggle.setAttribute( 'aria-label', 'Show submenu' );
		data.links.forEach( ( link ) => {
			link.setAttribute( 'tabIndex', -1 );
		} );
	}
	data.isActive = false;
};

const handleResize = () => {
	// Find the site header height.
	const siteHeader = document.querySelector( '.site-header' );
	document.documentElement.style.setProperty(
		'--site-header-height',
		siteHeader.clientHeight + 'px'
	);

	const navElement = document.querySelector( '[data-js="main-nav"]' );
	if ( state.v_width >= NAVIGATION_BREAKPOINT ) {
		navElement.setAttribute( 'aria-hidden', 'false' );
		return;
	}

	navElement.setAttribute( 'aria-hidden', 'true' );
};

const bindEvents = () => {
	on( document, 'ucsc/resize_executed', handleResize );
};

const init = () => {
	if ( ! document.querySelector( '[data-js="main-nav"]' ) ) {
		return;
	}

	initMainNav();
	bindEvents();
	handleResize(); // run on load to set the proper attribute.
};

export default init;

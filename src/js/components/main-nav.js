/**
 * @module Main Navigation
 * @description scripts for the main nav.
 */

import { on } from '../utils/events';
import state from '../config/state';
import { NAVIGATION_BREAKPOINT } from '../config/options';
import * as bodyLock from '../utils/body-lock';

// Cache some elements.
const cache = {
	desktopBreakpoint: NAVIGATION_BREAKPOINT,
	nav: null,
	navToggle: null,
	menuItems: [],
};

/**
 * Add JS behaviors to the main-nav menu.
 */
function initMainNav() {
	cache.nav = document.querySelector( '[data-js="main-nav"]' );
	if ( ! cache.nav ) {
		return;
	}

	// Get our mobile-to-desktop breakpoint.
	const breakpoint = window
		.getComputedStyle( cache.nav )
		.getPropertyValue( '--desktop-breakpoint' )
		.replace( 'px', '' );
	if ( breakpoint ) {
		cache.desktopBreakpoint = parseInt( breakpoint );
	}

	// Add mobile nav toggle.
	cache.navToggle = document.querySelector(
		'.site-header__navigation-toggle'
	);
	cache.navToggle.addEventListener( 'click', () => {
		if ( document.body.classList.contains( 'main-nav--is-open' ) ) {
			hideMenu();
		} else {
			showMenu();
		}
	} );

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
}

/**
 * Show the main-nav menu on mobile.
 */
function showMenu() {
	bodyLock.lock();
	document.body.classList.add( 'main-nav--is-open' );
	cache.nav.setAttribute( 'aria-hidden', false );
	cache.navToggle.setAttribute( 'aria-label', 'Hide main navigation menu' );
}

/**
 * Hide the main-nav menu on mobile.
 */
function hideMenu() {
	bodyLock.unlock();
	document.body.classList.remove( 'main-nav--is-open' );
	cache.nav.setAttribute( 'aria-hidden', true );
	cache.navToggle.setAttribute( 'aria-label', 'Show main navigation menu' );

	// Reset submenus on hide.
	cache.menuItems.forEach( ( data ) => {
		deactivateMenuItem( data );
	} );
}

/**
 * Check if our main-nav menu is in mobile mode (vs desktop mode).
 */
function menuIsMobile() {
	return (
		Math.max(
			document.documentElement.clientWidth || 0,
			window.innerWidth || 0
		) < cache.desktopBreakpoint
	);
}

/**
 * Update a top-level menu item's active status.
 *
 * @param {Object} data The menu item data object from cache.menuItems.
 */
function updateMenuItem( data ) {
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
}

/**
 * Mark a top-level menu item as active and show its submenu if it has one.
 *
 * @param {Object} data The menu item data object from cache.menuItems.
 */
function activateMenuItem( data ) {
	data.menuItem.classList.add( 'menu-item--is-active' );
	if ( data.submenu ) {
		data.submenu.setAttribute( 'aria-hidden', false );
		data.toggle.setAttribute( 'aria-label', 'Hide submenu' );
		data.links.forEach( ( link ) => {
			link.removeAttribute( 'tabIndex' );
		} );
	}
	data.isActive = true;
}

/**
 * Mark a top-level menu item as inactive and hide its submenu if it has one.
 *
 * @param {Object} data The menu item data object from cache.menuItems.
 */
function deactivateMenuItem( data ) {
	data.menuItem.classList.remove( 'menu-item--is-active' );
	if ( data.submenu ) {
		data.submenu.setAttribute( 'aria-hidden', true );
		data.toggle.setAttribute( 'aria-label', 'Show submenu' );
		data.links.forEach( ( link ) => {
			link.setAttribute( 'tabIndex', -1 );
		} );
	}
	data.isActive = false;
}

const handleResize = () => {
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

/**
 * @module Main Navigation
 * @description scripts for the main nav.
 */

import { on } from '../utils/events';
import state from '../config/state';
import { NAVIGATION_BREAKPOINT } from '../config/options';
import * as bodyLock from '../utils/body-lock';

// Matches the 0.2s height transition on .site-header__navigation, plus slack.
const ANIMATION_CLEANUP_MS = 300;

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
	animationTimer: null,
};

/**
 * Remove the transient animation-state classes.
 */
const clearAnimationClasses = () => {
	window.clearTimeout(cache.animationTimer);
	document.documentElement.classList.remove(
		'main-nav--is-opening',
		'main-nav--is-closing'
	);
};

/**
 * Guarantee the animation-state classes are cleared even when the height
 * transition never runs (invalid height calc) or is interrupted (rapid
 * toggling) — in both cases no transitionend fires. Correctness must never
 * depend on transition events.
 */
const scheduleAnimationCleanup = () => {
	window.clearTimeout(cache.animationTimer);
	cache.animationTimer = window.setTimeout(
		clearAnimationClasses,
		ANIMATION_CLEANUP_MS
	);
};

/**
 * Publish the site header's current height for the menu's height calc.
 * Null-guarded: the .site-header class comes from template-part block
 * attributes and is not guaranteed to exist in the rendered markup.
 */
const measureSiteHeader = () => {
	const siteHeader = document.querySelector('.site-header');
	if (siteHeader) {
		document.documentElement.style.setProperty(
			'--site-header-height',
			siteHeader.clientHeight + 'px'
		);
	}
};

/**
 * Anchor the open panel to the sticky header's real bottom edge in viewport
 * coordinates. The panel is position: fixed, so this stays correct whether
 * the Truss header above is expanded, missing, or replaced by a banner —
 * the theme cannot rely on collapsing the Truss header (its internals are
 * shadow-DOM encapsulated, so theme selectors can't reach it).
 */
const measurePanelTop = () => {
	const anchor = cache.stickyHeader || document.querySelector('.site-header');
	if (anchor) {
		document.documentElement.style.setProperty(
			'--nav-panel-top',
			Math.max(0, anchor.getBoundingClientRect().bottom) + 'px'
		);
	}
};

/**
 * Show the main-nav menu on mobile.
 */
const showMenu = () => {
	// Re-measure the header at the moment we open. The load-time measurement
	// goes stale once fonts and the async Truss header settle, and a
	// too-small value makes the panel extend past the viewport bottom with
	// its tail unreachable.
	measureSiteHeader();
	measurePanelTop();

	// Stop observing the sticky header when we open the menu, because it's going
	// to float to the top of the viewport and not reflect our actual scroll
	// position. Clear its class too: the observer can't update it while the
	// menu is open, and a stale "stuck" state overrides the collapsed Truss
	// header in CSS.
	cache.stickyHeaderObserver.unobserve(cache.stickyHeader);
	document.documentElement.classList.remove('header-region--stuck');
	bodyLock.lock();

	document.documentElement.classList.remove('main-nav--is-closing');
	document.documentElement.classList.add('main-nav--is-open');
	if (!cache.navIsOpen) {
		document.documentElement.classList.add('main-nav--is-opening');
	}
	scheduleAnimationCleanup();

	cache.nav.setAttribute('aria-hidden', false);
	cache.navToggle.setAttribute('aria-label', 'Hide main navigation menu');
	cache.navIsOpen = true;
};

/**
 * Hide the main-nav menu on mobile.
 */
const hideMenu = () => {
	document.documentElement.classList.remove(
		'main-nav--is-open',
		'main-nav--is-opening'
	);
	if (cache.navIsOpen) {
		document.documentElement.classList.add('main-nav--is-closing');
	}
	scheduleAnimationCleanup();

	cache.nav.setAttribute('aria-hidden', true);
	cache.navToggle.setAttribute('aria-label', 'Show main navigation menu');

	// Reset submenus when the main nav is hidden.
	cache.menuItems.forEach((data) => {
		deactivateMenuItem(data);
	});

	// Unlock the body immediately. Deferring this to the closing transition's
	// end left the page frozen whenever that transition never ran or was
	// interrupted (no transitionend fires in either case).
	if (bodyLock.isLocked()) {
		bodyLock.unlock();
	}

	// Re-observe only after the body is unlocked so the observer measures
	// real scroll geometry.
	cache.stickyHeaderObserver.observe(cache.stickyHeader);
	cache.navIsOpen = false;
};

/**
 * Toggle the main-nav menu on mobile.
 */
const toggleMenu = () => {
	if (cache.navIsOpen) {
		hideMenu();
	} else {
		showMenu();
	}
};

/**
 * Add JS behaviors to the main-nav menu.
 */
const initMainNav = () => {
	cache.nav = document.querySelector('[data-js="main-nav"]');
	if (!cache.nav) {
		return;
	}

	// Observer when our sticky header becomes "stuck".
	cache.stickyHeader = document.querySelector('.header-region');
	cache.stickyHeaderObserver = new window.IntersectionObserver(
		(entries) => {
			document.documentElement.classList.toggle(
				'header-region--stuck',
				entries[0].intersectionRatio < 1
			);
		},
		{
			threshold: 1,
			rootMargin: '-1px 0px 0px 0px',
		}
	);

	// Get our mobile-to-desktop breakpoint.
	const breakpoint = window
		.getComputedStyle(cache.nav)
		.getPropertyValue('--desktop-breakpoint')
		.replace('px', '');
	if (breakpoint) {
		cache.desktopBreakpoint = parseInt(breakpoint);
	}

	// Clear the animation-state classes when the menu's own height transition
	// settles. These classes are cosmetic; the timer in show/hideMenu backstops
	// this cleanup, so correctness never depends on these events firing. The
	// guards matter: transitions on descendants (submenu wrappers, link
	// hovers) bubble up to this element.
	const onTransitionSettled = (event) => {
		if (
			event.target !== event.currentTarget ||
			event.propertyName !== 'height'
		) {
			return;
		}
		clearAnimationClasses();
	};
	cache.nav.addEventListener('transitionend', onTransitionSettled);
	cache.nav.addEventListener('transitioncancel', onTransitionSettled);

	// Add mobile nav toggle.
	cache.navToggle = document.querySelector('.site-header__navigation-toggle');
	cache.navToggle.addEventListener('click', toggleMenu);

	// Add menu item behaviors.
	cache.nav.querySelectorAll('.menu > .menu-item').forEach((menuItem) => {
		// Cache menu item data, including references to the different elements
		// connected to this menu item.
		const data = {
			menuItem,
			submenu: null,
			toggle: null,
			links: null,
			isActive: false,
		};
		cache.menuItems.push(data);

		// Listen for mouse and keyboard interactions.
		data.menuItem.addEventListener('focusin', () => updateMenuItem(data));
		data.menuItem.addEventListener('focusout', () => updateMenuItem(data));
		data.menuItem.addEventListener('mouseover', () => updateMenuItem(data));
		data.menuItem.addEventListener('mouseout', () => updateMenuItem(data));

		// There's nothing more to do if this menu item has no submenu.
		const submenuEl = data.menuItem.querySelector(':scope > .sub-menu');
		if (!submenuEl) {
			return;
		}

		// Cache submenu links.
		data.links = submenuEl.querySelectorAll('a');

		// Add a wrapper around the submenu for animating height transitions.
		data.submenu = document.createElement('div');
		data.submenu.classList.add('sub-menu-wrapper');
		data.submenu.append(submenuEl);

		// Add a submenu toggle button.
		data.toggle = document.createElement('button');
		data.toggle.classList.add('sub-menu-toggle');
		data.toggle.addEventListener('click', () => {
			if (data.isActive) {
				deactivateMenuItem(data);
			} else {
				activateMenuItem(data);
			}
		});

		// Insert the toggle before the submenu.
		data.menuItem.append(data.toggle);
		data.menuItem.append(data.submenu);
	});

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
const updateMenuItem = (data) => {
	// On mobile, only open menu items when the associated toggle button is
	// pressed.
	if (menuIsMobile()) {
		return;
	}

	// On desktop, open menu items on :focus-within or :hover.
	const shouldBeActive =
		data.menuItem.matches(':focus-within') ||
		data.menuItem.matches(':hover');
	if (data.isActive && !shouldBeActive) {
		deactivateMenuItem(data);
	} else if (!data.isActive && shouldBeActive) {
		activateMenuItem(data);
	}
};

/**
 * Mark a top-level menu item as active and show its submenu if it has one.
 *
 * @param {Object} data The menu item data object from cache.menuItems.
 */
const activateMenuItem = (data) => {
	data.menuItem.classList.add('menu-item--is-active');
	if (data.submenu) {
		data.submenu.setAttribute('aria-hidden', false);
		data.toggle.setAttribute('aria-label', 'Hide submenu');
		data.links.forEach((link) => {
			link.removeAttribute('tabIndex');
		});
	}
	data.isActive = true;
};

/**
 * Mark a top-level menu item as inactive and hide its submenu if it has one.
 *
 * @param {Object} data The menu item data object from cache.menuItems.
 */
const deactivateMenuItem = (data) => {
	data.menuItem.classList.remove('menu-item--is-active');
	if (data.submenu) {
		data.submenu.setAttribute('aria-hidden', true);
		data.toggle.setAttribute('aria-label', 'Show submenu');
		data.links.forEach((link) => {
			link.setAttribute('tabIndex', -1);
		});
	}
	data.isActive = false;
};

const handleResize = () => {
	measureSiteHeader();

	const navElement = document.querySelector('[data-js="main-nav"]');
	if (!navElement) {
		return;
	}

	if (state.v_width >= cache.desktopBreakpoint) {
		// Leaving mobile with the menu open: close it so the body scroll lock
		// doesn't leak into the desktop layout.
		if (cache.navIsOpen) {
			hideMenu();
		}
		navElement.setAttribute('aria-hidden', 'false');
		return;
	}

	// Never stamp aria-hidden onto the open menu — mobile browsers fire
	// resize while it's up (URL bar collapse, rotation, keyboard). Do keep
	// the panel anchored to the header, which may have changed height.
	if (cache.navIsOpen) {
		measurePanelTop();
		return;
	}

	navElement.setAttribute('aria-hidden', 'true');
};

const bindEvents = () => {
	on(document, 'ucsc/resize_executed', handleResize);
};

const init = () => {
	if (!document.querySelector('[data-js="main-nav"]')) {
		return;
	}

	initMainNav();
	bindEvents();
	handleResize(); // run on load to set the proper attribute.
};

export default init;

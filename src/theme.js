// front end scripts

// Cache some elements.
const el = {
	nav: null,
	navToggle: null,
};

document.addEventListener('DOMContentLoaded', function () {
	initMainNav();
});

/**
 * Add JS behaviors to the main-nav menu.
 */
function initMainNav() {
	el.nav = document.querySelector('[data-js="main-nav"]');
	if (!el.nav) {
		return;
	}

	// Add mobile nav toggle.
	el.navToggle = document.querySelector('.site-header__navigation-toggle');
	el.navToggle.addEventListener('click', () => {
		if (document.body.classList.contains('main-nav--is-open')) {
			collapseMenu();
		} else {
			expandMenu();
		}
	});

	// Add exapnd/collapse behavior to menu items with sub-menus.
	el.nav.querySelectorAll('.menu-item-has-children').forEach((parent) => {
		// Add a wrapper around the sub-menu for animating height transitions.
		const submenuWrapper = document.createElement('div');
		const submenu = parent.querySelector('.sub-menu');
		submenuWrapper.append(submenu);
		submenuWrapper.classList.add('sub-menu-wrapper');

		// Add a sub-menu toggle button to each sub-menu parent.
		const toggle = document.createElement('button');
		toggle.classList.add('sub-menu-toggle');
		toggle.addEventListener('click', () => {
			if (submenuWrapper.getAttribute('aria-hidden') === 'true') {
				expandSubmenu(parent, toggle, submenuWrapper, submenu);
			} else {
				collapseSubmenu(parent, toggle, submenuWrapper, submenu);
			}
		});

		// Add the toggle before the sub-menu wrapper.
		parent.append(toggle);
		parent.append(submenuWrapper);
	});

	// Start closed.
	collapseMenu();
}

/**
 * Expand the main-nav menu on mobile.
 */
function expandMenu() {
	document.body.classList.add('main-nav--is-open');
	el.nav.setAttribute('aria-hidden', false);
	el.navToggle.setAttribute('aria-label', 'Close main navigation menu');
}

/**
 * Collapse the main-nav menu on mobile.
 */
function collapseMenu() {
	document.body.classList.remove('main-nav--is-open');
	el.nav.setAttribute('aria-hidden', true);
	el.navToggle.setAttribute('aria-label', 'Open main navigation menu');

	// Reset submenus on collapse.
	el.nav.querySelectorAll('.menu-item-has-children').forEach((parent) => {
		collapseSubmenu(parent);
	});
}

/**
 * Expand a main-nav submenu on mobile.
 *
 * @param {HTMLElement} parent The menu item parent of the sub-menu to expand.
 */
function expandSubmenu(parent) {
	const { submenuWrapper, toggle, links } = querySubmenuElements(parent);
	parent.classList.add('menu-item--expanded');
	submenuWrapper.setAttribute('aria-hidden', false);
	toggle.setAttribute('aria-label', 'Collapse submenu');
	links.forEach((link) => {
		link.removeAttribute('tabIndex');
	});
}

/**
 * Collapse a main-nav submenu on mobile.
 *
 * @param {HTMLElement} parent The menu item parent of the sub-menu to collapse.
 */
function collapseSubmenu(parent) {
	const { submenuWrapper, toggle, links } = querySubmenuElements(parent);
	parent.classList.remove('menu-item--expanded');
	submenuWrapper.setAttribute('aria-hidden', true);
	toggle.setAttribute('aria-label', 'Expand submenu');
	links.forEach((link) => {
		link.setAttribute('tabIndex', -1);
	});
}

/**
 * Helper function for finding the different parts of a submenu from its menu
 * item parent in the DOM. Only works once the main-nav has been initialized.
 *
 * @param {HTMLElement} parent The menu item parent of the sub-menu to get
 *                             elements for.
 */
function querySubmenuElements(parent) {
	return {
		parent,
		submenuWrapper: parent.querySelector('.sub-menu-wrapper'),
		toggle: parent.querySelector('.sub-menu-toggle'),
		links: parent.querySelectorAll('.sub-menu a'),
	};
}

// front end scripts
document.addEventListener('DOMContentLoaded', function () {
	initMainNav();
});

/**
 * Add expand/collapse behaviors for main navigation.
 */
function initMainNav() {
	const nav = document.querySelector('[data-js="main-nav"]');
	if (!nav) {
		return;
	}

	// Add exapnd/collapse behavior to menu items with sub-menus.
	nav.querySelectorAll('.menu-item-has-children').forEach((parent) => {
		// Add a wrapper to the sub-menu for height transitions.
		const submenuWrapper = document.createElement('div');
		submenuWrapper.classList.add('sub-menu-wrapper');

		const submenu = parent.querySelector('.sub-menu');
		submenuWrapper.append(submenu);

		// Add a sub-menu toggle button.
		const toggle = document.createElement('button');
		toggle.type = 'button';
		toggle.setAttribute('aria-label', 'Expand submenu');
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

		// Start collapsed.
		collapseSubmenu(parent, toggle, submenuWrapper, submenu);
	});
}

function collapseSubmenu(parent, toggle, submenuWrapper, submenu) {
	parent.classList.remove('menu-item--expanded');
	submenuWrapper.setAttribute('aria-hidden', true);
	toggle.setAttribute('aria-label', 'Expand submenu');
	submenu.querySelectorAll('a').forEach((link) => {
		link.setAttribute('tabIndex', -1);
	});
}

function expandSubmenu(parent, toggle, submenuWrapper, submenu) {
	parent.classList.add('menu-item--expanded');
	submenuWrapper.setAttribute('aria-hidden', false);
	toggle.setAttribute('aria-label', 'Collapse submenu');
	submenu.querySelectorAll('a').forEach((link) => {
		link.removeAttribute('tabIndex');
	});
}

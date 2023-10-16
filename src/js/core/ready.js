/**
 * @module On Ready JS kickoff
 * @description when the client side is ready, kick off these core JS functions.
 */

import debounce from 'lodash/debounce';
import { on, ready } from '../utils/events';
import resize from './resize';
import components from './components';
import blockPatterns from './block-patterns';
import viewportDims from './viewport-dims';

const bindEvents = () => {
	on( window, 'resize', debounce( resize, 200, false ) );
};

const init = () => {
	// set initial states
	viewportDims();

	// initialize global events
	bindEvents();

	// initialize components
	components();

	// initialize block pattern behaviors
	blockPatterns();
};

/**
 * @function domReady
 * @description Export our dom ready enabled init.
 */

const domReady = () => {
	ready( init );
};

export default domReady;

let scroll = 0;
const scroller = document.documentElement;
let locked = false;

/**
 * @function isLocked
 * @description Returns state
 */

const isLocked = () => locked;

/**
 * @function lock
 * @description Lock the body at a particular position and prevent scroll,
 * use margin to simulate original scroll position.
 */

const lock = () => {
	const style = document.body.style;
	scroll = scroller.scrollTop;
	locked = true;

	style.position = 'fixed';
	style.marginTop = `-${scroll}px`;
};

/**
 * @function unlock
 * @description Unlock the body and return it to its actual scroll position.
 */

const unlock = () => {
	const style = document.body.style;

	style.position = 'static';
	style.marginTop = '0px';

	scroller.scrollTop = scroll;
	locked = false;
};

export { lock, unlock, isLocked };

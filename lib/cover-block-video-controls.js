(function () {
	function buildControls(video, cover) {
		// Video plays and loops by default; autoplay is disabled below when
		// the user prefers reduced motion.

		// Both icons share the same 24x24 viewBox so they render at an
		// identical height; only the path differs.
		const ICON_PAUSE =
			'<svg class="cv-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M7 5h3v14H7zM14 5h3v14h-3z"/></svg>';
		const ICON_PLAY =
			'<svg class="cv-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M8 5v14l11-7z"/></svg>';
		const ICON_CC =
			'<svg class="cv-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M19 4H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-8 7H9.5v-.5h-2v3h2V13H11v1c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1v-4c0-.55.45-1 1-1h3c.55 0 1 .45 1 1v1zm7 0h-1.5v-.5h-2v3h2V13H18v1c0 .55-.45 1-1 1h-3c-.55 0-1-.45-1-1v-4c0-.55.45-1 1-1h3c.55 0 1 .45 1 1v1z"/></svg>';

		const bar = document.createElement('div');
		bar.className = 'cv-controls';
		bar.innerHTML = `
    <button class="cv-btn cv-play" aria-label="Pause">${ICON_PAUSE}<span class="cv-label">Pause</span></button>
    <button class="cv-btn cv-cc"   aria-label="Turn captions on" aria-pressed="false">${ICON_CC}<span class="cv-label">Captions</span></button>
  `;
		cover.insertAdjacentElement('afterend', bar);

		const playBtn = bar.querySelector('.cv-play');
		const ccBtn = bar.querySelector('.cv-cc');

		// Play / pause — sync button to actual video state
		const playLabel = playBtn.querySelector('.cv-label');
		const syncPlayBtn = () => {
			const playing = !video.paused;
			playBtn.querySelector('.cv-icon').outerHTML = playing
				? ICON_PAUSE
				: ICON_PLAY;
			playLabel.textContent = playing ? 'Pause' : 'Play';
			playBtn.setAttribute('aria-label', playing ? 'Pause' : 'Play');
		};
		syncPlayBtn();

		playBtn.addEventListener('click', () => {
			video.paused ? video.play() : video.pause();
		});
		video.addEventListener('play', syncPlayBtn);
		video.addEventListener('pause', syncPlayBtn);

		// Respect prefers-reduced-motion: don't autoplay the background video.
		// Strip the autoplay attribute so it can't restart, and pause anything
		// the browser already began. The user can still start it manually.
		const reduceMotion = window.matchMedia(
			'(prefers-reduced-motion: reduce)'
		);
		const applyReducedMotion = () => {
			if (reduceMotion.matches) {
				video.removeAttribute('autoplay');
				video.pause();
			}
		};
		applyReducedMotion();
		reduceMotion.addEventListener('change', applyReducedMotion);

		// Captions — the button is useless without a track, so remove it
		// outright; the hidden attribute would be overridden by the
		// display rule on .cv-btn.
		const track = video.textTracks && video.textTracks[0];
		if (!track) {
			ccBtn.remove();
			return;
		}
		track.mode = 'hidden';

		ccBtn.addEventListener('click', () => {
			const on = track.mode === 'showing';
			track.mode = on ? 'hidden' : 'showing';
			ccBtn.setAttribute('aria-pressed', String(!on));
			ccBtn.setAttribute(
				'aria-label',
				on ? 'Turn captions on' : 'Turn captions off'
			);
			ccBtn.classList.toggle('is-on', !on);
		});
	}

	document.querySelectorAll('.wp-block-cover').forEach((cover) => {
		const video = cover.querySelector('.wp-block-cover__video-background');
		if (video) buildControls(video, cover);
	});
})();

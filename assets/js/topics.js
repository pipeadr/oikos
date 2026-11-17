/**
 * Oikos — Grilla de temas: abrir y cerrar tarjetas.
 *
 * - Una sola tarjeta abierta por sección. Presionar la abierta la cierra;
 *   presionar otra cierra la anterior.
 * - Clic fuera de la tarjeta abierta o Esc la cierran (con Esc el foco
 *   vuelve a su botón).
 * - El estado vive en aria-expanded del botón y .is-open en el <li>. La que
 *   se cierra lleva .is-closing mientras anima, para quedar por encima de las
 *   demás pero debajo de la que se abre.
 * - La dirección de apertura la decide el CSS según la columna real.
 *
 * Sin JS los botones llegan con aria-expanded="true" (la descripción se ve);
 * aquí pasan a false.
 */
(() => {
	'use strict';

	const sections = document.querySelectorAll('[data-topics]');

	if (!sections.length) {
		return;
	}

	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	/** Duración de --transition-expand en ms (para quitar .is-closing). */
	const expandDuration = (el) => {
		if (reducedMotion.matches) {
			return 0;
		}

		const value = getComputedStyle(el).getPropertyValue('--transition-expand').trim();
		const match = value.match(/^([\d.]+)(m?s)/);

		if (!match) {
			return 350;
		}

		return parseFloat(match[1]) * ('s' === match[2] ? 1000 : 1);
	};

	const initSection = (section) => {
		const buttons = Array.from(section.querySelectorAll('.oikos-topics__button'));

		if (!buttons.length) {
			return;
		}

		let openButton = null;
		const closingTimers = new WeakMap();

		const itemOf = (button) => button.closest('.oikos-topics__item');

		const close = (button) => {
			const item = itemOf(button);

			button.setAttribute('aria-expanded', 'false');
			item.classList.remove('is-open');
			item.classList.add('is-closing');

			window.clearTimeout(closingTimers.get(item));
			closingTimers.set(
				item,
				window.setTimeout(() => item.classList.remove('is-closing'), expandDuration(item))
			);

			if (openButton === button) {
				openButton = null;
				document.removeEventListener('click', onDocumentClick);
				document.removeEventListener('keydown', onKeydown);
			}
		};

		const open = (button) => {
			if (openButton && openButton !== button) {
				close(openButton);
			}

			const item = itemOf(button);

			window.clearTimeout(closingTimers.get(item));
			item.classList.remove('is-closing');
			item.classList.add('is-open');
			button.setAttribute('aria-expanded', 'true');

			if (!openButton) {
				document.addEventListener('click', onDocumentClick);
				document.addEventListener('keydown', onKeydown);
			}

			openButton = button;
		};

		// Clic fuera de la tarjeta abierta (incluye otras zonas de la página).
		function onDocumentClick(event) {
			if (openButton && !itemOf(openButton).contains(event.target)) {
				close(openButton);
			}
		}

		function onKeydown(event) {
			if ('Escape' !== event.key || !openButton) {
				return;
			}

			const button = openButton;

			close(button);
			button.focus();
		}

		buttons.forEach((button) => {
			button.setAttribute('aria-expanded', 'false');

			button.addEventListener('click', () => {
				if (openButton === button) {
					close(button);
				} else {
					open(button);
				}
			});
		});

		section.classList.add('is-ready');
	};

	sections.forEach(initSection);
})();

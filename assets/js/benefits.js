/**
 * Oikos — Beneficios con foto que cambia (template-parts/sections/benefits.php).
 *
 * - Clic, Enter o Espacio en un beneficio (es un <button>) lo activa: mueve
 *   .is-active y aria-current y hace el fundido a su foto (data-photo; si es
 *   la misma, no cambia nada). Elegir un beneficio detiene la rotación: el
 *   botón de pausa queda en "Reanudar" por si se quiere volver a activar.
 * - Rotación automática (data-autoplay, 2+ beneficios, sin movimiento
 *   reducido): un temporizador pasa al siguiente cada data-interval segundos
 *   (al final vuelve al primero). Al pausar guarda el tiempo restante y al
 *   reanudar sigue desde ahí. La línea de progreso es una animación CSS de la
 *   misma duración que se congela con la misma clase (.is-paused).
 *   Pausan: mouse encima, foco de teclado dentro y sección fuera de pantalla
 *   (IntersectionObserver). La pausa del usuario (botón o elección) quita
 *   .is-rotating: al reanudar, el beneficio activo empieza su tiempo de cero.
 * - Sin aria-live: los cambios automáticos no se anuncian.
 */
(() => {
	'use strict';

	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	const initBenefits = (root) => {
		const items = Array.from(root.querySelectorAll('.oikos-benefits__item'));
		const buttons = items.map((item) => item.querySelector('.oikos-benefits__button'));
		const photos = Array.from(root.querySelectorAll('[data-photo-index]'));
		const toggle = root.querySelector('.oikos-benefits__toggle');

		if (items.length < 2 || buttons.some((button) => !button)) {
			return;
		}

		let current = Math.max(0, items.findIndex((item) => item.classList.contains('is-active')));

		const show = (index) => {
			current = index;

			items.forEach((item, i) => {
				const active = i === index;
				item.classList.toggle('is-active', active);
				if (active) {
					buttons[i].setAttribute('aria-current', 'true');
				} else {
					buttons[i].removeAttribute('aria-current');
				}
			});

			const photo = items[index].dataset.photo;
			photos.forEach((img) => img.classList.toggle('is-active', img.dataset.photoIndex === photo));
		};

		// --- Rotación ---------------------------------------------------------

		const canRotate = root.hasAttribute('data-autoplay') && Boolean(toggle);
		const interval = Math.min(20, Math.max(4, Number(root.dataset.interval) || 7)) * 1000;
		const pause = {
			hover: false,
			focus: false,
			offscreen: 'IntersectionObserver' in window,
			user: false,
		};

		let timer = 0;
		let startedAt = 0;
		let remaining = interval;

		const enabled = () => canRotate && !reducedMotion.matches;
		const paused = () => pause.hover || pause.focus || pause.offscreen;
		const running = () => enabled() && !pause.user && !paused();

		// Detiene el temporizador y guarda cuánto le faltaba.
		const freeze = () => {
			if (timer) {
				window.clearTimeout(timer);
				timer = 0;
				remaining = Math.max(0, remaining - (performance.now() - startedAt));
			}
		};

		const next = () => {
			timer = 0;
			remaining = interval;
			show((current + 1) % items.length);
			if (running()) {
				startedAt = performance.now();
				timer = window.setTimeout(next, remaining);
			}
		};

		const update = () => {
			const on = enabled();

			freeze();

			// Sin rotación (usuario o movimiento reducido), el próximo arranque
			// empieza de cero, igual que la línea (se reinicia con .is-rotating).
			if (!on || pause.user) {
				remaining = interval;
			}

			root.classList.toggle('is-rotating', on && !pause.user);
			root.classList.toggle('is-paused', paused());

			if (toggle) {
				toggle.hidden = !on;
				toggle.dataset.state = pause.user ? 'paused' : 'playing';
				toggle.setAttribute('aria-label', pause.user ? toggle.dataset.labelPlay : toggle.dataset.labelPause);
			}

			if (running()) {
				startedAt = performance.now();
				timer = window.setTimeout(next, remaining);
			}
		};

		buttons.forEach((button, i) => {
			button.addEventListener('click', () => {
				show(i);
				pause.user = true;
				update();
			});
		});

		if (canRotate) {
			toggle.addEventListener('click', () => {
				pause.user = !pause.user;
				update();
			});

			// Solo el mouse pausa: en pantallas táctiles el "hover" emulado no termina nunca.
			root.addEventListener('pointerenter', (event) => {
				if (event.pointerType === 'mouse') {
					pause.hover = true;
					update();
				}
			});
			root.addEventListener('pointerleave', (event) => {
				if (event.pointerType === 'mouse') {
					pause.hover = false;
					update();
				}
			});

			// Solo el foco de teclado: un clic también enfoca el botón y dejaría
			// la rotación pausada después de "Reanudar".
			root.addEventListener('focusin', (event) => {
				pause.focus = event.target.matches(':focus-visible');
				update();
			});
			root.addEventListener('focusout', (event) => {
				if (!root.contains(event.relatedTarget)) {
					pause.focus = false;
					update();
				}
			});

			if ('IntersectionObserver' in window) {
				new IntersectionObserver(
					(entries) => {
						pause.offscreen = !entries[entries.length - 1].isIntersecting;
						update();
					},
					// Umbral 0 con margen: en móvil la sección es más alta que la
					// pantalla y una proporción fija nunca se alcanzaría.
					{ rootMargin: '-15% 0px -15% 0px' }
				).observe(root);
			}

			reducedMotion.addEventListener('change', update);
		}

		update();
		root.classList.add('is-ready');
	};

	document.querySelectorAll('[data-benefits]').forEach(initBenefits);
})();

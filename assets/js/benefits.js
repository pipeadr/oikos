/**
 * Oikos — Beneficios con foto que cambia (template-parts/sections/benefits.php).
 *
 * - Clic, Enter o Espacio en un beneficio (es un <button>) lo activa: mueve
 *   .is-active y aria-current y hace el fundido a su foto (data-photo; si es
 *   la misma, no cambia nada). Elegir un beneficio detiene la rotación para
 *   siempre (ya eligió).
 * - Rotación automática (data-autoplay, 2+ beneficios, sin movimiento
 *   reducido): un temporizador pasa al siguiente cada data-interval segundos
 *   (al final vuelve al primero). Al pausar guarda el tiempo restante y al
 *   reanudar sigue desde ahí. La línea de progreso es una animación CSS de la
 *   misma duración que se congela con la misma clase (.is-paused).
 *   Pausan: mouse encima, foco de teclado dentro y sección fuera de pantalla
 *   (IntersectionObserver); al salir sigue. Elegir un beneficio quita
 *   .is-rotating (la línea desaparece).
 * - Sin botón de pausa (pedido del cliente): WCAG 2.2.2 queda cubierto por
 *   la pausa con hover/foco, la parada al elegir y prefers-reduced-motion.
 * - Sin aria-live: los cambios automáticos no se anuncian.
 */
(() => {
	'use strict';

	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	const initBenefits = (root) => {
		const items = Array.from(root.querySelectorAll('.oikos-benefits__item'));
		const buttons = items.map((item) => item.querySelector('.oikos-benefits__button'));
		const photos = Array.from(root.querySelectorAll('[data-photo-index]'));

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

		const canRotate = root.hasAttribute('data-autoplay');
		const interval = Math.min(20, Math.max(4, Number(root.dataset.interval) || 7)) * 1000;
		const pause = {
			hover: false,
			focus: false,
			offscreen: 'IntersectionObserver' in window,
			chosen: false,
		};

		let timer = 0;
		let startedAt = 0;
		let remaining = interval;

		const enabled = () => canRotate && !reducedMotion.matches;
		const paused = () => pause.hover || pause.focus || pause.offscreen;
		const running = () => enabled() && !pause.chosen && !paused();

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

			// Sin rotación (eligió o movimiento reducido), un arranque posterior
			// empieza de cero, igual que la línea (se reinicia con .is-rotating).
			if (!on || pause.chosen) {
				remaining = interval;
			}

			root.classList.toggle('is-rotating', on && !pause.chosen);
			root.classList.toggle('is-paused', paused());


			if (running()) {
				startedAt = performance.now();
				timer = window.setTimeout(next, remaining);
			}
		};

		buttons.forEach((button, i) => {
			button.addEventListener('click', () => {
				show(i);
				pause.chosen = true;
				update();
			});
		});

		if (canRotate) {
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

			// Solo el foco de teclado: un clic también enfoca el botón del
			// beneficio, y ese caso ya lo cubre la elección.
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

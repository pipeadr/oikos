/**
 * Oikos — Slider (componente template-parts/components/slider.php).
 *
 * El desplazamiento y el arrastre táctil los hace el navegador (CSS
 * scroll-snap). Este script solo:
 * - detecta la diapositiva activa (sin listener de scroll: se recalcula cuando
 *   el IntersectionObserver ve cambiar la visibilidad y en scrollend) y
 *   actualiza aria-current de los indicadores y el estado de las flechas;
 * - marca la diapositiva activa con .is-active y avisa con el evento
 *   'oikos-slider:change' (detail.index) en el contenedor. Las tarjetas de
 *   servicio lo usan para expandirse en pantallas táctiles;
 * - marca el contenedor con .is-ready (el CSS distingue "sin JS");
 * - lleva a una diapositiva al pulsar un indicador o una flecha;
 * - agrega teclado: ← → Inicio Fin en el carrusel y en los indicadores.
 * Sin autoplay.
 *
 * Diapositiva activa: la que tiene su borde inicial más cerca de la posición
 * de desplazamiento; al llegar al final, la última (con varias diapositivas
 * visibles, la última nunca queda alineada al inicio).
 */
(() => {
	'use strict';

	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	const initSlider = (root) => {
		const track = root.querySelector('.oikos-slider__track');
		const slides = Array.from(root.querySelectorAll('.oikos-slider__slide'));
		const dots = Array.from(root.querySelectorAll('[data-slider-dot]'));
		const prev = root.querySelector('[data-slider-prev]');
		const next = root.querySelector('[data-slider-next]');

		if (!track || slides.length < 2) {
			return;
		}

		let current = -1;

		// Posición de cada diapositiva relativa a la primera (no depende del scroll).
		const offsetOf = (index) => slides[index].offsetLeft - slides[0].offsetLeft;

		const render = (index) => {
			if (index === current) {
				return;
			}
			current = index;

			slides.forEach((slide, i) => slide.classList.toggle('is-active', i === current));

			dots.forEach((dot, i) => {
				if (i === current) {
					dot.setAttribute('aria-current', 'true');
				} else {
					dot.removeAttribute('aria-current');
				}
			});

			if (prev) {
				prev.disabled = current === 0;
			}
			if (next) {
				next.disabled = current === slides.length - 1;
			}

			root.dispatchEvent(new CustomEvent('oikos-slider:change', { detail: { index: current } }));
		};

		const activeFromScroll = () => {
			const position = Math.abs(track.scrollLeft); // abs: también en RTL
			const max = track.scrollWidth - track.clientWidth;

			if (max > 0 && position >= max - 2) {
				return slides.length - 1;
			}

			let best = 0;
			let distance = Infinity;
			slides.forEach((slide, i) => {
				const d = Math.abs(offsetOf(i) - position);
				if (d < distance) {
					distance = d;
					best = i;
				}
			});
			return best;
		};

		const update = () => render(activeFromScroll());

		const goTo = (index) => {
			const target = Math.max(0, Math.min(slides.length - 1, index));

			track.scrollTo({
				left: offsetOf(target),
				behavior: reducedMotion.matches ? 'auto' : 'smooth',
			});

			// Se fija ya para que el teclado encadene pasos sin esperar al observer.
			render(target);
		};

		// Cualquier cambio de visibilidad recalcula la activa (barato: lee posiciones).
		const observer = new IntersectionObserver(update, {
			root: track,
			threshold: [0, 0.25, 0.5, 0.75, 1],
		});
		slides.forEach((slide) => observer.observe(slide));

		// Posición final exacta tras el snap (donde exista scrollend).
		track.addEventListener('scrollend', update);

		// Destino de una tecla a partir de una posición (la activa o el indicador con foco).
		const keyTarget = (key, from) => {
			switch (key) {
				case 'ArrowLeft':
					return from - 1;
				case 'ArrowRight':
					return from + 1;
				case 'Home':
					return 0;
				case 'End':
					return slides.length - 1;
				default:
					return null;
			}
		};

		track.addEventListener('keydown', (event) => {
			// Solo con el foco en el carrusel, no en un enlace o botón de una diapositiva.
			if (event.target !== track) {
				return;
			}
			const target = keyTarget(event.key, current);
			if (null === target) {
				return;
			}
			event.preventDefault();
			goTo(target);
		});

		dots.forEach((dot) => {
			dot.addEventListener('click', () => goTo(Number(dot.dataset.sliderDot)));

			dot.addEventListener('keydown', (event) => {
				const target = keyTarget(event.key, Number(dot.dataset.sliderDot));
				if (null === target) {
					return;
				}
				event.preventDefault();
				goTo(target);
				dots[current].focus();
			});
		});

		if (prev) {
			prev.addEventListener('click', () => goTo(current - 1));
		}
		if (next) {
			next.addEventListener('click', () => goTo(current + 1));
		}

		update();
		root.classList.add('is-ready');
	};

	document.querySelectorAll('[data-oikos-slider]').forEach(initSlider);
})();

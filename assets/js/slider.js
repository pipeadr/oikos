/**
 * Oikos — Slider (componente template-parts/components/slider.php).
 *
 * El desplazamiento y el arrastre táctil los hace el navegador (CSS
 * scroll-snap). Este script solo:
 * - detecta la diapositiva activa (IntersectionObserver, sin listener de scroll)
 *   y actualiza aria-current de los indicadores y el estado de las flechas;
 * - lleva a una diapositiva al pulsar un indicador o una flecha;
 * - agrega teclado: ← → Inicio Fin en el carrusel y en los indicadores.
 * Sin autoplay.
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

		let current = 0;

		const render = () => {
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
		};

		const goTo = (index) => {
			const target = Math.max(0, Math.min(slides.length - 1, index));

			track.scrollTo({
				left: slides[target].offsetLeft - slides[0].offsetLeft,
				behavior: reducedMotion.matches ? 'auto' : 'smooth',
			});

			// Se fija ya para que el teclado encadene pasos sin esperar al observer.
			current = target;
			render();
		};

		// Diapositiva activa: la que ocupa la mayor parte del carrusel.
		const observer = new IntersectionObserver(
			(entries) => {
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						current = slides.indexOf(entry.target);
						render();
					}
				});
			},
			{ root: track, threshold: 0.6 }
		);
		slides.forEach((slide) => observer.observe(slide));

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

		render();
	};

	document.querySelectorAll('[data-oikos-slider]').forEach(initSlider);
})();

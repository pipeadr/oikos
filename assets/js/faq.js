/**
 * Oikos — Preguntas frecuentes (template-parts/sections/faq.php).
 *
 * Mejora progresiva de <details>/<summary>: anima el alto al abrir y cerrar
 * con la Web Animations API (todos los navegadores). Sin este archivo, o con
 * prefers-reduced-motion, el <details> abre y cierra de forma nativa.
 *
 * Enter y Espacio sobre el <summary> generan un click: un solo manejador
 * sirve para mouse y teclado. Varios ítems pueden estar abiertos a la vez.
 */
( () => {
	'use strict';

	const DURATION = 250;
	const EASING = 'ease';
	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	/**
	 * Anima el alto del <details> entre dos valores.
	 *
	 * @param {HTMLDetailsElement} item    Ítem.
	 * @param {number}             from    Alto inicial (px).
	 * @param {number}             to      Alto final (px).
	 * @param {boolean}            opening true al abrir.
	 */
	const animate = ( item, from, to, opening ) => {
		if ( item.oikosAnimation ) {
			item.oikosAnimation.cancel();
		}

		item.classList.toggle( 'is-closing', ! opening );
		item.style.overflow = 'hidden';

		const animation = item.animate(
			{ height: [ `${ from }px`, `${ to }px` ] },
			{ duration: DURATION, easing: EASING }
		);

		item.oikosAnimation = animation;

		animation.onfinish = () => {
			item.oikosAnimation = null;
			item.style.overflow = '';
			item.classList.remove( 'is-closing' );

			if ( ! opening ) {
				item.open = false;
			}
		};

		animation.oncancel = () => {
			item.oikosAnimation = null;
		};
	};

	const onClick = ( event ) => {
		if ( reducedMotion.matches || typeof Element.prototype.animate !== 'function' ) {
			return; // Comportamiento nativo.
		}

		const summary = event.currentTarget;
		const item = summary.parentElement;

		event.preventDefault();

		// Alto actual (incluye una animación a medias: se invierte desde ahí).
		const current = item.getBoundingClientRect().height;
		const closed = summary.getBoundingClientRect().height;
		const closing = item.open && ! item.classList.contains( 'is-closing' );

		if ( closing ) {
			animate( item, current, closed, false );
			return;
		}

		item.classList.remove( 'is-closing' );
		item.open = true;

		if ( item.oikosAnimation ) {
			item.oikosAnimation.cancel();
		}

		animate( item, current, item.getBoundingClientRect().height, true );
	};

	document.querySelectorAll( '.oikos-faq__item > .oikos-faq__question' ).forEach( ( summary ) => {
		summary.addEventListener( 'click', onClick );
	} );
} )();

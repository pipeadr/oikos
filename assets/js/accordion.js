/**
 * Oikos — Acordeón (template-parts/components/accordion.php).
 *
 * Solo alterna aria-expanded en el botón de cada pregunta; el CSS anima el
 * panel a partir de ese atributo. Varios ítems pueden estar abiertos a la vez.
 * Teclado: Tab entre preguntas, Enter/Espacio abren y cierran (nativo del <button>).
 */
( () => {
	'use strict';

	document.querySelectorAll( '[data-oikos-accordion]' ).forEach( ( accordion ) => {
		accordion.addEventListener( 'click', ( event ) => {
			const trigger = event.target.closest( '.oikos-accordion__trigger' );

			if ( ! trigger || ! accordion.contains( trigger ) ) {
				return;
			}

			const expanded = trigger.getAttribute( 'aria-expanded' ) === 'true';
			trigger.setAttribute( 'aria-expanded', expanded ? 'false' : 'true' );
		} );
	} );
} )();

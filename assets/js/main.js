/**
 * Oikos — main.js
 *
 * JS global del sitio (vanilla ES6+, sin jQuery). Se carga con defer.
 */

/**
 * Newsletter: mientras la plataforma no esté conectada (formulario sin
 * action), se valida el correo, no se envía y se muestra el mensaje
 * pendiente configurado en "Oikos — Global".
 */
( () => {
	document.querySelectorAll( '[data-oikos-newsletter-pending]' ).forEach( ( form ) => {
		const status = form.querySelector( '.oikos-newsletter__status' );

		form.addEventListener( 'submit', ( event ) => {
			event.preventDefault();

			if ( status ) {
				status.textContent = form.dataset.oikosNewsletterPending || '';
			}
		} );
	} );
} )();

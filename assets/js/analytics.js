/**
 * Oikos — analytics.js
 *
 * Empuja al dataLayer los eventos que GTM no detecta por sí solo.
 * Solo se carga si hay ID de GTM y Contact Form 7 está activo.
 * No envía datos personales (nunca los valores de los campos).
 */
( () => {
	'use strict';

	window.dataLayer = window.dataLayer || [];

	// Envío exitoso de Contact Form 7 (el evento burbujea desde el <form>).
	document.addEventListener( 'wpcf7mailsent', ( event ) => {
		const form = event.target;
		const detail = event.detail || {};

		window.dataLayer.push( {
			event: 'cf7_submit_success',
			form_id: detail.contactFormId,
			form_name: ( form && form.dataset && form.dataset.oikosFormName ) || '',
		} );
	} );
} )();

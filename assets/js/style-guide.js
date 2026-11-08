/**
 * Oikos — style-guide.js
 *
 * Muestra el valor calculado de cada token y estilo de texto en el ancho
 * actual, y avisa si la página desborda horizontalmente.
 */
( () => {
	'use strict';

	const root = document.documentElement;
	const px = ( value ) => `${ Math.round( parseFloat( value ) * 100 ) / 100 }px`;

	// Convierte un valor CSS (clamp, rem…) en px midiendo un elemento temporal.
	const measure = ( token ) => {
		const probe = document.createElement( 'div' );
		probe.style.cssText = `position:absolute;visibility:hidden;width:var(${ token })`;
		document.body.appendChild( probe );
		const width = getComputedStyle( probe ).width;
		probe.remove();
		return px( width );
	};

	const update = () => {
		const styles = getComputedStyle( root );

		document.querySelectorAll( '[data-sg-token]' ).forEach( ( el ) => {
			const token = el.dataset.sgToken;
			el.textContent = el.hasAttribute( 'data-sg-px' )
				? measure( token )
				: styles.getPropertyValue( token ).trim();
		} );

		document.querySelectorAll( '[data-sg-type]' ).forEach( ( sample ) => {
			const s = getComputedStyle( sample );
			const target = document.querySelector( `[data-sg-type-for="${ sample.dataset.sgType }"]` );
			if ( target ) {
				const family = s.fontFamily.split( ',' )[ 0 ].replace( /"/g, '' );
				target.textContent = `${ family } ${ s.fontWeight } · ${ px( s.fontSize ) } / ${ px( s.lineHeight ) } · tracking ${ s.letterSpacing }`;
			}
		} );

		const button = document.querySelector( '.oikos-sg .oikos-btn' );
		const buttonInfo = document.querySelector( '[data-sg-button-height]' );
		if ( button && buttonInfo ) {
			buttonInfo.textContent = `Alto calculado del botón: ${ px( button.getBoundingClientRect().height ) }`;
		}

		const container = document.querySelector( '[data-sg-container]' );
		if ( container ) {
			const box = container.closest( '.oikos-container' ).getBoundingClientRect();
			container.textContent = `Ancho ${ px( box.width ) } · margen lateral ${ px( box.left ) }`;
		}

		const viewport = document.querySelector( '[data-sg-viewport]' );
		if ( viewport ) {
			const overflow = root.scrollWidth > root.clientWidth;
			viewport.textContent = `Ventana: ${ root.clientWidth }px · ${ overflow ? `⚠ desborde horizontal (${ root.scrollWidth }px)` : 'sin desborde horizontal' }`;
			viewport.classList.toggle( 'is-overflowing', overflow );
		}
	};

	let frame;
	window.addEventListener( 'resize', () => {
		cancelAnimationFrame( frame );
		frame = requestAnimationFrame( update );
	} );

	if ( document.fonts && document.fonts.ready ) {
		document.fonts.ready.then( update );
	}
	update();
} )();

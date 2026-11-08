/**
 * Oikos — header.js
 *
 * 1. Submenús (disclosure): clic/Enter/Espacio, hover con puntero fino en
 *    desktop, Esc cierra y devuelve el foco, clic fuera cierra.
 * 2. Menú móvil (< 1024): aria-expanded, Esc, clic fuera (backdrop), foco
 *    atrapado en la barra, scroll del fondo bloqueado, cierre al pasar a
 *    desktop.
 * 3. Sticky: se esconde al bajar y reaparece al subir. La animación la
 *    desactiva el CSS con prefers-reduced-motion.
 */
( () => {
	const header = document.querySelector( '[data-oikos-header]' );

	if ( ! header ) {
		return;
	}

	const root = document.documentElement;
	const panel = header.querySelector( '#oikos-header-panel' );
	const menuToggle = header.querySelector( '.oikos-header__toggle' );
	const backdrop = document.querySelector( '[data-oikos-header-backdrop]' );
	const bar = header.querySelector( '.oikos-header__bar' );
	const subToggles = Array.from( header.querySelectorAll( '.oikos-nav__toggle' ) );

	const desktopMq = window.matchMedia( '(min-width: 64rem)' );
	const finePointerMq = window.matchMedia( '(hover: hover) and (pointer: fine)' );

	const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])';

	header.classList.add( 'is-enhanced' );

	/* ----------------------------------------------------------------------
	   1. Submenús
	   ---------------------------------------------------------------------- */

	const setSubmenu = ( toggle, open ) => {
		toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
	};

	const closeSubmenus = ( except = null ) => {
		subToggles.forEach( ( toggle ) => {
			if ( toggle !== except ) {
				setSubmenu( toggle, false );
			}
		} );
	};

	const openSubmenu = () => subToggles.find( ( t ) => t.getAttribute( 'aria-expanded' ) === 'true' );

	subToggles.forEach( ( toggle ) => {
		const item = toggle.closest( '.oikos-nav__item' );
		let leaveTimer = 0;

		toggle.addEventListener( 'click', () => {
			const willOpen = toggle.getAttribute( 'aria-expanded' ) !== 'true';
			closeSubmenus( toggle );
			setSubmenu( toggle, willOpen );
		} );

		// Hover solo en desktop con mouse/trackpad.
		item.addEventListener( 'mouseenter', () => {
			if ( ! desktopMq.matches || ! finePointerMq.matches ) {
				return;
			}
			window.clearTimeout( leaveTimer );
			closeSubmenus( toggle );
			setSubmenu( toggle, true );
		} );

		item.addEventListener( 'mouseleave', () => {
			if ( ! desktopMq.matches || ! finePointerMq.matches ) {
				return;
			}
			leaveTimer = window.setTimeout( () => setSubmenu( toggle, false ), 200 );
		} );

		// En desktop, el submenú se cierra cuando el foco sale del ítem.
		item.addEventListener( 'focusout', ( event ) => {
			if ( desktopMq.matches && ! item.contains( event.relatedTarget ) ) {
				setSubmenu( toggle, false );
			}
		} );
	} );

	/* ----------------------------------------------------------------------
	   2. Menú móvil
	   ---------------------------------------------------------------------- */

	const isMenuOpen = () => header.classList.contains( 'is-open' );

	const setMenuLabel = ( open ) => {
		if ( ! menuToggle ) {
			return;
		}
		const label = open ? menuToggle.dataset.labelClose : menuToggle.dataset.labelOpen;
		const text = menuToggle.querySelector( '.screen-reader-text' );
		if ( label && text ) {
			text.textContent = label;
		}
	};

	const openMenu = () => {
		header.classList.add( 'is-open' );
		header.classList.remove( 'is-hidden' );
		menuToggle.setAttribute( 'aria-expanded', 'true' );
		setMenuLabel( true );
		root.classList.add( 'oikos-menu-open' );
		if ( backdrop ) {
			backdrop.hidden = false;
		}
		if ( panel ) {
			panel.scrollTop = 0;
		}
	};

	const closeMenu = ( { returnFocus = false } = {} ) => {
		if ( ! isMenuOpen() ) {
			return;
		}
		header.classList.remove( 'is-open' );
		menuToggle.setAttribute( 'aria-expanded', 'false' );
		setMenuLabel( false );
		root.classList.remove( 'oikos-menu-open' );
		if ( backdrop ) {
			backdrop.hidden = true;
		}
		closeSubmenus();
		if ( returnFocus ) {
			menuToggle.focus();
		}
	};

	if ( menuToggle ) {
		menuToggle.addEventListener( 'click', () => {
			if ( isMenuOpen() ) {
				closeMenu();
			} else {
				openMenu();
			}
		} );
	}

	if ( backdrop ) {
		backdrop.addEventListener( 'click', () => closeMenu( { returnFocus: true } ) );
	}

	// Foco atrapado dentro de la barra mientras el panel está abierto.
	const visibleFocusables = () =>
		Array.from( bar.querySelectorAll( FOCUSABLE ) ).filter(
			( el ) => el.getClientRects().length > 0 && ! el.closest( '[hidden]' )
		);

	document.addEventListener( 'keydown', ( event ) => {
		if ( event.key === 'Escape' ) {
			const sub = openSubmenu();
			// Primero se cierra el submenú (si el foco está en él); luego el panel.
			if ( sub && sub.closest( '.oikos-nav__item' ).contains( document.activeElement ) ) {
				setSubmenu( sub, false );
				sub.focus();
				return;
			}
			if ( isMenuOpen() ) {
				closeMenu( { returnFocus: true } );
				return;
			}
			if ( sub ) {
				setSubmenu( sub, false );
			}
			return;
		}

		if ( event.key !== 'Tab' || ! isMenuOpen() ) {
			return;
		}

		const items = visibleFocusables();
		if ( ! items.length ) {
			return;
		}

		const first = items[ 0 ];
		const last = items[ items.length - 1 ];

		if ( ! bar.contains( document.activeElement ) ) {
			event.preventDefault();
			first.focus();
		} else if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	} );

	// Clic fuera del header: cierra submenús (y el panel, si el clic no fue en el backdrop).
	document.addEventListener( 'click', ( event ) => {
		if ( header.contains( event.target ) ) {
			return;
		}
		closeSubmenus();
	} );

	// Al pasar a desktop, el panel móvil se cierra solo.
	desktopMq.addEventListener( 'change', ( event ) => {
		if ( event.matches ) {
			closeMenu();
		}
		closeSubmenus();
	} );

	/* ----------------------------------------------------------------------
	   3. Sticky: esconder al bajar, mostrar al subir
	   ---------------------------------------------------------------------- */

	const DELTA = 8;
	let lastY = window.scrollY;
	let ticking = false;

	const mustStayVisible = () =>
		isMenuOpen() || Boolean( openSubmenu() ) || header.contains( document.activeElement );

	const updateSticky = () => {
		const y = Math.max( window.scrollY, 0 );
		const threshold = header.offsetHeight + header.offsetTop;

		if ( mustStayVisible() || y <= threshold ) {
			header.classList.remove( 'is-hidden' );
		} else if ( y > lastY + DELTA ) {
			header.classList.add( 'is-hidden' );
		} else if ( y < lastY - DELTA ) {
			header.classList.remove( 'is-hidden' );
		}

		if ( Math.abs( y - lastY ) > DELTA || y <= threshold ) {
			lastY = y;
		}
		ticking = false;
	};

	window.addEventListener(
		'scroll',
		() => {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( updateSticky );
			}
		},
		{ passive: true }
	);

	// Si el foco de teclado entra al header escondido, se muestra.
	header.addEventListener( 'focusin', () => header.classList.remove( 'is-hidden' ) );
} )();

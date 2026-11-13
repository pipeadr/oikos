<?php
/**
 * Navegación: walker del menú principal y CTA global "Agenda tu cita".
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Datos del CTA "Agenda tu cita" desde "Oikos — Global".
 *
 * @return array{text:string,url:string,target:string,footer_title:string,footer_text:string}
 *               url = '' si no hay enlace configurado.
 */
function oikos_cta() {
	static $cta = null;

	if ( null !== $cta ) {
		return $cta;
	}

	$link = oikos_option( 'oikos_global_cta_enlace' );
	$link = is_array( $link ) ? $link : array();

	$cta = array(
		'text'         => (string) oikos_option( 'oikos_global_cta_texto', __( 'Agenda tu cita', 'oikos' ) ),
		'url'          => isset( $link['url'] ) ? (string) $link['url'] : '',
		'target'       => ! empty( $link['target'] ) ? (string) $link['target'] : '',
		'footer_title' => (string) oikos_option( 'oikos_global_cta_footer_titulo', __( '¿Todo listo para agendar tu cita?', 'oikos' ) ),
		'footer_text'  => (string) oikos_option( 'oikos_global_cta_footer_boton', __( 'Agenda tu cita ahora', 'oikos' ) ),
	);

	return $cta;
}

/**
 * Imprime el botón del CTA "Agenda tu cita".
 *
 * @param array $args {
 *     @type string $text     Texto (por defecto el del header).
 *     @type string $class    Clases del botón.
 *     @type string $location Valor de data-track-location.
 * }
 * @return void
 */
function oikos_the_cta_button( $args = array() ) {
	$cta = oikos_cta();

	if ( '' === $cta['url'] ) {
		return;
	}

	$args = wp_parse_args(
		$args,
		array(
			'text'     => $cta['text'],
			'class'    => 'oikos-btn oikos-btn--primary',
			'location' => '',
		)
	);

	printf(
		'<a class="%1$s" href="%2$s"%3$s data-track="agenda-cita" data-track-location="%4$s">%5$s</a>',
		esc_attr( $args['class'] ),
		esc_url( $cta['url'] ),
		'_blank' === $cta['target'] ? ' target="_blank" rel="noopener"' : '',
		esc_attr( $args['location'] ),
		esc_html( $args['text'] )
	);
}

/**
 * Normaliza un campo de enlace ACF y, si no tiene URL, usa el CTA global.
 *
 * - Enlace con URL: se devuelve tal cual; si le falta el texto, toma el del CTA.
 * - Enlace vacío: se devuelve el CTA global "Agenda tu cita".
 *
 * @param mixed $link Valor de un campo link de ACF (array url/title/target).
 * @return array{url:string,title:string,target:string} url = '' si tampoco hay CTA.
 */
function oikos_link_or_cta( $link ) {
	$link = is_array( $link ) ? $link : array();
	$cta  = oikos_cta();

	if ( empty( $link['url'] ) ) {
		return array(
			'url'    => $cta['url'],
			'title'  => $cta['text'],
			'target' => $cta['target'],
		);
	}

	return array(
		'url'    => (string) $link['url'],
		'title'  => ! empty( $link['title'] ) ? (string) $link['title'] : $cta['text'],
		'target' => ! empty( $link['target'] ) ? (string) $link['target'] : '',
	);
}

/**
 * Walker del menú principal.
 *
 * - Cada ítem: <li class="oikos-nav__item"> con <a class="oikos-nav__link">.
 * - Ítems con hijos (nivel 0): el padre es un <button> que abre el submenú
 *   (disclosure accesible: aria-expanded + aria-controls). El enlace del
 *   ítem padre no se usa; el primer hijo debe ser la página general.
 * - La página actual lleva aria-current="page"; el ancestro de la página
 *   actual lleva la clase is-current (borde de "ítem activo").
 */
class Oikos_Primary_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * ID del último padre abierto, para enlazar botón y submenú.
	 *
	 * @var int
	 */
	private $parent_id = 0;

	/**
	 * Abre un submenú.
	 *
	 * @param string   $output Salida.
	 * @param int      $depth  Profundidad.
	 * @param stdClass $args   Argumentos de wp_nav_menu().
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= sprintf(
			'<ul class="oikos-nav__submenu" id="%s">',
			esc_attr( 'oikos-submenu-' . $this->parent_id )
		);
	}

	/**
	 * Cierra un submenú.
	 *
	 * @param string   $output Salida.
	 * @param int      $depth  Profundidad.
	 * @param stdClass $args   Argumentos de wp_nav_menu().
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	/**
	 * Abre un ítem.
	 *
	 * @param string   $output            Salida.
	 * @param WP_Post  $data_object       Ítem de menú.
	 * @param int      $depth             Profundidad.
	 * @param stdClass $args              Argumentos de wp_nav_menu().
	 * @param int      $current_object_id ID del objeto actual.
	 * @return void
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
		$item       = $data_object;
		$wp_classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$is_parent  = 0 === $depth && in_array( 'menu-item-has-children', $wp_classes, true );
		$is_current = in_array( 'current-menu-item', $wp_classes, true );
		$is_active  = $is_current || (bool) array_intersect(
			array( 'current-menu-ancestor', 'current-menu-parent', 'current_page_parent', 'current_page_ancestor' ),
			$wp_classes
		);

		$classes = array( 'oikos-nav__item' );
		if ( $depth > 0 ) {
			$classes[] = 'oikos-nav__item--sub';
		}
		if ( $is_parent ) {
			$classes[] = 'oikos-nav__item--parent';
		}
		if ( $is_active ) {
			$classes[] = 'is-current';
		}
		// Clases personalizadas que el editor agregue en Apariencia → Menús.
		foreach ( $wp_classes as $custom ) {
			if ( '' !== $custom && 0 !== strpos( $custom, 'menu-item' ) && 0 !== strpos( $custom, 'current' ) && 0 !== strpos( $custom, 'page_item' ) && 'page-item' !== substr( $custom, 0, 9 ) ) {
				$classes[] = $custom;
			}
		}

		$title = apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );

		$output .= '<li class="' . esc_attr( implode( ' ', array_unique( $classes ) ) ) . '">';

		if ( $is_parent ) {
			$this->parent_id = (int) $item->ID;

			$output .= sprintf(
				'<button type="button" class="oikos-nav__link oikos-nav__toggle" aria-expanded="false" aria-controls="%1$s"><span>%2$s</span>%3$s</button>',
				esc_attr( 'oikos-submenu-' . $item->ID ),
				esc_html( $title ),
				wp_kses( oikos_icon( 'chevron-down', 'oikos-nav__chevron' ), oikos_icon_kses() )
			);
			return;
		}

		$atts = array(
			'class'        => 'oikos-nav__link',
			'href'         => ! empty( $item->url ) ? $item->url : '',
			'target'       => ! empty( $item->target ) ? $item->target : '',
			'rel'          => '_blank' === $item->target ? trim( $item->xfn . ' noopener' ) : $item->xfn,
			'title'        => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'aria-current' => $is_current ? 'page' : '',
		);
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = 'href' === $attr ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$arrow = $depth > 0 ? wp_kses( oikos_icon( 'arrow-right', 'oikos-nav__arrow' ), oikos_icon_kses() ) : '';

		$output .= '<a' . $attributes . '><span>' . esc_html( $title ) . '</span>' . $arrow . '</a>';
	}

	/**
	 * Cierra un ítem.
	 *
	 * @param string   $output      Salida.
	 * @param WP_Post  $data_object Ítem de menú.
	 * @param int      $depth       Profundidad.
	 * @param stdClass $args        Argumentos de wp_nav_menu().
	 * @return void
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}

/**
 * Imprime un menú simple del footer (un nivel) con su título.
 *
 * El título de la columna es el nombre del menú asignado a la ubicación
 * (se edita en Apariencia → Menús).
 *
 * @param string $location Ubicación del menú.
 * @return void
 */
function oikos_footer_menu( $location ) {
	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return;
	}

	$menu = wp_get_nav_menu_object( $locations[ $location ] );

	if ( ! $menu ) {
		return;
	}

	$heading_id = 'oikos-footer-menu-' . sanitize_html_class( $location );
	?>
	<nav class="oikos-footer__col" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<h2 class="oikos-footer__col-title" id="<?php echo esc_attr( $heading_id ); ?>"><?php echo esc_html( $menu->name ); ?></h2>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => 'oikos-footer__links',
				'fallback_cb'    => false,
				'depth'          => 1,
			)
		);
		?>
	</nav>
	<?php
}

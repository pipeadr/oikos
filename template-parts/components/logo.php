<?php
/**
 * Componente: logo enlazado al inicio.
 *
 * Args:
 * - variant: 'default'    custom logo del Personalizador (header).
 *            'inverse'    campo "Logo para fondos oscuros".
 *            'newsletter' campo "Logo del newsletter" (árbol + "OIKOS
 *                         Psicología"); si está vacío, "Logo para fondos
 *                         oscuros". Usa también las clases de 'inverse'.
 * - class:   clase extra para el enlace.
 *
 * SVG o PNG. El tamaño lo controla el CSS (.oikos-logo img), así que un SVG
 * sin width/height se ve igual. Sin imagen: nombre del sitio en texto.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_variant = isset( $args['variant'] ) && in_array( $args['variant'], array( 'inverse', 'newsletter' ), true ) ? $args['variant'] : 'default';
$oikos_class   = isset( $args['class'] ) ? (string) $args['class'] : '';

if ( 'newsletter' === $oikos_variant ) {
	$oikos_logo_id = (int) oikos_option( 'oikos_global_newsletter_logo', 0 );
	$oikos_img     = oikos_logo_img( $oikos_logo_id, array( 'loading' => 'lazy' ) );

	if ( ! $oikos_img ) {
		$oikos_img = oikos_logo_img( (int) oikos_option( 'oikos_global_logo_oscuro', 0 ), array( 'loading' => 'lazy' ) );
	}
} elseif ( 'inverse' === $oikos_variant ) {
	$oikos_logo_id = (int) oikos_option( 'oikos_global_logo_oscuro', 0 );
	$oikos_img     = oikos_logo_img( $oikos_logo_id, array( 'loading' => 'lazy' ) );
} else {
	$oikos_logo_id = (int) get_theme_mod( 'custom_logo', 0 );
	$oikos_img     = oikos_logo_img( $oikos_logo_id ); // Sin fetchpriority: la imagen LCP es la del hero.
}

$oikos_classes = trim( 'oikos-logo oikos-logo--' . ( 'newsletter' === $oikos_variant ? 'inverse oikos-logo--newsletter' : $oikos_variant ) . ( $oikos_img ? ' oikos-logo--image' : ' oikos-logo--text' ) . ' ' . $oikos_class );
?>
<a class="<?php echo esc_attr( $oikos_classes ); ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"<?php echo ( 'default' === $oikos_variant && is_front_page() ) ? ' aria-current="page"' : ''; ?>>
	<?php if ( $oikos_img ) : ?>
		<?php echo $oikos_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escapado en oikos_logo_img(). ?>
	<?php else : ?>
		<?php bloginfo( 'name' ); ?>
	<?php endif; ?>
</a>

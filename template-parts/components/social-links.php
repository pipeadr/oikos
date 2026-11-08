<?php
/**
 * Componente: íconos de redes sociales (repetidor de "Oikos — Global").
 *
 * Args:
 * - location: valor de data-track-location (ej. 'footer').
 * - class:    clase extra para la lista.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_networks = oikos_option( 'oikos_global_redes', array() );

if ( empty( $oikos_networks ) || ! is_array( $oikos_networks ) ) {
	return;
}

$oikos_location = isset( $args['location'] ) ? (string) $args['location'] : '';
$oikos_class    = isset( $args['class'] ) ? (string) $args['class'] : '';

$oikos_labels = array(
	'whatsapp'  => __( 'WhatsApp', 'oikos' ),
	'instagram' => __( 'Instagram', 'oikos' ),
	'facebook'  => __( 'Facebook', 'oikos' ),
	'x'         => __( 'X (Twitter)', 'oikos' ),
	'tiktok'    => __( 'TikTok', 'oikos' ),
	'youtube'   => __( 'YouTube', 'oikos' ),
	'linkedin'  => __( 'LinkedIn', 'oikos' ),
);

$oikos_items = array();

foreach ( $oikos_networks as $oikos_row ) {
	$oikos_network = isset( $oikos_row['red'] ) ? sanitize_key( $oikos_row['red'] ) : '';
	$oikos_url     = isset( $oikos_row['url'] ) ? trim( (string) $oikos_row['url'] ) : '';

	if ( ! isset( $oikos_labels[ $oikos_network ] ) ) {
		continue;
	}

	if ( '' === $oikos_url && 'whatsapp' === $oikos_network ) {
		$oikos_url = oikos_whatsapp_url();
	}

	if ( '' === $oikos_url ) {
		continue;
	}

	$oikos_items[] = array(
		'network' => $oikos_network,
		'url'     => $oikos_url,
	);
}

if ( ! $oikos_items ) {
	return;
}
?>
<ul class="<?php echo esc_attr( trim( 'oikos-social ' . $oikos_class ) ); ?>">
	<?php foreach ( $oikos_items as $oikos_item ) : ?>
		<li class="oikos-social__item">
			<a
				class="oikos-social__link"
				href="<?php echo esc_url( $oikos_item['url'] ); ?>"
				target="_blank"
				rel="noopener"
				data-track="<?php echo esc_attr( 'whatsapp' === $oikos_item['network'] ? 'whatsapp' : 'redes' ); ?>"
				data-track-location="<?php echo esc_attr( $oikos_location ); ?>"
			>
				<?php oikos_the_icon( $oikos_item['network'], 'oikos-social__icon' ); ?>
				<span class="screen-reader-text">
					<?php
					/* translators: %s: nombre de la red social. */
					printf( esc_html__( '%s (se abre en una pestaña nueva)', 'oikos' ), esc_html( $oikos_labels[ $oikos_item['network'] ] ) );
					?>
				</span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>

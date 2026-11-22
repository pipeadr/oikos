<?php
/**
 * Componente: tarjeta de un miembro del equipo.
 *
 * Foto vertical (imagen destacada del CPT equipo) que cubre la tarjeta y,
 * abajo sobre ella, un recuadro café translúcido con el nombre y la bio corta.
 * Todo sale del CPT: la página solo elige qué miembros mostrar.
 *
 * Args:
 * - member_id     (int)    ID del CPT equipo (obligatorio).
 * - heading_level (int)    Nivel del nombre (3 por defecto).
 * - sizes         (string) Atributo sizes de la foto.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'member_id'     => 0,
		'heading_level' => 3,
		'sizes'         => '(min-width: 72rem) 538px, (min-width: 48rem) calc(50vw - 5rem), calc(100vw - 2.5rem)',
	)
);

$oikos_member_id = absint( $oikos_args['member_id'] );

if ( ! $oikos_member_id || 'equipo' !== get_post_type( $oikos_member_id ) ) {
	return;
}

$oikos_name     = get_the_title( $oikos_member_id );
$oikos_bio      = trim( (string) oikos_field( 'oikos_equipo_bio_corta', $oikos_member_id ) );
$oikos_image_id = (int) get_post_thumbnail_id( $oikos_member_id );
$oikos_level    = min( 6, max( 2, (int) $oikos_args['heading_level'] ) );

$oikos_classes = 'oikos-team-card' . ( $oikos_image_id ? '' : ' oikos-team-card--no-photo' );
?>
<article class="<?php echo esc_attr( $oikos_classes ); ?>">
	<?php if ( $oikos_image_id ) : ?>
		<div class="oikos-team-card__media">
			<?php
			echo wp_get_attachment_image(
				$oikos_image_id,
				'oikos-portrait',
				false,
				array(
					'class'    => 'oikos-team-card__img',
					'alt'      => oikos_team_photo_alt( $oikos_member_id, $oikos_image_id ),
					'loading'  => 'lazy',
					'decoding' => 'async',
					'sizes'    => $oikos_args['sizes'],
				)
			);
			?>
		</div>
	<?php endif; ?>

	<div class="oikos-team-card__panel">
		<?php
		printf(
			'<h%1$d class="oikos-team-card__name oikos-text-card-hero">%2$s</h%1$d>',
			(int) $oikos_level,
			esc_html( $oikos_name )
		);
		?>

		<?php if ( '' !== $oikos_bio ) : ?>
			<p class="oikos-team-card__bio oikos-text-body"><?php echo esc_html( $oikos_bio ); ?></p>
		<?php endif; ?>
	</div>
</article>

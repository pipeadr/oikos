<?php
/**
 * Sección: tarjetas de equipo ("Los profesionales que te acompañan en este
 * proceso"). Reutilizable en Servicios, Nosotros y las páginas de terapia.
 *
 * No lee ACF: la plantilla le pasa los datos por $args. Los datos de cada
 * miembro salen del CPT equipo (template-parts/components/team-card.php).
 *
 * Args:
 * - id             (string) id del <section> (ancla).
 * - title          (string) Título (obligatorio). Los saltos de línea se
 *                           respetan solo desde 768 px (corte del Figma).
 * - heading_level  (int)    2–5. El nombre de cada miembro va un nivel abajo.
 * - members        (int[])  IDs del CPT equipo, en orden (obligatorio).
 * - link           (array)  url, title, target. Opcional: vacío → sin botón.
 * - track_location (string) data-track-location del botón.
 *
 * Al usarla en otra plantilla, agregarla a la condición de oikos-team en
 * oikos_template_assets() y a oikos_page_team_members() (schema).
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'id'             => 'equipo',
		'title'          => '',
		'heading_level'  => 2,
		'members'        => array(),
		'link'           => array(),
		'track_location' => '',
	)
);

$oikos_title   = trim( (string) $oikos_args['title'] );
$oikos_members = array_filter( array_map( 'absint', (array) $oikos_args['members'] ) );

if ( '' === $oikos_title || ! $oikos_members ) {
	return;
}

$oikos_level    = min( 5, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_link     = is_array( $oikos_args['link'] ) ? $oikos_args['link'] : array();
$oikos_title_id = wp_unique_id( 'oikos-team-' );

// Cada línea escapada; los saltos se vuelven <br> que el CSS oculta en móvil.
$oikos_title_html = oikos_title_lines_html( $oikos_title, 'oikos-team__break' );
?>
<section id="<?php echo esc_attr( $oikos_args['id'] ); ?>" class="oikos-section oikos-team" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>">
	<div class="oikos-container oikos-team__inner">
		<?php
		printf(
			'<h%1$d id="%2$s" class="oikos-team__heading oikos-text-heading">%3$s</h%1$d>',
			(int) $oikos_level,
			esc_attr( $oikos_title_id ),
			$oikos_title_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- líneas escapadas con esc_html().
		);
		?>

		<ul class="oikos-team__list" role="list">
			<?php foreach ( $oikos_members as $oikos_member_id ) : ?>
				<li class="oikos-team__item">
					<?php
					get_template_part(
						'template-parts/components/team-card',
						null,
						array(
							'member_id'     => $oikos_member_id,
							'heading_level' => $oikos_level + 1,
						)
					);
					?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( ! empty( $oikos_link['url'] ) && ! empty( $oikos_link['title'] ) ) : ?>
			<div class="oikos-team__action">
				<a
					class="oikos-btn oikos-btn--primary oikos-btn--block-mobile"
					href="<?php echo esc_url( $oikos_link['url'] ); ?>"
					<?php echo ( isset( $oikos_link['target'] ) && '_blank' === $oikos_link['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
					data-track="conoce-nosotros"
					data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
				><?php echo esc_html( $oikos_link['title'] ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>

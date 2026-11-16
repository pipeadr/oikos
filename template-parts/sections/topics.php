<?php
/**
 * Sección: grilla de temas con tarjetas que se abren
 * ("Todo lo que puedes trabajar en un proceso terapéutico").
 *
 * Intro (título + texto) y una grilla de tarjetas arena con el título abajo.
 * Al presionar una, se abre a doble ancho flotando sobre la vecina y muestra
 * la descripción (y un enlace opcional). La lógica la maneja
 * assets/js/topics.js; la dirección de apertura la decide el CSS según la
 * columna real de la tarjeta.
 *
 * No lee ACF: la plantilla le pasa los datos por $args.
 *
 * Args:
 * - id             (string) Prefijo único para los id del HTML.
 * - title          (string) Título de la sección (estilo Heading). Vacío → no se imprime.
 * - text           (string) Texto de apoyo (Body, text/secondary). Opcional.
 * - heading_level  (int)    Nivel del título (2 por defecto); los temas van un nivel debajo.
 * - items          (array)  Cada uno: title, text, link (arreglo de link de ACF: url, title, target).
 * - track_location (string) data-track-location de los enlaces "Leer más".
 *
 * Sin JS: los botones llegan con aria-expanded="true" y todas las
 * descripciones se ven (la grilla acomoda tarjetas de alto libre). Un tema
 * sin descripción ni enlace no lleva botón: es solo un título.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_args = wp_parse_args(
	isset( $args ) && is_array( $args ) ? $args : array(),
	array(
		'id'             => 'temas',
		'title'          => '',
		'text'           => '',
		'heading_level'  => 2,
		'items'          => array(),
		'track_location' => 'temas',
	)
);

// Temas válidos (con título) y normalizados.
$oikos_items = array();
foreach ( (array) $oikos_args['items'] as $oikos_item ) {
	if ( ! is_array( $oikos_item ) || '' === trim( (string) ( $oikos_item['title'] ?? '' ) ) ) {
		continue;
	}

	$oikos_link = is_array( $oikos_item['link'] ?? null ) ? $oikos_item['link'] : array();

	$oikos_items[] = array(
		'title' => trim( (string) $oikos_item['title'] ),
		'text'  => trim( (string) ( $oikos_item['text'] ?? '' ) ),
		'link'  => empty( $oikos_link['url'] ) ? array() : array(
			'url'    => (string) $oikos_link['url'],
			'title'  => trim( (string) ( $oikos_link['title'] ?? '' ) ),
			'target' => (string) ( $oikos_link['target'] ?? '' ),
		),
	);
}

if ( '' === trim( (string) $oikos_args['title'] ) || ! $oikos_items ) {
	return;
}

$oikos_id       = sanitize_html_class( $oikos_args['id'], 'temas' );
$oikos_level    = min( 5, max( 2, (int) $oikos_args['heading_level'] ) );
$oikos_sublevel = $oikos_level + 1;
$oikos_title_id = $oikos_id . '-titulo';
$oikos_text     = trim( (string) $oikos_args['text'] );
?>
<section class="oikos-topics" aria-labelledby="<?php echo esc_attr( $oikos_title_id ); ?>" data-topics>
	<div class="oikos-container oikos-topics__inner">
		<div class="oikos-topics__intro">
			<h<?php echo (int) $oikos_level; ?> id="<?php echo esc_attr( $oikos_title_id ); ?>" class="oikos-topics__heading oikos-text-heading"><?php echo esc_html( $oikos_args['title'] ); ?></h<?php echo (int) $oikos_level; ?>>

			<?php if ( '' !== $oikos_text ) : ?>
				<p class="oikos-topics__lead oikos-text-body oikos-text-secondary"><?php echo esc_html( $oikos_text ); ?></p>
			<?php endif; ?>
		</div>

		<div class="oikos-topics__cards">
			<ul class="oikos-topics__grid" role="list">
				<?php
				foreach ( $oikos_items as $oikos_index => $oikos_item ) :
					$oikos_has_body = '' !== $oikos_item['text'] || $oikos_item['link'];
					$oikos_body_id  = $oikos_id . '-tema-' . ( $oikos_index + 1 );
					?>
					<li class="oikos-topics__item<?php echo $oikos_has_body ? '' : ' oikos-topics__item--static'; ?>">
						<div class="oikos-topics__card">
							<div class="oikos-topics__content">
								<h<?php echo (int) $oikos_sublevel; ?> class="oikos-topics__title">
									<?php if ( $oikos_has_body ) : ?>
										<button type="button" class="oikos-topics__button" aria-expanded="true" aria-controls="<?php echo esc_attr( $oikos_body_id ); ?>"><?php echo esc_html( $oikos_item['title'] ); ?></button>
									<?php else : ?>
										<?php echo esc_html( $oikos_item['title'] ); ?>
									<?php endif; ?>
								</h<?php echo (int) $oikos_sublevel; ?>>

								<?php if ( $oikos_has_body ) : ?>
									<div class="oikos-topics__body" id="<?php echo esc_attr( $oikos_body_id ); ?>">
										<div class="oikos-topics__body-inner">
											<?php if ( '' !== $oikos_item['text'] ) : ?>
												<p class="oikos-topics__text"><?php echo esc_html( $oikos_item['text'] ); ?></p>
											<?php endif; ?>

											<?php if ( $oikos_item['link'] ) : ?>
												<a
													class="oikos-topics__link"
													href="<?php echo esc_url( $oikos_item['link']['url'] ); ?>"
													data-track="tema-leer-mas"
													data-track-location="<?php echo esc_attr( $oikos_args['track_location'] ); ?>"
													<?php echo '_blank' === $oikos_item['link']['target'] ? 'target="_blank" rel="noopener"' : ''; ?>
												><?php echo esc_html( '' !== $oikos_item['link']['title'] ? $oikos_item['link']['title'] : __( 'Leer más', 'oikos' ) ); ?><span class="screen-reader-text">: <?php echo esc_html( $oikos_item['title'] ); ?></span></a>
											<?php endif; ?>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

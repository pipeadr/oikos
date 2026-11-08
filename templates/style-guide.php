<?php
/**
 * Template Name: Guía de estilos
 *
 * Herramienta interna para revisar tokens, estilos de texto y componentes
 * base en todos los anchos. Solo visible para editores; nunca se indexa.
 * Los textos de muestra viven aquí porque no son contenido del sitio.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

if ( ! current_user_can( 'edit_pages' ) ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	require get_404_template();
	return;
}

add_filter(
	'wp_robots',
	static function ( $robots ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
		return $robots;
	}
);

$oikos_sg_colors = array(
	__( 'Roles (Figma)', 'oikos' ) => array(
		'--color-text-primary'          => 'color/text/primary',
		'--color-text-secondary'        => 'color/text/secondary',
		'--color-interactive-primary'   => 'color/interactive/primary',
		'--color-interactive-secondary' => 'color/interactive/secondary',
		'--color-content-inverse'       => 'color/content/inverse',
		'--color-surface-base'          => 'color/surface/base',
		'--color-border-default'        => 'color/border/default',
		'--color-surface-page'          => __( 'fondo de página (por confirmar)', 'oikos' ),
	),
	__( 'Marca (sin rol confirmado)', 'oikos' ) => array(
		'--color-brand-sage'    => 'sage',
		'--color-brand-sand'    => 'arena',
		'--color-brand-caramel' => 'caramelo',
	),
	__( 'Derivados (contraste AA con texto crema)', 'oikos' ) => array(
		'--color-brand-caramel-strong' => __( 'caramelo oscurecido · botón --tonal', 'oikos' ),
		'--gradient-brand'             => __( 'gradiente de bandas · verde → mezcla 50 % sage', 'oikos' ),
	),
);

$oikos_sg_text_styles = array(
	'oikos-text-display' => array( 'Display', 'Terapia psicológica para cuidar de ti y de tus relaciones.' ),
	'oikos-text-heading' => array( 'Heading', 'Lo que no se sana, se repite' ),
	'oikos-text-card'    => array( 'Card', 'Presencial y virtual' ),
	'oikos-text-body'    => array( 'Body', 'En Oikos acompañamos procesos individuales, de pareja y de familia con calidez y rigor profesional. Un espacio seguro —presencial en Medellín o virtual desde donde estés— para entender lo que duele, sanar lo que se repite y construir vínculos más conscientes.' ),
	'oikos-text-button'  => array( 'Button', 'Agenda tu cita' ),
	'oikos-text-eyebrow' => array( 'Eyebrow', 'El método' ),
	'oikos-text-nav'     => array( 'Nav', 'Acompañamientos' ),
);

$oikos_sg_buttons = array(
	'oikos-btn--primary'   => __( 'Agenda tu cita', 'oikos' ),
	'oikos-btn--secondary' => __( 'Conoce el método', 'oikos' ),
	'oikos-btn--light'     => __( 'Ver servicio', 'oikos' ),
	'oikos-btn--tonal'     => __( 'Agenda tu cita ahora', 'oikos' ),
);

$oikos_sg_surfaces = array(
	'page'  => __( 'Sobre blanco', 'oikos' ),
	'cream' => __( 'Sobre crema', 'oikos' ),
	'dark'  => __( 'Sobre banda verde', 'oikos' ),
	'brown' => __( 'Sobre bloque café (footer) · --tonal es para este fondo', 'oikos' ),
);

get_header();
?>

<div class="oikos-sg">
	<header class="oikos-sg__intro oikos-container">
		<p class="oikos-text-eyebrow oikos-text-secondary"><?php esc_html_e( 'Herramienta interna · no indexable', 'oikos' ); ?></p>
		<h1 class="oikos-text-display"><?php esc_html_e( 'Guía de estilos', 'oikos' ); ?></h1>
		<p class="oikos-sg__viewport" data-sg-viewport aria-live="polite"></p>
	</header>

	<!-- Colores -->
	<section class="oikos-section oikos-container" aria-labelledby="sg-colors">
		<h2 id="sg-colors" class="oikos-text-heading"><?php esc_html_e( 'Colores', 'oikos' ); ?></h2>
		<?php foreach ( $oikos_sg_colors as $oikos_group => $oikos_tokens ) : ?>
			<h3 class="oikos-sg__subtitle oikos-text-card"><?php echo esc_html( $oikos_group ); ?></h3>
			<ul class="oikos-sg__swatches">
				<?php foreach ( $oikos_tokens as $oikos_var => $oikos_label ) : ?>
					<li class="oikos-sg__swatch">
						<span class="oikos-sg__swatch-color" style="background: var(<?php echo esc_attr( $oikos_var ); ?>);"></span>
						<code><?php echo esc_html( $oikos_var ); ?></code>
						<span class="oikos-sg__meta"><?php echo esc_html( $oikos_label ); ?></span>
						<span class="oikos-sg__meta" data-sg-token="<?php echo esc_attr( $oikos_var ); ?>"></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>
	</section>

	<!-- Tipografía -->
	<section class="oikos-section oikos-sg__band" aria-labelledby="sg-type">
		<div class="oikos-container">
			<h2 id="sg-type" class="oikos-text-heading"><?php esc_html_e( 'Estilos de texto', 'oikos' ); ?></h2>
			<?php foreach ( $oikos_sg_text_styles as $oikos_class => $oikos_style ) : ?>
				<div class="oikos-sg__type-row">
					<p class="oikos-sg__meta">
						<code>.<?php echo esc_html( $oikos_class ); ?></code> · <?php echo esc_html( $oikos_style[0] ); ?>
						· <span data-sg-type-for="<?php echo esc_attr( $oikos_class ); ?>"></span>
					</p>
					<p class="<?php echo esc_attr( $oikos_class ); ?>" data-sg-type="<?php echo esc_attr( $oikos_class ); ?>"><?php echo esc_html( $oikos_style[1] ); ?></p>
				</div>
			<?php endforeach; ?>
			<div class="oikos-sg__type-row">
				<p class="oikos-sg__meta"><code>.oikos-text-body.oikos-text-secondary</code></p>
				<p class="oikos-text-body oikos-text-secondary"><?php echo esc_html( $oikos_sg_text_styles['oikos-text-body'][1] ); ?></p>
			</div>
		</div>
	</section>

	<!-- Espaciado -->
	<section class="oikos-section oikos-container" aria-labelledby="sg-spacing">
		<h2 id="sg-spacing" class="oikos-text-heading"><?php esc_html_e( 'Espaciado', 'oikos' ); ?></h2>
		<h3 class="oikos-sg__subtitle oikos-text-card"><?php esc_html_e( 'Escala fija (8 px)', 'oikos' ); ?></h3>
		<ul class="oikos-sg__spacing">
			<?php for ( $oikos_i = 1; $oikos_i <= 10; $oikos_i++ ) : ?>
				<?php $oikos_var = '--spacing-' . $oikos_i; ?>
				<li>
					<code><?php echo esc_html( $oikos_var ); ?></code>
					<span class="oikos-sg__bar" style="width: var(<?php echo esc_attr( $oikos_var ); ?>);"></span>
					<span class="oikos-sg__meta" data-sg-token="<?php echo esc_attr( $oikos_var ); ?>" data-sg-px></span>
				</li>
			<?php endfor; ?>
		</ul>
		<h3 class="oikos-sg__subtitle oikos-text-card"><?php esc_html_e( 'Fluidos (móvil → desktop)', 'oikos' ); ?></h3>
		<ul class="oikos-sg__spacing">
			<?php
			$oikos_fluid = array(
				'--container-padding' => '20 → 80',
				'--space-section'     => '48 → 80',
				'--card-padding'      => '20 → 24',
			);
			foreach ( $oikos_fluid as $oikos_var => $oikos_range ) :
				?>
				<li>
					<code><?php echo esc_html( $oikos_var ); ?></code>
					<span class="oikos-sg__bar" style="width: var(<?php echo esc_attr( $oikos_var ); ?>);"></span>
					<span class="oikos-sg__meta"><?php echo esc_html( $oikos_range ); ?> · <span data-sg-token="<?php echo esc_attr( $oikos_var ); ?>" data-sg-px></span></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>

	<!-- Radios -->
	<section class="oikos-section oikos-sg__band" aria-labelledby="sg-radius">
		<div class="oikos-container">
			<h2 id="sg-radius" class="oikos-text-heading"><?php esc_html_e( 'Radios', 'oikos' ); ?></h2>
			<ul class="oikos-sg__radii">
				<?php foreach ( array( '--radius-md' => 'radius/md · botones', '--radius-lg' => 'radius/lg · tarjetas' ) as $oikos_var => $oikos_label ) : ?>
					<li>
						<span class="oikos-sg__radius" style="border-radius: var(<?php echo esc_attr( $oikos_var ); ?>);"></span>
						<code><?php echo esc_html( $oikos_var ); ?></code>
						<span class="oikos-sg__meta"><?php echo esc_html( $oikos_label ); ?> · <span data-sg-token="<?php echo esc_attr( $oikos_var ); ?>" data-sg-px></span></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>

	<!-- Botones -->
	<section class="oikos-section oikos-container" aria-labelledby="sg-buttons">
		<h2 id="sg-buttons" class="oikos-text-heading"><?php esc_html_e( 'Botones (Medium)', 'oikos' ); ?></h2>
		<?php foreach ( $oikos_sg_surfaces as $oikos_surface => $oikos_surface_label ) : ?>
			<div class="oikos-sg__surface oikos-sg__surface--<?php echo esc_attr( $oikos_surface ); ?>">
				<p class="oikos-sg__meta"><?php echo esc_html( $oikos_surface_label ); ?></p>
				<div class="oikos-sg__buttons">
					<?php foreach ( $oikos_sg_buttons as $oikos_variant => $oikos_label ) : ?>
						<a class="oikos-btn <?php echo esc_attr( $oikos_variant ); ?>" href="#sg-buttons"><?php echo esc_html( $oikos_label ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endforeach; ?>
		<div class="oikos-sg__surface oikos-sg__surface--page">
			<p class="oikos-sg__meta"><code>.oikos-btn--compact</code> · <?php esc_html_e( '44 px de alto (CTA del header)', 'oikos' ); ?></p>
			<a class="oikos-btn oikos-btn--primary oikos-btn--compact" href="#sg-buttons"><?php esc_html_e( 'Agenda tu cita', 'oikos' ); ?></a>
		</div>
		<div class="oikos-sg__surface oikos-sg__surface--page">
			<p class="oikos-sg__meta"><code>.oikos-btn--block-mobile</code> · <?php esc_html_e( 'ancho completo por debajo de 768 px', 'oikos' ); ?></p>
			<a class="oikos-btn oikos-btn--primary oikos-btn--block-mobile" href="#sg-buttons"><?php esc_html_e( 'Agenda tu cita', 'oikos' ); ?></a>
		</div>
		<p class="oikos-sg__meta" data-sg-button-height></p>
	</section>

	<!-- Tarjeta base -->
	<section class="oikos-section oikos-sg__band" aria-labelledby="sg-card">
		<div class="oikos-container">
			<h2 id="sg-card" class="oikos-text-heading"><?php esc_html_e( 'Tarjeta base', 'oikos' ); ?></h2>
			<div class="oikos-sg__cards">
				<?php foreach ( array( 'Presencial y virtual', '+150 procesos acompañados', 'Un proceso único para ti' ) as $oikos_card ) : ?>
					<div class="oikos-card">
						<p class="oikos-text-card"><?php echo esc_html( $oikos_card ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="oikos-sg__cards">
				<div class="oikos-card">
					<p class="oikos-text-eyebrow oikos-text-secondary"><?php esc_html_e( 'Tarjeta con contenido', 'oikos' ); ?></p>
					<p class="oikos-text-card"><?php esc_html_e( 'Terapia de pareja', 'oikos' ); ?></p>
					<p class="oikos-text-body oikos-text-secondary"><?php esc_html_e( 'Un espacio para entender lo que duele y construir vínculos más conscientes.', 'oikos' ); ?></p>
					<a class="oikos-btn oikos-btn--primary" href="#sg-card"><?php esc_html_e( 'Agenda tu cita', 'oikos' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<!-- Contenedor -->
	<section class="oikos-section" aria-labelledby="sg-container">
		<div class="oikos-container">
			<h2 id="sg-container" class="oikos-text-heading"><?php esc_html_e( 'Contenedor', 'oikos' ); ?></h2>
			<div class="oikos-sg__container-demo">
				<code>.oikos-container</code>
				<span class="oikos-sg__meta" data-sg-container></span>
			</div>
		</div>
	</section>
</div>

<?php
get_footer();

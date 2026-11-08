<?php
/**
 * Header: barra flotante con logo, menú principal en píldoras y CTA.
 *
 * - ≥ 1024 px: menú en línea; "Acompañamientos" (ítem con hijos) abre un
 *   desplegable.
 * - < 1024 px: el menú vive en un panel que se despliega dentro de la misma
 *   barra (botón hamburguesa). El submenú funciona como acordeón y el CTA
 *   aparece al final del panel.
 * - Fijo arriba; header.js lo esconde al bajar y lo muestra al subir.
 *
 * Sin JS: el panel queda visible en móvil y los submenús abren con
 * hover/foco (ver .is-enhanced en header.css).
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

$oikos_has_menu = has_nav_menu( 'primary' );
$oikos_cta      = oikos_cta();
$oikos_has_cta  = '' !== $oikos_cta['url'];
?>
<header class="oikos-header" data-oikos-header>
	<div class="oikos-container">
		<div class="oikos-header__bar">
			<div class="oikos-header__brand">
				<?php get_template_part( 'template-parts/components/logo', null, array( 'variant' => 'default' ) ); ?>
			</div>

			<?php if ( $oikos_has_menu || $oikos_has_cta ) : ?>
				<div class="oikos-header__panel" id="oikos-header-panel">
					<?php if ( $oikos_has_menu ) : ?>
						<nav class="oikos-nav" aria-label="<?php esc_attr_e( 'Menú principal', 'oikos' ); ?>">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'primary',
									'container'      => false,
									'menu_class'     => 'oikos-nav__list',
									'fallback_cb'    => false,
									'depth'          => 2,
									'walker'         => new Oikos_Primary_Nav_Walker(),
								)
							);
							?>
						</nav>
					<?php endif; ?>

					<?php if ( $oikos_has_cta ) : ?>
						<div class="oikos-header__panel-cta">
							<?php
							oikos_the_cta_button(
								array(
									'class'    => 'oikos-btn oikos-btn--primary oikos-btn--block-mobile',
									'location' => 'header-menu',
								)
							);
							?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="oikos-header__actions">
				<?php
				oikos_the_cta_button(
					array(
						'class'    => 'oikos-btn oikos-btn--primary oikos-btn--compact oikos-header__cta',
						'location' => 'header',
					)
				);
				?>

				<?php if ( $oikos_has_menu || $oikos_has_cta ) : ?>
					<button
						type="button"
						class="oikos-header__toggle"
						aria-expanded="false"
						aria-controls="oikos-header-panel"
						data-label-open="<?php esc_attr_e( 'Abrir menú', 'oikos' ); ?>"
						data-label-close="<?php esc_attr_e( 'Cerrar menú', 'oikos' ); ?>"
					>
						<span class="screen-reader-text"><?php esc_html_e( 'Abrir menú', 'oikos' ); ?></span>
						<?php oikos_the_icon( 'menu', 'oikos-header__toggle-icon oikos-header__toggle-icon--open' ); ?>
						<?php oikos_the_icon( 'close', 'oikos-header__toggle-icon oikos-header__toggle-icon--close' ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
	</div>
</header>
<div class="oikos-header__backdrop" data-oikos-header-backdrop hidden></div>

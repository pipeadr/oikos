<?php
/**
 * Ubicación ACF "Página de servicio": páginas de terapia (hijas de la
 * página con plantilla "Servicios general"). Ver oikos_is_service_page().
 *
 * Se carga desde oikos_register_service_page_location() (inc/services.php),
 * cuando ACF_Location ya existe.
 *
 * @package oikos
 */

defined( 'ABSPATH' ) || exit;

/**
 * Regla de ubicación "Página de servicio".
 */
class Oikos_ACF_Location_Service_Page extends ACF_Location {

	/**
	 * Datos de la regla.
	 *
	 * @return void
	 */
	public function initialize() {
		$this->name           = 'oikos_service_page';
		$this->label          = __( 'Página de servicio (Oikos)', 'oikos' );
		$this->category       = 'page';
		$this->object_type    = 'post';
		$this->object_subtype = 'page';
	}

	/**
	 * Valores posibles.
	 *
	 * @param array $rule Regla.
	 * @return array
	 */
	public function get_values( $rule ) {
		return array(
			'1' => __( 'Hija de "Servicios general"', 'oikos' ),
		);
	}

	/**
	 * ¿La pantalla actual cumple la regla?
	 *
	 * @param array $rule        Regla.
	 * @param array $screen      Pantalla (post_id, post_type, page_parent…).
	 * @param array $field_group Grupo de campos.
	 * @return bool
	 */
	public function match( $rule, $screen, $field_group ) {
		$post_id   = isset( $screen['post_id'] ) ? absint( $screen['post_id'] ) : 0;
		$post_type = isset( $screen['post_type'] ) ? $screen['post_type'] : get_post_type( $post_id );

		$result = false;

		if ( 'page' === $post_type ) {
			// ACF envía page_parent cuando se cambia el padre en el editor.
			$parent = isset( $screen['page_parent'] ) ? $screen['page_parent'] : null;
			$result = oikos_is_service_page( $post_id, $parent );
		}

		return ( isset( $rule['operator'] ) && '!=' === $rule['operator'] ) ? ! $result : $result;
	}
}

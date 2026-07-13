<?php
/**
 * OIDC Client – Sanitize-Callbacks für Admin-Einstellungen.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Stellt Sanitize-Callbacks für Admin-Einstellungen bereit.
 */
class OIDC_Admin_Sanitize {

	/**
	 * Sanitize-Callback für Checkboxen: gibt '1' oder '' zurück.
	 *
	 * @param mixed $value Eingabewert.
	 * @return string '1' oder ''.
	 */
	public function sanitize_checkbox( $value ) {
		return ( '1' === $value || true === $value ) ? '1' : '';
	}

	/**
	 * Sanitize-Callback für Client-Secrets: entfernt nur Null-Bytes und Whitespace.
	 *
	 * @param mixed $value Eingabewert.
	 * @return string Bereinigtes Secret.
	 */
	public function sanitize_secret( $value ) {
		// Nur Null-Bytes und Whitespace am Rand entfernen – keine HTML-Stripping
		$value = wp_unslash( $value );
		$value = str_replace( "\x00", '', $value ); // Null-Bytes
		return trim( $value );
	}

	/**
	 * Sanitize-Callback für das Rollen-Mapping (JSON-Objekt).
	 *
	 * @param mixed $value JSON-kodiertes Rollen-Mapping.
	 * @return string Sanitiertes JSON oder leerer String.
	 */
	public function sanitize_role_mapping( $value ) {
		if ( empty( $value ) ) {
			return '';
		}
		$decoded = json_decode( wp_unslash( $value ), true );
		if ( ! is_array( $decoded ) ) {
			return '';
		}
		$sanitized = array();
		foreach ( $decoded as $k => $v ) {
			$sanitized[ sanitize_text_field( $k ) ] = sanitize_text_field( $v );
		}
		return wp_json_encode( $sanitized );
	}
}

<?php
/**
 * Trait helper.
 *
 * @package GlotCore\TranslationMemory
 */

namespace GlotCore\TranslationMemory;

/**
 * Helper trait.
 */
trait Helper {

	/**
	 * Normalize string for simplified matching.
	 *
	 * @param string $text Original string.
	 *
	 * @return string Normalized string.
	 */
	protected function normalize( string $text ): string {
		// Lowercase.
		$text = mb_strtolower( $text );

		// Replace placeholders (%s, %d, %1$s, etc.).
		$text = preg_replace( '/%[0-9\$\.\-]*[sd]/i', ' VAR ', $text );

		// Replace variables like {name}.
		$text = preg_replace( '/\{[^\}]+\}/', ' VAR ', $text );

		// Remove punctuation.
		$text = preg_replace( '/[^\p{L}\p{N}\s]/u', ' ', $text );

		// Normalize whitespace.
		$text = preg_replace( '/\s+/', ' ', $text );

		return trim( $text );
	}

	/**
	 * Levenshtein similarity (0–1).
	 *
	 * @param string $a First string (normalized).
	 * @param string $b Second string (normalized).
	 *
	 * @return float Similarity score between 0 and 1.
	 */
	protected function levenshtein_similarity( string $a, string $b ): float {
		$len_a = mb_strlen( $a );
		$len_b = mb_strlen( $b );

		// If both are empty, they are identical.
		if ( 0 === $len_a && 0 === $len_b ) {
			return 1.0;
		}

		// If one is empty, similarity is 0.
		if ( 0 === $len_a || 0 === $len_b ) {
			return 0.0;
		}

		// Use standard levenshtein (byte-based).
		$distance = levenshtein( $a, $b );

		return 1 - ( $distance / max( $len_a, $len_b ) );
	}

	/**
	 * Return table name with prefix.
	 *
	 * @return string
	 */
	protected static function get_table(): string {
		global $wpdb;

		return $wpdb->prefix . 'gp_tm_originals';
	}
}

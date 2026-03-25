<?php
/**
 * Translation memory search class.
 *
 * @package GlotCore\TranslationMemory
 */

namespace GlotCore\TranslationMemory;

/**
 * Translation memory search class.
 */
class Search {

	use Helper;

	/**
	 * Main search method.
	 *
	 * @param int $original_id The original ID to search for.
	 * @param int $translation_set_id The translation set ID to filter translations.
	 *
	 * @return array Array of recent translations for similar originals.
	 */
	public function search( int $original_id, int $translation_set_id ): array {
		// Step 0: Get original string.
		$original = gp_get_original( $original_id );
		if ( empty( $original ) || empty( $original->singular ) ) {
			return array();
		}

		$search_text = $original->singular;

		// Step 1: Get candidates from DB using BOOLEAN MODE search.
		$originals = $this->search_originals( $search_text, 100 );

		// Step 2: Rank candidates using Levenshtein similarity.
		$ranker         = new Ranker( 0.8, 10 );
		$ranker_results = $ranker->rank( $search_text, $originals );

		// Step 3: Extract original IDs from ranked results.
		$original_ids = wp_list_pluck( $ranker_results, 'original_id' );

		// Step 4: Get recent translations for these original IDs.
		$translations = $this->get_recent_translations( $original_ids, $translation_set_id );

		return $translations;
	}

	/**
	 * Search of originals based on input string.
	 * Returns array of objects with original_id, singular_simplified, and score.
	 *
	 * @param string $search_text The original string to search for.
	 * @param int    $limit Number of results to return.
	 *
	 * @return array
	 */
	public function search_originals( string $search_text, int $limit = 100 ): array {
		global $wpdb;

		$normalized = $this->normalize( $search_text );
		if ( empty( $normalized ) ) {
			return array();
		}

		$boolean_query = $this->build_boolean_query( $normalized );

		$table_name = self::get_table();

		$sql = $wpdb->prepare(
			'
            SELECT original_id,
                   singular_simplified,
                   MATCH(singular_simplified) AGAINST(%s IN BOOLEAN MODE) AS score
            FROM %i
            WHERE MATCH(singular_simplified) AGAINST(%s IN BOOLEAN MODE)
            ORDER BY score DESC
            LIMIT %d
            ',
			$boolean_query,
			$table_name,
			$boolean_query,
			$limit
		);

		return $wpdb->get_results( $sql ); // phpcs:ignore
	}

	/**
	 * Get recent translations for given original IDs.
	 *
	 * @param array $original_ids Array of original IDs.
	 * @param int   $translation_set_id Translation set ID to filter translations.
	 *
	 * @return array Translations.
	 */
	public function get_recent_translations( array $original_ids, int $translation_set_id ): array {
		global $wpdb;

		if ( empty( $original_ids ) ) {
			return array();
		}

		// Sanitize IDs.
		$original_ids = array_map( 'intval', $original_ids );
		$original_ids = array_filter( $original_ids );

		if ( empty( $original_ids ) ) {
			return array();
		}

		// Prepare IDs string for IN().
		$original_ids_str = implode( ',', wp_parse_id_list( $original_ids ) );

		// phpcs:disable
		$sql = $wpdb->prepare(
			'
            SELECT id, original_id, translation_set_id, translation_0, status, date_added
            FROM %i
            WHERE translation_set_id = %d
              AND original_id IN (' . $original_ids_str . ')
              AND status = %s
            ORDER BY original_id ASC, date_added DESC
            ',
			$wpdb->gp_translations,
			$translation_set_id,
			'current'
		);
		// phpcs:enable

		$translations = $wpdb->get_results( $sql ); // phpcs:ignore

		// Limit to 5 recent translations.
		$translations = array_slice( $translations, 0, 5 );

		// Translation strings.
		$translation_strings = wp_list_pluck( $translations, 'translation_0' );

		return $translation_strings;
	}

	/**
	 * Build BOOLEAN MODE query.
	 * Each word is prefixed with '+' and suffixed with '*' for wildcard matching.
	 *
	 * @param string $search_text The normalized search text.
	 *
	 * @return string The BOOLEAN MODE query string.
	 */
	protected function build_boolean_query( string $search_text ): string {
		$words = explode( ' ', $search_text );
		$words = array_filter( $words );

		$query_parts = array();

		foreach ( $words as $word ) {
			if ( mb_strlen( $word ) < 2 ) {
				continue;
			}

			$query_parts[] = '+' . $word . '*';
		}

		return implode( ' ', $query_parts );
	}
}

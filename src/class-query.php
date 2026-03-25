<?php
/**
 * Translation memory query class.
 *
 * @package GlotCore\TranslationMemory
 */

namespace GlotCore\TranslationMemory;

/**
 * Translation memory query class.
 */
class Query {

	use Helper;

	/**
	 * Insert or update simplified string.
	 *
	 * @param int    $original_id Original ID.
	 * @param string $simplified Simplified string.
	 *
	 * @return void
	 */
	public static function upsert( int $original_id, string $simplified ): void {
		global $wpdb;

		$wpdb->replace( // phpcs:ignore
			self::get_table(),
			array(
				'original_id'         => $original_id,
				'singular_simplified' => $simplified,
			),
			array(
				'%d',
				'%s',
			)
		);
	}

	/**
	 * Delete entry.
	 *
	 * @param int $original_id Original ID.
	 *
	 * @return void
	 */
	public static function delete( int $original_id ): void {
		global $wpdb;

		$wpdb->delete( // phpcs:ignore
			self::get_table(),
			array( 'original_id' => $original_id ),
			array( '%d' )
		);
	}
}

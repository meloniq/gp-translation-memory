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
class Database {

	use Helper;

	/**
	 * Create TM table.
	 *
	 * @return void
	 */
	public static function create_table(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = self::get_table();

		$sql = "
        CREATE TABLE {$table_name} (
            original_id BIGINT(20) UNSIGNED NOT NULL,
            singular_simplified TEXT NOT NULL,
            PRIMARY KEY (original_id),
            FULLTEXT KEY singular_simplified (singular_simplified)
        ) $charset_collate;
        ";

		dbDelta( $sql );
	}

	/**
	 * Drop TM table.
	 *
	 * @return void
	 */
	public static function drop_table(): void {
		global $wpdb;

		$table_name = self::get_table();

		$sql = "DROP TABLE IF EXISTS {$table_name}";
		$wpdb->query( $sql ); // phpcs:ignore
	}

	/**
	 * Clear all entries from the TM table.
	 *
	 * @return void
	 */
	public static function clear_table(): void {
		global $wpdb;

		$table_name = self::get_table();

		$sql = "TRUNCATE TABLE {$table_name}";
		$wpdb->query( $sql ); // phpcs:ignore
	}
}

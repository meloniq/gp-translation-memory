<?php
/**
 * Translation memory core class.
 *
 * @package GlotCore\TranslationMemory
 */

namespace GlotCore\TranslationMemory;

/**
 * Translation memory core class.
 */
class Core {

	use Helper;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// plugin activation hook.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		// plugin uninstall hook.
		register_uninstall_hook( __FILE__, array( $this, 'uninstall' ) );
	}

	/**
	 * Plugin activation callback.
	 *
	 * @return void
	 */
	public function activate(): void {
		// Create the TM table on plugin activation.
		Database::create_table();
	}

	/**
	 * Plugin uninstall callback.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		// Drop the TM table on plugin uninstall.
		Database::drop_table();
	}
}

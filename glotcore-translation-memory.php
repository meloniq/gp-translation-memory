<?php
/**
 * Plugin Name:       GlotCore Translation Memory
 * Plugin URI:        https://blog.meloniq.net/gp-translation-memory/
 *
 * Description:       Suggests translations based on a translation memory of previously translated strings in GlotPress.
 * Tags:              glotpress, translation, memory, suggestions
 *
 * Requires at least: 4.9
 * Requires PHP:      7.4
 * Version:           1.0
 *
 * Author:            MELONIQ.NET
 * Author URI:        https://meloniq.net/
 *
 * License:           GPLv2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Text Domain:       glotcore-translation-memory
 *
 * Requires Plugins:  glotpress
 *
 * @package GlotCore\TranslationMemory
 */

namespace GlotCore\TranslationMemory;

// If this file is accessed directly, then abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'GC_TM_TD', 'glotcore-translation-memory' );
define( 'GC_TM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GC_TM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * GP Init Setup.
 *
 * @return void
 */
function gp_init() {
	global $glotcore_translation_memory;

	require_once trailingslashit( __DIR__ ) . 'src/trait-helper.php';
	require_once trailingslashit( __DIR__ ) . 'src/class-core.php';
	require_once trailingslashit( __DIR__ ) . 'src/class-database.php';
	require_once trailingslashit( __DIR__ ) . 'src/class-query.php';
	require_once trailingslashit( __DIR__ ) . 'src/class-ranker.php';
	require_once trailingslashit( __DIR__ ) . 'src/class-search.php';
	require_once trailingslashit( __DIR__ ) . 'src/class-sync.php';

	$glotcore_translation_memory['core'] = new Core();
	$glotcore_translation_memory['sync'] = new Sync();
}
add_action( 'gp_init', 'GlotCore\TranslationMemory\gp_init' );

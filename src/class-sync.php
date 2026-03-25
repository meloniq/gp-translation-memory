<?php
/**
 * Translation memory synchronization class.
 *
 * @package GlotCore\TranslationMemory
 */

namespace GlotCore\TranslationMemory;

/**
 * Translation memory synchronization class.
 */
class Sync {

	use Helper;

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'gp_original_created', array( $this, 'on_create' ) );
		add_action( 'gp_original_saved', array( $this, 'on_save' ) );
		add_action( 'gp_original_deleted', array( $this, 'on_delete' ) );
	}

	/**
	 * Handle original creation.
	 *
	 * @param object $original The original object.
	 *
	 * @return void
	 */
	public function on_create( object $original ): void {
		if ( empty( $original ) || empty( $original->id ) ) {
			return;
		}

		$simplified = $this->normalize( $original->singular );

		if ( empty( $simplified ) ) {
			Query::delete( $original->id );
			return;
		}

		Query::upsert( $original->id, $simplified );
	}

	/**
	 * Handle original update.
	 *
	 * @param object $original The original object.
	 *
	 * @return void
	 */
	public function on_save( object $original ): void {
		if ( empty( $original ) || empty( $original->id ) ) {
			return;
		}

		$simplified = $this->normalize( $original->singular );

		if ( empty( $simplified ) ) {
			Query::delete( $original->id );
			return;
		}

		Query::upsert( $original->id, $simplified );
	}

	/**
	 * Handle original deletion.
	 *
	 * @param object $original The original object.
	 *
	 * @return void
	 */
	public function on_delete( object $original ): void {
		if ( empty( $original ) || empty( $original->id ) ) {
			return;
		}

		Query::delete( $original->id );
	}
}

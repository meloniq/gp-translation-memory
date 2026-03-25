<?php
/**
 * Translation memory ranking class.
 *
 * @package GlotCore\TranslationMemory
 */

namespace GlotCore\TranslationMemory;

/**
 * Translation memory ranking class.
 */
class Ranker {

	use Helper;

	/**
	 * Similarity threshold (0 to 1).
	 *
	 * @var float
	 */
	protected $threshold;

	/**
	 * Number of results to return after ranking.
	 *
	 * @var int
	 */
	protected $limit;

	/**
	 * Constructor.
	 *
	 * @param float $threshold Similarity threshold (0 to 1).
	 * @param int   $limit Number of results to return after ranking.
	 *
	 * @return void
	 */
	public function __construct( $threshold = 0.8, $limit = 10 ) {
		$this->threshold = $threshold;
		$this->limit     = $limit;
	}

	/**
	 * Main ranking method
	 *
	 * @param string $input Original string (not simplified).
	 * @param array  $candidates Results from DB (objects with singular_simplified).
	 *
	 * @return array
	 */
	public function rank( string $input, array $candidates ) {
		$input_normalized = $this->normalize( $input );
		$input_length     = mb_strlen( $input_normalized );

		$results = array();

		foreach ( $candidates as $row ) {

			$candidate = $row->singular_simplified ?? '';
			if ( empty( $candidate ) ) {
				continue;
			}

			// Quick length filter (performance optimization).
			if ( abs( mb_strlen( $candidate ) - $input_length ) > 30 ) {
				continue;
			}

			$score = $this->levenshtein_similarity( $input_normalized, $candidate );

			if ( $score >= $this->threshold ) {
				$row->lev_score = $score;
				$results[]      = $row;
			}
		}

		// Sort by similarity DESC.
		usort(
			$results,
			function ( $a, $b ) {
				return $b->lev_score <=> $a->lev_score;
			}
		);

		return array_slice( $results, 0, $this->limit );
	}
}

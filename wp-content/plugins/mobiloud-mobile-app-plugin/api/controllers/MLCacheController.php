<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 */
class MLCacheController {
	/**
	 * Set cache item as wp transient record
	 *
	 * @since 3.3.3
	 *
	 * @param $type String - type of the record (for the flush cache by type)
	 * @param $key String - unique key for the data
	 * @param $data String - cached data
	 */
	function set_cache( $type, $key, $data ) {
		$hash = hash( 'crc32', $key );
		set_transient( $type . '_' . $hash, $data, 8 * HOUR_IN_SECONDS );
	}

	/**
	 * Get cache from wp transient database
	 *
	 * @since 3.3.3
	 *
	 * @param $type String
	 * @param $key String
	 *
	 * @return String | null
	 */
	function get_cache( $type, $key ) {
		$hash   = hash( 'crc32', $key );
		$cached = get_transient( $type . '_' . $hash );

		return ( ! empty( $cached ) ? $cached : null );
	}

}
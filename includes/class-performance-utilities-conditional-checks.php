<?php

/**
 * Conditional checks for filtering page match rules
 *
 * @link       https://github.com/jruns/wp-performance-utilities
 * @since      0.1.0
 *
 * @package    Performance_Utilities
 * @subpackage Performance_Utilities/includes
 */

class Performance_Utilities_Conditional_Checks {
	public function __construct() {
	}

	public static function filter_matches( $matches ) {
		global $wp;

		$allowed_conditionals = array(
			'is_home', 'is_front_page', 'is_single', 'is_page', 'is_author', 'is_archive', 'has_excerpt',
			'is_search', 'is_404', 'is_paged', 'is_attachment', 'is_singular', 'is_user_logged_in',
			'not_is_home', 'not_is_front_page', 'not_is_single', 'not_is_page', 'not_is_author', 'not_is_archive', 'not_has_excerpt',
			'not_is_search', 'not_is_404', 'not_is_paged', 'not_is_attachment', 'not_is_singular', 'not_is_user_logged_in'
		);

		$url_path = sanitize_title( str_replace( '/', '_', wp_parse_url( $wp->request )['path'] ) );

		return array_filter( $matches, function( $value ) use( $allowed_conditionals, $url_path ) {
			// Default to match all posts/pages if 'where' key is not set
			if ( ! array_key_exists( 'where', $value ) ) {
				return true;
			}

			if ( is_string( $value['where'] ) ) {
				$value['where'] = array( $value['where'] );
			}

			return array_reduce( $value['where'], function( $carry, $conditional ) use ( $allowed_conditionals, $url_path ) {
				$negate = false;
				if ( 0 === strpos( $conditional, 'not_' ) ) {
					// negate conditional
					$conditional = substr( $conditional, 4 );
					$negate = true;
				}

				if ( 'all' === $conditional ) {
					// Match all pages if conditional is set to 'all'
					return $carry && true;
				} else if ( 0 === strpos( $conditional, 'path_' ) ) {
					$conditional = substr( $conditional, 5 );
					return $negate ? ( $carry && ! substr( $url_path, 0, strlen( $conditional ) ) === $conditional ) : ( $carry && substr( $url_path, 0, strlen( $conditional ) ) === $conditional );
				} elseif ( ! in_array( $conditional, $allowed_conditionals ) ) {
					// Exclude unallowed conditional from matching
					return $carry;
				}

				return $negate ? ( $carry && ! $conditional() ) : ( $carry && $conditional() );
			}, true);

			return true;
		} );
	}
}

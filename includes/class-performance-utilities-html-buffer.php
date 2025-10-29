<?php

/**
 * HTML Buffer for modifying html before its output
 *
 * @link       https://github.com/jruns/wp-performance-utilities
 * @since      0.1.0
 *
 * @package    Performance_Utilities
 * @subpackage Performance_Utilities/includes
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Performance_Utilities_Html_Buffer {

	private $buffer;

	public function __construct() {
		add_action( 'init', array( $this, 'start_buffer' ) );
		add_action( 'shutdown', array( $this, 'end_buffer' ), 9 );
	}

	static function filter_buffer( $buffer ) {
		$buffer = apply_filters( 'wppu_modify_final_output', $buffer );
		return $buffer;
	}

	public function start_buffer() {
		ob_start( array( Performance_Utilities_Html_Buffer::class, 'filter_buffer' ) );
	}

	public function end_buffer() {
		$this->buffer = ob_get_clean();
	}

	/**
	 * Process buffer tag replacements
	 *
	 * @since    0.3.0
	 */
	public static function process_buffer_replacements( $buffer, $args ) {
		extract( $args );

		$moves_queue = array();
		$insert_delay_scripts = array();

		foreach ( $match_settings as $ele ) {
			// skip element if it is missing required keys or has unallowed values
			if ( ! array_key_exists( 'match', $ele ) || ! array_key_exists( 'find', $ele ) || ! in_array( $ele['match'], $match_types ) ) {
				continue;
			}

			// Turn array search options into an OR string
			if ( is_array( $ele['find'] ) ) {
				$ele['find'] = join( "|", $ele['find'] );
			}
			$search_string = addcslashes( $ele['find'], '/()[]{}.,+*^$' );

			if ( 'code' === $ele['match'] ) {
				if ( 'script' === $tag_type ) {
					$regex_string = '/<script[^>]*?>(?!<\/[^>]*script[^>]*?)[\s\S]+?<\/[^>]*script[^>]*?>\n?/im';
				} else {
					$regex_string = '/<style[^>]*?>(?!<\/[^>]*script[^>]*?)[\s\S]+?<\/[^>]*style[^>]*?>\n?/im';
				}
			} else {
				$regex_base = $ele['match'] . '=[\\\'\"][^\\\'\"]*(' . $search_string . ')[^\\\'\"]*[\\\'\"]';
				
				if ( 'script' === $tag_type ) {
					$regex_string = '/<script[^>]*?' . $regex_base . '[^>]*?>[\s\S]*?<\/[^>]*script[^>]*>\n?/im';
				} elseif ( isset( $tag_matches ) && 'link' === $tag_matches ) {
					$regex_string = '/<link[^>]*?' . $regex_base . '[^>]*?rel=[\\\'\"]stylesheet[\\\'\"][^>]*?>\n?|<link[^>]*?rel=[\\\'\"]stylesheet[\\\'\"][^>]*' . $regex_base . '[^>]*?>\n?/im';
				} else {
					$regex_string = '/<link[^>]*?' . $regex_base . '[^>]*?rel=[\\\'\"]stylesheet[\\\'\"][^>]*?>\n?|<link[^>]*?rel=[\\\'\"]stylesheet[\\\'\"][^>]*' . $regex_base . '[^>]*?>\n?|<style[^>]*?' . $regex_base . '[^>]*?>[\s\S]*?<\/[^>]*style[^>]*>\n?/im';
				}
			}

			$buffer = preg_replace_callback( 
				$regex_string, 
				function( $matches ) use( $operation, $tag_type, $search_string, $ele, &$insert_delay_scripts )  {
					$tag_contents = $matches[0];

					if ( 'code' === $ele['match'] ) {
						// Check if code section matches search settings. Skip tag if it doesn't.
						if ( 0 === preg_match( '/(' . $search_string . ')/im', $tag_contents ) ) {
							return $tag_contents;
						}
					}

					if ( $operation === 'delay' ) {
						$delay_args = array(
							'tag_type'		=> $tag_type,
							'tag_contents'	=> $tag_contents,
							'ele'			=> $ele
						);
						$tag_contents = Performance_Utilities_Delay_Scripts_And_Styles::process_tag( $delay_args, $insert_delay_scripts );
					} else {
						if ( $operation === 'move_to_footer' ) {
							$moves_queue[] = $tag_contents;
						}

						// Remove existing tag
						$tag_contents = '';
					}
					
					return $tag_contents;
				},
				$buffer
			);
		}

		// Add tags queued for movement to the footer.
		foreach( $moves_queue as $tag_to_move ) {
			$buffer = str_replace( '</body>', $tag_to_move . '</body>', $buffer );
		}

		// Add delay scripts if needed
		if ( ! empty( $insert_delay_scripts ) ) {
			$delay_scripts = Performance_Utilities_Delay_Scripts_And_Styles::get_delay_scripts( $insert_delay_scripts );

			$buffer = str_replace( '</body>', $delay_scripts . '</body>', $buffer );
		}

		return $buffer;
	}
}

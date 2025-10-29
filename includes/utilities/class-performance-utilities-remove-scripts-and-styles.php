<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Performance_Utilities_Remove_Scripts_And_Styles {

	private $settings;

	public static $needs_html_buffer = true;

	public function __construct() {
		$this->settings = array(
			'scripts'	=> array(),
			'styles'	=> array()
		);

		$this->settings = apply_filters( 'wppu_scripts_and_styles_to_remove', $this->settings ) ?? $this->settings;
	}

	public function process_removals( $buffer ) {
		// Filter out removals that are not valid for the current page, based on conditional matches
		$this->settings['scripts'] = Performance_Utilities_Conditional_Checks::filter_matches( $this->settings['scripts'] );
		$this->settings['styles'] = Performance_Utilities_Conditional_Checks::filter_matches( $this->settings['styles'] );

		// Process removals
		if ( ! empty( $this->settings['scripts'] ) ) {
			// Process all script tags
			$match_args = array(
				'tag_type'			=> 'script',
				'match_settings'	=> $this->settings['scripts'],
				'match_types'		=> array( 'id', 'src', 'code' ),
				'operation'			=> 'remove'
			);
			$buffer = Performance_Utilities_Html_Buffer::process_buffer_replacements( $buffer, $match_args );
		}

		if ( ! empty( $this->settings['styles'] ) ) {
			// Process all stylesheet link and style tags
			$match_args = array(
				'tag_type'			=> 'style',
				'match_settings'	=> $this->settings['styles'],
				'match_types'		=> array( 'id', 'href', 'code' ),
				'operation'			=> 'remove'
			);
			$buffer = Performance_Utilities_Html_Buffer::process_buffer_replacements( $buffer, $match_args );
		}

		return $buffer;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.1.0
	 */
	public function run() {
		// Iterate over scripts and styles to remove
		add_filter( 'wppu_modify_final_output', array( $this, 'process_removals' ), 15 );
	}
}

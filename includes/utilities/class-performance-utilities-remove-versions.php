<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class Performance_Utilities_Remove_Versions {

	private $settings;

	public static $needs_html_buffer = true;

	public function __construct() {
	}

	public function process_version_removals( $buffer ) {
		$youtube_iframe_count = 0;

		// Process all script and stylesheet source attributes
		$buffer = preg_replace( 
			'/(<(?:link|script)[^>]*?(?:href|src)=[\\\'\"][^\\\'\"]+)(\?ver=[^\\\'\"]+)([\\\'\"][^>]*?>)/im',
			'${1}${3}',
			$buffer
		);

		return $buffer;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.6.0
	 */
	public function run() {
		add_filter( 'wppu_modify_final_output', array( $this, 'process_version_removals' ), 10 );
	}
}

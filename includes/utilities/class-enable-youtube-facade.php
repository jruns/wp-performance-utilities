<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class PerformanceUtilities_Enable_Youtube_Facade {

	private $settings;

	public static $needs_html_buffer = true;

	public function __construct() {
	}

	public function process_youtube_iframes( $buffer ) {
		$youtube_iframe_count = 0;

		// Process all YouTube iframe tags
		$buffer = preg_replace_callback( 
			'/<iframe[^>]*?src=[\\\'\"]([^\\\'\"]*youtube\.com[^\\\'\"]*)[\\\'\"][^>]*?>[\s\S]*?<\/[^>]*iframe[^>]*>/im', 
			function( $matches ) {
				// Replace YouTube iframes with placeholder image (facade)
				$has_youtube_iframes = true;

				$original_iframe = $matches[0];
				$original_video_url = $matches[1];

				preg_match( '/src=[\\\'\"][^\\\'\"]*youtube\.com\/embed\/([a-zA-Z0-9]+)[^\\\'\"]*[\\\'\"]/i', $matches[0], $video_id_matches );
				$video_id = $video_id_matches[1];

				$nonembed_video_url = "https://www.youtube.com/watch?v=${video_id}";

				preg_match( '/width=[\\\'\"]([^\\\'\"]*)[\\\'\"] height=[\\\'\"]([^\\\'\"]*)[\\\'\"]/i', $matches[0], $dimension_matches );
				$width = $dimension_matches[1];
				$height = $dimension_matches[2];

				$img_url = "https://img.youtube.com/vi/$video_id/hqdefault.jpg";

				return "<div class='perfutils-youtube-embed perfutils-youtube-embed-$video_id' style='width:${width}px'>
				<a href='$nonembed_video_url' data-video-id='$video_id' data-width='$width' data-height='$height' style=\"\" title='Play' target='_blank'>
					<img src='$img_url' loading='lazy' width='$width' height='$height' style='height: ${height}px;object-fit: cover;aspect-ratio: $width / $height;' />
					<div class='perfutils-youtube-play'></div>
				</a></div>";
			},
			$buffer,
			-1,
			$youtube_iframe_count
		);

		if( $youtube_iframe_count > 0 ) {
			$buffer = str_replace( '</body>', $this->get_footer_code() . '</body>', $buffer );
		}

		return $buffer;
	}

	public function get_footer_code() {
		$footer_code = "<link rel='stylesheet' id='perfutils-youtube-facade-css' href='". plugin_dir_url( __DIR__ ) . 'css/youtube_facade.min.css' . "' media='all' />
<script id='perfutils-youtube-facade-js' src='". plugin_dir_url( __DIR__ ) . 'js/youtube_facade.min.js' . "'></script>
";
		return $footer_code;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.2.0
	 */
	public function run() {
		add_filter( 'perfutils_modify_final_output', array( $this, 'process_youtube_iframes' ), 8 );
	}
}

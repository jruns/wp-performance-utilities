<?php

class Performance_Utilities_Enable_Youtube_Facade {

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

				return "<div class='wppu-youtube-embed wppu-youtube-embed-$video_id' style='width:${width}px'>
				<a href='$nonembed_video_url' data-video-id='$video_id' data-width='$width' data-height='$height' style=\"\" title='Play' target='_blank'>
					<img src='$img_url' loading='lazy' width='$width' height='$height' style='height: ${height}px;object-fit: cover;aspect-ratio: $width / $height;' />
					<div class='wppu-youtube-play'></div>
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
		$footer_code = '
<style id="wppu-youtube-facade-styles">
.wppu-youtube-embed{position:relative}.wppu-youtube-embed a{text-decoration:none}.wppu-youtube-play{position:absolute;left:50%;top:50%;content:"";width:62px;height:48px;margin-left:-34px;margin-top:-24px;padding:0;background:red;border-radius:50%/10%;color:#fff;text-align:center}.wppu-youtube-play::before{background:inherit;border-radius:5%/50%;bottom:9%;content:"";left:-5%;position:absolute;right:-5%;top:9%}.wppu-youtube-play::after{border-style:solid;border-width:1em 0 1em 1.732em;border-color:transparent transparent transparent #fff;content:" ";font-size:.64em;height:0;margin:-1em 0 0 -.75em;top:50%;position:absolute;width:0}.wp-embed-responsive .wp-embed-aspect-16-9 .wp-block-embed__wrapper::before{padding-top:0}.wp-embed-responsive .wp-embed-aspect-16-9 .wp-block-embed__wrapper .wppu-youtube-embed:has(iframe){padding-top:56.25%}.wp-embed-responsive .wp-has-aspect-ratio .wppu-youtube-embed{width:100%!important}.wp-block-embed .wppu-youtube-embed{max-width:100%}.wp-embed-responsive .wp-has-aspect-ratio .wppu-youtube-embed,.wp-embed-responsive .wp-has-aspect-ratio .wppu-youtube-embed a,.wp-embed-responsive .wp-has-aspect-ratio .wppu-youtube-embed img{height:100%!important;width:100%!important;max-width:100%!important}
</style>

<script id="wppu-youtube-facade-scripts">
document.addEventListener("DOMContentLoaded",(function(){var e=document.querySelectorAll(".wppu-youtube-embed a");e.forEach((function(e){e.addEventListener("click",(function(t){t.preventDefault();var r=document.createElement("iframe");r.setAttribute("src","https://www.youtube.com/embed/"+e.dataset.videoId+"?autoplay=1"),r.setAttribute("allowfullscreen","true"),r.setAttribute("frameborder","0"),r.setAttribute("width",e.dataset.width??560),r.setAttribute("height",e.dataset.height??315),r.setAttribute("allow","accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"),r.setAttribute("referrerpolicy","strict-origin-when-cross-origin"),e.replaceWith(r),r.focus()}))}))}));
</script>

';
		return $footer_code;
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.2.0
	 */
	public function run() {
		add_filter( 'wppu_modify_final_output', array( $this, 'process_youtube_iframes' ), 8 );
	}
}

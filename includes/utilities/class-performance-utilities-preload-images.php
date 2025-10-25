<?php

class Performance_Utilities_Preload_Images {

	private $settings;

	public static $needs_html_buffer = true;
	public static $runs_in_admin = true;

	public function __construct() {
		$this->settings = array(
			'images'	=> array()
		);

		$this->settings = apply_filters( 'wppu_images_to_preload', $this->settings ) ?? $this->settings;
	}

	public function process_images( $buffer ) {
		// Filter out settings that are not valid for the current page, based on conditional matches
		$this->settings['images'] = Performance_Utilities_Conditional_Checks::filter_matches( $this->settings['images'] );

		$preload_tags = '';

		// Process images to preload
		if ( ! empty( $this->settings['images'] ) ) {

			// Process specific urls to insert
			foreach( $this->settings['images'] as $image_setting ) {
				if ( array_key_exists( 'url', $image_setting ) && ! empty( $image_setting['url'] ) ) {
					$media_query = '';

					if ( array_key_exists( 'args', $image_setting ) && ! empty( $image_setting['args'] ) ) {
						if ( array_key_exists( 'media', $image_setting['args'] ) && ! empty( $image_setting['args']['media'] ) ) {
							$media_query = "media=\"{$image_setting['args']['media']}\" ";
						}
					}

					$preload_tags = "<link rel=\"preload\" href=\"{$image_setting['url']}\" as=\"image\" fetchpriority=\"high\" {$media_query}/>" . PHP_EOL . $preload_tags;
				}
			}
		}

		if ( ! empty( $preload_tags ) ) {
			$buffer = str_replace( '</head>', $preload_tags . '</head>', $buffer );
		}

		return $buffer;
	}

	public function add_meta_box( $post_type, $post ) {
		add_meta_box(
			'wppu_preload_images_metabox',
			'Preload Images',
			array( $this, 'render_meta_box' ),
			null,
			'normal',
			'low',
			array( '__block_editor_compatible_meta_box' => true )
		);
	}
	
	public function render_meta_box( $post ) {

    	$allowed_html = array(
			'div' => array(),
		);

		$output = '<div>Image Preload form goes here</div>';
		echo wp_kses( $output, $allowed_html );
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    0.8.0
	 */
	public function run() {
		add_filter( 'wppu_modify_final_output', array( $this, 'process_images' ), 9 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 100, 2 );
	}
}
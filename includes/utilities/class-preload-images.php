<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class PerformanceUtilities_Preload_Images {

	private $settings;

	public static $needs_html_buffer = true;
	public static $runs_in_admin = true;

	public function __construct() {
		$this->settings = array(
			'images'	=> array()
		);

		$this->settings = apply_filters( 'perfutils_images_to_preload', $this->settings ) ?? $this->settings;
	}

	public function process_images( $buffer ) {
		// Filter out settings that are not valid for the current page, based on conditional matches
		$this->settings['images'] = PerformanceUtilities_Conditional_Checks::filter_matches( $this->settings['images'] );

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
			'perfutils_preload_images_metabox',
			'Preload Images',
			array( $this, 'render_meta_box' ),
			null,
			'normal',
			'low',
			array( '__block_editor_compatible_meta_box' => true )
		);
	}
	
	public function render_meta_box( $post ) {
		wp_enqueue_style( 'perfutils-preload-images-editor', plugin_dir_url( __DIR__ ) . 'css/preload_images_editor.css', array(), constant( 'PERFUTILS_VERSION' ) );

    	$allowed_html = array(
			'div' => array(
				'class' => array(),
				'style' => array()
			),
			'input' => array(
				'class' => array(),
				'type' => array(),
				'size' => array(),
				'placeholder' => array(),
				'value' => array(),
			),
			'select' => array(
				'name' => array(),
				'id' => array(),
				'class' => array(),
			),
			'option' => array(
				'value' => array(),
				'selected' => array(),
			),
			'br' => array()
		);

		$output = '
		<div class="perfutils-preloadimages perfutils-container">
			<div class="perfutils-row">
				<div class="perfutils-item"></div>
				<div class="perfutils-item perfutils-bold">Image URL:</div>
				<div class="perfutils-item perfutils-bold">Load when Screen Width is: (optional)</div>
			</div>
			<div class="perfutils-row">
				<div class="perfutils-item perfutils-bold">Image 1:</div>
				<div class="perfutils-item"><input class="fullwidth" type="text" placeholder="Enter the image URL" /></div>
				<div class="perfutils-item">
					<select>
						<option value="">-- Comparison operator --</option>
						<option value="gt">Greater than</option>
						<option value="lt">Less than</option>
						<option value="gteq">Greater than or equal to</option>
						<option value="lteq">Less than or equal to</option>
						<option value="eq">Equal to</option>
					</select>
					<input type="text" size="7" placeholder="Width in px" />
				</div>
			</div>
			<div class="perfutils-row">
				<div class="perfutils-item perfutils-bold">Image 2:</div>
				<div class="perfutils-item"><input class="fullwidth" type="text" placeholder="Enter the image URL" /></div>
				<div class="perfutils-item">
					<select>
						<option value="">-- Comparison operator --</option>
						<option value="gt">Greater than</option>
						<option value="lt">Less than</option>
						<option value="gteq">Greater than or equal to</option>
						<option value="lteq">Less than or equal to</option>
						<option value="eq">Equal to</option>
					</select>
					<input type="text" size="7" placeholder="Width in px" />
				</div>
			</div>
			<div class="perfutils-row">
				<div class="perfutils-item perfutils-bold">Image 3:</div>
				<div class="perfutils-item"><input class="fullwidth" type="text" placeholder="Enter the image URL" /></div>
				<div class="perfutils-item">
					<select>
						<option value="">-- Comparison operator --</option>
						<option value="gt">Greater than</option>
						<option value="lt">Less than</option>
						<option value="gteq">Greater than or equal to</option>
						<option value="lteq">Less than or equal to</option>
						<option value="eq">Equal to</option>
					</select>
					<input type="text" size="7" placeholder="Width in px" />
				</div>
			</div>
		</div>';
		echo wp_kses( $output, $allowed_html );
	}

	/**
	 * Execute commands after initialization
	 *
	 * @since    1.0.0
	 */
	public function run() {
		add_filter( 'perfutils_modify_final_output', array( $this, 'process_images' ), 9 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ), 100, 2 );
	}
}